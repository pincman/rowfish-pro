<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 分类CMS文章展示块
 */
CSF::createWidget('pm_module_power_series', array(
    'title'       => esc_html__('PM: 轮播专题展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-cms',
    'description' => esc_html__('轮播幻灯+专题展示', 'rizhuti-v2'),
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
            'id'      => 'is_box_right',
            'type'    => 'switcher',
            'title'   => esc_html__('轮播右侧显示', 'rizhuti-v2'),
            'default' => false,
        ),
        array(
            'id'    => 'item',
            'type'  => 'text',
            'title' => esc_html__('项目名', 'rizhuti-v2'),
            'default' => '教程'
        ),
        array(
            'id'          => 'series',
            'type'        => 'select',
            'title'       => esc_html__('选择专题', 'rizhuti-v2'),
            'placeholder' => esc_html__('选择专题', 'rizhuti-v2'),
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'  => 'series',
            ),
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
if (!function_exists('pm_module_power_series')) {
    function pm_module_power_series($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '幻灯+专题展示块',
            'desc' => '',
            'item' => '教程',
            'is_box_right' => false,
            'series' => array(),
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
        $style_order = (!empty($instance['is_box_right'])) ? ' order-first' : '';
        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : ''; //autoplay
        ob_start(); ?>

        <div class="module posts-wrapper post-cms">
            <div class="row">
                <div class="<?php echo count($instance['series']) > 0 ? 'col-lg-6' : 'col-lg-12' ?> col-sm-12">
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
                <?php if (count($instance['series']) > 0) : ?>
                    <div class="col-lg-6 col-sm-12<?php echo esc_attr($style_order); ?>">
                        <div class="cms_grid_list">
                            <div class="row">
                                <?php foreach ($instance['series'] as $id) : ?>
                                    <div class="col-6">
                                        <article id="post-<?php the_ID(); ?>" <?php post_class('post post-grid'); ?>>
                                            <?php
                                            $serie = get_term_by('ID', $id, 'series');
                                            $bg_img = get_term_meta($serie->term_id, 'bg-image', true);
                                            $bg_img = (!empty($bg_img)) ? $bg_img : get_template_directory_uri() . '/assets/img/series-bg.jpg';
                                            $img   =
                                                '<div class="entry-media">';
                                            $img .= "<div class='placeholder' style='padding-bottom: 66.6%;'>";
                                            $img .= '<a href="' . esc_url(get_term_link($serie->term_id))  . '" title="' . $serie->name . '" rel="nofollow noopener noreferrer">';
                                            $img .= '<img class="lazyload" data-src="' . pm_get_size_thumbnail($bg_img) . '" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="' . $serie->name . '" />';

                                            $img .= '</a>';
                                            $img .= '</div>';
                                            $img .= '</div>';
                                            if (empty($serie)) {
                                                continue;
                                            }
                                            echo $img;
                                            ?>
                                            <div class="entry-wrapper">
                                                <header class="entry-header">
                                                    <h2 class="entry-title">
                                                        <a href="<?php echo get_category_link($serie->term_id); ?>" title="<?php echo $serie->name; ?>" rel="bookmark"><?php echo $serie->name; ?></a>
                                                    </h2>
                                                </header>
                                                <div class="entry-footer">
                                                    <div class="entry-meta">
                                                        <span class="text-light"><?php echo sprintf('本系列共%s个%s', $serie->count, $instance['item']); ?></span>
                                                        <span class="meta-shhop-icon"><i class="fas fa-play-circle"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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
