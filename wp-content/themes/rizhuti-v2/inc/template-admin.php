<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * 关闭wordpress的后台地址 wp-admin 和 /wp-login.php/登录地址
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:16:41+0800
 * @return   [type]                   [description]
 */
function site_login_protection() {
    $is  = apply_filters('is_site_login_protection', true);
    $key = apply_filters('site_login_protection_key', 'wordpress');
    if ($is || !isset($_GET['admin']) || $_GET['admin'] != $key) {
        wp_redirect(wp_login_url());exit;
    }
}
// add_action('login_enqueue_scripts','site_login_protection');

/**
 * 后台菜单
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:16:58+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_add_admin_menu() {
    if (is_close_site_shop() || !current_user_can('manage_options')) {
        return;
    }
    $index_page = 'rizhuti_v2_shop_index_page';
    add_menu_page(esc_html__('商城管理', 'rizhuti-v2'), '商城管理', 'administrator', $index_page, $index_page, 'dashicons-cart', 100);
    $menu = [
        ['menu' => 'rizhuti_v2_shop_index_page', 'page' => 'shop_index', 'name' => esc_html__('商城总览', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_pay_order_page', 'page' => 'pay_order', 'name' => esc_html__('资源订单', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_vip_order_page', 'page' => 'pay_order', 'name' => esc_html__('会员订单', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_coin_order_page', 'page' => 'pay_order', 'name' => esc_html__('充值订单', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_down_order_page', 'page' => 'down_order', 'name' => esc_html__('下载记录', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_aff_order_page', 'page' => 'aff_order', 'name' => esc_html__('推广记录', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_author_aff_order_page', 'page' => 'author_aff_order', 'name' => esc_html__('作者分成', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_coin_log_page', 'page' => 'aff_order', 'name' => esc_html__('余额日志', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_cdk_order_page', 'page' => 'aff_order', 'name' => esc_html__('卡密管理', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_admin_pay_page', 'page' => 'aff_order', 'name' => esc_html__('后台充值', 'rizhuti-v2')],
        ['menu' => 'rizhuti_v2_edit_price_page', 'page' => 'aff_order', 'name' => esc_html__('批量修改', 'rizhuti-v2')],
    ];
    if (_cao('is_site_tickets',true)) {
        $menu[] = ['menu' => 'rizhuti_v2_msg_order_page', 'page' => 'order_page', 'name' => esc_html__('消息工单', 'rizhuti-v2')];
    }
    foreach ($menu as $k => $v) {
        add_submenu_page($index_page, $v['name'], $v['name'], 'administrator', $v['menu'], $v['menu']);
    }
}
add_action('admin_menu', 'rizhuti_v2_add_admin_menu');


/**
 * 商城总览
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:17:08+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_shop_index_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/shop_index.php';
}


/**
 * 资源订单记录
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:17:08+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_pay_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/pay_order.php';
}

/**
 * 会员订单记录
 * @Author   Dadong2g
 * @DateTime 2021-03-12T14:47:11+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_vip_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/vip_order.php';
}

/**
 * 充值订单记录
 * @Author   Dadong2g
 * @DateTime 2021-03-12T14:47:34+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_coin_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/coin_order.php';
}

/**
 * 下载记录
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:17:14+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_down_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/down_order.php';
}

/**
 * 推广记录
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:17:25+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_aff_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/aff_order.php';
}

/**
 * 作者分成
 * @Author   Dadong2g
 * @DateTime 2021-06-01T22:21:59+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_author_aff_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/author_aff_order.php';
}


/**
 * 卡密管理
 * @Author   Dadong2g
 * @DateTime 2021-03-12T15:26:28+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_cdk_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/cdk_order.php';
}

/**
 * 余额日志查询
 * @Author   Dadong2g
 * @DateTime 2021-03-12T21:43:50+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_coin_log_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/coin_log.php';
}

/**
 * 消息工单
 * @Author   Dadong2g
 * @DateTime 2021-01-24T17:45:21+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_msg_order_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/msg_order.php';
}
/**
 * 批量编辑
 * @Author   Dadong2g
 * @DateTime 2021-03-13T21:51:43+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_edit_price_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/edit_price.php';
}

/**
 * 后台充值
 * @Author   Dadong2g
 * @DateTime 2021-03-14T09:50:45+0800
 * @return   [type]                   [description]
 */
function rizhuti_v2_admin_pay_page() {
    date_default_timezone_set(get_option('timezone_string'));
    require_once get_template_directory() . '/inc/admin/admin_pay.php';
}


/**
 * 普通用户发布文章权限控制
 */
