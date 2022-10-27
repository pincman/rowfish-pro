<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

if (!current_user_can('manage_options') && is_admin()) {
    return;
}

global $ri_vip_options;
$prefix = '_prefix_profile_options';
CSF::createProfileOptions($prefix, array(
    'data_type' => 'unserialize',
));
CSF::createSection($prefix, array(
    'title'  => esc_html__('rizhuti-v2-会员其他信息', 'rizhuti-v2'),
    'fields' => array(
        array(
            'id'         => 'mycoin',
            'type'       => 'text',
            'title'      => '钱包余额',
            'attributes' => array(
                'readonly' => 'readonly',
            ),
            'default'    => '0',
        ),
        array(
            'id'         => 'aff_from_id',
            'type'       => 'text',
            'title'      => '推荐人ID',
            'attributes' => array(
                'readonly' => 'readonly',
            ),
            'default'    => '0',
        ),
        array(
            'id'      => 'vip_type',
            'type'    => 'select',
            'title'   => esc_html__('会员等级', 'rizhuti-v2'),
            'options' => $ri_vip_options,
        ),
        array(
            'id'    => 'vip_time',
            'type'  => 'text',
            'title' => esc_html__('会员到期时间戳', 'rizhuti-v2'),
            'desc'  => __('日期不对会导致会员无效，会员到期日期需要填写时间戳格式</br>时间戳转换：https://tool.lu/timestamp/', 'rizhuti-v2'),
        ),


        array(
            'id'      => 'user_avatar_type',
            'type'    => 'select',
            'title'   => esc_html__('头像显示类型', 'rizhuti-v2'),
            'options' => array(
                'custom'   => '自定义',
                'qq'       => 'QQ',
                'weixin'   => '微信（开放平台）',
                'mpweixin' => '微信（公众号）',
                'weibo'    => '微博',
            ),
            'default' => 'custom',
        ),

        array(
            'id'         => 'custom_avatar',
            'type'       => 'upload',
            'title'      => '自定义头像',
            'add_title'  => '上传图片',
            'desc'       => '设置用户自定义头像',
            'default'    => get_template_directory_uri() . '/assets/img/avatar.png',
            'dependency' => array('user_avatar_type', '==', 'custom'),
        ),

        array(
            'id'    => 'is_fuck',
            'type'  => 'switcher',
            'title' => '封号该用户',
            'desc' => '封号h后无法登录账号',
        ),
        array(
            'id'         => 'is_fuck_desc',
            'type'       => 'textarea',
            'title'      => '封号原因',
            'default'    => '本站检测到您存在恶意刷单，下载，采集，恶意攻击，评论，赠予封号！',
            'dependency' => array('is_fuck', '==', 'true'),
        ),


    ),

));
