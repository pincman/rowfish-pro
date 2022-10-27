<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:35:18 +0800
 * @Path           : /wp-content/themes/rowfish/course/templates/series.php
 * @Description    : 课程专题页面模板
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */



get_header();
$sidebar = rizhuti_v2_sidebar();
$column_classes = rizhuti_v2_column_classes($sidebar);

$series = get_queried_object();
global $wp_query;
?>
<div class="archive series container">
    <div class="row">
        <div class="<?php echo esc_attr($column_classes[0]); ?>">
            <div class="content-area archive-list">
                <div class="row posts-wrapper scroll">
                    <?php if (have_posts()) : ?>
                    <?php
                        /* Start the Loop */
                        while (have_posts()) : the_post();
                            get_template_part('course/templates/item');
                        endwhile;
                    else :
                        get_template_part('course/templates/none');

                    endif;
                    ?>
                </div>
                <?php rizhuti_v2_pagination(5); ?>
            </div>
        </div>
        <?php if ($sidebar != 'none') : ?>
            <div class="<?php echo esc_attr($column_classes[1]); ?>">
                <aside id="secondary" class="widget-area">
                    <?php dynamic_sidebar('cat_sidebar'); ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
