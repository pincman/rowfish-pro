<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-23 08:23:38 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/single-question.php
 * @Description    : 单个问题页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (rizhuti_v2_sidebar() != 'none') {
	global $post;
	update_post_meta($post->ID, 'sidebar_single_style', 'none');
}
global $current_user;
?>
<div id="ap-single" class="ap-q clearfix">

	<div class="ap-question-lr ap-row" itemtype="https://schema.org/Question" itemscope="">
		<div class="ap-q-left <?php echo (is_active_sidebar('ap-qsidebar')) ? 'ap-col-8' : 'ap-col-12'; ?>">
			<?php
			/**
			 * Action hook triggered before question meta in single question.
			 *
			 * @since 4.1.2
			 */
			do_action('ap_before_question_meta');
			?>
			<div class="ap-question-meta clearfix">
				<?php ap_question_metas(); // xss ok. 
				?>
			</div>
			<?php
			/**
			 * Action hook triggered after single question meta.
			 *
			 * @since 4.1.5
			 */
			do_action('ap_after_question_meta');
			?>
			<div ap="question" apid="<?php the_ID(); ?>">
				<div id="question" role="main" class="ap-content">
					<div class="ap-single-vote"><?php ap_vote_btn(); ?></div>
					<?php
					/**
					 * Action triggered before question title.
					 *
					 * @since   2.0
					 */
					do_action('ap_before_question_title');
					?>
					<div class="ap-avatar">
						<a href="<?php echo esc_url(get_author_posts_url($current_user->ID, get_the_author_meta('display_name', $current_user->ID))); ?>">
							<?php ap_author_avatar(ap_opt('avatar_size_qquestion')); ?>
						</a>
					</div>
					<div class="ap-cell clearfix">
						<div class="ap-cell-inner">
							<div class="ap-q-metas">
								<span class="ap-author" itemprop="author" itemscope itemtype="http://schema.org/Person">
									<?php echo ap_user_display_name(['html' => true]); ?>
								</span>
								<a href="<?php the_permalink(); ?>" class="ap-posted">
									<?php
									$posted = 'future' === get_post_status() ? __('Scheduled for', 'anspress-question-answer') : __('Published', 'anspress-question-answer');

									$time = ap_get_time(get_the_ID(), 'U');

									if ('future' !== get_post_status()) {
										$time = ap_human_time($time);
									}

									printf('<time itemprop="datePublished" datetime="%1$s">%2$s</time>', ap_get_time(get_the_ID(), 'c'), $time);
									?>
								</a>
								<span class="ap-comments-count">
									<?php $comment_count = get_comments_number(); ?>
									<?php printf(_n('%s Comment', '%s Comments', $comment_count, 'anspress-question-answer'), '<span itemprop="commentCount">' . (int) $comment_count . '</span>'); ?>
								</span>
							</div>

							<!-- Start ap-content-inner -->
							<div class="ap-q-inner">
								<?php
								/**
								 * Action triggered before question content.
								 *
								 * @since   2.0.0
								 */
								do_action('ap_before_question_content');
								?>

								<div class="question-content ap-q-content" itemprop="text">
									<?php the_content(); ?>
								</div>

								<?php
								/**
								 * Action triggered after question content.
								 *
								 * @since   2.0.0
								 */
								do_action('ap_after_question_content');
								?>
							</div>

							<div class="ap-post-footer clearfix">
								<?php ap_post_actions_buttons(); ?>
								<?php do_action('ap_post_footer'); ?>
							</div>
						</div>

						<?php ap_post_comments(); ?>
					</div>
				</div>
			</div>

			<?php
			/**
			 * Action triggered before answers.
			 *
			 * @since   4.1.8
			 */
			do_action('ap_before_answers');
			?>

			<?php
			// Get answers.
			ap_answers();

			// Get answer form.
			ap_get_template_part('answer-form');
			?>
		</div>

		<?php if (is_active_sidebar('ap-qsidebar')) { ?>
			<div class="ap-question-right ap-col-4">
				<div class="ap-question-info">
					<?php dynamic_sidebar('ap-qsidebar'); ?>
				</div>
			</div>
		<?php } ?>

	</div>
</div>