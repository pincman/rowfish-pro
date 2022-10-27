<?php
function get_current_post_for_box()
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
 * 文章设置选项字段
 */
function get_post_options()
{
    return
        ['hook' => '_prefix_pm_options', 'params' => [
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
        ]];
}
