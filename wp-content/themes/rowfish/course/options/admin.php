<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-20 00:29:04 +0800
 * @Path           : /wp-content/themes/rowfish/course/options/admin.php
 * @Description    : 课程模块主题设置选项
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
if (!function_exists('rf_set_admin_course_meta')) {
    /**
     * 后台-主题设置-课程模块
     *
     * @return void
     */
    function rf_set_admin_course_meta()
    {
        $style_fields = [
            [
                'id'          => 'sidebar_course_style',
                'type'        => 'select',
                'title'       => esc_html__('课程内页侧边栏', 'rizhuti-v2'),
                'placeholder' => '',
                'options'     => array(
                    'none'  => esc_html__('无', 'rizhuti-v2'),
                    'right' => esc_html__('右侧', 'rizhuti-v2'),
                    'left'  => esc_html__('左侧', 'rizhuti-v2'),
                ),
                'default'     => 'right',
            ],
            array(
                'id'      => 'is_course_list_author',
                'type'    => 'switcher',
                'title'   => esc_html__('课程列表中显示作者头像', 'rizhuti-v2'),
                'default' => true,
            ),
            [
                'id'      => 'is_course_list_excerpt',
                'type'    => 'switcher',
                'title'   => '课程列表中显示课程摘要',
                'default' => true,
            ],
            [
                'id'      => 'is_course_list_category',
                'type'    => 'switcher',
                'title'   => '课程列表中显示分类信息',
                'default' => true,
            ],
            [
                'id'      => 'is_course_list_date',
                'type'    => 'switcher',
                'title'   => '课程列表中显示更新时间',
                'default' => false,
            ],
            [
                'id' => 'is_course_list_status',
                'type' => 'switcher',
                'title' => '课程列表中显示课程状态',
                'default' => true,
            ],
            [
                'id' => 'is_course_list_level',
                'type' => 'switcher',
                'title' => '课程列表中显示课程难度',
                'default' => true,
            ],
            [
                'id' => 'is_course_list_views',
                'type' => 'switcher',
                'title' => '课程列表中显示观看数量',
                'default' => true,
            ],
            [
                'id' => 'is_course_list_favnum',
                'type' => 'switcher',
                'title' => '课程列表中显示收藏数量',
                'default' => true,
            ],
            [
                'id' => 'is_course_list_shop',
                'type' => 'switcher',
                'title' => '课程列表中显示课程价格/会员限制',
                'default' => true,
            ],
        ];
        $filter_fields = [
            [
                'id'      => 'is_course_archive_filter',
                'type'    => 'switcher',
                'title'   => '启用课程筛选条功能',
                'label'   => '可在课程首页和课程分类页中单独关闭',
                'default' => true,
            ],
            [
                'id'         => 'is_course_archive_filter_order',
                'type'       => 'switcher',
                'title'      => '启用排序筛选',
                'label'      => '显示排序筛选',
                'default'    => true,
                'dependency' => array('is_course_archive_filter', '==', 'true'),
            ],
            [
                'id'         => 'is_course_archive_filter_price',
                'type'       => 'switcher',
                'title'      => '启用价格筛选',
                'label'      => '显示价格筛选',
                'default'    => true,
                'dependency' => array('is_course_archive_filter', '==', 'true'),
            ],
            [
                'id' => 'is_course_simple_filter_price',
                'type' => 'switcher',
                'title' => '启用简单价格筛选',
                'label' => '开启此项的前提是确保你的资源针对所有VIP组用户均为免费使用(影响全局,强烈建议开启)',
                'default' => true,
                'dependency' => array('is_course_archive_filter_price', '==', '1'),
            ],
            [
                'id' => 'is_course_archive_filter_cat',
                'type' => 'switcher',
                'title' => '课程模块首页一级分类筛选',
                'label' => '',
                'default' => true,
                'dependency' => array('is_course_archive_filter', '==', 'true'),
            ],
            [
                'id' => 'course_archive_filter_cat_1',
                'type' => 'select',
                'title' => '课程主分类筛选设置',
                'desc' => '排序规则以设置的顺序为准',
                'placeholder' => '选择分类',
                'inline' => true,
                'chosen' => true,
                'multiple' => true,
                'options' => 'categories',
                'query_args' => [
                    'taxonomy' => 'course_category',
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'hide_empty' => false,
                ],
                'dependency' => [array('is_course_archive_filter_cat', '==', 'true'), array('is_course_archive_filter', '==', 'true')],
            ],
            [
                'id' => 'is_course_status_filter',
                'type' => 'switcher',
                'title' => '启用课程状态筛选',
                'label' => '必须先在"全局筛选"中启用"业内筛选条"',
                'default' => true,
                'dependency' => array('is_course_archive_filter', '==', 'true'),
            ],

            [
                'id' => 'is_course_level_filter',
                'type' => 'switcher',
                'title' => '启用课程难度等级筛选',
                'label' => '必须先在"全局筛选"中启用"业内筛选条"',
                'default' => true,
                'dependency' => array('is_course_archive_filter', '==', 'true'),
            ],
        ];
        $base_fields = [
            [
                'id' => 'free_onlogin_play',
                'type' => 'switcher',
                'title' => '免费课程免登陆观看,下载资源',
                'label' => '此项只用于课程类型的文章,其它类型的文章根随下面的"免登录购买"设置',
                'default' => true,
            ],
            [
                'id' => 'course_status',
                'type' => 'repeater',
                'title' => '课程状态',
                'subtitle' => '请务必填写唯一标识符,否则等级无法被识别',
                'fields' => [
                    [
                        'id' => 'slug',
                        'type' => 'text',
                        'title' => '唯一标识符*(必须为英文或数字)',
                    ],
                    [
                        'id' => 'name',
                        'type' => 'text',
                        'title' => '等级名称',
                    ],
                    [
                        'id' => 'color',
                        'type' => 'color',
                        'title' => '标识颜色',
                    ],
                ],
                'default' => [
                    ['slug' => 'preparing', 'name' => '策划中', 'color' => '#6c757d'],
                    ['slug' => 'waiting', 'name' => '待发布', 'color' => '#10c469'],
                    ['slug' => 'updating', 'name' => '更新中', 'color' => '#ff5b5b'],
                    ['slug' => 'complete', 'name' => '已完结', 'color' => '#536de6']
                ],
            ],
            [
                'id' => 'course_levels',
                'type' => 'repeater',
                'title' => '课程等级',
                'subtitle' => '请务必填写唯一标识符,否则等级无法被识别',
                'fields' => [
                    [
                        'id' => 'slug',
                        'type' => 'text',
                        'title' => '唯一标识符*(必须为英文或数字)',
                    ],
                    [
                        'id' => 'name',
                        'type' => 'text',
                        'title' => '等级名称',
                    ],
                    [
                        'id' => 'icon',
                        'type' => 'icon',
                        'title' => '等级图标'
                    ],
                    [
                        'id' => 'color',
                        'type' => 'select',
                        'title' => '图标背景颜色',
                        'placeholder' => '选择颜色',
                        'inline' => true,
                        'options' => [
                            'primary' => '蓝', 'secondary' => '灰', 'success' => '绿', 'danger' => '红', 'warning' => '黄', 'info' => '青', 'light' => '亮', 'dark' => '黑'
                        ],
                    ],
                ],
                'default' => [
                    ['slug' => 'simple', 'name' => '入门', 'icon' => 'fas fa-feather-alt', 'color' => 'warning'],
                    ['slug' => 'advanced', 'name' => '进阶', 'icon' => 'fab fa-gripfire', 'color' => 'danger'],
                    ['slug' => 'master', 'name' => '大师', 'icon' => 'fab fa-envira', 'color' => 'success']
                ],
            ],
            [
                'id' => 'is_course_template_help',
                'type' => 'switcher',
                'title' => '课程内页是否显示常见问题',
                'label' => '为了与文章内页不冲突,课程页面显示的常见问题需要这里单独设置',
                'default' => true,
            ],
            [
                'id'         => 'course_template_help',
                'type'       => 'repeater',
                'title'      => esc_html__('文章内页常见问题配置', 'rizhuti-v2'),
                'fields'     => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'title'   => esc_html__('标题', 'rizhuti-v2'),
                        'default' => esc_html__('问题标题', 'rizhuti-v2'),
                    ),
                    array(
                        'id'       => 'desc',
                        'type'     => 'textarea',
                        'title'    => esc_html__('描述内容', 'rizhuti-v2'),
                        'sanitize' => false,
                        'default'  => esc_html__('这里是问题描述内容', 'rizhuti-v2'),
                    ),
                ),
                'default'   => [
                    ['title' => '为什么本站部分教程需要付费或者订阅才能使用?', 'desc' => '因为视频教程的制作耗费站长大量的业余时间,有着巨大的工作量.每一集教程,需要编写代码,编写文档,录制视频,剪辑视频等多个流程,并且还提供了问答服务,如果全部免费的话本站将很难持续发展下去为大家提供更优质的内容.所以不得不收取一定费用,望大家谅解.'],
                    ['title' => '成为订阅者将获得哪些权限以及享受哪些服务?', 'desc' => '订阅本站后可以使用本站的一切服务,包括视频教程的学习,下载,问答以及本站发布的其它任何资源都可以随意使用.本站保证不再对订阅者收取二次费用.需要注意的是,后续推送的站长直播服务只有终身订阅者才可以享受.'],
                    ['title' => '本站主要提供哪些内容和服务?', 'desc' => '本站目前提供各类编程开发相关的技术视频教程以及针对这些视频教程的问答服务和这些技术周边生态的导航,文档的翻译,开源项目的推荐,技巧性文章的发布等等.并且也为订阅者提供QQ群和discord问答服务.对于视频教程中的代码,站长专门搭建了一个代码托管平台方便大家下载.'],
                    ['title' => '本站的所涉及的技术栈包含哪些方面?', 'desc' => '本站在编程语言方面专注于Javascript/Typescript,Golang,PHP等几种站长擅长以及工作中常用的语言.技术栈涉及React,Vue等前端生态以及,Node.js,各种golang和php技术等后端技能,同时也会涉及一些包括Linux,Devops,Docker,K8S等在内与编程相关的技术,甚至还会讲解一些硬件方面的东西,总之用"杂七杂八"形容最为贴切.'],
                    ['title' => '本站支付后，可以退款吗?', 'desc' => '本站绝大多数收费属于原创的虚拟商品，具有可复制性，可传播性，一旦授予，不接受任何形式的退款、换货要求。请您在购买获取或订阅之前确认好 ,避免引起不必要的纠纷'],
                ],
                'dependency' => array('is_course_template_help', '==', 'true'),
            ],
        ];
        $course_fields_index = null;
        foreach (CSF::$args['sections']['_riprov2_options'] as $key => $value) {
            if (isset($value['parent']) && $value['parent'] === 'course_fields') {
                $course_fields_index = $key;
                break;
            }
        }
        if (is_null($course_fields_index)) {
            array_splice(CSF::$args['sections']['_riprov2_options'], 3, 0, [[
                'id'    => 'course_fields',
                'title' => '课程模块',
                'icon'  => 'fa fa-plus-circle',
            ], [
                'parent' => 'course_fields',
                'title'  => '基本设置',
                'icon'   => 'fa fa-circle',
                'fields' => $base_fields
            ], [
                'parent' => 'course_fields',
                'title'  => '布局风格',
                'icon'   => 'fa fa-circle',
                'fields' => $style_fields
            ], [
                'parent' => 'course_fields',
                'title' => '课程筛选',
                'icon'   => 'fa fa-circle',
                'fields' =>  $filter_fields
            ]]);
        }
    }
}
