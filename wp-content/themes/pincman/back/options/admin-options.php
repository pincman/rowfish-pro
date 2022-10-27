<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.
$prefix = _OPTIONS_PRE;

// Admin Options
CSF::createSection($prefix, [
    'title'  => '子主题增加选项',
    'icon'   => 'fa fa-circle',
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
]);
