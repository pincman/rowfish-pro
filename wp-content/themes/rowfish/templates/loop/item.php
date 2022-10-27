<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 04:00:26 +0800
 * @Path           : /wp-content/themes/rowfish/templates/loop/item.php
 * @Description    : 文章列表的项目模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$is_search = isset($args['search']) && $args['search'];
$no_big = (isset($args['no-big']) && $args['no-big']) || $is_search;
$no_author = isset($args['no-author']) && $args['no-author'];
$col_classes = (rizhuti_v2_sidebar() != 'none' || $no_big) ? 'col-lg-12' : 'col-lg-6 col-12';
if (is_author()) $col_classes = 'col-lg-6 col-12';
$info = rf_get_post_info();
$summary = empty($info['summary']) ? rizhuti_v2_excerpt() : wp_trim_words(strip_shortcodes($info['summary']), '46', '...');
$thumbnail_size = ['width' => 200, 'height' => 120];
$is_big_block = rizhuti_v2_sidebar() != 'none' && $info['block_style'] == 'big' && !is_author() && !$no_big;
if (!$no_big) {
	if ($is_big_block) {
		$col_classes = $col_classes . ' big-block';
		$thumbnail_size = ['width' => 800, 'height' => 250];
		if ($info['merge_thumbnail']) {
			$thumbnail_size['height'] = 300;
			$col_classes = $col_classes . ' merge-thumbnail';
		}
	} else {
		$col_classes = $col_classes . ' small-block';
		$thumbnail_size = ['width' => 250, 'height' => 190];
	}
}
if ($is_search) {
	$col_classes = 'col-lg-5ths col-lg-3 col-md-4 col-12';
	$thumbnail_size = 'thumbnail';
	$info['merge_thumbnail'] = false;
}
?>

<div class="<?php echo esc_attr($col_classes); ?>">

	<article id="post-<?php the_ID(); ?>" <?php post_class($is_search ? 'post post-grid' : 'post post-list'); ?>>
		<?php if (_cao('is_post_list_price', true)) {
			echo get_post_meta_vip_price();
		} ?>
		<?php if ($is_search) :
			$meta_icon = 'fas fa-sticky-note';
			$meta_icon_color = 'primary';
			$meta_icon_text = '文章';
			if (get_post_type() == 'docs') {
				$meta_icon = 'fas fa-book-open';
				$meta_icon_color = 'warning';
				$meta_icon_text = '文档';
			}
		?>
			<span class='badge badge-<?php echo $meta_icon_color; ?>  meta-search-icon' data-toggle='tooltip' data-placement='right' data-delay='0' title='<?php echo $meta_icon_text; ?>'><i class='<?php echo $meta_icon; ?>'></i></span>
		<?php endif; ?>
		<?php echo rf_get_post_media(null, $thumbnail_size); ?>
		<?php if ($info['merge_thumbnail']) : ?>
			<div class="entry-wrapper">
				<header class="entry-header">
					<?php rizhuti_v2_entry_title(array('link' => true)); ?>
				</header>
				<div class="entry-excerpt"><?php echo rizhuti_v2_excerpt(); ?></div>
			</div>
		<?php else : ?>
			<div class="entry-wrapper">
				<?php if (_cao('is_post_list_category', 1) && count(get_the_category()) > 0) {
					rizhuti_v2_category_dot(2);
				} ?>
				<header class="entry-header">
					<?php rizhuti_v2_entry_title(array('link' => true)); ?>
				</header>

				<div class="entry-excerpt"><?php echo $summary; ?></div>

				<div class="entry-footer">
					<?php rizhuti_v2_entry_meta(
						array(
							'author' => _cao('is_post_list_author', 1) && !is_author() && !$no_author,
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
		<?php endif; ?>
	</article>

</div>