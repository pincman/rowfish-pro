<?php

/**
 * 修改:
 * 
 * 加载自定义和各个插件的css与js
 * 加载后台的js来删除rizhuti默认的文章配置框
 */
/**
 * 加载子主题资源文件
 * 为了让日主题的资源文件在前,前面通过action删除了,在此重新加载
 * @return void 
 */
function pm_assets()
{
    rizhuti_v2_scripts();
    if (!is_admin()) {
        wp_enqueue_style('pincman_app_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/app.css', [], time());
        if (is_plugin_active('anspress-question-answer/anspress-question-answer.php')) {
            wp_enqueue_style('pincman_anspress_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/anspress.css', [], time());
        }
        wp_enqueue_style('pincman_docspress_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/docspress.css', [], time());
        wp_enqueue_style('pincman_comment_css', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/comment.css', [], time());
        wp_enqueue_script('pincman_app_js', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/app.js', array('jquery', 'plugins', 'app', 'question'), time(), true);
        if (is_plugin_active('anspress-question-answer/anspress-question-answer.php')) {
            wp_enqueue_script('pincman_anspress_js', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/anspress.js', array('jquery', 'app', 'anspress-main'), time(), true);
        }
    }
}

/**
 * 加载后台资源文件
 * 主要目的是删除日主题中的文章选项以使用自定义的文章选项
 * @param mixed $hook 
 * @return void 
 */
function pm_admin_assets($hook)
{
    wp_enqueue_script('pincman_wpdocs_editor_script', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/admin.js', [], time(), true);

    $data = array(
        'hook' => $hook
    );

    wp_localize_script('pincman_wpdocs_editor_script', 'pincman_wpdocs_editor', $data);
}
