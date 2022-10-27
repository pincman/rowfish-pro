<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/**
 * 示例1：修改网站会员组名称
 * @Author   Dadong2g
 * @DateTime 2021-01-16T23:53:23+0800
 * @param    [type]                   $old_opt [description]
 * @return   [type]                            [description]
 */
function new_vip_options($old_opt)
{
    return array(
        '0'    => '普通用户',
        // '31'   => '月卡VIP',
        '365'  => '年费订阅者',
        '3600' => '永久订阅者',
    );
}
add_filter('ri_vip_options', 'new_vip_options');
