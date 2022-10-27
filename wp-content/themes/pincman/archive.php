<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package rizhuti-v2
 */

get_header();
$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes($sidebar);
$item_style = rizhuti_v2_item_style();

?>
<?php if (_cao('is_archive_filter')) {
	get_template_part('template-parts/global/archive-filter');
	// if (is_category()) {
	// 	$cat_filter_enabled = get_term_meta(get_queried_object_id(), 'enabled_filter', true);
	// 	if ($cat_filter_enabled === '1' || $cat_filter_enabled === '') {
	// 		get_template_part('template-parts/global/archive-filter');
	// 	}
	// } else {
	// 	get_template_part('template-parts/global/archive-filter');
	// }
} ?>

<div class="archive container">
	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">
			<div class="content-area archive-list">
				<div class="row posts-wrapper scroll">
					<?php if (have_posts()) : ?>
					<?php
						/* Start the Loop */
						while (have_posts()) : the_post();
							get_template_part('template-parts/loop/item', $item_style);
						endwhile;
					else :
						get_template_part('template-parts/loop/item', 'none');

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
</div>

<?php
get_footer();
