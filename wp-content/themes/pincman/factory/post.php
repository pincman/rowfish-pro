<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 06:31:50
 * @updated_at: 2021-06-01 07:51:43
 * @description: 日主题商城的一些修改
 * @homepage: https://pincman.cn
 */

/**
 * 检测当前文章ID与另一个文章的ID是否相同
 * @param mixed $current_id 
 * @param mixed|null $post_ID 
 * @return bool 
 */
function check_is_current_post($current_id, $post_ID = null)
{
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    return $current_id == $post_ID;
}
/**
 * 获取文章信息
 * @param mixed|null $post_ID 
 * @param mixed|null $meta_key 
 * @return mixed 
 */
function pm_shop_post_info($post_ID = null, $meta_key = null)
{
    global $current_user, $ri_vip_options;
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    $post_id = $post_ID;
    $user_id = $current_user->ID;
    $RiClass = new RiClass($post_ID, $user_id);
    $IS_PAID = $RiClass->is_pay_post();
    $vip_type = (int) _get_user_vip_type($user_id);
    $vip_name = $ri_vip_options[_get_user_vip_type($user_id)];
    $wppay_price =  get_post_meta($post_ID, 'wppay_price', 1);
    $wppay_vip_auth = get_post_meta($post_ID, 'wppay_vip_auth', 1);
    $summary = get_post_meta($post_id, 'summary', true);
    // 用户是否为vip用户
    $vip_user =  $vip_type > 0 || $IS_PAID == 3;
    // 文章价格
    $price = empty($wppay_price) ? 0 : (float)$wppay_price;
    // 文章是否免费
    $free = (empty($wppay_vip_auth) || (int)$wppay_vip_auth == 0) || $IS_PAID == 4;
    // 文章是否已购买
    $paid = in_array($IS_PAID, [1, 2]);
    // 是否为教程类型
    $course = get_post_meta($post_ID, 'wppay_type', 1) == '5';
    $vip = !empty($wppay_vip_auth) && (int)$wppay_vip_auth > 0;
    // 是否vip专属文章
    $only_vip = $price <= 0 && $vip;
    // 是否可以阅读
    $can = (_cao('free_onlogin_down') == '1' && $free) || (!_cao('free_onlogin_down') && $user_id && $free) || $paid || $vip_user;
    // 半高背景图
    $hero_image =  get_post_meta($post_ID, 'hero_image', 1);
    // 文章类型
    $type =  get_post_meta($post_ID, 'wppay_type', 1);
    // 下载资源
    $download_meta =  get_post_meta($post_ID, 'wppay_down', 1);
    $download = !empty($download_meta) && is_array($download_meta) ? $download_meta : [];
    // 下载资源的其它信息
    $download_info = get_post_meta($post_ID, 'wppay_info', 1);
    $collection = compact('post_id', 'summary', 'user_id', 'vip_user', 'vip_type', 'vip_name', 'vip', 'only_vip', 'price', 'free', 'paid',  'can', 'course', 'type', 'download', 'download_info', 'hero_image');
    if ($meta_key) {
        return key_exists($meta_key, $collection) ? $collection[$meta_key] : null;
    }
    return $collection;
}


/**
 * 文章内容存储过滤器
 * 移除markdown内容提交后对'>'的转义
 * 
 * @param mixed $content 
 * @return mixed 
 */
function pm_pre_content_filter($content)
{
    $new_content = str_replace('&gt;', '>', $content);
    return $new_content;
}
function the_content_filter($content)
{
    $block = join("|", array("tabs", "tab"));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep);
    return $rep;
}
/**
 * 文章内容读取过滤器
 * @param mixed $content 
 * @return mixed 
 */
function pm_content_filter($content)
{
    // 去除在tabs短代码之外自动添加的p标签
    $block = join("|", array("tabs", "tab"));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep);
    // 如果不是页面类型的文章则包在'content-preview'这个div内
    if (get_post_type() === 'page') return $rep;
    return "<div class='content-preview'>" . $rep . "</div>";
}

function custom_docs_link($link, $post = null)
{
    if (is_plugin_active('anspress-question-answer/anspress-question-answer.php')) {
        if ($post->post_type == 'docs') {
            $docspress_archive_id = docspress()->get_option('docs_page_id', 'docspress_settings', false);
            $docs_page            = $docspress_archive_id ? get_post($docspress_archive_id) : false;
            $slug                 = $docs_page ? get_post_field('post_name', $docs_page) : 'docs';
            return home_url($slug . '/' . $post->ID . '.html');
        } else {
            return $link;
        }
    }
}

function custom_docs_rewrites_init()
{
    if (is_plugin_active('anspress-question-answer/anspress-question-answer.php')) {
        $docspress_archive_id = docspress()->get_option('docs_page_id', 'docspress_settings', false);
        $docs_page            = $docspress_archive_id ? get_post($docspress_archive_id) : false;
        $slug                 = $docs_page ? get_post_field('post_name', $docs_page) : 'docs';
        add_rewrite_rule($slug . '/([0-9]+)?.html$', 'index.php?post_type=docs&p=$matches[1]', 'top');
    }
}

// 单价总是设置为0(用于取消单价设置)
// function set_default_price($msg)
// {
//     $post = get_post();
//     update_post_meta($post->ID, 'wppay_price', 0);
//     return $msg;
// }
// add_filter('post_updated_messages', 'set_default_price');
