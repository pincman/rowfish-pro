<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 00:20:43 +0800
 * @Path           : /wp-content/themes/rowfish/course/functions/page.php
 * @Description    : 课程首页相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_get_course_page_id')) {
    /**
     * 获取课程首页ID
     *
     * @return void
     */
    function rf_get_course_page_id()
    {
        $args = [
            'post_type' => 'page',
            'fields' => 'ids',
            'nopaging' => true,
            'meta_key' => '_wp_page_template',
            'meta_value' => 'pages/courses.php'
        ];
        $pages = get_posts($args);
        if ($pages) return is_array($pages) ? $pages : [$pages];
        return [];
    }
}

if (!function_exists('rf_build_course_page_query')) {
    /**
     * 构建课程首页基础查询
     *
     * @param integer $per_page
     * @param boolean $force
     * @return void
     */
    function rf_build_course_page_query($per_page = 8, $force = false)
    {
        if (is_page_template('pages/courses.php') || $force) {
            $querySetting = rf_get_filter_setting();
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $GLOBALS['wp_query'] = new WP_Query(array_merge([
                'post_type' => 'course',
                'meta_query' => $querySetting['meta'],
                'posts_per_page' => $per_page,
                'paged' => $paged,
                'post_status' => 'publish'
            ], $querySetting['orders']));
        }
    }
}
