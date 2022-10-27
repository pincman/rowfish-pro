<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package rizhuti-v2
 */



new RizhutiSEO;

/**
 * 初始化主题自带SEO配置
 */
class RizhutiSEO{
    
    public $is_seo = false;
    public $site_seo = array();
    public $separator = '-';

    public function __construct() {
        $this->is_seo = _cao('is_rizhuti_v2_seo',false);
        $this->site_seo = _cao('site_seo');
        $this->separator = (isset($this->site_seo['separator'])) ? $this->site_seo['separator'] : '-' ;
        add_filter( 'excerpt_more', array($this, 'new_excerpt_more') );
        if ( $this->is_seo && is_array($this->site_seo) ) {
            add_filter( 'document_title_separator', array($this, 'custom_title_separator_to_line') );
            add_filter( 'pre_get_document_title', array($this, 'custom_post_document_title') );
            add_action('wp_head', array($this, 'custom_head') , 5);
        }
    }
    
    //修饰更多字符
    public function new_excerpt_more($more) {
        return '...';
    }


    //标题分隔符修改成 “-”
    public function custom_title_separator_to_line(){
        return $this->separator; //自定义标题分隔符
    }
    
    //自定义SEO标题 custom_title
    public function custom_post_document_title( $pre ){
        if ( is_singular() && $custom = get_post_meta( get_the_ID(), 'custom_title', true ) ) {
            $pre = $custom;
        }elseif (is_category() || (is_archive() && taxonomy_exists('series')) ) {
            # 分类页
            $termObj = get_queried_object();
            if ( $custom = get_term_meta($termObj->term_id, 'seo-title', true) ) {
                $pre = $custom;
            }
        }
        return $pre;
    }



    //自定义顶部钩子 添加描述 关键词 meta_og
    public function custom_head(){
        global $post;
        $key = '';
        $desc = '';
        $meta_og = array();
        $is_modular_home = is_page_template_modular(); //是否模块化首页
        if ( is_home() || $is_modular_home ) {
            # 首页
            $key = $this->site_seo['keywords'];
            $desc = $this->site_seo['description'];
        } elseif (is_singular() && !$is_modular_home ) {
            # 文章 页面
            if ( $meta_k = get_post_meta($post->ID, 'keywords', true) ) {
                $key = trim($meta_k);
            }else{
                if (get_the_tags($post->ID)) {
                    foreach (get_the_tags($post->ID) as $tag) {
                        $key .= $tag->name . ',';
                    }
                }
                foreach (get_the_category($post->ID) as $category) {
                    $key .= $category->cat_name . ',';
                }
            }
            
            if ( $meta_d = get_post_meta($post->ID, 'description', true) ) {
                $desc = trim($meta_d);
            }else{
                $excerpt = get_the_excerpt($post->ID);
                if (empty($excerpt)) {
                    $excerpt = $post->post_content;
                }

                $desc = wp_trim_words(strip_shortcodes($excerpt),120);
            }


            //Open Graph Protocol
            $meta_og = array(
                'title' => get_the_title($post->ID),
                'description' => $desc,
                'type' => 'article',
                'url' => esc_url(get_the_permalink($post->ID)),
                'site_name' => get_bloginfo('name'),
                'image' => _get_post_thumbnail_url($post->ID,'full'),
            );

        } elseif (is_category() || (is_archive() && taxonomy_exists('series')) ) {
            # 分类页标签
            $termObj = get_queried_object();
            if ( $meta_k = get_term_meta($termObj->term_id, 'seo-keywords', true) ) {
                $key = trim($meta_k);
            }
            if ( $meta_d = get_term_meta($termObj->term_id, 'seo-description', true) ) {
                $desc = trim($meta_d);
            }
        }

        if ( $site_favicon=_cao('site_favicon') ) {
           echo "<link href=\"$site_favicon\" rel=\"icon\">\n";
        }

        if (!empty($key)) {
            echo "<meta name=\"keywords\" content=\"$key\">\n";
        }
        if (!empty($desc)) {
            echo "<meta name=\"description\" content=\"$desc\">\n";
        }
        if (!empty($meta_og)) {
            foreach ($meta_og as $key => $value) {
                echo "<meta property=\"og:$key\" content=\"$value\">\n";
            }
        }

    }

}

