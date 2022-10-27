<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:09:34 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/list-head.php
 * @Description    : 问题列表顶部组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
if (rizhuti_v2_sidebar() != 'none') {
	global $post;
	update_post_meta($post->ID, 'sidebar_single_style', 'none');
}
/**
 * Display question list header
 * Shows sorting, search, tags, category filter form. Also shows a ask button.
 *
 * @package AnsPress
 * @author  Rahul Aryan <rah12@live.com>
 */
$link = ap_get_link_to('ask');

/**
 * Filter ask button link.
 *
 * @param string $link
 */
$link = apply_filters('ap_ask_btn_link', $link);
?>

<div class="ap-list-head clearfix">
	<div class="pull-right">
		<a class="ap-btn-ask" href="<?php echo $link; ?>">提问题</a>
	</div>

	<?php ap_get_template_part('search-form'); ?>
	<?php ap_list_filters(); ?>
</div>


<?php
/**
 * Display an alert showing count for unpublished questions.
 *
 * @since 4.1.13
 */

$questions_count = (int) get_user_meta(get_current_user_id(), '__ap_unpublished_questions', true);

if ($questions_count > 0) {
	$text = sprintf(_n('%d question is', '%d questions are', $questions_count, 'anspress-question-answer'), $questions_count);

	echo '<div class="ap-unpublished-alert ap-alert warning"><i class="apicon-pin"></i>';
	printf(
		// Translators: Placeholder contain link to unpublished questions.
		esc_html__('Your %s unpublished. ', 'anspress-question-answer'),
		'<a href="' . esc_url(ap_get_link_to('/')) . '?unpublished=true">' . esc_attr($text) . '</a>'
	);
	echo '</div>';
}
?>