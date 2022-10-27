<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:05:07 +0800
 * @Path           : /wp-content/themes/rowfish/course/options/index.php
 * @Description    : 设置所有课程模块选项并隐藏一些默认文章自带的选项
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
require_once __DIR__ . '/category.php';
require_once __DIR__ . '/post.php';
require_once __DIR__ . '/page.php';
add_action('admin_init', function () {
    remove_meta_box('authordiv', 'course', 'normal');
});
add_filter('hidden_meta_boxes', function ($hidden, $screen) {
    if ($screen->id !== 'course') {
        return $hidden;
    }
    $hidden = is_array($hidden) ? $hidden : [];
    return array_merge($hidden, ['commentstatusdiv', 'commentsdiv']);
}, 10, 2);
add_action('after_setup_theme', function () {
    rf_create_course_category_metabox();
    rf_create_course_post_metabox();
    rf_get_course_page_options();
});
