<?php

/**
 * Docs archive template
 *
 * This template can be overridden by copying it to yourtheme/docspress/archive.php.
 *
 * @author  nK
 * @package docspress/Templates
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

docspress()->get_template_part('global/wrap-start');

?>
<?php
// docspress()->get_template_part('archive/title');
?>
<div class="container">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="row">
            <?php
            $current_term = false;
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    // phpcs:ignore
                    $terms = wp_get_post_terms(get_the_ID(), 'docs_category');
                    if (
                        $terms &&
                        !empty($terms) &&
                        isset($terms[0]->name) &&
                        $current_term !== $terms[0]->name &&
                        empty(get_post_meta(get_the_ID(), 'course', true))
                    ) {
                        // phpcs:ignore
                        $current_term = $terms[0]->name;
            ?> <div class="col-12">
                            <h6 class="pt-4"><?php echo $current_term; ?><span class="badge badge-pill badge-primary-lighten ml-2"><?php echo $terms[0]->count; ?></span></h6>
                        </div>
                    <?php } ?>
                    <?php
                    $articles       = get_pages(
                        array(
                            'child_of'  => get_the_ID(),
                            'post_type' => 'docs',
                        )
                    );
                    $articles_count = count($articles);
                    if (empty(get_post_meta(get_the_ID(), 'course', true))) :
                    ?>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                            <div class="card mb-lg-4 mb-2 card-hover">
                                <div class="d-flex justify-content-between align-items-center p-3 doc-card">
                                    <a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>">
                                        <div class="d-flex align-items-center">
                                            <?php the_post_thumbnail('docspress_archive'); ?>
                                            <div class="ml-3">
                                                <b class=""><?php echo the_title(); ?></b>
                                                <p class="mb-0 small text-muted"><?php echo get_the_content(); ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php docspress()->get_template_part('archive/loop-articles'); ?>
                            </div>
                        </div>
                <?php endif;
                endwhile; ?>
            <?php endif; ?>
        </div>
    </article>
</div>

<?php

docspress()->get_template_part('global/wrap-end');
