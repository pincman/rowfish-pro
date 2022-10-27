<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 分类CMS文章展示块
 */
CSF::createWidget('pm_module_dospress_links', array(
    'title'       => esc_html__('PM: 文档模块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-cms',
    'description' => esc_html__('文档链接', 'rizhuti-v2'),
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
    ),
));
if (!function_exists('pm_module_dospress_links')) {
    function pm_module_dospress_links($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '幻灯+专题展示块',
            'desc' => '',
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
        // $_args = array(
        //     'cat'                 => (int)$instance['category'],
        //     'ignore_sticky_posts' => true,
        //     'post_status'         => 'publish',
        //     'posts_per_page'      => 4,
        //     'paged'              => (int)$instance['offset'],
        // );
        // if ($instance['orderby'] == 'menu_order') {
        //     $_args['orderby'] = ['menu_order' => 'ASC'];
        // } else if ($instance['orderby'] == 'views') {
        //     $_args['meta_key'] = '_views';
        //     $_args['orderby'] = 'meta_value_num';
        //     $_args['order'] = 'DESC';
        // }

        // $PostData = new WP_Query($_args);
        // $i = 0;
        $params = array(
            'post_type'      => 'docs',
            'posts_per_page' => -1, // phpcs:ignore
            'post_parent'    => 0,
            'orderby'        => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
        );
        $categories = get_terms(
            array(
                'taxonomy'   => 'docs_category',
                'hide_empty' => false,
            )
        );

        if (!empty($categories)) {
            $docs_by_cat = array(
                0 => array(),
            );

            // get all available terms in array.
            foreach ($categories as $cat) {
                $docs_by_cat[$cat->slug] = array();
            }

            // get parent docs.
            $parent_docs = get_pages(
                array(
                    'post_type'   => 'docs',
                    'parent'      => 0,
                    'sort_column' => 'menu_order',
                )
            );
            if ($parent_docs) {
                // set all doc IDs to array by terms.
                foreach ($parent_docs as $doc) {
                    $term = get_the_terms($doc, 'docs_category');

                    if ($term && !empty($term)) {
                        $term = $term[0]->slug;
                    } else {
                        $term = 0;
                    }

                    $docs_by_cat[$term][] = $doc->ID;
                }

                // add posts IDs in post__in.
                if (count($docs_by_cat) >= 2) {
                    $params['post__in'] = array();
                    foreach ($docs_by_cat as $docs) {
                        $params['post__in'] = array_merge($params['post__in'], $docs);
                    }
                    $params['orderby'] = 'post__in';
                }
            }
        }

        $wp_query = new WP_Query($params);

        ob_start(); ?>

        <div class="module posts-wrapper post-cms">
            <?php
            $i = 0;
            $current_term = false;
            if ($wp_query->have_posts()) : ?>
                <div class="row">
                    <?php while ($wp_query->have_posts()) : $wp_query->the_post();
                        // phpcs:ignore
                        $terms = wp_get_post_terms(get_the_ID(), 'docs_category');
                        if (
                            $terms &&
                            !empty($terms) &&
                            isset($terms[0]->name) &&
                            $current_term !== $terms[0]->name
                        ) {
                            // phpcs:ignore
                            $current_term = $terms[0]->name;
                        }
                        if ($i > 5 || !empty(get_post_meta(get_the_ID(), 'course', true))) break;
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                            <div class="card mb-lg-4 mb-2 card-hover">
                                <div class="d-flex justify-content-between align-items-center p-3 doc-card">
                                    <a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>">
                                        <div class="d-flex align-items-center">
                                            <?php the_post_thumbnail('docspress_archive'); ?>
                                            <div class="ml-3">
                                                <b class=""><?php echo the_title(); ?></b>
                                                <p class="mb-0 small text-muted"><?php echo get_the_content(); ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php $i++;
                    endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
