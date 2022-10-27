<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 专题文章CMS展示块
 */
CSF::createWidget('pm_module_cms_list', array(
    'title'       => esc_html__('PM: 首页专题文章模块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-list-cms',
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
            'id'    => 'item',
            'type'  => 'text',
            'title' => esc_html__('项目名', 'rizhuti-v2'),
            'default' => '文章'
        ),
        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 2,
        ),
        array(
            'id'         => 'category_data',
            'type'       => 'group',
            'title'      => '新建',
            'fields'     => array(
                array(
                    'id'          => 'category',
                    'type'        => 'select',
                    'title'       => esc_html__('选择专题', 'rizhuti-v2'),
                    'placeholder' => esc_html__('选择专题', 'rizhuti-v2'),
                    'options'     => 'categories',
                    'query_args'  => array(
                        'taxonomy'  => 'series',
                    ),
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
        ),

    ),
));

if (!function_exists('pm_module_cms_list')) {
    function pm_module_cms_list($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '专题文章展示',
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
                    $category = get_term_by('ID', $item['category'], 'series');
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
                    if (empty($category)) {
                        continue;
                    }

                    // 查询
                    $tax_query = array('relation' => 'OR', array(
                        'taxonomy' => 'series',
                        'field'    => 'term_id',
                        'terms' => $item['category'],
                        'operator' => 'IN'
                    ));
                    $_args = array(
                        'tax_query' => $tax_query,
                        'ignore_sticky_posts' => true,
                        'post_status'         => 'publish',
                        'posts_per_page'      => (int)$instance['count'],
                        'paged'              => (int)$item['offset'],
                        'orderby'             => $item['orderby'],
                    );
                    $PostData = new WP_Query($_args);
                    $i = 0;
                    echo '<div class="col-lg-3 col-12"><div class="card mb-4">';
                    echo '<div class="cat-info">';
                    echo '<span class="text-light">' . sprintf(__('共%s个%s', 'rizhuti-v2'), $category->count, $instance['item']) . '</span>';
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