/**
 * 全站统一 body css样式控制
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:45:03+0800
 * @param    [type]                   $classes [description]
 * @return   [type]                            [description]
 */
function rizhuti_v2_body_classes($classes) {
    // Adds a class of hfeed to non-singular pages.
    if ( !is_singular() ) {
        $classes[] = 'hfeed';
    }

    //是否全宽模式
    if ( apply_filters('is_site_wide_screen',false) || _cao('is_site_wide_screen',false) ) {
        $classes[] = 'wide-screen';
    }
    //是否夜间模式
    if ( !empty($_SESSION['site_dark_open']) || apply_filters('is_site_dark_open',false) ) {
        $classes[] = 'dark-open';
    }

    $classes[] = 'navbar-' . apply_filters('navbar_style',_cao('navbar_style', 'regular'));

    if ( !_cao('navbar_disable_search', true) ) {
        $classes[] = 'no-search';
    }

    if ( rizhuti_v2_show_hero() ) {
        $classes[] = 'with-hero';
        $classes[] = 'hero-' . rizhuti_v2_compare_options(_cao('hero_single_style', 'none'), get_post_meta(get_the_ID(), 'hero_single_style', 1));
            $classes[] = get_post_format() ? 'hero-' . get_post_format() : 'hero-image';
    }
    $classes[] = 'pagination-' . apply_filters('site_pagination',_cao('site_pagination', 'numeric'));

    if ( get_previous_posts_link() ) {
        $classes[] = 'paged-previous';
    }

    if ( get_next_posts_link() ) {
        $classes[] = 'paged-next';
    }

    $classes[] = 'no-off-canvas';
    $classes[] = 'sidebar-' . rizhuti_v2_sidebar();

    if ( is_page_template_modular() ) {
        $classes[] = apply_filters('site_modular_title','modular-title-3');
    }

    return $classes;
}
add_filter('body_class', 'rizhuti_v2_body_classes');



// 禁用古腾堡小工具
if (true) {
    // Disables the block editor from managing widgets in the Gutenberg plugin.
    add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
    // Disables the block editor from managing widgets.
    add_filter( 'use_widgets_block_editor', '__return_false' );
}



/**
 * 启用session会话缓存
 * @Author   Dadong2g
 * @DateTime 2021-01-31T13:15:09+0800
 * @return   [type]                   [description]
 */
function ri_session_start() {
    //检测系统是否支持启用session
    if (!session_id()) {
        session_start();
    }
}
add_action('ri_session_start','ri_session_start');

function ri_session_starts() {
    if (wp_doing_ajax() && !session_id()) {
        session_start();
    }
}
add_action('init','ri_session_starts');


/**
 * 管理员后台关闭s
 */
add_action( 'admin_init', function () {
    // session_write_close();
} );




/**
 * 缩略图大小控制同步wordpress自带设置
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:44:46+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_setup_post_thumbnail_size() {
    $thumbnail_size = apply_filters('post_thumbnail_size',_cao('post_thumbnail_size', 'numeric'));
    if (empty($thumbnail_size)) {
        $thumbnail_size['width']  = 300;
        $thumbnail_size['height'] = 200;
    }
    update_option('thumbnail_size_w', $thumbnail_size['width']);
    update_option('thumbnail_size_h', $thumbnail_size['height']);
    update_option('thumbnail_crop', _cao('post_thumbnail_crop', '1'));

    $medium_size = _cao('post_medium_size');
    if (empty($medium_size)) {
        $medium_size['width']  = 0;
        $medium_size['height'] = 0;
    }
    update_option('medium_size_w', $medium_size['width']);
    update_option('medium_size_h', $medium_size['height']);

    $large_size = _cao('post_large_size');
    if (empty($large_size)) {
        $large_size['width']  = 0;
        $large_size['height'] = 0;
    }
    update_option('large_size_w', $large_size['width']);
    update_option('large_size_h', $large_size['height']);
    
}
add_action('admin_init', 'rizhuti_v2_setup_post_thumbnail_size');


/**
 * 内页标题优化
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:44:32+0800
 * @param    [type]                   $title [description]
 * @return   [type]                          [description]
 */
