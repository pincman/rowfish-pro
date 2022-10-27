<?php
defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间;
global $current_user;
$user_aff_info =_get_user_aff_info($current_user->ID);
$infoArr = [
  ['title'=>'累计佣金','value'=>$user_aff_info['leiji'],'css'=>'bg-primary' ],
  ['title'=>'已提现','value'=>$user_aff_info['yiti'],'css'=>'bg-info' ],
  ['title'=>'提现中','value'=>$user_aff_info['tixian'],'css'=>'bg-danger' ],
  ['title'=>'可提现','value'=>$user_aff_info['keti'],'css'=>'bg-success' ],
];
if (!_cao('is_site_aff')) {
 exit();
}

?>

<div class="row">
  <?php foreach ($infoArr as $item) : ?>
  <div class="col-6 col-md-3">
    <div class="card mb-3 <?php echo $item['css'];?>">
      <div class="p-4 text-center">
        <h3 class="m-0 text-white">
          ￥<?php echo $item['value'];?>
        </h3>
        <span class="text-white"><?php echo $item['title'];?></span>
      </div>
    </div>
  </div>
  <?php endforeach;?>
</div>

<div class="card mt-0 mt-lg-3">
    <div class="card-header">
      <h5 class="card-title"><?php echo esc_html__('推广奖励','rizhuti-v2');?></h5>
    </div>
    <!-- Body -->
    <div class="card-body p-0">
      <div class="table-responsive border-0 overflow-y-hidden">
          <table class="table mb-0 text-nowrap">
            
            <tbody class="text-dark">
              <tr>
                <td>提现账号</td>
                <td><?php echo $current_user->user_login;?></td>
              </tr>
              <tr>
                <td>推广总数</td>
                <td><?php echo $user_aff_info['total'];?> 条订单</td>
              </tr>
              <tr>
                <td>推广链接</td>
                <td><?php echo esc_url(add_query_arg(array('aff' => $current_user->ID), home_url()));?></td>
              </tr>
              <tr>
                <td>当前佣金比例</td>
                <td><?php echo ($user_aff_info['aff_ratio']*100);?> %</td>
              </tr>


              <tr>
                <td>累计佣金</td>
                <td><?php echo $user_aff_info['leiji'];?> 元</td>
              </tr>
              <tr>
                <td>已提现</td>
                <td><?php echo $user_aff_info['yiti'];?> 元</td>
              </tr>
              <tr>
                <td>提现中</td>
                <td><?php echo $user_aff_info['tixian'];?> 元</td>
              </tr>
              <tr>
                <td>可提现</td>
                <td><?php echo $user_aff_info['keti'];?> 元</td>
              </tr>
              
            </tbody>
          </table>
      </div>
    	<ul class="small text-muted mx-2">
    		<li>推广说明：</li>
    		<li>如果用户是通过您的推广链接购买的资源或者开通会员，则按照推广佣金比列奖励到您的佣金中</li>
        <li>如果用户是通过您的链接新注册的用户，推荐人是您，该用户购买资都会给你佣金</li>
        <li>如果用户是你的下级，用户使用其他推荐人链接购买，以上下级关系为准，优先给注册推荐人而不是推荐链接</li>
        <li>推广奖励金额保留一位小数点四舍五入。0.1之类的奖励金额不计算</li>
    		<li>前台无法查看推广订单详情，如需查看详情可联系管理员截图查看详细记录和时间</li>
        <li>如需提现请联系网站管理员，发送您的账号信息和收款码进行人工提现</li>
    	</ul>
    </div>
    <!-- End Body -->
</div>

