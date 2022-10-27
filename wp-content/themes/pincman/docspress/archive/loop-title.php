<?php

/**
 * Docs archive loop title template
 *
 * This template can be overridden by copying it to yourtheme/docspress/archive/loop-title.php.
 *
 * @author  nK
 * @package docspress/Templates
 * @version 1.0.0
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