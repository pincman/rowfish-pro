<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-18 10:09:50 +0800
 * @Updated_at     : 2021-11-19 05:15:57 +0800
 * @Path           : /wp-content/themes/rowfish/pages/page-modular.php
 * @Description    : 主题首页模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

/**
 * Template Name: 网站模块化首页
 */

get_header();
$top_attrs = '';
if (_cao('is_home_top_back_image') == '1') {
    $top_attrs = ' class="section home_top_bg_image"';
    if (!empty(_cao('home_top_back_image_light'))) {
        $top_attrs .= ' data-background-light="' . _cao('home_top_back_image_light') . '"';
    }
    if (!empty(_cao('home_top_back_image_dark'))) {
        $top_attrs .= ' data-background-dark="' . _cao('home_top_back_image_dark') . '"';
    }
} else {
    $top_attrs = ' class="section"';
}
?>
<div <?php echo $top_attrs; ?>><?php dynamic_sidebar('home_top'); ?></div>
<?php dynamic_sidebar('modules'); ?>

<?php get_footer(); ?>