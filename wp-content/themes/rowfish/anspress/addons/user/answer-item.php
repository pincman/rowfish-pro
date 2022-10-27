<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:12:33 +0800
 * @Path           : /wp-content/themes/rowfish/anspress/addons/user/answer-item.php
 * @Description    : 答案列表中的答案项组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!ap_user_can_view_post(get_the_ID())) {
	return;
}
global $current_user;
?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="ap-answer-single ap-bpsingle">
		<div class="ap-bpsingle-title entry-title" itemprop="title">
			<?php ap_answer_status(); ?>
			<a class="ap-bpsingle-hyperlink" itemprop="url" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</div>

		<div class="ap-bpsingle-content clearfix">
			<div class="ap-avatar ap-pull-left">
				<a href="<?php echo esc_url(get_author_posts_url($current_user->ID, get_the_author_meta('display_name', $current_user->ID))); ?>">
					<?php ap_author_avatar(40); ?>
				</a>
			</div>
			<div class="ap-bpsingle-desc no-overflow">
				<a href="<?php the_permalink(); ?>" class="ap-bpsingle-published">
					<time itemprop="datePublished" datetime="<?php echo ap_get_time(get_the_ID(), 'c'); ?>">
						<?php
						echo esc_html(
							sprintf(
								// Translators: %s contain human readable time.
								__('Posted %s', 'anspress-question-answer'),
								ap_human_time(ap_get_time(get_the_ID(), 'U'))
							)
						);
						?>
					</time>
				</a>
				<p><?php echo ap_truncate_chars(get_the_content(), 200); ?></p>
				<a href="<?php the_permalink(); ?>" class="ap-view-question"><?php esc_html_e('View Question', 'anspress-question-answer'); ?></a>
			</div>
		</div>

		<div class="ap-bpsingle-meta">
			<span class="apicon-thumb-up"><?php printf(_n('%d Vote', '%d Votes', ap_get_votes_net(), 'anspress-question-answer'), ap_get_votes_net()); ?></span>
			<?php if (ap_is_selected(get_the_ID())) : ?>
				<span class="ap-bpsingle-selected apicon-check" title="<?php esc_attr_e('This answer is selected as best', 'anspress-question-answer'); ?>"><?php esc_attr_e('Selected', 'anspress-question-answer'); ?></span>
			<?php endif; ?>
			<?php ap_recent_post_activity(); ?>
		</div>

	</div>
</div>