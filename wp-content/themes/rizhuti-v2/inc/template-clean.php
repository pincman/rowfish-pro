<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;


/**
 * 关闭古腾堡编辑器
 */
if (!_cao('disable_gutenberg_edit')) {
    add_filter('use_block_editor_for_post', '__return_false');
    remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
}



/**
 * Remove WordPress admin bar
 */
function hide_admin_bar($flag) {
    return false;
}
add_filter('show_admin_bar', 'hide_admin_bar');


/**
 * 移除wp自带顶部导航条
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:21:41+0800
 * @return   [type]                   [description]
 */
function remove_admin_bar() {
    remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'remove_admin_bar');


/**
 * Disable xmlrpc.php
 */
add_filter('xmlrpc_enabled', '__return_false');



/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:20:50+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'rizhuti_v2_pingback_header');


/**
 * 清除wordpress自带的meta标签
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:19:56+0800
 * @return   [type]                   [description]
 */
function ashuwp_clean_theme_meta() {
    remove_action('wp_head', 'print_emoji_detection_script', 7, 1);
    remove_action('wp_print_styles', 'print_emoji_styles', 10, 1);
    remove_action('wp_head', 'rsd_link', 10, 1);
    remove_action('wp_head', 'wp_generator', 10, 1);
    remove_action('wp_head', 'feed_links', 2, 1);
    remove_action('wp_head', 'feed_links_extra', 3, 1);
    remove_action('wp_head', 'index_rel_link', 10, 1);
    remove_action('wp_head', 'wlwmanifest_link', 10, 1);
    remove_action('wp_head', 'start_post_rel_link', 10, 1);
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'rest_output_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10, 1);
    remove_action('wp_head', 'rel_canonical', 10, 0);
}
add_action('after_setup_theme', 'ashuwp_clean_theme_meta');

/**
 * WordPress 后台禁用Google Open Sans字体，加速网站
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:20:05+0800
 * @param    [type]                   $translations [description]
 * @param    [type]                   $text         [description]
 * @param    [type]                   $context      [description]
 * @param    [type]                   $domain       [description]
 * @return   [type]                                 [description]
 */
function wpdx_disable_open_sans($translations, $text, $context, $domain) {
    if ('Open Sans font: on or off' == $context && 'on' == $text) {
        $translations = 'off';
    }
    return $translations;
}
add_filter('gettext_with_context', 'wpdx_disable_open_sans', 888, 4);



/**
 * 删除OpenSans字体加快网站速度
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:20:11+0800
 * @return   [type]                   [description]
 */
function remove_open_sans() {    
    wp_deregister_style( 'open-sans' );    
    wp_register_style( 'open-sans', false );    
    wp_enqueue_style('open-sans','');    
}    
add_action( 'init', 'remove_open_sans' );


remove_action( 'wp_head', 'wp_resource_hints', 2 );


/**
 * 后台页面 替换Gravatar为v2ex头像源
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:18:43+0800
 * @param    [type]                   $avatar [description]
 * @return   [type]                           [description]
 */
function replace_get_avatar( $avatar ) {
    if (is_admin()) {
        $avatar = preg_replace("/http:\/\/(www|\d).gravatar.com\/avatar\//","http://cdn.v2ex.com/gravatar/",$avatar);
        return $avatar;
    }else{
        return $avatar;
    }
}
add_filter('get_avatar', 'replace_get_avatar');

/**
 * 禁用缩放图片尺寸
 */
add_filter('big_image_size_threshold', '__return_false');


