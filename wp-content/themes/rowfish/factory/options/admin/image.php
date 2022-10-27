<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:24:37 +0800
 * @Path           : /wp-content/themes/rowfish/factory/options/admin/image.php
 * @Description    : 修改rizhuti-v2主题设置中的选项(图片设置)
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_set_admin_image_meta')) {
    /**
     * 后台-主题设置-图片设置
     */
    function rf_set_admin_image_meta()
    {
        if (
            count(CSF::$args['sections']['_riprov2_options']) <= 3
            || !isset(CSF::$args['sections']['_riprov2_options'][4]['fields'])
        ) {
            return;
        }
        // CSF::$args['sections']['_riprov2_options'][4]['title'] = '图片设置';
        $image_fields = CSF::$args['sections']['_riprov2_options'][4]['fields'];
        $default_thumb_index = null;
        foreach ($image_fields as $key => $value) {
            if (isset($value['id'])) {
                if ($value['id'] === 'default_thumb') {
                    $default_thumb_index = $key;
                }
            }
        }
        if (!is_null($default_thumb_index)) {
            unset($image_fields[$default_thumb_index]);
            $image_fields = [
                [
                    'id' => 'is_top_bg_perticle',
                    'type' => 'switcher',
                    'title' => '首页及列表页顶部背景perticle效果',
                    'label' => '会影响部分客户端GPU性能,但是在有背景图的情况下效果非常好',
                    'default' => true,
                ],
                [
                    'id' => 'is_home_top_back_image',
                    'type' => 'switcher',
                    'title' => '首页顶部背景图',
                    'label' => '根据亮暗主题请设置不同的图片',
                    'default' => true,
                ],
                [
                    'id' => 'home_top_back_image_light',
                    'type' => 'upload',
                    'sanitize' => false,
                    'title' => '明亮主题下的首页顶部半高背景图',
                    'desc' => '建议在明亮主题下使用亮色背景,防止字体颜色被遮盖',
                    'default' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/spring.png',
                    'dependency' => array('is_home_top_back_image', '==', '1'),
                ],
                [
                    'id' => 'home_top_back_image_dark',
                    'type' => 'upload',
                    'sanitize' => false,
                    'title' => '暗黑主题下的首页顶部半高背景图',
                    'desc' => '建议在暗黑主题下使用暗色背景,防止字体颜色被遮盖',
                    'default' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/deer.png',
                    'dependency' => array('is_home_top_back_image', '==', '1'),
                ],
                [
                    'id' => 'is_archive_top_bg',
                    'type' => 'switcher',
                    'title' => '启用列表页顶部背景图',
                    'label' => '启用后自动选取一张顶部背景图或者在文章(包括课程等)分类(专题,标签等)选项中指定的图片作为背景',
                    'default' => true,
                ],
                [
                    'id' => 'author_top_images',
                    'type' => 'repeater',
                    'title' => esc_html__('用户主页可选图片', 'rizhuti-v2'),
                    'fields' => array(
                        array(
                            'id' => 'title',
                            'type' => 'text',
                            'title' => esc_html__('标题', 'rizhuti-v2'),
                            'default' => esc_html__('图片标题(请务必填写)', 'rizhuti-v2'),
                        ),
                        array(
                            'id' => 'image',
                            'type' => 'upload',
                            'title' => '图片',
                            'default' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/deer.png'
                        ),
                    ),
                    'default' => [
                        [
                            'title' => '梦幻麋鹿',
                            'image' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/deer.png'
                        ],
                        [
                            'title' => '末日城市',
                            'image' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/city.png'
                        ],
                        [
                            'title' => '春天来了',
                            'image' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/spring.png'
                        ],
                        [
                            'title' => '天下布武',
                            'image' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/war.png'
                        ],
                        [
                            'title' => '幽冥世界',
                            'image' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/author/underworld.png'
                        ],
                    ],
                ],
                [
                    'id' => 'default_archive_images',
                    'type' => 'repeater',
                    'title' => '页面随机顶部背景图',
                    'subtitle' => '课程模块首页,分类,标签等文章列表集合的顶部背景图,也可以在分类和标签中单独指定',
                    'accordion_title_number' => true,
                    'fields' => [
                        [
                            'id' => 'url',
                            'type' => 'upload',
                            'title' => '图片',
                            'default' => get_template_directory_uri() . '/assets/img/logo.png',
                        ],
                    ],
                    'default' => [
                        ['url' => 'https://pic.pincman.com/media/20210518114708.jpg'],
                        ['url' => 'https://pic.pincman.com/media/20210518114420.png'],
                        ['url' => 'https://pic.pincman.com/media/20210518114740.png']
                    ],
                ],
                [
                    'id' => 'default_hero_images',
                    'type' => 'repeater',
                    'title' => '视频(课程等)默认半高/全高背景图',
                    'subtitle' => '视频区块背景封面图,也可以在文章内单独指定',
                    'accordion_title_number' => true,
                    'fields' => [
                        [
                            'id' => 'url',
                            'type' => 'upload',
                            'title' => '图片',
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
                    'id' => 'default_thumb_images',
                    'type' => 'repeater',
                    'title' => '文章(课程等)随机特色图片',
                    'subtitle' => '在文章中没有指定特色图是会使用以下随机图片',
                    'accordion_title_number' => true,
                    'fields' => [
                        [
                            'id' => 'url',
                            'type' => 'upload',
                            'title' => '图片',
                            'default' => get_template_directory_uri() . '/assets/img/logo.png',
                        ],
                    ],
                    'default' => [
                        ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/1.jpg'],
                        ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/2.jpg'],
                        ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/2.jpg'],
                        ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/3.jpg'],
                        ['url' => trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/4.jpg'],
                    ],
                ],
                ...$image_fields
            ];
            array_splice(CSF::$args['sections']['_riprov2_options'], 1, 0, [array_merge(CSF::$args['sections']['_riprov2_options'][4], [
                'title' => '图片设置',
                'fields' => $image_fields,
            ])]);
            unset(CSF::$args['sections']['_riprov2_options'][5]);
            CSF::set_used_fields($image_fields);
        }
    }
}

