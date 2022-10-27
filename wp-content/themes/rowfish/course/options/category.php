<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 00:49:02 +0800
 * @Path           : /wp-content/themes/rowfish/course/options/category.php
 * @Description    : 后台课程分类选项
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_create_course_category_metabox')) {
    /**
     * 后台课程分类选项框
     *
     * @return void
     */
    function rf_create_course_category_metabox()
    {
        $taxonomy_fields = array(
            [
                'id' => 'enabled_top_image',
                'type' => 'switcher',
                'title' => '启用顶部背景图',
                'desc' => '是否显示分类和标签等文章列表集合的顶部背景横条',
                'default' => _cao('is_archive_top_bg'),
            ],
            [
                'id' => 'top_bar_image',
                'type' => 'upload',
                'sanitize' => false,
                'title' => '自定义顶部背景图',
                'desc' => '分类顶部背景图(如果不设置,则直接显示后台设置的随机图片)',
                'default' => '',
                'dependency' => array('enabled_top_image', '==', '1'),
            ],
            [
                'id' => 'bg-image',
                'type' => 'upload',
                'title' => '特色图片',
                'desc' => '用于展示缩略图(如果不设置,则直接显示后台设置的随机图片)',
                'default' => '',
            ],
            array(
                'id'       => 'seo-title',
                'type'     => 'text',
                'title'    => esc_html__('自定义SEO标题', 'rizhuti-v2'),
                'subtitle' => esc_html__('为空则不设置', 'rizhuti-v2'),
            ),
            array(
                'id'       => 'seo-keywords',
                'type'     => 'text',
                'title'    => esc_html__('SEO关键词', 'rizhuti-v2'),
                'subtitle' => esc_html__('关键词用英文逗号,隔开', 'rizhuti-v2'),
            ),
            array(
                'id'       => 'seo-description',
                'type'     => 'textarea',
                'title'    => esc_html__('SEO描述', 'rizhuti-v2'),
                'subtitle' => esc_html__('字数控制到80-180最佳', 'rizhuti-v2'),
            ),
        );
        CSF::createTaxonomyOptions('rf_course_series_options', array(
            'taxonomy' => 'course_series',
            'data_type' => 'unserialize', // The type of the database save options. `serialize` or `unserialize`
        ));
        CSF::createSection('rf_course_series_options', array(
            'fields' => $taxonomy_fields
        ));
        array_splice($taxonomy_fields, 3, 0, [
            [
                'id' => 'enabled_price_filter',
                'type' => 'select',
                'title' => '启用VIP与价格筛选',
                'options' => array(
                    '0' => '跟随设置',
                    '1' => '不启用',
                ),
                'inline' => true,
                'default' => 0,
            ],
            [
                'id' => 'course_status_filter',
                'type' => 'select',
                'title' => '启用课程状态筛选',
                'options' => array(
                    '0' => '跟随设置',
                    '1' => '不启用',
                ),
                'inline' => true,
                'default' => 0,
                'dependency' => [array('enabled_order_filter', '!=', '2')]
            ],
            [
                'id' => 'course_level_filter',
                'type' => 'select',
                'title' => '启用课程难度筛选',
                'options' => array(
                    '0' => '跟随设置',
                    '1' => '不启用',
                ),
                'inline' => true,
                'default' => 0,
                'dependency' => [array('enabled_order_filter', '!=', '2')]
            ],
            array(
                'id' => 'wppay_vip_auth',
                'type' => 'select',
                'title' => esc_html__('VIP会员权限', 'rizhuti-v2'),
                'subtitle' => esc_html__('权限关系是包含关系，终身可查看年月', 'rizhuti-v2'),
                'inline' => true,
                'options' => rf_get_vip_enabled_names_for_options(),
                'default' => _cao('wppay_vip_auth', '0'),
            ),
        ]);
        CSF::createTaxonomyOptions('rf_course_category_options', array(
            'taxonomy' => 'course_category',
            'data_type' => 'unserialize', // The type of the database save options. `serialize` or `unserialize`
        ));
        CSF::createSection('rf_course_category_options', array(
            'fields' => $taxonomy_fields
        ));
    }
}
