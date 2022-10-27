<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 00:17:36 +0800
 * @Path           : /wp-content/themes/rowfish/course/functions/init.php
 * @Description    : 课程模块初始化相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_course_admin_init')) {
    /**
     * 课程模块后台管理初始化
     *
     * @return void
     */
    function rf_course_admin_init()
    {
        // 课程文章
        $labels = array(
            'name' => __('视频课程', 'rizhuti-v2'),
            'singular_name' => __('课程', 'rizhuti-v2'),
            'menu_name' => __('课程', 'rizhuti-v2'),
            'name_admin_bar' => __('课程', 'rizhuti-v2'),
            'add_new' => __('创建课程', 'rizhuti-v2'),
            'add_new_item' => __('添加新课程', 'rizhuti-v2'),
            'new_item' => __('新课程', 'rizhuti-v2'),
            'edit_item' => __('编辑课程', 'rizhuti-v2'),
            'view_item' => __('查看课程', 'rizhuti-v2'),
            'all_items' => __('全部课程', 'rizhuti-v2'),
            'search_items' => __('搜索课程', 'rizhuti-v2'),
            'not_found' => __('未找到课程.', 'rizhuti-v2'),
            'not_found_in_trash' => __('未找到课程.', 'rizhuti-v2'),
            'archives' => __('课程存档', 'rizhuti-v2'),
            'insert_into_item' => __('插入课程', 'rizhuti-v2'),
            'uploaded_to_this_item' => __('上传到此课程', 'rizhuti-v2'),
            'filter_items_list' => __('筛选课程列表', 'rizhuti-v2'),
            'items_list_navigation' => __('课程列表导航', 'rizhuti-v2'),
            'items_list' => __('课程列表', 'rizhuti-v2'),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('课程模块', 'rizhuti-v2'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'course'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 20,
            'supports' => array('title', 'editor', 'thumbnail'),
            'taxonomies' => array('course_category'),
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-editor-help',
        );

        register_post_type('course', $args);
        add_rewrite_rule('^courses/([0-9]+)/?', 'index.php?post_type=course&p=$matches[1]', 'top');

        // 课程分类
        $labels = array(
            'name' => __('分类', 'rizhuti-v2'),
            'singular_name' => __('分类', 'rizhuti-v2'),
            'search_items' => __('搜索分类', 'rizhuti-v2'),
            'popular_items' => __('热门分类', 'rizhuti-v2'),
            'all_items' => __('全部分类', 'rizhuti-v2'),
            'view_item' => __('查看分类', 'rizhuti-v2'),
            'edit_item' => __('编辑分类', 'rizhuti-v2'),
            'update_item' => __('更新分类', 'rizhuti-v2'),
            'add_new_item' => __('添加新分类', 'rizhuti-v2'),
            'new_item_name' => __('新分类名称', 'rizhuti-v2'),
            'not_found' => __('没有找到分类分类', 'rizhuti-v2'),
            'back_to_items' => __('返回分类', 'rizhuti-v2'),
            'menu_name' => __('课程分类', 'rizhuti-v2'),
            'choose_from_most_used' => __('从常用分类中选择', 'rizhuti-v2'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'course_categories'),
            'show_in_rest' => true,
            'has_archive' => true,
            'public' => true,
            'show_admin_column' => true,
        );

        register_taxonomy('course_category', 'course', $args);

        // 课程专题
        $labels = array(
            'name' => '专题',
            'singular_name' => 'series',
            'search_items' => '搜索',
            'popular_items' => '热门',
            'all_items' => '所有',
            'parent_item' => '父级专题',
            'edit_item' => '编辑',
            'update_item' => '更新',
            'add_new_item' => '添加',
            'new_item_name' => '专题名称',
            'separate_items_with_commas' => '按逗号分开',
            'add_or_remove_items' => '添加或删除',
            'choose_from_most_used' => '从经常使用的类型中选择',
            'menu_name' => '课程专题',
        );
        register_taxonomy(
            'course_series',
            array('course'),
            array(
                'hierarchical' => true,
                'labels' => $labels,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'course-series'),
                'show_in_rest' => true,
            )
        );
    }
}


/**
 * 初始化课程模块
 */
add_action('init', 'rf_course_admin_init');

/**
 * 加载课程模块相关模板
 */
add_filter('template_include', function ($template) {
    $termObj = get_queried_object();
    $taxonomy = (!empty($termObj) && !empty($termObj->taxonomy)) ? $termObj->taxonomy : '';
    // if (is_page_template('pages/courses.php')) {
    //     rf_build_course_page_query();
    // }

    if ($taxonomy == 'course_category') {
        $new_template = locate_template(array('course/templates/archive.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    } elseif ($taxonomy == 'course_series') {
        $new_template = locate_template(array('course/templates/series.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }
    $post_type = get_post_type();
    if (!empty(rf_get_post_id()) && $post_type === 'course' && is_single()) {
        $new_template = locate_template(array('course/templates/single.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }
    return $template;
}, 99);


/**
 * 更改body的class
 */
add_filter('body_class', function ($classes) {
    global $wp_query;
    remove_filter('body_class', 'rizhuti_v2_body_classes');
    $tmp_query = $wp_query;
    rf_build_course_page_query();
    $classes = rizhuti_v2_body_classes($classes);
    $info = rf_get_post_info();
    if ($info['is_course']) {
        $classes = array_merge($classes, ['with-hero', 'single-format-video',  'hero-wide', 'hero-video']);
    }
    $GLOBALS['wp_query'] = $tmp_query;
    return $classes;
}, 99);
