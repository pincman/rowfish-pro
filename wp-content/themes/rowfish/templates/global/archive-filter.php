<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 04:03:34 +0800
 * @Path           : /wp-content/themes/rowfish/templates/global/archive-filter.php
 * @Description    : 文章过滤组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
$currentterm = get_queried_object();
$currentterm_id = isset($currentterm->term_id) ? $currentterm->term_id : null;
$top_term_id = (is_category()) ? get_category_root_id($currentterm_id) : 0;
$current_array = !empty($currentterm_id) ? array($currentterm_id) : [];
$parent_id = !empty($currentterm->parent) ? $currentterm->parent : null;
if ($parent_id) {
    $current_array[] = $parent_id;
    $parent_term = get_term($parent_id, 'category');
    $parent_id = $parent_term->parent;
}
$enabled_price_filter = _cao('is_archive_filter_price') == '1';
$enabled_order_filter = _cao('is_archive_filter_order') == '1';
$is_simple_price_filter = _cao('is_simple_filter_price') == '1';
$price_select_options = [];
if ($enabled_price_filter) {
    $_vip_options = rf_get_vip_options();
    unset($_vip_options[0]);
    $price_select_options = array(
        '0' => esc_html__('免费', 'rizhuti-v2'),
        '1' => esc_html__('付费', 'rizhuti-v2'),
    );
    if ($is_simple_price_filter) {
        $price_select_options[] = rf_get_base_vip_name() . '专属';
    } else {
        foreach ($_vip_options as $key => $item) {
            if ($item['enabled']) {
                $price_select_options[] = $item['name'] . esc_html__('免费', 'rizhuti-v2');
            }
        }
    }
}


$order_select_options = array(
    'date' => '<i class="far fa-clock"></i> ' . esc_html__('最新', 'rizhuti-v2'),
    'modified' => '<i class="fab fa-buffer"></i> ' . esc_html__('更新', 'rizhuti-v2')
);
if ($enabled_price_filter) {
    $order_select_options['paynum'] = '<i class="fab fa-shopify"></i> 销量';
}
$order_select_options = array_merge($order_select_options, [
    'favnum' => '<i class="far fa-star"></i> ' . esc_html__('收藏', 'rizhuti-v2'),
    'views' => '<i class="far fa-eye"></i> ' . esc_html__('热度', 'rizhuti-v2')
]);
?>

