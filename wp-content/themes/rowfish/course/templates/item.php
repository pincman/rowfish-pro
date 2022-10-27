<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:56:17 +0800
 * @Path           : /wp-content/themes/rowfish/course/templates/item.php
 * @Description    : 课程列表中的课程项目模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$col_classes = 'col-lg-5ths col-lg-3 col-md-4 col-12';
$no_author = isset($args['no-author']) && $args['no-author'];
$info = rf_get_post_info();
$summary = empty($info['summary']) ? rizhuti_v2_excerpt() : wp_trim_words(strip_shortcodes($info['summary']), '46', '...');
$is_search = isset($args['search']) && $args['search'];
?>

<div class="<?php echo esc_attr($col_classes); ?>">

	<article id="post-<?php the_ID(); ?>" <?php post_class('post post-grid'); ?>>

		<div class="entry-icons"><?php rf_show_course_status_icon(); ?><?php rf_show_course_level_icon(); ?></div>

		<?php if ($is_search) : ?>
			<span class='badge badge-success meta-search-icon' data-toggle='tooltip' data-placement='right' data-delay='0' title='课程'><i class='fas fa-video'></i></span>
		<?php endif; ?>
		<?php echo rf_get_post_media(null, 'thumbnail'); ?>
		<div class="entry-wrapper">

			<?php if (_cao('is_course_list_category', 1)) {
				rf_course_category_dot(2);
			} ?>

			<header class="entry-header post-title-flex">
				<?php rizhuti_v2_entry_title(array('link' => true)); ?>
				<?php rf_course_serie_dot(1); ?>
			</header>

			<?php if (_cao('is_course_list_excerpt', 1)) {
				echo '<div class="entry-excerpt">' . $summary . '</div>';
			} ?>

			<div class="entry-footer">
				<?php rf_show_course_entry_meta(
					array(
						'author' => _cao('is_course_list_author', 1) && !is_author() && !$no_author,
						'edit' => _cao('is_course_grid_edit', 1),
						'favnum' => _cao('is_course_list_favnum', 1),
						'views' => _cao('is_course_list_views', 1),
						'shop' => _cao('is_course_list_shop', 1),
						'date' => _cao('is_course_list_date', 1),
					),
					$info
				); ?>
			</div>
		</div>
	</article>

</div>