<?php
global $post;
$post_id = $post->ID; //文章ID
$format   = get_post_format() ? get_post_format() : 'image';
$hero_image = null;
$post_hero_image = get_post_meta($post_id, 'hero_image', true);
$admin_hero_image = null;
$admin_options_images = _cao('hero_images');
if (is_array($admin_options_images) && count($admin_options_images) > 0) {
    $admin_images = array_filter($admin_options_images, function ($img) {
        return $img && is_array($img) && $img['url'] && $img['url'] !== '';
    });
    if (count($admin_images)) {
        $admin_hero_image = $admin_images[rand(0, count($admin_images) - 1)];
    }
}
if ($post_hero_image) $hero_image = $post_hero_image;
else if ($admin_hero_image && isset($admin_hero_image['url'])) $hero_image = $admin_hero_image['url'];
else $hero_image = pm_get_post_thumbnail_url(null, 'full');
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