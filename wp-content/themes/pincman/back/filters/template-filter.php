<?php

/**
 * 修改:
 * 删除默认的rizhuti_v2_archive_filter过滤器
 * 添加pm_archive_filter过滤器,可以过滤更多的自定义数据
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
function remove_rizhuti_v2_archive_filter()
{
    remove_filter('pre_get_posts', 'rizhuti_v2_archive_filter', 99);
}
function pm_archive_filter($query)
{
    //is_search判断搜索页面  !is_admin排除后台  $query->is_main_query()只影响主循环
    if (!$query->is_admin && is_archive() && $query->is_main_query()) {
        $custom_meta_query = ['relation' => 'AND'];
        // 排序：
        $order      = !empty($_GET['order']) ? $_GET['order'] : null;
        $price_type = !empty($_GET['price_type']) ? (int) $_GET['price_type'] : null;
        $course_status = isset($_GET['course_status']) ? (int) $_GET['course_status'] : null;
        $course_level = isset($_GET['course_level']) ? (int) $_GET['course_level'] : null;

        if ($order == 'views') {
            $query->set('meta_key', '_views');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
        } elseif ($order == 'favnum') {
            $query->set('orderby', ['favnum' => 'DESC', 'favnum_none' => 'DESC']);
            $custom_meta_query[] = [
                'relation' => 'OR',
                ['favnum' => ['key' =>  '_favnum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['favnum_none' => ['key' =>  '_favnum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
            ];
        } elseif (!empty($order)) {
            $query->set('orderby', [$order]);
        } else {
            $query->set('orderby', ['menu_order' => 'ASC']);
        }

        // 筛选
        if ($price_type) {
            // $custom_meta_query = [];
            $_meta  = [];
            $_price = 'wppay_price';
            $_auth  = 'wppay_vip_auth';
            switch ($price_type) {
                case 1:
                    $_meta[] = [
                        'relation' => 'AND',
                        ['key' => $_price, 'compare' => '<=', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '<=', 'value' => '0'],
                    ];
                    break;
                case 2:
                    $_meta[] = [
                        'relation' => 'AND',
                        ['key' => $_price, 'compare' => '>', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '>', 'value' => '0'],
                    ];
                    break;
                case 3:
                    $_meta[] = [
                        'relation' => 'AND',
                        ['key' => $_price, 'compare' => '==', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '>', 'value' => '0'],
                    ];
                    break;
                default:
                    break;
            }
            $custom_meta_query[] = $_meta;
            // $query->set('meta_query', $custom_meta_query);
        }
        if (!is_null($course_status)) {
            // $query->set('meta_key', 'wppay_course_status');
            // $query->set('meta_value', $course_status);
            $custom_meta_query[] = [
                ['key' =>  'wppay_course_status', 'compare' => '=', 'value' => $course_status, 'type' => 'NUMERIC'],
            ];
            // if ($is_online) {
            //     $query->set('meta_key', 'wppay_course_status');
            //     $query->set('meta_value', $course_status);
            // } else {
            //     $custom_meta_query[] = [
            //         'relation' => 'OR',
            //         ['key' =>  'is_online', 'compare' => '=', 'value' => 0, 'type' => 'NUMERIC'],
            //         ['key' =>  'is_online', 'compare' => 'NOT EXISTS'],
            //     ];
            // }
        }
        if (!is_null($course_level)) {
            $custom_meta_query[] = [
                ['key' =>  'wppay_course_level', 'compare' => '=', 'value' => $course_level, 'type' => 'NUMERIC'],
            ];
        }
        $query->set('meta_query', $custom_meta_query);
    }
    return $query;
}
add_action('init', 'remove_rizhuti_v2_archive_filter');
add_filter('pre_get_posts', 'pm_archive_filter', 100);
// function remove_fix_nav_current_class()
// {
//     // remove_filter('nav_menu_css_class', \AnsPress_Hooks, 'fix_nav_current_class', 10, 2);
// }
// anspress()->add_filter('nav_menu_css_class', 'remove_fix_nav_current_class', 10);
