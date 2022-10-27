<?php

/**
 * 修改:
 * 
 * 全面改造视频为在线网课系统
 */
global $ri_vip_options;
$dir = get_template_directory_uri() . '/assets';
$uinfo = pm_shop_post_info();
// 是否有介绍视频
$course_introduce = get_post_meta($uinfo['post_id'], 'wppay_course_intro', true) == '1';
$default_chapter_num = $course_introduce ? 0 : 1;
$current_chapter_num = !empty($_GET['chapter']) ? (int) $_GET['chapter'] : $default_chapter_num;
if ($current_chapter_num < 1) $current_chapter_num = $default_chapter_num;
// 付费资源信息

if (!$uinfo['course']) return;
//获取视频信息
$course_data = get_post_meta($uinfo['post_id'], 'wppay_chapter_info', true);
$js_video_url = '';
if ($current_chapter_num === 0) {
	$js_video_url =  get_post_meta($uinfo['post_id'], 'wppay_course_intro_video', true);
	$_content = '<div class="content-do-video"><div class="views text-muted"></div></div>';
} else {
	$current_chapter = $course_data[$current_chapter_num - 1] ?? [];
	$chapter_online = $current_chapter['online'] == '1';
	$is_single_free = (_cao('free_onlogin_down') == '1' && $current_chapter['free'] == '1') || (!_cao('free_onlogin_down') && $uinfo['user_id'] && $current_chapter['free'] == '1');
	$is_free = $uinfo['free'] || $is_single_free;
	$can_paly = ($is_free || $uinfo['can']) && $chapter_online;
	if (is_close_site_shop() || !$course_data || empty($course_data) || count($course_data) <= 0) return;
	//业务逻辑
	$_content = '<div class="content-do-video"><div class="views text-muted"><span class="badge badge-light note"><i class="fa fa-info-circle"></i> ' . esc_html__('暂无权限播放', 'rizhuti-v2') . '</span>';
	if ($can_paly) {
		$js_video_url = $current_chapter['video'];
		$_content .= '</div><div class="mb-2 text-muted">';
		if ($uinfo['vip_user']) {
			$_content .= '<span class="ml-2"></span>' . get_post_vip_auth_badge($shop_info['wppay_vip_auth']) . esc_html__('视频可播放', 'rizhuti-v2');
		}
	} elseif (!$chapter_online) {
		$_content .= '当前课时还未上线,尽请期待';
	} else {
		if (!$uinfo['user_id']) {
			$_content .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-primary btn-rounded btn-sm my-2"><i class="fa fa-user"></i> ' . esc_html__('登录后才可播放', 'rizhuti-v2') . '</a>';
		} else if ($uinfo['vip']) {
			$_content .= $uinfo['only_vip'] ? '当前视频只有订阅者才可观看' : '当前视频需要购买本教程或订阅后才可观看';
		}
		$_content .= '<div class="mb-4 text-center">';
		if (!$uinfo['only_vip']) {
			if (site_mycoin('is')) {
				$_content .= '<button type="button" class="click-pay-post btn btn-primary btn-rounded btn-sm mx-2 mt-lg-4" data-postid="' . $uinfo['post_id'] . '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $uinfo['post_id']) . '" data-price="' . $uinfo['price'] . '">支付 ' . convert_site_mycoin($uinfo['price'], 'coin') . ' <i class="' . site_mycoin('icon') . '"></i> ' . site_mycoin('name') . '播放</button>';
			} else {
				$_content .= '<button type="button" class="click-pay-post btn btn-primary btn-rounded btn-sm mx-2 mt-lg-4" data-postid="' . $uinfo['post_id'] . '" data-nonce="' . wp_create_nonce('rizhuti_click_' . $uinfo['post_id']) . '" data-price="' . $uinfo['price'] . '">支付 ￥' . $uinfo['price'] . ' 播放</button>';
			}
		}
		$_content .= '<a href="' . get_user_page_url('vip') . '" class="btn btn-outline-success btn-rounded btn-sm mx-2 mt-lg-4"><i class="fa fa-code"></i> 订阅本站</a>';
	}
	$_content .= '</div></div>';
}


