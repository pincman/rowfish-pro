<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:33:46 +0800
 * @Path           : /wp-content/themes/rowfish/course/widgets/course_download.php
 * @Description    : 课程页侧边栏的资料下载小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

//付费下载小工具
CSF::createWidget('rf_course_download_widget', array(
    'title'       => esc_html__('RF: 课程资料下载', 'rowfish'),
    'classname'   => 'rizhuti_v2-widget-shop-down',
    'description' => esc_html__('用于课程页面侧边栏的下载组件', 'rowfish'),
    'fields'      => array(

        array(
            'id'         => 'pay_btn_name',
            'type'       => 'text',
            'title'      => '购买按钮名称',
            'dsec'       => '',
            'default'    => '立即购买',
        ),
        array(
            'id'         => 'pay_login_btn_name',
            'type'       => 'text',
            'title'      => '登录提示按钮名称',
            'dsec'       => '',
            'default'    => '登录账户',
        ),

        array(
            'id'         => 'vip_btn_name',
            'type'       => 'text',
            'title'      => '会员开通按钮名称',
            'dsec'       => '',
            'default'    => '订阅本站',
        ),

        array(
            'id'    => 'desc',
            'type'  => 'textarea',
            'sanitize' => false,
            'title' => esc_html__('小工具底部提示', 'rowfish'),
            'default' => __('如下载遇到问题可联系站长或<a href="/user/msg">提交工单</a>处理', 'picman'),
        ),
    ),
));

