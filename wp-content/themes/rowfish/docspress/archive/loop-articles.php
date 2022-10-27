<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:30:16 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/archive/loop-articles.php
 * @Description    : 覆盖文档列表模块
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable
$show            = docspress()->get_option('show_articles', 'docspress_archive', true);
$articles_number = intval(docspress()->get_option('articles_number', 'docspress_archive', 6));

if (-1 === $articles_number) {
    $articles_number = 9999;
}

if (!$show || $articles_number < 1) {
    return;
}

$top_articles = new WP_Query(
    array(
        'post_type'      => 'docs',
        'posts_per_page' => -1, // phpcs:ignore
        'post_parent'    => get_the_ID(),
        'orderby'        => array(
            'menu_order' => 'ASC',
            'date'       => 'DESC',
        ),
        'post_status' => 'publish',
    )
);
$parent_link  = get_permalink();

$count = 0;
// phpcs:enable

if ($top_articles->have_posts()) : ?>

    <ul>
        <?php
        while ($top_articles->have_posts()) :
            $top_articles->the_post();
            if ($count >= $articles_number) {
                break;
            }
            $count++;
        ?>

            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

        <?php endwhile; ?>

        <?php if ($top_articles->post_count > $articles_number) : ?>
            <li class="more">
                <a href="<?php echo esc_url($parent_link); ?>">
                    <?php
                    // translators: %s articles count.
                    printf(esc_html__('+%s More', 'docspress'), intval($top_articles->post_count) - $articles_number);
                    ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>

<?php endif;
wp_reset_postdata(); ?>