<?php

/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */

/**
 * 修改:
 * 
 * 只有在网课系统文章时才加载single-shop.php
 * 为网课系统文章添加course-container类
 */

$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes($sidebar);
$uinfo = pm_shop_post_info();
$part = (!is_close_site_shop() && _cao('is_single_shop_template', true) && $uinfo['course']) ? 'single-shop' : 'single';

get_header();

?>
<?php if ($uinfo['course']) echo "<div id='course-container'>"; ?>
<div class="container">
	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">
			<div class="content-area">
				<?php while (have_posts()) : the_post();
					get_template_part('template-parts/content/' . $part);
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
<?php if ($is_course) echo "</div>"; ?>
<?php get_footer(); ?>