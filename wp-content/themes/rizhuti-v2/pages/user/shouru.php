<?php
defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间;
global $current_user;
$user_aff_info =_get_user_author_aff_info($current_user->ID);
$infoArr = [
  ['title'=>'累计收入','value'=>$user_aff_info['leiji'],'css'=>'bg-primary' ],
  ['title'=>'已提现','value'=>$user_aff_info['yiti'],'css'=>'bg-info' ],
  ['title'=>'提现中','value'=>$user_aff_info['tixian'],'css'=>'bg-danger' ],
  ['title'=>'可提现','value'=>$user_aff_info['keti'],'css'=>'bg-success' ],
];
if (!_cao('is_site_author_aff')) {
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
      <h5 class="card-title"><?php echo esc_html__('文章收入详情','rizhuti-v2');?></h5>
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
                <td>累计销售总数</td>
                <td><?php echo $user_aff_info['total'];?></td>
              </tr>
              <tr>
                <td>作者提成比例</td>
                <td><?php echo ($user_aff_info['author_aff_ratio']*100);?> %</td>
              </tr>

              <tr>
                <td>累计收入</td>
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
    		<li>作者提成说明：</li>
        <li>如果您在本站投稿的文章或者发布的文章被他人购买，则按照用户实际付款金额和您的作者提成比例计算您的每单收入</li>
        <li>如果您没有发布文章或者投稿权限，可以联系管理员开通作者权限</li>
    		<li>作为本站作者或者投稿者，请您切勿发布违法违规等内容，情节严重者封号处理，不给予提现</li>
        <li>如果您本人购买本人发布的文章，则不计算作者分成收入</li>
        <li>推广奖励金额保留一位小数点四舍五入。0.1之类的奖励金额不计算</li>
    		<li>前台无法查看收入订单详情，如需查看详情可联系管理员截图查看详细记录和时间</li>
        <li>如需提现请联系网站管理员，发送您的账号信息和收款码进行人工提现</li>
    	</ul>
    </div>
    <!-- End Body -->
</div>

