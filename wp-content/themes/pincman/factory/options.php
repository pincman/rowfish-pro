<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 07:54:23
 * @updated_at: 2021-06-02 06:07:05
 * @description: 主题选项
 * @homepage: https://pincman.cn
 */
function get_current_post()
{
    global $post;

    if (empty($post) && array_key_exists('post', $_GET)) {
        $post = get_post($_GET['post']);
    }

    // Optional: get an empty post object from the post_type
    if (empty($post) && array_key_exists('post_type', $_GET)) {
        $object = new stdClass();
        $object->post_type = $_GET['post_type'];
        return new WP_Post($object);
    }

    if (empty($post)) {
        return null;
    }

    return $post;
}

/**
 * docspress文档选项
 */
$docs_options = ['hook' => '_prefix_pm_docs_options', 'params' => [
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
/**
 * 主题设置选项
 */
$admin_options = [
    'hook' => _OPTIONS_PRE, 'params' => [
        'title'  => 'Pincman主题',
        'icon'   => 'fa fa-circle',
    ],
    'fields' => [
        [
            'id'         => 'download_limit',
            'type'       => 'text',
            'title'      => '下载限速',
            'subtitle'      => '0为不限速,以mb为单位',
            'default'    => 0,
        ],
        [
            'id'         => 'course_status_filter',
            'type'       => 'switcher',
            'title'      => '启用课程状态筛选',
            'label'      => '显示分类页文章列表的课程状态筛选(必须在父主题"启用内页筛选条功能")',
            'default'    => true,
        ],
        [
            'id'         => 'course_level_filter',
            'type'       => 'switcher',
            'title'      => '启用课程难度等级筛选',
            'label'      => '显示分类页文章列表的课程难度筛选(必须在父主题"启用内页筛选条功能")',
            'default'    => true,
        ],
        [
            'id'         => 'free_onlogin_down',
            'type'       => 'switcher',
            'title'      => '免费资源免登陆',
            'label'      => '免费资源无需登录即可观看,下载,阅读',
            'default'    => false,
        ],
        [
            'id'    => 'top_images',
            'type'       => 'repeater',
            'title' => '随机顶部背景图',
            'subtitle'  => '分类,标签等文章列表集合的顶部背景图,也可以在分类和标签中单独指定',
            'accordion_title_number' => true,
            'fields'                 => [
                [
                    'id'       => 'url',
                    'type'     => 'upload',
                    'title'    => '图片',
                    'default' => get_template_directory_uri() . '/assets/img/logo.png',
                ],
            ],
            'default' => [
                ['url' => 'https://pic.pincman.com/media/20210518114708.jpg'],
                ['url' => 'https://pic.pincman.com/media/20210518114420.png'],
                ['url' => 'https://pic.pincman.com/media/20210518114740.png']
            ]
        ],
        [
            'id'    => 'hero_images',
            'type'       => 'repeater',
            'title' => '随机半高/全景图',
            'subtitle'  => '视频等区块背景封面图,也可以在文章内单独指定',
            'accordion_title_number' => true,
            'fields'                 => [
                [
                    'id'       => 'url',
                    'type'     => 'upload',
                    'title'    => '图片',
                    'default' => get_template_directory_uri() . '/assets/img/logo.png',
                ],
            ],
            'default' => [
                ['url' => 'https://pic.pincman.com/media/20210423110054.png'],
                ['url' => 'https://pic.pincman.com/media/20210423115122.png'],
                ['url' => 'https://pic.pincman.com/media/20210423130851.png']
            ]
        ],
        [
            'id'    => 'post_images',
            'type'       => 'repeater',
            'title' => '文章随机特色图片',
            'subtitle'  => '在文章中没有指定是会使用以下随机图片',
            'accordion_title_number' => true,
            'fields'                 => [
                [
                    'id'       => 'url',
                    'type'     => 'upload',
                    'title'    => '图片',
                    'default' => get_template_directory_uri() . '/assets/img/logo.png',
                ],
            ],
            'default' => [
                ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/1.jpg'],
                ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/2.jpg'],
                ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/2.jpg'],
                ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/3.jpg'],
                ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/4.jpg'],
            ]
        ],

    ],
];
/**
 * 文章设置选项字段
 */
$post_options = ['hook' => '_prefix_pm_options', 'params' => [
    'title'     => 'Picman主题选项',
    'post_type' => 'post',
    'data_type' => 'unserialize',
    'priority'  => 'high'
], 'fields' => [
    // 常规字段
    [
        'id'       => 'summary',
        'type'     => 'textarea',
        'title'    => esc_html__('简要', 'rizhuti-v2'),
        'subtitle' => esc_html__('字数控制到50以内最佳', 'rizhuti-v2'),
        'sanitize' => false,
    ],
    [
        'id'         => 'hero_image',
        'type'       => 'upload',
        'title'      => esc_html__('背景图片', 'rizhuti-v2'),
        'dsec'       => '半高或全屏背景图片,不填则使用主题设置中添加的随机图片',
        'dependency' => array('wppay_type', 'any', '5,6'),
    ],
    // 商城字段
    [
        'id'      => 'wppay_type',
        'type'    => 'select',
        'title'   => esc_html__('资源类型', 'rizhuti-v2'),
        'inline'  => true,
        'options' => array(
            '0' => esc_html__('不启用', 'rizhuti-v2'),
            '1' => esc_html__('付费全文', 'rizhuti-v2'),
            '2' => esc_html__('付费隐藏内容', 'rizhuti-v2'),
            // 付费下载已经和下载免费下载融合为下载资源,所以此处不在需要
            // '3' => esc_html__('付费下载', 'rizhuti-v2'),
            '4' => esc_html__('下载资源', 'rizhuti-v2'),
            '5' => esc_html__('视频教程', 'rizhuti-v2'),
            '7' => esc_html__('开源推荐', 'rizhuti-v2'),
            // 因为本主题不需要相册所以直接去除
            // '6' => esc_html__('图片相册', 'rizhuti-v2'),
        ),
        'inline'  => true,
        'default' => 0,
    ],
    [
        'id'         => 'wppay_vip_auth',
        'type'       => 'select',
        'title'      => esc_html__('VIP权限', 'rizhuti-v2'),
        'subtitle'   => esc_html__('权限关系是包含关系，终身可查看按年付费', 'rizhuti-v2'),
        'inline'     => true,
        'options'    => array(
            '0' => esc_html__('免费', 'rizhuti-v2'),
            '2' => esc_html__('年费订阅者', 'rizhuti-v2'),
            '3' => esc_html__('永久订阅者', 'rizhuti-v2'),
        ),
        'default'    => 0,
        'dependency' => array('wppay_type', 'any', '1,2,3,4,5,6'),
    ],
    [
        'id'         => 'wppay_price',
        'type'       => 'text',
        'title'      => esc_html__('收费价格', 'rizhuti-v2'),
        'desc'       => esc_html__('单位RMB,价格为0时，如果启用订阅会员权限，则普通用户不能购买。只允许会员下载，反之普通用户可以购买', 'rizhuti-v2'),
        'default'    => '0',
        'validate'   => 'csf_validate_numeric',
        'dependency' => array('wppay_vip_auth', '>', '0'),
    ],
    [
        'type'       => 'content',
        'content'    => '<b style="color: red;">在文章内容中插入短代码：</b> [rihide] 这里面填写要隐藏的内容 [/rihide] ',
        'dependency' => array('wppay_type', '==', '2'),
    ],
    [
        'type'       => 'content',
        'content'    => '<b style="color: red;">在文章内容开头和结尾插入短代码：</b> [rihide] 这里面是文章内容 [/rihide] ',
        'dependency' => array('wppay_type', '==', '1'),
    ],
    // 开源项目
    [
        'id'         => 'wppay_oss_name',
        'type'       => 'text',
        'title'      => esc_html__('项目名称', 'rizhuti-v2'),
        'sanitize'   => false,
        'dependency' => array('wppay_type', '==', '7'),
    ],
    [
        'id'         => 'wppay_oss_website',
        'type'       => 'text',
        'title'      => esc_html__('主页/文档', 'rizhuti-v2'),
        'subtitle'   => esc_html__('为空则不显示', 'rizhuti-v2'),
        'sanitize'   => false,
        'dependency' => array('wppay_type', '==', '7'),
    ],
    [
        'id'         => 'wppay_oss_git',
        'type'       => 'text',
        'title'      => esc_html__('仓库地址', 'rizhuti-v2'),
        'subtitle'   => esc_html__('为空则不显示', 'rizhuti-v2'),
        'sanitize'   => false,
        'dependency' => array('wppay_type', '==', '7'),
    ],
    [
        'id'         => 'wppay_oss_demourl',
        'type'       => 'text',
        'title'      => esc_html__('演示地址', 'rizhuti-v2'),
        'subtitle'   => esc_html__('为空则不显示', 'rizhuti-v2'),
        'sanitize'   => false,
        'dependency' => array('wppay_type', '==', '7'),
    ],
    [
        'id'         => 'wppay_oss_agreement',
        'type'       => 'text',
        'title'      => esc_html__('开源协议', 'rizhuti-v2'),
        'subtitle'   => esc_html__('默认为 MIT', 'rizhuti-v2'),
        'sanitize'   => false,
        'default' => 'MIT',
        'dependency' => array('wppay_type', '==', '7'),
    ],
    [
        'id'         => 'wppay_oss_info',
        'type'       => 'repeater',
        'title'      => esc_html__('项目其他信息', 'rizhuti-v2'),
        'fields'     => array(
            array(
                'id'      => 'title',
                'type'    => 'text',
                'title'   => esc_html__('标题', 'rizhuti-v2'),
                'default' => esc_html__('标题', 'rizhuti-v2'),
            ),
            array(
                'id'       => 'desc',
                'type'     => 'text',
                'title'    => esc_html__('描述内容', 'rizhuti-v2'),
                'sanitize' => false,
                'default'  => esc_html__('这里是描述内容', 'rizhuti-v2'),
            ),
        ),
        'dependency' => array('wppay_type', '==', '7'),
    ],
    // 视频教程
    [
        'id'      => 'wppay_course_status',
        'type'    => 'select',
        'title'   => '课程状态',
        'inline'  => true,
        'options' => array(
            '0' => '策划中',
            '1' => '待发布',
            '2' => '更新中',
            '3' => '已完结',
        ),
        'inline'  => true,
        'default' => 0,
        'dependency' => array('wppay_type', '==', '5'),
    ],
    [
        'id'      => 'wppay_course_level',
        'type'    => 'select',
        'title'   => '难度等级',
        'inline'  => true,
        'options' => array(
            '0' => '入门',
            '1' => '进阶',
            '2' => '大师',
        ),
        'inline'  => true,
        'default' => 0,
        'dependency' => array('wppay_type', '==', '5'),
    ],
    [
        'id'      => 'wppay_course_question',
        'type'    => 'select',
        'title'   => '关联的问答分类',
        'placeholder' => '选择分类',
        'inline'  => true,
        'options'     => [],
        'default' => null,
        'dependency' => array('wppay_type', '==', '5'),
    ],
    [
        'id'      => 'wppay_course_document',
        'type'    => 'select',
        'title'   => '关联的文档',
        'placeholder' => '选择文档',
        'inline'  => true,
        'options'     => [],
        'default' => null,
        'dependency' => array('wppay_type', '==', '5'),
    ],
    [
        'id'      => 'wppay_course_intro',
        'type'    => 'switcher',
        'title'   => '介绍视频',
        'desc'   => '是否添加一个教程介绍视频',
        'default' => false,
    ],
    [
        'id'      => 'wppay_course_intro_title',
        'type'    => 'text',
        'title'   => '介绍视频名称',
        'default' => '',
        'dependency' => array('wppay_course_intro', '==', '1'),
    ],
    [
        'id'      => 'wppay_course_intro_video',
        'type'    => 'text',
        'title'   => '介绍视频url',
        'default' => '',
        'dependency' => array('wppay_course_intro', '==', '1'),
    ],
    [
        'id'         => 'wppay_chapter_info',
        'type'       => 'repeater',
        'title'      => esc_html__('教程大纲', 'rizhuti-v2'),
        'fields'     => array(
            array(
                'id'      => 'title',
                'type'    => 'text',
                'title'   => esc_html__('标题', 'rizhuti-v2'),
                'default' => esc_html__('标题', 'rizhuti-v2'),
            ),
            array(
                'id'      => 'video',
                'type'    => 'text',
                'title'   => esc_html__('视频url', 'rizhuti-v2'),
                'default' => '',
                'dependency' => array('online', '==', '1'),
            ),
            array(
                'id'      => 'online',
                'type'    => 'switcher',
                'title'   => esc_html__('是否上线', 'rizhuti-v2'),
                'desc'   => esc_html__('当前视频是否上线', 'rizhuti-v2'),
                'default' => false,
            ),
            array(
                'id'      => 'free',
                'type'    => 'switcher',
                'title'   => esc_html__('是否免费', 'rizhuti-v2'),
                'desc'   => esc_html__('单集免费(只对收费教程有效)', 'rizhuti-v2'),
                'default' => false,
            ),
        ),
        'dependency' => array('wppay_type', '==', '5'),
    ],
    // 下载资源
    [
        'id'                     => 'wppay_down',
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
            ),
            array(
                'id'    => 'online',
                'type'  => 'switcher',
                'default' => false,
                'title' => esc_html__('是否上线', 'rizhuti-v2'),
                'desc'       => '如果不开启则只显示下载按钮,但无法点击下载',
            ),
        ),
        'dependency' => array('wppay_type', '!=', '7'),
    ],
    [
        'id'         => 'wppay_demourl',
        'type'       => 'text',
        'title'      => esc_html__('演示地址', 'rizhuti-v2'),
        'subtitle'   => esc_html__('为空则不显示', 'rizhuti-v2'),
        'sanitize'   => false,
        'dependency' => array('wppay_type', '==', '4'),
    ],
    [
        'id'         => 'wppay_info',
        'type'       => 'repeater',
        'title'      => esc_html__('下载资源其他信息', 'rizhuti-v2'),
        'fields'     => array(
            array(
                'id'      => 'title',
                'type'    => 'text',
                'title'   => esc_html__('标题', 'rizhuti-v2'),
                'default' => esc_html__('标题', 'rizhuti-v2'),
            ),
            array(
                'id'       => 'desc',
                'type'     => 'text',
                'title'    => esc_html__('描述内容', 'rizhuti-v2'),
                'sanitize' => false,
                'default'  => esc_html__('这里是描述内容', 'rizhuti-v2'),
            ),
        ),
        'dependency' => array('wppay_type', 'any', '4,5,6'),
    ],
    // 相册字段,因为本主题不需要所以直接去除
    // [
    //     'title'      => esc_html__('插入图片相册', 'rizhuti-v2'),
    //     'id'         => "hero_gallery_data",
    //     'type'       => 'gallery',
    //     'desc'       => '（需要文章 形式 设置为相册格式,布局风格设置为背景才显示模块）',
    //     'sanitize'   => false,
    //     'dependency' => array('wppay_type', '==', '6'),
    // ],
    // [
    //     'title'      => esc_html__('前几张免费查看？', 'rizhuti-v2'),
    //     'id'         => "hero_gallery_data_free_num",
    //     'type'       => 'text',
    //     'desc'       => '0为不设置，如果设置2则表示前两张免费查看，其余部分需要付费',
    //     'default'    => '0',
    //     'dependency' => array('wppay_type', '==', '6'),
    // ],
]];
/**
 * 分类及标签设置选项
 */
