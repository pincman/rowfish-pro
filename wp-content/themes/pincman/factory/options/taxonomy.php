<?php

/**
 * 分类及标签设置选项
 */
function get_taxonomy_options()
{
    return
        ['hook' => 'cat_pm_options', 'params' => [
            'taxonomy'  => ['post_tag', 'category'],
            'data_type' => 'unserialize',
        ], 'fields' => [
            [
                'id'      => 'enabled_top_image',
                'type'    => 'switcher',
                'title'   => '顶部背景图',
                'desc'    => '是否显示分类和标签等文章列表集合的顶部背景横条',
                'default'    => true,
            ],
            [
                'id'      => 'top_bar_image',
                'type'     => 'upload',
                'sanitize' => false,
                'title'   => '顶部背景图',
                'desc'    => '子主题添加的选项,所以下面的特色图片不再作为背景图,如果不设置,则直接显示后台设置的随机图片',
                'default'    => '',
                'dependency' => array('enabled_top_image', '==', '1'),
            ],
            [
                'id'      => 'is_course_category',
                'type'    => 'switcher',
                'title'   => '是否课程分类',
                'desc'    => '此分类是否为一个课程分类',
                'default' => false,
            ],
            [
                'id'      => 'enabled_order_filter',
                'type'    => 'select',
                'title'   => '启用排序筛选',
                'inline'  => true,
                'options' => [
                    '0' => '跟随设置',
                    '1' => '不启用',
                    '2' => '启用',
                ],
                'inline'  => true,
                'default' => 0,
            ],
            [
                'id'      => 'enabled_price_filter',
                'type'    => 'select',
                'title'   => '启用VIP与价格筛选',
                'inline'  => true,
                'options' => array(
                    '0' => '跟随设置',
                    '1' => '不启用',
                    '2' => '启用',
                ),
                'inline'  => true,
                'default' => 0,
            ],
        ]];
}
