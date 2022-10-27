<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:27:55 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/single/footer.php
 * @Description    : 文档底部组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!defined('ABSPATH')) {
    exit;
}

?>

<footer class="entry-footer">
    <div itemprop="author" itemscope itemtype="https://schema.org/Person">
        <meta itemprop="name" content="<?php echo esc_attr(get_the_author()); ?>" />
        <meta itemprop="url" content="<?php echo esc_attr(get_author_posts_url(get_the_author_meta('ID'))); ?>" />
    </div>

    <meta itemprop="datePublished" content="<?php echo esc_attr(get_the_time('c')); ?>" />
    <!-- <time itemprop="dateModified" datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>">
        <?php
        // translators: %s - last time modified.
        printf(esc_html__('Last modified %s', 'docspress'), esc_html(get_the_modified_date()));
        ?>
    </time> -->
</footer>