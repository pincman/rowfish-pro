<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;



/**
 * rizhuti-v2 functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package rizhuti-v2
 */

if (!defined('_RI_VERSION')) {
    // Replace the version number of the theme on each release.
    define('_RI_VERSION', '2.0.0');
}


//调试模式显示错误日志信息
if (defined('WP_DEBUG') && WP_DEBUG == true) {
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}



if (!function_exists('rizhuti_v2_setup')):

    function rizhuti_v2_setup() {

        // 第一启用主题时候插入订单表
        $the_theme_status = get_option('rizhuti_v2_theme_setup_status_1');
        if (empty($the_theme_status) && extension_loaded('swoole_loader') ) {
            $RiClass = new RiClass();
            $RiClass->setup_wppay_order();
            update_option('rizhuti_v2_theme_setup_status_1', '1');
        }

        //启用主题后删除主题包zip文件，防止被人恶意打包下载
        
        if ( file_exists( $theme_zip = dirname(dirname(__FILE__)) . '/rizhuti-v2.zip' ) ) {
            @unlink($theme_zip);
        }

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on rizhuti-v2, use a find and replace
         * to change 'rizhuti-v2' to the name of your theme in all the template files.
         */
        load_theme_textdomain('rizhuti-v2', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        // add link manager // 开启友情链接功能
        add_filter('pre_option_link_manager_enabled', '__return_true');
        
        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');
        // add_theme_support('post-formats', array('video','audio','gallery'));
        add_theme_support('post-formats', array('video', 'gallery'));
        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */

        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(
            array(
                'primary' => esc_html__('顶部主菜单', 'rizhuti-v2'),
            )
        );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
            )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

    }
endif;
add_action('after_setup_theme', 'rizhuti_v2_setup');




/**
 * 初始化主题必备的页面
 * @Author   Dadong2g
 * @DateTime 2021-02-19T10:10:22+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_setup_theme_page() {
    // 初始化页面
    $init_pages = array(
        'pages/page-modular.php' => array(esc_html__('模块化首页', 'rizhuti-v2'), 'home'),
        'pages/page-user.php'    => array(esc_html__('个人中心', 'rizhuti-v2'), 'user'),
        'pages/page-login.php'   => array(esc_html__('登录/注册', 'rizhuti-v2'), 'login'),
        'pages/page-series.php'   => array(esc_html__('专题集合', 'rizhuti-v2'), 'series'),
        'pages/page-tags.php'   => array(esc_html__('标签云', 'rizhuti-v2'), 'tags'),
        'pages/page-container.php'   => array(esc_html__('空白页面', 'rizhuti-v2'), 'container'),
        'pages/page-links.php'   => array(esc_html__('网址导航', 'rizhuti-v2'), 'links'),
    );
    foreach ($init_pages as $template => $item) {
        $page = array(
            'post_title'  => $item[0],
            'post_name'   => $item[1],
            'post_status' => 'publish',
            'post_type'   => 'page',
            'post_author' => 1,
        );
        $page_check = get_page_by_title($item[0]);
        if (!isset($page_check->ID)) {
            $page_id = wp_insert_post($page);
            update_post_meta($page_id, '_wp_page_template', $template);
        }
    }
}
add_action('admin_init', 'rizhuti_v2_setup_theme_page');


/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function rizhuti_v2_widgets_init() {


    register_sidebar(array(
        'name'          => esc_html__('首页模块化布局', 'rizhuti-v2'),
        'id'            => 'modules',
        'description'   => esc_html__('添加首页模块化布局', 'rizhuti-v2'),
        'before_widget' => '<div id="%1$s" class="section %2$s"><div class="container">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h3 class="section-title"><span>',
        'after_title'   => '</span></h3>',
    ));

    $sidebars = array(
        'sidebar' => esc_html__('文章页侧边栏','rizhuti-v2'),
        'cat_sidebar' => esc_html__('分类页侧边栏','rizhuti-v2'),
        'page_sidebar' => esc_html__('页面侧边栏','rizhuti-v2'),
        // 'question_sidebar' => esc_html__('问答侧边栏','rizhuti-v2'),
        'footer' => esc_html__('网站底部边栏','rizhuti-v2'),
    );

    // if ( !is_site_question() ) {
    //     unset($sidebars['question_sidebar']);
    // }

    foreach ($sidebars as $key => $value) {

        register_sidebar(
            array(
                'name'          => $value,
                'id'            => $key,
                'description'   => esc_html__('添加小工具到这里', 'rizhuti-v2'),
                'before_widget' => '<div id="%1$s" class="widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h5 class="widget-title">',
                'after_title'   => '</h5>',
            )
        );
    }



}
add_action('widgets_init', 'rizhuti_v2_widgets_init');

/**
 * 初始化本地时间
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:23:18+0800
 * @return   [type]                   [description]
 */
