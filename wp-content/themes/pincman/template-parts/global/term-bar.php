<?php
// global $post;
// $post_ID = $post->ID;
// $image = (_cao('is_archive_top_bg_one', '1')) ? _cao('archive_top_bg_one_img') : pm_get_post_thumbnail_url();
// $post_image_meta = get_post_meta($post_ID, 'enabled_top_image', true);
// if (get_post_type() === 'page') {
//     $is_show = $post_image_meta === '' || $post_image_meta === '1';
// } else {
//     $is_show = $category_image_meta === '' || $category_image_meta === '1';
// }

$termObj = get_queried_object();
$category_image_meta = get_term_meta(get_queried_object_id(), 'enabled_top_image', true);
$is_show = $category_image_meta === '' || $category_image_meta === '1';
if (empty($termObj) || empty($termObj->taxonomy)) return;
// $taxonomy = (!empty($termObj) && !empty($termObj->taxonomy)) ? $termObj->taxonomy : 'category';
if (is_post_type_archive('question') || $taxonomy == 'question_tag' || $taxonomy == 'question_category' || get_post_type() == 'docs' || !$is_show) {
    return;
}
$taxonomy =  $termObj->taxonomy;
$top_image = get_term_meta($termObj->term_id, 'bg-image', true);
$admin_top_images = _cao('top_images');
$meta_top_image = get_term_meta($termObj->term_id, 'top_bar_image', true);
if (is_array($admin_top_images) && count($admin_top_images) > 0) {
    $admin_images = array_filter($admin_top_images, function ($img) {
        return $img && is_array($img) && $img['url'] && $img['url'] !== '';
    });
    if (count($admin_images)) {
        $admin_top_image = $admin_images[rand(0, count($admin_images) - 1)];
    }
}
if ($meta_top_image) $top_image = $meta_top_image;
else if ($admin_top_image && isset($admin_top_image['url'])) $top_image = $admin_top_image['url'];
?>
<div class="term-bar <?php echo $taxonomy; ?>">
    <div class="term-bg lazyload visible blur scale-12" data-bg="<?php echo esc_url($top_image); ?>"></div>
    <div class="container m-auto">
        <?php if (is_archive()) {
            if ('series' == $taxonomy) {
                the_archive_title('<h1 class="term-title"><span class="badge badge-pill badge-primary-lighten mr-2">' . esc_html__('分季教程', 'rizhuti-v2') . '</span>', '</h1>');
                if (!empty($termObj->description)) {
                    echo '<p class="term-description">' . $termObj->description . '</p>';
                }
            } else {
                the_archive_title('<h1 class="term-title">', '</h1>');
                if (!empty($termObj->description)) {
                    echo '<p class="term-description">' . $termObj->description . '</p>';
                }
            }
        } elseif (is_search()) {
            echo '<h1 class="term-title">' . sprintf(esc_html__('（%s）搜索到 %s 个结果', 'rizhuti-v2'), get_search_query(), $wp_query->found_posts) . '</h1>';
        } ?>
    </div>
</div>