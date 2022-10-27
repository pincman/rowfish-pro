<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */

get_header();

?>

<div class="container">
	<div class="row">
		<div class="col-12">
			<div class="content-area">
				<?php while ( have_posts() ) : the_post();?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
				  <div class="container">
				    <?php get_template_part( 'template-parts/content/entry-header' );?>
				    <div class="entry-wrapper">
				      <div class="entry-content u-text-format u-clearfix">

				      	<div class="entry-attachment">
					       <?php $image_size = apply_filters( 'wporg_attachment_size', 'large' ); 
					             echo wp_get_attachment_image( get_the_ID(), $image_size ); ?>
					 
					           <?php if ( has_excerpt() ) : ?>
					       
					           <div class="entry-caption">
					                 <?php the_excerpt(); ?>
					           </div><!-- .entry-caption -->
					       <?php endif; ?>
						</div><!-- .entry-attachment -->

				        <?php
				        if ($copyright = _cao('single_copyright')) {
				          echo '<div class="post-note alert alert-info mt-4" role="alert">' . $copyright . '</div>';
				        }?>
				      </div>
				    </div>
				  </div>
				</article>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>