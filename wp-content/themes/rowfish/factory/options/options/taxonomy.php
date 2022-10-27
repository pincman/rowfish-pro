<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:10:41 +0800
 * @Path           : /wp-content/themes/rowfish/factory/options/options/taxonomy.php
 * @Description    : 修改rizhuti-v2中后台的一些文章分类和文章专题的选项
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_create_taxonomy_metabox')) {
    /**
     * 修改分类,专题,标签页面的后台选项
     */
    function rf_create_taxonomy_metabox()
    {
        $tax_options = CSF::$args['sections']['cat_taxonomy_options'];
        $fields = [];
        if (count($tax_options) <= 0) return;
        if (!isset($tax_options[0]['fields']) || count(array_filter($tax_options[0]['fields'], function ($value) {
                return $value['id'] === 'top_bar_image';
            })) > 0) return;
        CSF::$args['taxonomy_options']['cat_taxonomy_options']['taxonomy'] = ['category'];
        foreach ($tax_options[0]['fields'] as $key => $value) {
            if (isset($value['id'])) {
                if ($value['id'] === 'bg-image') {
                    $value['default'] = '';
                    $fields = array_merge($fields, [
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
                        $value
                    ]);
                } else if ($value['id'] === 'archive_item_style') {
                    $fields = array_merge($fields, [
                        [
                            'id' => 'enabled_order_filter',
                            'type' => 'select',
                            'title' => '启用排序筛选',
                            'options' => [
                                '0' => '跟随设置',
                                '1' => '不启用',
                            ],
                            'inline' => true,
                            'default' => 0,
                        ],
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
                    ]);
                } else {
                    array_push($fields, $value);
                }
            } else {
                array_push($fields, $value);
            }
            CSF::$args['sections']['cat_taxonomy_options'][0]['fields'] = $fields;
            CSF::set_used_fields($fields);
        }
        $tag_fields = [];
        $series_fields = [];
        foreach (CSF::$args['sections']['cat_taxonomy_options'][0]['fields'] as $v) {
            if (!isset($v['id']) || !in_array($v['id'], ['archive_single_style', 'bg-image', 'enabled_price_filter'])) {
                $tag_fields[] = $v;
                $series_fields[] = $v;
            } elseif (isset($v['id']) && $v['id'] == 'bg-image') {
                $series_fields[] = $v;
            }
        }
        CSF::createTaxonomyOptions('rf_tag_options', [
            'taxonomy' => ['post_tag'],
            'data_type' => 'unserialize',
        ]);
        CSF::createSection('rf_tag_options', ['fields' => $tag_fields]);
        CSF::$args['sections']['series_taxonomy_options'][0]['fields'] = $series_fields;
        CSF::set_used_fields($series_fields);
    }
}


add_action('after_setup_theme', 'rf_create_taxonomy_metabox', -99);
add_action('init', 'rf_create_taxonomy_metabox', -99);
add_action('switch_theme', 'rf_create_taxonomy_metabox', -99);
