<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */
?>

<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="text" class="form-control" placeholder="<?php echo esc_html__( '输入关键词 回车...', 'rizhuti-v2' ); ?>" autocomplete="off" value="<?php echo esc_attr( get_search_query() ) ?>" name="s" required="required">
</form>