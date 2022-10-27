<?php
/**
 * Docs archive main page template
 *
 * This template can be overridden by copying it to yourtheme/docspress/archive/page.php.
 *
 * @author  nK
 * @package docspress/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="entry-content">
        <?php docspress()->get_template_part( 'archive/description' ); ?>

        <div class="docspress-archive">
            <ul class="docspress-archive-list">
                <?php
                // phpcs:ignore
                $current_term = false;

                if ( have_posts() ) :
                    while ( have_posts() ) :
                        the_post();

                        // phpcs:ignore
                        $terms = wp_get_post_terms( get_the_ID(), 'docs_category' );
                        if (
                            $terms &&
                            ! empty( $terms ) &&
                            isset( $terms[0]->name ) &&
                            $current_term !== $terms[0]->name
                        ) {
                            // phpcs:ignore
                            $current_term = $terms[0]->name;
                            ?>
                            <li class="docspress-archive-list-category">
                                <?php echo esc_html( $terms[0]->name ); ?>
                            </li>
                            <?php
                        }

                        ?>
                        <li class="docspress-archive-list-item">
                            <?php docspress()->get_template_part( 'archive/loop-title' ); ?>
                            <?php docspress()->get_template_part( 'archive/loop-articles' ); ?>
                        </li>
                        <?php
                    endwhile;
                endif;
                ?>
            </ul>
        </div>

        <?php
            wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . __( 'Pages:', 'docspress' ),
                    'after'  => '</div>',
                )
            );
            ?>
    </div>
</article>
