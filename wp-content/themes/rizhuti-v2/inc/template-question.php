<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * register_post_type question Question question
 * 开发需求来自ritheme会员要求 设计逻辑灵感借鉴 https://themebetter.com/
 * 开发文档参考 https://developer.wordpress.org/reference/functions/register_post_type/
 */

function rizhuti_v2_question_init() {
    // 自定义文章类型
    $labels = array(
        'name'                  => __('问答社区', 'rizhuti-v2'),
        'singular_name'         => __('问答', 'rizhuti-v2'),
        'menu_name'             => __('问答', 'rizhuti-v2'),
        'name_admin_bar'        => __('问答', 'rizhuti-v2'),
        'add_new'               => __('新提问', 'rizhuti-v2'),
        'add_new_item'          => __('添加新提问', 'rizhuti-v2'),
        'new_item'              => __('新提问', 'rizhuti-v2'),
        'edit_item'             => __('编辑问题', 'rizhuti-v2'),
        'view_item'             => __('查看问题', 'rizhuti-v2'),
        'all_items'             => __('全部问题', 'rizhuti-v2'),
        'search_items'          => __('搜索问题', 'rizhuti-v2'),
        'not_found'             => __('未找到问题.', 'rizhuti-v2'),
        'not_found_in_trash'    => __('未找到问题.', 'rizhuti-v2'),
        'archives'              => __('问题存档', 'rizhuti-v2'),
        'insert_into_item'      => __('插入问题', 'rizhuti-v2'),
        'uploaded_to_this_item' => __('上传到此问题', 'rizhuti-v2'),
        'filter_items_list'     => __('筛选问题列表', 'rizhuti-v2'),
        'items_list_navigation' => __('问题列表导航', 'rizhuti-v2'),
        'items_list'            => __('问题列表', 'rizhuti-v2'),
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('快速问答社区', 'rizhuti-v2'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'question'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'supports'           => array('title', 'editor', 'author', 'comments'),
        'taxonomies'         => array('question_tag'),
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-editor-help',
    );

    register_post_type('question', $args);

    //自定义分类法
    $labels = array(
        'name'                  => __('话题', 'rizhuti-v2'),
        'singular_name'         => __('话题', 'rizhuti-v2'),
        'search_items'          => __('搜索话题', 'rizhuti-v2'),
        'all_items'             => __('全部话题', 'rizhuti-v2'),
        'view_item'             => __('查看话题', 'rizhuti-v2'),
        'parent_item'           => null,
        'parent_item_colon'     => null,
        'edit_item'             => __('编辑话题', 'rizhuti-v2'),
        'update_item'           => __('更新话题', 'rizhuti-v2'),
        'add_new_item'          => __('添加新话题', 'rizhuti-v2'),
        'new_item_name'         => __('新话题名称', 'rizhuti-v2'),
        'not_found'             => __('没有找到话题分类', 'rizhuti-v2'),
        'back_to_items'         => __('返回话题', 'rizhuti-v2'),
        'menu_name'             => __('话题', 'rizhuti-v2'),
        'popular_items'         => __('热门话题', 'rizhuti-v2'),
        'choose_from_most_used' => __('从常用话题中选择', 'rizhuti-v2'),
    );

    $args = array(
        'labels'            => $labels,
        'has_archive'       => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'question_tag'),
        'show_in_rest'      => true,
    );

    register_taxonomy('question_tag', 'question', $args);

    add_rewrite_rule('^question/([0-9]+)/?', 'index.php?post_type=question&p=$matches[1]', 'top');

}
add_action('init', 'rizhuti_v2_question_init');

/**
 * 加载模板
 * @Author   Dadong2g
 * @DateTime 2021-04-03T21:44:11+0800
 * @param    [type]                   $template [description]
 * @return   [type]                             [description]
 */
