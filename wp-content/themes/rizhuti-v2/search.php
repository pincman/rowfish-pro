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

<div class="archive container">
	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">
			<div class="content-area">
				<div class="row posts-wrapper scroll">
					<?php if (have_posts()) : ?>
					<?php
						while (have_posts()) : the_post();
							get_template_part('template-parts/loop/item', $item_style);
						endwhile;
					else :
						get_template_part('template-parts/loop/item', 'none');
					endif; ?>
				</div>
				<?php rizhuti_v2_pagination(5); ?>
			</div>
		</div>
		<?php if ($sidebar != 'none') : ?>
			<div class="<?php echo esc_attr($column_classes[1]); ?>">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