function riplus_theme_archive_title($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    }
    return $title;
}
add_filter('get_the_archive_title', 'riplus_theme_archive_title');


/**
 * 编辑器添加“下一页”按钮
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:44:13+0800
 * @param    [type]                   $mce_buttons [description]
 * @return   [type]                                [description]
 */
function wp_add_next_page_button($mce_buttons) {
    $pos = array_search('wp_more', $mce_buttons, true);
    if ($pos !== false) {
        $tmp_buttons   = array_slice($mce_buttons, 0, $pos + 1);
        $tmp_buttons[] = 'wp_page';
        $mce_buttons   = array_merge($tmp_buttons, array_slice($mce_buttons, $pos + 1));
    }
    return $mce_buttons;
}
add_filter('mce_buttons', 'wp_add_next_page_button');



/**
 * 伪静态路由
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:43:57+0800
 * @param    [type]                   $wp_rewrite [description]
 * @return   [type]                               [description]
 */
function riplus_oauth_page_rewrite_rules($wp_rewrite) {
    // 如果当前是自定义固定链接则设置
    if ( get_option('permalink_structure') ) {
        $new_rules['^oauth/([A-Za-z]+)$']          = 'index.php?oauth=$matches[1]';
        $new_rules['^oauth/([A-Za-z]+)/callback$'] = 'index.php?oauth=$matches[1]&oauth_callback=1';
        $new_rules['^goto$'] = 'index.php?goto=1';
        $new_rules['^user/([^/]*)/?'] = 'index.php?page_id='.get_page_id('user').'&action=$matches[1]';
        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    }
}
add_action('generate_rewrite_rules', 'riplus_oauth_page_rewrite_rules');

/**
 * 伪静态路由查询字段
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:12:04+0800
 * @param    [type]                   $public_query_vars [description]
 * @return   [type]                                      [description]
 */
function riplus_add_oauth_page_query_vars($public_query_vars) {
    if (!is_admin()) {
        $public_query_vars[] = 'oauth'; // 添加参数白名单oauth，代表是各种OAuth登录处理页
        $public_query_vars[] = 'oauth_callback'; // OAuth登录最后一步，整合WP账户，自定义用户名
        $public_query_vars[] = 'goto'; //下载页跳转
        $public_query_vars[] = 'action'; //user_page action
    }
    return $public_query_vars;
}
add_filter('query_vars', 'riplus_add_oauth_page_query_vars');

/**
 * 伪静态路由页面模板
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:12:20+0800
 * @return   [type]                   [description]
 */
function riplus_oauth_page_template() {
    $sns = strtolower(get_query_var('oauth')); //转换为小写
    $sns_callback = get_query_var('oauth_callback');
    if ($sns && in_array($sns, array('qq', 'weixin','mpweixin', 'weibo'))) {
        if (is_close_site_shop()) {exit;}
        $template = $sns_callback ? TEMPLATEPATH . '/inc/sns/' . $sns . '/callback.php' : TEMPLATEPATH . '/inc/sns/' . $sns . '/login.php';
        load_template($template);exit;
    }

    $goto = strtolower(get_query_var('goto')); //转换为小写
    if ($goto==1) {
        $template = TEMPLATEPATH . '/inc/goto.php';
        load_template($template);exit;
    }


}

add_action('template_redirect', 'riplus_oauth_page_template', 5);


/**
 * 捕获是否微信内访问条件满足获取openid
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:43:45+0800
 * @return   [type]                   [description]
 */
