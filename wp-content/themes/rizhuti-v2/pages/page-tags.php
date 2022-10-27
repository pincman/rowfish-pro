<?php
/**
 * Template Name: 标签云页面模板
 */

get_header();
$bg_image = _get_post_thumbnail_url(null, 'full');
?>

<div class="hero lazyload visible page-top-hero" data-bg="<?php echo esc_url($bg_image); ?>">
	<div class="container">
		<header class="entry-header">
		<?php rizhuti_v2_entry_title(array('link' => false, 'tag' => 'h1'));
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;?>
		</header>
	</div>
</div>

<div class="container">
	<div class="mt-lg-4 mt-3">
		<div class="row">
			<?php $tags = get_tags(array(
			  // 'get'=>'all',
			  'taxonomy' => 'post_tag',
			  'orderby' => 'count',
			  'number' => 80, //显示30个标签
			  'hide_empty' => true // for development
			));
			foreach ( $tags as $tag ) : $tag_link = get_tag_link( $tag->term_id ); ?>
			<div class="col-xl-3 col-md-4 col-6 tags">
			  <div class="card mb-lg-4 mb-2 card-hover">
			    <div class="p-3">
			      <a href="<?php echo $tag_link;?>" rel="tag" title="<?php echo $tag->name;?>">
				      <div class="d-flex align-items-center">
				        <dir class="m-0 tags-substr"><?php echo mb_substr( $tag->name,0,1);?></dir>
				        <div class="ml-3">
				          <b class=""><?php echo $tag->name;?></b>
				          <p class="mb-0 small text-muted"><span class="mr-2"><b class="b mr-1"><?php echo $tag->count;?></b>个文章</span></p>
				        </div>
				      </div>
			      </a>
			    </div>
			  </div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
</div>

<?php get_footer();?>

