<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

CSF::createSection($prefix_meta_opts, array(
    'fields' => $fields
));

// MetaBox Options
if (!apply_filters('is_site_shop', empty(_cao('is_site_shop', '1')))) :
    // 付费meta
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
    // var_dump($question_cat_query);
    $prefix_meta_opts = '_prefix_pm_options';
    CSF::createMetabox($prefix_meta_opts, array(
        'title'     => esc_html__('Picman主题选项', 'rizhuti-v2'),
        'post_type' => 'post',
        'data_type' => 'unserialize',
        'priority'  => 'high',
    ));
    $fields = [
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
    ];

    // 是否订阅者资源
    $fields = array_merge($fields, [
        [
            'id'      => 'wppay_type',
            'type'    => 'select',
            'title'   => esc_html__('资源类型', 'rizhuti-v2'),
            'inline'  => true,
            'options' => array(
                '0' => esc_html__('不启用', 'rizhuti-v2'),
                '1' => esc_html__('付费全文', 'rizhuti-v2'),
                '2' => esc_html__('付费隐藏内容', 'rizhuti-v2'),
                // '3' => esc_html__('付费下载', 'rizhuti-v2'),
                '4' => esc_html__('下载资源', 'rizhuti-v2'),
                '5' => esc_html__('视频教程', 'rizhuti-v2'),
                '7' => esc_html__('开源推荐', 'rizhuti-v2'),
                '6' => esc_html__('图片相册', 'rizhuti-v2'),
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
    ]);

    // 订阅者文章
    $fields = array_merge(
        $fields,
        [
            [
                'type'       => 'content',
                'content'    => '<b style="color: red;">在文章内容中插入短代码：</b> [rihide] 这里面填写要隐藏的内容 [/rihide] ',
                'dependency' => array('wppay_type', '==', '2'),
            ],
            [
                'type'       => 'content',
                'content'    => '<b style="color: red;">在文章内容开头和结尾插入短代码：</b> [rihide] 这里面是文章内容 [/rihide] ',
                'dependency' => array('wppay_type', '==', '1'),
            ]
        ]
    );

    // 订阅者相册
    $fields = array_merge($fields, [
        [
            'title'      => esc_html__('插入图片相册', 'rizhuti-v2'),
            'id'         => "hero_gallery_data",
            'type'       => 'gallery',
            'desc'       => '（需要文章 形式 设置为相册格式,布局风格设置为背景才显示模块）',
            'sanitize'   => false,
            'dependency' => array('wppay_type', '==', '6'),
        ],
        [
            'title'      => esc_html__('前几张免费查看？', 'rizhuti-v2'),
            'id'         => "hero_gallery_data_free_num",
            'type'       => 'text',
            'desc'       => '0为不设置，如果设置2则表示前两张免费查看，其余部分需要付费',
            'default'    => '0',
            'dependency' => array('wppay_type', '==', '6'),
        ],
    ]);

    // 开源项目
    $fields = array_merge($fields, [
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
    ]);

    // 视频教程
    $fields = array_merge($fields, [
        // [
        //     'id'      => 'is_online',
        //     'type'    => 'switcher',
        //     'title'   => esc_html__('教程是否上线', 'rizhuti-v2'),
        //     'desc'   => esc_html__('处于更新中的视频教程', 'rizhuti-v2'),
        //     'default' => false,
        //     'dependency' => array('wppay_type', '==', '5'),
        // ],
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
        // [
        //     'id'      => 'online',
        //     'type'    => 'switcher',
        //     'title'   => esc_html__('是否上线', 'rizhuti-v2'),
        //     'desc'   => esc_html__('当前视频是否上线', 'rizhuti-v2'),
        //     'default' => false,
        // ]
    ]);
    if (count($question_categories)) {
        $fields[] =  [
            'id'      => 'wppay_course_question',
            'type'    => 'select',
            'title'   => '关联的问答分类',
            'placeholder' => '选择分类',
            'inline'  => true,
            'options'     => $question_categories,
            'default' => null,
            'dependency' => array('wppay_type', '==', '5'),
        ];
    }
    $fields[] =   [
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
                'default' => esc_html__('视频url', 'rizhuti-v2'),
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
    ];
    // 下载资源
    $fields = array_merge($fields, [
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
    ]);
    CSF::createSection($prefix_meta_opts, array(
        'fields' => $fields
    ));

endif;

// $prefix_page_meta_opts = '_prefix_pm_page_options';
// CSF::createMetabox($prefix_page_meta_opts, array(
//     'title'     => esc_html__('Picman主题选项', 'rizhuti-v2'),
//     'post_type' => 'page',
//     'data_type' => 'unserialize',
//     'priority'  => 'high',
// ));
// CSF::createSection($prefix_page_meta_opts, array(
//     'fields' => array(

//         array(
//             'id'      => 'enabled_top_image',
//             'type'    => 'switcher',
//             'title'   => '启用显示顶部背景图',
//             'desc'    => '开启后,没有设置特色图片则显示随机图',
//             'default'    => true,
//         ),
//     ),
// ));
