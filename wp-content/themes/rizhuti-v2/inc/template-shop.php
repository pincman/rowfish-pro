<?php

/**
 * Exit if accessed directly.
 */
defined('ABSPATH') || exit;

/**
 * RiConf init
 */

/**
 * 初始化会员名称 如需修改改这里即可 可以用 add_filter()添加或修改
 * @var [type]
 */
$ri_vip_options = apply_filters('ri_vip_options', array(
    '0'    => esc_html__('普通用户', 'rizhuti-v2'),
    '31'   => esc_html__('包月VIP', 'rizhuti-v2'),
    '365'  => esc_html__('包年VIP', 'rizhuti-v2'),
    '3600' => esc_html__('终身VIP', 'rizhuti-v2'),
));

/**
 * 初始化支付方式配置 如需修改改这里即可 可以用 add_filter()添加或修改
 * @var [type]
 */
$ri_pay_type_options = apply_filters('ri_pay_type_options', array(
    '1'  => array('name' => '支付宝', 'sulg' => 'alipay'),
    '2'  => array('name' => '微信', 'sulg' => 'weixinpay'),
    '11' => array('name' => '虎皮椒-支付宝', 'sulg' => 'hupijiao_alipay'),
    '12' => array('name' => '虎皮椒-微信', 'sulg' => 'hupijiao_weixin'),
    '21' => array('name' => '迅虎H5-支付宝', 'sulg' => 'xunhupay_alipay'),
    '22' => array('name' => '迅虎H5-微信', 'sulg' => 'xunhupay_weixin'),
    '88' => array('name' => '卡密支付', 'sulg' => 'cdk_pay'),
    '99' => array('name' => '余额支付', 'sulg' => 'mycoin_pay'),
    '77' => array('name' => '后台充值', 'sulg' => 'admin_pay'),
));

/**
 * 商城功能入口控制开关
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:54:54+0800
 * @return   boolean                  [description]
 */
function is_close_site_shop() {
    return apply_filters('is_site_shop', empty(_cao('is_site_shop', '1')));
}

/**
 * 站内币信息获取
 * @Author   Dadong2g
 * @DateTime 2021-03-10T11:58:25+0800
 * @param    string                   $params [description]
 * @return   [type]                           [description]
 */
function site_mycoin($params = 'name') {
    switch ($params) {
    case 'is':
        return (int) _cao('is_site_mycoin', 1);
        break;
    case 'name':
        return _cao('site_mycoin_name', '金币');
        break;
    case 'rate':
        return (float) _cao('site_mycoin_rate', '10');
        break;
    case 'icon':
        return _cao('site_mycoin_icon', '金币');
        break;
    case 'min_pay':
        return (int) _cao('site_mycoin_pay_minnum', 1);
        break;
    case 'max_pay':
        return (int) _cao('site_mycoin_pay_maxnum', 1);
        break;
    case 'pay_arr':
        return (array) _cao('site_mycoin_pay_arr', array());
        break;
    default:
        return _cao('site_mycoin_name', 'fas fa-coins');
        break;
    }

}

/**
 * 转换站内币汇率
 * @Author   Dadong2g
 * @DateTime 2021-03-10T12:18:41+0800
 * @param    integer                  $num [数量]
 * @param    string                   $to  [coin or rmb]
 * @return   [type]                        [float]
 */
function convert_site_mycoin($num = 0, $to = 'coin') {
    // RMB汇率
    $site_rate = site_mycoin('rate');
    switch ($to) {
    case 'coin':
        $new_num = $num * $site_rate;
        break;
    case 'rmb':
        $new_num = $num / $site_rate;
        break;
    default:
        $new_num = $num;
        break;
    }
    return (float) $new_num;
}

/**
 * 获取用户余额
 * @Author   Dadong2g
 * @DateTime 2021-03-10T13:21:55+0800
 * @param    [type]                   $user_id [description]
 * @return   [type]                            [description]
 */
function get_user_mycoin($user_id = null) {

    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }

    $mycoin = (float) get_user_meta($user_id, 'mycoin', true);
    if (0 > $mycoin) {
        $mycoin = 0;
    }
    return $mycoin;

}

/**
 * 更新用户余额
 * @Author   Dadong2g
 * @DateTime 2021-03-10T13:17:54+0800
 * @param    [type]                   $user_id  [description]
 * @param    integer                  $coin_num [-100 100 支持负数]
 * @return   [type]                             [description]
 */
function update_user_mycoin($user_id = null, $coin_num = 0) {

    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }

    $this_coin = (float) get_user_meta($user_id, 'mycoin', true);

    if (empty($this_coin)) {
        $this_coin = 0;
    }

    $old_coin = (float) get_user_meta($user_id, 'old_mycoin', true);
    if (empty($old_coin)) {
        $old_coin = 0;
    }

    $new_coin = $this_coin + $coin_num;
    if (0 > $new_coin) {
        $new_coin = 0;
    }

    if ($new_coin != $this_coin) {
        update_user_meta($user_id, 'old_mycoin', $this_coin);
    }

    return update_user_meta($user_id, 'mycoin', $new_coin);

}

/**
 * 会员配置初始化
 * @Author   Dadong2g
 * @DateTime 2021-06-01T21:11:10+0800
 * @return   [type]                   [description]
 */