<div class="archive-filter filter-bar">
    <div class="container">
        <div class="filters">

            <?php
            // 获取一级分类
            $filter_cat_1 = _cao('archive_filter_cat_1');
            if (!empty($filter_cat_1)) {
                echo '<ul class="filter">';
                echo '<li><span>' . esc_html__('文章分类', 'rizhuti-v2') . '<i class="fa fa-angle-right ml-1"></i></span></li>';
                foreach ($filter_cat_1 as $_cid) {
                    $item = get_term($_cid, 'category');
                    $is_current = (in_array($item->term_id, $current_array)) ? ' class="current"' : '';
                    echo '<li' . $is_current . '><a href="' . get_category_link($_cid) . '" title="' . sprintf(__('%s个文章', 'rizhuti-v2'), $item->count) . '">' . $item->name . '<span class="badge badge-pill badge-primary-lighten ml-1">' . $item->count . '</span></a></li>';
                }
                echo '</ul>';
            }
            // 获取二级分类
            $cat_orderby = _cao('archive_filter_cat_orderby', 'id');
            if ($top_term_id > 0 && $child2 = get_category($top_term_id)) {
                $is_child3 = 0; //三级指针
                $child_categories = get_terms('category', array('hide_empty' => 0, 'parent' => $child2->term_id, 'orderby' => $cat_orderby, 'order' => 'DESC'));
                if (!empty($child_categories)) {
                    echo '<ul class="filter child">';
                    echo '<li><span>' . $child2->name . '<i class="fa fa-angle-right ml-1"></i></span></li>';
                    foreach ($child_categories as $item) {
                        $is_current = (in_array($item->term_id, $current_array)) ? ' class="current"' : '';
                        if (!empty($is_current)) {
                            $is_child3 = $item->term_id;
                        }
                        echo '<li' . $is_current . '><a href="' . get_category_link($item->term_id) . '" title="' . sprintf(__('%s个文章', 'rizhuti-v2'), $item->count) . '">' . $item->name . '<span class="badge badge-success-lighten ml-1">' . $item->count . '</span></a></li>';
                    }
                    echo '</ul>';
                }

                // 三级分类
                if ($is_child3 > 0 && $child3 = get_category($is_child3)) {
                    $child_categories = get_terms('category', array('hide_empty' => 0, 'parent' => $child3->term_id, 'orderby' => $cat_orderby, 'order' => 'DESC'));
                    if (!empty($child_categories)) {
                        echo '<ul class="filter child">';
                        echo '<li><span>' . $child3->name . '<i class="fa fa-angle-right ml-1"></i></span></li></span></li>';
                        foreach ($child_categories as $item) {
                            $is_current = (in_array($item->term_id, $current_array)) ? ' class="current"' : '';
                            echo '<li' . $is_current . '><a href="' . get_category_link($item->term_id) . '" title="' . sprintf(__('%s个文章', 'rizhuti-v2'), $item->count) . '">' . $item->name . '</a></li>';
                        }
                        echo '</ul>';
                    }
                }
            }

            if (_cao('is_archive_filter_tag', '1') && is_category()) {
                $tags = rizhuti_get_category_tags($currentterm_id);
                $tags = (empty($tags)) ? rizhuti_get_category_tags($top_term_id) : $tags;
                if (!empty($tags)) {
                    echo '<ul class="filter">';
                    echo '<li><span>' . esc_html__('相关标签', 'rizhuti-v2') . '<i class="fa fa-angle-right ml-1"></i></span></li></span></li>';
                    foreach ($tags as $tag) {
                        echo '<li><a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . '</a></li>';
                    }
                    echo '</ul>';
                }
            }
            $tab_filter_content = [];
            if ($enabled_price_filter) {
                global $ri_vip_options;
                $current = isset($_GET['price_type']) ? (int)$_GET['price_type'] : null;
                $_current_css = is_null($current) ? ' class="current"' : '';
                $tab_filter_content['price'] = '<div class="col-12 col-xl-' . ($is_simple_price_filter ? 4 : 12) . ' col-lg-' . ($is_simple_price_filter ? 6 : 12) . '"><ul class="filter">';
                $tab_filter_content['price'] .= '<li><span>' . esc_html__('限定', 'rizhuti-v2') . '</span><i class="fa fa-angle-right ml-1"></i></li>';
                $tab_filter_content['price'] .= '<li' . $_current_css . '><a href="' . remove_query_arg("price_type") . '">全部</a></li>';
                foreach ($price_select_options as $key => $item) {
                    $_current_css = ($current === $key) ? ' current' : '';
                    $tab_filter_content['price'] .= '<li class="' . $_current_css . '"><a href="' . add_query_arg("price_type", $key) . '">' . $item . '</a></li>';
                }
                $tab_filter_content['price'] .= '</ul></div>';
            }

            if ($enabled_price_filter || $enabled_order_filter) {
                echo '<div class="filter-tab">';
                if ($enabled_price_filter) {
                    echo '<div class="row justify-content-between">';
                    foreach ($tab_filter_content as $value) {
                        echo $value;
                    }
                    echo '</div>';
                }
                if ($enabled_order_filter) {
                    $current = isset($_GET['order']) ? $_GET['order'] : null;
                    $_current_css = is_null($current) ? ' class="current"' : '';
                    $tab_filter_content['order'] = '<div class="row"><div class="col-12 col-sm-12">';
                    $tab_filter_content['order'] .= '<ul class="filter">';
                    $tab_filter_content['order'] .= '<li><span>排序</span><i class="fa fa-angle-right ml-1"></i></li>';
                    $tab_filter_content['order'] .= '<li "' . $_current_css . '"><a href="' . remove_query_arg("order") . '"><i class="fas fa-random"></i>默认</a></li>';
                    foreach ($order_select_options as $key => $item) {
                        $_current_css = $current === $key ? ' class="current"' : '';
                        $tab_filter_content['order'] .= '<li ' . $_current_css . '"><a href="' . add_query_arg("order", $key) . '">' . $item . '</a></li>';
                    }
                    $tab_filter_content['order'] .= '</ul>';
                    $tab_filter_content['order'] .= '</div></div>';
                    echo $tab_filter_content['order'];
                }
                echo '</div>';
            }
            ?>
        </div>

    </div>
</div>