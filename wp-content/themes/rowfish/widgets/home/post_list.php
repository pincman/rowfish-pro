<?php if (!defined('ABSPATH')) {
    /*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-18 03:28:31 +0800
 * @Updated_at     : 2021-11-19 03:57:20 +0800
 * @Path           : /wp-content/themes/rowfish/widgets/home/post_list.php
 * @Description    : 首页分类文章列表小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
    die;
}
if (!function_exists('rf_get_home_post_list_fields')) {
    function rf_get_home_post_list_fields()
    {
        $orderby_options = array(
            'date'          => esc_html__('日期', 'rizhuti-v2'),
            'menu_order' => esc_html__('自定义(需要安装post type order插件)', 'rizhuti-v2'),
            'rand'          => esc_html__('随机', 'rizhuti-v2'),
            'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
            'views'         => esc_html__('阅读量', 'rizhuti-v2'),
            'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
            'title'         => esc_html__('标题', 'rizhuti-v2'),
            'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
        );
        $default_orderby = 'date';
        if (isPostTypesOrder()) {
            $orderby_options = array_merge(['menu_order' => esc_html__('自定义(根据post type order插件)', 'rizhuti-v2')], $orderby_options);
            $default_orderby = 'menu_order';
        }
        return array(
            array(
                'id'    => 'title',
                'type'  => 'text',
                'title' => esc_html__('标题', 'rizhuti-v2'),
            ),
            array(
                'id'    => 'desc',
                'type'  => 'text',
                'title' => esc_html__('描述', 'rizhuti-v2'),
            ),
            array(
                'id'          => 'categories',
                'type'        => 'select',
                'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
                'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
                'options'     => 'categories',
                'inline'      => true,
                'chosen'      => true,
                'multiple'    => true,
                'query_args'  => array(
                    'orderby' => 'count',
                    'order'   => 'DESC',
                ),
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
                'id'      => 'is_excerpt',
                'type'    => 'switcher',
                'title'   => esc_html__('显示摘要', 'rizhuti-v2'),
                'default' => true,
            ),
            array(
                'id'      => 'offset',
                'type'    => 'text',
                'title'   => esc_html__('第几页', 'rizhuti-v2'),
                'default' => 0,
            ),
        );
    }
}
/**
 * 分类文章展示
 */
CSF::createWidget('rf_home_post_list_widget', array(
    'title'       => esc_html__('RF: 首页-分类文章展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-carousel',
    'description' => esc_html__('田字格形式展示文章', 'rizhuti-v2'),
    'fields'      => rf_get_home_post_list_fields(),
));
if (!function_exists('rf_home_post_list_widget')) {
    function rf_home_post_list_widget($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '分类文章展示',
            'desc' => '',
            'item_style' => 'list',
            'is_excerpt' => true,
            'count' => 4,
            'offset' => 0,
            'category' => 0,
            'orderby' => isPostTypesOrder() ? 'menu_order' : 'date',
        ), $instance);
        if (!is_array($instance['categories'])) {
            $instance['categories'] = [];
        }

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo "<div style='display: flex; justify-content: space-between;'>";
            echo "<div class='widget-top'>";
            if (!empty($instance['title'])) {
                echo $args['before_title'] . $instance['title'] . $args['after_title'];
            }
            if (!empty($instance['desc'])) {
                echo "<small>{$instance['desc']}</small>";
            }
            echo "</div>";
            echo  '<a style="margin-bottom: 35px;" href="' . esc_url(get_category_link($instance['category'])) . '" class="float-right btn btn-outline-secondary btn-sm">' . esc_html__('查看全部', 'rizhuti-v2') . '<i class="fa fa-angle-right ml-1"></i></a>';
            echo "</div>";
        }
        // 查询
        $_args = array(
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );

        if (count($instance["categories"]) > 0) {
            $_args['tax_query'] = [
                'taxonomy' => 'categories',
                'field'    => 'term_id',
                'terms' => $instance["categories"],
                'operator' => 'IN'
            ];
        }

        if ($instance['orderby'] == 'menu_order') {
            $_args['orderby'] = ['menu_order' => 'ASC'];
        } else if ($instance['orderby'] == 'views') {
            $_args['orderby'] = ['views' => 'DESC', 'views_none' => 'DESC'];
            $_args['meta_query'] = array_merge($_args['meta_query'], [[
                'relation' => 'OR',
                ['views' => ['key' => '_views', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['views_none' => ['key' => '_views', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
            ]]);
        } else if ($instance['orderby'] == 'favnum') {
            $_args['orderby'] = ['favnum' => 'DESC', 'favnum_none' => 'DESC'];
            $_args['meta_query'] = array_merge($_args['meta_query'], [[
                'relation' => 'OR',
                ['favnum' => ['key' => '_favnum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['favnum_none' => ['key' => '_favnum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
            ]]);
        }


        $PostData = new WP_Query($_args);
        $col_classes =  'col-lg-6 col-12 small-block';
        ob_start(); ?>
        <div class="module posts-wrapper home-post-item archive-list">
            <div class="row">
                <?php while ($PostData->have_posts()) : $PostData->the_post(); ?>
                    <?php
                    $info = rf_get_post_info();
                    $summary = empty($info['summary']) ? rizhuti_v2_excerpt() : wp_trim_words(strip_shortcodes($info['summary']), '46', '...');
                    ?>
                    <div class="<?php echo esc_attr($col_classes); ?>">
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post post-list'); ?>>
                            <?php if (_cao('is_post_list_price', true)) {
                                echo get_post_meta_vip_price();
                            } ?>

                            <?php echo rf_get_post_media(null, ['width' => 250, 'height' => 190]); ?>
                            <div class="entry-wrapper">
                                <?php if (_cao('is_post_list_category', 1) && count(get_the_category()) > 0) {
                                    rizhuti_v2_category_dot(2);
                                } ?>
                                <header class="entry-header">
                                    <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                                </header>
                                <?php if (!empty($instance['is_excerpt'])) : ?>
                                    <div class="entry-excerpt"><?php echo $summary; ?></div>
                                <?php endif; ?>
                                <div class="entry-footer">
                                    <div class="entry-footer">
                                        <?php rizhuti_v2_entry_meta(
                                            array(
                                                'author' => _cao('is_post_list_author', 1),
                                                'category' => false,
                                                'comment' => _cao('is_post_list_comment', 1),
                                                'date' => _cao('is_post_list_date', 1),
                                                'favnum' => _cao('is_post_list_favnum', 1),
                                                'views' => _cao('is_post_list_views', 1),
                                                'shop' => _cao('is_post_list_shop', 1),
                                            )
                                        ); ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
