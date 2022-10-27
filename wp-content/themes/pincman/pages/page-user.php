<?php

/**
 * Template Name: 用户中心页面
 */

if (is_close_site_shop()) {
    wp_safe_redirect(home_url());
    exit;
}


//未登录跳转
if (!is_user_logged_in()) {
    wp_safe_redirect(wp_login_url());
    exit;
}

global $current_user;

if (!empty(get_user_meta($current_user->ID, 'is_fuck', true))) {
    $_current_user_id = $current_user->ID;
    wp_logout();
    riplus_wp_die(
        __('您的账号检测异常', 'rizhuti-v2'),
        get_user_meta($_current_user_id, 'is_fuck_desc', true)
    );
    exit;
}


//是否强制绑定邮箱
if (_cao('is_site_register_bind_email') && empty($current_user->user_email)) {
    $url = add_query_arg(array('mod' => 'bindemail'), wp_login_url());
    wp_safe_redirect($url);
    exit;
}

get_header();

/**
 * 获取页面
 * @var [type]
 */
$action_var = get_query_var('action');
$part_action = (!empty($action_var)) ? strtolower(get_query_var('action')) : 'index';
$action_opt = pincman_user_page_options();

if ($part_action && array_key_exists($part_action, array_merge($action_opt['shop'], $action_opt['info']))) {
    $load_page = 'pages/user/' . $part_action;
} else {
    $load_page = 'pages/user/index';
}

get_template_part('pages/user/header');

?>


<div class="container user-top-container">
    <div class="row">
        <div class="col-lg-3 menu-column"><?php get_template_part('pages/user/menu'); ?></div>
        <div class="col-lg-9 card-column"><?php get_template_part($load_page); ?></div>
    </div>
</div>

<?php get_footer(); ?>

<!-- JS脚本 -->
<script type="text/javascript">
    jQuery(function() {
        'use strict';
        if ($(window).width() < 992) {
            $(".widget-area .rizhuti_v2-widget-shop-down").insertAfter($("#header-widget-shop-down p"));
        } else {
            var MarginTop = 30;
            jQuery('.user-top-container .menu-column').theiaStickySidebar({
                additionalMarginTop: MarginTop
            });
            jQuery('.container .card-column').theiaStickySidebar({
                additionalMarginTop: MarginTop
            });
        }

    });
</script>