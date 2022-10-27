<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-23 08:09:42 +0800
 * @Path           : /wp-content/themes/rowfish/templates/global/term-bar.php
 * @Description    : 列表页顶部背景
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$term_data = rf_get_term_top();
if (is_null($term_data['image'])) return;
?>
<div class="term-bar <?php echo $term_data['type']; ?>">
    <div class="term-bg lazyload visible blur scale-12" data-bg="<?php echo esc_url($term_data['image']); ?>"></div>
    <div class="container m-auto">
        <?php
        if ($term_data['type'] === 'page') {
            echo '<h1 class="term-title">' . get_the_title() . '</h1>';
            global $post;
            $post_content = is_post_type_archive('docs') ? docspress()->get_docs_page_content() : $post->post_content;
            $content = apply_filters('the_content', $post_content);
            if ($post && strlen(trim(strip_tags($content))) > 0) {
                echo '<p class="term-description">' . wp_trim_words(strip_shortcodes($content), '146', '...') . '</p>';
            }
        } elseif ($term_data['archive']) {
            if ('series' == $term_data['type']) {
                the_archive_title('<h1 class="term-title"><span class="badge badge-pill badge-primary-lighten mr-2">' . esc_html__('专题', 'rizhuti-v2') . '</span>', '</h1>');
            } elseif ('course_series' == $term_data['type']) {
                the_archive_title('<h1 class="term-title"><span class="badge badge-pill badge-primary-lighten mr-2">' . esc_html__('专题课程', 'rizhuti-v2') . '</span>', '</h1>');
            } else {
                the_archive_title('<h1 class="term-title">', '</h1>');
            }
            if (!empty($term_data['description'])) {
                echo '<p class="term-description">' . $term_data['description'] . '</p>';
            } else if (is_post_type_archive('docs')) {
                echo '<p class="term-description">' . wp_trim_words(strip_shortcodes(docspress()->get_docs_page_content()), '146', '...') . '</p>';
            }
        } elseif (is_search()) {
            echo '<h1 class="term-title">' . sprintf(esc_html__('（%s）搜索到 %s 个结果', 'rizhuti-v2'), get_search_query(), $wp_query->found_posts) . '</h1>';
        } ?>
    </div>
</div>