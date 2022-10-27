<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 03:33:31 +0800
 * @Path           : /wp-content/themes/rowfish/single.php
 * @Description    : 文章内页
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes($sidebar);
$part = (!is_close_site_shop() && _cao('is_single_shop_template', true) && _get_post_shop_type() >= 3) ? 'single-shop' : 'single';

get_header();

?>

<div class="container">
	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">
			<?php if (_cao('is_single_breadcrumb', '1') && $part == 'single') : ?>
				<div class="content-area crumb-area">
					<div class="article-crumb"><?php rizhuti_v2_breadcrumb('breadcrumb'); ?></div>
				</div>
			<?php endif; ?>
			<div class="content-area">
				<?php while (have_posts()) : the_post();
					in_array($part, ['page', 'single']) ? get_template_part('templates/content/' . $part) : get_template_part('template-parts/content/' . $part);
				endwhile; ?>
			</div>
		</div>
		<?php if ($sidebar != 'none') : ?>
			<div class="<?php echo esc_attr($column_classes[1]); ?>">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>