<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 00:49:17 +0800
 * @Path           : /wp-content/themes/rowfish/course/options/page.php
 * @Description    : 后台课程首页选项
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_get_course_page_options')) {
    /**
     * 后台课程首页选项框
     *
     * @return void
     */
    function rf_get_course_page_options()
    {
        $data = ['hook' => '_prefix_rf_course_page_options', 'params' => [
            'title' => '模块设置',
            'post_type' => 'page',
            'page_templates' => 'pages/courses.php',
            'data_type' => 'unserialize',
            'priority' => 'high'
        ], 'fields' => [
            [
                'id' => 'course_page_close_filter',
                'type' => 'switcher',
                'title' => '强制关闭筛选条',
                'label' => '无法"主题设置中"是否开启内置筛选条都在此页面中关闭',
                'default' => false,
            ],
            [
                'id' => 'course_top_image_enabled',
                'type' => 'switcher',
                'title' => '启用课程模块首页顶部背景图',
                'label' => '启用后课程首页也会出现随机背景图,如需固定的背景图请到课程页面设置',
                'default' => true,
            ],
            [
                'id' => 'course_top_image_description',
                'type' => 'text',
                'title' => '页面顶部背景描述',
                'label' => '这里的文字将会在页面顶部背景图的标题下显示',
                'default' => '',
            ],
            [
                'id' => 'course_single_top_image_enabled',
                'type' => 'switcher',
                'title' => '课程模块首页固定一张顶部图片',
                'label' => '如果不开启则使用自动使用主题设置中设置的"列表页随机顶部背景图"中的一张',
                'default' => false,
                'dependency' => array('course_top_image_enabled', '==', 'true'),
            ],
            [
                'id'    => 'course_single_top_image',
                'type'  => 'upload',
                'title' => esc_html__('固定顶部图', 'rizhuti-v2'),
                'dependency' => array('course_single_top_image_enabled', '==', 'true'),
            ],
        ]];
        CSF::createMetabox($data['hook'], $data['params']);
        CSF::createSection($data['hook'], array(
            'fields' => $data['fields']
        ));
    }
}
