<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 00:24:33 +0800
 * @Path           : /wp-content/themes/rowfish/course/functions/archive.php
 * @Description    : 课程列表相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_is_course_archive')) {
    /**
     * 是否为课程模块的分类或专题
     * @param $term
     * @return bool
     */
    function rf_is_course_archive($term)
    {
        if (empty($term) || !isset($term->taxonomy)) return false;
        return $term->taxonomy === 'course_category' || $term->taxonomy === 'course_series';
    }
}
if (!function_exists('rf_get_course_categories')) {
    /**
     * 获取课程所属的分类
     * @param array $args
     * @return WP_Error|WP_Term[]
     */
    function rf_get_course_categories($post_id = 0, $args = array())
    {
        $post_id = (int) $post_id;

        $defaults = array('fields' => 'ids');
        $args     = wp_parse_args($args, $defaults);

        $cats = wp_get_object_terms($post_id, 'course_category', $args);
        return $cats;
    }
}
if (!function_exists('rf_get_course_series')) {
    /**
     * 获取课程关联的专题
     * @param int $post_id
     * @param array $args
     * @return WP_Error|WP_Term[]
     */
    function rf_get_course_series($post_id = 0, $args = array())
    {
        $post_id = (int) $post_id;

        $defaults = array('fields' => 'ids');
        $args     = wp_parse_args($args, $defaults);

        $cats = wp_get_object_terms($post_id, 'course_series', $args);
        return $cats;
    }
}
if (!function_exists('rf_course_serie_dot')) {
    /**
     * 获取显示课程所属的专题
     * @param int $num
     */
    function rf_course_serie_dot($num = 2)
    {
        global $post;
        $post_ID = $post->ID;
        $series = get_the_terms($post_ID, 'course_series');
        $i = 0;
        if ($series && count($series) > 0) {
            echo '<span class="meta-serie-dot">';

            foreach ($series as $v) {
                $i++;
                if ($i > $num) break;
                echo '<a href="' . esc_url(get_term_link($v->term_id)) . '" rel="category">' . esc_html($v->name) . '</a>';
            }

            echo '</span>';
        }
    }
}
if (!function_exists('rf_course_category_dot')) {
    /**获取显示课程所属的分类
     * @param int $num
     */
    function rf_course_category_dot($num = 2)
    {
        global $post;
        $post_ID = $post->ID;
        $categories = get_the_terms($post_ID, 'course_category');
        $i = 0;
        if ($categories && count($categories) > 0) {
            echo '<span class="meta-category-dot">';

            foreach ($categories as $k => $c) {
                $i++;
                if ($i > $num) break;
                echo '<a href="' . esc_url(get_category_link($c->term_id)) . '" rel="category"><i class="dot"></i>' . esc_html($c->name) . '</a>';
            }

            echo '</span>';
        }
    }
}
if (!function_exists('rf_show_course_level_icon')) {
    /**
     * 课程列表中显示课程等级图标
     * @param null $post_ID
     */
    function rf_show_course_level_icon($post_ID = null)
    {
        $info = rf_get_post_info($post_ID);
        $levels = _cao('course_levels', []);
        if (_cao('is_course_list_level', true) && $info['is_course']) {
            foreach ($levels as $key => $value) {
                if ($value['slug'] == $info['course']['level']) {
                    echo '<span class="meta-course-icon bg-' . $value['color'] . '" data-toggle="tooltip" data-placement="right" data-delay="0" title="' . $value['name'] . '"><i class="' . $value['icon'] . '"></i></span>';
                }
            }
        }
    }
}
if (!function_exists('rf_show_course_status_icon')) {
    /**
     * 课程列表中显示课程状态
     * @param null $post_ID
     */
    function rf_show_course_status_icon($post_ID = null)
    {
        $info = rf_get_post_info($post_ID);
        $status = _cao('course_status', []);
        if (_cao('is_course_list_status', true) && $info['is_course']) {
            foreach ($status as $key => $value) {
                if ($value['slug'] == $info['course']['status']) {
                    echo '<span class="meta-course-icon" style="background-color: ' . $value['color'] . ';">' . $value['name'] . '</span>';
                }
            }
        }
    }
}
if (!function_exists('rf_show_course_entry_meta')) {
    /**
     * 视频课程列表项中底部区块显示
     * @param array $opt
     * @param null $info
     */
    function rf_show_course_entry_meta($opt = array(), $info = null)
    {
        $options = array_merge(array(
            'status' => true,
            'level' => true,
            'edit' => false,
            'views' => true,
            'favnum' => true,
            'shop' => true,
            'date' => false,
        ), $opt);

        $info = is_null($info) ? rf_get_post_info() : $info;
        if (!isset($info['post_id'])) return;
        if (in_array(true, $options)) : ?>
            <div class="entry-meta">
                <?php if ($options['author']) :
                    $author_id = (int)get_post_field('post_author', $info['post_id']); ?>
                    <span class="meta-author">
                        <a href="<?php echo esc_url(get_author_posts_url($author_id, get_the_author_meta('display_name', $author_id))); ?>">
                            <?php
                            echo get_avatar($author_id);
                            ?>
                        </a>
                    </span>
                <?php endif; ?>
                <?php if ($options['views']) : ?>
                    <span class="meta-views"><i class="fa fa-eye"></i> <?php echo _get_post_views($info['post_id']); ?></span>
                <?php endif; ?>
                <?php if ($options['favnum']) : ?>
                    <span class="meta-favnum"><i class="far fa-star"></i> <?php echo _get_post_fav($info['post_id']); ?></span>
                <?php endif; ?>
                <?php if ($options['date']) : ?>
                    <span class="meta-date">
                        <a href="<?php echo esc_url(get_the_permalink($info['post_id'])); ?>" rel="nofollow">
                            <time datetime="<?php echo esc_attr(get_the_date('c', $info['post_id'])); ?>">
                                <i class="fa fa-clock-o"></i>
                                <?php
                                if (_cao('is_post_list_date_diff', true)) {
                                    echo sprintf(__('%s前', 'rizhuti-v2'), human_time_diff(get_the_time('U', $info['post_id']), current_time('timestamp')));
                                } else {
                                    echo esc_html(get_the_date(null, $info['post_id']));
                                }
                                ?>
                            </time>
                        </a>
                    </span>
                <?php endif; ?>
                <?php
                //付费文章类型
                if ($options['shop']) :
                    $this_icon = site_mycoin('icon');
                    if ($info['is_free']) {
                        $price_meta = esc_html__('免费', 'ripro-v2');
                    } elseif ($info['vip_only']) {
                        $price_meta = rf_post_vip_label() . esc_html__('专属', 'ripro-v2');
                        $this_icon = 'fa fa-diamond';
                    } else {
                        $price_meta = $info['price'];
                        echo '<span class="meta-paynum"><i class="fab fa-shopify"></i> ' . $info['course']['paynum'] . '</span>';
                    }
                    echo '
            <span class="meta-shhop-icon"><i class="' . $this_icon . '"></i> ' . $price_meta . '</span>';

                endif;

                //编辑按钮
                if ($options['edit']) : ?>
                    <span class="meta-edit"><?php edit_post_link('[编辑]'); ?></span>
                <?php endif; ?>


            </div>
<?php endif;
    }
}
