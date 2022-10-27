<?php if (!defined('ABSPATH')) {
    die;
}
/**
 * 视差背景
 */
CSF::createWidget('new_module_parallax', array(
    'title'       => esc_html__('PM: 视差背景', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-parallax',
    'description' => esc_html__('Displays a parallax background.', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'image',
            'type'  => 'upload',
            'title' => esc_html__('背景图', 'rizhuti-v2'),
            'default'     => get_template_directory_uri() . '/assets/img/top-bg.jpg',
        ),
        array(
            'id'       => 'text',
            'type'     => 'text',
            'title'    => esc_html__('标题', 'rizhuti-v2'),
            'default'  => esc_html__('标题', 'rizhuti-v2'),
            'sanitize' => false,
        ),
        array(
            'id'      => 'desc',
            'type'    => 'textarea',
            'title'   => '描述',
            'sanitize' => false,
            'default' => '',
        ),

        array(
            'id'    => 'link',
            'type'  => 'Text',
            'title' => esc_html__('主链接', 'rizhuti-v2'),
        ),

        array(
            'id'    => 'new_tab',
            'type'  => 'switcher',
            'title' => esc_html__('新窗口打开链接？', 'rizhuti-v2'),
        ),

        array(
            'id'      => 'primary_text',
            'type'    => 'Text',
            'title'   => esc_html__('按钮1文字', 'rizhuti-v2'),
            'default' => '<i class="fa fa-credit-card"></i> 关于我们',
        ),

        array(
            'id'    => 'primary_link',
            'type'  => 'Text',
            'title' => esc_html__('按钮1链接', 'rizhuti-v2'),
        ),

        array(
            'id'    => 'primary_new_tab',
            'type'  => 'switcher',
            'title' => esc_html__('按钮1新窗口打开链接？', 'rizhuti-v2'),
        ),

        array(
            'id'      => 'secondary_text',
            'type'    => 'Text',
            'title'   => esc_html__('按钮2文本', 'rizhuti-v2'),
            'default' => '<i class="fa fa-paw"></i> 更多介绍',
        ),

        array(
            'id'    => 'secondary_link',
            'type'  => 'Text',
            'title' => esc_html__('按钮2链接', 'rizhuti-v2'),
        ),

        array(
            'id'    => 'secondary_new_tab',
            'type'  => 'switcher',
            'title' => esc_html__('按钮2新窗口打开链接？', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('new_module_parallax')) {
    function new_module_parallax($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        $instance = array_merge(array(
            'image' => get_template_directory_uri() . '/assets/img/top-bg.jpg',
            'text' => '文字描述介绍',
            'desc' => ''
        ), $instance);


        echo $args['before_widget'];

        ob_start(); ?>
        <div class="module parallax">
            <?php if (!empty($instance['image'])) : ?>
                <img class="jarallax-img lazyload" data-src="<?php echo esc_url($instance['image']); ?>" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="<?php echo esc_attr($instance['text']); ?>">
            <?php endif;

            if ($instance['text'] != '') : ?>
                <div class="container">
                    <h4 class="entry-title"><?php echo $instance['text']; ?></h4>
                    <?php if (!empty($instance['desc'])) : ?>
                        <div class="entry-footer">
                            <p><?php echo $instance['desc']; ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($instance['primary_text'] != '') : ?>
                        <a class="btn btn-outline-warning btn-lg" href="<?php echo esc_url($instance['primary_link']); ?>" <?php echo esc_attr($instance['primary_new_tab'] ? ' target="_blank"' : ''); ?>><?php echo $instance['primary_text']; ?></a>
                    <?php endif; ?>
                    <?php if ($instance['secondary_text'] != '') : ?>
                        <a class="btn btn-light btn-sm" href="<?php echo esc_url($instance['secondary_link']); ?>" <?php echo esc_attr($instance['secondary_new_tab'] ? ' target="_blank"' : ''); ?>><?php echo $instance['secondary_text']; ?></a>
                    <?php endif; ?>
                </div>
            <?php endif;

            if (!empty($instance['link'])) : ?>
                <a class="u-permalink" href="<?php echo esc_url($instance['link']); ?>" <?php echo esc_attr($instance['new_tab'] ? ' target="_blank"' : ''); ?>></a>
            <?php endif; ?>
        </div> <?php

                echo ob_get_clean();

                echo $args['after_widget'];
            }
        }