function rizhuti_v2_question_template($template) {

    $termObj  = get_queried_object();
    $taxonomy = (!empty($termObj) && !empty($termObj->taxonomy)) ? $termObj->taxonomy : '';
    if ($taxonomy == 'question_tag') {
        $new_template = locate_template(array('archive-question.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'rizhuti_v2_question_template', 99);

/**
 * 链接规则
 * @Author   Dadong2g
 * @DateTime 2021-04-03T21:44:19+0800
 * @param    [type]                   $url  [description]
 * @param    [type]                   $post [description]
 * @return   [type]                         [description]
 */
function rizhuti_v2_question_link($url, $post) {
    global $post;
    if (empty($post)) {
        return $url;
    }
    if ($post->post_type == 'question') {
        return home_url('question/' . $post->ID . '.html');
    } else {
        return $url;
    }
}
add_filter('post_type_link', 'rizhuti_v2_question_link', 10, 2);

/**
 * 获取问答文章回答数量
 * @Author   Dadong2g
 * @DateTime 2021-04-03T10:00:02+0800
 * @param    [type]                   $post_id   [description]
 * @param    integer                  $parent_id [description]
 * @return   [type]                              [description]
 */
function get_question_comment_num($post_id = 0, $parent_id = 0) {
    global $wpdb;
    if ($post_id > 0) {
        $res = $wpdb->get_var($wpdb->prepare("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_parent = 0 AND comment_type = %s", $post_id, 'question'));
        return (int) $res;
    }
    if ($parent_id > 0) {
        $children = get_question_children_comment($parent_id);
        return count($children);
    }

}

/**
 * 获取评论子级
 * @Author   Dadong2g
 * @DateTime 2021-04-03T20:35:37+0800
 * @param    [type]                   $comment_ID   [description]
 * @param    integer                  $data [description]
 * @return   [type]                              [description]
 */
function get_question_children_comment($comment_ID, $data = array()) {
    global $wpdb;
    $pid = $wpdb->get_col($wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_parent = %d AND comment_type = %s", $comment_ID, 'question'));
    if (count($pid) > 0) {
        foreach ($pid as $v) {
            $data[] = $v;
            $data   = get_question_children_comment($v, $data); //注意写$data 返回给上级
        }
    }
    if (count($data) > 0) {
        return $data;
    }
    return array();
}

/**
 * 获取赞同数量 点赞数量
 * @Author   Dadong2g
 * @DateTime 2021-04-03T10:19:53+0800
 * @param    [type]                   $post_id   [description]
 * @param    integer                  $parent_id [description]
 * @return   [type]                              [description]
 */
function get_question_liek_num($comment_ID) {

    $liek_users = get_comment_meta($comment_ID, 'liek_users', true); # 获取...
    if (empty($liek_users) || !is_array($liek_users)) {
        $liek_users = array();
    }
    if (get_comment_meta($comment_ID, 'liek_num', true) != count($liek_users)) {
        update_comment_meta($comment_ID, 'liek_num', count($liek_users));
    }
    return count($liek_users);
}

function update_question_liek_num() {
    header('Content-type:application/json; Charset=utf-8');
    $comment_ID = !empty($_POST['cid']) ? (int) $_POST['cid'] : 0;
    $user_id    = get_current_user_id();

    if (!$user_id) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请登录后点赞', 'rizhuti-v2')));exit;
    }

    if (!$comment_ID) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择点赞条目', 'rizhuti-v2')));exit;
    }

    $liek_users = get_comment_meta($comment_ID, 'liek_users', true); # 获取...

    if (empty($liek_users) || !is_array($liek_users)) {
        $liek_users = array();
    }

    if (in_array($user_id, $liek_users)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('您已投票', 'rizhuti-v2')));exit;
    } else {
        // 新点赞 开始处理
        array_push($liek_users, $user_id);

        if (update_comment_meta($comment_ID, 'liek_users', $liek_users)) {
            $this_num = (int) get_comment_meta($comment_ID, 'liek_num', true);
            $new_num  = $this_num + 1;
            update_comment_meta($comment_ID, 'liek_num', $new_num);
            echo json_encode(array('status' => '1', 'msg' => esc_html__('点赞成功', 'rizhuti-v2')));exit;
        }

    }

    echo json_encode(array('status' => '0', 'msg' => esc_html__('点赞异常', 'rizhuti-v2')));exit;

}
add_action('wp_ajax_go_question_like', 'update_question_liek_num');
add_action('wp_ajax_nopriv_go_question_like', 'update_question_liek_num');

/**
 * 获取问答框
 * @Author   Dadong2g
 * @DateTime 2021-04-03T12:43:51+0800
 * @return   [type]                   [description]
 */
function get_question_box() {
    header('Content-type:application/json; Charset=utf-8');
    $comment_ID = !empty($_POST['cid']) ? (int) $_POST['cid'] : 0;
    $post_id    = !empty($_POST['pid']) ? (int) $_POST['pid'] : 0;
    $user_id    = get_current_user_id();

    if (!$comment_ID || !$post_id) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择评论项目', 'rizhuti-v2')));exit;
    }

    // question-box
    $html     = '<div class="question-box">';
    $children = get_question_children_comment($comment_ID, array($comment_ID));
    //查找
    $args = array(
        // 'parent'   => $comment_ID,
        // 'comment__in' => $children,
        'parent__in' => $children,
        'post_id'    => $post_id,
        'orderby'    => array('comment_date' => 'ASC'),
        'type'       => 'question',
    );
    // 新建查询
    $comment_query = new WP_Comment_Query;
    $items         = $comment_query->query($args);
    $html .= '<h5>' . sprintf(__('共 %s 条评论', 'rizhuti-v2'), count($items)) . '</h5>';
    $html .= '<ul class="comment-list">';

    foreach ($items as $item) {
        $html .= '<li>';
        $html .= '<header>';
        $html .= '<span class="meta-author"> <div class="d-flex align-items-center">' . get_avatar($item->user_id) . get_the_author_meta('display_name', $item->user_id) . '</div></span>';
        $html .= '<span class="meta-author">' . sprintf(__('%s前', 'rizhuti-v2'), human_time_diff(strtotime($item->comment_date), current_time('timestamp'))) . '</span>';
        if ($item->comment_parent) {
            $reply = get_comment_author($item->comment_parent);
            $reply = $reply ? esc_html__('回复给', 'rizhuti-v2') . $reply : '';
            $html .= $reply;
        }

        $html .= '</header>';
        $html .= '<p class="mt-1 m-0">' . $item->comment_content . '</p>';
        $html .= '</li>';
    }

    $html .= '</ul>';

    $html .= '<div class="comment-form">';
    $html .= '<div class="input-group">';
    $html .= '<input type="text" placeholder="' . esc_html__('写下你的评论...', 'rizhuti-v2') . '" name="comment-input" class="form-control" autocomplete="off">';
    $html .= '<div class="input-group-append"><button class="btn btn-primary go-inst-comment" type="button" data-cid="' . $comment_ID . '" data-pid="' . $post_id . '"><i class="fa fa-send"></i> ' . esc_html__('发布', 'rizhuti-v2') . '</button></div>';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '</div>';
    // question-box end

    echo json_encode(array('status' => '1', 'msg' => $html));exit;

}
add_action('wp_ajax_get_question_box', 'get_question_box');
add_action('wp_ajax_nopriv_get_question_box', 'get_question_box');

