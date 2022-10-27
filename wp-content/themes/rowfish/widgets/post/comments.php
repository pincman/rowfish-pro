<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-13 19:35:47 +0800
 * @Updated_at     : 2021-11-19 03:42:17 +0800
 * @Path           : /wp-content/themes/rowfish/widgets/post/comments.php
 * @Description    : 最新评论小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$default_orderby = 'date';
$orderby_options =   array(
    'date'          => esc_html__('日期', 'rizhuti-v2'),
    'rand'          => esc_html__('随机', 'rizhuti-v2'),
    'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
    'views'         => esc_html__('阅读量', 'rizhuti-v2'),
    'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
    'title'         => esc_html__('标题', 'rizhuti-v2'),
    'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
);
if (isPostTypesOrder()) {
    $orderby_options = array_merge(['menu_order' => esc_html__('自定义(根据post type order插件)', 'rizhuti-v2')], $orderby_options);
    $default_orderby = 'menu_order';
}
/**
 * 侧边栏分类文章展示
 */
CSF::createWidget('rf_comments_widget', array(
    'title'       => esc_html__('RF: 最新评论', 'rizhuti-v2'),
    'classname'   => 'rowfish-comments-widget',
    'description' => esc_html__('与当前文章同一个专题下/分类下的文章展示', 'rizhuti-v2'),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
            'default' => '最新评论'
        ),
        array(
            'id'      => "only_current",
            'type'    => 'switcher',
            'title'   => esc_html__("只显示当前文章", 'rizhuti-v2'),
            'label' =>  '在文章内页只显示当前文章下的评论(此选项在分类页面无效)',
            'default' => true,
        ),
        array(
            'id'         => "icon",
            'type'       => 'icon',
            'title'      => '顶部图标',
            'default'    => 'far fa-comment-alt',
        ),
        array(
            'id'      => "is_avatar",
            'type'    => 'switcher',
            'title'   => esc_html__("是否显示作者头像", 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('评论数量', 'rizhuti-v2'),
            'default' => 5,
        ),

    ),
));
if (!function_exists('rf_comments_widget')) {
    function rf_comments_widget($args, $instance)
    {
        if (is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // 查询
        $_args = array(
            'number'  => $instance['count'],
            'paged' => 1,
            'orderby' => 'comment_date',
            'order' => 'DESC',
            'status' => 'approve',
            'post_status' => 'publish'
        );
        $post_id = rf_get_post_id();
        if ($instance['only_current'] && $post_id && !is_category()) {
            $_args['post_id'] = $post_id;
        }
        $query = new WP_Comment_Query($_args);

        ob_start(); ?>
        <ul class="comments-list">
            <?php
            if ($query->comments) :
                foreach ($query->comments as $comment) :
                    $comment_text = get_comment_text($comment->comment_ID);
                    $author = get_comment_author($comment->comment_ID);
                    $author_link = $comment->user_id ?  esc_url(get_author_posts_url($comment->user_id, get_the_author_meta('display_name', $comment->user_id))) : "javascript: void(0);";
                    $reply_num =  get_comments([
                        'parent' => $comment->comment_ID,
                        'count' => true
                    ]);
            ?>
                    <div id="div-comment-<?php echo $comment->comment_ID; ?>" class="comment-inner">
                        <div class="comment-author vcard">
                            <?php if ($instance['is_avatar']) : ?>
                                <a href="<?php echo $author_link; ?>" data-toggle="tooltip" data-placement="right" data-delay="0" title="<?php echo $author; ?>"> <?php echo get_avatar($comment, 40); ?> </a>
                            <?php else : ?>
                                <a href="<?php echo $author_link; ?>" target="_self"><?php echo $author; ?></a>
                            <?php endif; ?>
                        </div>
                        <div class=" comment-body">
                            <div class="comment-text"><?php echo wp_strip_all_tags($comment_text); ?></div>
                            <div class="comment-reply-num">
                                <i class="far fa-comment-dots"></i>
                                <span><?php echo $reply_num; ?></span>
                            </div>
                        </div>
                    </div>
            <?php
                endforeach;
            endif;
            ?>
    <?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
