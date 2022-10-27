<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:09:03 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/question-list.php
 * @Description    : 问题列表模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

?>
<?php if (!get_query_var('ap_hide_list_head')) : ?>
	<?php ap_get_template_part('list-head'); ?>
<?php endif; ?>

<?php if (ap_have_questions()) : ?>

	<div class="ap-questions">
		<?php
		/* Start the Loop */
		while (ap_have_questions()) :
			ap_the_question();
			ap_get_template_part('question-list-item');
		endwhile;
		?>
	</div>
	<?php ap_questions_the_pagination(); ?>

<?php else : ?>

	<p class="ap-no-questions">
		<?php esc_attr_e('There are no questions matching your query or you do not have permission to read them.', 'anspress-question-answer'); ?>
	</p>

	<?php ap_get_template_part('login-signup'); ?>
<?php endif; ?>