function _get_ri_vip_options() {
    global $ri_vip_options;
    $cao_opt = _cao('site_vip_options');
    $vip_opt = [];
    foreach ($ri_vip_options as $key => $opt) {
        $price            = (isset($cao_opt[$key . '_vip_price'])) ? $cao_opt[$key . '_vip_price'] : 0;
        $downnum          = (isset($cao_opt[$key . '_vip_downnum'])) ? (int) $cao_opt[$key . '_vip_downnum'] : 0;
        $download_rate    = (isset($cao_opt[$key . '_vip_download_rate'])) ? (float) $cao_opt[$key . '_vip_download_rate'] : 0;
        $aff_ratio        = (isset($cao_opt[$key . '_vip_aff_ratio'])) ? (float) $cao_opt[$key . '_vip_aff_ratio'] : 0;
        $author_aff_ratio = (isset($cao_opt[$key . '_vip_author_aff_ratio'])) ? (float) $cao_opt[$key . '_vip_author_aff_ratio'] : 0;
        $vip_opt[$key]    = [
            'name'             => $opt, //会员组名称
            'day'              => (int) $key, //会员有效天数
            'price'            => $price, //会员价格
            'downnum'          => $downnum, //每日下载次数
            'download_rate'    => $download_rate, //下载速度
            'aff_ratio'        => $aff_ratio, //推广佣金
            'author_aff_ratio' => $author_aff_ratio,
        ];
    }

    return apply_filters('get_ri_vip_options', $vip_opt);
}

/**
 * 文章是否下载资源文章
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:54:59+0800
 * @param    [type]                   $post_ID [description]
 * @return   [type]                            [description]
 */
function _get_post_shop_type($post_ID = null) {
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    $wppay_type = get_post_meta($post_ID, 'wppay_type', true);
    if (empty($wppay_type) || $wppay_type == 0) {
        return false;
    }
    return $wppay_type;
}

/**
 * 移动端商城定位
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:55:26+0800
 * @return   [type]                   [description]
 */
function shop_widget_wap_position() {
    if (!is_close_site_shop()) {
        echo '<div class="pt-0 d-none d-block d-xl-none d-lg-none"><aside id="header-widget-shop-down" class="widget-area"><p></p></aside></div>';
    }
}

/**
 * 获取商城资源信息
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:55:37+0800
 * @param    [type]                   $post_ID  [description]
 * @param    [type]                   $meta_key [description]
 * @return   [type]                             [description]
 */
function get_post_shop_info($post_ID = null, $meta_key = null) {
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    if ($meta_key) {
        $meta = get_post_meta($post_ID, $meta_key, 1);
    } else {
        $arr  = array('wppay_type', 'wppay_price', 'wppay_vip_auth', 'wppay_down', 'wppay_demourl', 'wppay_info');
        $meta = array();
        foreach ($arr as $_key) {
            $meta[$_key] = get_post_meta($post_ID, $_key, 1);
        }
    }
    return $meta;
}

/**
 * 文章资源会员权限
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:07+0800
 * @param    [type]                   $post_ID [description]
 * @return   [type]                            [description]
 */
function _get_post_vip_auth($post_ID = null) {
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    $meta = get_post_meta($post_ID, 'wppay_vip_auth', true);
    return (!empty($meta)) ? $meta : '0'; //默认原价
}

/**
 * 获取价格角标
 * @Author   Dadong2g
 * @DateTime 2021-03-31T10:41:48+0800
 * @param    [type]                   $post_ID [description]
 * @return   [type]                            [description]
 */
function get_post_meta_vip_price($post_ID = null) {
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }

    $shop_info = get_post_shop_info($post_ID);

    if (is_close_site_shop() || empty($shop_info['wppay_type'])) {
        return '';
    }

    //是否VIP资源 普通用户不能购买
    $is_vip_post = !empty($shop_info['wppay_vip_auth']) && empty($shop_info['wppay_price']);
    $price       = (float) $shop_info['wppay_price'];

    $bg   = 'bg-success';
    $icon = '<i class="fas fa-yen-sign mr-1"></i>';

    if (site_mycoin('is')) {
        $price = convert_site_mycoin($price, 'coin');
        $icon  = '<i class="' . site_mycoin('icon') . ' mr-1"></i>';
    }

    if (4 == $shop_info['wppay_type'] || $price == 0) {
        $bg    = 'bg-danger';
        $icon  = '';
        $price = esc_html__('免费', 'rizhuti-v2');
    } elseif ($is_vip_post || !empty($shop_info['wppay_vip_auth'])) {
        $bg    = 'bg-warning';
        $icon  = '<i class="fa fa-diamond mr-1"></i>';
        $price = esc_html__('免费', 'rizhuti-v2');
    }

    return '<span class="meta-vip-price ' . $bg . '">' . $icon . $price . '</span>';

}

/**
 * 文章权限角标
 * @Author   Dadong2g
 * @DateTime 2021-04-06T19:38:53+0800
 * @param    integer                  $vip_auth [description]
 * @return   [type]                             [description]
 */
function get_post_vip_auth_badge($vip_auth = 0) {
    global $ri_vip_options;
    $_icon  = '<i class="fa fa-diamond mr-1"></i>';
    $_badge = array('0' => '',
        '31'                => '<b class="badge badge-success-lighten mr-2">' . $_icon . $ri_vip_options['31'] . '</b>',
        '365'               => '<b class="badge badge-info-lighten mr-2">' . $_icon . $ri_vip_options['365'] . '</b>',
        '3600'              => '<b class="badge badge-warning-lighten mr-2">' . $_icon . $ri_vip_options['3600'] . '</b>',
    );
    $_vip_auth = array('0' => '',
        '1'                    => $_badge['31'] . $_badge['365'] . $_badge['3600'],
        '2'                    => $_badge['365'] . $_badge['3600'],
        '3'                    => $_badge['3600'],
    );
    return $_vip_auth[$vip_auth];
}

