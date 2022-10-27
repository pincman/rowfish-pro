<?php if (!defined('ABSPATH')) {die;} // Cannot access directly.


if (is_close_site_shop()) {
    return;
}

//付费下载小工具
CSF::createWidget('rizhuti_v2_shop_down', array(
    'title'       => esc_html__('RI-文章侧边栏 : 付费下载组件', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-shop-down',
    'description' => esc_html__('必备，付费下载组件', 'rizhuti-v2'),
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
            'title'      => '登录购买按钮名称',
            'dsec'       => '',
            'default'    => '登录购买',
        ),

        array(
            'id'         => 'vip_btn_name',
            'type'       => 'text',
            'title'      => '会员开通按钮名称',
            'dsec'       => '',
            'default'    => '升级VIP免费下载',
        ),
        array(
            'id'         => 'vip_btn_desc',
            'type'       => 'text',
            'title'      => '会员权限提示文字',
            'dsec'       => '',
            'default'    => '以下会员可免费获取本资源',
        ),

        array(
            'id'    => 'desc',
            'type'  => 'textarea',
            'sanitize' => false,
            'title' => esc_html__('小工具底部提示', 'rizhuti-v2'),
            'default' => __('下载遇到问题？可联系客服或提交工单', 'rizhuti-v2'),
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
if (!function_exists('rizhuti_v2_shop_down')) {
    function rizhuti_v2_shop_down($args, $instance) {
    	// 付费资源信息
    	$shop_info = get_post_shop_info();
    	if (!is_single() || !in_array($shop_info['wppay_type'],[3,4]) || is_close_site_shop()) {
    		return;
    	}
    	
    	$instance = array_merge( array( 
            'pay_btn_name' => '立即购买',
            'pay_login_btn_name' => '登录购买',
            'vip_btn_name' => '升级VIP免费下载',
            'vip_btn_desc' => '以下会员可免费获取本资源',
            'desc' => '下载遇到问题？可联系客服或提交工单',
        ), $instance);
    	
    	global $post,$current_user,$ri_vip_options;
    	$user_id = $current_user->ID; //用户ID
    	$post_id = $post->ID; //文章ID
    	$click_nonce = wp_create_nonce('rizhuti_click_' . $post_id);
    	//是否购买
    	$RiClass = new RiClass($post_id,$user_id);
        $IS_PAID = $RiClass->is_pay_post();
        echo $args['before_widget'];
        //是否VIP资源 普通用户不能购买
        $is_vip_post = !empty($shop_info['wppay_vip_auth']) && empty($shop_info['wppay_price']);
    	// 是否免费资源
        $price_text = (float)$shop_info['wppay_price'];
        switch ($IS_PAID) {
            case 4:
                 $price_text = __('<small>免费下载</small>','rizhuti-v2');
                 break;
            case 1:
                 $price_text = __('<small>已购买</small>','rizhuti-v2');
                 break;
            case 2:
                 $price_text = __('<small>已购买</small>','rizhuti-v2');
                 break;
            case 3:
                 $price_text = __('<small>VIP免费下载</small>','rizhuti-v2');
                 break;
            default:
                if ($is_vip_post) {
                    $price_text = __('<small>VIP专属资源</small>','rizhuti-v2');
                }else{

                    if (site_mycoin('is')) {
                        $price_text = '<span>'.convert_site_mycoin($shop_info['wppay_price'],'coin').'</span> <small><i class="'.site_mycoin('icon').'"></i> '.site_mycoin('name').'</small>';
                    }else{
                        $price_text = '<small>￥</small> <span>'.$price_text.'</span>';
                    }
                    
                }
                break;
        }

        //显示价格信息
        echo '<div class="price"><h3>'.$price_text.'</h3></div>';
        
        
    	// 显示原始价格 END
    	$_badge=array(
			'0'    => '',
		    '31'   => '<b class="badge badge-success-lighten mr-2"><i class="fa fa-diamond"></i> '.$ri_vip_options['31'].'</b>',
		    '365'  => '<b class="badge badge-info-lighten mr-2"><i class="fa fa-diamond"></i> '.$ri_vip_options['365'].'</b>',
		    '3600' => '<b class="badge badge-warning-lighten mr-2"><i class="fa fa-diamond"></i> '.$ri_vip_options['3600'].'</b>',
		);
		$_vip_auth = array(
            '0' => __('会员暂无优惠', 'rizhuti-v2'),
            '1' => $_badge['31'].$_badge['365'].$_badge['3600'],
            '2' => $_badge['365'].$_badge['3600'],
            '3' => $_badge['3600'],
        );
    	// 价格配置
        $user_price = [];
        $user_price['no'] = [
        	'title'=>esc_html__('普通用户购买','rizhuti-v2'),
        	'price'=>esc_attr($price_text),
        	'checked'=>' checked',//是否默认选中
        	'disabled'=>' disabled',//是否禁止选中
        	'btns'=>'<p><button type="button" class="btn btn-block btn-primary mt-3 click-pay-post" data-postid="' . $post_id. '" data-nonce="' . $click_nonce . '" data-price="' . $shop_info['wppay_price'] . '">'.$instance['pay_btn_name'].'</button></p>',
        ];

        if (empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay')) {
            $user_price['no']['btns'] = '<p><a href="'.wp_login_url(curPageURL()).'" class="btn btn-block btn-primary mt-3" rel="nofollow noopener noreferrer">'.$instance['pay_login_btn_name'].'</a></p>';
        }

        if ($shop_info['wppay_vip_auth']>0) {
			$user_price['vip'] = [
	        	'title'=>__('<b class="text-warning"><i class="fa fa-diamond"></i> VIP会员免费下载</b>','rizhuti-v2'),
		        'price'=>esc_html__('免费','rizhuti-v2'),
		        'checked'=>'',
                'disabled'=>'',
		        'btns'=>'<p style="display:none;"><small class="d-block my-2">'.esc_html__('以下会员可免费获取本资源','rizhuti-v2').'</small>'.$_vip_auth[$shop_info['wppay_vip_auth']].'<a href="'.get_user_page_url('vip').'" class="btn btn-block btn-warning mt-3" rel="nofollow noopener noreferrer">'.$instance['vip_btn_name'].'</a></p>',
		    ];
		    $user_price['no']['disabled'] = '';
		}
        //是否VIP资源
        if ($is_vip_post) {
            $user_price['vip']['checked'] = ' checked';
            $user_price['vip']['disabled'] = ' disabled';
            $user_price['vip']['btns'] = '<p><small class="d-block my-2">'.$instance['vip_btn_desc'].'</small>'.$_vip_auth[$shop_info['wppay_vip_auth']].'<a href="'.get_user_page_url('vip').'" class="btn btn-block btn-warning mt-3" rel="nofollow noopener noreferrer">'.$instance['vip_btn_name'].'</a></p>';
            unset($user_price['no']);
        }
		if ($IS_PAID>0) {
            // 下载数据按钮处理
			if (!empty($shop_info['wppay_down']) && is_array($shop_info['wppay_down'])) {
				$post_down_info = $shop_info['wppay_down'];
            } else {
                #旧版本数据完美兼容处理wppay_down wppay_down_info
                $post_down_info[] = array('name' => '立即下载','url'  => $shop_info['wppay_down'],'pwd'  => get_post_meta($post_id, 'wppay_down_info', true),);
            }
            $post_down_btns = '';
    		foreach ($post_down_info as $key => $item) {
                $down_ids = urlencode(base64_encode($post_id.'-'.$key.'-'.$click_nonce));
                $down_link = get_goto_url('down',$down_ids);
                $post_down_btns .= '<div class="btn-group btn-block mt-2" role="group">';
                $post_down_btns .= '<a target="_blank" href="'.$down_link.'" class="btn btn-dark"><i class="fas fa-download"></i> ' . $item['name'] . '</a>';
                if (!empty($item['pwd'])) {

                    wp_enqueue_script('clipboard');

                    $post_down_btns .= '<button type="button" class="go-copy btn btn-sm btn-dark" title="'.esc_html__('点击复制密码', 'rizhuti-v2').'" data-clipboard-text="' . $item['pwd'] . '"><span>'. esc_html__('密码：', 'rizhuti-v2') .'</span>' . $item['pwd'] . '</button>';
                }
                $post_down_btns .= '</div>';
            }
            // 没有开启免登录购买 免费资源需要登录下载
            if ($IS_PAID==4 && empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay')) {
                $post_down_btns = '<a href="'.wp_login_url(curPageURL()).'" class="btn btn-block btn-warning mt-3" rel="nofollow noopener noreferrer">'.esc_html__('登录后免费下载','rizhuti-v2').'</a>';
            }

    	}
		switch ($IS_PAID) {
			case '1':
				$user_price['no']['title'] = esc_html__('您已成功购买本资源','rizhuti-v2');
				$user_price['no']['checked'] = ' checked';
				$user_price['no']['disabled'] = ' disabled';
				$user_price['no']['btns'] = '<p>'.$post_down_btns.'</p>';
				unset($user_price['vip']);
				break;
			case '2':
				$user_price['no']['title'] = esc_html__('您已成功购买本资源','rizhuti-v2');
				$user_price['no']['checked'] = ' checked';
				$user_price['no']['disabled'] = ' disabled';
				$user_price['no']['btns'] = '<p>'.$post_down_btns.'</p>';
				unset($user_price['vip']);
				break;
			case '3':
                if ($shop_info['wppay_vip_auth']>0) {
                    $user_price['vip']['checked'] = ' checked';
                    $user_price['vip']['disabled'] = ' disabled';
                    $user_price['vip']['btns'] = '<p><small class="d-block my-2">'.esc_html__('以下VIP会员免费下载，您已获得免费特权','rizhuti-v2').'</small>'.$_vip_auth[$shop_info['wppay_vip_auth']].'</br>'.$post_down_btns.'</p>';
                    unset($user_price['no']);
                    if (!$is_vip_post) {
                        $today_down = (array)_get_user_today_down($user_id);
                        $is_today_down = is_today_down_posot($user_id,$post_id);
                        if (empty($is_today_down) && (empty($today_down['ke']) || $today_down['ke']==0)) {
                            // 下载次数用完
                            if (site_mycoin('is')) {
                                $the_price_text = ' <i class="'.site_mycoin('icon').'"></i> '.convert_site_mycoin($shop_info['wppay_price'],'coin');
                            }else{
                                $the_price_text = ' ￥ '.$shop_info['wppay_price'].'</span>';
                            }
                            $the_pay_btn = '<button type="button" class="btn btn-block btn-warning mt-3 click-pay-post" data-postid="' . $post_id. '" data-nonce="' . $click_nonce . '" data-price="' . $shop_info['wppay_price'] . '">'.esc_html__('购买本资源','rizhuti-v2').$the_price_text.'</button>';
                            $user_price['vip']['btns'] = '<p><small class="d-block my-2">'.esc_html__('以下VIP会员免费下载，您已获得免费特权','rizhuti-v2').'</small>'.$_vip_auth[$shop_info['wppay_vip_auth']].'</br><button type="button" class="btn btn-block btn-danger mt-3" disabled><i class="fas fa-info-circle"></i> '.esc_html__('可下载次数不足','rizhuti-v2').'</button>'.$the_pay_btn.'</p>';
                        }

                    }
                    break;
                }
            case '4':
                $user_price['no']['title'] = esc_html__('该资源免费下载，无需购买','rizhuti-v2');
                $user_price['no']['checked'] = ' checked';
                $user_price['no']['disabled'] = ' disabled';
                $user_price['no']['btns'] = '<p>'.$post_down_btns.'</p>';
                unset($user_price['vip']);
                break;
		}

    	echo '<ul class="pricing-options">';
    	foreach ($user_price as $k => $v) {
			echo '<li>';
			echo '<div class="custom-radio">';
			echo '<input type="radio" id="post_price_opt_'.$k.'" name="price_filter_opt"'.$v['checked'].$v['disabled'].'>';
			echo '<label for="post_price_opt_'.$k.'" data-price="'.$v['price'].'">';
			echo '<span class="circle"></span>'.$v['title'];
			echo '</label>';
			echo '</div>';
			echo $v['btns'];
			echo '</li>';
		}
		echo '</ul>';
		////其他信息
		if (!empty($shop_info['wppay_info'])) {
            echo '<div class="down-info">';
            echo '<h5>'.esc_html__('其他信息','rizhuti-v2').'</h5>';
            echo '<ul class="infos">';
            if (!empty($shop_info['wppay_demourl'])) {
                echo '<li><p class="data-label">' . esc_html__('链接','rizhuti-v2') . '</p><p class="info"><a target="_blank" rel="nofollow noopener noreferrer" href="' . $shop_info['wppay_demourl'] . '" class="badge badge-secondary-lighten"><i class="fas fa-link"></i> ' . esc_html__('点击查看','rizhuti-v2') . '</a></p></li>';
            }
            foreach ($shop_info['wppay_info'] as $key => $value) {
            	echo '<li><p class="data-label">' . $value['title'] . '</p><p class="info">' . $value['desc'] . '</p></li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        if (!empty($instance['desc'])) {
            echo '<div class="down-help mt-2 small text-muted">'.$instance['desc'].'</div>';
        }

        echo $args['after_widget'];
    }
}