// 视频选集
// if (count($video_data) > 1) {
$_content2 =  '<p class="head-con text-muted"><i class="far fa-list-alt"></i> ' . esc_html__('教程选集', 'rizhuti-v2') . ' (' . count($course_data) . ')<b class="small text-muted">' . get_the_title() . '</b></p>';
$_content2 .=  '';
$_content2 .= '<ul class="list-box">';
if ($course_introduce) {
	$intro_title = get_post_meta($uinfo['post_id'], 'wppay_course_intro_title', true);
	$v_name  = !empty($intro_title) ?  $intro_title : '序言';
	$v_url = !empty($js_video_url) ? $js_video_url : '';
	$_content2 .= '<li>';
	$actived = ($current_chapter_num === 0) ? ' active' : '';
	$disabled = !empty($js_video_url) ? '' : 'disabled';
	$_content2 .= '<a href="' . add_query_arg("chapter", 0) . '" class="switch-video' . esc_attr($actived) . '" data-index="0" data-url="' . $v_url . '"' . $disabled . '><span class="mr-2">' . $v_name . '</span></a >';
	$_content2 .= '</li>';
}

foreach ($course_data as $key => $v) {
	$v_name = (!empty($v['title'])) ?  $v['title'] : '第' . ($key + 1) . '集';
	$v_online =  $v['online'] === '1';
	$offline_class = !$v_online ? ' offline' : '';
	$v_url = !empty($js_video_url) ? $js_video_url : '';
	$_content2 .= '<li>';
	$actived = ($key == $current_chapter_num - 1) ? ' active' : '';
	$disabled = !empty($js_video_url) ? '' : 'disabled';
	$_content2 .= '<a href="' . add_query_arg("chapter", $key + 1) . '" class="switch-video' .	$offline_class . esc_attr($actived) . '" data-index="' . ($key + 1) . '" data-url="' . $v_url . '"' . $disabled . '><span class="mr-2">第' . ($key + 1) . '集: </span><span>' . $v_name . '</span><i>' . $pay_note . '</i></a >';
	$_content2 .= '</li>';
}
$_content2 .= '</ul>';

$classes_col = ['col-lg-9 col-12', 'col-lg-3 col-12'];

?>


<div class="hero-media video">
	<div class="container-lg">
		<div class="row no-gutters">
			<div class="<?php echo esc_attr($classes_col[0]); ?>">
				<div id="rizhuti-video"></div>
			</div>
			<?php if (!empty($_content2)) { ?>
				<div class="<?php echo esc_attr($classes_col[1]); ?>">
					<div id="rizhuti-video-page"><?php echo $_content2; ?></div>
				</div>
			<?php } ?>
		</div>

	</div>
</div>

<!-- JS脚本 -->

<script type="text/javascript">
	jQuery(function() {
		'use strict';
		var js_video_url = '<?php echo htmlspecialchars_decode($js_video_url); ?>';
		var js_video_content = '<?php echo $_content; ?>';
		const dp = new DPlayer({
			container: document.getElementById("rizhuti-video"),
			theme: "#fd7e14",
			screenshot: !1,
			video: {
				url: js_video_url,
				type: "auto",
				pic: ""
			}
		});
		var video_vh = "inherit";
		if ($(".dplayer-video").bind("loadedmetadata", function() {
				var e = this.videoWidth || 0,
					i = this.videoHeight || 0,
					a = $("#rizhuti-video").width();
				i > e && (video_vh = e / i * a, $(".dplayer-video").css("max-height", video_vh))
			}), "" == js_video_url) {
			var mask = $(".dplayer-mask");
			mask.show(), mask.hasClass("content-do-video") || (mask.append(js_video_content), $(".dplayer-video-wrap").addClass("video-filter"))
		} else {
			var notice = $(".dplayer-notice");
			notice.hasClass("dplayer-notice") && (notice.css("opacity", "0.8"), notice.append('<i class="fa fa-unlock-alt"></i> 您已获得当前视频播放权限'), setTimeout(function() {
				notice.css("opacity", "0")
			}, 2e3)), dp.on("fullscreen", function() {
				$(".dplayer-video").css("max-height", "unset")
			}), dp.on("fullscreen_cancel", function() {
				$(".dplayer-video").css("max-height", video_vh)
			})
		}
		var vpage = $("#rizhuti-video-page .switch-video");
		vpage.on("click", function() {
			var e = $(this);
			vpage.removeClass("active"), e.addClass("active"), dp.switchVideo({
				url: e.data("url"),
				type: "auto"
			})
		});
	});
</script>