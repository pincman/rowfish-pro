<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 分类展示滑块 catbox
 */
CSF::createWidget('pm_module_catbox_carousel', array(
    'title'       => esc_html__('PM: 首页分类滑块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-catbox-carousel',
    'description' => esc_html__('Displays a 分类BOX滑块', 'rizhuti-v2'),
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
            'id'          => 'category',
            'type'        => 'select',
            'title'       => '要展示的分类',
            'desc'        => '按顺序选择可以排序',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'orderby' => 'count',
                'order'   => 'DESC',
            ),
        ),

        array(
            'id'    => 'autoplay',
            'type'  => 'switcher',
            'title' => esc_html__('自动播放', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('pm_module_catbox_carousel')) {
    function pm_module_catbox_carousel($args, $instance)
    {
        if (!is_page_template_modular() || empty($instance['category'])) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '分类BOX滑块',
            'category' => 0,
            'autoplay' => true,
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
        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : '';
        ob_start(); ?>

        <div class="module catbox-carousel owl<?php echo $autoplay; ?>">
            <?php foreach ((array)$instance['category'] as $key => $cat_id) :
                $category = get_term($cat_id, 'category');
                $bg_img = get_term_meta($category->term_id, 'bg-image', true);
                $bg_img = (!empty($bg_img)) ? $bg_img : get_template_directory_uri() . '/assets/img/series-bg.jpg';
                $badge = array('success', 'info', 'warning', 'danger', 'light', 'primary', 'warning', 'danger', 'success', 'info', 'warning', 'light', 'primary',);
            ?>
                <div class="lazyload visible catbox-bg" data-bg="<?php echo esc_url($bg_img); ?>">
                    <a href="<?php echo get_category_link($category->term_id); ?>" class="catbox-block">
                        <div class="catbox-content">
                            <p class="mb-1"><?php echo $category->description; ?></p>
                            <span class="badge badge-<?php echo $badge[$key]; ?>-lighten mb-1"><?php echo $instance['item']; ?> <?php echo $category->count; ?>+</span>
                            <h3 class="catbox-title"><?php echo $category->name; ?></h3>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

<?php wp_reset_postdata();
        echo ob_get_clean();
        echo $args['after_widget'];
    }
}
