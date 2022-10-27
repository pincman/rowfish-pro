<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 04:04:22 +0800
 * @Path           : /wp-content/themes/rowfish/templates/global/hero.php
 * @Description    : 半高背景组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$hero_image = rf_get_hero_image();
$format   = get_post_format() ? get_post_format() : 'image';
?>

<div class="hero lazyload visible" data-bg="<?php echo esc_url($hero_image); ?>">
    <?php if (is_singular() && $format == 'image') {
        echo '<div class="container">';
        get_template_part('template-parts/content/entry-header');
        echo '</div>';
    } else {
        get_template_part('template-parts/content/hero-' . $format);
    } ?>
</div>