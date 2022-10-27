<?php
$col_classes = (rizhuti_v2_sidebar() != 'none') ? 'col-lg-12' : 'col-lg-6 col-12';
?>

<div class="<?php echo esc_attr($col_classes); ?>">

	<article id="post-<?php the_ID(); ?>" <?php post_class('post post-list'); ?>>
		<?php echo pm_post_icons(); ?>
		<?php echo pm_get_post_media(null, 'thumbnail'); ?>
		<div class="entry-wrapper">
			<?php if (_cao('is_post_list_category', 1)) {
				rizhuti_v2_category_dot(2);
			} ?>
			<header class="entry-header">
				<?php rizhuti_v2_entry_title(array('link' => true)); ?>
			</header>
			<?php $arr = get_post_custom_values('summary'); ?>
			<div class="entry-excerpt"><?php if (is_array($arr) && $arr[0] !== '') : ?><?php echo trim($arr[0]); ?><?php endif; ?></div>

			<div class="entry-footer">
				<?php rizhuti_v2_entry_meta(
					array(
						'author' => _cao('is_post_list_author', 1),
						'category' => false,
						'comment' => _cao('is_post_list_comment', 1),
						'date' => _cao('is_post_list_date', 1),
						'favnum' => _cao('is_post_list_favnum', 1),
						'views' => _cao('is_post_list_views', 1),
						'shop' => _cao('is_post_list_shop', 1),
					)
				); ?>
			</div>
		</div>
	</article>

</div>