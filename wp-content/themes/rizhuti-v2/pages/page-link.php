<?php
/**
 * Template Name: 网址导航页面模板
 */

get_header();
$bg_image = _get_post_thumbnail_url(null, 'full');
$terms = get_terms( 'link_category', array(
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'ASC', //DESC
) );
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
<?php 
foreach ($terms as $key => $item) : $bookmarks = get_bookmarks( array('orderby' => 'link_rating','category' => $item->term_id) );?>
	<h6 class="pt-4" id="bookmarks-<?php echo $item->term_id;?>"><?php echo $item->name;?><span class="badge badge-pill badge-primary-lighten ml-2"><?php echo $item->count;?></span></h6>
	<div class="row">
	<?php foreach ( $bookmarks as $bookmark ) : 
		$link_image = (!empty($bookmark->link_image)) ? $bookmark->link_image : get_template_directory_uri().'/assets/img/link.png';
		$nofollow = (!empty($bookmark->link_rel)) ? 'nofollow noopener noreferrer' : 'noopener noreferrer' ;
		?>
		<div class="col-xl-4 col-lg-6 col-md-6 col-12">
		  <div class="card mb-lg-4 mb-2 card-hover">
		    <div class="d-flex justify-content-between align-items-center p-3">
		      <a target="<?php echo $bookmark->link_target;?>" href="<?php echo $bookmark->link_url;?>" rel="<?php echo $nofollow;?>" title="<?php echo $bookmark->link_name;?>">
			      <div class="d-flex align-items-center">
			        <img src="<?php echo $link_image;?>" alt="<?php echo $bookmark->link_name;?>" class="avatar-lg">
			        <div class="ml-3">
			          <b class=""><?php echo $bookmark->link_name;?></b>
			          <p class="mb-0 small text-muted"><?php echo $bookmark->link_description;?></p>
			        </div>
			      </div>
		      </a>
		    </div>
		  </div>
		</div>
	<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</div>

<?php get_footer();?>

