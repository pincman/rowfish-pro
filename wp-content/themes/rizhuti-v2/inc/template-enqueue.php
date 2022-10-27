<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * 网站静态脚本样式统一调用
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:05:29+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_scripts() {
    // Get the theme data.
    $the_theme     = wp_get_theme();
    $theme_dir     = get_template_directory_uri() . '/assets';
    $css_version   = $the_theme->get('Version');
    if (!is_admin()) {

        // 去掉wp自带jquery 加载新版本jquery
        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', $theme_dir . '/js/jquery.min.js', array(), '3.5.1',false);
        
        //bootstrap  bootstrap-4.6.0
        wp_enqueue_style('bootstrap', $theme_dir . '/bootstrap/css/bootstrap.min.css', array(), '4.6.0');
        wp_enqueue_script('bootstrap', $theme_dir . '/bootstrap/js/bootstrap.min.js', array('jquery','popper'), '4.6.0',true);


        // Font awesome 4 and 5 loader jsdelivr加速
        if (apply_filters('csf_fa4', false)) {
            wp_enqueue_style('csf-fa', 'https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), '4.7.0');
        } else {
            wp_enqueue_style('csf-fa5', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/css/all.min.css', array(), '5.14.0');
            wp_enqueue_style('csf-fa5-v4-shims', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.1/css/v4-shims.min.css', array(), '5.14.0');
        }


        //plugins
        wp_enqueue_style('plugins', $theme_dir . '/css/plugins.css', array('bootstrap'), '1.0.0');
        wp_enqueue_style('app', $theme_dir . '/css/app.css', array('bootstrap','plugins'), $css_version);
        wp_enqueue_style('dark', $theme_dir . '/css/dark.css', array('bootstrap','plugins','app'), $css_version);

        //文章页面js
        
        if ( is_singular() && !is_page_template_modular() ) {
            global $post;
            if ( 'video' == get_post_format($post->ID) ) {
                // DPlayer 
                wp_enqueue_script('hls', $theme_dir . '/DPlayer/hls.js', array('jquery','app'),'', true);
                wp_enqueue_script('dplayer', $theme_dir . '/DPlayer/DPlayer.min.js', array('hls'),'', true);
            }

            if ( has_shortcode( $post->post_content, 'gallery' ) || '6' == get_post_meta($post->ID, 'wppay_type', 1) ) {
                //lightGallery
                wp_enqueue_style('lightGallery', $theme_dir . '/lightGallery/css/lightgallery.min.css', array('app'), '1.10.0');
                wp_enqueue_script('mousewheel', $theme_dir . '/js/jquery.mousewheel.min.js', array('app'), '3.1.13' ,true);
                wp_enqueue_script('lightGallery', $theme_dir . '/lightGallery/js/lightgallery-all.min.js', array('app'), '1.10.0' ,true);

                //justifiedGallery
                wp_enqueue_style('justifiedgallery', $theme_dir . '/justifiedGallery/justifiedGallery.min.css', array('lightGallery'), $css_version);
                wp_enqueue_script('justifiedgallery', $theme_dir . '/justifiedGallery/jquery.justifiedGallery.min.js', array('jquery','lightGallery'), $css_version,true);
            }

            if (_cao('is_single_share','1')) {
                wp_enqueue_script('html2canvas',$theme_dir . '/js/html2canvas.min.js', array(),'1.0.0',true);
            }
        }

        // jarallax
        if ( is_page_template_modular() || is_singular() ) {
            wp_enqueue_script('jarallax', $theme_dir . '/jarallax/jarallax.min.js', array('jquery'), '1.12.5' ,true);
            wp_enqueue_script('jarallax-video', $theme_dir . '/jarallax/jarallax-video.min.js', array('jarallax'), '1.0.1' ,true);
        }

        //plugins
        wp_enqueue_script('plugins', $theme_dir . '/js/plugins.js', array('jquery'), $css_version,true);
        //popper.min
        wp_enqueue_script('popper', $theme_dir . '/js/popper.min.js', array('jquery'), $css_version,true);
        
        //site appjs
        wp_enqueue_script('app', $theme_dir . '/js/app.js', array('jquery','plugins'), $css_version,true);

        //TCaptcha 007
        wp_register_script('captcha', 'https://ssl.captcha.qq.com/TCaptcha.js', array('jquery','app'),'', true);

        //clipboard
        wp_register_script('clipboard',  $theme_dir . '/js/clipboard.min.js', array('jquery'),'2.0.6', true);

        //question
        wp_register_script('question', $theme_dir . '/js/question.js', array('jquery','app'),'', true);
        

    }

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    $script_params = apply_filters('rizhuti_v2_script_params',array(
        'home_url'         => esc_url(home_url()),
        'admin_url'        => esc_url(admin_url('admin-ajax.php')),
        'comment_list_order' => get_option('comment_order'),
        'infinite_load'    => apply_filters('rizhuti_v2_infinite_button_load', esc_html__('加载更多', 'rizhuti-v2')),
        'infinite_loading' => apply_filters('rizhuti_v2_infinite_button_load', esc_html__('加载中...', 'rizhuti-v2')),
    ));
   
    if (!is_close_site_shop()) {
        $script_params['pay_type_html'] = _riplus_get_pay_type_html();
    }
    if (is_singular()) {
        global $post;
        $script_params['singular_id'] = $post->ID;
    }
    
    wp_localize_script('app', 'rizhutiv2', $script_params);

}
add_action('wp_enqueue_scripts', 'rizhuti_v2_scripts');


/**
 * 管理页面CSS
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:10:46+0800
 * @return   [type]                   [description]
 */
function caoAdminScripts() {
    if (isset($_GET['page']) && strpos($_GET['page'],'rizhuti_v2') !== false) {
        wp_enqueue_style('rizhuti-v2-admin', get_template_directory_uri() . '/assets' . '/css/admin.css', array(),'1.0');
    }
}
add_action('admin_enqueue_scripts', 'caoAdminScripts');


///////////////////////////// RITHEME.COM END ///////////////////////////