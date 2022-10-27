<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 08:50:43
 * @updated_at: 2021-06-01 08:29:30
 * @description: 初始化Pincman主题,添加filters,actions和短代码
 * @homepage: https://pincman.cn
 */

/**
 * 主题初始化
 * @return void 
 */
function init_pincman_theme()
{
    // 在微信内访问自动关闭商城和登录注册功能
    // if (is_weixin_visit() && _cao('is_weixin_close_site_shop', false)) {
    // add_filter('is_site_shop', '__return_true');
    // }
    pm_remove_default_archive_filter();
    remove_action('wp_enqueue_scripts', 'rizhuti_v2_scripts');
    remove_action('template_redirect', 'riplus_oauth_page_template', 5);
    remove_action('wp_ajax_add_question_new', 'add_question_new');
    add_post_type_support('docs', 'wpcom-markdown');
    add_post_type_support('question', 'wpcom-markdown');
    add_post_type_support('answer', 'wpcom-markdown');
}

/**
 * 主题设置初始化函数
 * 
 * @param mixed $params 
 * @return mixed 
 */
function pm_option_init($params)
{
    // remove_filter('csf_' . _OPTIONS_PRE . '_args', 'rizhuti_option_init');
    $current_theme = wp_get_theme('pincman');
    $params['framework_title'] = '主题设置 <small>版本' . $current_theme->get('Version') . '</small>';
    $params['theme']           = 'light'; //  light OR dark
    $params['enqueue_webfont'] = false;
    $params['enqueue']         = false;
    $params['show_search']     = false;
    return $params;
}
// shortcodes
add_shortcode('tabs', 'pm_tabs_group_shortcode');
add_shortcode('tab', 'pm_tab_shortcode');
add_shortcode('scode', 'pm_alert_shortcode');
// filters
add_filter('csf_' . _OPTIONS_PRE . '_args', 'pm_option_init', 100);
add_filter('wp_handle_upload_prefilter', 'pm_upload_filter');
add_filter('pre_post_content', 'pm_pre_content_filter', 99);
add_filter('ri_vip_options', 'pm_vip_options');
add_filter('pre_get_posts', 'pm_archive_filter', 100);
add_filter('the_content', 'pm_content_filter');
// actions
add_action('init', 'init_pincman_theme');
add_action('template_redirect', 'pm_oauth_page_template', 10);
add_action('wp_ajax_add_question_new', 'pm_ri_question_add');
add_action('wp_enqueue_scripts', 'pm_assets');
add_filter('post_type_link', 'custom_docs_link', 11, 3);
add_action('init', 'custom_docs_rewrites_init', 99);
add_action('admin_enqueue_scripts', 'pm_admin_assets', 50);

// add_filter("the_content", "the_content_filter");
