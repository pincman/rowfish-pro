<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 专题文章CMS展示块
 */
CSF::createWidget('pm_child_categories', array(
    'title'       => esc_html__('PM: 子分类列表', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post',
    'description' => esc_html__('显示一个分类的子分类', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
            'default' => '分类列表'
        ),
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => '父分类',
            'placeholder' => '选择分类',
            'options'     => 'categories',
            'default' => null
        ),
        array(
            'id'      => 'post_types',
            'type'    => 'select',
            'title'   => '启用的文章类型',
            'options' => array(
                '0' => esc_html__('免费文章', 'rizhuti-v2'),
                '1' => esc_html__('付费全文文章', 'rizhuti-v2'),
                '2' => esc_html__('付费隐藏内容文章', 'rizhuti-v2'),
                // '3' => esc_html__('付费下载', 'rizhuti-v2'),
                '4' => esc_html__('下载资源', 'rizhuti-v2'),
                '5' => esc_html__('视频教程', 'rizhuti-v2'),
                '7' => esc_html__('开源推荐', 'rizhuti-v2'),
                // '6' => esc_html__('图片相册', 'rizhuti-v2'),
            ),
            'multiple'  => true,
            'default' => ['0', '1', '2'],
        ),
    ),
));
if (!function_exists('pm_child_categories')) {
    function pm_child_categories($args, $instance)
    {
        if (is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '分类列表',
            'category' => null,
            'post_types' => ['0', '1', '2'],
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        $categories = !is_null($instance['category']) ? get_categories(array('parent' => $instance['category'])) : get_categories();
        ob_start(); ?>
        <div class="posts-wrapper list">
            <?php foreach ($categories as $key => $cat) : ?>
                <?php
                $ratio = (2 / 3) * 100 . '%';
                $bg_img = get_term_meta($cat->term_id, 'bg-image', true);
                $bg_img = (!empty($bg_img)) ? $bg_img : get_template_directory_uri() . '/assets/img/series-bg.jpg';
                $img   = '<div class="entry-media">';
                // $img .= '<div class="placeholder" style="padding-bottom: ' . esc_attr($ratio) . '">';
                $img .= '<a class="img-link" href="' . get_category_link($cat->term_id) . '" title="' . $cat->name . '" style="background-image: url(' . $bg_img . ');">';
                // $img .= '<img class="lazyload" data-src="' . $bg_img . '" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="' . get_the_title() . '" />';

                $img .= '</a>';
                // $img .= '</div>';
                $img .= '</div>';
                ?>
                <article id="category-<?php echo $cat->term_id; ?>" <?php post_class('post post-list category-widget-block'); ?>>
                    <?php echo $img; ?>
                    <div class="entry-wrapper">
                        <header class="">
                            <?php echo '<h2 class="entry-title"><a href="' . get_category_link($cat->term_id) . '" title="' . $cat->name . '" rel="bookmark">' . $cat->name . '</a></h2>'; ?>
                        </header>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
