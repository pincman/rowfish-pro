<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

$prefix = '_rizhuti_v2_shortcodes';

if (false && !is_close_site_shop()) {
    CSF::createShortcoder($prefix, array(
        'button_title'   => '添加付费隐藏内容',
        'select_title'   => '选择添加的内容块',
        'insert_title'   => '插入到文章',
        'show_in_editor' => true,
        'gutenberg'      => array(
            'title'       => 'Ri简码组件',
            'description' => 'Ri简码组件',
            'icon'        => 'screenoptions',
            'category'    => 'widgets',
            'keywords'    => array('shortcode', 'csf', 'insert'),
            'placeholder' => '在此处编写Ri简码...',
        ),
    ));

    CSF::createSection($prefix, array(
        'title'     => '隐藏部分付费内容[rihide]',
        'view'      => 'normal',
        'shortcode' => 'rihide',
        'fields'    => array(

            array(
                'id'    => 'content',
                'type'  => 'wp_editor',
                'title' => '',
                'desc'  => '[rihide]隐藏部分付费内容[/rihide] <br/> 注意：添加隐藏内容后，因为公用价格和折扣字段，所有资源类型优先为付费查看内容模式，侧边栏下载资源小工具将不显示',
            ),

        ),
    ));
}

/**
 * 付费查看部分内容
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:14:41+0800
 * @param    [type]                   $atts    [description]
 * @param    string                   $content [description]
 * @return   [type]                            [description]
 */
function rizhuti_v2_hide_shortcode($atts, $content = '')
{
    // 付费资源信息
    $shop_info = get_post_shop_info();
    if (!in_array($shop_info['wppay_type'], [1, 2]) || is_close_site_shop()) {
        return;
    }

    global $post, $current_user, $ri_vip_options;
    $user_id = $current_user->ID; //用户ID
    $post_id = $post->ID; //文章ID
    $click_nonce = wp_create_nonce('rizhuti_click_' . $post_id);
    //是否购买
    $RiClass = new RiClass($post_id, $user_id);
    $IS_PAID = $RiClass->is_pay_post();
    //是否VIP资源 普通用户不能购买
    $is_vip_post = !empty($shop_info['wppay_vip_auth']) && empty($shop_info['wppay_price']);
    $is_nologin_free = $IS_PAID == 4 && empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay');
    $is_nologin_pay = empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay');

    if (is_close_site_shop() && $IS_PAID != 4) {
        return '';
    }

    // 显示原始价格 END


    if ($IS_PAID > 0) {
        $_content = '<div class="ripay-content card mb-4">';
        $_content .= '<div class="card-body">';
        $_content .= '<h5 class="card-title text-center text-muted"><i class="fa fa-unlock-alt mr-2"></i>' . esc_html__('隐藏内容详情', 'rizhuti-v2') . '</h5>';
        if ($is_nologin_free) {
            $_content .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-outline-primary btn-sm my-2"><i class="fa fa-user"></i> ' . esc_html__('登录后免费查看', 'rizhuti-v2') . '</a>';
        } else {
            $_content .= do_shortcode($content);
        }
        $_content .= '</div></div>';
    } else {
        $_content = '<div class="ripay-content card text-center mb-4">';
        $_content .= '<div class="card-body">';
        $_content .= '<h5 class="card-title text-muted"><i class="fa fa-lock mr-2"></i>' . esc_html__('此处内容需要权限查看', 'rizhuti-v2') . '</h5>';
        $_content .= '<p class="card-text mb-0 py-4">';

        //是否VIP专属 
        if ($is_vip_post) {
            $_content .= '<span class="">' . esc_html__('此处内容为VIP专属', 'rizhuti-v2') . '</span>';
        } else {
            $_content .= esc_html__('您还没有获得查看权限', 'rizhuti-v2');
        }

        if (!empty($shop_info['wppay_vip_auth'])) {
            $_content .= '<span class="ml-2"></span>' . get_post_vip_auth_badge($shop_info['wppay_vip_auth']) . '可免费查看';
        }

        $_content .= '</p>';

        if (!$is_vip_post) {
            if ($is_nologin_pay) {
                $_content .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-outline-primary btn-sm mx-2 mt-2"><i class="fa fa-user"></i> ' . esc_html__('登录后购买', 'rizhuti-v2') . '</a>';
            } else {
                if (site_mycoin('is')) {
                    $_content .= '<button type="button" class="click-pay-post btn btn-outline-primary btn-sm mx-2 mt-2" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $post_id) . '" data-price="' . $shop_info['wppay_price'] . '">支付 ' . convert_site_mycoin($shop_info['wppay_price'], 'coin') . ' <i class="' . site_mycoin('icon') . '"></i> ' . site_mycoin('name') . '查看</button>';
                } else {
                    $_content .= '<button type="button" class="click-pay-post btn btn-outline-primary btn-sm mx-2 mt-2" data-postid="' . $post_id . '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $post_id) . '" data-price="' . $shop_info['wppay_price'] . '">支付 ￥' . $shop_info['wppay_price'] . ' 查看</button>';
                }
            }
        }

        if (!empty($shop_info['wppay_vip_auth'])) {
            $_content .= '<a href="' . get_user_page_url('vip') . '" class="btn btn-outline-warning btn-sm mx-2 mt-2"><i class="fa fa-diamond"></i> 升级VIP免费查看</a>';
        }

        $_content .= '</div></div>';
    }


    return do_shortcode($_content);
}
add_shortcode('rihide', 'rizhuti_v2_hide_shortcode');