/**
 * 获取会员到期时间
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:19+0800
 * @param    [type]                   $user_id [description]
 * @return   [type]                            [description]
 */
function _get_user_vip_endtime($user_id = null) {
    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }
    $end_time = is_timestamp(get_user_meta($user_id, 'vip_time', true));
    if ($end_time) {
        return $end_time;
    } else {
        return time();
    }

}

/**
 * 会员类型 已到期则为普通会员
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:26+0800
 * @param    [type]                   $user_id [description]
 * @return   [type]                            [description]
 */
function _get_user_vip_type($user_id = null) {
    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }
    $vip_type = get_user_meta($user_id, 'vip_type', true);
    $end_time = is_timestamp(get_user_meta($user_id, 'vip_time', true));
    if (empty($vip_type) || $vip_type == '0') {
        return '0';
    }
    //未开通过
    if (empty($end_time) || $end_time < time()) {
        return '0';
    }
    //已到期
    if ($vip_type == '31' || $vip_type == '365' || $vip_type == '3600') {
        return $vip_type;
    }
    //有效
    return '0';
}

/**
 * 获取用户推广信息
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:30+0800
 * @param    [type]                   $user_id [description]
 * @return   [type]                            [description]
 */
function _get_user_aff_info($user_id = null) {
    global $wpdb, $wppay_table_name;

    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }

    $info = [];
    $sql  = "SELECT SUM(round(order_price*aff_ratio,1)) FROM $wppay_table_name WHERE status=1 AND aff_uid=$user_id AND aff_ratio>0";
    // 获取总条数
    $info['total'] = $wpdb->get_var("SELECT COUNT(id) FROM $wppay_table_name WHERE status=1 AND aff_uid={$user_id} AND aff_ratio>0");
    //累计佣金
    $info['leiji'] = $wpdb->get_var($sql);
    //可提现
    $info['keti'] = $wpdb->get_var($sql . " AND IFNULL(aff_status,0)=0");
    //提现中
    $info['tixian'] = $wpdb->get_var($sql . " AND IFNULL(aff_status,0)=1");
    //已提现
    $info['yiti'] = $wpdb->get_var($sql . " AND IFNULL(aff_status,0)=2");

    $vip_opt          = _get_ri_vip_options();
    $aff_vip_type     = _get_user_vip_type($user_id);
    $aff_ratio        = $vip_opt[$aff_vip_type]['aff_ratio'];
    $author_aff_ratio = $vip_opt[$aff_vip_type]['author_aff_ratio'];

    if (!_cao('is_site_author_aff',false)) {
        $author_aff_ratio = 0;
    }

    //初始化
    $info['total']            = (empty($info['total'])) ? '0' : $info['total'];
    $info['aff_ratio']        = $aff_ratio;
    $info['author_aff_ratio'] = $author_aff_ratio;
    $info['leiji']            = (empty($info['leiji'])) ? '0' : $info['leiji'];
    $info['keti']             = (empty($info['keti'])) ? '0' : $info['keti'];
    $info['tixian']           = (empty($info['tixian'])) ? '0' : $info['tixian'];
    $info['yiti']             = (empty($info['yiti'])) ? '0' : $info['yiti'];
    return $info;
}


//获取该作者的收入详情
function _get_user_author_aff_info($user_id = null) {
    global $wpdb, $msg_table_name;

    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }

    $info = [];
    $sql  = "SELECT SUM(msg) FROM $msg_table_name WHERE uid={$user_id} AND type=4";
    // 获取总条数
    $info['total'] = $wpdb->get_var("SELECT COUNT(id) FROM $msg_table_name WHERE uid={$user_id} AND type=4");
    //累计收入
    $info['leiji'] = $wpdb->get_var($sql);
    //可提现
    $info['keti'] = $wpdb->get_var($sql . " AND IFNULL(to_status,0)=0");
    //提现中
    $info['tixian'] = $wpdb->get_var($sql . " AND IFNULL(to_status,0)=1");
    //已提现
    $info['yiti'] = $wpdb->get_var($sql . " AND IFNULL(to_status,0)=2");

    $vip_opt          = _get_ri_vip_options();
    $aff_vip_type     = _get_user_vip_type($user_id);
    $aff_ratio        = $vip_opt[$aff_vip_type]['aff_ratio'];
    $author_aff_ratio = $vip_opt[$aff_vip_type]['author_aff_ratio'];
    
    if (!_cao('is_site_author_aff',false)) {
        $author_aff_ratio = 0;
    }

    //初始化
    $info['total']            = (empty($info['total'])) ? '0' : $info['total'];
    $info['aff_ratio']        = $aff_ratio;
    $info['author_aff_ratio'] = $author_aff_ratio;
    $info['leiji']            = (empty($info['leiji'])) ? '0' : $info['leiji'];
    $info['keti']             = (empty($info['keti'])) ? '0' : $info['keti'];
    $info['tixian']           = (empty($info['tixian'])) ? '0' : $info['tixian'];
    $info['yiti']             = (empty($info['yiti'])) ? '0' : $info['yiti'];
    return $info;
}

/**
 * 是否时间戳
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:36+0800
 * @param    [type]                   $timestamp [description]
 * @return   boolean                             [description]
 */
function is_timestamp($timestamp) {
    if (!empty($timestamp) && strtotime(date('Y-m-d H:i:s', $timestamp)) == $timestamp) {
        return (int) $timestamp;
    } else {
        return false;
    }

}

