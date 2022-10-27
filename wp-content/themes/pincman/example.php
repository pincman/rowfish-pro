<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/**
 * 会员配置初始化-过滤器
 * @Author   Dadong2g
 * @DateTime 2021-01-22T10:18:45+0800
 * @param    [type]                   $param [description]
 * @return   [type]                        [description]
 */
function new_get_ri_vip_options($param)
{
    return $param;
}
add_filter('get_ri_vip_options', 'new_get_ri_vip_options');


/**
 * 示例2：给网站在添加一个模块化首页1
 * @Author   Dadong2g
 * @DateTime 2021-01-17T00:15:45+0800
 * @param    [type]                   $modular_arr [description]
 * @return   [type]                                [description]
 */
function new_page_template_modular($modular_arr)
{
    //在pages中新建一个模块化首页模板
    $modular_arr[] = 'pages/page-modular1.php';
    return $modular_arr;
}
add_filter('page_template_modular_php', 'new_page_template_modular');



/**
 * 示例2：将模块化页面2注册到小工具 命名 modules1
 * @Author   Dadong2g
 * @DateTime 2021-01-17T00:17:59+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_widgets_modular1()
{
    register_sidebar(array(
        'name'          => esc_html__('首页模块化布局-2', 'rizhuti-v2'),
        'id'            => 'modules1',
        'description'   => esc_html__('添加首页模块化布局-2', 'rizhuti-v2'),
        'before_widget' => '<div id="%1$s" class="section %2$s"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h3 class="section-title"><span>',
        'after_title'   => '</span></h3>',
    ));
}
add_action('widgets_init', 'rizhuti_v2_widgets_modular1');

/**
 * 示例7： 添加一个自己的子主题设置选项
 * 设置选项框架字段文档：http://codestarframework.com/documentation/
 */

/**
 * 获取设置的字段值 Custom function for get an option
 */
if (!function_exists('_cao')) {
    function _cao($option = '', $default = null)
    {
        $options_meta = '_rizhutiv2_options';
        $options      = get_option($options_meta);
        return (isset($options[$option])) ? $options[$option] : $default;
    }
}


if (!class_exists('CSF')) :
    // 引入父主题的设置框架class
    require_once get_template_directory() . '/inc/codestar-framework/codestar-framework.php';

    //主题设置储存的字段(需个父主题保持一致)
    $opt_prefix = '_rizhutiv2_options';

    //创建一个设置页面
    CSF::createSection($opt_prefix, array(
        'title'  => '子主题功能',
        'icon'   => 'fa fa-wordpress',
        'fields' => array(

            array(
                'type'    => 'notice',
                'style'   => 'success',
                'content' => '这是一个子主题自定义设置选项，方便开发者开发，这里举例简单的功能和设置，大佬请直接查看子主题源代码开日',
            ),


            array(
                'id'      => 'is_weixin_close_site_shop',
                'type'    => 'switcher',
                'title'   => esc_html__('在微信内访网站自动关闭商城', 'rizhuti-v2'),
                'desc'    => esc_html__('微信官方规则限制所有公众号小程序等不允许存在虚拟交易付款产品，此功能方便的一B，既然你不让出现，我就自动判断，在微信内访问的时候作为博客展示', 'rizhuti-v2'),
                'default' => false,
            ),

            array(
                'id'       => 'omnisearch_ads',
                'type'     => 'textarea',
                'title'    => esc_html__('搜索弹出框页面广告', 'rizhuti-v2'),
                'sanitize' => false,
                'default'  => '<a href="https://ritheme.com/" target="_blank"><img src="' . get_template_directory_uri() . '/assets/img/ad2.jpg"></a>',
            ),

            array(
                'id'       => 'entry_diy_ads1',
                'type'     => 'textarea',
                'title'    => esc_html__('文章内容顶部广告', 'rizhuti-v2'),
                'sanitize' => false,
                'default'  => '<a href="https://ritheme.com/" target="_blank"><img src="' . get_template_directory_uri() . '/assets/img/ad2.jpg" style=" width: 100%; margin: 20px 0; text-align: center; "></a>',
            ),
            array(
                'id'       => 'entry_diy_ads2',
                'type'     => 'textarea',
                'title'    => esc_html__('文章内容底部广告', 'rizhuti-v2'),
                'sanitize' => false,
                'default'  => '<a href="https://ritheme.com/" target="_blank"><img src="' . get_template_directory_uri() . '/assets/img/ad2.jpg" style=" width: 100%; margin: 20px 0; text-align: center; "></a>',
            ),

        ),
    ));

    //添加一个首页模块或者侧边栏模块设置
    CSF::createWidget('rizhuti_v2_widget_alert', array(
        'title'       => 'Ri-子主题模块-网站公告',
        'classname'   => 'rizhuti-v2-widget-alert',
        'description' => 'Ri主题的小工具',
        'fields'      => array(
            array(
                'id'      => 'title',
                'type'    => 'text',
                'title'   => '标题',
                'default' => '网站公告',
            ),
            array(
                'id'      => 'data',
                'type'    => 'textarea',
                'sanitize'   => false,
                'title'   => '自定义提示内容',
                'default' => '<strong>hello</strong> 这是一个可以关闭的提示框，使用子主题添加',
            ),

        ),
    ));

endif;

/**
 * 添加一个首页模块或者侧边栏模块调用
 * 界面UI可以使用 bootstrapV4 地址：https://v4.bootcss.com/docs/components/alerts/
 * @Author   Dadong2g
 * @DateTime 2021-01-18T22:04:42+0800
 * @param    [type]                   $args     [description]
 * @param    [type]                   $instance [description]
 * @return   [type]                             [description]
 */
function rizhuti_v2_widget_alert($args, $instance)
{
    echo $args['before_widget'];
    if (!empty($instance['title'])) {
        // echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
    }
    ob_start(); ?>

    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <?php echo $instance['data']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

<?php echo ob_get_clean();
    echo $args['after_widget'];
}


/**
 * 如何获取设置的字段值
 * 通过  _cao('is_post_vip_icon',true); 可以输出这个设置的值
 * 说明 _cao('字段ID','默认值');
 */


/**
 * 搜索弹出框加入一个广告
 * @Author   Dadong2g
 * @DateTime 2021-01-21T10:35:26+0800
 * @return   [type]                   [description]
 */
function omnisearch_ads()
{
    echo _cao('omnisearch_ads');
}
