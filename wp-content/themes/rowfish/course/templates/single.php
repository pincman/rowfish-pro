<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:35:06 +0800
 * @Path           : /wp-content/themes/rowfish/course/templates/single.php
 * @Description    : 课程内容页面模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */



$sidebar = _cao('sidebar_course_style', 'right');
$column_classes = rizhuti_v2_column_classes($sidebar);

get_header();

?>

<div id="course-container" class="container">
	<div class="row">
		<div class="<?php echo esc_attr($column_classes[0]); ?>">
			<div class="content-area">
				<?php while (have_posts()) : the_post();
					get_template_part('course/templates/content');
				endwhile; ?>
			</div>
		</div>
		<?php if ($sidebar != 'none') : ?>
			<div class="<?php echo esc_attr($column_classes[1]); ?>">
				<aside id="secondary" class="widget-area">
					<?php dynamic_sidebar('course_post'); ?>
				</aside>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>