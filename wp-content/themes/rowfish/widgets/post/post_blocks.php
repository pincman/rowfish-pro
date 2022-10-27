<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 03:37:53 +0800
 * @Path           : /wp-content/themes/rowfish/widgets/post/post_blocks.php
 * @Description    : 多功能文章侧边栏小工具
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!defined('ABSPATH')) {
    die;
}
if (!function_exists('rf_get_block_post_comments_fields')) {
    function rf_get_block_post_comments_fields($args)
    {
        return array(
            array(
                'id'      => "is_{$args['key']}",
                'type'    => 'switcher',
                'title'   => esc_html__("是否显示{$args['name']}", 'rizhuti-v2'),
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
                'default'    => 'far fa-comment-alt',
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),

        );
    }
}
if (!function_exists('rf_get_post_blocks_fields')) {
    function rf_get_post_blocks_fields($args)
    {
        $default_orderby = 'views';
        $orderby_options = [
            'views'         => esc_html__('阅读量', 'rizhuti-v2'),
            'favnum'         => esc_html__('收藏量', 'rizhuti-v2'),
            'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
        ];
        if ($args['key'] != 'hot') {
            $orderby_options = array_merge($orderby_options, [
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ]);
            $default_orderby = 'date';
            if (isPostTypesOrder()) {
                $orderby_options = array_merge(['menu_order' => esc_html__('自定义(根据post type order插件)', 'rizhuti-v2')], $orderby_options);
                $default_orderby = 'menu_order';
            }
        }
        $default_icon = 'fab fa-gripfire';
        if ($args['key'] == 'recommand') {
            $default_icon = 'far fa-thumbs-up';
        }
        return array(
            array(
                'id'      => "is_{$args['key']}",
                'type'    => 'switcher',
                'title'   => esc_html__("是否显示{$args['name']}文章", 'rizhuti-v2'),
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
                'id'      => "is_{$args['key']}_one_maxbg",
                'type'    => 'switcher',
                'title'   => esc_html__("第一篇{$args['name']}文章大图", 'rizhuti-v2'),
                'default' => false,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
            array(
                'id'      => "{$args['key']}_orderby",
                'type'    => 'select',
                'title'   => esc_html__('排序方式', 'rizhuti-v2'),
                'options' =>  $orderby_options,
                'default' => $default_orderby
            ),
            array(
                'id'      => "is_{$args['key']}_only_current",
                'type'    => 'switcher',
                'label' => '只对分类页面有效,如果是文章页面请选择具体分类',
                'title'   => esc_html__("只显示当前分类下的{$args['name']}文章", 'rizhuti-v2'),
                'default' => true,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
            array(
                'id'          => "{$args['key']}_category",
                'type'        => 'select',
                'title'       => esc_html__("只显示这些分类下的{$args['name']}文章", 'rizhuti-v2'),
                'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
                'options'     => 'categories',
                'desc'     => esc_html__('不选择分类则显示全部分类', 'rizhuti-v2'),
                'dependency' => array("is_{$args['key']}_only_current", '!=', '1'),
            ),
            array(
                'id'      => "{$args['key']}_count",
                'type'    => 'text',
                'title'   => esc_html__("显示{$args['name']}文章数量", 'rizhuti-v2'),
                'default' => 5,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),
            array(
                'id'      => "{$args['key']}_offset",
                'type'    => 'text',
                'title'   => esc_html__("{$args['name']}文章默认起始页", 'rizhuti-v2'),
                'default' => 0,
                'dependency' => array("is_{$args['key']}", '==', '1'),
            ),

        );
    }
}

if (!function_exists('rf_merge_block_post_values')) {
    function rf_merge_block_post_values($instance, $keys)
    {
        foreach ($keys as $key) {
            $default_orderby = 'views';
            $default_icon = 'fab fa-gripfire';
            if ($key == 'recommand') {
                $default_icon = 'far fa-thumbs-up';
            } elseif ($key == 'comments') {
                $default_icon = 'far fa-comment-alt';
            }
            if ($key == 'recommand') {
                $default_orderby = 'date';
                if (isPostTypesOrder()) {
                    $default_orderby = 'menu_order';
                }
            }
            if ($key != 'comments') {
                $instance = array_merge(array(
                    "is_{$key}_media" => true,
                    "is_{$key}_one_maxbg" => false,
                    "is_{$key}_only_current" => 0,
                    "{$key}_category" => 0,
                    "{$key}_count" => 5,
                    "{$key}_offset" => 0,
                    "{$key}_orderby" => $default_orderby,
                    "{$key}_icon" => $default_icon
                ), $instance);
            } else {
                $instance = array_merge(array(
                    "{$key}_icon" => $default_icon
                ), $instance);
            }
        }
        $instance = array_merge(['category_disableds' => []], $instance);
        return $instance;
    }
}
/**
 * 侧边栏分类文章展示
 */
CSF::createWidget('rf_post_blocks_widget', array(
    'title'       => esc_html__('RF: 多功能文章侧边栏', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post rowfish-widget-post-blocks',
    'description' => esc_html__('可添加于分类,专题及文章内页等', 'rizhuti-v2'),
    'fields'      => array_merge(
        [array(
            'id'          => "category_disableds",
            'type'        => 'select',
            'title'       => "不启用的分类",
            'desc'        => '在选择的分类页面下将不启用此小工具,只对分类页面有效',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'orderby' => 'count',
                'order'   => 'DESC',
            )
        )],
        rf_get_post_blocks_fields(['key' => 'recommand', 'name' => '推荐文章']),
        rf_get_post_blocks_fields(['key' => 'hot', 'name' => '热门文章']),
        rf_get_block_post_comments_fields(['key' => 'comments', 'name' => '文章评论']),
    ),
));
if (!function_exists('rf_post_blocks_widget')) {
    function rf_post_blocks_widget($args, $instance)
    {
        $params = ['recommand' => ['name' => '推荐文章'], 'hot' => ['name' => '热门文章'], 'comments' => ['name' => '文章评论']];
        $termObj = get_queried_object();
        $termID = (!empty($termObj) && !empty($termObj->term_id)) ? $termObj->term_id : null;
        $instance = rf_merge_block_post_values($instance, ['recommand', 'hot', 'comments']);
        $enableds = [];
        $features = array_keys($params);
        foreach ($features as $v) {
            if ($instance["is_{$v}"]) $enableds[$v] = $params[$v];
        }
        if (count($enableds) <= 0) return;
        $instance["category_disableds"] = empty($instance["category_disableds"]) ? [] : $instance["category_disableds"];
        if (is_page_template_modular()  || (isset($termID) && in_array($termID, $instance['category_disableds']))) {
            return;
        }
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
                    if (in_array($key, ['recommand', 'hot'])) :
                        $_args = array(
                            'cat'                 =>  !empty($instance["is_{$key}_only_current"]) ? $termID : (int)$instance["{$key}_category"],
                            'ignore_sticky_posts' => true,
                            'post_status'         => 'publish',
                            'posts_per_page'      => (int)$instance["{$key}_count"],
                            'paged'              => (int)$instance["{$key}_offset"],
                            'orderby'             => $instance["{$key}_orderby"]
                        );
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
                        } elseif ($instance["{$key}_orderby"] == 'views') {
                            $_args['orderby'] = ['views' => 'DESC', 'views_none' => 'DESC'];
                            $_args['meta_query'] = array_merge($_args['meta_query'], [[
                                'relation' => 'OR',
                                ['views' => ['key' => '_views', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                                ['views_none' => ['key' => '_views', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
                            ]]);
                        } elseif ($instance["{$key}_orderby"] == 'favnum') {
                            $_args['orderby'] = ['favnum' => 'DESC', 'favnum_none' => 'DESC'];
                            $_args['meta_query'] = array_merge($_args['meta_query'], [[
                                'relation' => 'OR',
                                ['favnum' => ['key' => '_favnum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                                ['favnum_none' => ['key' => '_favnum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
                            ]]);
                        }
                        $PostData = new WP_Query($_args);
                        $j = 0;
                    ?>

                        <div class="posts-wrapper list">
                            <?php while ($PostData->have_posts()) : $PostData->the_post();
                                $j++;
                                $maxbg = ($j == 1 && !empty($instance["is_{$key}_one_maxbg"])) ? ' maxbg' : '';
                            ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post post-list' . $maxbg); ?>>
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
                    <?php
                    elseif ($key == 'comments') :
                        $_args = array('number'  => '5', 'paged' => 1);
                        $query = new WP_Comment_Query($_args);
                    ?>
                        <ul class="comments-list">
                            <?php
                            if ($query->comments) :
                                foreach ($query->comments as $comment) :
                                    $comment_text = get_comment_text($comment->comment_ID);
                                    $author = get_comment_author($comment->comment_ID);
                                    if ($comment->user_id) {
                                        $author = '<a>' . $author . '</a>';
                                    } else if ($comment->comment_author_url) {
                                        $author = '<a href="' . esc_url($comment->comment_author_url) . '" target="_blank" rel="nofollow">' . $author . '</a>';
                                    }
                                    $reply_num =  get_comments([
                                        'parent' => $comment->comment_ID,
                                        'count' => true
                                    ]);
                            ?>
                                    <div id="div-comment-<?php echo $comment->comment_ID; ?>" class="comment-inner">
                                        <div class="comment-author vcard">
                                            <a href="<?php echo get_user_page_url(); ?>"> <?php echo get_avatar($comment, 40); ?> </a>
                                        </div>
                                        <div class="comment-body">
                                            <div class="comment-text"><?php echo wp_strip_all_tags($comment_text); ?></div>
                                            <div class="comment-reply-num">
                                                <i class="far fa-comment-dots"></i>
                                                <span><?php echo $reply_num; ?></span>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    <?php
                    endif;
                    ?>
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
