<?php

/**
 * Exit if accessed directly.
 * 新增:
 * pm_shop_post_info函数替代get_post_shop_info以便更便捷地使用自定义数据
 */
defined('ABSPATH') || exit;

function pm_shop_post_info($post_ID = null, $meta_key = null)
{
    global $current_user, $ri_vip_options;
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    $post_id = $post_ID;
    $user_id = $current_user->ID;
    $RiClass = new RiClass($post_ID, $user_id);
    $IS_PAID = $RiClass->is_pay_post();
    $vip_type = (int) _get_user_vip_type($user_id);
    $vip_name = $ri_vip_options[_get_user_vip_type($user_id)];
    $wppay_price =  get_post_meta($post_ID, 'wppay_price', 1);
    $wppay_vip_auth = get_post_meta($post_ID, 'wppay_vip_auth', 1);
    // 用户是否为vip用户
    $vip_user =  $vip_type > 0 || $IS_PAID == 3;
    // 文章价格
    $price = empty($wppay_price) ? 0 : (float)$wppay_price;
    // 文章是否免费
    $free = (empty($wppay_vip_auth) || (int)$wppay_vip_auth == 0) || $IS_PAID == 4;
    // 文章是否已购买
    $paid = in_array($IS_PAID, [1, 2]);
    // 是否为教程类型
    $course = get_post_meta($post_ID, 'wppay_type', 1) == '5';
    $vip = !empty($wppay_vip_auth) && (int)$wppay_vip_auth > 0;
    // 是否vip专属文章
    $only_vip = $price <= 0 && $vip;
    // 是否可以阅读
    $can = (_cao('free_onlogin_down') == '1' && $free) || (!_cao('free_onlogin_down') && $user_id && $free) || $paid || $vip_user;
    // 半高背景图
    $hero_image =  get_post_meta($post_ID, 'hero_image', 1);
    // 文章类型
    $type =  get_post_meta($post_ID, 'wppay_type', 1);
    // 下载资源
    $download_meta =  get_post_meta($post_ID, 'wppay_down', 1);
    $download = !empty($download_meta) && is_array($download_meta) ? $download_meta : [];
    // 下载资源的其它信息
    $download_info = get_post_meta($post_ID, 'wppay_info', 1);
    $collection = compact('post_id', 'user_id', 'vip_user', 'vip_type', 'vip_name', 'vip', 'only_vip', 'price', 'free', 'paid',  'can', 'course', 'type', 'download', 'download_info', 'hero_image');
    if ($meta_key) {
        return key_exists($meta_key, $collection) ? $collection[$meta_key] : null;
    }
    return $collection;
}

function pm_get_post_shop_info($post_ID = null, $meta_key = null)
{
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    if ($meta_key) {
        $meta = get_post_meta($post_ID, $meta_key, 1);
    } else {
        $arr  = array('hero_image', 'wppay_type', 'wppay_vip_auth', 'wppay_down', 'wppay_demourl', 'wppay_info', 'wppay_price');
        $meta = array();
        foreach ($arr as $_key) {
            $meta[$_key] = get_post_meta($post_ID, $_key, 1);
        }
    }
    return $meta;
}
function pm_course_status_icon($post_ID = null)
{
    $uinfo = pm_shop_post_info($post_ID);
    if ($uinfo['course']) {
        $course_status = get_post_meta($uinfo['post_id'], 'wppay_course_status', 1);
        switch ($course_status) {
            case '0':
                return "<span class='meta-vip-price bg-secondary'>策划中</span>";
                break;
            case '1':
                return "<span class='meta-vip-price bg-success'>待发布</span>";
                break;
            case '2':
                return "<span class='meta-vip-price bg-danger'>更新中</span>";
                break;
            case '3':
                return "<span class='meta-vip-price bg-primary'>已完结</span>";
                break;
            default:
                return "<span class='meta-vip-price bg-secondary'>策划中</span>";
                break;
        }
    }
}
/**
 * 获取文章徽标
 */