/**
 * 更新会员数据
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:40+0800
 * @param    [type]                   $user_id  [description]
 * @param    [type]                   $vip_type [description]
 * @param    [type]                   $day      [description]
 * @return   [type]                             [description]
 */
function update_user_vip_info($user_id, $vip_type, $day) {
    if (empty($user_id)) {
        return false;
    }
    $this_user_type    = _get_user_vip_type($user_id); //当前会员类型 0 31 365 3600
    $this_user_enditme = _get_user_vip_endtime($user_id); //当前会员到期时间

    if ($this_user_type == '3600') {
        return false;
    }

    $the_time = time();
    $end_time = $this_user_enditme;

    //如果不是会员 则到期时间以今天为准
    if (empty($this_user_type)) {
        $end_time = $the_time;
    }
    if ($end_time > $the_time) {
        $new_end_time = $end_time + $day * 24 * 3600;
        // 会员结束日期大于今天 累计增加
    } else {
        $new_end_time = $the_time + $day * 24 * 3600;
        // 会员结束日期小于今天 以今天开始加
    }
    if ($this_user_type == '31' && $vip_type == '365') {
        // 月卡升级年卡以月卡结束时间加一年
        $new_end_time = $end_time + $day * 24 * 3600;
    }

    if ($vip_type == '3600') {
        #  如果是升级或者开通永久会员 则赋值 9999-09-09 时间戳
        $new_end_time = strtotime('9999-09-09');
    }
    // 更新数据
    update_user_meta($user_id, 'vip_type', $vip_type);
    update_user_meta($user_id, 'vip_time', $new_end_time);
    return true;
}

/**
 * 添加下载记录
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:46+0800
 * @param    [type]                   $user_id [description]
 * @param    [type]                   $post_id [description]
 */
function add_new_down_log($user_id, $post_id) {
    global $wpdb, $down_table_name;
    $ip      = get_client_ip();
    $_params = array(
        'user_id'     => $user_id,
        'post_id'     => $post_id,
        'ip'          => $ip,
        'create_time' => time(),
    );
    $ins = $wpdb->insert($down_table_name, $_params, array('%d', '%d', '%s', '%s'));
    return $ins ? true : false;
}

/**
 * 获取用户下载次数信息
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:56:56+0800
 * @param    [type]                   $user_id [description]
 * @return   [type]                            [description]
 */
function _get_user_today_down($user_id = null) {
    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }
    $vip_options = _get_ri_vip_options();
    $user_type   = _get_user_vip_type($user_id);
    // 今日总共可以可下载次数
    $zong = $vip_options[$user_type]['downnum'];
    //今日已经下载次数
    $yi = _get_user_today_downum($user_id);
    //剩余次数
    $ke = ($zong - $yi);
    $ke = ($ke > 0) ? $ke : 0;
    return array('zong' => $zong, 'yi' => $yi, 'ke' => $ke);
}

/**
 * 获取用户下载总次数 如果是单独购买的文章资源 不会被限制下载
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:57:02+0800
 * @param    [type]                   $user_id [description]
 * @return   [type]                            [description]
 */
function _get_user_today_downum($user_id) {
    if (empty($user_id) || $user_id == 0) {
        return 0;
    }
    global $wpdb, $down_table_name, $wppay_table_name;
    $today = _get_today_Time();
    $num   = $wpdb->query($wpdb->prepare(
        "select a.post_id,b.status,count(a.post_id) from $down_table_name as a left join $wppay_table_name as b on (a.user_id = b.user_id and a.post_id=b.post_id and b.status=1) where a.user_id=%d and a.create_time>%d and a.create_time<%d group by a.post_id having isnull(b.status)", $user_id, $today['star'], $today['end']));
    return (int) $num;
}

/**
 * 今日是否下载过次资源
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:57:10+0800
 * @param    [type]                   $user_id [description]
 * @param    [type]                   $post_id [description]
 * @return   boolean                           [description]
 */
function is_today_down_posot($user_id, $post_id) {
    global $wpdb, $down_table_name;
    $today = _get_today_Time();
    $num   = $wpdb->query($wpdb->prepare(
        "select count(post_id) from $down_table_name where user_id=%d and post_id=%d and create_time>%d and create_time<%d group by post_id", $user_id, $post_id, $today['star'], $today['end']));
    return (int) $num;
}

/**
 * 获取今天的开始和结束时间
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:57:13+0800
 * @return   [type]                   [description]
 */
function _get_today_Time() {
    $str          = date("Y-m-d", time()) . "0:0:0";
    $data["star"] = strtotime($str);
    $str          = date("Y-m-d", time()) . "24:00:00";
    $data["end"]  = strtotime($str);
    return $data;
}

/**
 * 获取支付方式
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:57:19+0800
 * @return   [type]                   [description]
 */