function riplus_is_template_redirect() {

    //检测系统是否支持启用session
    if (!session_id()) {
        session_start();
    }

    // 推荐aff
    if (!empty($_GET['aff'])) {
        $_SESSION['current_aff_uid'] = absint($_GET['aff']);
    }
    
    //免登录用户购买 根据SESSION订单号设置Cookie
    if (!is_user_logged_in() && _cao('is_rizhuti_v2_nologin_pay')) {
        $orderNum = (!empty($_SESSION['current_pay_ordernum'])) ? $_SESSION['current_pay_ordernum'] : 0 ;
        
        if (!empty($orderNum)) {
            $RiClass = new RiClass;
            $order = $RiClass->get_order_info($orderNum);
            // 有订单并且已经支付
            if (!empty($order) && $order->status == 1) {
                $RiClass->AddPayPostCookie($order->post_id, $order->order_num);
                unset($_SESSION['current_pay_ordernum']);
                if( $order->post_id>0 && $order->order_type==1 ){
                    wp_redirect( get_the_permalink( $order->post_id ) );exit;
                }else{
                    wp_redirect(get_user_page_url());exit;
                }
            }
        }
    }
    
    //微信内是否跳转获取openid 开启微信jsapi才有效
    if (wp_is_mobile()
        && !is_close_site_shop()
        && is_weixin_visit()
        && (is_single() || is_page_template('pages/page-user.php'))
        && (!defined('DOING_AJAX') || !DOING_AJAX)
        && empty($_SESSION['current_weixin_openid'])
    ) {
        $opt = _cao('weixinpay');
        if (!empty($opt) && $opt['appid'] && $opt['is_jsapi']) {
            $current_url = urlencode(curPageURL());
            $wxurl       = home_url('/oauth/weixin?get_openid=1&rurl=' . $current_url);
            wp_safe_redirect($wxurl);exit;
        }
    }
    
}
add_action('template_redirect','riplus_is_template_redirect', 5);

/**
 * 登录地址
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:43:38+0800
 * @param    [type]                   $url      [description]
 * @param    [type]                   $redirect [description]
 * @return   [type]                             [description]
 */
 function riplus_login_url($url, $redirect) {
    $url = home_url('login?mod=login');
    if (!empty($redirect)) {
        $url = add_query_arg('redirect_to', urlencode($redirect), $url);
    }
    return esc_url($url);
}


/**
 * 注册地址
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:43:29+0800
 * @param    [type]                   $url [description]
 * @return   [type]                        [description]
 */
 function riplus_register_url($url) {
    $url = home_url('login?mod=register');
    return esc_url($url);
}


/**
 * 找回密码
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:43:23+0800
 * @param    [type]                   $url      [description]
 * @param    [type]                   $redirect [description]
 * @return   [type]                             [description]
 */
function riplus_lostpassword_url($url, $redirect) {
    $url = home_url('login?mod=lostpassword');
    return esc_url($url);
}
add_filter('login_url', 'riplus_login_url', 20, 2);
add_filter('register_url', 'riplus_register_url', 20);
add_filter('lostpassword_url', 'riplus_lostpassword_url', 20, 2);

add_filter('get_avatar_url', '_get_avatar_url', 10, 3);
add_filter('pre_get_avatar', '_pre_get_avatar', 10, 3);


/**
 * 替换默认头像url
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:43:11+0800
 * @param    [type]                   $url         [description]
 * @param    [type]                   $id_or_email [description]
 * @param    [type]                   $args        [description]
 * @return   [type]                                [description]
 */
function _get_avatar_url($url, $id_or_email, $args) {
    $user_id = 0;
    if (is_numeric($id_or_email)) {
        $user_id = absint($id_or_email);
    } elseif (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if (isset($user->ID) && $user->ID) {
            $user_id = $user->ID;
        }
    } elseif ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Post) {
        $user_id = $id_or_email->post_author;
    } elseif ($id_or_email instanceof WP_Comment) {
        $user_id = $id_or_email->user_id;
        if (!$user_id) {
            $user = get_user_by('email', $id_or_email->comment_author_email);
            if (isset($user->ID) && $user->ID) {
                $user_id = $user->ID;
            }

        }
    }


    $avatar_type = get_user_meta($user_id, 'user_avatar_type', 1);

    if (empty($avatar_type)) {
        $avatar_url = _the_theme_avatar();
    }elseif ($avatar_type=='custom') {
        $custom = get_user_meta($user_id,$avatar_type.'_avatar', 1);
        $custom = (empty($custom)) ? _the_theme_avatar() : $custom ;
        $avatar_url = set_url_scheme($custom);
    }else{
        $avatar_url = set_url_scheme(get_user_meta($user_id, 'open_'.$avatar_type.'_avatar', 1));
    }
    
    $url = preg_replace('/^(http|https):/i', '', $avatar_url);

    return $url;

}

