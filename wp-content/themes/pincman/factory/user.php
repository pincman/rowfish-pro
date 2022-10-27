<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 07:48:19
 * @updated_at: 2021-05-21 07:49:26
 * @description: 用户系统函数
 * @homepage: https://pincman.cn
 */

/**
 * 用户中心页面菜单参数配置
 * @Author Dadong2g
 * @DateTime 2021-01-23T09:38:44+0800
 * @return [type] [description]
 */
function pincman_user_page_options()
{

    $param_shop = [
        'coin' => ['action' => 'coin', 'name' => esc_html__('我的余额', 'rizhuti-v2'), 'icon' => site_mycoin('icon') . ' nav-icon'],
        // 'order' => ['action' => 'order', 'name' => esc_html__('购买记录', 'rizhuti-v2'), 'icon' => 'fas fa-shopping-basket nav-icon'],
        // 'down' => ['action' => 'down', 'name' => esc_html__('下载记录', 'rizhuti-v2'), 'icon' => 'fas fa-cloud-download-alt nav-icon'],
        'fav' => ['action' => 'fav', 'name' => esc_html__('我的收藏', 'rizhuti-v2'), 'icon' => 'far fa-star nav-icon'],
        // 'qa' => ['action' => 'qa', 'name' => esc_html__('我的问答', 'rizhuti-v2'), 'icon' => 'fa fa-newspaper-o nav-icon'],
        'aff' => ['action' => 'aff', 'name' => esc_html__('推广中心', 'rizhuti-v2'), 'icon' => 'fas fa-hand-holding-usd nav-icon'],
        'tou' => ['action' => 'tou', 'name' => esc_html__('文章投稿', 'rizhuti-v2'), 'icon' => 'fa fa-newspaper-o nav-icon'],
        'msg' => ['action' => 'msg', 'name' => esc_html__('消息工单', 'rizhuti-v2'), 'icon' => 'fa fa-bell-o nav-icon'],
        'vip' => ['action' => 'vip', 'name' => esc_html__('我的赞助', 'rizhuti-v2'), 'icon' => 'fa fa-code nav-icon'],
    ];
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
