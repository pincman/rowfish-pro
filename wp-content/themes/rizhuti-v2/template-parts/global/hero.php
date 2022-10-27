<?php
$format   = get_post_format() ? get_post_format() : 'image';
$bg_image = _get_post_thumbnail_url(null, 'full');
?>

<div class="hero lazyload visible" data-bg="<?php echo esc_url($bg_image); ?>">
<?php if (is_singular() && $format == 'image') {
    echo '<div class="container">';
    get_template_part('template-parts/content/entry-header');
    echo '</div>';
} else {
    get_template_part('template-parts/content/hero-'.$format);
}?>
</div>