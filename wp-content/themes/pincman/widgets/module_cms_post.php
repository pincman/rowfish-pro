<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 分类CMS文章展示块
 */
CSF::createWidget('pm_module_cms_post', array(
    'title'       => esc_html__('PM: 分类文章展示(田字格)', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-cms',
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
            'id'      => 'style',
            'type'    => 'select',
            'title'   => esc_html__('布局风格', 'rizhuti-v2'),
            'options' => array(
                'list'          => esc_html__('左轮播-右列表', 'rizhuti-v2'),
                'grid'          => esc_html__('左轮播-右网格', 'rizhuti-v2'),
            ),
            'default' => 'list',
        ),
        array(
            'id'      => 'is_box_right',
            'type'    => 'switcher',
            'title'   => esc_html__('轮播右侧显示', 'rizhuti-v2'),
            'default' => false,
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

        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),

    ),
));
if (!function_exists('pm_module_cms_post')) {
    function pm_module_cms_post($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '分类文章展示',
            'desc' => '',
            'style' => 'list',
            'is_box_right' => false,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'menu_order',
        ), $instance);


        echo $args['before_widget'];

        echo "<div class='widget-top'>";
        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        if (!empty($instance['desc'])) {
            echo "<small>{$instance['desc']}</small>";
        }
        echo "</div>";
        // 查询
        $_args = array(
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => 5,
            'paged'              => (int)$instance['offset'],
        );
        if ($instance['orderby'] == 'menu_order') {
            $_args['orderby'] = ['menu_order' => 'ASC'];
        } else if ($instance['orderby'] == 'views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }

        $PostData = new WP_Query($_args);
        $i = 0;
        $style = $instance['style'];
        $style_order = (!empty($instance['is_box_right'])) ? ' order-first' : '';
        ob_start(); ?>

        <div class="module posts-wrapper post-cms">
            <div class="row">
                <?php while ($PostData->have_posts()) : $PostData->the_post();
                    $i++; ?>
                    <?php if ($i == 1) : ?>
                        <div class="col-lg-6 col-sm-12">
                            <div class="cms_grid_box lazyload" data-bg="<?php echo pm_get_post_thumbnail_url(null, 'full'); ?>">
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                                    <div class="entry-wrapper">
                                        <header class="entry-header">
                                            <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                                        </header>
                                        <div class="entry-footer">
                                            <?php rizhuti_v2_entry_meta(array('category' => true, 'author' => true, 'views' => true, 'date' => true)); ?>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12<?php echo esc_attr($style_order); ?>">
                            <div class="cms_grid_list">
                                <?php if ($style == 'grid') {
                                    echo '<div class="row">';
                                } ?>
                            <?php else : ?>
                                <?php if ($style == 'grid') {
                                    echo '<div class="col-6">';
                                } ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post post-' . $style); ?>>

                                    <?php echo pm_get_post_media(null, 'thumbnail'); ?>
                                    <div class="entry-wrapper">
                                        <header class="entry-header">
                                            <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                                        </header>
                                        <div class="entry-footer">
                                            <?php rizhuti_v2_entry_meta(array(
                                                'author' => _cao('is_post_' . $style . '_author', 1),
                                                'category' => _cao('is_post_' . $style . '_category', 1),
                                                'comment' => _cao('is_post_' . $style . '_comment', 1),
                                                'date' => _cao('is_post_' . $style . '_date', 1),
                                                'favnum' => _cao('is_post_' . $style . '_favnum', 1),
                                                'views' => _cao('is_post_' . $style . '_views', 1),
                                                'shop' => _cao('is_post_' . $style . '_shop', 1),
                                            )); ?>
                                        </div>
                                    </div>
                                </article>
                                <?php if ($style == 'grid') {
                                    echo '</div>';
                                } ?>
                            <?php endif; ?>
                        <?php endwhile; ?>
                        <?php if ($style = 'grid') {
                            echo '</div>';
                        } ?>
                            </div>
                        </div>

            </div>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
