<?php if (!defined('ABSPATH')) {
    die;
}

CSF::createWidget('pm_module_homeback_start', array(
    'title'       => esc_html__('PM: 首页半高背景(开始)', 'rizhuti-v2'),
    'classname'   => 'pm_module_homeback_start',
    'description' => esc_html__('首页固定半高背景图,必须添加(结束)工具', 'rizhuti-v2'),
    'fields'      => array(
        array(
            'id'    => 'light-image',
            'type'  => 'upload',
            'title' => esc_html__('明亮主题背景图', 'rizhuti-v2'),
            'default'     => get_template_directory_uri() . '/assets/img/top-bg.jpg',
        ),
        array(
            'id'    => 'dark-image',
            'type'  => 'upload',
            'title' => esc_html__('暗黑主题背景图', 'rizhuti-v2'),
            'default'     => get_template_directory_uri() . '/assets/img/top-bg.jpg',
        ),
    ),
));
if (!function_exists('pm_module_homeback_start')) {
    function pm_module_homeback_start($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示
        echo $args['before_widget'];
        // ob_start();

        echo "<div class='home-back'>";
        // wp_reset_postdata();
        // echo ob_get_clean();
        echo $args['after_widget'];
        echo "</div>";
    }
}


CSF::createWidget('pm_module_homeback_end', array(
    'title'       => esc_html__('PM: 首页半高背景(结束)', 'rizhuti-v2'),
    'classname'   => 'pm_module_homeback_end',
    'description' => esc_html__('首页固定半高背景图,必须添加(开始)工具', 'rizhuti-v2'),
    'fields'      => array()
));
if (!function_exists('pm_module_homeback_end')) {
    function pm_module_homeback_end($args, $instance)
    {
        if (!is_page_template_modular()) {
            return false;
        } //非模块页面不显示

        // ob_start(); 
?>

        <!-- </div> -->

<?php
        // wp_reset_postdata();
        // echo ob_get_clean();
        echo $args['after_widget'];
    }
}
