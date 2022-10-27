<?php

/**
 * 修改:
 * 
 * 加载自定义和各个插件的css与js
 * 加载后台的js来删除rizhuti默认的文章配置框
 */
/**
 * Exit if accessed directly.
 */
defined('ABSPATH') || exit;

/**
 * 示例0：加载子主题的自定义CSS
 * @Author   Dadong2g
 * @DateTime 2021-01-16T23:51:11+0800
 * @return   [type]                   [description]
 */
function child_theme_configurator_scripts()
{
    rizhuti_v2_scripts();
    if (!is_admin()) {
        wp_enqueue_style('chld_theme_app_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/app.css', [], time());
        wp_enqueue_style('chld_theme_meida_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/media.css', [], time());
        wp_enqueue_style('chld_theme_anspress_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/anspress.css', [], time());
        wp_enqueue_style('chld_theme_docspress_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/docspress.css', [], time());
        wp_enqueue_style('chld_theme_comment_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/comment.css', [], time());
        wp_enqueue_style('chld_theme_code_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/code.css', [], time());
        wp_enqueue_style('chld_theme_dark_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/dark.css', [], time());
        wp_enqueue_script('child_theme_app_js', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/app.js', array('jquery', 'plugins', 'app', 'question', 'anspress-main'), time(), true);
    }
}

function child_theme_styles()
{
    if (!is_admin()) {
        // wp_enqueue_style('chld_theme_app_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/app.css', [], time());
        // wp_enqueue_style('chld_theme_code_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/comment.css', [], time());
        // wp_enqueue_style('chld_theme_code_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/code.css', [], time());
        // wp_enqueue_style('chld_theme_dark_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/dark.css', [], time());
    }
}


function wpdocs_my_admin_scripts($hook)
{
    wp_enqueue_script('wpdocs-my-editor-script', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/admin.js', [], time(), true);

    $data = array(
        'hook' => $hook
    );

    wp_localize_script('wpdocs-my-editor-script', 'my_editor_script', $data);
}
