<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 06:49:13
 * @updated_at: 2021-05-21 07:03:46
 * @description: 日主题默认规则修改
 * @homepage: https://pincman.cn
 */

/**
 * 修改会员组
 * @param mixed $old_opt 
 * @return string[] 
 */
function pm_vip_options($old_opt)
{
    return array(
        '0'    => '普通用户',
        // '31'   => '月卡VIP',
        '365'  => '年费订阅者',
        '3600' => '永久订阅者',
    );
}

/**
 * 会员到期时间的角标
 * @param mixed|null $user_id 
 * @param mixed|null $vip_type 
 * @return string 
 */
function pm_get_vip_badge($user_id = null, $vip_type = null)
{
    if (empty($vip_type) && !empty($user_id)) {
        global $ri_vip_options;
        $vip_type = _get_user_vip_type($user_id);
    }
    $_icon = '<i class="fa fa-code mr-1"></i>';
    $_badge = array(
        '0' => '<span class="badge badge-secondary-lighten mx-2">' . $_icon . $ri_vip_options['0'] . '</span>',
        // '31'   => '<span class="badge badge-success-lighten mx-2">' . $_icon . $ri_vip_options['31'] . '</span>',
        '365'  => '<span class="badge badge-info-lighten mx-2">' . $_icon . $ri_vip_options['365'] . '</span>',
        '3600' => '<span class="badge badge-warning-lighten mx-2">' . $_icon . $ri_vip_options['3600'] . '</span>',
    );
    return $_badge[$vip_type];
}

/**
 * 修改默认的oauth函数用于加载自定义的跳转页面
 * @return void 
 */
function pm_oauth_page_template()
{
    remove_action('template_redirect', 'riplus_oauth_page_template', 5);
    $sns = strtolower(get_query_var('oauth')); //转换为小写
    $sns_callback = get_query_var('oauth_callback');
    if ($sns && in_array($sns, array('qq', 'weixin', 'mpweixin', 'weibo'))) {
        if (is_close_site_shop()) {
            exit;
        }
        $template = $sns_callback ? TEMPLATEPATH . '/inc/sns/' . $sns . '/callback.php' : TEMPLATEPATH . '/inc/sns/' . $sns . '/login.php';
        load_template($template);
        exit;
    }

    $goto = strtolower(get_query_var('goto')); //转换为小写
    if ($goto == 1) {
        $template = get_theme_file_path('factory/redirect.php');
        load_template($template);
        exit;
    }
}
