<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:21:09 +0800
 * @Path           : /wp-content/themes/rowfish/factory/filters.php
 * @Description    : 主题自定义的filters
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

defined('ABSPATH') || exit;

/********************************* 主题 ****************************************/

/**
 * wordpress上传文件重命名
 */
add_filter('wp_handle_upload_prefilter', function ($file) {
    $time = date("YmdHis");
    $file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    return $file;
});

/**
 * RowFish主题设置初始化函数
 */
add_filter('csf_' . _OPTIONS_PRE . '_args', function ($params) {
    $current_theme = wp_get_theme('rowfish');
    $params['framework_title'] = '主题设置 <small>版本' . $current_theme->get('Version') . '</small>';
    $params['theme'] = 'light'; //  light OR dark
    $params['enqueue_webfont'] = false;
    $params['enqueue'] = false;
    $params['show_search'] = false;
    return $params;
}, 100);

/********************************* 文章内容 ****************************************/

/**
 * 过滤文章
 */
add_filter('pre_get_posts', 'rf_archive_filter', 100);
/**
 * 文章内容存储过滤器
 * 移除markdown内容提交后对'>'的转义
 */
add_filter('pre_post_content', function ($content) {
    $new_content = str_replace('&gt;', '>', $content);
    return $new_content;
}, 98);

/**
 * 文章内容读取过滤器
 */
add_filter('the_content', function ($content) {
    // 去除在tabs短代码之外自动添加的p标签
    $block = join("|", array("tabs", "tab"));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep);
    // 如果不是页面类型的文章则包在'content-preview'这个div内
    if (get_post_type() === 'page') return $rep;
    return "<div class='content-preview'>" . $rep . "</div>";
});

/**
 * 屏蔽wp-markeditor对短代码的原始呈现,让短代码中的内容也可以被markdown解析
 *
 * @param $preserve_shortcodes
 * @return false
 */
if (!function_exists('jetpack_markdown_preserve_shortcodes')) {
    function jetpack_markdown_preserve_shortcodes($preserve_shortcodes)
    {
        return false;
    }
}
add_filter("jetpack_markdown_preserve_shortcodes", "jetpack_markdown_preserve_shortcodes");

/********************************* 用户相关 ****************************************/

if (!function_exists('rf_get_avatar_url')) {
    /**
     * 获取自定义头像URL
     * @param $url
     * @param $id_or_email
     * @param $args
     * @return array|string|string[]|null
     */
    function rf_get_avatar_url($url, $id_or_email, $args)
    {
        $user_id = 0;
        if (is_numeric($id_or_email)) {
            $user_id = absint($id_or_email);
        } elseif (is_string($id_or_email) && is_email($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
            if (isset($user->ID) && $user->ID) {
                $user_id = $user->ID;
            }
        } elseif ($id_or_email instanceof WP_User) {
            $user_id = $id_or_email->ID;
        } elseif ($id_or_email instanceof WP_Post) {
            $user_id = $id_or_email->post_author;
        } elseif ($id_or_email instanceof WP_Comment) {
            $user_id = $id_or_email->user_id;
            if (!$user_id) {
                $user = get_user_by('email', $id_or_email->comment_author_email);
                if (isset($user->ID) && $user->ID) {
                    $user_id = $user->ID;
                }
            }
        }

        $avatar_type = get_user_meta($user_id, 'user_avatar_type', 1);
        if (empty($avatar_type)) {
            $avatar_url = _the_theme_avatar();
        } elseif ($avatar_type == 'custom') {

            $uploads = wp_upload_dir();

            $custom = get_user_meta($user_id, 'user_custom_avatar', 1);

            if (file_exists(WP_CONTENT_DIR . '/uploads' . $custom)) {
                $uploads['baseurl'] = WP_CONTENT_URL . '/uploads';
            }

            $custom = (empty($custom)) ? _the_theme_avatar() : $uploads['baseurl'] . $custom;

            $avatar_url = set_url_scheme($custom);
        } else {
            $avatar_url = set_url_scheme(get_user_meta($user_id, 'open_' . $avatar_type . '_avatar', 1));
        }
        $url = preg_replace('/^(http|https):/i', '', $avatar_url);
        return $url;
    }
}

if (!function_exists('rf_pre_get_avatar')) {
    /**
     * 获取自定义头像HTML
     * @param $avatar
     * @param $id_or_email
     * @param $args
     * @return string
     */
    function rf_pre_get_avatar($avatar, $id_or_email, $args)
    {

        $url = rf_get_avatar_url($avatar, $id_or_email, $args);
        $class = array('lazyload', 'avatar', 'avatar-' . (int)$args['size'], 'photo');
        if ($args['class']) {
            if (is_array($args['class'])) {
                $class = array_merge($class, $args['class']);
            } else {
                $class[] = $args['class'];
            }
        }
        if (is_admin()) {
            $lazy = '';
        } else {
            $lazy = 'data-';
        }
        $avatar = sprintf(
            "<img alt='%s' {$lazy}src='%s' class='%s' height='%d' width='%d' %s/>",
            esc_attr($args['alt']),
            esc_url($url),
            esc_attr(join(' ', $class)),
            (int)$args['height'],
            (int)$args['width'],
            $args['extra_attr']
        );
        return $avatar;
    }
}

add_filter('get_avatar_url', 'rf_get_avatar_url', 99, 3);
add_filter('pre_get_avatar', 'rf_pre_get_avatar', 99, 3);
