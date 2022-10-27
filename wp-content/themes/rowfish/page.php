<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 03:35:02 +0800
 * @Path           : /wp-content/themes/rowfish/page.php
 * @Description    : 页面模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes($sidebar);
get_header();
?>
<div class="container">
	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">
			<div class="content-area">
				<?php while (have_posts()) : the_post();
					get_template_part('templates/content/page');
				endwhile; ?>
			</div>
		</div>
		<?php if ($sidebar != 'none') : ?>
			<div class="<?php echo esc_attr($column_classes[1]); ?>">
				<aside id="secondary" class="widget-area">
					<?php dynamic_sidebar('page_sidebar'); ?>
				</aside>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>