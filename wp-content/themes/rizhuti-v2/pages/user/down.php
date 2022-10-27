<?php
defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间
global $current_user;
$today_down = _get_user_today_down($current_user->ID);
$vip_opt = _get_ri_vip_options();
$vip_type = _get_user_vip_type($current_user->ID);
$infoArr = [
  ['title'=>'今日可下载','value'=>$today_down['zong'],'css'=>'bg-primary' ],
  ['title'=>'今日已下载','value'=>$today_down['yi'],'css'=>'bg-info' ],
  ['title'=>'剩余下载次数','value'=>$today_down['ke'],'css'=>'bg-danger' ],
  ['title'=>'下载速度/KB每秒','value'=>$vip_opt[$vip_type]['download_rate'],'css'=>'bg-success' ],
];
?>


<div class="row">
  <?php foreach ($infoArr as $item) : ?>
  <div class="col-6 col-md-3">
    <div class="card mb-3 <?php echo $item['css'];?>">
      <div class="p-4 text-center">
        <h3 class="m-0 text-white">
          <?php echo $item['value'];?>
        </h3>
        <span class="text-white"><?php echo $item['title'];?></span>
      </div>
    </div>
  </div>
  <?php endforeach;?>
</div>

<div class="card mt-0 mt-lg-3">
    <div class="card-header">
      <h5 class="card-title"><?php echo esc_html__('下载记录','rizhuti-v2');?></h5>
    </div>
    <!-- Body -->
    <div class="card-body p-0">
	     
      <?php
          global $wpdb, $down_table_name,$ri_pay_type_options;
          //** custom_pagination start **//
          $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1; //第几页
          $limit = 10; //每页显示数量
          $offset = ( $pagenum - 1 ) * $limit; //偏移量
          $total = $wpdb->get_var( $wpdb->prepare("SELECT count(post_id) FROM {$down_table_name} WHERE user_id=%d", $current_user->ID) );//总数
          $max_num_pages = ceil( $total / $limit ); //多少页
          //** custom_pagination end ripro_v2_custom_pagination($pagenum,$max_num_pages) **//
          //
          $sql = "SELECT * FROM $down_table_name WHERE user_id=$current_user->ID ORDER BY create_time DESC LIMIT $limit OFFSET $offset";
          $result = $wpdb->get_results( $sql, 'ARRAY_A' );
          
      ?>
    	 <div class="table-responsive border-0 overflow-y-hidden">
          <table class="table mb-0 text-nowrap">
            <thead>
              <tr class="text-uppercase text-body">
                <th scope="col">资源名称</th>
                <th scope="col">下载IP</th>
                <th scope="col">下载时间</th>
              </tr>
            </thead>

            <tbody class="text-dark">
            <?php foreach ($result as $card) : ?>
            <tr class="text-uppercase text-body">
            <td class="align-middle border-top-0"><a href="<?php echo get_the_permalink($card['post_id']);?>" class="text-inherit"><?php echo get_the_title($card['post_id']);?></a></td>
            <td class="align-middle border-top-0"><?php echo $card['ip'];?></td>
            <td class="align-middle border-top-0"><?php echo date('Y-m-d H:i:s',$card['create_time']);?></td>
            </tr>
            <?php endforeach;?>
            </tbody>
          </table>
        </div>

        <?php rizhuti_v2_custom_pagination($pagenum,$max_num_pages);?>
    	
    	<ul class="small text-muted mx-2">
    		<li>下载规则说明：</li>
    		<li>您今日最多可下载<?php echo $today_down['zong'];?>个免费的资源（包含免费资源和会员免费资源）</li>
    		<li>永久会员享有所有会员权限，可以下载包年/包月会员免费资源</li>
    		<li>包年会员享有包月会员所有权限，可以下载包月会员免费资源</li>
    		<li>包月会员仅可以下载包月会员免费的资源</li>
    		<li>全站单独购买的资源，不计算下载次数</li>
    		<li>当日已经下载过的资源，重复点击不计算下载次数</li>
    		<li>下载次数限制只针对本站免费资源，会员免费资源计次</li>
    	</ul>
    </div>
    <!-- End Body -->
   
</div>