/**
 * 替换默认头像
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:42:55+0800
 * @param    [type]                   $avatar      [description]
 * @param    [type]                   $id_or_email [description]
 * @param    [type]                   $args        [description]
 * @return   [type]                                [description]
 */
function _pre_get_avatar($avatar, $id_or_email, $args) {

    $url = _get_avatar_url($avatar, $id_or_email, $args);

    $class = array('lazyload', 'avatar', 'avatar-' . (int) $args['size'], 'photo');
    if ($args['class']) {
        if (is_array($args['class'])) {
            $class = array_merge($class, $args['class']);
        } else {
            $class[] = $args['class'];
        }
    }
    if (is_admin()) {
        $lazy = '';
    } else {
        $lazy = 'data-';
    }
    $avatar = sprintf(
        "<img alt='%s' {$lazy}src='%s' class='%s' height='%d' width='%d' %s/>",
        esc_attr($args['alt']),
        esc_url($url),
        esc_attr(join(' ', $class)),
        (int) $args['height'],
        (int) $args['width'],
        $args['extra_attr']
    );
    return $avatar;
}


/**
 * 使用昵称替换链接中的用户名
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:42:36+0800
 * @param    [type]                   $link            [description]
 * @param    [type]                   $author_id       [description]
 * @param    [type]                   $author_nicename [description]
 * @return   [type]                                    [description]
 */
function rizhuti_v2_author_link( $link, $author_id, $author_nicename ){
    $author_nickname = get_user_meta( $author_id, 'nickname', true );
    if ( $author_nickname ) {
        $link = str_replace( $author_nicename, $author_nickname, $link );
    }
    return $link;
}
add_filter('author_link','rizhuti_v2_author_link', 10, 3 );

/**
 * 使用昵称替换用户名，通过用户ID进行查询
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:42:31+0800
 * @param    [type]                   $query_vars [description]
 * @return   [type]                               [description]
 */
function rizhuti_v2_author_request( $query_vars ){
    if ( array_key_exists( 'author_name', $query_vars ) ) {
        global $wpdb;
        $author_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='nickname' AND meta_value = %s", urldecode($query_vars['author_name']) ) );
        if ( $author_id ) {
            $query_vars['author'] = $author_id;
            unset( $query_vars['author_name'] );    
        }
    }
    return $query_vars;
}
add_filter('request','rizhuti_v2_author_request');



/**
 * 用户登录时间和登录IP
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:42:23+0800
 * @param    [type]                   $login [description]
 * @return   [type]                          [description]
 */
function riplus_insert_last_login($user_login, $user) {
    //最近登录时间
    update_user_meta($user->ID, 'last_login_time', current_time('mysql'));
    //最近登录IP
    update_user_meta($user->ID, 'last_login_ip',get_client_ip());
}
add_action('wp_login','riplus_insert_last_login', 10, 2);


/**
 * 用户注册时初始化
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:42:15+0800
 * @param    [type]                   $user_id [description]
 * @return   [type]                            [description]
 */
function riplus_credit_by_user_register($user_id) {
    //链接推广人与新注册用户(注册人meta)
    $ref_from = (isset($_SESSION['current_aff_uid'])) ? absint($_SESSION['current_aff_uid']) : 0;
    //更新推荐人ID
    update_user_meta($user_id, 'aff_from_id', absint($ref_from));
    //注册IP
    update_user_meta($user_id, 'register_ip',get_client_ip());
}
add_action('user_register','riplus_credit_by_user_register');





/**
 * 筛选条件
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:42:09+0800
 * @param    [type]                   $query [description]
 * @return   [type]                          [description]
 */
