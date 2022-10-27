<?php
global $post,$current_user,$ri_vip_options;
$user_id = $current_user->ID; //用户ID
$post_id = $post->ID; //文章ID
$dir = get_template_directory_uri().'/assets';

//获取视频信息
$video_data = array();
$video_textarea = get_post_meta( $post_id, 'hero_video_data' ,true);
$video_arr = explode(PHP_EOL, trim($video_textarea));


//格式化视频信息
foreach ($video_arr as $key => $item) {
	$item_exp = explode("|", trim($item));
	$_vurl = (!empty($item_exp[0])) ? $item_exp[0] : '' ;
	$_vname = (!empty($item_exp[1])) ? $item_exp[1] : '' ;

	// 视频信息
	$video_data[$key] = array_merge(array('url' => '','name' => ''),array('url' => $_vurl,'name' => $_vname));
}

// 付费资源信息
$shop_info = get_post_shop_info();

if ($shop_info['wppay_type']!='5') {
	return;
}

//是否购买
$RiClass = new RiClass($post_id,$user_id);
$IS_PAID = $RiClass->is_pay_post();
$is_vip_post = !empty($shop_info['wppay_vip_auth']) && empty($shop_info['wppay_price']);
$is_nologin_free = $IS_PAID==4 && empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay');
$is_nologin_pay = empty($user_id) && !_cao('is_rizhuti_v2_nologin_pay');


if ( is_close_site_shop() && $IS_PAID!=4) {
	return;
}


//业务逻辑
$_content = '<div class="content-do-video"><div class="views text-muted"><span class="badge badge-light note"><i class="fa fa-info-circle"></i> '.esc_html__('暂无权限播放','rizhuti-v2').'</span>';
$js_video_url = '';
if ($IS_PAID>0){
	if ($is_nologin_free) {
        $_content .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-primary btn-rounded btn-sm my-2"><i class="fa fa-user"></i> ' . esc_html__('登录后免费播放', 'rizhuti-v2') . '</a>';
    }else{
    	$js_video_url = $video_data[0]['url']; //输出视频地址
    }

}else{
	$_content .= '<div class="mb-4 text-muted">';
	if ($is_vip_post) {
            $_content .= esc_html__('该视频VIP专属播放','rizhuti-v2');
    }else{
        $_content .= esc_html__('您还没有获得播放权限', 'rizhuti-v2');
    }
    $_content .='</div><div class="mb-2 text-muted">';
    if (!empty($shop_info['wppay_vip_auth'])) {
    	$_content .= '<span class="ml-2"></span>'.get_post_vip_auth_badge($shop_info['wppay_vip_auth']).esc_html__('可免费播放','rizhuti-v2');
    }
    $_content .='</div>';

    if (!$is_vip_post) {
    	$_content .= '<div class="mb-4 text-center">';
        if ($is_nologin_pay) {
        	$_content .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-primary btn-sm btn-rounded mx-2 mt-lg-4"><i class="fa fa-user"></i> ' . esc_html__('登录后购买', 'rizhuti-v2') . '</a>';
		}else{
			if (site_mycoin('is')) {
                $_content .= '<button type="button" class="click-pay-post btn btn-primary btn-rounded btn-sm mx-2 mt-lg-4" data-postid="' . $post_id. '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $post_id) . '" data-price="' . $shop_info['wppay_price'] . '">支付 '.convert_site_mycoin($shop_info['wppay_price'],'coin').' <i class="'.site_mycoin('icon').'"></i> '.site_mycoin('name').'播放</button>';
            }else{
                $_content .= '<button type="button" class="click-pay-post btn btn-primary btn-rounded btn-sm mx-2 mt-lg-4" data-postid="' . $post_id. '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $post_id) . '" data-price="' . $shop_info['wppay_price'] . '">支付 ￥'.$shop_info['wppay_price'].' 播放</button>';
            }
			
		}
    }

    if (!empty($shop_info['wppay_vip_auth'])) {
		$_content .= '<a href="'.get_user_page_url('vip').'" class="btn btn-warning btn-rounded btn-sm mx-2 mt-lg-4"><i class="fa fa-diamond"></i> 升级VIP免费播放</a>';
	}
	$_content .= '</div></div>';
}

// 视频选集
if (count($video_data)>1) {
	$_content2 =  '<p class="head-con text-muted"><i class="far fa-list-alt"></i> '.esc_html__('视频选集','rizhuti-v2').' ('.count($video_data).')<b class="small text-muted">'.get_the_title().'</b></p>';
	$_content2 .=  '';
	$_content2 .= '<ul class="list-box">';
	foreach ($video_data as $key => $v) {
		$v_name = ( !empty($v['name']) ) ? $v['name'] : '第'.($key+1).'集' ;
		$v_url = ( !empty($js_video_url) ) ? $v['url'] : '' ;
		$_content2 .= '<li>';
		$actived = ($key==0) ? ' active' : '' ;
		$disabled = (!empty($js_video_url)) ? '' : 'disabled' ;
		$_content2 .= '<a href="javascript:;" class="switch-video'.esc_attr($actived).'" data-index="'.($key+1).'" data-url="'.$v_url.'"'.$disabled.'><span class="mr-2">P'.($key+1).'</span>'.$v_name.'<i>'.$pay_note.'</i></a >';
		$_content2 .= '</li>';
	}
	$_content2 .= '</ul>';

	$classes_col = ['col-lg-9 col-12','col-lg-3 col-12'];
}else{
	$_content2 = '';
	$classes_col = ['col-lg-12 col-12','col-lg-12 col-12'];
}

?>


<div class="hero-media video">
	<div class="container-lg">
		<div class="row no-gutters">
			<div class="<?php echo esc_attr($classes_col[0]);?>">
				<div id="rizhuti-video"></div>
			</div>
			<?php if (!empty($_content2)) { ?>
			<div class="<?php echo esc_attr($classes_col[1]);?>">
				<div id="rizhuti-video-page"><?php echo $_content2;?></div>
			</div>
			<?php } ?>
		</div>

	</div>
</div>

<!-- JS脚本 -->

<script type="text/javascript">
jQuery(function() {
    'use strict';
    var js_video_url = '<?php echo $js_video_url;?>';
    var js_video_content = '<?php echo $_content;?>';
    const dp=new DPlayer({container:document.getElementById("rizhuti-video"),theme:"#fd7e14",screenshot:!1,video:{url:js_video_url,type:"auto",pic:""}});var video_vh="inherit";if($(".dplayer-video").bind("loadedmetadata",function(){var e=this.videoWidth||0,i=this.videoHeight||0,a=$("#rizhuti-video").width();i>e&&(video_vh=e/i*a,$(".dplayer-video").css("max-height",video_vh))}),""==js_video_url){var mask=$(".dplayer-mask");mask.show(),mask.hasClass("content-do-video")||(mask.append(js_video_content),$(".dplayer-video-wrap").addClass("video-filter"))}else{var notice=$(".dplayer-notice");notice.hasClass("dplayer-notice")&&(notice.css("opacity","0.8"),notice.append('<i class="fa fa-unlock-alt"></i> 您已获得当前视频播放权限'),setTimeout(function(){notice.css("opacity","0")},2e3)),dp.on("fullscreen",function(){$(".dplayer-video").css("max-height","unset")}),dp.on("fullscreen_cancel",function(){$(".dplayer-video").css("max-height",video_vh)})}var vpage=$("#rizhuti-video-page .switch-video");vpage.on("click",function(){var e=$(this);vpage.removeClass("active"),e.addClass("active"),dp.switchVideo({url:e.data("url"),type:"auto"})});
});
</script>