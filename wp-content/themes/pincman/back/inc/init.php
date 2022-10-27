<?php

/**
 * Exit if accessed directly.
 */
defined('ABSPATH') || exit;
/**

 * @Author Dadong2g
 * @DateTime 2021-01-17T13:20:28+0800
 * @return [type] [description]
 */
function init_child_fun()
{
    // 在微信内访问自动关闭商城和登录注册功能
    // if (is_weixin_visit() && _cao('is_weixin_close_site_shop', false)) {
    // add_filter('is_site_shop', '__return_true');
    // }
    remove_action('wp_enqueue_scripts', 'rizhuti_v2_scripts');
    remove_action('template_redirect', 'riplus_oauth_page_template', 5);
    remove_action('wp_ajax_add_question_new', 'add_question_new');
    add_post_type_support('docs', 'wpcom-markdown');
    add_post_type_support('question', 'wpcom-markdown');
    add_post_type_support('answer', 'wpcom-markdown');
}
