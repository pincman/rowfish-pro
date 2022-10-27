<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package rizhuti-v2
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<?php
$termObj = get_queried_object();
$taxonomy = (!empty($termObj) && !empty($termObj->taxonomy)) ? $termObj->taxonomy : null;
$fullheight = !is_front_page() && !is_home();
// $fullheight = $taxonomy === 'question_category' || $taxonomy === 'question_tag' || get_post_type() === 'question' || get_post_type() === 'docs';
?>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="app" class="site<?php if ($fullheight) : ?> full-content<?php else : ?> body-back<?php endif; ?>">
		<?php get_template_part('template-parts/global/header-menu');
		if (rizhuti_v2_show_hero()) {
			get_template_part('template-parts/global/hero');
		}
		if (is_archive() || is_search()) {
			get_template_part('template-parts/global/term-bar');
		} ?>
		<main id="main" role="main" class="site-content">