if (!class_exists('Restrict_User_Content')):

    /**
     * Class Definition
     */
    class Restrict_User_Content {

        /**
         * @var bool Does this plugin need a settings page?
         */
        private $_has_settings_page = true;

        /**
         * @var array default settings
         */
        private $_default_settings = array(
            'additional_user_ids' => '0',
        );

        /**
         * Construct
         */
        public function __construct() {
            //Start your custom goodness
            add_action('pre_get_posts', array($this, 'ruc_pre_get_posts_media_user_only'));
            add_filter('parse_query', array($this, 'ruc_parse_query_useronly'));
            add_filter('ajax_query_attachments_args', array($this, 'ruc_ajax_attachments_useronly'));
            add_filter('views_edit-post', array($this, 'ruc_remove_other_users_posts'));
            add_filter('views_edit-page', array($this, 'ruc_remove_other_users_posts'));
            add_filter('admin_footer_text', array($this, 'my_admin_footer_text'));
            add_action('admin_menu', array($this, 'n_a_remove_menu_page'));
            add_action('wp_before_admin_bar_render', array($this, 'remove_admin_bar_links'));
            add_action('admin_init', array($this, 'no_admin_access_page'));
        }

        public function my_admin_footer_text() {
            return '<i class="fa fa-wordpress"></i> the wordpress theme by <a href="https://ritheme.com/" target="_blank">ritheme.com</a>';
        }

        //普通用户禁止修改后台个人信息 设置默认布局颜色
        public function no_admin_access_page() {
            $user_id = get_current_user_id();

            if (!current_user_can('manage_options') && is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX )) {
                if ( !_cao('is_site_tougao_wp',false) ) {
                    wp_redirect(home_url());die();
                }
                if (defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE) {
                    wp_redirect(admin_url('edit.php'));die();
                }
                //默认颜色
                if (get_user_meta($user_id, 'admin_color', true) != 'light') {
                    update_user_meta($user_id, 'admin_color', 'light');
                }
                // wordpress 投稿者可以上传附件
                if (current_user_can('contributor') && !current_user_can('upload_files')) {
                    $contributor = get_role('contributor');
                    $contributor->add_cap('upload_files');
                    // $contributor->remove_cap('upload_files');
                }
            }
        }

        //删除后台顶部菜单
        public function remove_admin_bar_links() {
            if (!current_user_can('manage_options') && is_admin()) {
                global $wp_admin_bar;
                $wp_admin_bar->remove_menu('rizhuti-v2'); // 移除链接
                $wp_admin_bar->remove_menu('my-account'); // 移除链接
                $wp_admin_bar->remove_menu('wp-logo'); // 移除链接
            }
        }

        //删除后台页面
        public function n_a_remove_menu_page() {
            if (!current_user_can('manage_options') && is_admin()) {
                remove_menu_page('index.php');
                remove_menu_page('tools.php');
                remove_menu_page('edit-comments.php');
                remove_menu_page('profile.php');
                remove_menu_page('rizhuti-v2');
            }
        }

        //后台媒体库文件
        public function ruc_pre_get_posts_media_user_only($query) {

            if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/upload.php') !== false) {
                if (!current_user_can('update_core')) {
                    $query->set('author__in', $this->ruc_create_list_of_user_ids());
                }
            }
        }

        public function ruc_parse_query_useronly($wp_query) {
            if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/edit.php') !== false) {
                if (!current_user_can('update_core')) {
                    $current_user = wp_get_current_user();
                    $wp_query->set('author', $current_user->ID);
                }
            }
        }

        public function ruc_ajax_attachments_useronly($query) {
            if (!current_user_can('update_core')) {
                $users               = $this->ruc_create_list_of_user_ids();
                $query['author__in'] = $users;
            }
            return $query;
        }

        private function ruc_create_list_of_user_ids() {
            $current_user = wp_get_current_user();
            $users = explode(',', '');
            array_unshift($users, $current_user->ID);
            return $users;
        }

        public function ruc_remove_other_users_posts($views) {
            if (!current_user_can('manage_options')) {
                foreach ($views as $key => $data) {
                    if ('mine' !== $key) {
                        unset($views[$key]);
                    }
                }
            }
            return $views;
        }

    }

    new Restrict_User_Content();

endif;

/**
 * 时间日期查询类
 */
class RiPLus_Time {

    /**
     * 返回今日开始和结束的时间戳
     *
     * @return array
     */
    public static function today() {

        return [
            mktime(0, 0, 0, date('m'), date('d'), date('Y')),
            mktime(23, 59, 59, date('m'), date('d'), date('Y')),
        ];
    }

