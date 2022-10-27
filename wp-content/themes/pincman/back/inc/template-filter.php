<?php

/**
 * Exit if accessed directly.
 */
defined('ABSPATH') || exit;

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
        $template = get_theme_file_path('inc/goto.php');
        load_template($template);
        exit;
    }
}
// add_filter('post_type_link', 'custom_qa_link', 11, 3);
// function custom_qa_link($link, $post = null)
// {
//     if ($post->post_type == 'dwqa-question') {
//         return home_url('questions/' . $post->ID . '.html');
//     } else {
//         return $link;
//     }
// }
