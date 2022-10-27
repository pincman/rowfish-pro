<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-11 14:34:54 +0800
 * @Updated_at     : 2021-11-19 05:34:38 +0800
 * @Path           : /wp-content/themes/rowfish/course/widgets/course_blocks.php
 * @Description    : 课程页侧边栏的多功能课程展示小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
if (!defined('ABSPATH')) {
    die;
}
if (!function_exists('rf_get_course_blocks_fields')) {
    function rf_get_course_blocks_fields($args)
    {
        $default_orderby = 'date';
        $default_icon = 'far fa-comment-alt';
        $orderby_options = [];
        if ($args['key'] == 'recommand') {
            $orderby_options = array_merge($orderby_options, [
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('课程ID', 'rizhuti-v2'),
            ]);
            $default_orderby = 'date';
            if (isPostTypesOrder()) {
                $orderby_options = array_merge(['menu_order' => esc_html__('自定义(根据post type order插件)', 'rizhuti-v2')], $orderby_options);
                $default_orderby = 'menu_order';
            }
            $default_icon = 'far fa-thumbs-up';
        } elseif ($args['key'] == 'payhot') {
            $default_icon = 'fab fa-gripfire';
        }
        $fields = array(
            array(
                'id'      => "is_{$args['key']}",
                'type'    => 'switcher',
                'title'   => esc_html__("是否显示{$args['name']}课程", 'rizhuti-v2'),
                'default' => false,
            ),
            array(
                'id'    => "{$args['key']}_title",
                'type'  => 'text',
                'title' => esc_html__("{$args['name']}区块标题", 'rizhuti-v2'),
                'default' => "{$args['name']}",
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
            array(
                'id'         => "{$args['key']}_icon",
                'type'       => 'icon',
                'title'      => '顶部图标',
                'desc'       => '区块标签顶部图标',
                'default'    => $default_icon,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
            array(
                'id'      => "is_{$args['key']}_media",
                'type'    => 'switcher',
                'title'   => esc_html__("显示{$args['name']}文章缩略图", 'rizhuti-v2'),
                'default' => true,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
            array(
                'id'      => "{$args['key']}_count",
                'type'    => 'text',
                'title'   => esc_html__("显示{$args['name']}课程数量", 'rizhuti-v2'),
                'default' => 5,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
            array(
                'id'      => "{$args['key']}_offset",
                'type'    => 'text',
                'title'   => esc_html__("{$args['name']}课程默认起始页", 'rizhuti-v2'),
                'default' => 0,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
        );
        if (count($orderby_options) > 0) {
            array_splice($fields, 3, 0, [array(
                'id'      => "{$args['key']}_orderby",
                'type'    => 'select',
                'title'   => esc_html__('排序方式', 'rizhuti-v2'),
                'options' =>  $orderby_options,
                'default' => $default_orderby
            )]);
        }
        return $fields;
    }
}
if (!function_exists('rf_merge_course_blocks_values')) {
    function rf_merge_course_blocks_values($instance, $keys)
    {
        foreach ($keys as $key) {
            $default_orderby = 'date';
            $default_icon = 'far fa-comment-alt';
            if ($key == 'recommand') {
                $default_icon = 'far fa-thumbs-up';
                $default_orderby = 'date';
                if (isPostTypesOrder()) {
                    $default_orderby = 'menu_order';
                }
            } elseif ($key == 'payhot') {
                $default_icon = 'fab fa-gripfire';
                $default_orderby = 'paynum';
            }
            $instance = array_merge(array(
                "is_{$key}_media" => true,
                "{$key}_count" => 5,
                "{$key}_offset" => 0,
                "{$key}_orderby" => $default_orderby,
                "{$key}_icon" => $default_icon
            ), $instance);
        }
        if (!isset($instance['categories']) || empty($instance['categories'])) {
            $instance['categories'] = [];
        }
        return $instance;
    }
}
/**
 * 侧边栏分类文章展示
 */
CSF::createWidget('rf_course_blocks_widget', array(
    'title'       => esc_html__('RF: 多功能课程侧边栏', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post rowfish-widget-post-blocks',
    'description' => esc_html__('用于课程内页', 'rizhuti-v2'),
    'fields'      => array_merge(
        array(array(
            'id'          => "categories",
            'type'        => 'select',
            'title'       => esc_html__("只显示这些分类下的课程", 'rizhuti-v2'),
            'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
            'multiple' => true,
            'inline'      => true,
            'chosen'      => true,
            'options' => 'categories',
            'query_args' => [
                'taxonomy' => 'course_category',
                'orderby' => 'count',
                'order' => 'DESC',
            ],
            'desc'     => esc_html__('不选择分类则显示全部分类下的课程', 'rizhuti-v2'),
        )),
        rf_get_course_blocks_fields(['key' => 'recommand', 'name' => '推荐课程']),
        rf_get_course_blocks_fields(['key' => 'payhot', 'name' => '热销课程']),
        rf_get_course_blocks_fields(['key' => 'news', 'name' => '最新课程']),
    ),
));
if (!function_exists('rf_course_blocks_widget')) {
    function rf_course_blocks_widget($args, $instance)
    {
        $params = ['recommand' => ['name' => '推荐课程'], 'payhot' => ['name' => '热销课程'], 'news' => ['name' => '最新课程']];
        // $termObj = get_queried_object();
        // $termID = (!empty($termObj) && !empty($termObj->term_id)) ? $termObj->term_id : null;
        $instance = rf_merge_course_blocks_values($instance, ['recommand', 'payhot', 'news']);
        $enableds = [];
        $features = array_keys($params);
        foreach ($features as $v) {
            if ($instance["is_{$v}"]) $enableds[$v] = $params[$v];
        }
        if (is_page_template_modular() || count($enableds) <= 0) return;
        echo $args['before_widget'];
        $i = 0;
        ob_start(); ?>
        <ul class="nav nav-tabs nav-justified" id="multi_post_loop_tab" role="tablist">
            <?php
            foreach ($enableds as $key => $enable) :
                $itemCls = 'nav-item';
                $hrefCls = 'nav-link';
                if ($i == 0) {
                    $itemCls = $itemCls . ' active';
                    $hrefCls = $hrefCls . ' active';
                } ?>
                <li class="<?php echo  $itemCls ?>" data-index="<?php echo $i; ?>">
                    <a class="<?php echo  $hrefCls ?>" id="<?php echo $key; ?>-tab" data-toggle="tab" data-target="#<?php echo $key; ?>" role="tab" aria-controls="<?php echo $key; ?>" aria-selected="<?php echo $i == 0 ? 'true' : 'false'; ?>">
                        <i class="<?php echo $instance["{$key}_icon"]; ?>"></i>
                    </a>
                </li>
            <?php $i++;
            endforeach; ?>
            <span class="navs-slider-bar"></span>
        </ul>
        <div class="tab-content" id="multi_post_loop_body">
            <?php
            $i = 0;
            $tabCls = 'tab-pane fade';
            foreach ($enableds as $key => $enable) :
                $tabCls = $i == 0 ? 'tab-pane fade show active' : 'tab-pane fade';
            ?>
                <div class="<?php echo $tabCls ?>" id="<?php echo $key; ?>" role="tabpanel" aria-labelledby="<?php echo $key; ?>-tab">
                    <?php
                    $_args = array(
                        'post_type' => 'course',
                        'ignore_sticky_posts' => true,
                        'post_status'         => 'publish',
                        'posts_per_page'      => (int)$instance["{$key}_count"],
                        'paged'              => (int)$instance["{$key}_offset"],
                        'orderby'             => $instance["{$key}_orderby"],
                    );
                    if (count($instance["categories"]) > 0) {
                        $_args['tax_query'] = [
                            'taxonomy' => 'course_categories',
                            'field'    => 'term_id',
                            'terms' => $instance["categories"],
                            'operator' => 'IN'
                        ];
                    }
                    $_args['meta_query'] = [];
                    if ($key == 'recommand') {
                        $_args['meta_query'] = [[
                            'key'     => 'is_recommand',
                            'value'   => '1',
                            'compare' => '=',
                        ]];
                    }
                    if ($instance["{$key}_orderby"] == 'menu_order') {
                        $_args['orderby'] = ['menu_order' => 'ASC'];
                    }
                    if ($key == 'hotpay') {
                        $_args['orderby'] = ['paynum' => 'DESC', 'paynum_none' => 'DESC'];
                        $_args['meta_query'] = array_merge($_args['meta_query'], [[
                            'relation' => 'OR',
                            ['paynum' => ['key' => '_paynum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                            ['paynum_none' => ['key' => '_paynum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
                        ]]);
                    }
                    $PostData = new WP_Query($_args);
                    $j = 0;
                    ?>

                    <div class="posts-wrapper list">
                        <?php while ($PostData->have_posts()) : $PostData->the_post();
                            $j++;
                        ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post post-list'); ?>>
                                <?php if (!empty($instance["is_{$key}_media"])) {
                                    echo rf_get_post_media(null, 'thumbnail');
                                } ?>
                                <div class="entry-wrapper">
                                    <header class="">
                                        <?php rizhuti_v2_entry_title(array('link' => true)); ?>
                                    </header>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php
                $i++;
            endforeach; ?>
        </div>
<?php wp_reset_postdata();
        echo ob_get_clean();
        wp_reset_postdata();
        echo $args['after_widget'];
    }
}
