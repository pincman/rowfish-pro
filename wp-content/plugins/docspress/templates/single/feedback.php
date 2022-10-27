<?php
/**
 * Single docs feedback template
 *
 * This template can be overridden by copying it to yourtheme/docspress/single/feedback.php.
 *
 * @author  nK
 * @package docspress/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! docspress()->get_option( 'show_feedback_buttons', 'docspress_single', true ) ) {
    return;
}

// phpcs:disable
$show_counts = docspress()->get_option( 'show_feedback_buttons_likes', 'docspress_single', true );

?>

<div class="docspress-single-feedback">
    <?php
    $positive       = 0;
    $negative       = 0;
    $positive_title = '';
    $negative_title = '';

    if ( $show_counts ) {
        $positive = (int) get_post_meta( get_the_ID(), 'positive', true );
        $negative = (int) get_post_meta( get_the_ID(), 'negative', true );

        // translators: %s - likes number.
        $positive_title = $positive ? sprintf( _n( '%d person found this useful', '%d persons found this useful', $positive, 'docspress' ), number_format_i18n( $positive ) ) : __( 'No votes yet', 'docspress' );

        // translators: %s - dislikes number.
        $negative_title = $negative ? sprintf( _n( '%d person found this not useful', '%d persons found this not useful', $negative, 'docspress' ), number_format_i18n( $negative ) ) : __( 'No votes yet', 'docspress' );
    }
    // phpcs:enable
    ?>

    <div>
        <?php echo esc_html__( 'Was this page helpful?', 'docspress' ); ?>
    </div>

    <div class="docspress-single-feedback-vote">
        <a href="#" class="docspress-btn" data-id="<?php the_ID(); ?>" data-type="positive" title="<?php echo esc_attr( $positive_title ); ?>">
            <?php echo esc_html__( 'Yes', 'docspress' ); ?>

            <?php if ( $positive ) { ?>
                <span class="badge"><?php echo esc_html( number_format_i18n( $positive ) ); ?></span>
            <?php } ?>
        </a>
        <a href="#" class="docspress-btn" data-id="<?php the_ID(); ?>" data-type="negative" title="<?php echo esc_attr( $negative_title ); ?>">
            <?php echo esc_html__( 'No', 'docspress' ); ?>

            <?php if ( $negative ) { ?>
                <span class="badge"><?php echo esc_html( number_format_i18n( $negative ) ); ?></span>
            <?php } ?>
        </a>
    </div>
</div>