function _riplus_get_pay_type_html() {
    global $ri_pay_type_options, $current_user;
    $is_alipay   = $is_weixinpay   = $is_iconpay   = $is_cdkpay   = false;
    $alipay_type = $wxpay_type = $iconpay_type = $cdkpay_type = 0;
    foreach ($ri_pay_type_options as $k => $v) {

        $_type = $k % 10;

        if (($is_alipay && $_type == 1) || ($is_weixinpay && $_type == 2)) {
            continue;
        }

        if (_cao('is_' . $v['sulg'])) {
            if ($_type == 1) {
                $is_alipay   = true;
                $alipay_type = $k;
            } elseif ($_type == 2) {
                $is_weixinpay = true;
                $wxpay_type   = $k;
            } elseif ($_type == 9) {
                $is_iconpay   = true;
                $iconpay_type = 99;
            } elseif ($_type == 8) {
                $is_cdkpay   = true;
                $cdkpay_type = 88;
            }

        }

        if ($is_alipay && $is_weixinpay && $is_iconpay) {
            break;
        }

    }

    $html = '<div class="pay-button-box">';

    if ($is_alipay) {
        $html .= '<div class="pay-item" id="alipay" data-type="' . $alipay_type . '"><i class="alipay"></i><span>' . esc_html__('支付宝', 'rizhuti-v2') . '</span></div>';
    }

    if ($is_weixinpay) {
        $html .= '<div class="pay-item" id="weixinpay" data-type="' . $wxpay_type . '"><i class="weixinpay"></i><span>' . esc_html__('微信支付', 'rizhuti-v2') . '</span></div>';
    }

    if ($is_iconpay && is_user_logged_in() && get_query_var('action') != 'coin') {
        $html .= '<div class="pay-item" id="iconpay" data-type="' . $iconpay_type . '"><i class="iconpay"></i><span>' . esc_html__('余额支付', 'rizhuti-v2') . '</span></div>';
    }

    if (false && $is_cdkpay && is_user_logged_in() && get_query_var('action') == 'coin') {
        $html .= '<div class="pay-item" id="cdkpay" data-type="' . $cdkpay_type . '"><i class="cdkpay"></i><span>' . esc_html__('卡密支付', 'rizhuti-v2') . '</span></div>';
    }

    $html .= '</div>';
    return array('html' => $html, 'alipay' => $alipay_type, 'weixinpay' => $wxpay_type, 'iconpay' => $iconpay_type, 'cdkpay' => $cdkpay_type);
}

/**
 * 获取支付方式html字符串
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:57:23+0800
 * @param    [type]                   $type        [description]
 * @param    [type]                   $order_price [description]
 * @param    [type]                   $qrimg       [description]
 * @return   [type]                                [description]
 */
function get_ajax_payqr_html($type, $order_price, $qrimg) {
    switch ($type) {
    case 'alipay':
        $iconstr  = '<img src="' . get_template_directory_uri() . '/assets/img/alipay.png" class="qr-pay">';
        $html_str = '<div class="qrcon"> <h5> ' . $iconstr . ' </h5> <div class="title">支付宝扫码支付 ' . $order_price . ' 元</div> <div align="center" class="qrcode"> <img src="' . $qrimg . '"/> </div> <div class="bottom alipay"> 请使用支付宝扫一扫<br><small>扫码后等待 5 秒左右，切勿关闭扫码窗口</small></br></div> </div>';
        break;
    case 'weixinpay':
        $iconstr  = '<img src="' . get_template_directory_uri() . '/assets/img/weixin.png" class="qr-pay">';
        $html_str = '<div class="qrcon"> <h5> ' . $iconstr . ' </h5> <div class="title">微信扫码支付 ' . $order_price . ' 元</div> <div align="center" class="qrcode"> <img src="' . $qrimg . '"/> </div> <div class="bottom weixinpay"> 请使用微信扫一扫<br><small>扫码后等待 5 秒左右，切勿关闭扫码窗口</small></br></div> </div>';
        break;
    default:
        break;
    }

    return $html_str;
}

/**
 * 统一支付
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:57:40+0800
 * @param    [type]                   $pay_type   [description]
 * @param    [type]                   $order_data [description]
 * @return   [type]                               [description]
 */