if (!function_exists('rf_course_download_widget')) {
    function rf_course_download_widget($args, $instance)
    {
        $info = rf_get_post_info();
        if (!$info['is_course']) {
            return;
        }
        $instance = array_merge(array(
            'pay_btn_name' => '立即购买',
            'pay_login_btn_name' => '登录账户',
            'vip_btn_name' => '订阅本站',
            'desc' => '如下载遇到问题可联系站长或<a href="/user/msg" target="_blank">提交工单</a>处理'
        ), $instance);
        $click_nonce = wp_create_nonce('rizhuti_click_' . $info['post_id']);
        echo $args['before_widget'];
        // 是否免费资源
        $price_text = __('<small>课程资料</small>', 'rizhuti-v2');
        if (!$info['has_permission']) {
            if ($info['vip_only']) {
                $price_text = __('<small>' . rf_post_vip_label() . '专属课程</small>', 'rizhuti-v2');
            } else {
                if (site_mycoin('is')) {
                    $price_text = '<span>' . convert_site_mycoin($info['price'], 'coin') . '</span> <small><i class="' . site_mycoin('icon') . '"></i> ' . site_mycoin('name') . '或订阅</small>';
                } else {
                    $price_text = '<small>¥' . $info['price'] . '或订阅</small>';
                }
            }
        } elseif ($info['user']['id'] && $info['user']['auth_type'] <= 0 && $info['is_paid']) {
            $price_text = __('<small>已购课程</small>', 'rizhuti-v2');
        }
        //显示价格信息
        echo '<div class="price"><h3>' . $price_text . '</h3></div>';

        // 下载数据按钮处理
        $post_down_info = $info['course']['download'];
        $post_down_btns = '';
        $can_downs = 0;
        foreach ($post_down_info as $key => $item) {
            $down_ids = urlencode(base64_encode($info['post_id'] . '-' . $key . '-' . $click_nonce));
            $down_link = get_goto_url('down', $down_ids);
            $is_single_free = $item['free'] == '1';
            if (empty($item['url'])) $down_link = "javascript:void(0);";
            $post_down_btns .= $key == 0 ? '<div class="btn-group btn-block" role="group">' : '<div class="mt-3 btn-group btn-block" role="group">';
            if ($item['online'] != '1') {
                $post_down_btns .= '<button class="btn btn-flex btn-dark btn-sm disabled" disabled><i class="far fa-stop-circle"></i><div>' . $item['name'] . '[未上线]</div></button>';
            } elseif ($info['has_permission'] || $is_single_free) {
                $can_downs++;
                $post_down_btns .= '<a  ';
                $post_down_btns .= !empty($item['url']) ? 'target="_blank" ' : 'target="_self" ';
                $post_down_btns .= 'href="' . $down_link . '" class="btn btn-flex btn-outline-danger btn-sm"><i class="fas fa-download"></i> <div>' . $item['name'] . '</div></a>';
                if (!empty($item['pwd'])) {

                    wp_enqueue_script('clipboard');

                    $post_down_btns .= '<button type="button" class="go-copy btn btn-sm btn-danger" title="密码: [' . $item['pwd'] . ']" data-clipboard-text="' . $item['pwd'] . '">' . esc_html__('点击复制密码', 'rizhuti-v2') .  '</button>';
                }
            } else {
                $btn_name = $info['vip_only'] ? '需订阅本站后下载' : '订阅或购买后下载';
                $post_down_btns .= '<button class="btn btn-dark btn-sm btn-flex" style="background-color: #000; opacity: 1;" disabled><i class="fas fa-download"></i> <div> ' .
                    $item['name'] . '<span style="color: #dededf; font-size: 12px; margin-left: 10px;">[' . $btn_name .
                    ']</span></div></button>';
            }
            $post_down_btns .= '</div>';
        }
        $vip_btn_desc = '在此处下载本教程的课件,文档,视频等';
        if (!$info['has_permission']) {
            if ($can_downs > 0) {
                $vip_btn_desc = '本课程资料需购买或升级为' . rf_post_vip_label() . '后下载';
                if ($info['vip_only']) {
                    $vip_btn_desc = '本课程部分资料需升级为' . rf_post_vip_label() . '后下载';
                }
            } else {
                $vip_btn_desc = '本课程所有资料需购买或升级为' . rf_post_vip_label() . '后下载';
                if ($info['vip_only']) {
                    $vip_btn_desc = '本课程所有资料需升级为' . rf_post_vip_label() . '后下载';
                }
            }
        }
        echo '<p style="margin-bottom: 5px;text-align: center;"><small class="my-2 d-block">' . $vip_btn_desc . '</small></p><div class="price-widget-body">';
        if (!$info['has_permission']) {
            $user_btns = '<div class="download_price_vip_btns">';
            if (!$info['user']['id']) {
                $user_btns .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-block btn-sm btn-success" target="_blank" rel="nofollow noopener noreferrer style="width: 100%;">' . $instance['pay_login_btn_name'] . '</a>';
            } else {
                if ($info['price'] > 0) {
                    $user_btns .= '<button type="button" class="btn btn-block btn-sm btn-primary click-pay-post" data-postid="' . $info['post_id'] . '" data-nonce="' . $click_nonce . '" data-price="' . $info['price'] . '"';
                    $user_btns .= $info['auth_type'] <= 0 ? ' style="width: 100%;"' : ' style="width: 45%;"';
                    $user_btns .= '>' . $instance['pay_btn_name'] . '</button>';
                }
                if ($info['auth_type'] > 0) {
                    $user_btns .= '<a href="' . get_user_page_url('vip') . '" class="btn btn-block btn-sm btn-warning" target="_blank" rel="nofollow noopener noreferrer"';
                    $user_btns .= $info['price'] <= 0 ? ' style="width: 100%;"' : ' style="width: 45%;"';
                    $user_btns .= '>' . $instance['vip_btn_name'] . '</a>';
                }
            }
            $user_btns .= '</div>';
            echo $user_btns;
        }
        if (count($post_down_info) <= 0) {
            echo  '<div class="download-none-btns"><span>[暂时没有课程资料可下载]</span></div>';
        } else {
            if ($can_downs > 0) {
                echo $post_down_btns;
            } else {
                echo '<div class="download-none-btns"><span>[您没有权限下载本课程资料]</span></div>';
            }
        }

        //其他信息
        if (!empty($uinfo['download_info'])) {
            echo '<div class="down-info">';
            echo '<h5>' . esc_html__('其他信息', 'rizhuti-v2') . '</h5>';
            echo '<ul class="infos">';
            // if (!empty($shop_info['wppay_demourl'])) {
            //     echo '<li><p class="data-label">' . esc_html__('链接', 'rizhuti-v2') . '</p><p class="info"><a target="_blank" rel="nofollow noopener noreferrer" href="' . $shop_info['wppay_demourl'] . '" class="badge badge-secondary-lighten"><i class="fas fa-link"></i> ' . esc_html__('点击查看', 'rizhuti-v2') . '</a></p></li>';
            // }
            foreach ($uinfo['download_info'] as $key => $value) {
                echo '<li><p class="data-label">' . $value['title'] . '</p><p class="info">' . $value['desc'] . '</p></li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        echo '</div>';
        if (!empty($instance['desc'])) {
            echo '<div class="mt-2 down-help small text-muted">' . $instance['desc'] . '</div>';
        }

        echo $args['after_widget'];
    }
}


// Shop Widget Options