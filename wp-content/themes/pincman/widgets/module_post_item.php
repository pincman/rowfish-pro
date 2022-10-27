<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 分类文章展示
 */
CSF::createWidget('pincman_module_post_item', array(
    'title'       => esc_html__('PM : 分类文章展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-carousel',
    'description' => esc_html__('Displays a 文章展示', 'rizhuti-v2'),
    'fields'      => array(

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
            'id'          => 'category',
            'type'        => 'select',
            'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
            'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
            'options'     => 'categories',
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => array(
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'menu_order' => esc_html__('自定义(需要安装post type order插件)', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                'views'         => esc_html__('阅读量', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ),
        ),

        // 分类页布局
        array(
            'id'          => 'item_style',
            'type'        => 'select',
            'title'       => '布局风格',
            'placeholder' => '',
            'options'     => array(
                'list' => esc_html__('列表', 'rizhuti-v2'),
                'grid' => esc_html__('网格', 'rizhuti-v2'),
            ),
            'default'     => 'list',
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

    ),
));
if (!function_exists('pincman_module_post_item')) {
    function pincman_module_post_item($args, $instance)
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
            'orderby' => 'date',
        ), $instance);


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
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );

        if ($instance['orderby'] == 'menu_order') {
            $_args['orderby'] = ['menu_order' => 'ASC'];
        } else if ($instance['orderby'] == 'views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }


        $PostData = new WP_Query($_args);
        $col_classes = ($instance['item_style'] == 'list') ? 'col-lg-6 col-12' : 'col-lg-5ths col-lg-3 col-md-4 col-6';
        ob_start(); ?>
        <div class="module posts-wrapper home-post-item <?php echo esc_attr($instance['item_style']); ?>">
            <div class="row">
                <?php while ($PostData->have_posts()) : $PostData->the_post(); ?>
                    <?php
                    $uinfo = pm_shop_post_info();
                    $course_status = get_post_meta($uinfo['post_id'], 'wppay_course_status', 1);
                    ?>
                    <?php if ($course_status != '1' && $course_status != '2') : ?>
                        <?php continue; ?>
                    <?php else : ?>
                        <div class="<?php echo esc_attr($col_classes); ?>">
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post post-' . $instance['item_style']); ?>>
                                <?php
                                //  if (_cao('is_post_' . $instance['item_style'] . '_price', true)) {
                                //     echo get_post_meta_vip_price();
                                // } 
                                echo pm_course_status_icon();
                                ?>

                                <?php echo pm_get_post_media(null, 'thumbnail'); ?>
                                <div class="entry-wrapper">
                                    <?php if (_cao('is_post_' . $instance['item_style'] . '_category', 1)) {
                                        rizhuti_v2_category_dot(2);
                                    } ?>
                                    <header class="entry-header">
                                        <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                                    </header>
                                    <?php if (!empty($instance['is_excerpt'])) : ?>
                                        <div class="entry-excerpt"><?php echo rizhuti_v2_excerpt(); ?></div>
                                    <?php endif; ?>
                                    <div class="entry-footer">
                                        <?php rizhuti_v2_entry_meta(array(
                                            'author' => _cao('is_post_' . $instance['item_style'] . '_author', 1),
                                            'category' => false,
                                            'comment' => _cao('is_post_' . $instance['item_style'] . '_comment', 1),
                                            'date' => _cao('is_post_' . $instance['item_style'] . '_date', 1),
                                            'favnum' => _cao('is_post_' . $instance['item_style'] . '_favnum', 1),
                                            'views' => _cao('is_post_' . $instance['item_style'] . '_views', 1),
                                            'shop' => _cao('is_post_' . $instance['item_style'] . '_shop', 1),
                                        )); ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
