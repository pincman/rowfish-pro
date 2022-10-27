<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:29:21 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/global/wrap-start.php
 * @Description    : 在小屏幕下为自带的主题添加上一些css匹配类
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */



if (!defined('ABSPATH')) {
    exit;
}

get_header('docs');

// phpcs:disable
$theme_class = '';

// additional class for default theme to add fix styles.
$current_theme = get_template();
if (in_array($current_theme, array('twentyseventeen', 'twentysixteen', 'twentyfifteen'), true)) {
    $theme_class = ' docspress_theme_' . $current_theme;
}
// phpcs:enable

?>
<div id="primary" class="content-area<?php echo esc_attr($theme_class); ?> archive-list">
    <main id="main" class="site-main" role="main">