function rizhuti_v2_archive_filter($query) {
    //is_search判断搜索页面  !is_admin排除后台  $query->is_main_query()只影响主循环
    if (!$query->is_admin && is_archive() && $query->is_main_query()) {
        // 排序：
        $order      = !empty($_GET['order']) ? $_GET['order'] : null;
        $price_type = !empty($_GET['price_type']) ? (int) $_GET['price_type'] : null;


        if ($order=='views') {
            $query->set( 'meta_key', '_views' );
            $query->set( 'orderby', 'meta_value_num');
            $query->set( 'order', 'DESC' );
        }elseif ($order=='favnum') {
            $query->set( 'meta_key', '_favnum' );
            $query->set( 'orderby', 'meta_value_num');
            $query->set( 'order', 'DESC' );
        }elseif (!empty($order)) {
            $query->set('orderby', $order);
        }

        
        
        // 筛选
        if ($price_type) {
            $custom_meta_query = [];
            $_meta  = [];
            $_price = 'wppay_price';
            $_auth  = 'wppay_vip_auth';
            $_type  = 'wppay_type';
           
            switch ($price_type) {
            case 1:
                $_meta[] = [
                    'relation' => 'OR',
                    ['key' => $_type, 'compare' => '=', 'value' => '4'],
                    ['key' => $_price, 'compare' => '=', 'value' => '0']
                ];
                break;
            case 2:
                $_meta[] = [
                    'relation' => 'AND',
                    ['key' => $_type, 'compare' => '>', 'value' => '0'],
                    ['key' => $_price, 'compare' => '>', 'value' => '0'],
                    ['key' => $_type, 'compare' => '!=', 'value' => '4'],
                    // ['key' => $_auth, 'compare' => '<=', 'value' => '0'],
                ];

                break;
            case 3:
                $_meta[] = [
                    'relation' => 'AND',
                    ['key' => $_type, 'compare' => '>', 'value' => '0'],
                    ['key' => $_type, 'compare' => '!=', 'value' => '4'],
                    ['key' => $_auth, 'compare' => '>', 'value' => '0'],
                    ['key' => $_price, 'compare' => '=', 'value' => '0'],
                ];
                break;
            case 31:
                $_meta[] = [
                    'relation' => 'AND',
                    ['key' => $_type, 'compare' => '>', 'value' => '0'],
                    ['key' => $_type, 'compare' => '!=', 'value' => '4'],
                    ['key' => $_auth, 'compare' => '=', 'value' => '1'],
                    ['key' => $_price, 'compare' => '>', 'value' => '0'],
                ];
                break;
            case 365:
                $_meta[] = [
                    'relation' => 'AND',
                    ['key' => $_type, 'compare' => '>', 'value' => '0'],
                    ['key' => $_type, 'compare' => '!=', 'value' => '4'],
                    ['key' => $_auth, 'compare' => '!=', 'value' => '0'],
                    ['key' => $_auth, 'compare' => '<=', 'value' => '2'],
                    ['key' => $_price, 'compare' => '>', 'value' => '0'],
                ];
                break;
            case 3600:
                $_meta[] = [
                    'relation' => 'AND',
                    ['key' => $_type, 'compare' => '>', 'value' => '0'],
                    ['key' => $_type, 'compare' => '!=', 'value' => '4'],
                    ['key' => $_auth, 'compare' => '!=', 'value' => '0'],
                    ['key' => $_auth, 'compare' => '<=', 'value' => '3'],
                    ['key' => $_price, 'compare' => '>', 'value' => '0'],
                ];
                break;
            default:
                break;
            }
            $custom_meta_query[] = $_meta;
            $query->set('meta_query', $custom_meta_query);
        }

    }

    // //搜索优化细分安全优化
    if ( !$query->is_admin && $query->is_main_query() && is_search() ) {
        $query->set('post_type', 'post');
    }


    //仅会员可见的专题 series 彩蛋
    if (apply_filters('is_site_series_cap', false) && !$query->is_admin) {
        $cap_terms = get_terms('series', array(
            'hide_empty' => true,
            'parent'     => 0,
            'child_of'   => 0,
            'meta_query' => array(array('key' => 'series_cap','compare' => '!=', 'value' => 'no' ))
            )
        );

        $terms_ids = array();
        foreach ($cap_terms as $key => $value) {
            $terms_ids[] = $value->term_id;
        }

        $tax_query = array('relation' => 'OR',array(
            'taxonomy' => 'series',
            'field'    => 'term_id',
            'terms' => $terms_ids,
            'operator' => 'NOT IN'
        ));
        $query->set( 'tax_query', $tax_query );
    }
    return $query;
}

