<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-18 00:09:25 +0800
 * @Updated_at     : 2021-11-19 05:33:38 +0800
 * @Path           : /wp-content/themes/rowfish/course/widgets/home_course_power.php
 * @Description    : 首页视频课程田字格列表+自定义幻灯片小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
if (!defined('ABSPATH')) {
    die;
}
if (!function_exists('rf_home_course_power_fields')) {
    function rf_home_course_power_fields()
    {
        $orderby_options = [
            'date'          => esc_html__('日期', 'rizhuti-v2'),
            'views'         => '阅读量',
            'favnum'        => '收藏量',
            'paynum'        => '销量',
            'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
            'rand'          => esc_html__('随机', 'rizhuti-v2'),
            'title'         => esc_html__('标题', 'rizhuti-v2'),
            'ID'            => esc_html__('课程ID', 'rizhuti-v2'),
        ];
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
                'id'      => 'is_box_right',
                'type'    => 'switcher',
                'title'   => esc_html__('轮播右侧显示', 'rizhuti-v2'),
                'default' => false,
            ),

            array(
                'id'          => 'categories',
                'type'        => 'select',
                'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
                'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
                'options'     => 'categories',
                'multiple' => true,
                'inline'      => true,
                'chosen'      => true,
                'query_args' => [
                    'taxonomy' => 'course_category',
                    'orderby' => 'count',
                    'order' => 'DESC',
                ],
            ),
            array(
                'id'      => 'orderby',
                'type'    => 'select',
                'title'   => esc_html__('排序方式', 'rizhuti-v2'),
                'options' => $orderby_options,
                'default' => $default_orderby
            ),

            array(
                'id'      => 'offset',
                'type'    => 'text',
                'title'   => esc_html__('起始页', 'rizhuti-v2'),
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

        );
    }
}
/**
 * 分类CMS文章展示块
 */
CSF::createWidget('rf_home_course_power_widget', array(
    'title'       => esc_html__('RF: 首页-轮播课程展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-cms',
    'description' => esc_html__('轮播幻灯+课程展示', 'rizhuti-v2'),
    'fields'      => rf_home_course_power_fields()
));
if (!function_exists('rf_home_course_power_widget')) {
    function rf_home_course_power_widget($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示
        $instance = array_merge(array(
            'title' => '幻灯+分类课程展示块',
            'desc' => '',
            'is_box_right' => false,
            'offset' => 0,
            'categories' => [],
            'orderby' => isPostTypesOrder() ? 'menu_order' : 'date',
            'diy_data' => array(),
        ), $instance);
        if (!is_array($instance['categories'])) $instance['categories'] = [];


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
            'post_type' => 'course',
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => 4,
            'paged'              => (int)$instance['offset'],
        );
        if (count($instance["categories"]) > 0) {
            $_args['tax_query'] = [
                'taxonomy' => 'course_categories',
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
        } else if ($instance['orderby'] == 'paynum') {
            $_args['orderby'] = ['paynum' => 'DESC', 'paynum_none' => 'DESC'];
            $_args['meta_query'] = array_merge($_args['meta_query'], [[
                'relation' => 'OR',
                ['paynum' => ['key' => '_paynum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['paynum_none' => ['key' => '_paynum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
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
        $i = 0;
        $style_order = (!empty($instance['is_box_right'])) ? ' order-first' : '';
        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : ''; //autoplay
        ob_start(); ?>

        <div class="module posts-wrapper post-cms">
            <div class="container">
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
                                <div class="row">
                                    <?php
                                    while ($PostData->have_posts()) :
                                        $PostData->the_post();
                                        $info = rf_get_post_info();
                                    ?>
                                        <div class="col-6">
                                            <article id="post-<?php the_ID(); ?>" <?php post_class('post post-grid'); ?>>

                                                <?php echo rf_get_post_media(null, 'thumbnail'); ?>
                                                <div class="entry-wrapper">
                                                    <header class="entry-header">
                                                        <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                                                    </header>
                                                    <div class="entry-footer">
                                                        <?php rf_show_course_entry_meta(
                                                            array(
                                                                'author' => _cao('is_course_list_author', 1),
                                                                'edit' => _cao('is_course_grid_edit', 1),
                                                                'favnum' => _cao('is_course_list_favnum', 1),
                                                                'views' => _cao('is_course_list_views', 1),
                                                                'shop' => _cao('is_course_list_shop', 1),
                                                                'date' => _cao('is_course_list_date', 1),
                                                            ),
                                                            $info
                                                        ); ?>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                        <?php $i++; ?>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
