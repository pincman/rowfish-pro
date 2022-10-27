<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-23 14:54:41 +0800
 * @Path           : /wp-content/themes/rowfish/course/options/post.php
 * @Description    : 后台课程内容页选项
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_get_course_post_options')) {
    /**
     * 后台课程选项
     *
     * @return void
     */
    function rf_get_course_post_options()
    {
        $options = ['hook' => '_prefix_rf_course_post_options', 'params' => [
            'title' => '课程设置',
            'post_type' => 'course',
            'data_type' => 'unserialize',
            'priority' => 'high'
        ], 'fields' => array(
            array(
                'id' => 'is_recommand',
                'type' => 'switcher',
                'title' => '是否为推荐课程',
                'default' => false,
            ),
            array(
                'id' => 'shop_enabled',
                'type' => 'switcher',
                'title' => '是否收费',
                'desc' => '如果不开启,则该资源对所有用户都免费',
                'default' => false,
            ),
            array(
                'id' => 'wppay_price',
                'type' => 'text',
                'title' => esc_html__('收费价格', 'rizhuti-v2'),
                'desc' => esc_html__('单位RMB,价格为0时，如果启用VIP会员权限，则普通用户不能购买。只允许会员下载，反之普通用户可以购买', 'rizhuti-v2'),
                'default' => _cao('wppay_price', '0'),
                'validate' => 'csf_validate_numeric',
                'dependency' => array('shop_enabled', '!=', '0'),
            ),

            array(
                'id' => 'wppay_vip_auth',
                'type' => 'select',
                'title' => esc_html__('VIP会员权限', 'rizhuti-v2'),
                'subtitle' => esc_html__('权限关系是包含关系，终身可查看年月', 'rizhuti-v2'),
                'inline' => true,
                'options' => rf_get_vip_enabled_names_for_options(),
                'default' => _cao('wppay_vip_auth', '0'),
                'dependency' => array('shop_enabled', '!=', '0'),
            ),
            array(
                'id' => '_paynum',
                'type' => 'number',
                'title' => '已售数量',
                'desc' => '可自定义修改数字',
                'unit' => '个',
                'output' => '.heading',
                'output_mode' => 'width',
                'default' => 0,
                'dependency' => array('shop_enabled', '==', '1'),
            ),
            array(
                'id' => 'hero_image',
                'type' => 'upload',
                'title' => '背景图片',
                'dsec' => '半高或全屏背景图片,不填则使用RowFish主题设置中添加的随机图片',
            ),
            array(
                'id' => 'content_summary',
                'type' => 'textarea',
                'title' => '摘要',
                'subtitle' => '在课程列表页显示的摘要,如果不设置则直接截取内容中的一部分',
                'sanitize' => false,
            ),
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

        )];

        // 课程选项
        if ((bool)_cao('is_course_status_filter', false) && !empty(_cao('course_status'))) {
            $status = [];
            foreach (_cao('course_status') as $item) {
                $status[$item['slug']] = $item['name'];
            }
            array_push($options['fields'], array(
                'id' => 'course_status',
                'type' => 'select',
                'title' => '课程状态',
                'inline' => true,
                'options' => $status,
                'default' => 0,
            ));
        }
        if ((bool)_cao('is_course_level_filter', false) && !empty(_cao('course_levels'))) {
            $levels = [];
            foreach (_cao('course_levels') as $level) {
                $levels[$level['slug']] = $level['name'];
            }
            array_push($options['fields'], array(
                'id' => 'course_level',
                'type' => 'select',
                'title' => '难度等级',
                'inline' => true,
                'options' => $levels,
                'default' => 0,
            ));
        }
        if (isAnsPress()) {
            array_push($options['fields'], array(
                'id' => 'course_question',
                'type' => 'select',
                'title' => '关联的问答分类',
                'placeholder' => '选择分类',
                'inline' => true,
                'options' => array(),
                'default' => null,
            ));
        }
        if (isDocsPress()) {
            array_push($options['fields'], array(
                'id' => 'course_document',
                'type' => 'select',
                'title' => '关联的文档',
                'placeholder' => '选择文档',
                'inline' => true,
                'options' => array(),
                'default' => null,
            ));
        }
        $options['fields'] = array_merge(
            $options['fields'],
            [
                array(
                    'id' => 'course_intro',
                    'type' => 'switcher',
                    'title' => '介绍视频',
                    'desc' => '是否添加一个教程介绍视频',
                    'default' => false,
                ),
                array(
                    'id' => 'course_intro_title',
                    'type' => 'text',
                    'title' => '介绍视频名称',
                    'default' => '',
                    'dependency' => array('course_intro', '==', '1'),
                ),
                array(
                    'id' => 'course_intro_video',
                    'type' => 'text',
                    'title' => '介绍视频url',
                    'default' => '',
                    'dependency' => array('course_intro', '==', '1'),
                ),
                array(
                    'id'                     => 'course_wppay_down',
                    'type'                   => 'group',
                    'title'                  => esc_html__('下载资源', 'rizhuti-v2'),
                    'subtitle'               => esc_html__('支持多个下载地址，支持https:,thunder:,magnet:,ed2k 开头地址', 'rizhuti-v2'),
                    'accordion_title_number' => true,
                    'fields'                 => array(
                        array(
                            'id'      => 'name',
                            'type'    => 'text',
                            'title'   => esc_html__('资源名称', 'rizhuti-v2'),
                            'default' => esc_html__('资源名称', 'rizhuti-v2'),
                        ),
                        array(
                            'id'       => 'url',
                            'type'     => 'upload',
                            'title'    => esc_html__('下载地址', 'rizhuti-v2'),
                            'sanitize' => false,
                            'default'  => null,
                            'dependency' => array('online', '==', true),
                        ),
                        array(
                            'id'    => 'pwd',
                            'type'  => 'text',
                            'title' => esc_html__('下载密码', 'rizhuti-v2'),
                            'dependency' => array('online', '==', true),
                        ),
                        array(
                            'id'    => 'free',
                            'type'  => 'switcher',
                            'default' => false,
                            'title' => esc_html__('单个免费', 'rizhuti-v2'),
                            'desc'       => '只在整篇收费的情况下有效',
                            'dependency' => array('shop_enabled', '==', true, true)
                        ),
                        array(
                            'id'    => 'online',
                            'type'  => 'switcher',
                            'default' => false,
                            'title' => esc_html__('是否上线', 'rizhuti-v2'),
                            'desc'       => '如果不开启则只显示下载按钮,但无法点击下载',
                        ),
                    ),
                ),
                array(
                    'id' => 'course_chapter_info',
                    'type' => 'repeater',
                    'title' => '教程大纲',
                    'fields' => array(
                        array(
                            'id' => 'title',
                            'type' => 'text',
                            'title' => '标题',
                            'default' => '标题',
                        ),
                        array(
                            'id' => 'video',
                            'type' => 'text',
                            'title' => '视频url',
                            'default' => '',
                            'dependency' => array('online', '==', '1'),
                        ),
                        array(
                            'id' => 'pic',
                            'type' => 'upload',
                            'title' => '视频封面',
                            'default' => '',
                            'dependency' => array('online', '==', '1'),
                        ),
                        array(
                            'id' => 'online',
                            'type' => 'switcher',
                            'title' => '是否上线',
                            'desc' => '当前视频是否上线',
                            'default' => false,
                        ),
                        array(
                            'id' => 'enabled_doc',
                            'type' => 'switcher',
                            'title' => '是否有文档',
                            'desc' => '开启后可选择当前视频的关联文档',
                            'default' => false,
                        ),
                        array(
                            'id' => 'free',
                            'type' => 'switcher',
                            'title' => '是否免费',
                            'desc' => '单集免费(只对收费教程有效)',
                            'dependency' => array('shop_enabled', '!=', '0', true),
                            'default' => false,
                        ),
                    ),
                ),
            ]
        );
        return $options;
    }
}
if (!function_exists('rf_create_course_post_metabox')) {
    /**
     * 后台课程选项框
     *
     * @return void
     */
    function rf_create_course_post_metabox()
    {
        $data = rf_get_course_post_options();
        $cate_index = array_search('course_question', array_map(function ($arr) {
            return $arr['id'] ?? '';
        }, $data['fields']));
        if (isAnsPress()) {
            $question_cat_query = new WP_Term_Query([
                'taxonomy' => 'question_category',
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => false,
            ]);
            $question_categories = [];
            foreach ($question_cat_query->get_terms() as $key => $item) {
                $question_categories[$item->term_id] = $item->name;
            }

            if (count($question_categories)) {
                $data['fields'][$cate_index]['options'] = $question_categories;
            } else {
                unset($data['fields'][$cate_index]);
            }
        } else {
            unset($data['fields'][$cate_index]);
        }
        if (isDocsPress()) {
            $document_query = new WP_Query([
                'post_type' => 'docs',
                'posts_per_page' => -1, // phpcs:ignore
                'post_parent' => 0,
                'orderby' => [
                    'menu_order' => 'ASC',
                    'date' => 'DESC',
                ],
                'post_status' => 'publish',
                'meta_query' => [[
                    'key' => 'is_course_docs', 'compare' => '=', 'value' => 1, 'type' => 'NUMERIC',
                ]],
            ]);
            $post = rf_get_query_post();
            $child_docs = [];
            if ($post) {
                $current_doc = get_post_meta($post->ID, 'course_document', true);
                if ($current_doc) {
                    $children_query = new WP_Query(
                        array(
                            'post_type' => 'docs',
                            'posts_per_page' => -1, // phpcs:ignore
                            'post_parent' => $current_doc,
                            'post_status' => 'publish',
                            'orderby' => array(
                                'menu_order' => 'ASC',
                                'date' => 'DESC',
                            ),
                        )
                    );
                    if ($children_query->have_posts()) {
                        foreach ($children_query->get_posts() as $child_doc) {
                            $child_docs[$child_doc->ID] = $child_doc->post_title;
                        }
                    }
                }
            }
            if (count($child_docs) > 0) {
                foreach ($data['fields'] as $index => $value) {
                    if (isset($value['fields']) && in_array('video', array_column($value['fields'], 'id'))) {
                        $fields = $value['fields'];
                        array_push($fields, [
                            'id' => 'doc',
                            'type' => 'select',
                            'title' => '关联的文档',
                            'placeholder' => '选择文档',
                            'desc' => '需要先选择教程关联文档保存后方可选择',
                            'inline' => true,
                            'options' => $child_docs,
                            'default' => null,
                            'dependency' => array('enabled_doc', '==', '1'),
                        ]);
                        $value['fields'] = $fields;
                        $data['fields'][$index] = $value;
                    }
                }
            }
            $docs = [];

            if ($document_query->have_posts()) {
                while ($document_query->have_posts()) {
                    $document_query->the_post();
                    $docs[get_the_ID()] = get_the_title();
                }
            }
            $doc_index = array_search('course_document', array_map(function ($arr) {
                return $arr['id'] ?? '';
            }, $data['fields']));
            if (count($docs)) {
                $data['fields'][$doc_index]['options'] = $docs;
            } else {
                unset($data['fields'][$doc_index]);
            }
        }

        CSF::createMetabox($data['hook'], $data['params']);
        CSF::createSection($data['hook'], array(
            'fields' => $data['fields']
        ));
    }
}
