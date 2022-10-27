<?php

/**
 * User question template
 * Display user profile questions.
 *
 * @link https://anspress.net
 * @since 4.0.0
 * @package AnsPress
 *
 * @since 4.1.13 Fixed pagination issue when in main user page.
 */

global $wp;
$user_id                        = get_current_user_id();
$args['ap_current_user_ignore'] = true;
$args['author']                 = $user_id;

/**
 * Filter authors question list args
 *
 * @var array
 */
$args = apply_filters('ap_authors_questions_args', $args);

anspress()->questions = new \Question_Query($args);
?>

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

	<?php
	ap_pagination(false, anspress()->questions->max_num_pages, '?paged=%#%', ap_user_link(get_current_user_id(), 'questions'));
	?>

<?php
else :
	ap_get_template_part('content-none');
endif;
?>