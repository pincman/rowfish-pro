<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-16 00:14:55 +0800
 * @Updated_at     : 2021-11-19 03:34:28 +0800
 * @Path           : /wp-content/themes/rowfish/search.php
 * @Description    : 搜索页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
get_header();
$column_classes = rizhuti_v2_column_classes('none');
$item_style = rizhuti_v2_item_style();
$GLOBALS['wp_query']->set('posts_per_page', 12);
$GLOBALS['wp_query']->query($GLOBALS['wp_query']->query_vars);

?>

<div class="archive container">
	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">
			<div class="content-area archive-list">
				<div class="row posts-wrapper scroll">
					<?php if (have_posts()) : ?>
					<?php
						while (have_posts()) : the_post();
							if (get_post_type() == 'course') {
								$template = 'course/templates/item';
							} else {
								$template = 'templates/loop/item';
							}
							get_template_part($template, null, ['search' => true]);
						endwhile;
					else :
						get_template_part('templates/loop/item', 'none');
					endif; ?>
				</div>
				<?php rizhuti_v2_pagination(5); ?>
			</div>
		</div>
	</div>
</div>

<?php
get_footer();
