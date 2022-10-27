<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-13 12:57:24 +0800
 * @Updated_at     : 2021-11-19 03:40:43 +0800
 * @Path           : /wp-content/themes/rowfish/widgets/post/relation_posts.php
 * @Description    : 相关文章小工具
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
CSF::createWidget('rf_relation_posts_widget', array(
    'title'       => esc_html__('RF: 相关文章侧边栏', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post',
    'description' => esc_html__('与当前文章同一个专题下/分类下的文章展示', 'rizhuti-v2'),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'is_media',
            'type'    => 'switcher',
            'title'   => esc_html__('显示缩略图', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_one_maxbg',
            'type'    => 'switcher',
            'title'   => esc_html__('第一篇文章大图', 'rizhuti-v2'),
            'default' => false,
        ),
        // array(
        //     'id'          => 'category',
        //     'type'        => 'select',
        //     'title'       => esc_html__('无专题分类', 'rizhuti-v2'),
        //     'placeholder' => esc_html__('最新文章', 'rizhuti-v2'),
        //     'options'     => 'categories',
        //     'desc'     => esc_html__('当前文章无专题时显示此分类下的文章,不选择为最新文章', 'rizhuti-v2'),
        // ),
        array(
            'id'      => "is_category",
            'type'    => 'switcher',
            'title'   => esc_html__("是否显示同分类下的课程", 'rizhuti-v2'),
            'default' => true,
            'desc'     => esc_html__('分类或专题请至少选择一个', 'rizhuti-v2'),
        ),
        array(
            'id'      => "is_series",
            'type'    => 'switcher',
            'title'   => esc_html__("分类或专题请至少选择一个", 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => $orderby_options,
            'default' => $default_orderby
        ),

        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 4,
        ),
        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),

    ),
));
if (!function_exists('rf_relation_posts_widget')) {
    function rf_relation_posts_widget($args, $instance)
    {
        if (is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'is_media' => true,
            'is_one_maxbg' => false,
            'is_category' => true,
            'is_series' => true,
            'orderby' => isPostTypesOrder() ? 'menu_order' : 'date',
            'count' => 4,
            'offset' => 0,
        ), $instance);


        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // 查询
        $_args = array(
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );
        $info = rf_get_post_info();
        $post_id = $info['post_id'];
        $tax_query = [];
        if ($instance['is_category'] && $instance['is_series']) {
            $tax_query = ['relation' => 'OR'];
        }
        if ($instance['is_category']) {
            $categories = get_the_terms($post_id, 'category');
            if ($categories && count($categories)) {
                $ids = array_map(function ($item) {
                    return $item->term_id;
                }, $categories);
                $tax_query[] = [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms' => $ids,
                    'operator' => 'IN'
                ];
            }
        }
        if ($instance['is_series']) {
            $series = get_the_terms($post_id, 'series');
            if ($series && count($series)) {
                $ids = array_map(function ($item) {
                    return $item->term_id;
                }, $series);
                $tax_query[] = [
                    'taxonomy' => 'series',
                    'field'    => 'term_id',
                    'terms' => $ids,
                    'operator' => 'IN'
                ];
            }
        }
        $_args['tax_query'] = $tax_query;
        if ($instance['orderby'] == 'menu_order') {
            $_args['orderby'] = ['menu_order' => 'ASC'];
        } else if ($instance['orderby'] == 'views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }

        $PostData = new WP_Query($_args);
        $i = 0;

        ob_start(); ?>
        <div class="posts-wrapper list">
            <?php while ($PostData->have_posts()) : $PostData->the_post();
                if (!rf_check_is_current_post($post_id)) :
                    $i++;
                    $maxbg = ($i == 1 && !empty($instance['is_one_maxbg'])) ? ' maxbg' : '';
                    $post_info = rf_get_post_info();
            ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post post-list' . $maxbg); ?>>
                        <?php if (!empty($instance['is_media'])) {
                            echo rf_get_post_media(null, 'thumbnail');
                        } ?>
                        <div class="entry-wrapper entry-flex">
                            <header class="entry-header">
                                <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                            </header>
                            <footer class="entry-excerpt">
                                <small><?php echo $post_info['summary']; ?></small>
                            </footer>
                        </div>
                    </article>
            <?php endif;
            endwhile; ?>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
