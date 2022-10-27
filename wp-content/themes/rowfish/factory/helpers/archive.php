<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:26:03 +0800
 * @Path           : /wp-content/themes/rowfish/factory/helpers/archive.php
 * @Description    : 数据列表及过滤器相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
if (!function_exists('rf_get_term_top')) {
    /**
     * 获取分类页面顶部图片
     *
     * @param $termObj
     * @return mixed|string|null
     */
    function rf_get_term_top()
    {
        $data = ['image' => null, 'description' => null, 'type' => null, 'archive' => is_archive(), 'is_term' => false];
        if (is_author()) return $data;
        if (is_page_template('pages/courses.php')) {
            $post_id = rf_get_post_id();
            if (!$post_id) return $data;
            $top_image_enabled = get_post_meta($post_id, 'course_top_image_enabled', true) == '1';
            if (!$top_image_enabled) return $data;
            $description = get_post_meta($post_id, 'course_top_image_description', true);
            $data = array_merge($data, [
                'type' => 'page',
                'description' => empty($description) ? null : $description
            ]);
            $is_single_image = get_post_meta($post_id, 'course_single_top_image_enabled', true) == '1';
            $single_image = get_post_meta($post_id, 'course_single_top_image', true);
            if ($is_single_image && !empty($single_image)) {
                $data['image'] = $single_image;
            }
        } else {
            $termObj = get_queried_object();
            if ($termObj && !empty($termObj->taxonomy)) {
                $is_archive_top_enabled = get_term_meta(get_queried_object_id(), 'enabled_top_image', true) == '1';
                if (!$is_archive_top_enabled) return $data;
                $data = array_merge($data, [
                    'type' => $termObj->taxonomy,
                    'is_term' => true,
                    'description' => $termObj->description
                ]);
                $archive_top_image = get_term_meta($termObj->term_id, 'top_bar_image', true);
                if ($archive_top_image) $data['image'] = $archive_top_image;
            }
        }
        if (is_null($data['image'])) {
            $global_top_images = _cao('default_archive_images');
            if (is_array($global_top_images) && count($global_top_images) > 0) {
                $global_top_images = array_filter($global_top_images, function ($img) {
                    return $img && is_array($img) && $img['url'] && !empty($img['url']);
                });
                if (count($global_top_images)) {
                    $global_top_image = $global_top_images[rand(0, count($global_top_images) - 1)];
                    if ($global_top_image && isset($global_top_image['url'])) $data['image'] = $global_top_image['url'];
                }
            }
        }

        if (is_null($data['image']) && $data['is_term']) {
            $data['image'] = rf_cute_thumbnail(null, 'full');
        }
        return $data;
    }
}
if (!function_exists('rf_order_filter_query')) {

    /**
     * 排序查询过滤器
     *
     * @param $query
     * @return array
     */
    function rf_order_filter_query()
    {
        $order = isset($_GET['order']) ? esc_sql($_GET['order']) : null;
        $data = [];
        $meta = [];
        if (is_null($order)) {
            if (isPostTypesOrder()) {
                $data = ['orderby' => ['menu_order' => 'ASC']];
            }
        } elseif ($order == 'views') {
            $data = ['orderby' => ['views' => 'DESC', 'views_none' => 'DESC']];
            $meta[] = [
                'relation' => 'OR',
                ['views' => ['key' => '_views', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['views_none' => ['key' => '_views', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
            ];
        } elseif ($order == 'favnum') {
            $data = ['orderby' => ['favnum' => 'DESC', 'favnum_none' => 'DESC']];
            $meta[] = [
                'relation' => 'OR',
                ['favnum' => ['key' => '_favnum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['favnum_none' => ['key' => '_favnum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
            ];
        } elseif ($order == 'paynum') {
            $data = ['orderby' => ['paynum' => 'DESC', 'paynum_none' => 'DESC']];
            $meta[] = [
                'relation' => 'OR',
                ['paynum' => ['key' => '_paynum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['paynum_none' => ['key' => '_paynum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
            ];
        } else {
            $data = ['orderby' => $order];
        }
        return compact('data', 'meta');
    }
}
if (!function_exists('rf_get_filter_setting')) {
    /**
     * 根据 $_GET 获取过滤设置
     */
    function rf_get_filter_setting()
    {
        $_meta = [];
        if (rf_is_course_archive(get_queried_object()) || is_page_template('pages/courses.php')) {
            $_meta = array_merge($_meta, rf_course_price_filter_meta(), rf_course_filter_meta());
        }
        $orders = rf_order_filter_query();
        $_meta = array_merge($_meta, $orders['meta']);
        return ['orders' => $orders['data'], 'meta' => $_meta];
    }
}
if (!function_exists('rf_archive_filter')) {
    /**
     * 根据过滤条过滤文章/课程数据
     *
     * @param $query
     * @return mixed
     */
    function rf_archive_filter($query)
    {
        if (!$query->is_admin && $query->is_main_query() && is_archive()) {
            $setting = rf_get_filter_setting();
            if (!rf_is_course_archive(get_queried_object()) && !is_page_template('pages/courses.php')) {
                $query->set('posts_per_page', 6);
            }
            $query->set('meta_query', $setting['meta']);
            foreach ($setting['orders'] as $key => $item) {
                $query->set($key, $item);
            }
        }
        return $query;
    };
}
