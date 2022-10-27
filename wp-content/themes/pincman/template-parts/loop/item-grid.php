<?php

/**
 * 修改:
 * 
 * 改造Grid布局,使其可以更好地适配在线网课系统
 */
$col_classes = (rizhuti_v2_sidebar() != 'none') ? 'col-6' : 'col-lg-5ths col-lg-3 col-md-4 col-12';
?>

<div class="<?php echo esc_attr($col_classes); ?>">

	<article id="post-<?php the_ID(); ?>" <?php post_class('post post-grid'); ?>>
		<?php echo pm_course_status_icon(); ?>
		<?php echo pm_get_post_media(null, 'thumbnail'); ?>
		<div class="entry-wrapper">
			<?php
			if (_cao('is_post_grid_category', 1)) {
				rizhuti_v2_category_dot(2);
			}
			?>
			<header class="entry-header post-title-flex">
				<?php rizhuti_v2_entry_title(array('link' => true)); ?>
				<?php pm_serie_dot(1); ?>
			</header>
			<?php $arr = get_post_custom_values('summary'); ?>
			<div class="entry-icons"><?php echo pm_post_icons(); ?></div>
			<div class="entry-excerpt"><?php if (is_array($arr) && $arr[0] !== '') : ?><?php echo trim($arr[0]); ?><?php endif; ?></div>

			<div class="entry-footer">
				<?php rizhuti_v2_entry_meta(
					array(
						'author' => _cao('is_post_grid_author', 1),
						'category' => false,
						'comment' => _cao('is_post_grid_comment', 1),
						'date' => _cao('is_post_grid_date', 1),
						'favnum' => _cao('is_post_grid_favnum', 1),
						'views' => _cao('is_post_grid_views', 1),
						'shop' => _cao('is_post_grid_shop', 1),
					)
				); ?>
			</div>
		</div>
	</article>

</div>