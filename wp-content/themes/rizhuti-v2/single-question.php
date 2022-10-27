<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */

$sidebar = 'right';
$column_classes = rizhuti_v2_column_classes( $sidebar );
get_header();

?>

<div class="container">
	<div class="row">
		<div class="<?php echo esc_attr( $column_classes[0] ); ?>">
			<div class="content-area">
				<?php while ( have_posts() ) : the_post();
					get_template_part( 'template-parts/content/single-question');
				endwhile; ?>
			</div>
		</div>
		<?php if ( $sidebar != 'none' ) : ?>
			<div class="<?php echo esc_attr( $column_classes[1] ); ?>">
				<aside id="secondary" class="widget-area">
				<?php get_template_part( 'template-parts/global/widget-question'); ?>
				</aside>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php 
wp_enqueue_script('question');
get_footer(); 
?>