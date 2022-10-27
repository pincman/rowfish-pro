<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-23 14:57:01 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/archive.php
 * @Description    :  覆盖docspress文档列表页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
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
            $used = [];
            $cat_args = array(
                'hide_empty' => true,
            );
            $categories = get_terms('docs_category', $cat_args);
            $query_args = [
                [
                    'query' => [
                        'post_status' => 'publish',
                        'post_type'   => 'docs',
                        'post_parent'    => 0,
                        'sort_column' => isPostTypesOrder() ? 'menu_order' : 'date',
                        'ignore_sticky_posts' => true,
                        'meta_key'     => 'is_course_docs',
                        'meta_value'   => true,
                        'meta_compare' => '!=',
                        'tax_query' => [[
                            'taxonomy' => 'docs_category',
                            'field'    => 'term_id',
                            'terms' => array_map(function ($cat) {
                                return $cat->term_id;
                            }, $categories),
                            'operator' => 'NOT IN'
                        ]],
                    ]
                ]
            ];
            foreach ($categories as $category) {
                $query_args[] = [
                    'category' => $category,
                    'query' => [
                        'post_status' => 'publish',
                        'post_type'   => 'docs',
                        'post_parent'    => 0,
                        'sort_column' => isPostTypesOrder() ? 'menu_order' : 'date',
                        'ignore_sticky_posts' => true,
                        'meta_key'     => 'is_course_docs',
                        'meta_value'   => true,
                        'meta_compare' => '!=',
                        'tax_query' => [
                            [
                                'taxonomy' => 'docs_category',
                                'field'    => 'term_id',
                                'terms' => $category->term_id,
                            ]
                        ],
                    ]
                ];
            }
            foreach ($query_args as $arg) :
                $query = new WP_Query($arg['query']);
                if ($query->have_posts()) :
                    if (isset($arg['category'])) :
            ?>
                        <div class="col-12">
                            <h6 class="pt-4"><?php echo $arg['category']->name; ?><span class="badge badge-pill badge-primary-lighten ml-2"><?php echo $arg['category']->count; ?></span></h6>
                        </div>
                        <?php
                    endif;
                    while ($query->have_posts()) :
                        $query->the_post();
                        if (!in_array($post->ID, $used)) :
                            array_push($used, $post->ID);
                            $articles       = get_pages(
                                array(
                                    'child_of'  => get_the_ID(),
                                    'post_type' => 'docs',
                                )
                            );
                            $articles_count = count($articles);
                            $summary = get_post_meta(get_the_ID(), 'content_summary', true);
                            if (empty($summary)) {
                                $summary = get_the_content();
                            }
                        ?>
                            <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                                <div class="card mb-lg-4 mb-2 card-hover">
                                    <div class="d-flex justify-content-between align-items-center p-3 doc-card">
                                        <a href="<?php the_permalink(); ?>" title="<?php echo the_title(); ?>">
                                            <div class="d-flex align-items-center">
                                                <?php the_post_thumbnail('docspress_archive'); ?>
                                                <div class="ml-3">
                                                    <b class=""><?php echo the_title(); ?></b>
                                                    <p class="mb-0 small text-muted"><?php echo wp_trim_words(strip_shortcodes($summary), '46', '...'); ?></p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php docspress()->get_template_part('archive/loop-articles'); ?>
                                </div>
                            </div>
            <?php
                        endif;
                    endwhile;
                endif;
            endforeach;
            ?>
        </div>
    </article>
</div>

<?php

docspress()->get_template_part('global/wrap-end');
