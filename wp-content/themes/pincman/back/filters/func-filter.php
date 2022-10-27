<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// 单价总是设置为0(用于取消单价设置)
// function set_default_price($msg)
// {
//     $post = get_post();
//     update_post_meta($post->ID, 'wppay_price', 0);
//     return $msg;
// }
// add_filter('post_updated_messages', 'set_default_price');

//wordpress上传文件重命名
function git_upload_filter($file)
{
    $time = date("YmdHis");
    $file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'git_upload_filter');

function remove_tiny_content_filter($content)
{
    $new_content = str_replace('&gt;', '>', $content);
    return $new_content;
}
add_filter('pre_post_content', 'remove_tiny_content_filter', 99);
function add_content_wrapper_filter($content)
{
    if (get_post_type() === 'page') return $content;
    return "<div class='content-preview'>" . $content . "</div>";
}
add_filter('the_content', 'add_content_wrapper_filter', 99);
