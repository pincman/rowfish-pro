<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:13:04 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/addons/user/answers.php
 * @Description    : 答案列表页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */



global $answers;
?>

<?php if (ap_have_answers()) : ?>
	<div id="ap-bp-answers">
		<?php
		/* Start the Loop */
		while (ap_have_answers()) :
			ap_the_answer();
			ap_get_template_part('addons/user/answer-item');
		endwhile;
		?>
	</div>
	<?php
	if ($answers->max_num_pages > 1) {
		$args = wp_json_encode(
			[
				'ap_ajax_action' => 'user_more_answers',
				'__nonce'        => wp_create_nonce('loadmore-answers'),
				'type'           => 'answers',
				'current'        => 1,
				'user_id'        => get_queried_object_id(),
			]
		);

		echo '<button class="ap-bp-loadmore ap-btn" ap-loadmore="' . esc_js($args) . '">' . esc_attr__('Load more answers', 'anspress-question-answer') . '</button>';
	}
	?>

<?php
else :
	_e('No answer posted by this user.', 'anspress-question-answer');
endif;
?>