<?php

/**
 * Template Name: 专题集合页面模板
 */

get_header();
$bg_image = pm_get_post_thumbnail_url(null, 'full');
?>

<div class="hero lazyload visible page-top-hero" data-bg="<?php echo esc_url($bg_image); ?>">
	<div class="container">
		<header class="entry-header">
			<?php rizhuti_v2_entry_title(array('link' => false, 'tag' => 'h1'));
			while (have_posts()) : the_post();
				the_content();
			endwhile; ?>
		</header>
	</div>
</div>

<div class="container">
	<?php

	// link_category
	$terms = get_terms('series', array(
		'hide_empty' => true,
		'orderby' => 'ID',
		'order' => 'DESC', //DESC ASC
	));

	// var_dump($terms);die;
	?>

	<div class="module posts-wrapper post-cms-title mt-lg-4 mt-3">
		<div class="row">
			<?php foreach ($terms as $item) {
				$category = get_term_by('ID', $item->term_id, 'series');
				// 查询
				$tax_query = array('relation' => 'OR', array(
					'taxonomy' => 'series',
					'field'    => 'term_id',
					'terms' => $item->term_id,
					'operator' => 'IN'
				));
				$_args = array(
					'tax_query' => $tax_query,
					'ignore_sticky_posts' => true,
					'post_status'         => 'publish',
					'posts_per_page'      => 3,
				);
				$PostData = new WP_Query($_args);
				$i = 0;
				echo '<div class="col-lg-4 col-12"><div class="card mb-4">';
				echo '<div class="cat-info">';
				echo '<span class="text-light">' . sprintf(__('共%s个文章', 'rizhuti-v2'), $category->count) . '</span>';
				echo '<h3><a href="' . esc_url(get_term_link($category->term_id)) . '" rel="category">' . $category->name . '<span class="badge badge-danger ml-2"></span></a></h3>';
				echo '</div>';
				while ($PostData->have_posts()) : $PostData->the_post();
					$i++;
					if ($i == 1) {
						echo pm_get_post_media(null, 'thumbnail');
						echo '<div class="card-body">';
						rizhuti_v2_entry_title(array('link' => true, 'tag' => 'h5', 'classes' => 'card-title text-nowrap-ellipsis'));
						echo '<p class="card-text text-muted small text-nowrap-ellipsis">' . rizhuti_v2_excerpt() . '</p>';
						echo '</div>';
						echo '<ul class="list-group list-group-flush mt-0 mb-2">';
					} else {
						echo '<li class="list-group-item py-2 text-nowrap-ellipsis"><a href="' . esc_url(get_permalink()) . '" title="' . get_the_title() . '" rel="bookmark">' . get_the_title() . '</a></li>';
					}
				endwhile;
				wp_reset_postdata();
				echo '<li class="list-group-item py-2 text-nowrap-ellipsis"><a href="' . esc_url(get_term_link($category->term_id)) . '" rel="category" class="btn btn-light btn-block">' . esc_html__('查看专题', 'rizhuti-v2') . '<i class="fa fa-angle-right ml-1"></i></a></li>';
				echo '</ul></div></div>';
			} ?>
		</div>

	</div>

</div>

<?php get_footer(); ?>