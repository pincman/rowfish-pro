<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 06:42:14
 * @updated_at: 2021-05-21 06:53:03
 * @description: 网课系统的一些函数
 * @homepage: https://pincman.cn
 */

/**
 * 教程列表中显示上线状态的图标
 * @param mixed|null $post_ID 
 * @return string|void 
 */
function pm_course_status_icon($post_ID = null)
{
    $uinfo = pm_shop_post_info($post_ID);
    if ($uinfo['course']) {
        $course_status = get_post_meta($uinfo['post_id'], 'wppay_course_status', 1);
        switch ($course_status) {
            case '0':
                return "<span class='meta-vip-price bg-secondary'>策划中</span>";
                break;
            case '1':
                return "<span class='meta-vip-price bg-success'>待发布</span>";
                break;
            case '2':
                return "<span class='meta-vip-price bg-danger'>更新中</span>";
                break;
            case '3':
                return "<span class='meta-vip-price bg-primary'>已完结</span>";
                break;
            default:
                return "<span class='meta-vip-price bg-secondary'>策划中</span>";
                break;
        }
    }
}
/**
 * 获取开源项目类型文章的信息
 * @param mixed|null $post_ID 
 * @param mixed|null $meta_key 
 * @return mixed 
 */
function pm_oss_post_info($post_ID = null, $meta_key = null)
{
    if (empty($post_ID)) {
        global $post;
        $post_ID = $post->ID;
    }
    if ($meta_key) {
        $meta = get_post_meta($post_ID, $meta_key, 1);
    } else {
        $arr  = array('wppay_type', 'wppay_oss_name', 'wppay_oss_website', 'wppay_oss_git', 'wppay_oss_demourl', 'wppay_oss_agreement', 'wppay_oss_info');
        $meta = array();
        foreach ($arr as $_key) {
            $meta[$_key] = get_post_meta($post_ID, $_key, 1);
        }
    }
    return $meta;
}
