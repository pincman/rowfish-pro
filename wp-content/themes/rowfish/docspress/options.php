<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-23 14:54:46 +0800
 * @Path           : /wp-content/themes/rowfish/docspress/options.php
 * @Description    : 为文档添加一些选项用于关联视频课程
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
CSF::createMetabox('_prefix_rf_docs_options', [
    'title'     => 'RF主题选项',
    'post_type' => 'docs',
    'data_type' => 'unserialize',
    'priority'  => 'high'
]);
CSF::createSection('_prefix_rf_docs_options', array(
    'fields' => [
        [
            'id'      => 'is_course_docs',
            'type'    => 'switcher',
            'title'   => '是否为视频课程文档',
            'desc'   => '如果当前文档是子文档无论是否选择都会继承父文档属性',
            'default' => false,
        ],
        array(
            'id' => 'content_summary',
            'type' => 'textarea',
            'title' => '摘要',
            'subtitle' => '在文档列表页显示的摘要,如果不设置则直接截取内容中的一部分',
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
    ]
));