function get_pay_snyc_data($pay_type, $order_data) {
    global $ri_pay_type_options;
    if (!is_user_logged_in() && _cao('is_rizhuti_v2_nologin_pay')) {
        $_SESSION['current_pay_ordernum'] = $order_data['order_num'];
    }
    // 判断支付方式 1 支付宝 START
    $RiPlusPay = new RiPlusPay();
    $_the_type = (string) $ri_pay_type_options[$pay_type]['sulg'];

    //卡密支付
    if ('cdk_pay' == $_the_type) {
        global $current_user;
        $user_id = $current_user->ID;

        if (!_cao('is_cdk_pay', true)) {
            echo json_encode(array('status' => '0', 'msg' => '卡密通道暂未开启'));exit;
        }

        $cdk_money = RiCdk::get_cdk($order_data['cdk_code'], true);
        if (empty($cdk_money) || $cdk_money <= 0) {
            echo json_encode(array('status' => '0', 'msg' => '无效卡密，请输入有效卡密'));exit;
        }
        // 充值站内币数量
        $coin_num = convert_site_mycoin($cdk_money, 'coin');

        // 销毁卡密
        if (RiCdk::update_cdk($order_data['cdk_code'])) {
            $trade_no = $order_data['cdk_code']; // 时间戳和消费前余额
            $RiClass  = new RiClass;
            if (!$RiClass->send_order_trade_notify($order_data['order_num'], $trade_no)) {
                echo json_encode(array('status' => '0', 'msg' => '订单处理状态异常'));exit;
            }
        } else {
            echo json_encode(array('status' => '0', 'msg' => '卡密异常，请刷新重试'));exit;
        }

        //延迟处理 增强交互体验
        usleep(500000);
        //返回前段json数据
        echo json_encode(array('status' => '1', 'type' => '4', 'msg' => '卡密充值成功，+ ' . $coin_num . ' ' . site_mycoin('name') . '，余额：' . get_user_mycoin($user_id), 'num' => $order_data['order_num']));exit;

    }
    //余额支付
    if ('mycoin_pay' == $_the_type) {
        global $current_user;
        $user_id = $current_user->ID;

        if (!site_mycoin('is') || $order_data['order_type'] == 99) {
            echo json_encode(array('status' => '0', 'msg' => '余额支付通道未开启'));exit;
        }

        $coin_price  = convert_site_mycoin($order_data['order_price'], 'coin');
        $user_mycoin = get_user_mycoin($user_id);
        if ($user_mycoin < $coin_price) {
            echo json_encode(array('status' => '0', 'msg' => '<a target="_blank" href="' . get_user_page_url('coin') . '" class="btn btn-sm btn-danger ml-2">' . site_mycoin('name') . '余额不足，点击充值</a>'));exit;
        }

        $coin_price *= -1;
        if (!update_user_mycoin($user_id, $coin_price)) {
            echo json_encode(array('status' => '0', 'msg' => '余额消费异常'));exit;
        }

        //扣费成功 添加消费记录和订单状态
        $trade_no = '99-' . time(); // 时间戳和消费前余额
        $RiClass  = new RiClass;
        if (!$RiClass->send_order_trade_notify($order_data['order_num'], $trade_no)) {
            echo json_encode(array('status' => '0', 'msg' => '订单处理状态异常'));exit;
        }

        //余额支付成功 添加消费纪录
        if (true) {
            $order_type_text = get_order_type_text($order_data['order_type']);
            $_msg            = $order_type_text . '：' . $coin_price . '，￥' . $order_data['order_price'] . '，【' . $user_mycoin . '=>' . get_user_mycoin($user_id) . '】，订单编号：' . $order_data['order_num'];
            RiMsg::add_user_coin_log($user_id, $_msg);
        }

        //延迟处理 增强交互体验
        usleep(500000);

        //返回前段json数据
        echo json_encode(array('status' => '1', 'type' => '4', 'msg' => '支付成功，扣除 ' . $coin_price . ' ' . site_mycoin('name') . '，余额：' . get_user_mycoin($user_id), 'num' => $order_data['order_num']));exit;

    }

    //后台支付
    if ('admin_pay' == $_the_type) {
        echo json_encode(array('status' => '0', 'msg' => '仅限管理员操作'));exit;
    }

    // 支付宝官方
    if ($_the_type == 'alipay') {
        $config = _cao('alipay');
        if ($config['is_mapi']) {
            //电脑网站mapi网关支付 支付宝即时到账
            if (empty($config['pid']) || empty($config['md5Key'])) {
                echo json_encode(array('status' => '0', 'msg' => '请设置mapi网关配置参数'));exit;
            }
            if (wp_is_mobile() && $config['is_mobile']) {
                $pay_url = $RiPlusPay->alipay_mapi_wap_pay($order_data);
            } else {
                $pay_url = $RiPlusPay->alipay_mapi_pay($order_data);
            }
            echo json_encode(array('status' => '1', 'type' => '2', 'msg' => $pay_url, 'num' => $order_data['order_num']));exit;
        } elseif (wp_is_mobile() && $config['is_mobile']) {
            // APP H5支付
            if (empty($config['appid']) || empty($config['privateKey'])) {
                echo json_encode(array('status' => '0', 'msg' => '请设置支付宝接口配置参数'));exit;
            }
            $pay_url = $RiPlusPay->alipay_app_wap_pay($order_data);
            echo json_encode(array('status' => '1', 'type' => '2', 'msg' => $pay_url, 'qrcode' => '', 'num' => $order_data['order_num']));exit;
        } else {
            // APP当面付
            $pay_url = $RiPlusPay->alipay_app_qr_pay($order_data);
            echo json_encode(array('status' => '1', 'type' => '1', 'msg' => get_ajax_payqr_html('alipay', $order_data['order_price'], getQrcodeApi($pay_url)), 'num' => $order_data['order_num']));exit;
        }
    }
    //微信官方支付
    if ($_the_type == 'weixinpay') {
        $config = _cao('weixinpay');

        if (wp_is_mobile() && $config['is_mobile'] && !is_weixin_visit()) {
            # 手机端h5跳转支付
            $pay_url = $RiPlusPay->weixin_h5_pay($order_data);
            echo json_encode(array('status' => '1', 'type' => '2', 'msg' => $pay_url, 'num' => $order_data['order_num']));
            exit;
        } elseif (is_weixin_visit() && $config['is_jsapi']) {
            # 微信内jsapi支付
            if (_cao('sns_weixin')['sns_weixin_mod'] != 'mp') {
                echo json_encode(array('status' => '0', 'msg' => '微信内jsapi支付请在登录设置里设置公众号登录配置'));
                exit;
            }
            if (!empty($_SESSION['current_weixin_openid'])) {
                $order_data['openid'] = $_SESSION['current_weixin_openid'];
            }
            $pay_url = $RiPlusPay->weixin_jsapi_pay($order_data);
            echo json_encode(array('status' => '1', 'type' => '3', 'msg' => $pay_url, 'num' => $order_data['order_num']));
            exit;
        } else {
            #navtie扫码支付
            $pay_url = $RiPlusPay->weixin_qr_pay($order_data);
            echo json_encode(array('status' => '1', 'type' => '1', 'msg' => get_ajax_payqr_html('weixinpay', $order_data['order_price'], getQrcodeApi($pay_url)), 'num' => $order_data['order_num']));
            exit;
        }
    }
    //虎皮支付宝 hpj
    if ($_the_type == 'hupijiao_alipay') {
        $pay_url = $RiPlusPay->hpj_alipay_pay($order_data);

        if (wp_is_mobile()) {
            echo json_encode(array('status' => '1', 'type' => '2', 'msg' => $pay_url['url'], 'num' => $order_data['order_num']));
        } else {
            echo json_encode(array('status' => '1', 'type' => '1', 'msg' => get_ajax_payqr_html('alipay', $order_data['order_price'], $pay_url['url_qrcode']), 'num' => $order_data['order_num']));
        }
        exit;
    }
    //虎皮椒微信 hpj
    if ($_the_type == 'hupijiao_weixin') {
        $pay_url = $RiPlusPay->hpj_weixin_pay($order_data);
        if (wp_is_mobile()) {
            echo json_encode(array('status' => '1', 'type' => '2', 'msg' => $pay_url['url'], 'num' => $order_data['order_num']));
        } else {
            echo json_encode(array('status' => '1', 'type' => '1', 'msg' => get_ajax_payqr_html('weixinpay', $order_data['order_price'], $pay_url['url_qrcode']), 'num' => $order_data['order_num']));
        }
        exit;
    }
    //讯虎支付宝
    if ($_the_type == 'xunhupay_alipay') {
        $date = $RiPlusPay->new_xunhu_pay($order_data, 'alipay');
        if (!empty($date['h5'])) {
            echo json_encode(array('status' => '1', 'type' => '2', 'msg' => $date['h5'], 'num' => $order_data['order_num']));
        } elseif (!empty($date['qrcode'])) {
            echo json_encode(array('status' => '1', 'type' => '1', 'msg' => get_ajax_payqr_html('alipay', $order_data['order_price'], getQrcodeApi($date['qrcode'])), 'num' => $order_data['order_num']));
        }
        exit;
    }
    //讯虎h5微信
    if ($_the_type == 'xunhupay_weixin') {
        $date = $RiPlusPay->new_xunhu_pay($order_data, 'wechat');
        if (!empty($date['h5'])) {

            if (!is_weixin_visit()) {
                $url = $date['h5'] . '&redirect_url=' . urlencode($order_data['callback_url']);
            } else {
                $url = $date['h5'];
            }

            echo json_encode(array('status' => '1', 'type' => '2', 'msg' => $url, 'num' => $order_data['order_num']));
        } elseif (!empty($date['qrcode'])) {
            echo json_encode(array('status' => '1', 'type' => '1', 'msg' => get_ajax_payqr_html('weixinpay', $order_data['order_price'], getQrcodeApi($date['qrcode'])), 'num' => $order_data['order_num']));
        }
        exit;
    }

}