function pm_post_icons($post_ID = null)
{
    $uinfo = pm_shop_post_info($post_ID);
    $content = '';
    if ($uinfo['course']) {
        $course_level = get_post_meta($uinfo['post_id'], 'wppay_course_level', 1);
        switch ($course_level) {
            case '0':
                $content .= "<span class='meta-vip-price bg-success'><i class='fab  fa-envira'></i></span>";
                break;
            case '1':
                $content .= "<span class='meta-vip-price bg-danger'><i class='fab fa-gripfire'></i></span>";
                break;
            case '2':
                $content .= "<span class='meta-vip-price bg-warning'><i class='fas fa-feather-alt'></i></span>";
                break;
            case '3':
                $content .= "<span class='meta-vip-price bg-primary'><i class='fas fa-feather-alt'></i></span>";
                break;
            default:
                break;
        }
    }
    if ($uinfo['vip_user']) return $content;
    if ($uinfo['free']) {
        return  $content . '<span class="meta-vip-price bg-primary"><i class="fas fa-hand-holding-water"></i>免费</span>';
    }
    if ($uinfo['only_vip']) {
        return $content . '<span class="meta-vip-price bg-info"><i class="fas fa-code"></i>订阅专享</span>';
    }
    if ($uinfo['paid']) return $content . '<span class="meta-vip-price bg-danger"><i class="fab fa-opencart"></i>已购</span>';
    $bg = 'bg-success';
    $icon = '<i class="fas fa-coins mr-1"></i>';
    if (site_mycoin('is')) {
        $price = convert_site_mycoin($uinfo['price'], 'coin');
        $icon = '<i class="' . site_mycoin('icon') . ' mr-1"></i>';
    }
    return $content . '<span class="meta-vip-price ' . $bg . '">' . $icon . $uinfo['price'] . '</span>';
}


/*
 * 获取价格角标
 * @Author   Dadong2g
 * @DateTime 2021-03-31T10:41:48+0800
 * @param    [type]                   $post_ID [description]
 * @return   [type]                            [description]
 */
function pm_get_post_meta_vip_price($post_ID = null)
{
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }

    $shop_info = pm_get_post_shop_info($post_ID);
    $is_course_cat = _get_post_shop_type($post_ID) === '5';
    $course_filter = get_term_meta(get_queried_object_id(), 'enabled_course_filter', true);
    $is_course_filter = $course_filter === '' || $course_filter === '1';
    //是否VIP专属资源
    $is_vip_post = !empty($shop_info['wppay_vip_auth']) && $shop_info['wppay_vip_auth'] !== '0';
    $price = empty($shop_info['wppay_price']) ? 0 : (float)$shop_info['wppay_price'];
    $is_free = $price <= 0;
    $content = '';
    if ($is_course_filter && $is_course_cat) {
        $course_status = get_post_meta($post_ID, 'wppay_course_status', 1);

        switch ($course_status) {
            case '0':
                $content .= "<span class='meta-vip-price bg-secondary'><i class='fas fa-feather-alt'></i></span>";
                break;
            case '1':
                $content .= "<span class='meta-vip-price bg-success'><i class='fab  fa-envira'></i></span>";
                break;
            case '2':
                $content .= "<span class='meta-vip-price bg-danger'><i class='fab fa-gripfire'></i></span>";
                break;
            case '3':
                $content .= "<span class='meta-vip-price bg-primary'><i class='fas fa-hand-holding-water'></i></span>";
                break;
            default:
                $content .= "<span class='meta-vip-price bg-secondary'><i class='fas fa-feather-alt'></i></span>";
                break;
        }
    }
    if ($is_free) {
        if (!$is_vip_post) return $content;
        return $content . '<span class="meta-vip-price bg-info"><i class="fas fa-code"></i>订阅专享</span>';
    }
    $content .= '<span class="meta-vip-price bg-info"><i class="fas fa-code"></i>订阅专享</span>';
    $bg = 'bg-success';
    $icon = '<i class="fas fa-coins mr-1"></i>';
    if (site_mycoin('is')) {
        $price = convert_site_mycoin($price, 'coin');
        $icon = '<i class="' . site_mycoin('icon') . ' mr-1"></i>';
    }
    return $content . '<span class="meta-vip-price ' . $bg . '">' . $icon . $price . '</span>';
}


function pm_get_vip_badge($user_id = null, $vip_type = null)
{
    if (empty($vip_type) && !empty($user_id)) {
        global $ri_vip_options;
        $vip_type = _get_user_vip_type($user_id);
    }
    $_icon = '<i class="fa fa-code mr-1"></i>';
    $_badge = array(
        '0' => '<span class="badge badge-secondary-lighten mx-2">' . $_icon . $ri_vip_options['0'] . '</span>',
        // '31'   => '<span class="badge badge-success-lighten mx-2">' . $_icon . $ri_vip_options['31'] . '</span>',
        '365'  => '<span class="badge badge-info-lighten mx-2">' . $_icon . $ri_vip_options['365'] . '</span>',
        '3600' => '<span class="badge badge-warning-lighten mx-2">' . $_icon . $ri_vip_options['3600'] . '</span>',
    );
    return $_badge[$vip_type];
}

function pm_oss_post_info($post_ID = null, $meta_key = null)
{
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    if ($meta_key) {
        $meta = get_post_meta($post_ID, $meta_key, 1);
    } else {
        $arr  = array('wppay_type', 'wppay_oss_name', 'wppay_oss_website', 'wppay_oss_git', 'wppay_oss_demourl', 'wppay_oss_agreement', 'wppay_oss_info');
        $meta = array();
        foreach ($arr as $_key) {
            $meta[$_key] = get_post_meta($post_ID, $_key, 1);
        }
    }
    return $meta;
}
