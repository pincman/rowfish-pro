<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:11:38 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/ask.php
 * @Description    : 提问页面模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (rizhuti_v2_sidebar() != 'none') {
	global $post;
	update_post_meta($post->ID, 'sidebar_single_style', 'none');
}
?>
<div id="ap-ask-page" class="clearfix">
	<?php if (ap_user_can_ask()) : ?>
		<?php rf_ap_ask_form(); ?>
	<?php elseif (is_user_logged_in()) : ?>
		<div class="ap-no-permission">
			<?php _e('You do not have permission to ask a question.', 'anspress-question-answer'); ?>
		</div>
	<?php endif; ?>

	<?php ap_get_template_part('login-signup'); ?>
</div>