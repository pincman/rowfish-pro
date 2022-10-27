<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:18:03 +0800
 * @Path           : /wp-content/themes/rowfish/pages/user/question-list-item.php
 * @Description    : 用户中心-我的问答页面-问题列表
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!ap_user_can_view_post(get_the_ID())) {
	return;
}

$clearfix_class = array('ap-questions-item clearfix');

?>
<div id="question-<?php the_ID(); ?>" <?php post_class($clearfix_class); ?> itemtype="https://schema.org/Question" itemscope="">
	<div class="ap-questions-inner">
		<div class="ap-list-counts">
			<!-- Votes count -->
			<?php if (!ap_opt('disable_voting_on_question')) : ?>
				<span class="ap-questions-count ap-questions-vcount">
					<span itemprop="upvoteCount"><?php ap_votes_net(); ?></span>
					<?php _e('Votes', 'anspress-question-answer'); ?>
				</span>
			<?php endif; ?>

			<!-- Answer Count -->
			<a class="ap-questions-count ap-questions-acount" href="<?php echo ap_answers_link(); ?>">
				<span itemprop="answerCount"><?php ap_answers_count(); ?></span>
				<?php _e('Ans', 'anspress-question-answer'); ?>
			</a>
		</div>

		<div class="ap-questions-summery">
			<span class="ap-questions-title" itemprop="name">
				<?php ap_question_status(); ?>
				<a class="ap-questions-hyperlink" itemprop="url" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			</span>
			<div class="ap-display-question-meta">
				<?php echo ap_question_metas(); ?>
			</div>
		</div>
	</div>
</div><!-- list item -->