/**
 * 添加问答评论
 * @Author   Dadong2g
 * @DateTime 2021-04-03T15:00:15+0800
 */
function add_question_comment() {
    header('Content-type:application/json; Charset=utf-8');
    $comment_ID = !empty($_POST['cid']) ? (int) $_POST['cid'] : 0;
    $post_id    = !empty($_POST['pid']) ? (int) $_POST['pid'] : 0;
    $text       = !empty($_POST['text']) ? wp_unslash($_POST['text']) : '';
    $user_id    = get_current_user_id();

    if (!$user_id) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请登录后评论', 'rizhuti-v2')));exit;
    }

    if (!$post_id) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请选择回复条目', 'rizhuti-v2')));exit;
    }

    if (empty($text)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入内容', 'rizhuti-v2')));exit;
    }

    $ins = wp_insert_comment(array(
        'comment_parent'    => $comment_ID,
        'user_id'           => $user_id,
        'comment_post_ID'   => $post_id,
        'comment_content'   => esc_html($text),
        'comment_type'      => 'question',
        'comment_meta'      => array('liek_num' => '0'),
        'comment_agent'     => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
        'comment_author_IP' => get_client_ip(),
    ));

    if (!$ins) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('评论异常', 'rizhuti-v2')));exit;
    } else {

        $html = '<li>';
        $html .= '<header>';
        $html .= '<span class="meta-author"> <div class="d-flex align-items-center">' . get_avatar($user_id) . get_the_author_meta('display_name', $user_id) . '</div></span>';
        $html .= '<span class="meta-author">' . esc_html__('刚刚', 'rizhuti-v2') . '</span>';
        $html .= '</header>';
        $html .= '<p class="mt-1 m-0">' . $text . '</p>';
        $html .= '</li>';

        echo json_encode(array('status' => '1', 'msg' => $html));exit;
    }

}
add_action('wp_ajax_add_question_comment', 'add_question_comment');
add_action('wp_ajax_nopriv_add_question_comment', 'add_question_comment');

/**
 * 添加新问题
 * @Author   Dadong2g
 * @DateTime 2021-04-03T15:00:15+0800
 */
function add_question_new() {
    header('Content-type:application/json; Charset=utf-8');
    $text    = !empty($_POST['text']) ? wp_unslash($_POST['text']) : '';
    $title   = !empty($_POST['title']) ? wp_unslash($_POST['title']) : '';
    $user_id = get_current_user_id();

    if (!$user_id) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请登录后发布', 'rizhuti-v2')));exit;
    }

    if (empty($title)) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('请输入标题', 'rizhuti-v2')));exit;
    }

    if (mb_strlen($title, 'UTF-8') < 6) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('标题太短', 'rizhuti-v2')));exit;
    }

    // 插入文章
    $new_post = wp_insert_post(array(
        'post_title'     => wp_strip_all_tags($title),
        'post_content'   => esc_html($text),
        'post_type'      => 'question',
        'post_status'    => 'pending',
        'comment_status' => true,
        'post_author'    => $user_id,
    ));

    if ($new_post instanceof WP_Error) {
        echo json_encode(array('status' => '0', 'msg' => esc_html__('发布失败', 'rizhuti-v2')));exit;
    } else {
        echo json_encode(array('status' => '1', 'msg' => esc_html__('发布成功，审核后展示', 'rizhuti-v2')));exit;
    }

}
add_action('wp_ajax_add_question_new', 'add_question_new');
add_action('wp_ajax_nopriv_add_question_new', 'add_question_new');
