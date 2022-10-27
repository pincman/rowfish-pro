<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 00:21:42 +0800
 * @Path           : /wp-content/themes/rowfish/course/functions/content.php
 * @Description    : 课程内容相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!function_exists('rf_get_course_status')) {
    /**
     * 获取自定义的视频课程进度状态
     *
     * @return array|mixed|null
     */
    function rf_get_course_status()
    {
        $status = _cao('course_status');
        if (!is_array($status)) $status = [];
        return array_filter($status, function ($status) {
            return isset($status['slug']);
        });
    }
}
if (!function_exists('rf_get_course_levels')) {
    /**
     * 获取自定义的视频课程难度等级
     *
     * @return array|mixed|null
     */
    function rf_get_course_levels()
    {
        $levels = _cao('course_levels');
        if (!is_array($levels)) $levels = [];
        return array_filter($levels, function ($level) {
            return isset($level['slug']);
        });
    }
}

/**
 * 课程文章链接规则
 */
add_filter('post_type_link', function ($url, $post) {
    global $post;
    if (empty($post)) {
        return $url;
    }
    if ($post->post_type == 'course') {
        return home_url('courses/' . $post->ID . '.html');
    } else {
        return $url;
    }
}, 10, 2);
