<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 21:33:02 +0800
 * @Path           : /wp-content/themes/rowfish/pages/user/vip.php
 * @Description    : 用户中心-我的会员页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间
global $current_user;
$vip_options = rf_get_vip_options();
// var_dump($vip_opt);die;
$vip_type = _get_user_vip_type($current_user->ID);
$vip_endtime = date('Y-m-d', _get_user_vip_endtime());
?>


<!-- Body -->
<div class="div">
    <div class="row justify-content-center">
        <?php foreach ($vip_options as $type => $opt) :
            if (isset($opt['enabled']) && $opt['enabled']) :
        ?>
                <div class="col-lg-4 mb-3">
                    <div class="pay-vip-item card shadow" data-type="<?php echo $type; ?>" data-price="<?php echo $opt['price']; ?>">
                        <div class="vip-body">
                            <h5 class="vip-title"><?php echo rf_get_vip_badge(null, $type, ' nav-icon'); ?></h5>
                            <p class="vip-price py-2">￥<?php echo $opt['price']; ?></p>
                            <?php if ($opt['day'] == 3600) {
                                $day = esc_html__('永久', 'rizhuti-v2');
                            } else {
                                $day = $opt['day'] . esc_html__('天', 'rizhuti-v2');
                            } ?>
                            <p class="vip-text day small text-muted">会员时长：<?php echo $day; ?></p>
                        </div>
                    </div>
                </div>
        <?php
            endif;
        endforeach;
        ?>
    </div>
</div>
<!-- End Body -->

<div class="card mt-0 mt-lg-3">
    <div class="card-header">
        <h5 class="card-title"><?php echo rf_get_vip_icon(' nav-icon') ?>立即<?php echo _cao('vip_dopay_name', '订阅本站'); ?>，享受<?php echo rf_get_base_vip_name(); ?>权限</h5>
    </div>

    <!-- Footer -->
    <div class="card-footer">
        <div class="row align-items-center flex-grow-1 mb-0">
            <div class="col-md col-12 mb-lg-0 mb-4">
                <div class="row justify-content-lg-between align-items-sm-center">
                    <div class="col-lg-6 col-12 text-muted">
                        <div class="d-flex align-items-center">
                            <div class="mr-2">
                                <img src="<?php echo get_avatar_url($current_user->ID); ?>" class="rounded-circle border-width-4 border-white" width="40">
                            </div>
                            <div class="lh-1">
                                <p class="mb-1" id="the-user" data-type="<?php echo $vip_type; ?>" data-vipname="<?php echo $vip_options[$vip_type]['name']; ?>"><?php echo $current_user->display_name; ?> <?php echo rf_get_vip_badge(null, $vip_type, ' nav-icon'); ?>
                                </p>
                                <p class="mb-1 small d-block text-muted"><?php echo $vip_endtime; ?>到期</p>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-lg-6 col-12">
                        <strong class="mr-2" id="vip-day-note"><span class="badge badge-success mr-1"></span></strong>
                    </div> -->
                </div>

            </div>
            <div class="col-md-auto col-12">
                <button id="go-pay-vip" type="button" class="btn btn-primary btn-block go-pay-vip" data-nonce="<?php echo wp_create_nonce('rizhuti-v2_click_' . $current_user->ID); ?>" disabled><?php echo esc_html__('请选择开通套餐', 'rizhuti-v2'); ?></button>
            </div>
        </div>
    </div>
    <!-- End Footer -->


</div>
<div class="card card-bordered bg-img-hero">
    <div class="card-body">
        <!-- Table -->
        <div class="table-responsive border-0">
            <table class="table mb-0 text-nowrap">
                <thead>
                    <tr class="text-uppercase text-body">
                        <th scope="col" class="font-weight-bold">级别</th>
                        <th scope="col" class="font-weight-bold">服务</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    <?php
                    foreach ($vip_options as $value) :
                        if (!isset($value['enabled']) || $value['enabled']) :
                    ?>
                            <tr>
                                <th scope="row"><?php echo $value['name']; ?></th>
                                <td class="align-middle border-top-0"><?php echo $value['service']; ?></td>
                            </tr>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Table -->
    </div>
    <div class="card-footer">
        <p class="small text-muted mb-0">© 2021. 虚拟资源，会员VIP账号为非实物商品，购买成功后不支持退款，请认真选购</p>
    </div>
</div>

<!-- JS脚本 -->
<script type="text/javascript">
    jQuery(function() {
        'use strict';
        $(".go-pay-vip").on("click", function(event) {
            event.preventDefault();
            var _this = $(this)
            var deft = _this.html()
            var postDate = {
                "action": "go_vip_pay",
                "vip_type": _this.data("type"),
                "pay_type": 0,
                "nonce": _this.data("nonce")
            }
            select_pay_mode(postDate);
            return;
        });

        //会员购买NEW 支付模式
        $(".pay-vip-item").on("click", function() {
            var _this = $(this);
            var type = _this.data('type');
            var price = _this.data('price');
            var name = _this.find(".vip-title").html();
            var day = _this.find(".vip-text.day").html();
            var pay_btn = $("#go-pay-vip");
            var the_user_type = $("#the-user").data('type');
            var the_user_vip_name = $("#the-user").data('vipname');
            var site_vip = [0, 31, 365, 3600];
            pay_btn.removeAttr("disabled")
            var note = '<span class="badge badge-success mr-1">开通</span>';
            if (the_user_type == type) {
                note = '<span class="badge badge-success mr-1">续费</span>';
            } else {
                note = '<span class="badge badge-success mr-1">开通</span>';
            }
            if (type == site_vip[0]) {
                note = '';
                pay_btn.attr("disabled", "true")
                pay_btn.html("请选择开通套餐")
            } else {
                pay_btn.html("支付开通 ￥" + price)
            }
            switch (the_user_type) {
                case site_vip[1]:
                    if (type == site_vip[1]) {
                        pay_btn.html("立即续费 ￥" + price)
                    } else {
                        pay_btn.html("立即升级 ￥" + price)
                    }
                    break;
                case site_vip[2]:
                    if (type == site_vip[2]) {
                        pay_btn.html("立即续费 ￥" + price)
                    } else if (type == site_vip[3]) {
                        pay_btn.html("立即升级 ￥" + price)
                    } else {
                        note = '';
                        pay_btn.attr("disabled", "true")
                        pay_btn.html("您已拥有该特权")
                    }
                    break;
                case site_vip[3]:
                    note = '';
                    pay_btn.html("您已开通" + the_user_vip_name)
                    pay_btn.attr("disabled", "true")
                    break;
            }
            _this.parents(".row").find('.col-lg-4 .pay-vip-item').removeClass("ok")
            _this.addClass('ok');
            // $("#vip-day-note").html(note + name + ' <span class="badge badge-light mr-1">' + day + '</span>')

            pay_btn.data('price', price)
            pay_btn.data('type', type)
        });

        $(".col-lg-4 .pay-vip-item").eq(0).click();

    });
</script>