function set_local_timezone_string() {
    if (!is_admin()) {
        $timezone = get_option('timezone_string');
        date_default_timezone_set($timezone);
    }
}
// add_action('init','set_local_timezone_string');



// Require Composer's autoloading file
// if it's present in theme directory.
if (file_exists($composer = get_template_directory() . '/vendor/autoload.php')) {
    require_once $composer;
}


/**
 * 加载依赖文件库
 * @var array
 */
$rizhuti_v2_includes = array(
    '/inc/template-shop.php',
    '/inc/template-framework.php',
    '/inc/template-clean.php',
    '/inc/template-tags.php',
    '/inc/template-filter.php',
    '/inc/template-enqueue.php',
    '/inc/template-navwalker.php',
    '/inc/template-ajax.php',
    '/inc/template-admin.php',
    '/inc/class/pay.xh.class.php',
);



/**
 * 获取swoole_loader版本信息
 * @Author   Dadong2g
 * @DateTime 2021-05-02T19:17:51+0800
 * @return   [type]                   [description]
 */
function ri_getSysInfo() {
    $sysEnv = [];
    // Get content of phpinfo
    ob_start();
    phpinfo();
    $sysInfo = ob_get_contents();
    ob_end_clean();
    // Explode phpinfo content
    $sysInfoList = explode('</tr>', $sysInfo);
    foreach ($sysInfoList as $sysInfoItem) {
        if (preg_match('/thread safety/i', $sysInfoItem)) {
            $sysEnv['thread_safety'] = (preg_match('/(enabled|yes)/i', $sysInfoItem) != 0);
        }
        if (preg_match('/swoole_loader support/i', $sysInfoItem)) {
            $sysEnv['swoole_loader'] = (preg_match('/(enabled|yes)/i', $sysInfoItem) != 0);
        }
        if (preg_match('/swoole_loader version/i', $sysInfoItem)) {
            preg_match('/\d+.\d+.\d+/s', $sysInfoItem, $match);
            $sysEnv['swoole_loader_version'] = isset($match[0]) ? $match[0] : false;
        }
    }
    //var_dump($sysEnv);die();
    return $sysEnv;
}


/**
 * swoole_loader扩展安装帮助页面
 */
if (extension_loaded('swoole_loader')) {
    $php_v = substr(PHP_VERSION,0,3);
    $sysInfo = ri_getSysInfo();
    $swoole_v = ( isset($sysInfo['swoole_loader_version']) ) ? $sysInfo['swoole_loader_version'] : '2.2.5' ;
    
    //尝试兼容PHP8
    if ( $php_v == '8.0' ) {
        $rizhuti_v2_includes[] = '/inc/class/pay.class.8.php';
    } elseif ( $php_v == '7.4' ) {
        if ($swoole_v == '3.0') {
            $rizhuti_v2_includes[] = '/inc/class/pay.class.v3.php';
        }else{
            $rizhuti_v2_includes[] = '/inc/class/pay.class.php';
        }
    }else{
        wp_die('<small>rizhuti-v2需要php7.4版本支持，鉴于WordPress官方推荐PHP为最新7.4版本，其他低版本php暂不支持，php7.4性能极佳，使用rizhuti-v2性能更佳，如您的PHP环境没有7.4版本，请去FTP或者文件管理删除 \wp-content\themes\rizhuti-v2\ 主题目录即可恢复网站。</small>','php版本过低提示');exit;
    }

    
}else{
    wp_redirect(get_template_directory_uri().'/help/swoole-compiler-loader.php');die;
}




/**
 * Include files
 * @var [type]
 */
$rizhuti_v2_dir = get_template_directory();

foreach ($rizhuti_v2_includes as $file) {
    require_once $rizhuti_v2_dir . $file;
}



///////////////////////////// RITHEME.COM END ///////////////////////////