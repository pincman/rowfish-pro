<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 03:37:13 +0800
 * @Path           : /wp-content/themes/rowfish/archive.php
 * @Description    : 文章列表页模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

use AnsPress\Addons\Profile;

get_header();
$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes($sidebar);
$item_style = rizhuti_v2_item_style();
if (_cao('is_archive_filter') && !is_author()) {
	get_template_part('templates/global/archive-filter');
}
?>
<div class="archive container<?php if (is_author()) echo ' author-archive'; ?>">
	<?php
	if (is_author()) :
		global $author;
		$author_id = (int) $author;
		$author_tab = isset($_GET['type']) ? esc_sql($_GET['type']) : null;
		$profile =  Profile::init();
		$profile->user_pages();
		$ap_menu     = apply_filters('ap_user_menu_items', anspress()->user_pages, $author_id);
		$current_tab = get_query_var('user_page', ap_opt('user_page_slug_questions'));
		if (!is_null($author_tab) && !in_array($author_tab, ['courses', 'questions', 'answers'])) {
			header("Location: " . esc_url(get_author_posts_url($author_id, get_the_author_meta('display_name', $author_id))));
			exit();
		}
	?>
		<?php get_template_part('templates/author/header', null, [
			'author_id' => $author_id
		]); ?>
		<ul class="nav nav-tabs">

			<li class="nav-item<?php if (is_null($author_tab)) echo ' active'; ?>">
				<a class="nav-link" href="<?php echo is_null($author_tab) ? 'javascript:void(0);' : esc_url(get_author_posts_url($author_id, get_the_author_meta('display_name', $author_id))); ?>" target="_self">
					<i class="fas fa-bookmark"></i><span>文章</span></a>
			</li>
			<li class="nav-item<?php if ($author_tab == 'courses') echo ' active'; ?>">
				<a class="nav-link" href="<?php echo $author_tab == 'courses' ?  'javascript:void(0);' : esc_url(get_author_posts_url($author_id, get_the_author_meta('display_name', $author_id))) . '?type=courses'; ?>" target="_self">
					<i class=" fas fa-coffee"></i><span>课程</span></a>
			</li>
			<li class=" nav-item<?php if ($author_tab == 'questions') echo ' active'; ?>">
				<a class="nav-link" href="<?php echo $author_tab == 'questions' ?  'javascript:void(0);' : esc_url(get_author_posts_url($author_id, get_the_author_meta('display_name', $author_id))) . '?type=questions'; ?>" target="_self">
					<i class="fas fa-comments"></i><span>问题</span>
				</a>
			</li>
			<li class="nav-item<?php if ($author_tab == 'answers') echo ' active'; ?>">
				<a class="nav-link" href="<?php echo $author_tab == 'answers' ?  'javascript:void(0);' : esc_url(get_author_posts_url($author_id, get_the_author_meta('display_name', $author_id))) . '?type=answers'; ?>" target="_self">
					<i class="fas fa-comment-alt"></i><span>回答</span></a>
			</li>
		</ul>
		<?php get_template_part('templates/author/archive', null, [
			'author_tab' => $author_tab,
			'author_id' => $author_id
		]); ?>

	<?php else : ?>
		<div class="row">
			<div class="<?php echo esc_attr($column_classes[0]); ?>">
				<div class="content-area archive-list">
					<div class="row posts-wrapper scroll">
						<?php if (have_posts()) : ?>
						<?php
							/* Start the Loop */
							while (have_posts()) : the_post();
								get_template_part('templates/loop/item');
							endwhile;
						else :
							get_template_part('templats/loop/item', 'none');

						endif;
						?>
					</div>
					<?php rizhuti_v2_pagination(5); ?>
				</div>
			</div>
			<?php if ($sidebar != 'none') : ?>
				<div class="<?php echo esc_attr($column_classes[1]); ?>">
					<aside id="secondary" class="widget-area">
						<?php dynamic_sidebar('cat_sidebar'); ?>
					</aside>
				</div>
			<?php endif; ?>
		</div>

	<?php endif; ?>
</div>
<?php
get_footer();
