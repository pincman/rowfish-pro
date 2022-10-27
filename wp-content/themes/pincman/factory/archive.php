<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 06:25:29
 * @updated_at: 2021-05-21 06:50:50
 * @description: 文章列表函数
 * @homepage: https://pincman.cn
 */


/**
 * 删除默认文章列表过滤器
 * @return void 
 */
function pm_remove_default_archive_filter()
{
    remove_filter('pre_get_posts', 'rizhuti_v2_archive_filter', 99);
}
/**
 * 添加自定义的文章列表过滤器
 * @param mixed $query 
 * @return mixed 
 */
function pm_archive_filter($query)
{
    //is_search判断搜索页面  !is_admin排除后台  $query->is_main_query()只影响主循环
    if (!$query->is_admin && is_archive() && $query->is_main_query()) {
        $custom_meta_query = ['relation' => 'AND'];
        // 排序：
        $order      = !empty($_GET['order']) ? $_GET['order'] : null;
        $price_type = !empty($_GET['price_type']) ? (int) $_GET['price_type'] : null;
        $course_status = isset($_GET['course_status']) ? (int) $_GET['course_status'] : null;
        $course_level = isset($_GET['course_level']) ? (int) $_GET['course_level'] : null;

        if ($order == 'views') {
            $query->set('meta_key', '_views');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
        } elseif ($order == 'favnum') {
            $query->set('orderby', ['favnum' => 'DESC', 'favnum_none' => 'DESC']);
            $custom_meta_query[] = [
                'relation' => 'OR',
                ['favnum' => ['key' =>  '_favnum', 'compare' => '>=', 'value' => 0, 'type' => 'NUMERIC']],
                ['favnum_none' => ['key' =>  '_favnum', 'compare' => 'NOT EXISTS', 'value' => 0, 'type' => 'NUMERIC']],
            ];
        } elseif (!empty($order)) {
            $query->set('orderby', [$order]);
        } else {
            $query->set('orderby', ['menu_order' => 'ASC']);
        }

        // 筛选
        if ($price_type) {
            // $custom_meta_query = [];
            $_meta  = [];
            $_price = 'wppay_price';
            $_auth  = 'wppay_vip_auth';
            switch ($price_type) {
                case 1:
                    $_meta[] = [
                        'relation' => 'AND',
                        ['key' => $_price, 'compare' => '<=', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '<=', 'value' => '0'],
                    ];
                    break;
                case 2:
                    $_meta[] = [
                        'relation' => 'AND',
                        ['key' => $_price, 'compare' => '>', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '>', 'value' => '0'],
                    ];
                    break;
                case 3:
                    $_meta[] = [
                        'relation' => 'AND',
                        ['key' => $_price, 'compare' => '==', 'value' => '0'],
                        ['key' => $_auth, 'compare' => '>', 'value' => '0'],
                    ];
                    break;
                default:
                    break;
            }
            $custom_meta_query[] = $_meta;
            // $query->set('meta_query', $custom_meta_query);
        }
        if (!is_null($course_status)) {
            // $query->set('meta_key', 'wppay_course_status');
            // $query->set('meta_value', $course_status);
            $custom_meta_query[] = [
                ['key' =>  'wppay_course_status', 'compare' => '=', 'value' => $course_status, 'type' => 'NUMERIC'],
            ];
            // if ($is_online) {
            //     $query->set('meta_key', 'wppay_course_status');
            //     $query->set('meta_value', $course_status);
            // } else {
            //     $custom_meta_query[] = [
            //         'relation' => 'OR',
            //         ['key' =>  'is_online', 'compare' => '=', 'value' => 0, 'type' => 'NUMERIC'],
            //         ['key' =>  'is_online', 'compare' => 'NOT EXISTS'],
            //     ];
            // }
        }
        if (!is_null($course_level)) {
            $custom_meta_query[] = [
                ['key' =>  'wppay_course_level', 'compare' => '=', 'value' => $course_level, 'type' => 'NUMERIC'],
            ];
        }
        $query->set('meta_query', $custom_meta_query);
    }
    return $query;
}

/**
 * 文章列表中显示的文章状态的一些图标
 * @param mixed|null $post_ID 
 * @return string 
 */
function pm_post_icons($post_ID = null)
{
    $uinfo = pm_shop_post_info($post_ID);
    $content = '';
    if ($uinfo['course']) {
        $course_level = get_post_meta($uinfo['post_id'], 'wppay_course_level', 1);
        switch ($course_level) {
            case '0':
                $content .= "<span class='meta-vip-price bg-success'><i class='fab  fa-envira'></i></span>";
                break;
            case '1':
                $content .= "<span class='meta-vip-price bg-danger'><i class='fab fa-gripfire'></i></span>";
                break;
            case '2':
                $content .= "<span class='meta-vip-price bg-warning'><i class='fas fa-feather-alt'></i></span>";
                break;
            case '3':
                $content .= "<span class='meta-vip-price bg-primary'><i class='fas fa-feather-alt'></i></span>";
                break;
            default:
                break;
        }
    }
    if ($uinfo['vip_user']) return $content;
    if ($uinfo['free']) {
        return  $content . '<span class="meta-vip-price bg-primary"><i class="fas fa-hand-holding-water"></i>免费</span>';
    }
    if ($uinfo['only_vip']) {
        return $content . '<span class="meta-vip-price bg-info"><i class="fas fa-code"></i>订阅专享</span>';
    }
    if ($uinfo['paid']) return $content . '<span class="meta-vip-price bg-danger"><i class="fab fa-opencart"></i>已购</span>';
    $bg = 'bg-success';
    $icon = '<i class="fas fa-coins mr-1"></i>';
    if (site_mycoin('is')) {
        $price = convert_site_mycoin($uinfo['price'], 'coin');
        $icon = '<i class="' . site_mycoin('icon') . ' mr-1"></i>';
    }
    return $content . '<span class="meta-vip-price ' . $bg . '">' . $icon . $uinfo['price'] . '</span>';
}
