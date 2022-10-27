<?php
function get_doc_options()
{
    return
        ['hook' => '_prefix_pm_docs_options', 'params' => [
            'title'     => 'Picman主题选项',
            'post_type' => 'docs',
            'data_type' => 'unserialize',
            'priority'  => 'high'
        ], 'fields' => [
            [
                'id'      => 'course',
                'type'    => 'switcher',
                'title'   => '是否为视频文章专用文档',
                'desc'   => '如果当前文档是子文档无论是否选择都会继承父文档属性',
                'default' => false,
            ]
        ]];
}
