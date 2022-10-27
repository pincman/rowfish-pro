<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.
/**
 * 改造下载挂件与网课系统整合
 */
if (apply_filters('is_site_shop', empty(_cao('is_site_shop', '1')))) {
    return;
}

//付费下载小工具
CSF::createWidget('pm_download', array(
    'title'       => esc_html__('PM: 下载组件', 'pincman'),
    'classname'   => 'rizhuti_v2-widget-shop-down',
    'description' => esc_html__('用于视频,资料,相册的下载组件', 'pincman'),
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
            'title' => esc_html__('小工具底部提示', 'picman'),
            'default' => __('如下载遇到问题可联系站长或<a href="/user/msg">提交工单</a>处理', 'picman'),
        ),
    ),
));


/**
 * 付费下载小工具
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:15:14+0800
 * @param    [type]                   $args     [description]
 * @param    [type]                   $instance [description]
 * @return   [type]                             [description]
 */
if (!function_exists('pm_download')) {
    function pm_download($args, $instance)
    {
        $uinfo = pm_shop_post_info();
        // 付费资源信息
        if (!is_single() || !in_array($uinfo['type'], [4, 5, 6]) || apply_filters('is_site_shop', empty(_cao('is_site_shop', '1')))) {
            return;
        }

        $instance = array_merge(array(
            'pay_btn_name' => '立即购买',
            'pay_login_btn_name' => '登录账户',
            'vip_btn_name' => '订阅本站',
            'desc' => '如下载遇到问题可联系站长或<a href="/user/msg" target="_blank">提交工单</a>处理'
        ), $instance);
        $click_nonce = wp_create_nonce('rizhuti_click_' . $uinfo['post_id']);
        echo $args['before_widget'];
        $vip_btn_desc = '该资源需购买或订阅后下载';
        if ($uinfo['course']) $vip_btn_desc = '本教程所有资料需购买或订阅后下载';
        if ($uinfo['vip']) {
            $vip_btn_desc = '该资源需订阅后下载';
            if ($uinfo['course']) $vip_btn_desc = '本教程所有资料需订阅后下载';
        }
        // 是否免费资源
        if ($uinfo['free'] || $uinfo['vip_user']) {
            $price_text = $uinfo['course'] ? __('<small>教程资料</small>', 'rizhuti-v2') : __('<small>资源下载</small>', 'rizhuti-v2');
        } elseif ($uinfo['paid']) {
            $price_text = $uinfo['course'] ? __('<small>已购教程</small>', 'rizhuti-v2') : __('<small>已购资源</small>', 'rizhuti-v2');
        } else {
            if ($uinfo['only_vip']) {
                $price_text = $uinfo['course'] ? __('<small>订阅者专属教程</small>', 'rizhuti-v2') : __('<small>订阅专属资源</small>', 'rizhuti-v2');
            } else {
                if (site_mycoin('is')) {
                    $price_text = '<span>' . convert_site_mycoin($uinfo['price'], 'coin') . '</span> <small><i class="' . site_mycoin('icon') . '"></i> ' . site_mycoin('name') . '或订阅</small>';
                } else {
                    $price_text = '<small>¥' . $uinfo['price'] . '或订阅</small>';
                }
            }
        }

        //显示价格信息
        echo '<div class="price"><h3>' . $price_text . '</h3></div>';

        // 下载数据按钮处理
        $post_down_info = $uinfo['download'];
        $post_down_btns = '';
        $can_downs = 0;
        foreach ($post_down_info as $key => $item) {
            $down_ids = urlencode(base64_encode($uinfo['post_id'] . '-' . $key . '-' . $click_nonce));
            $down_link = get_goto_url('down', $down_ids);
            $is_single_free = $item['free'] == '1';
            if (empty($item['url'])) $down_link = "javascript:void(0);";
            $post_down_btns .= $key == 0 ? '<div class="btn-group btn-block" role="group">' : '<div class="mt-3 btn-group btn-block" role="group">';
            if ($item['online'] != '1') {
                $post_down_btns .= '<button class="btn btn-flex btn-dark btn-sm disabled" disabled><i class="far fa-stop-circle"></i><div>' . $item['name'] . '[未上线]</div></button>';
            } elseif ($uinfo['can'] || $is_single_free) {
                $can_downs++;
                $post_down_btns .= '<a  ';
                if (!empty($item['url'])) {
                    $post_down_btns .= 'target="_blank" ';
                }
                $post_down_btns .= 'href="' . $down_link . '" class="btn btn-flex btn-outline-danger btn-sm"><i class="fas fa-download"></i> <div>' . $item['name'] . '</div></a>';
                if (!empty($item['pwd'])) {

                    wp_enqueue_script('clipboard');

                    $post_down_btns .= '<button type="button" class="go-copy btn btn-sm btn-danger" title="' . esc_html__('点击复制密码', 'rizhuti-v2') . '" data-clipboard-text="' . $item['pwd'] . '">' . esc_html__('密码：', 'rizhuti-v2') . '[' . $item['pwd'] . ']</button>';
                }
            } else {
                $btn_name = $uinfo['only_vip'] ? '需订阅本站后下载' : '订阅或购买后下载';
                $post_down_btns .= '<button class="btn btn-dark btn-sm btn-flex" style="background-color: #000; opacity: 1;" disabled><i class="fas fa-download"></i> <div> ' .
                    $item['name'] . '<span style="color: #dededf; font-size: 12px; margin-left: 10px;">[' . $btn_name .
                    ']</span></div></button>';
            }
            $post_down_btns .= '</div>';
        }

        if ($can_downs > 0) {
            $vip_btn_desc = $uinfo['course'] ? '本教程部分资料需购买或订阅后下载' : '该资源需购买或订阅后下载';
            if ($uinfo['only_vip']) {
                $vip_btn_desc = $uinfo['course'] ? '本教程部分资料需订阅后下载' : '部分资源需订阅后下载';
            }
        }
        if ($uinfo['free']) {
            $vip_btn_desc = $uinfo['course'] ? '本教程为免费教程<br />在此处下载本教程的课件,文档,视频等' : '本资源本免费资源,可以随意下载';
        }
        if ($uinfo['vip_user']) {
            $vip_btn_desc = $uinfo['course'] ? '你好! 订阅者<br />在此处下载本教程的课件,文档,视频等' : '你好! 订阅者<br />你可以观看,阅读,下载本站所有教程和资源';
        }
        if ($uinfo['paid']) {
            $vip_btn_desc = $uinfo['course'] ? '本教程已购<br />在此处下载本教程的课件,文档,视频等' : '本资源已购买,可以随意下载';
        }

        $user_btns = '<p style="margin-bottom: 5px;text-align: center;"><small class="my-2 d-block">' . $vip_btn_desc . '</small>';
        if (!$uinfo['can']) {
            $user_btns .= '</p><div class="download_price_vip_btns">';;
            if (!$uinfo['user_id']) {
                $user_btns .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-block btn-sm btn-success" target="_blank" rel="nofollow noopener noreferrer style="width: 100%;">' . $instance['pay_login_btn_name'] . '</a>';
            } else {
                if ($uinfo['price'] > 0) {
                    $user_btns .= '<button type="button" class="btn btn-block btn-sm btn-primary click-pay-post" data-postid="' . $uinfo['post_id'] . '" data-nonce="' . $click_nonce . '" data-price="' . $uinfo['price'] . '"';
                    $user_btns .= !$uinfo['vip'] ? ' style="width: 100%;"' : ' style="width: 45%;"';
                    $user_btns .= '>' . $instance['pay_btn_name'] . '</button>';
                }
                if ($uinfo['vip']) {
                    $user_btns .= '<a href="' . get_user_page_url('vip') . '" class="btn btn-block btn-sm btn-warning" target="_blank" rel="nofollow noopener noreferrer"';
                    $user_btns .= $uinfo['price'] <= 0 ? ' style="width: 100%;"' : ' style="width: 45%;"';
                    $user_btns .= '>' . $instance['vip_btn_name'] . '</a>';
                }
            }
            $user_btns .= '</div>';
        } else {
            $user_btns .= '</p><div class="price-widget-body">';
        }
        echo $user_btns;
        if (count($post_down_info) <= 0) {
            echo $uinfo['course'] ?  '<div class="download-none-btns"><span>[暂时没有教程资料可下载]</span></div>' : '<div class="download-none-btns"><span>[暂时没有资源可下载]</span></div>';
        } else {
            if ($can_downs > 0) {
                echo $post_down_btns;
            } else {
                echo $uinfo['course'] ? '<div class="download-none-btns"><span>[您没有权限下载本教程资料]</span></div>' : '<div class="download-none-btns"><span>[您没有权限下载本资源]</span></div>';
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