<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
require 'vendor/autoload.php';
// require_once get_theme_file_path('mdeditor/CustomWPComMarkdown.php');
if (!defined('_OPTIONS_PRE')) {
    // Replace the version number of the theme on each release.
    define('_OPTIONS_PRE', '_rizhutiv2_options');
}

if (!function_exists('_cao')) {
    function _cao($option = '', $default = null)
    {
        $options_meta = _OPTIONS_PRE;
        $options      = get_option($options_meta);
        return (isset($options[$option])) ? $options[$option] : $default;
    }
}

function change_admin_options()
{
    CSF::$args['admin_options'][_OPTIONS_PRE] = [
        'menu_title' => '主题设置',
        'menu_slug'  => 'theme-setting'
    ];
}

add_action('after_setup_theme', 'change_admin_options');

require_once get_theme_file_path(
    'inc/codestar-framework/codestar-framework.php'
); //core
require_once get_theme_file_path(
    'inc/codestar-framework/classes/init.class.php'
);

// add_action('wp_enqueue_scripts', 'child_theme_configurator_scripts');
// add_action('admin_enqueue_scripts', 'wpdocs_my_admin_scripts', 50);
// // add_action('template_redirect', 'pm_oauth_page_template', 5);
// add_action('template_redirect', 'pm_oauth_page_template', 10);
// // add_action('wp_ajax_add_question_new', 'custom_add_question_new');
// add_action('init', 'init_child_fun');
// /**
//  * 主题设置初始化
//  * @Author   Dadong2g
//  * @DateTime 2021-01-16T14:29:56+0800
//  * @param    [type]                   $params [description]
//  * @return   [type]                           [description]
//  */
// function pm_option_init($params)
// {
//     // remove_filter('csf_' . _OPTIONS_PRE . '_args', 'rizhuti_option_init');
//     $current_theme = wp_get_theme('pincman');
//     $params['framework_title'] = '主题设置 <small>版本' . $current_theme->get('Version') . '</small>';
//     $params['theme']           = 'light'; //  light OR dark
//     $params['enqueue_webfont'] = false;
//     $params['enqueue']         = false;
//     $params['show_search']     = false;
//     return $params;
// }
// add_filter('csf_' . _OPTIONS_PRE . '_args', 'pm_option_init', 100);


// function remove_post_box()
// {
//     remove_meta_box('_prefix_wppay_options', 'post', 'normal');
// }
// add_action('do_meta_boxes', 'remove_post_box');
// add_action('add_meta_boxes_page', 'remove_post_box');
// require_once get_theme_file_path('example.php');
// require_once get_theme_file_path('options/index.php');
require_once get_theme_file_path('widgets/index.php');
// require_once get_theme_file_path('filters/index.php');
require_once get_theme_file_path('factory/index.php');

// require_once get_theme_file_path('inc/index.php');
// require_once get_theme_file_path('short-codes.php');
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (is_plugin_active('anspress-question-answer/anspress-question-answer.php')) {
    require_once get_theme_file_path('anspress/function.php');
}
