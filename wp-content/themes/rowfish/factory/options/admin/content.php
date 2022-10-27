<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:23:51 +0800
 * @Path           : /wp-content/themes/rowfish/factory/options/admin/content.php
 * @Description    : 修改rizhuti-v2主题设置中的选项(内容选项)
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_get_admin_style_meta')) {
    /**
     * 后台-主题设置-内容设置-布局风格
     * @return array
     */
    function rf_get_admin_style_meta()
    {
        $options = CSF::$args['sections']['_riprov2_options'];
        if (count($options) <= 5) {
            return [];
        }
        $navbar_style_index = null;
        $archive_single_style_index = null;
        $hero_single_style_index = null;
        $top_fields = $options[2]['fields'] ?? [];
        $style_fields = $options[3]['fields'] ?? [];
        $single_fields = $options[4]['fields'] ?? [];
        foreach ($style_fields as $key => $value) {
            if (isset($value['id'])) {
                if ($value['id'] === 'navbar_style') {
                    $navbar_style_index = $key;
                }
                if ($value['id'] === 'archive_single_style') {
                    $archive_single_style_index = $key;
                }
                if (
                    in_array($value['id'], [
                        'archive_item_style',
                        'is_post_grid_excerpt',
                        'is_post_grid_author',
                        'is_post_grid_category',
                        'is_post_grid_date',
                        'is_post_grid_comment',
                        'is_post_grid_favnum',
                        'is_post_grid_views',
                        'is_post_grid_shop',
                        'is_post_grid_price'
                    ]) && (!isset($value['class']) || $value['class'] != 'hidden')
                ) {
                    $attr = isset($value['attributes']) ? $value['attributes'] : [];
                    $style_fields[$key]['class'] = 'hidden';
                    $style_fields[$key]['attributes'] = array_merge($attr, ['type' => 'hidden']);
                }
            }
        }
        if (!$navbar_style_index && $archive_single_style_index) {
            array_splice($style_fields, 1, 0, $top_fields);
            $style_fields = array_merge($style_fields, array_slice($style_fields, 3, 3));
            array_splice($style_fields, $archive_single_style_index + 1, 0, [
                [
                    'id' => 'archive_list_block_style',
                    'type' => 'select',
                    'title' => '文章列表展示风格',
                    'desc' => '此项设定也可以在文章编辑中单独设置,无侧边栏时只显示小区块',
                    'placeholder' => '选择风格',
                    'inline' => true,
                    'options' => [
                        'fix' => '随机交替', 'small' => '全部小区块', 'big' => '全部大区块'
                    ],
                    'default' => 'fix'
                ],
                [
                    'id' => 'archive_list_merge_thumbnail',
                    'type' => 'switcher',
                    'title' => '文章列表默认大区块特色图和内容合并',
                    'desc' => '推荐在文章编辑中对置顶的文章单独设置该项以调整美观度',
                    'default' => false,
                ],
            ]);

            // array_splice($style_fields, 3, 3);
            foreach ($style_fields as $key => $value) {
                if (in_array($value['id'], ['is_compare_options_to_global', 'hero_single_style'])) {
                    $attr = isset($value['attributes']) ? $value['attributes'] : [];
                    $style_fields[$key]['class'] = 'hidden';
                    $style_fields[$key]['attributes'] = array_merge($attr, ['type' => 'hidden']);
                    if ($value['id'] = 'hero_single_style') {
                        $hero_single_style_index = $key;
                    }
                }
            }
            if (!is_null($hero_single_style_index)) {
                $style_fields[$hero_single_style_index + 1] = [
                    'id' => 'is_single_template_top_img',
                    'type' => 'switcher',
                    'title' => '文章内页启用顶部特色图',
                    'desc' => '开启后，文章顶部显示特色图片大图,如果没有设置特色图片则使用随机特色图',
                    'default' => true,
                ];
            }
            foreach ($single_fields as $key => $value) {
                if (isset($value['id'])) {
                    if ($value['id'] === 'is_single_shop_template') {
                        $attr = isset($value['attributes']) ? $value['attributes'] : [];
                        $single_fields[$key]['class'] = 'hidden';
                        $single_fields[$key]['attributes'] = array_merge($attr, ['type' => 'hidden']);
                    }
                    if ($value['id'] == 'related_posts_item_style') {
                        $single_fields[$key]['default'] = 'none';
                    }

                    if ($value['id'] === 'is_single_entry_page') {
                        $single_fields[$key]['default'] = false;
                    }
                    if (($value['id'] === 'is_single_shop_template_img' || $value['id'] === 'single_shop_template_help')) {
                        unset($single_fields[$key]);
                    }
                }
            }
            // $style_fields = array_merge($style_fields, $single_fields);
            // CSF::$args['sections']['_riprov2_options'][2]['fields'] = $style_fields;
            // CSF::set_used_fields($style_fields);
            return array_merge($style_fields, $single_fields);
        }
        return [];
    }
}
if (!function_exists('rf_get_admin_filter_meta')) {
    /**
     * 后台-主题设置-内容设置-文章过滤
     * @return array|mixed
     */
    function rf_get_admin_filter_meta()
    {
        if (
            count(CSF::$args['sections']['_riprov2_options']) <= 7
            || !isset(CSF::$args['sections']['_riprov2_options'][6]['fields'])
        ) {
            return [];
        }
        $filter_fields = CSF::$args['sections']['_riprov2_options'][6]['fields'];
        $is_archive_top_bg_index = null;
        $is_archive_top_bg_one_index = null;
        $is_archive_filter_price_index = null;
        foreach ($filter_fields as $key => $value) {
            if (isset($value['id'])) {
                if ($value['id'] === 'is_archive_top_bg') {
                    $is_archive_top_bg_index = $key;
                }
                if ($value['id'] === 'is_archive_top_bg_one') {
                    $is_archive_top_bg_one_index = $key;
                }
                if ($value['id'] === 'is_archive_filter_price') {
                    $is_archive_filter_price_index = $key;
                }
                if ($value['id'] === 'is_archive_filter') {
                    $filter_fields[$key]['label'] = '如果需要单独关闭一个分类或页面模块的筛选条(比如: 课程模块首页),可以到它的"页面"中设置';
                }
            }
        }
        if (!is_null($is_archive_top_bg_index) && !is_null($is_archive_top_bg_one_index)) {
            unset($filter_fields[$is_archive_top_bg_index]);
            unset($filter_fields[$is_archive_top_bg_one_index]);
            if (!is_null($is_archive_filter_price_index)) {
                array_splice($filter_fields, $is_archive_filter_price_index + 1, 0, [[
                    'id' => 'is_simple_filter_price',
                    'type' => 'switcher',
                    'title' => '启用简单价格筛选',
                    'label' => '开启此项的前提是确保你的资源针对所有VIP组用户均为免费使用(影响全局,强烈建议开启)',
                    'default' => true,
                    'dependency' => array('is_archive_filter_price', '==', '1'),
                ]]);
            }
            return $filter_fields;
        }
        return [];
    }
}
if (!function_exists('rf_set_admin_content_meta')) {
    /**
     * 后台-主题设置-内容设置
     */
    function rf_set_admin_content_meta()
    {
        $style_fields = rf_get_admin_style_meta();
        $filter_fields = rf_get_admin_filter_meta();
        if (count($style_fields) > 0 && count($filter_fields) > 0) {
            unset(CSF::$args['sections']['_riprov2_options'][2]);
            unset(CSF::$args['sections']['_riprov2_options'][3]);
            unset(CSF::$args['sections']['_riprov2_options'][4]);
            unset(CSF::$args['sections']['_riprov2_options'][6]);
            array_splice(CSF::$args['sections']['_riprov2_options'], 2, 0, [[
                'id' => 'content_fields',
                'title' => '内容设置',
                'icon' => 'fa fa-plus-circle',
            ], [
                'parent' => 'content_fields',
                'title' => '布局风格',
                'icon' => 'fa fa-circle',
                'fields' => $style_fields
            ], [
                'parent' => 'content_fields',
                'title' => '文章过滤',
                'icon' => 'fa fa-circle',
                'fields' => $filter_fields
            ]]);
        }
    }
}

