<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */

$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes( $sidebar );
$part = ( !is_close_site_shop() && _cao('is_single_shop_template',true) && _get_post_shop_type() >=3 ) ? 'single-shop' : 'single' ;

get_header();

?>

<div class="container">
	<div class="row">
		<div class="<?php echo esc_attr( $column_classes[0] ); ?>">
			<div class="content-area">
				<?php while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/content/'.$part);
				endwhile; ?>
			</div>
		</div>
		<?php if ( $sidebar != 'none' ) : ?>
			<div class="<?php echo esc_attr( $column_classes[1] ); ?>">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>