add_filter('pre_get_posts','rizhuti_v2_archive_filter',99);
add_filter( 'use_default_gallery_style', '__return_false' );



//搜索标题优化
function site_search_by_title_only( $search, $wp_query )
{
    global $wpdb;
    
    if ( empty( $search ) || empty(_cao('is_search_title_only',false)) ){
        return $search; // skip processing - no search term in query
    }

    $q = $wp_query->query_vars;    
    $n = ! empty( $q['exact'] ) ? '' : '%';
 
    $search = $searchand = '';
 
    foreach ( (array) $q['search_terms'] as $term ) {
        $term = esc_sql( $wpdb->esc_like( $term ) );
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $searchand = ' AND ';
    }
 
    if ( ! empty( $search ) ) {
        $search = " AND ({$search}) ";
        if ( ! is_user_logged_in() ){
            $search .= " AND ($wpdb->posts.post_password = '') ";
        }
    }
 
    return $search;
}
add_filter( 'posts_search', 'site_search_by_title_only', 500, 2 );



/**
 * 上传文件MD5重命名，新增时间戳防止重复
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:41:55+0800
 * @param    [type]                   $filename [description]
 * @return   [type]                             [description]
 */
function _new_filename($filename){
    if (!_cao('md5_file_udpate',true)) return $filename;
    $info = pathinfo($filename);
    $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
    $name = basename($filename, $ext);
    return time().'-'.substr(md5($name), 0, 15) . $ext;
}
add_filter('sanitize_file_name', '_new_filename', 10);





/**
 * 在所有文章底部版权声明
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:41:43+0800
 * @param    [type]                   $content [description]
 */
function add_after_post_content_note($content) {
    if (!is_feed() && !is_home() && is_singular() && is_main_query() && !empty(_cao('single_copyright'))) {
        $content .= '<div class="post-note alert alert-info mt-2" role="alert">' . _cao('single_copyright') . '</div>';
    }
    return $content;
}
// add_filter('the_content', 'add_after_post_content_note', 99);



/**
 * 添加文章专题分类大法
 * 字段为【 series 】
 * @Author   Dadong2g
 * @DateTime 2021-01-26T10:03:43+0800
 */
function site_add_post_series_taxonomy(){
    $labels = array(
        'name'                       => '专题',
        'singular_name'              => 'series',
        'search_items'               => '搜索',
        'popular_items'              => '热门',
        'all_items'                  => '所有',
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => '编辑',
        'update_item'                => '更新',
        'add_new_item'               => '添加',
        'new_item_name'              => '专题名称',
        'separate_items_with_commas' => '按逗号分开',
        'add_or_remove_items'        => '添加或删除',
        'choose_from_most_used'      => '从经常使用的类型中选择',
        'menu_name'                  => '专题',
    );
    register_taxonomy(
        'series',
        array('post'),
        array(
            'hierarchical' => true,
            'labels'       => $labels,
            'show_ui'      => true,
            'query_var'    => true,
            'rewrite'      => array('slug' => 'series'),
            'show_in_rest' => true,
        )
    );

}
add_action('init','site_add_post_series_taxonomy');


/**
 * 评论过滤
 * @Author   Dadong2g
 * @DateTime 2021-04-05T09:16:26+0800
 * @param    [type]                   $commentdata [description]
 * @return   [type]                                [description]
 */
function rizhuti_v2_comment_preprocess($commentdata) {

    if ( get_post_type( $commentdata['comment_post_ID'] ) == 'question' ) {
        $commentdata['comment_type'] = 'question';
    }

    return $commentdata;

}

add_filter('preprocess_comment', 'rizhuti_v2_comment_preprocess');

