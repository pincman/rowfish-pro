<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 分类CMS文章展示块
 */
CSF::createWidget('pm_module_power_post', array(
    'title'       => esc_html__('PM: 轮播文章展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-cms',
    'description' => esc_html__('轮播幻灯+文章展示', 'rizhuti-v2'),
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
        array(
            'id'     => 'diy_data',
            'type'   => 'group',
            'title'  => '轮播幻灯片',
            'fields' => array(
                array(
                    'id'          => '_img',
                    'type'        => 'upload',
                    'title'       => '上传幻灯片',
                    'default'     => get_template_directory_uri() . '/assets/img/bg.jpg',
                ),
                array(
                    'id'      => '_blank',
                    'type'    => 'switcher',
                    'title'   => '新窗口打开链接',
                    'default' => true,
                ),
                array(
                    'id'      => '_href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'default' => '',
                ),
                array(
                    'id' => 'title',
                    'type' => 'text',
                    'title' => '标题',
                    'default' => '幻灯片标题'
                ),
                array(
                    'id'      => '_desc',
                    'type'    => 'textarea',
                    'title'   => '描述内容',
                    'sanitize' => false,
                    'default' => '幻灯片描述',
                ),

            ),

        ),
        array(
            'id'    => 'autoplay',
            'type'  => 'switcher',
            'title' => esc_html__('幻灯片自动播放', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('pm_module_power_post')) {
    function pm_module_power_post($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示
        $instance = array_merge(array(
            'title' => '幻灯+分类文章展示块',
            'desc' => '',
            'style' => 'list',
            'is_box_right' => false,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'menu_order',
            'diy_data' => array(),
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
            'posts_per_page'      => 4,
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
        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : ''; //autoplay
        ob_start(); ?>

        <div class="module posts-wrapper post-cms">
            <div class="row">
                <div class="<?php echo $PostData->post_count > 0 ? 'col-lg-6' : 'col-lg-12'; ?> col-sm-12">
                    <div class="cms_grid_box lazyload">
                        <div class="module slider img-center owl<?php echo $autoplay; ?>" style="height: 100%;">
                            <?php foreach ($instance['diy_data'] as $item) : ?>
                                <div class="slider lazyload visible" data-bg="<?php echo  esc_url($item['_img']); ?>">
                                    <article>
                                        <div class="entry-wrapper">
                                            <header class="entry-header">
                                                <?php
                                                if (!empty($item['_href'])) {
                                                    echo '<h2 class="entry-title"><a href="' . esc_url($item['_href']) . '" title="' . strip_tags($item['title']) . '" rel="bookmark">' . strip_tags($item['title']) . '</a></h2>';
                                                } else {
                                                    echo '<h2  class="entry-title">' . strip_tags($item['title']) . '</h2>';
                                                }
                                                ?>
                                            </header>
                                            <div class="entry-footer">
                                                <p><?php echo strip_tags($item['_desc']); ?></p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
                <?php if ($PostData->post_count > 0) : ?>
                    <div class="col-lg-6 col-sm-12<?php echo esc_attr($style_order); ?>">
                        <div class="cms_grid_list">
                            <?php if ($style == 'grid') : ?><div class="row"><?php endif; ?>
                                <?php while ($PostData->have_posts()) : $PostData->the_post(); ?>
                                    <?php if ($style == 'grid') : ?><div class="col-6"><?php endif; ?>
                                        <article id="post-<?php the_ID(); ?>" <?php post_class('post post-' . $style); ?>>

                                            <?php echo pm_get_post_media(null, 'thumbnail'); ?>
                                            <div class="entry-wrapper">
                                                <header class="entry-header">
                                                    <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                                                </header>
                                                <div class="entry-footer">
                                                    <?php rizhuti_v2_entry_meta(array(
                                                        // 'author' => _cao('is_post_' . $style . '_author', 1),
                                                        'category' => _cao('is_post_' . $style . '_category', 1),
                                                        'comment' => _cao('is_post_' . $style . '_comment', 1),
                                                        // 'date' => _cao('is_post_' . $style . '_date', 1),
                                                        'favnum' => _cao('is_post_' . $style . '_favnum', 1),
                                                        'views' => _cao('is_post_' . $style . '_views', 1),
                                                        'shop' => _cao('is_post_' . $style . '_shop', 1),
                                                    )); ?>
                                                </div>
                                            </div>
                                        </article>
                                        <?php if ($style == 'grid') : ?>
                                        </div><?php endif; ?>
                                    <?php $i++; ?>
                                <?php endwhile; ?>
                                <?php if ($style == 'grid') : ?></div><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
