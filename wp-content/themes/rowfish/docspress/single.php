<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:26:25 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/single.php
 * @Description    : 覆盖docspress文档页面
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!defined('ABSPATH')) {
    exit;
}

docspress()->get_template_part('global/wrap-start');

while (have_posts()) :
    the_post(); ?>
    <div class="container article-content entry-wrapper doc-single">
        <article id="post-<?php the_ID(); ?>" <?php post_class('docspress-single' . (docspress()->get_option('ajax', 'docspress_single', true) ? ' docspress-single-ajax' : '')); ?>>

            <?php docspress()->get_template_part('single/sidebar'); ?>

            <div class="docspress-single-content">
                <?php
                docspress()->get_template_part('single/content-breadcrumbs');

                // docspress()->get_template_part('single/content-title');
                ?>
                <div class="entry-content">
                    <?php
                    the_content();

                    wp_link_pages(
                        array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'docspress'),
                            'after'  => '</div>',
                        )
                    );

                    docspress()->get_template_part('single/content-articles');
                    ?>
                </div><!-- .entry-content -->

                <?php

                docspress()->get_template_part('single/footer');

                // docspress()->get_template_part('single/adjacent-links');

                docspress()->get_template_part('single/feedback');

                docspress()->get_template_part('single/feedback-suggestion');

                if (docspress()->get_option('show_comments', 'docspress_single', true)) {
                    docspress()->get_template_part('single/comments');
                }

                ?>
            </div><!-- .docspress-single-content -->
        </article><!-- #post-## -->
    </div>
<?php

endwhile;

docspress()->get_template_part('global/wrap-end');