/**
 * 付款成功后信息处理佣金
 * @Author   Dadong2g
 * @DateTime 2021-06-01T21:22:06+0800
 * @param    [type]                   $order [description]
 * @return   [type]                          [description]
 */
function site_shop_pay_succ_callback($order) {

    if (empty($order) || empty($order->status)) {
        return false;
    }

    //网站未开启作者提成
    if ( !_cao('is_site_author_aff',1) ) {
        return false;
    }

    //非文章订单
    if ($order->order_type != 1 ) {
        return false;
    }
    
    #购买文章 发放作者佣金 
    $author_id       = (int) get_post($order->post_id)->post_author;
    $author_aff_info = _get_user_aff_info($author_id);

    //本人购买本人订单不计算佣金 
    if ($author_id > 1 && $order->user_id != $author_id && $author_aff_info['author_aff_ratio'] > 0) {
        // 提成金额
        $amount = sprintf('%0.2f', $author_aff_info['author_aff_ratio'] * $order->order_price);
        //添加提成记录
        return RiMsg::add_author_aff_log($author_id,$order->post_id,$amount,$order->order_price);
    }


}

add_action('rizhuti_v2_order_pay_success', 'site_shop_pay_succ_callback', 10, 1);

/**
 * 获取订单类型文本
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:58:03+0800
 * @param    [type]                   $order_type [description]
 * @return   [type]                               [description]
 */
function get_order_type_text($order_type) {
    global $ri_vip_options;
    //获取订单类型
    switch ($order_type) {
    case '1':
        //文章订单
        return esc_html__('文章订单', 'rizhuti_v2');
    case '2':
        return $ri_vip_options['31'];
    case '3':
        return $ri_vip_options['365'];
    case '4':
        return $ri_vip_options['3600'];
    case '99':
        return esc_html__('充值', 'rizhuti_v2');
    default:
        return esc_html__('其他', 'rizhuti_v2');
    }
}

/**
 * 获取会员类型角标徽章
 * @Author   Dadong2g
 * @DateTime 2021-01-27T10:27:54+0800
 * @param    [type]                   $user_id  [description]
 * @param    [type]                   $vip_type [description]
 * @return   [type]                             [description]
 */
function get_vip_badge($user_id = null, $vip_type = null) {
    if (empty($vip_type) && !empty($user_id)) {
        global $ri_vip_options;
        $vip_type = _get_user_vip_type($user_id);
    }
    $_icon  = '<i class="fa fa-diamond mr-1"></i>';
    $_badge = array(
        '0'    => '<span class="badge badge-secondary-lighten mx-2">' . $_icon . $ri_vip_options['0'] . '</span>',
        '31'   => '<span class="badge badge-success-lighten mx-2">' . $_icon . $ri_vip_options['31'] . '</span>',
        '365'  => '<span class="badge badge-info-lighten mx-2">' . $_icon . $ri_vip_options['365'] . '</span>',
        '3600' => '<span class="badge badge-warning-lighten mx-2">' . $_icon . $ri_vip_options['3600'] . '</span>',
    );
    return $_badge[$vip_type];
}

/**
 * 消息系统+文章提成系统
 * wp_wppay_msg_log
 * 当前仅开启工单支持，私信暂未完善
 */
class RiMsg {