$taxonomy_options = ['hook' => 'cat_pm_options', 'params' => [
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

/**
 * 创建选项
 * @return void 
 */
function pm_create_options($type, $data)
{
    if ($type == 'docs') {
        CSF::createMetabox($data['hook'], $data['params']);
        CSF::createSection($data['hook'], array(
            'fields' => $data['fields']
        ));
    } else if ($type == 'admin') {
        CSF::createSection($data['hook'], array_merge($data['params'], ['fields' => $data['fields']]));
    } else if ($type == 'post' && !apply_filters('is_site_shop', empty(_cao('is_site_shop', '1')))) {
        $question_cat_query = new WP_Term_Query([
            'taxonomy' => 'question_category',
            'orderby'                => 'name',
            'order'                  => 'ASC',
            'hide_empty'             => false,
        ]);
        $question_categories = [];
        foreach ($question_cat_query->get_terms() as $key => $item) {
            $question_categories[$item->term_id] = $item->name;
        }
        $cate_index = array_search('wppay_course_question', array_map(function ($arr) {
            return $arr['id'] ?? '';
        }, $data['fields']));
        if (count($question_categories)) {
            $data['fields'][$cate_index]['options'] = $question_categories;
        } else {
            unset($data['fields'][$cate_index]);
        }
        $document_query  = new WP_Query([
            'post_type'      => 'docs',
            'posts_per_page' => -1, // phpcs:ignore
            'post_parent'    => 0,
            'orderby'        => [
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ],
            'meta_query' => [[
                'key' =>  'course', 'compare' => '=', 'value' => 1, 'type' => 'NUMERIC',
            ]]
        ]);
        $post = get_current_post();
        $child_docs = [];
        if ($post) {
            $current_doc = get_post_meta($post->ID, 'wppay_course_document', true);
            if ($current_doc) {
                $children_query = new WP_Query(
                    array(
                        'post_type'      => 'docs',
                        'posts_per_page' => -1, // phpcs:ignore
                        'post_parent'    => $current_doc,
                        'orderby'        => array(
                            'menu_order' => 'ASC',
                            'date'       => 'DESC',
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
        $docs = [];

        if ($document_query->have_posts()) {
            while ($document_query->have_posts()) {
                $document_query->the_post();
                $docs[get_the_ID()] = get_the_title();
            }
        }
        $doc_index = array_search('wppay_course_document', array_map(function ($arr) {
            return $arr['id'] ?? '';
        }, $data['fields']));
        if (count($docs)) {
            $data['fields'][$doc_index]['options'] = $docs;
        } else {
            unset($data['fields'][$doc_index]);
        }
        CSF::createMetabox($data['hook'], $data['params']);
        CSF::createSection($data['hook'], array(
            'fields' => $data['fields']
        ));
    } else if ($type == 'taxonomy') {
        CSF::createTaxonomyOptions($data['hook'], $data['params']);
        CSF::createSection($data['hook'], ['fields' => $data['fields']]);
    }
}
pm_create_options('docs', $docs_options);
pm_create_options('admin', $admin_options);
pm_create_options('post', $post_options);
pm_create_options('taxonomy', $taxonomy_options);
