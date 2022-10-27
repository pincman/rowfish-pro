<?php
$currentterm = get_queried_object();
$currentterm_id  = $currentterm->term_id;
$top_term_id = (is_category()) ? get_category_root_id($currentterm_id) : 0;
$current_array = array($currentterm_id);
$parent_id = $currentterm->parent;
while ($parent_id) {
    $current_array[] = $parent_id;
    $parent_term = get_term($parent_id, 'category');
    $parent_id = $parent_term->parent;
}
// 是否为课程分类
$is_course_category = get_term_meta(get_queried_object_id(), 'is_course_category', true) === '1';
$custom_archive_class = $is_course_category ? ' custom_archive' : '';
?>

<div class="archive-filter<?php echo $custom_archive_class; ?>">
    <div class="container">
        <div class="filters">

            <?php
            // 获取一级分类
            $filter_cat_1 = _cao('archive_filter_cat_1');
            if (_cao('is_archive_filter_cat', '1') && !empty($filter_cat_1)) {
                echo '<ul class="filter">';
                echo '<li><span>' . esc_html__('全部分类', 'rizhuti-v2') . '<i class="fa fa-angle-right ml-1"></i></span></li>';
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
                    echo '<li><span>' . esc_html__('标签', 'rizhuti-v2') . '<i class="fa fa-angle-right ml-1"></i></span></li></span></li>';
                    foreach ($tags as $tag) {
                        echo '<li><a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . '</a></li>';
                    }
                    echo '</ul>';
                }
            }

            // 是否启用价格筛选
            $enabled_global_price_filter = _cao('is_archive_filter_price', '1') == '1';
            // 是否启用排序筛选
            $enabled_global_order_filter = _cao('is_archive_filter_order', '1') == '1';
            // 是否启用课程状态筛选
            $enabled_global_course_status_filter = _cao('course_status_filter', '1') == '1';
            $enabled_global_course_level_filter = _cao('course_status_filter', '1') == '1';
            // 获取分类下的价格筛选和排序筛选
            $enabled_cate_price_filter = get_term_meta(get_queried_object_id(), 'enabled_price_filter', true);
            // 获取分类下的排序筛选
            $enabled_cate_order_filter = get_term_meta(get_queried_object_id(), 'enabled_order_filter', true);
            $enabled_course_status_filter = false;
            $enabled_course_level_filter = false;
            $filters_count = 0;
            $enabled_price_filter = ($enabled_global_price_filter && empty($enabled_cate_price_filter)) || $enabled_cate_price_filter == '2';
            $enabled_order_filter = ($enabled_global_order_filter && empty($enabled_cate_order_filter)) || $enabled_cate_order_filter == '2';
            if ($enabled_global_course_status_filter && $is_course_category)  $enabled_course_status_filter = true;
            if ($enabled_global_course_level_filter && $is_course_category)  $enabled_course_level_filter = true;
            $order_filter_content = '';
            if ($enabled_order_filter) {
                // echo $filters_count >= 2 ? '<div class="col-12 col-sm-4 recent">' :
                //     '<div class="col-12 recent" style="justify-content: flex-start">';
                // echo '<div class="col-12 recent" style="justify-content: flex-start">';
                $is_current = !empty($_GET['order']) ? $_GET['order'] : 'default';
                $order_filter_content .= '<ul class="filter"><li><span>排序</span><i class="fa fa-angle-right ml-1"></i></li>';
                // if ($filters_count < 2) 
                $order_arr = [
                    'default' => '<i class="far fa-clock"></i> ' . esc_html__('默认', 'rizhuti-v2'),
                    'date' => '<i class="far fa-clock"></i> ' . esc_html__('最新', 'rizhuti-v2'),
                    'favnum' =>  '<i class="fas fa-random"></i> ' . esc_html__('收藏', 'rizhuti-v2'),
                    'views' => '<i class="far fa-eye"></i> ' . esc_html__('热度', 'rizhuti-v2'),
                    // 'rand' => '<i class="fas fa-random"></i> ' . esc_html__('随机', 'rizhuti-v2'),
                ];
                foreach ($order_arr as $key => $item) {
                    $_current_css = ($is_current == $key) ? ' class="current"' : '';
                    $href = $key == 'default' ?  remove_query_arg('order') :  add_query_arg("order", $key);
                    $order_filter_content .= '<li' . $_current_css . '><a href="' . $href . '">' . $item . '</a></li>';
                }
                $order_filter_content .= '</ul>';
                // echo '</div>';
            }
            if ($enabled_price_filter) $filters_count++;
            if ($enabled_course_status_filter) $filters_count++;
            if ($enabled_level_status_filter) $filters_count++;
            // 当前文章是否为课程类型
            // $is_course_cat = _get_post_shop_type($post_ID) === '5';
            if ($enabled_price_filter || $enabled_course_status_filter || $enabled_course_level_filter) {
                // $style = $filters_count <= 1 ? ' style="justify-content: space-between;"' : '';
                echo '<div class="filter-tab"><div class="row">';
                if ($enabled_price_filter && !is_close_site_shop()) {
                    $sm = $filters_count > 1 ? ' col-sm-4' : '';
                    echo '<div class="col-12' . $sm . '">';
                    $is_current = isset($_GET['price_type']) ? (int) $_GET['price_type'] : 0;
                    echo '<ul class="filter">';
                    $riplus_type_arr = ['全部', '免费', '可购买', '订阅专属'];
                    echo '<li><span>是否免费</span><i class="fa fa-angle-right ml-1"></i></li>';
                    foreach ($riplus_type_arr as $key => $item) {
                        $_current_css = ($is_current == $key) ? ' class="current"' : '';
                        if ($key > 0) echo '<li' . $_current_css . '><a href="' . add_query_arg("price_type", $key) . '">' . $item . '</a></li>';
                        else echo '<li' . $_current_css . '><a href="' . remove_query_arg("price_type") . '">' . $item . '</a></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                if ($enabled_course_status_filter) {
                    $sm = $filters_count > 1 ? ' col-sm-5' : '';
                    echo '<div class="col-12' . $sm . '">';
                    $is_current = isset($_GET['course_status']) ? (int) $_GET['course_status'] : null;
                    echo '<ul class="filter">';
                    $riplus_type_arr = ['策划中', '待发布', '更新中', '已完结'];
                    echo '<li><span>上线状态</span><i class="fa fa-angle-right ml-1"></i></li>';
                    $_current_css = is_null($is_current) ? ' class="current"' : '';
                    echo '<li' . $_current_css . '><a href="' . remove_query_arg("course_status") . '">全部</a></li>';
                    foreach ($riplus_type_arr as $key => $item) {
                        $_current_css = $is_current === $key ? ' class="current"' : '';
                        echo '<li' . $_current_css . '><a href="' . add_query_arg("course_status", $key) . '">' . $item . '</a></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                if ($enabled_course_level_filter) {
                    $sm = $filters_count > 1 ? ' col-sm-3' : '';
                    echo '<div class="col-12' . $sm . '">';
                    $is_current = isset($_GET['course_level']) ? (int) $_GET['course_level'] : null;
                    echo '<ul class="filter">';
                    $riplus_type_arr = ['入门', '进阶', '大师'];
                    echo '<li><span>教程难度</span><i class="fa fa-angle-right ml-1"></i></li>';
                    $_current_css = is_null($is_current) ? ' class="current"' : '';
                    echo '<li' . $_current_css . '><a href="' . remove_query_arg("course_level") . '">全部</a></li>';
                    foreach ($riplus_type_arr as $key => $item) {
                        $_current_css = $is_current === $key ? ' class="current"' : '';
                        echo '<li' . $_current_css . '><a href="' . add_query_arg("course_level", $key) . '">' . $item . '</a></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
                echo '</div></div>';
                // if ($filters_count <= 1 && $enabled_order_filter) {
                //     echo '<div class="col-12 col-sm-4  offset-sm-2">' . $order_filter_content . '</div></div></div>';
                // } else {
                //     echo '</div></div>' . $order_filter_content;
                // }
            }
            echo $order_filter_content;
            ?>
        </div>

    </div>
</div>