    public static $msg_status = array('0' => '未读', '1' => '已读', '2' => '已回复');
    public static $msg_type   = array('0' => '系统通知', '1' => '工单消息', '2' => '私信消息', '3' => '消费记录', '4' => '文章提成');




    //获取数据
    public static function get_tickets($uid = null, $to_status = null, $page = 1, $limt = 20) {
        global $wpdb, $msg_table_name;
        $res = "SELECT * FROM {$msg_table_name} WHERE type=1";
        if ($uid) {
            $uid = (int) $uid;
            $res .= " AND uid=$uid";
        }
        if ($to_status !== null) {
            $to_status = (int) $to_status;
            $res .= " AND to_status=$to_status";
        }
        $res .= " ORDER BY time DESC";
        $res .= " LIMIT " . esc_sql(($page - 1) * $limt . ',' . $limt);
        return $wpdb->get_results($res);

    }

    //添加工单
    public static function add_tickets($uid = null, $msg = '') {
        global $wpdb, $msg_table_name;
        $params = array(
            'uid'       => $uid,
            'to_uid'    => 0,
            'type'      => 1,
            'msg'       => $msg,
            'to_status' => 0,
            'time'      => time(),
        );
        $ins = $wpdb->insert($msg_table_name, $params, array('%d', '%d', '%d', '%s', '%d', '%s'));
        return $ins ? true : false;

    }

    //回复消息
    public static function update_tickets($id, $to_msg, $to_status) {
        global $wpdb, $msg_table_name;
        $update = $wpdb->update($msg_table_name, array('to_time' => time(), 'to_msg' => wp_strip_all_tags($to_msg), 'to_status' => $to_status), array('id' => $id), array('%s', '%s', '%d'), array('%d'));
        return $update ? true : false;
    }

    //删除消息
    public static function del_tickets($id) {
        global $wpdb, $msg_table_name;
        return $wpdb->delete(
            "$msg_table_name",
            ['id' => $id],
            ['%d']
        );
    }

    //添加消费记录入库
    public static function add_user_coin_log($uid = null, $msg = '') {
        global $wpdb, $msg_table_name;
        $params = array(
            'uid'       => 0,
            'to_uid'    => $uid,
            'type'      => 3,
            'msg'       => $msg,
            'to_status' => 0,
            'time'      => time(),
        );
        $ins = $wpdb->insert($msg_table_name, $params, array('%d', '%d', '%d', '%s', '%d', '%s'));
        return $ins ? true : false;

    }


    //添加文章提成订单 author_aff_ratio add_author_aff_log
    public static function add_author_aff_log($author_id = 0, $post_id = 0,$aff_price = 0 ,$post_price = 0) {
        global $wpdb, $msg_table_name;
        $params = array(
            'uid'       => $author_id, //文章作者
            'to_uid'    => $post_id, //文章ID
            'type'      => 4,  //类型
            'msg'       => $aff_price, //信息 提成金额
            'to_msg'       => $post_price, //信息 用户实际付款金额
            'to_status' => 0, //结算状态
            'time'      => time(), //入库时间
        );
        $ins = $wpdb->insert($msg_table_name, $params, array('%d', '%d', '%d', '%s','%s', '%d', '%s'));
        return $ins ? true : false;

    }

}

/**
 * 卡密系统
 * wppay_cdk_name
 */
class RiCdk {

    public static $cdk_status = array('0' => '未使用', '1' => '已使用', '2' => '失效');
    public static $cdk_type   = array('0' => '无', '1' => '充值卡', '2' => '会员月卡', '3' => '会员年卡', '4' => '永久会员卡', '5' => '立减卡', '5' => '邀请码');

    //获取卡密信息
    public static function get_cdk($code, $ischeck = false) {
        global $wpdb, $wppay_cdk_name;
        $cdk_money = 0;
        $code      = sanitize_text_field(wp_unslash($code));
        $cdk       = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wppay_cdk_name WHERE code = %s ", $code));
        if (empty($cdk)) {
            return 0;
        }
        //是否检测卡密 返回卡密面值 元
        if ($ischeck && $cdk->status == 0 && $cdk->end_time > time() && $cdk->apply_time == 0) {

            return (float) $cdk->money;
        } else {
            return 0;
        }
        return $cdk;
    }

    //添加卡密
    public static function add_cdk($money, $day, $num) {
        global $wpdb, $wppay_cdk_name;
        $money = (float) $money;
        $day   = (int) $day;
        $num   = (int) $num;
        for ($i = 0; $i < $num; $i++) {
            // 字符串
            $length     = 12;
            $chars      = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $chars      = str_shuffle($chars);
            $length_num = $length < strlen($chars) - 1 ? $length : str_len($chars) - 1;
            $cdk_code   = substr($chars, 0, $length_num);

            $create_time = time();
            $end_time    = $create_time + $day * 24 * 60 * 60;
            $params      = array(
                'code'        => $cdk_code,
                'code_type'   => 1,
                'create_time' => $create_time,
                'end_time'    => $end_time,
                'apply_time'  => 0,
                'money'       => sprintf('%0.2f', $money), //保留两个百分点
                'status'      => 0,
            );
            $ins = $wpdb->insert($wppay_cdk_name, $params, array('%s', '%d', '%s', '%s', '%s', '%s', '%d'));
        }
        return $ins ? true : false;
    }

    //更新卡密
    public static function update_cdk($code) {
        global $wpdb, $wppay_cdk_name;
        $update = $wpdb->update(
            $wppay_cdk_name,
            array('apply_time' => time(), 'status' => 1),
            array('code' => $code),
            array('%s', '%d'),
            array('%s')
        );
        return $update ? true : false;
    }

}