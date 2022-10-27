<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-23 08:09:32 +0800
 * @Path           : /wp-content/themes/rowfish/header.php
 * @Description    : 主题顶部导航
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
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
?>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="app" class="site<?php if ($fullheight) : ?> full-content<?php endif; ?>">
		<?php get_template_part('templates/global/header-menu');
		$info = rf_get_post_info();
		if (is_singular('course') && $info['is_course']) {
			get_template_part('course/templates/hero');
		} elseif (rizhuti_v2_show_hero()) {
			get_template_part('templates/global/hero');
		}
		if (is_archive() || is_search() || is_page_template('pages/courses.php')) {
			get_template_part('templates/global/term-bar');
		} ?>
		<main id="main" role="main" class="site-content">