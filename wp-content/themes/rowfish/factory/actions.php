<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 01:37:05 +0800
 * @Path           : /wp-content/themes/rowfish/factory/actions.php
 * @Description    : 主题自定义的actions
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

/********************************* 主题初始化 ****************************************/

/**
 * 初始化主题
 */
add_action('init', function () {
    remove_filter('pre_get_posts', 'rizhuti_v2_archive_filter', 99);
    remove_action('wp_enqueue_scripts', 'rizhuti_v2_scripts');
    remove_action('template_redirect', 'riplus_oauth_page_template', 5);
    remove_action('wp_ajax_seav_userinfo', 'seav_userinfo');
    add_post_type_support('course', 'wpcom-markdown');
    if (isDocsPress()) {
        add_post_type_support('docs', 'wpcom-markdown');
    }
    if (isAnsPress()) {
        add_post_type_support('question', 'wpcom-markdown');
        add_post_type_support('answer', 'wpcom-markdown');
    }
});

/**
 * 加载CSS和JS文件
 */
add_action('wp_enqueue_scripts', function () {
    $the_theme = wp_get_theme();
    $theme_version = WP_DEBUG ? time() : $the_theme->get('Version');
    rizhuti_v2_scripts();
    $parent_assets_dir = get_template_directory_uri() . '/assets';
    if (!is_admin()) {
        wp_enqueue_script('rowfish_popper', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/plugins/popper.min.js', [], $theme_version, true);
        wp_enqueue_style('rowfish_app', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/app.css', [], $theme_version);
        wp_enqueue_style('rowfish_comment', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/comment.css', [], $theme_version);
        if (isDocsPress()) {
            wp_enqueue_style('rowfish_docspress', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/docspress.css', [], $theme_version);
        }
        if (isAnsPress()) {
            wp_enqueue_style('rowfish_anspress', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/anspress.css', [], $theme_version);
            wp_enqueue_script('rowfish_anspress', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/plugins/anspress.js', array('app'), $theme_version, true);
        }
        wp_enqueue_style('rowfish_dark', trailingslashit(get_stylesheet_directory_uri()) . 'assets/css/dark.css', ['rowfish_app'], $theme_version);
        if (get_post_type() == 'course') {
            // DPlayer 
            wp_enqueue_script('hls', $parent_assets_dir . '/DPlayer/hls.js', array('jquery', 'app'), '', true);
            wp_enqueue_script('dplayer', $parent_assets_dir . '/DPlayer/DPlayer.min.js', array('hls'), '', true);
        }
        wp_enqueue_script('tippy', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/plugins/tippy-bundle.umd.min.js', ['rowfish_popper'], '6.3.7', true);
        $is_perticle = !empty(_cao('is_top_bg_perticle')) && (bool)_cao('is_top_bg_perticle');
        if ($is_perticle) {
            wp_enqueue_script('rowfish_perticle', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/plugins/particle.js', [], $theme_version, true);
        }
        $rf_app_deps = ['app', 'tippy'];
        if ($is_perticle) {
            array_push($rf_app_deps, 'rowfish_perticle');
        }
        wp_enqueue_script('rowfish_app', trailingslashit(get_stylesheet_directory_uri()) . 'assets/js/app.js', $rf_app_deps, $theme_version, true);
    }
}, 99);

/**
 * 初始化后台
 */
add_action('admin_init', function () {
    // 初始化课程首页模板
    $init_pages = array(
        'pages/courses.php' => array('视频课程', 'courses'),
    );
    foreach ($init_pages as $template => $item) {
        $page = array(
            'post_title' => $item[0],
            'post_name' => $item[1],
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        );
        $page_check = get_page_by_title($item[0]);
        if (!isset($page_check->ID)) {
            $page_id = wp_insert_post($page);
            update_post_meta($page_id, '_wp_page_template', $template);
            update_post_meta($page_id, 'course_top_image_enabled', '1');
        }
    }
}, 100);

/********************************* 小工具 ****************************************/

/**
 * 添加小工具位置
 */
add_action('widgets_init', function () {
    register_sidebar(
        array(
            'name' => '课程内容页侧边栏',
            'id' => 'course_post',
            'description' => esc_html__('添加小工具到这里', 'rizhuti-v2'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title">',
            'after_title' => '</h5>',
        )
    );
    register_sidebar(
        array(
            'name' => '首页半高布局',
            'id' => 'home_top',
            'description' => esc_html__('添加小工具到这里', 'rizhuti-v2'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title">',
            'after_title' => '</h5>',
        )
    );
});

/********************************* 用户相关 ****************************************/

if (!function_exists('update_avatar_photo')) {
    /**
     * 使用Ajax更新用户头像
     * @throws Exception
     */
    function update_avatar_photo()
    {
        if (is_close_site_shop()) {
            exit;
        }
        header('Content-type:application/json; Charset=utf-8');
        global $current_user;
        $user_id = $current_user->ID;
        $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
        $file = !empty($_FILES['file']) ? $_FILES['file'] : null;

        if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2-click-' . $user_id)) {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新重试', 'ripro-v2')));
            exit;
        }

        $wp_filetype = wp_check_filetype($file['name']);
        $img_info = getimagesize($file['tmp_name']); //读取图片信息
        $arrType = array('image/jpg', 'image/gif', 'image/png', "image/jpeg");
        $typearr = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
        $_filesubstr = substr(strrchr($file['name'], '.'), 1);

        if (!in_array($wp_filetype['type'], $arrType) || empty($img_info) || !in_array($_filesubstr, $typearr)) {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('图片类型错误', 'ripro-v2')));
            exit;
        }

        if ($file['size'] > 80040) {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('头像最大限制80KB', 'ripro-v2')));
            exit;
        }

        add_filter('upload_dir', function ($dirs) {
            $dirs['baseurl'] = WP_CONTENT_URL . '/uploads';
            $dirs['basedir'] = WP_CONTENT_DIR . '/uploads';
            $dirs['path'] = $dirs['basedir'] . $dirs['subdir'];
            $dirs['url'] = $dirs['baseurl'] . $dirs['subdir'];
            return $dirs;
        });

        $uploads = wp_upload_dir();

        $old_img = get_user_meta($user_id, 'user_custom_avatar', 1);
        if ($old_img) {
            $old_img = str_replace($uploads['baseurl'], '', $old_img);
            @unlink($uploads['basedir'] . $old_img);
        }

        $filename = date('H/i/s') . '-' . random_int(000000, 999999) . '.' . $_filesubstr;
        // $filename = 'avatar-' . $user_id . '.' . $_filesubstr;


        // wp_get_upload_dir
        $res = wp_upload_bits($filename, null, file_get_contents($file['tmp_name']), date('Y/m/d'));

        if (!$res['error']) {
            update_user_meta($user_id, 'user_custom_avatar', str_replace($uploads['baseurl'], '', $res['url']));
            update_user_meta($user_id, 'user_avatar_type', 'custom');
            echo json_encode(array('status' => '1', 'msg' => '上传成功'));
            exit;
        } else {
            echo json_encode(array('status' => '0', 'msg' => '上传失败'));
            exit;
        }
    }
}

add_action('wp_ajax_update_avatar_photo', 'update_avatar_photo');

if (!function_exists('rf_seav_userinfo')) {
    /**
     * 使用Ajax保存用户基本信息
     */
    function rf_seav_userinfo()
    {
        if (is_close_site_shop()) {
            exit;
        }
        header('Content-type:application/json; Charset=utf-8');
        global $current_user;
        $user_id = $current_user->ID;
        $nonce = !empty($_POST['nonce']) ? $_POST['nonce'] : null;
        $qq = !empty($_POST['qq']) ? esc_sql($_POST['qq']) : null;
        $blog = !empty($_POST['blog']) ? esc_sql($_POST['blog']) : null;
        $top_image = !empty($_POST['top_image']) ? esc_sql($_POST['top_image']) : null;
        $description = !empty($_POST['description']) ? sanitize_text_field($_POST['description']) : null;
        if ($nonce && !wp_verify_nonce($nonce, 'rizhuti-v2-click-' . $user_id)) {
            echo json_encode(array('status' => '0', 'msg' => esc_html__('非法请求，请刷新重试', 'rizhuti-v2')));
            exit;
        }
        $userdata = [];
        $userdata['ID'] = $user_id;
        $userdata['nickname'] = !empty($_POST['nickname']) ? sanitize_text_field($_POST['nickname']) : esc_html__('匿名用户', 'rizhuti-v2');
        $userdata['display_name'] = $userdata['nickname'];
        if (wp_update_user($userdata)) {
            if ($qq && is_numeric($qq)) {
                update_user_meta($user_id, 'qq', $qq);
            }
            if ($blog && filter_var($blog, FILTER_VALIDATE_URL)) {
                update_user_meta($user_id, 'blog', $blog);
            }
            if ($top_image && filter_var($top_image, FILTER_VALIDATE_URL) || is_null($top_image)) {
                update_user_meta($user_id, 'top_image', $top_image);
            }
            if ($description) {
                update_user_meta($user_id, 'description', $description);
            }
            echo json_encode(array('status' => '1', 'msg' => esc_html__('保存成功', 'rizhuti-v2')));
            exit;
        }
        echo json_encode(array('status' => '0', 'msg' => esc_html__('保存失败，请刷新重试', 'rizhuti-v2')));
        exit;
    }
}

add_action('wp_ajax_seav_userinfo', 'rf_seav_userinfo', 99);

if (!function_exists('rf_oauth_page_template')) {
    /**
     * 处理第三方登录
     * 更改跳转页面为自定义页面
     */
    function rf_oauth_page_template()
    {
        $sns = strtolower(get_query_var('oauth')); //转换为小写
        $sns_callback = get_query_var('oauth_callback');
        if ($sns && in_array($sns, array('qq', 'weixin', 'mpweixin', 'weibo'))) {
            if (is_close_site_shop()) {
                exit;
            }
            $template = $sns_callback ? TEMPLATEPATH . '/inc/sns/' . $sns . '/callback.php' : TEMPLATEPATH . '/inc/sns/' . $sns . '/login.php';
            load_template($template);
            exit;
        }

        $goto = strtolower(get_query_var('goto')); //转换为小写
        if ($goto == 1) {
            $template = get_theme_file_path('templates/redirect.php');
            load_template($template);
            exit;
        }
    }
}

add_action('template_redirect', 'rf_oauth_page_template', 99);
