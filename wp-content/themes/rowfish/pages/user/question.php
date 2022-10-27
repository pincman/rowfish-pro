<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:17:42 +0800
 * @Path           : /wp-content/themes/rowfish/pages/user/question.php
 * @Description    : 用户中心-我的问答页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

use AnsPress\Addons\Profile;
use Anspress\Addons\Reputation;

defined('ABSPATH') || exit;
date_default_timezone_set(get_option('timezone_string')); //初始化本地时间
global $current_user;
$profile =  Profile::init();
$profile->user_pages();
$ap_user_id =  ap_current_user_id();
$current_tab = get_query_var('user_page', ap_opt('user_page_slug_questions'));
$ap_menu     = apply_filters('ap_user_menu_items', anspress()->user_pages, $current_user->ID);
?>

<div class="card">
	<div class="card-header">
		<h5 class="card-title">问答频道历史记录</h5>
	</div>

	<!-- Body -->
	<div class="card-body">

		<ul class="nav nav-segment nav-fill mt-0 mb-4" id="editUserTab" role="tablist">
			<?php $loop_index = 0;
			foreach ((array) $ap_menu as $args) :  ?>
				<?php if ((empty($args['private']) || true === $args['private']) && in_array($args['slug'], ['questions', 'answers'])) : ?>
					<li class="nav-item">
						<a class="nav-link<?php if ($loop_index == 0) echo ' active'; ?>" id="question-<?php echo esc_attr($args['slug']); ?>-tab" data-toggle="tab" data-target="#question-<?php echo esc_attr($args['slug']); ?>" role="tab" aria-selected="true">
							<?php if (!empty($args['icon'])) {
								echo '<i class="' . esc_attr($args['icon']) . '"></i>';
							}
							echo esc_attr($args['label']); ?>
						</a>
					</li>
			<?php
					$loop_index++;
				endif;
			endforeach; ?>
		</ul>
		<div class="tab-content anspress user-center-anspress" id="anspress">
			<?php $loop_index = 0; ?>
			<?php foreach ((array) $ap_menu as $args) : ?>
				<?php if (empty($args['private']) || true === $args['private'] && $current_user->ID === $ap_user_id && in_array($args['slug'], ['questions', 'answers'])) : ?>
					<div aria-labelledby="question-<?php echo esc_attr($args['slug']); ?>-tab" class="tab-pane fade<?php if ($loop_index == 0) echo ' active show'; ?>" id="question-<?php echo esc_attr($args['slug']); ?>" role="tabpanel">
						<?php if ($args['slug'] == 'questions') : ?>
							<?php
							$args['ap_current_user_ignore'] = true;
							$args['author']                 = $current_user->ID;
							/**
							 * Filter authors question list args
							 *
							 * @var array
							 */
							$args = apply_filters('ap_authors_questions_args', $args);
							anspress()->questions = new \Question_Query($args); ?>
							<?php if (ap_have_questions()) : ?>
								<div class="ap-questions">
									<?php
									/* Start the Loop */
									while (ap_have_questions()) :
										ap_the_question();
										get_template_part('pages/user/question-list-item');
									endwhile;
									?>
								</div>

								<?php
								ap_pagination(false, anspress()->questions->max_num_pages, '?paged=%#%', ap_user_link(ap_current_user_id(), 'questions'));
								?>

							<?php else : ?>
								<div class="text-center space-1">
									<img class="avatar avatar-xl mb-3" src="<?php echo get_template_directory_uri(); ?>/assets/img/empty-state-no-data.svg">
									<p class="card-text">暂无记录</p>
									</img>
								</div>
							<?php endif; ?>
						<?php elseif ($args['slug'] == 'answers') : ?>
							<?php
							global $answers;
							$args['ap_current_user_ignore'] = true;
							$args['ignore_selected_answer'] = true;
							$args['showposts']              = 10;
							$args['author']                 = $current_user->ID;
							$args               = apply_filters('ap_user_answers_args', $args);
							anspress()->answers = $answers = new \Answers_Query($args);
							?>
							<?php if (ap_have_answers()) : ?>
								<div id="ap-bp-answers">
									<?php
									/* Start the Loop */
									while (ap_have_answers()) :
										ap_the_answer();
										get_template_part('pages/user/answer-item');
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

							<?php else : ?>
								<div class="text-center space-1">
									<img class="avatar avatar-xl mb-3" src="<?php echo get_template_directory_uri(); ?>/assets/img/empty-state-no-data.svg">
									<p class="card-text">暂无记录</p>
									</img>
								</div>
							<?php endif; ?>
						<?php elseif ($args['slug'] == 'reputations') : ?>
							<?php
							$reputation = Reputation::init();
							$reputations = new \AnsPress_Reputation_Query(['user_id' => $current_user->ID]);
							?>
							<?php if ($reputations->have()) : ?>
								<table class="ap-reputations" style="width: 100%;">
									<tbody>
										<?php
										while ($reputations->have()) :
											$reputations->the_reputation();
										?>
											<?php ap_get_template_part('addons/reputation/item', ['reputations' => $reputations]); ?>
										<?php endwhile; ?>
									</tbody>
								</table>

								<?php if ($reputations->total_pages > 1) : ?>
									<button ap-loadmore="
	<?php
									echo esc_js(
										wp_json_encode(
											array(
												'ap_ajax_action' => 'load_more_reputation',
												'__nonce'        => wp_create_nonce('load_more_reputation'),
												'current'        => 1,
												'user_id'        => $reputations->args['user_id'],
											)
										)
									);
	?>
" class="ap-loadmore ap-btn" target="_self"><?php esc_attr_e('Load More', 'anspress-question-answer'); ?></button>
								<?php endif; ?>
							<?php else : ?>
								<div class="text-center space-1">
									<img class="avatar avatar-xl mb-3" src="<?php echo get_template_directory_uri(); ?>/assets/img/empty-state-no-data.svg">
									<p class="card-text">暂无记录</p>
									</img>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php $loop_index++; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

	</div>
</div>