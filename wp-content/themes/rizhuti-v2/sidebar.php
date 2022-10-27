<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */

if ( ! is_active_sidebar( 'sidebar' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area">
	<?php dynamic_sidebar( 'sidebar' ); ?>
</aside><!-- #secondary -->
