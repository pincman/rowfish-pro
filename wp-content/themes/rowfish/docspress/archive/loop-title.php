<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:30:35 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/archive/loop-title.php
 * @Description    : 覆盖文档列表中的标题模块
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */



if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable
$articles       = get_pages(
    array(
        'child_of'  => get_the_ID(),
        'post_type' => 'docs',
    )
);
$articles_count = count($articles);
// phpcs:enable

?>

<a href="<?php the_permalink(); ?>" class="docspress-archive-list-item-title">
    <div class="d-flex align-items-center">
        <?php the_post_thumbnail('docspress_archive'); ?>
        <div class="ml-3">
            <b class=""><?php echo the_title(); ?></b>
            <p class="mb-0 small text-muted"><?php echo get_the_content(); ?></p>
        </div>
    </div>
</a>