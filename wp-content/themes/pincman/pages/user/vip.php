<?php
defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间
global $current_user, $ri_vip_options;

$vip_opt = _get_ri_vip_options();
unset($vip_opt[0]); //卸载普通用户
// var_dump($vip_opt);die;
$vip_type = _get_user_vip_type($current_user->ID);
$vip_endtime = date('Y-m-d', _get_user_vip_endtime());
$_icon = '<i class="fa fa-code mr-1"></i>';
$_badge = array(
    '0' => '<b class="badge badge-secondary-lighten">' . $_icon . $ri_vip_options['0'] . '</b>',
    '31'   => '<b class="badge badge-success-lighten">' . $_icon . $ri_vip_options['31'] . '</b>',
    '365'  => '<b class="badge badge-info-lighten">' . $_icon . $ri_vip_options['365'] . '</b>',
    '3600' => '<b class="badge badge-warning-lighten">' . $_icon . $ri_vip_options['3600'] . '</b>',
);
?>


<!-- Body -->
<div class="div">
    <div class="row justify-content-center">
        <?php foreach ($vip_opt as $type => $opt) { ?>
            <div class="col-lg-4 mb-3">
                <div class="pay-vip-item card shadow" data-type="<?php echo $type; ?>" data-price="<?php echo $opt['price']; ?>">
                    <div class="vip-body">
                        <h5 class="vip-title"><?php echo $_badge[$type]; ?></h5>
                        <p class="vip-price py-2">￥<?php echo $opt['price']; ?></p>
                        <?php if ($opt['day'] == 3600) {
                            $day = esc_html__('永久', 'rizhuti-v2');
                        } else {
                            $day = $opt['day'] . esc_html__('天', 'rizhuti-v2');
                        } ?>
                        <p class="vip-text day small text-muted">赞助时长：<?php echo $day; ?></p>
                        <!-- <p class="vip-text day small text-muted">所需费用：<i class="<?php echo site_mycoin('icon'); ?>"></i> <?php echo convert_site_mycoin($opt['price'], 'coin'); ?>元</p> -->
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<!-- End Body -->

<div class="card mt-0 mt-lg-3">
    <div class="card-header">
        <h5 class="card-title"><i class="fa fa-code nav-icon"></i><?php echo esc_html__('开通订阅者，无限制地学习与获取所有资源', 'rizhuti-v2'); ?></h5>
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
                                <p class="mb-1" id="the-user" data-type="<?php echo $vip_type; ?>"><?php echo $current_user->display_name; ?> <?php echo $_badge[$vip_type]; ?>
                                </p>
                                <p class="mb-1 small d-block text-muted"><?php echo $vip_endtime; ?>到期</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <strong class="mr-2" id="vip-day-note"><span class="badge badge-success mr-1"></span></strong>
                    </div>
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
                    <tr>
                        <th scope="row">普通用户</th>
                        <td class="align-middle border-top-0">可以学习免费教程,查看免费文档以及下载免费资源</td>
                    </tr>
                    <tr>
                        <th scope="row">年订阅者</th>
                        <td class="align-middle border-top-0">一年内可享受所有的教程学习,文档查看,资源下载以及问答服务,可加入年费QQ群学习</td>
                    </tr>
                    <tr>
                        <th scope="row">永久订阅者</th>
                        <td class="align-middle border-top-0">永久享受所有的教程学习,文档查看,资源下载以及问答服务,可加入订阅者QQ群学习</td>
                    </tr>

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
                    pay_btn.html("您已开通订阅者")
                    pay_btn.attr("disabled", "true")
                    break;
            }
            _this.parents(".row").find('.col-lg-4 .pay-vip-item').removeClass("ok")
            _this.addClass('ok');
            $("#vip-day-note").html(note + name + ' <span class="badge badge-light mr-1">' + day + '</span>')

            pay_btn.data('price', price)
            pay_btn.data('type', type)
        });

        $(".col-lg-4 .pay-vip-item").eq(0).click();

    });
</script>