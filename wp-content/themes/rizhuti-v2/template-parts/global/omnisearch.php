<div id="omnisearch" class="omnisearch">
    <div class="container">
        <form class="omnisearch-form" method="get" action="<?php echo home_url(); ?>/">
            <div class="form-group">
                <div class="input-group input-group-merge input-group-flush">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>

                    <div class="input-group-prepend d-flex align-items-center" style=" max-width: 35%; ">
                        <?php wp_dropdown_categories(array(
                            'hide_empty'       => true,
                            'orderby'          => 'name',
                            'hierarchical'     => true,
                            'depth'     => 1,
                            'id'     => 'omnisearch-cat',
                            'class'     => 'selectpicker',
                            'show_option_none' => esc_html__('全部', 'rizhuti-v2'),
                            'option_none_value' => '',
                        )); ?>
                    </div>
                    <input type="text" class="search-ajax-input form-control" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_html__('输入关键词 回车搜索...', 'rizhuti-v2'); ?>" autocomplete="off">

                </div>
            </div>
        </form>
        <div class="omnisearch-suggestions">
            <div class="search-keywords">
                <?php $args = array('taxonomy' => array('post_tag', 'category'), 'number' => 18, 'orderby' => 'name', 'smallest' => 14, 'largest' => 14, 'unit' => 'px',);
                wp_tag_cloud($args); ?>
            </div>
            <?php do_action('omnisearch_body'); ?>
        </div>
    </div>
</div>