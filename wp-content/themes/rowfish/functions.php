<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 03:35:59 +0800
 * @Path           : /wp-content/themes/rowfish/functions.php
 * @Description    : 函数加载中心
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
if (!defined('ABSPATH')) {
    exit;
}
require 'vendor/autoload.php';
require_once __DIR__ . '/factory/helpers/index.php';
if (!defined('_OPTIONS_PRE')) {
    // Replace the version number of the theme on each release.
    define('_OPTIONS_PRE', '_riprov2_options');
}

if (!defined('_RF_OPTIONS_PRE')) {
    define('_RF_OPTIONS_PRE', '_rowfish_options');
}


if (!function_exists('_cao')) {
    function _cao($option = '', $default = null)
    {
        $options_meta = _OPTIONS_PRE;
        $options      = get_option($options_meta);
        return (isset($options[$option])) ? $options[$option] : $default;
    }
}
if (!class_exists('CSF')) :
    // 引入父主题的设置框架class
    require_once get_template_directory() . '/inc/codestar-framework/codestar-framework.php';
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (is_plugin_active('anspress-question-answer/anspress-question-answer.php')) {
        require_once get_theme_file_path('anspress/function.php');
    }
    require_once get_theme_file_path('factory/index.php');
    require_once get_theme_file_path('widgets/index.php');

endif;
