<?php
if (!defined('ABSPATH')) {
	die;
}
header("Content-type:text/html;character=utf-8");
//验证数据是否合法
if (empty($_GET) || !(isset($_GET['down']) || isset($_GET['url']))) {
	riplus_wp_die('内部链接参数非法提示', '<small>此页面作为主题内部链接跳转，请传入正确参数</small>');
}
//内链跳转URL模式
if (isset($_GET['url']) && !isset($_GET['down'])) {
	$url = urldecode($_GET['url']);
	wp_redirect($url);
	exit;
}

//下载文件模式
if (isset($_GET['down']) && !isset($_GET['url'])) {

	global $current_user;
	$down_user_id = $current_user->ID;
	$down_info = (array)explode('-', base64_decode(urldecode($_GET['down'])));

	if (empty($down_info)) {
		riplus_wp_die(__('非法请求', 'rizhuti-v2'), '<small>下载地址校验失败，请返回页面刷新重试，</small>');
	}

	$down_post_id = (int)$down_info[0];
	$down_post_key = (int)$down_info[1];
	$down_nonce = (string)$down_info[2];

	if (!wp_verify_nonce($down_nonce, 'rizhuti_click_' . $down_post_id)) {
		riplus_wp_die(__('非法请求', 'rizhuti-v2'), '<small>下载地址校验失败，请返回页面刷新重试，</small>');
	}

	//是否购买
	// $RiClass = new RiClass($down_post_id, $down_user_id);
	// $IS_PAID = $RiClass->is_pay_post();
	// // 是否单独购买的文章
	// $IS_PAY = $IS_PAID == 1 || $IS_PAID == 2;

	// if (!$IS_PAID) {
	// 	riplus_wp_die(__('您没有权限下载', 'rizhuti-v2'), '<small>请购买本资源后进行下载</small>');
	// }

	// // 没有开启免登录购买 免费资源需要登录下载
	// if ($IS_PAID == 4 && !$down_user_id && !_cao('is_rizhuti_v2_nologin_pay')) {
	// 	riplus_wp_die('免费资源请登录后下载', '<a href="' . wp_login_url(curPageURL()) . '" class="btn text-success">点击此处登录</a>');
	// }

	// //如果是未购买用户 $IS_PAID==1 || $IS_PAID==2 判断用户下载次数是否有效
	// if (!$IS_PAY && $down_user_id > 0) {
	// 	$today_down = (array)_get_user_today_down($down_user_id);
	// 	$is_today_down = is_today_down_posot($down_user_id, $down_post_id); //今日是否下载过 下载过则不重复计算 直接下载
	// 	if (empty($is_today_down) && (empty($today_down['ke']) || $today_down['ke'] == 0)) {
	// 		$emsg = '<ul class="list-group"> <li class="list-group-item d-flex justify-content-between align-items-center"> 今日总共可下载次数 <span class="badge badge-primary badge-pill">' . $today_down['zong'] . '</span> </li> <li class="list-group-item d-flex justify-content-between align-items-center"> 今日已下载次数 <span class="badge badge-primary badge-pill">' . $today_down['yi'] . '</span> </li> <li class="list-group-item d-flex justify-content-between align-items-center"> 今日剩余下载次数 <span class="badge badge-primary badge-pill">' . $today_down['ke'] . '</span> </li> </ul>';
	// 		riplus_wp_die('今日下载次数已用完', $emsg);
	// 	}
	// }

	// 开始下载处理
	$post_down_info = (array)get_post_meta($down_post_id, 'wppay_down', true);
	$the_down_url = $post_down_info[$down_post_key]['url'];
	$the_down_name = $post_down_info[$down_post_key]['name'];

	if (empty($the_down_url) || $the_down_url == '' || $the_down_url == '#' || $the_down_url == '/') {
		riplus_wp_die('下载地址失效或丢失', '下载地址无效，请联系站长或提交工单处理。');
	}

	//添加下载记录
	if (!add_new_down_log($down_user_id, $down_post_id)) {
		riplus_wp_die('下载记录异常', '请联系站长或提交工单处理');
	}

	// 外链地址直接跳转判断 $arr = parse_url($url);
	$parse_url = parse_url($the_down_url);
	if ($_SERVER['HTTP_HOST'] != $parse_url['host']) {
		$_downurl = urldecode($the_down_url);
		wp_redirect($_downurl);
		exit;
	} else {
		$vip_options = (array)_get_ri_vip_options();
		$vip_type = _get_user_vip_type($down_user_id);
		// $download_rate = $vip_options[$vip_type]['download_rate'];
		// if (empty($download_rate) || $download_rate == 0) {
		// 	$download_rate = 100000;
		// }
		$download_rate = 100000;
		//设置文件最长执行时间
		set_time_limit(0);
		// 本地缓冲下载文件
		$file_dir  = $parse_url['path'];
		// 本地缓冲下载文件
		$file_dir = ABSPATH . '/' . chop($file_dir);
		if (!file_exists($file_dir) || !is_file($file_dir)) {
			riplus_wp_die('文件不存在或已失效', '请联系站长或提交工单处理');
		}
		$file_name = $the_down_name . '-' . time() . '.' . pathinfo($file_dir, PATHINFO_EXTENSION);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		header('Expires: 0');
		header('Cache-Control: private');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file_dir));
		header('Content-Disposition: attachment; filename=' . rawurlencode($file_name));
		// flush 内容
		flush();
		// 打开文件
		$fp = fopen($file_dir, 'r');
		while (!feof($fp)) {
			print fread($fp, round($download_rate * 1024));
			// flush 内容输出到浏览器端
			flush();
			ob_flush();  //防止PHP或web服务器的缓存机制影响输出
			// 终端1秒后继续
			sleep(1);
		}
		fclose($fp);
		exit; // 关闭文件流
	}
}


exit;
