<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:25:07 +0800
 * @Path           : /wp-content/themes/rowfish/factory/helpers/user.php
 * @Description    : 用户相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_user_page_action_param_opt')) {
    /**
     * 用户中心页面菜单参数配置
     * @return mixed|void
     */
    function rf_user_page_action_param_opt()
    {

        $param_shop = [
            'msg' => ['action' => 'msg', 'name' => esc_html__('消息工单', 'rizhuti-v2'), 'icon' => 'fa fa-bell-o nav-icon'],
            'question' => ['action' => 'question', 'name' => esc_html__('我的问答', 'rizhuti-v2'), 'icon' => 'far fa-comment-alt nav-icon'],
            'fav' => ['action' => 'fav', 'name' => esc_html__('我的收藏', 'rizhuti-v2'), 'icon' => 'fas fa-star nav-icon'],
            // 'down'   => ['action' => 'down', 'name' => esc_html__('下载记录', 'rizhuti-v2'), 'icon' => 'fas fa-cloud-download-alt nav-icon'],
            'vip' => ['action' => 'vip', 'name' => esc_html__('我的会员', 'rizhuti-v2'), 'icon' => _cao('vip_icon', 'fab fa-codepen') . '  nav-icon'],
            'order' => ['action' => 'order', 'name' => esc_html__('购买记录', 'rizhuti-v2'), 'icon' => 'fas fa-shopping-basket nav-icon'],
            'coin' => ['action' => 'coin', 'name' => esc_html__('我的余额', 'rizhuti-v2'), 'icon' => site_mycoin('icon') . ' nav-icon'],
            'aff' => ['action' => 'aff', 'name' => esc_html__('推广中心', 'rizhuti-v2'), 'icon' => 'fas fa-hand-holding-usd nav-icon'],
            'tou' => ['action' => 'tou', 'name' => esc_html__('文章投稿', 'rizhuti-v2'), 'icon' => 'fa fa-newspaper-o nav-icon'],
            'shouru' => ['action' => 'shouru', 'name' => esc_html__('作者收入', 'rizhuti-v2'), 'icon' => 'fas fa-hand-holding-usd nav-icon'],
        ];

        if (!isAnsPress()) {
            unset($param_shop['question']);
        }
        if (!_cao('is_site_mycoin', true)) {
            unset($param_shop['coin']);
        }
        if (!_cao('is_site_tickets', true)) {
            unset($param_shop['msg']);
        }
        if (!_cao('is_site_aff')) {
            unset($param_shop['aff']);
        }
        if (!_cao('is_site_tougao')) {
            unset($param_shop['tou']);
        }

        if (!_cao('is_site_author_aff', false)) {
            unset($param_shop['shouru']);
        }

        if (is_oauth_password()) {
            $password_notfy = '<span class="badge badge-danger-lighten nav-link-badge">' . esc_html__('请设置密码', 'rizhuti-v2') . '</span>';
        } else {
            $password_notfy = '';
        }
        $param_user = [
            'index' => ['action' => 'index', 'name' => esc_html__('基本资料', 'rizhuti-v2'), 'icon' => 'fas fa-id-card nav-icon nav-icon'],
            'bind' => ['action' => 'bind', 'name' => esc_html__('账号绑定', 'rizhuti-v2'), 'icon' => 'fas fa-mail-bulk nav-icon'],
            'password' => ['action' => 'password', 'name' => esc_html__('密码设置', 'rizhuti-v2') . $password_notfy, 'icon' => 'fas fa-shield-alt nav-icon'],
        ];

        return apply_filters('user_page_action_param_opt', array('shop' => $param_shop, 'info' => $param_user));
    }
}

