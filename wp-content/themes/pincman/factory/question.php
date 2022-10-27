<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 06:55:14
 * @updated_at: 2021-05-21 08:57:01
 * @description: anspress插件及日主题问答系统修改
 * @homepage: https://pincman.cn
 */


/**
 * 添加新问题
 * @Author   Dadong2g
 * @DateTime 2021-04-03T15:00:15+0800
 */
function pm_ri_question_add()
{
    header('Content-type:application/json; Charset=utf-8');
    $text    = !empty($_POST['text']) ? wp_unslash($_POST['text']) : '';
    $title   = !empty($_POST['title']) ? wp_unslash($_POST['title']) : '';
    $user_id = get_current_user_id();

    if (!$user_id) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请登录后发布', 'rizhuti-v2')));
        exit;
    }

    if (empty($title)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入标题', 'rizhuti-v2')));
        exit;
    }

    if (mb_strlen($title, 'UTF-8') < 6) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('标题太短', 'rizhuti-v2')));
        exit;
    }

    // 插入文章
    $new_post = wp_insert_post(array(
        'post_title'     => wp_strip_all_tags($title),
        'post_content'   => esc_html($text),
        'post_type'      => 'question',
        'post_status'    => 'publish',
        'comment_status' => true,
        'post_author'    => $user_id,
    ));

    if ($new_post instanceof WP_Error) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('发布失败', 'rizhuti-v2')));
        exit;
    } else {
        echo json_encode(array('status' => '1', 'msg' => esc_html__('发布成功', 'rizhuti-v2')));
        exit;
    }
}
