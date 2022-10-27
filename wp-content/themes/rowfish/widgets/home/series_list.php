<?php if (!defined('ABSPATH')) {
    /*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-18 05:55:56 +0800
 * @Updated_at     : 2021-11-19 03:58:04 +0800
 * @Path           : /wp-content/themes/rowfish/widgets/home/series_list.php
 * @Description    : 首页文章与课程专题数据列表块小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
    die;
}
if (!function_exists('rf_get_home_series_list_fields')) {
    function rf_get_home_series_list_fields()
    {
        $default_orderby =  'date';
        $post_orderby_options = [
            'date'          => esc_html__('日期', 'rizhuti-v2'),
            'rand'          => esc_html__('随机', 'rizhuti-v2'),
            'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
            'views'         => esc_html__('阅读量', 'rizhuti-v2'),
            'favnum'        => '收藏量',
            'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
            'title'         => esc_html__('标题', 'rizhuti-v2'),
            'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
        ];
        $course_orderby_options = [
            'date'          => esc_html__('日期', 'rizhuti-v2'),
            'views'         => '阅读量',
            'favnum'        => '收藏量',
            'paynum'        => '销量',
            'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
            'rand'          => esc_html__('随机', 'rizhuti-v2'),
            'title'         => esc_html__('标题', 'rizhuti-v2'),
            'ID'            => esc_html__('课程ID', 'rizhuti-v2'),
        ];
        if (isPostTypesOrder()) {
            $default_orderby =  'menu_order';
            $post_orderby_options = array_merge(['menu_order' => esc_html__('自定义(根据post type order插件)', 'rizhuti-v2')], $post_orderby_options);
            $course_orderby_options = array_merge(['menu_order' => esc_html__('自定义(根据post type order插件)', 'rizhuti-v2')], $course_orderby_options);
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
                'id'         => 'category_data',
                'type'       => 'group',
                'title'      => '新建',
                'fields'     => array(
                    array(
                        'id'    => 'item',
                        'type'  => 'text',
                        'title' => esc_html__('项目名', 'rizhuti-v2'),
                        'default' => '文章'
                    ),
                    array(
                        'id'      => 'count',
                        'type'    => 'text',
                        'title'   => esc_html__('显示数量', 'rizhuti-v2'),
                        'default' => 2,
                    ),
                    array(
                        'id' => 'type',
                        'type' => 'select',
                        'title' => '专题类型',
                        'options' => array(
                            'series' => '文章专题',
                            'course_series' => '课程专题',
                        ),
                        'inline' => true,
                        'default' => 'series',
                    ),
                    array(
                        'id'          => 'series',
                        'type'        => 'select',
                        'title'       => esc_html__('文章专题', 'rizhuti-v2'),
                        'placeholder' => esc_html__('选择专题', 'rizhuti-v2'),
                        'options'     => 'categories',
                        'query_args'  => array(
                            'taxonomy'  => 'series',
                            'orderby' => 'count',
                            'order'   => 'DESC',
                        ),
                        'dependency' => ['type', '==', 'series']
                    ),
                    array(
                        'id'          => 'course_series',
                        'type'        => 'select',
                        'title'       => esc_html__('课程专题', 'rizhuti-v2'),
                        'placeholder' => esc_html__('选择专题', 'rizhuti-v2'),
                        'options'     => 'categories',
                        'query_args'  => array(
                            'taxonomy'  => 'course_series',
                            'orderby' => 'count',
                            'order'   => 'DESC',
                        ),
                        'dependency' => ['type', '==', 'course_series']
                    ),
                    array(
                        'id'      => 'post_orderby',
                        'type'    => 'select',
                        'title'   => esc_html__('文章排序', 'rizhuti-v2'),
                        'options' => $post_orderby_options,
                        'default' => $default_orderby,
                        'dependency' => ['type', '==', 'series']
                    ),
                    array(
                        'id'      => 'course_orderby',
                        'type'    => 'select',
                        'title'   => esc_html__('课程排序', 'rizhuti-v2'),
                        'options' => $course_orderby_options,
                        'default' => $default_orderby,
                        'dependency' => ['type', '==', 'course_series']
                    ),
                    array(
                        'id'      => 'offset',
                        'type'    => 'text',
                        'title'   => esc_html__('起始页', 'rizhuti-v2'),
                        'default' => 0,
                    ),
                ),
            ),

        );
    }
}
/**
 * 专题文章CMS展示块
 */
CSF::createWidget('rf_home_series_list_widget', array(
    'title'       => esc_html__('RF: 首页-专题文章/课程模块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-list-cms',
    'description' => esc_html__('显示文章或课程专题下的数据列表', 'rizhuti-v2'),
    'fields'      => rf_get_home_series_list_fields(),
));

if (!function_exists('rf_home_series_list_widget')) {
    function rf_home_series_list_widget($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '专题数据展示',
            'desc' => '',
            'category_data' => array(),
            'count' => 2,
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
        ob_start(); ?>

        <div class="module posts-wrapper post-cms-title">
            <div class="row">
                <?php foreach ($instance['category_data'] as $item) {
                    $orderby = $item['type'] == 'course_series' ? 'course_orderby' : 'post_orderby';
                    $category = get_term_by('ID', $item[$item['type']], $item['type']);
                    if (empty($category)) {
                        continue;
                    }
                    $bg_img = get_term_meta($category->term_id, 'bg-image', true);
                    $bg_img = (!empty($bg_img)) ? $bg_img : get_template_directory_uri() . '/assets/img/series-bg.jpg';

                    $img   =
                        '<div class="entry-media">';
                    $img .= "<div class='placeholder' style='padding-bottom: 66.6%;'>";
                    $img .= '<a href="' . esc_url(get_term_link($category->term_id))  . '" title="' . $category->name . '" rel="nofollow noopener noreferrer">';
                    $img .= '<img class="lazyload" data-src="' . $bg_img . '" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="' . $category->name . '" />';

                    $img .= '</a>';
                    $img .= '</div>';
                    $img .= '</div>';

                    // 查询
                    $tax_query = array('relation' => 'OR', array(
                        'taxonomy' => $item['type'],
                        'field'    => 'term_id',
                        'terms' => $item[$item['type']],
                        'operator' => 'IN'
                    ));
                    $_args = array(
                        'tax_query' => $tax_query,
                        'ignore_sticky_posts' => true,
                        'post_status'         => 'publish',
                        'posts_per_page'      => (int)$item['count'],
                        'paged'              => (int)$item['offset'],
                        'orderby'             => $orderby,
                    );
                    if ($orderby == 'menu_order') {
                        $_args['orderby'] = ['menu_order' => 'ASC'];
                    } else if ($orderby == 'views') {
                        $_args['orderby'] = ['views' => 'DESC', 'views_none' => 'DESC'];
                        $_args['meta_query'] = array_merge($_args['meta_query'], [[
                            'relation' => 'OR',
                            ['views' => ['key' => '_views', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                            ['views_none' => ['key' => '_views', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
                        ]]);
                    } else if ($orderby == 'paynum') {
                        $_args['orderby'] = ['paynum' => 'DESC', 'paynum_none' => 'DESC'];
                        $_args['meta_query'] = array_merge($_args['meta_query'], [[
                            'relation' => 'OR',
                            ['paynum' => ['key' => '_paynum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                            ['paynum_none' => ['key' => '_paynum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
                        ]]);
                    } else if ($orderby == 'favnum') {
                        $_args['orderby'] = ['favnum' => 'DESC', 'favnum_none' => 'DESC'];
                        $_args['meta_query'] = array_merge($_args['meta_query'], [[
                            'relation' => 'OR',
                            ['favnum' => ['key' => '_favnum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                            ['favnum_none' => ['key' => '_favnum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
                        ]]);
                    }
                    $PostData = new WP_Query($_args);
                    $i = 0;
                    echo '<div class="col-lg-3 col-12"><div class="card mb-4">';
                    echo '<div class="cat-info">';
                    echo '<span class="text-light">' . sprintf(__('共%s个%s', 'rizhuti-v2'), $category->count, $item['item']) . '</span>';
                    echo '<h3><a href="' . esc_url(get_term_link($category->term_id)) . '" rel="category">' . $category->name . '<span class="badge badge-danger ml-2"></span></a></h3>';
                    echo '</div>';

                    while ($PostData->have_posts()) : $PostData->the_post();
                        $i++;
                        if ($i == 1) {
                            echo $img;
                            echo '<div class="card-body">';
                            rizhuti_v2_entry_title(array('link' => true, 'tag' => 'h5', 'classes' => 'card-title text-nowrap-ellipsis'));
                            echo '<p class="card-text text-muted small text-nowrap-ellipsis">' . rizhuti_v2_excerpt() . '</p>';
                            echo '</div>';
                            echo '<ul class="list-group list-group-flush mt-0 mb-2">';
                        } else {
                            echo '<li class="list-group-item py-2 text-nowrap-ellipsis"><a href="' . esc_url(get_permalink()) . '" title="' . get_the_title() . '" rel="bookmark">' . get_the_title() . '</a></li>';
                        }
                    endwhile;
                    wp_reset_postdata();
                    echo '<li class="list-group-item py-2 text-nowrap-ellipsis"><a href="' . esc_url(get_term_link($category->term_id)) . '" rel="category" class="btn btn-light btn-block">' . esc_html__('查看专题', 'rizhuti-v2') . '<i class="fa fa-angle-right ml-1"></i></a></li>';
                    echo '</ul></div></div>';
                } ?>
            </div>
        </div>
<?php echo ob_get_clean();
        echo $args['after_widget'];
    }
}
