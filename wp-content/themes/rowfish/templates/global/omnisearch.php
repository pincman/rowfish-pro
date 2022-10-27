<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-15 07:43:57 +0800
 * @Updated_at     : 2021-11-19 04:05:11 +0800
 * @Path           : /wp-content/themes/rowfish/templates/global/omnisearch.php
 * @Description    : 顶部搜索弹出框组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
?>
<div id="omnisearch" class="omnisearch">
    <div class="container">
        <form class="omnisearch-form" method="get" action="<?php echo home_url(); ?>/">
            <div class="form-group">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="search-ajax-input form-control" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_html__('输入关键词 回车搜索...', 'rizhuti-v2'); ?>" autocomplete="off">

                </div>
            </div>
        </form>
        <div class="omnisearch-suggestions">
            <div class="search-keywords">
                <?php
                $args = array('taxonomy' => array('category'), 'number' => 18, 'orderby' => 'name', 'smallest' => 14, 'largest' => 14, 'unit' => 'px', 'separator' => "<span class='badge badge-secondary' data-toggle='tooltip' data-placement='right' data-delay='0' title='文章分类'><i class='far fa-folder-open'></i></span>\n");
                wp_tag_cloud($args);
                echo "<span class='badge badge-secondary' data-toggle='tooltip' data-placement='right' data-delay='0' title='文章分类'><i class='far fa-folder-open'></i></span>\n";
                $args = array('taxonomy' => array('post_tag'), 'number' => 18, 'orderby' => 'name', 'smallest' => 14, 'largest' => 14, 'unit' => 'px', 'separator' => "<span class='badge badge-danger' data-toggle='tooltip' data-placement='right' data-delay='0' title='文章标签'><i class='fas fa-tag'></i></span>\n");
                wp_tag_cloud($args);
                echo "<span class='badge badge-danger' data-toggle='tooltip' data-placement='right' data-delay='0' title='文章标签'><i class='fas fa-tag'></i></span>\n";
                $args = array('taxonomy' => array('course_category'), 'number' => 18, 'orderby' => 'name', 'smallest' => 14, 'largest' => 14, 'unit' => 'px', 'separator' => "<span class='badge badge-success' data-toggle='tooltip' data-placement='right' data-delay='0' title='课程分类'><i class='fas fa-video'></i></span>\n");
                wp_tag_cloud($args);
                echo "<span class='badge badge-success' data-toggle='tooltip' data-placement='right' data-delay='0' title='课程分类'><i class='fas fa-video'></i></span>\n";
                ?>
            </div>
            <?php do_action('omnisearch_body'); ?>
        </div>
    </div>
</div>