    /**
     * 返回昨日开始和结束的时间戳
     *
     * @return array
     */
    public static function yesterday() {
        $yesterday = date('d') - 1;
        return [
            mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
            mktime(23, 59, 59, date('m'), $yesterday, date('Y')),
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     *
     * @return array
     */
    public static function week() {
        $timestamp = time();
        return [
            mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")),
            mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y")),
        ];
    }

    /**
     * 返回上周开始和结束的时间戳
     *
     * @return array
     */
    public static function lastWeek() {
        $timestamp = time();
        return [
            mktime(0, 0, 0, date("m"), date("d") - date("w") + 1 - 7, date("Y")),
            mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 7, date("Y")),
        ];
    }

    /**
     * 返回本月开始和结束的时间戳
     *
     * @return array
     */
    public static function month($everyDay = false) {
        return [
            mktime(0, 0, 0, date('m'), 1, date('Y')),
            mktime(23, 59, 59, date('m'), date('t'), date('Y')),
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     *
     * @return array
     */
    public static function lastMonth() {
        $begin = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $end   = mktime(23, 59, 59, date('m') - 1, date('t', $begin), date('Y'));

        return [$begin, $end];
    }

    /**
     * 返回今年开始和结束的时间戳
     *
     * @return array
     */
    public static function year() {
        return [
            mktime(0, 0, 0, 1, 1, date('Y')),
            mktime(23, 59, 59, 12, 31, date('Y')),
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     *
     * @return array
     */
    public static function lastYear() {
        $year = date('Y') - 1;
        return [
            mktime(0, 0, 0, 1, 1, $year),
            mktime(23, 59, 59, 12, 31, $year),
        ];
    }

    public static function dayOf() {

    }

    /**
     * 获取几天前零点到现在/昨日结束的时间戳
     *
     * @param int $day 天数
     * @param bool $now 返回现在或者昨天结束时间戳
     * @return array
     */
    public static function dayToNow($day = 1, $now = true) {
        $end = time();
        if (!$now) {
            list($foo, $end) = self::yesterday();
        }

        return [
            mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
            $end,
        ];
    }

    /**
     * 返回几天前的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAgo($day = 1) {
        $nowTime = time();
        return $nowTime - self::daysToSecond($day);
    }

    /**
     * 返回几天后的时间戳
     *
     * @param int $day
     * @return int
     */
    public static function daysAfter($day = 1) {
        $nowTime = time();
        return $nowTime + self::daysToSecond($day);
    }

    /**
     * 天数转换成秒数
     *
     * @param int $day
     * @return int
     */
    public static function daysToSecond($day = 1) {
        return $day * 86400;
    }

    /**
     * 周数转换成秒数
     *
     * @param int $week
     * @return int
     */
    public static function weekToSecond($week = 1) {
        return self::daysToSecond() * 7 * $week;
    }

    private static function startTimeToEndTime() {

    }
}

/**
 * 自动清理公众号登录缓存
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:18:15+0800
 * @return   [type]                   [description]
 */
function remov_mpwx_log_run_cron() {
    if (isset($_GET['page']) && $_GET['page'] == 'rizhuti_v2_pay_order_page') {
        global $wpdb, $mpwx_log_table_name;
        $timestamp = time() - 180;
        return $wpdb->query($wpdb->prepare("DELETE FROM $mpwx_log_table_name WHERE create_time < %s ", $timestamp));
    }
}
add_action('admin_init', 'remov_mpwx_log_run_cron');




/**
 * 后台文章列表钩子定制
 */

function rizhuti_v2_add_sticky_column( $columns ) {

    $post_type = get_post_type();
    if ( $post_type == 'post' ) {
        return array_merge( $columns, array( 'wppay_price' => __( '售价', 'rizhuti-v2' ),'wppay_vip_auth' => __( '会员权限', 'rizhuti-v2' ) ) );
    }else{
        return $columns;
    }

}

function rizhuti_v2_display_posts_stickiness( $column, $post_id ) {
    switch ($column) {
        case 'wppay_price':
            $meta = get_post_meta($post_id, 'wppay_price', true);
            $meta = ($meta=='') ? '' : (float)$meta ;
            echo $meta;
        break;
        case 'wppay_vip_auth':
            $auth = array(
                '0' => esc_html__('不启用', 'rizhuti-v2'),
                '1' => esc_html__('包月VIP免费', 'rizhuti-v2'),
                '2' => esc_html__('包年VIP免费', 'rizhuti-v2'),
                '3' => esc_html__('终身VIP免费', 'rizhuti-v2'),
            );
            $meta = get_post_meta($post_id, 'wppay_vip_auth', true);
            $meta = ($meta=='') ? '' : $auth[intval($meta)] ;
            echo $meta;
        break;
    }
}


/**
 * 后台用户列表钩子定制
 */
function rizhuti_v2_add_user_column($columns) {
    $columns['registered'] = __('注册时间', 'rizhuti-v2');
    $columns['last_login'] = __('最近登录', 'rizhuti-v2');
    $columns['vip_type'] = __('会员等级', 'rizhuti-v2');
    $columns['mycoin'] = __('余额', 'rizhuti-v2');
    $columns['is_fuck'] = __('状态', 'rizhuti-v2');
    return $columns;
}

function rizhuti_v2_output_users_columns($var, $column_name, $user_id){
    switch( $column_name ) {
        case "registered" :
            $user = get_userdata($user_id);
            $ip = get_userdata($user_id);
            return get_date_from_gmt($user->user_registered).'<br>IP：'.get_user_meta($user_id, 'register_ip', true);
            break;
        case "last_login" :
            return get_user_meta($user_id, 'last_login_time', true).'<br>IP：'.get_user_meta($user_id, 'last_login_ip', true);
            break;
        case "vip_type" :
            global $ri_vip_options;
            $vip_type = _get_user_vip_type($user_id);
            return $ri_vip_options[$vip_type];
            break;
        case "mycoin" :
            return site_mycoin('name').'：'.get_user_mycoin($user_id);
        case "is_fuck" :
            if (!empty(get_user_meta($user_id, 'is_fuck', true))) {
                return esc_html__('封号中','rizhuti-v2');
            }else{
                return esc_html__('正常','rizhuti-v2');
            }
            break;
    }
}

function rizhuti_v2_users_sortable_columns($sortable_columns){
    $sortable_columns['registered'] = 'registered';
    return $sortable_columns;
}

function rizhuti_v2_views_users($views) {
    global $wpdb,$ri_vip_options;
    if (!current_user_can('edit_users')) {
        return $views;
    }
    foreach ($ri_vip_options as $k => $v) {
        if ( 0 != $k ){
            $count = $wpdb->get_var($wpdb->prepare("SELECT count(a.ID) FROM $wpdb->users a LEFT JOIN $wpdb->usermeta b ON a.ID = b.user_id WHERE ifnull(b.meta_key,'vip_type')=%s AND ifnull(b.meta_value,'0')=%s", 'vip_type',$k));
            $views['vip_'.$k] = '<a href="' . admin_url('users.php') . '?vip_type='.$k.'">'.$v.'<span class="count">（'.$count.'）</span></a>';
        }
    }
    return $views;
}


function rizhuti_v2_pre_user_query( $uqi ){
    global $wpdb;
    $vip_type = (empty($_GET['vip_type'])) ? false : trim($_GET['vip_type']) ;
    if ($vip_type) {
        $search_meta = $wpdb->prepare("
        ID IN ( SELECT user_id FROM {$wpdb->usermeta}
        WHERE ( meta_key='vip_type' AND meta_value = '%s' )
        )", $vip_type);

        $uqi->query_where = str_replace(
            'WHERE 1=1',
            "WHERE 1=1 AND " . $search_meta,
            $uqi->query_where );
    }
    
}

function rizhuti_v2_filter_users($query) {
    global $pagenow, $wpdb;
    $vip_type = (empty($_GET['vip_type'])) ? false : trim($_GET['vip_type']) ;
    if (is_admin() && 'users.php' == $pagenow && $vip_type) {
        $query->set('meta_query', array(
            array(
                'key'     => 'vip_type',
                'value'   => $vip_type,
                'compare' => '==',
            ),
            array(
                'key'     => 'vip_time',
                'value'   => time(),
                'compare' => '>',
            ),
        ));
    }
    return $query;
}

if (!is_close_site_shop()) {
    add_filter( 'manage_posts_columns' , 'rizhuti_v2_add_sticky_column' );
    add_action( 'manage_posts_custom_column' , 'rizhuti_v2_display_posts_stickiness', 10, 2 );
    add_filter('manage_users_columns', 'rizhuti_v2_add_user_column');
    add_action('manage_users_custom_column', 'rizhuti_v2_output_users_columns', 10, 3);
    add_filter( "manage_users_sortable_columns", 'rizhuti_v2_users_sortable_columns' );
    add_filter('views_users', 'rizhuti_v2_views_users');
    // add_filter('pre_get_users', 'rizhuti_v2_filter_users');
    add_action( 'pre_user_query', 'rizhuti_v2_pre_user_query');
}

