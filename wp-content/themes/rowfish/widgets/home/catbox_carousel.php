<?php if (!defined('ABSPATH')) {
    /*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-18 01:37:48 +0800
 * @Updated_at     : 2021-11-19 03:45:13 +0800
 * @Path           : /wp-content/themes/rowfish/widgets/home/catbox_carousel.php
 * @Description    : 首页分类与专题滑块小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
    die;
}
/**
 * 分类展示滑块 catbox
 */
CSF::createWidget('rf_home_catbox_carousel_widget', array(
    'title'       => esc_html__('RF: 首页-分类/专题滑块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-catbox-carousel',
    'description' => esc_html__('文章/课程的分类和专题的BOX滑块', 'rizhuti-v2'),
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
        [
            'id' => 'type',
            'type' => 'select',
            'title' => '类别',
            'options' => array(
                'category' => '文章分类',
                'course_category' => '课程分类',
                'series' => '文章专题',
                'course_series' => '课程专题',
            ),
            'inline' => true,
            'default' => 'category',
        ],
        array(
            'id'          => 'categories',
            'type'        => 'select',
            'title'       => '要展示的文章分类',
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
            'dependency' => ['type', '==', 'category']
        ),
        array(
            'id'          => 'course_categories',
            'type'        => 'select',
            'title'       => '要展示的课程分类',
            'desc'        => '按顺序选择可以排序',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'  => 'course_category',
                'orderby' => 'count',
                'order'   => 'DESC',
            ),
            'dependency' => ['type', '==', 'course_category']
        ),
        array(
            'id'          => 'series',
            'type'        => 'select',
            'title'       => '要展示的文章专题',
            'desc'        => '按顺序选择可以排序',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
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
            'title'       => '要展示的课程专题',
            'desc'        => '按顺序选择可以排序',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'  => 'course_series',
                'orderby' => 'count',
                'order'   => 'DESC',
            ),
            'dependency' => ['type', '==', 'course_series']
        ),
        array(
            'id'    => 'autoplay',
            'type'  => 'switcher',
            'title' => esc_html__('自动播放', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('rf_home_catbox_carousel_widget')) {
    function rf_home_catbox_carousel_widget($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'title' => '分类BOX滑块',
            'type' => 'category',
            'autoplay' => true,
        ), $instance);

        $categories = [];
        switch ($instance['type']) {
            case 'course_category':
                $categories = $instance['course_categories'];
                break;
            case 'series':
                $categories = $instance['series'];
                break;
            case 'course_series':
                $categories = $instance['course_series'];
                break;
            default:
                $categories = $instance['categories'];
                break;
        }
        if (!is_array($categories)) {
            $categories = [];
        }
        if (count($categories) <= 0) return false;

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
            <?php foreach ((array)$categories as $key => $cat_id) :
                $category = get_term($cat_id, $instance['type']);
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
