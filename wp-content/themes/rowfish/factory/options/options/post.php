<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:23:08 +0800
 * @Path           : /wp-content/themes/rowfish/factory/options/options/post.php
 * @Description    : 修改rizhuti-v2中后台的一些文章选项
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_create_post_metabox')) {
    /**
     * 修改文章页面的后台选项
     */
    function rf_create_post_metabox()
    {
        CSF::$args['metabox_options']['_prefix_wppay_options']['title'] = 'RF主题设置';
        $fields = CSF::$args['sections']['_prefix_wppay_options'][0]['fields'];
        $is_recommand_index = null;
        $wppay_type_index = null;
        foreach ($fields as $key => $value) {
            if (isset($value['id'])) {
                if ($value['id'] == 'is_recommand') {
                    $is_recommand_index = $key;
                } elseif ($value['id'] == 'wppay_type') {
                    $wppay_type_index = $key;
                }
            }
        }
        if (!is_null($is_recommand_index)) return;
        array_pop($fields[$wppay_type_index]['options']);
        array_pop($fields[$wppay_type_index]['options']);
        unset($fields[$wppay_type_index]['options']['4']);
        $fields[$wppay_type_index]['options']['3'] = esc_html__('下载资源', 'rizhuti-v2');
        $fields = array_merge([
            [
                'id' => 'is_recommand',
                'type' => 'switcher',
                'title' => '是否为推荐文章',
                'default' => false,
            ],
            [
                'id' => 'disable_top_thumbnail',
                'type' => 'switcher',
                'title' => '关闭顶部特色图',
                'desc' => '在当前文章非视频类型文章时关闭主题设置中设置的顶部特色图(如果开启的话)',
                'default' => false,
            ],
            [
                'id' => 'archive_block_style',
                'type' => 'select',
                'title' => '文章列表展示风格',
                'desc' => '如果跟随设置,将根据"主题设置"中的设定来展示',
                'placeholder' => '选择风格',
                'inline' => true,
                'options' => [
                    '1' => '跟随设置',
                    '2' => '小区块',
                    '3' => '大区块'
                ],
                'default' => '1'
            ],
            [
                'id' => 'is_merge_thumbnail',
                'type' => 'select',
                'title' => '大区块特色图和内容合并',
                'desc' => '推荐对置顶的文章启用该项以调整美观度',
                'placeholder' => '是否合并',
                'inline' => true,
                'options' => [
                    '1' => '跟随设置',
                    '2' => '合并',
                    '3' => '不合并'
                ],
                'dependency' => array('archive_block_style', '!=', '2'),
            ],
            [
                'id' => 'content_summary',
                'type' => 'textarea',
                'title' => '摘要',
                'subtitle' => '在文章列表页显示的摘要,如果不设置则直接截取内容中的一部分',
                'sanitize' => false,
            ],
        ], $fields);
        CSF::$args['sections']['_prefix_wppay_options'][0]['fields'] = $fields;
        CSF::set_used_fields($fields);
    }
}
add_action('after_setup_theme', 'rf_create_post_metabox', -99);
add_action('init', 'rf_create_post_metabox', -99);
add_action('switch_theme', 'rf_create_post_metabox', -99);
