<?php
defined('ABSPATH') || exit;
global $current_user;
$user_aff_info =_get_user_aff_info($current_user->ID);

if (!site_mycoin('is')) {
 exit();
}
$site_mycoin_pay_arr = site_mycoin('pay_arr');

?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title"><i class="<?php echo site_mycoin('icon');?>"></i> <?php echo esc_html__('我的余额，充值优惠活动','rizhuti-v2');?></h5>
    </div>
    
    <div class="pay_coin_bg p-4">
      <div class="m-0">
        <div class="float-lg-left float-none mb-lg-0 mb-2">
          <div class="d-flex align-content-center mt-1"><span>当前账户余额：</span><b class="bg-white badge-pill text-dark m-0"><?php echo get_user_mycoin($current_user->ID).site_mycoin('name');?></b></div>
        </div>
        <div class="float-lg-right float-none">
          <?php if (_cao('is_cdk_pay',true)) { ?>
            <button type="button" class="btn btn-sm btn-outline-danger go_cdkpay_coin" data-nonce="<?php echo wp_create_nonce('rizhuti-v2_click_' . $current_user->ID); ?>">使用卡密充值</button>
            
            <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?php echo esc_url(_cao('cdk_pay_pay_link','#'));?>" >去购买卡密</a>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Body -->
    <div class="card-body">
      <p>充值项目</p>
      <div class="row">
        <?php if (!empty($site_mycoin_pay_arr[0])) : 
        foreach ($site_mycoin_pay_arr as $key => $item) { ?>
          <div class="col-xl-4 col-md-4 col-6">
            <div class="card pay_coin_box mb-lg-4 mb-2 card-hover text-center p-4" data-key="<?php echo $key;?>" data-price="<?php echo convert_site_mycoin($item['num'],'rmb');?>">
              <?php if (!empty($item['give'])) {
                echo '<span class="pay_coin_give">多送'.(int)$item['give'].site_mycoin('name').'</span>';
              }?>
              <h5 class="mb-1 text-warning"><?php echo $item['num'].site_mycoin('name');?></h5>
              <p class="m-0 text-muted">￥<?php echo convert_site_mycoin($item['num'],'rmb');?></p>
            </div>
          </div>
        <?php }  
        else : 
          echo '<p class="p-4 text-danger">请在后台商城设置设置充值套餐</p>';
        endif; ?>
        
      </div>
      <hr>
      <div class="row">
        <div class="col">
          <div class="d-flex align-content-center mt-2"><span>支付金额：</span><b id="pay_price_note" class="text-danger m-0">0.0元</b></div>
        </div>
        <div class="col-auto">
          <?php 
          $is_opt_pay = _riplus_get_pay_type_html();
          if ($is_opt_pay['alipay'] || $is_opt_pay['weixinpay']) {
            echo '<button id="go-pay-coin" type="button" class="btn btn-primary go-pay-coin" disabled data-nonce="'.wp_create_nonce('rizhuti-v2_click_' . $current_user->ID).'">在线充值</button>';
          }else{
            if (_cao('is_cdk_pay',true)) {
              echo '<button type="button" class="btn btn-primary go_cdkpay_coin" data-nonce="'.wp_create_nonce('rizhuti-v2_click_' . $current_user->ID).'">使用卡密充值</button>';
            }else{
              echo '<button type="button" class="btn btn-danger" disabled >充值未开启</button>';
            }
          }
          ?>
        </div>
      </div>
      <hr>
      
      <ul class="small text-muted mx-2">
        <li>充值说明：</li>
        <li>充值最低额度为<?php echo site_mycoin('min_pay').site_mycoin('name');?></li>
        <li>充值汇率为1元=<?php echo convert_site_mycoin(1,'coin').site_mycoin('name');?></li>
        <li>人民币和<?php echo site_mycoin('name');?>不能互相转换</li>
        <li>余额永久有效，无时间限制</li>
        <li>卡密充值也可参与充值送活动</li>
        <li>充值活动赠送的<?php echo site_mycoin('name');?>直接到账余额</li>
      </ul>
  
    </div>
    <!-- End Body -->
   
</div>

<!-- JS脚本 -->
<script type="text/javascript">
jQuery(function() {
    'use strict';
    //卡密充值
    $(".go_cdkpay_coin").on("click", function(event) {
        event.preventDefault();
        var _this = $(this);
        var deft = _this.html();
        var cdk_code;
        Swal.fire({
          title: '使用卡密充值',
          input: 'text',
          inputPlaceholder: '请输入卡密代码',
          confirmButtonText: '使用卡密',
          width: 350,
          padding: 30,
          showCloseButton: true,
          inputValidator: (value) => {
            if (!value) {
              return '请输入卡密代码'
            }else{
              cdk_code = value;
            }
          }

        }).then((result) => {
          if (result.isConfirmed && cdk_code) {
            var postDate={
                "action": "go_cdkpay_coin",
                "nonce": _this.data("nonce"),
                "cdk_code": cdk_code,
                "pay_type": 88,
            }
            to_pay_data(postDate)
            // console.log(cdk_code)
          }
        })

        return;
    });

    // 在线充值
    $(".go-pay-coin").on("click", function(event) {
        event.preventDefault();
        var _this = $(this)
        var deft = _this.html()
        var postDate={
            "action": "go_coin_pay",
            "coin_key": _this.data("key"),
            "nonce": _this.data("nonce")
        }
        select_pay_mode(postDate);
        return;
    });

    //会员购买NEW 支付模式
    $(".pay_coin_box").on("click", function() {
        var _this = $(this);
        var key = _this.data('key');
        var price = _this.data('price');
        var pay_btn = $("#go-pay-coin");
        pay_btn.removeAttr("disabled")
        _this.parents(".row").find('.col-xl-4 .pay_coin_box').removeClass("ok")
        _this.addClass('ok');
        $("#pay_price_note").text('￥'+price)
        pay_btn.data('price', price)
        pay_btn.data('key', key)
    });
    $(".col-xl-4 .pay_coin_box").eq(0).click();
    
});
</script>
