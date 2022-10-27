<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:06:23 +0800
 * @Path           : /wp-content/themes/rowfish/course/functions/filter.php
 * @Description    : 课程过滤相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if(!function_exists('rf_course_filter_meta')){
    /**
     * 视频课程分类页面的状态和难度等级过滤
     *
     * @return array
     */
    function rf_course_filter_meta()
    {
        $_meta[] = [];
        $course_status = isset($_GET['course_status']) ? $_GET['course_status'] : null;
        $course_level = isset($_GET['course_level']) ? $_GET['course_level'] : null;
        if (!is_null($course_status)) {
            $_meta[] = [
                ['key' => 'course_status', 'compare' => '=', 'value' => $course_status],
            ];
        }
        if (!is_null($course_level)) {
            $_meta[] = [
                ['key' => 'course_level', 'compare' => '=', 'value' => $course_level],
            ];
        }
        return $_meta;
    }
}

if(!function_exists('rf_course_price_filter_meta')){
    /**
     * 课程模块价格过滤器
     *
     * @return array|array[]|string[][][]|void
     */
    function rf_course_price_filter_meta()
    {
        $price_type = isset($_GET['price_type']) ? (int)$_GET['price_type'] : null;
        $_price = 'wppay_price';
        $_auth = 'wppay_vip_auth';
        $_shop = 'shop_enabled';
        if (is_null($price_type)) return [];
        $is_simple_price_filter = _cao('is_course_simple_filter_price') == '1';
        switch ($price_type) {
            case 0:
                return [
                    'relation' => 'OR',
                    ['key' => $_shop, 'compare' => '!=', 'value' => '1'],
                    ['key' => $_price, 'compare' => '=', 'value' => '0'],
                    ['key' => $_auth, 'compare' => '<=', 'value' => '0'],
                ];
            case 1:
                return [
                    'relation' => 'AND',
                    ['key' => $_shop, 'compare' => '=', 'value' => '1'],
                    ['key' => $_price, 'compare' => '>', 'value' => '0'],
                ];
        }
        if ($is_simple_price_filter && $price_type == 2) {
            return [
                'relation' => 'AND',
                ['key' => $_shop, 'compare' => '!=', 'value' => '1'],
                ['key' => $_auth, 'compare' => '>', 'value' => '0'],
                ['key' => $_price, 'compare' => '=', 'value' => '0'],
            ];
        } else {
            switch ($price_type) {
                case 2:
                    return [
                        'relation' => 'AND',
                        ['key' => $_shop, 'compare' => '=', 'value' => '1'],
                        ['key' => $_auth, 'compare' => '=', 'value' => '1'],
                    ];
                case 3:
                    return [
                        'relation' => 'AND',
                        ['key' => $_shop, 'compare' => '=', 'value' => '1'],
                        ['key' => $_auth, 'compare' => '!=', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '<=', 'value' => '2'],
                    ];
                case 4:
                    return [
                        'relation' => 'AND',
                        ['key' => $_shop, 'compare' => '=', 'value' => '1'],
                        ['key' => $_auth, 'compare' => '!=', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '<=', 'value' => '3'],
                    ];
            }
        }
        return [];
    }
}
