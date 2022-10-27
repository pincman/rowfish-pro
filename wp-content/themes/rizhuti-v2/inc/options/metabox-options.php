<?php if (!defined('ABSPATH')) {die;} // Cannot access directly.

//布局meta
$prefix_meta_opts = '_prefix_meta_options';
CSF::createMetabox($prefix_meta_opts, array(
    'title'     => '布局风格',
    'post_type' => array('post', 'page'),
    'context'   => 'side',
    'data_type' => 'unserialize',
));

CSF::createSection($prefix_meta_opts, array(
    'fields' => array(

        array(
            'id'          => 'hero_single_style',
            'type'        => 'radio',
            'title'       => esc_html__('文章内页顶部风格', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'none' => esc_html__('默认常规', 'rizhuti-v2'),
                'wide' => esc_html__('顶部半高背景', 'rizhuti-v2'),
                'full' => esc_html__('顶部全屏背景', 'rizhuti-v2'),
            ),
            'default'     => _cao('hero_single_style'),
        ),
        array(
            'id'          => 'sidebar_single_style',
            'type'        => 'radio',
            'title'       => esc_html__('侧边栏', 'rizhuti-v2'),
            'placeholder' => '',
            'inline'      => true,
            'options'     => array(
                'right' => esc_html__('右侧', 'rizhuti-v2'),
                'none'  => esc_html__('无', 'rizhuti-v2'),
                'left'  => esc_html__('左侧', 'rizhuti-v2'),
            ),
            'default'     => _cao('sidebar_single_style'),
        ),

    ),
));

if (!is_close_site_shop()):
// 付费meta
$prefix_meta_opts = '_prefix_wppay_options';
CSF::createMetabox($prefix_meta_opts, array(
    'title'     => esc_html__('RiZhuti-V2文章付费资源设置', 'rizhuti-v2'),
    'post_type' => 'post',
    'data_type' => 'unserialize',
    'priority'  => 'high',
));

CSF::createSection($prefix_meta_opts, array(
    'fields' => array(

        array(
            'id'      => 'wppay_type',
            'type'    => 'select',
            'title'   => esc_html__('资源类型', 'rizhuti-v2'),
            'inline'  => true,
            'options' => array(
                '0' => esc_html__('不启用', 'rizhuti-v2'),
                '3' => esc_html__('付费下载资源', 'rizhuti-v2'),
                '4' => esc_html__('免费下载资源', 'rizhuti-v2'),
                '2' => esc_html__('付费隐藏内容', 'rizhuti-v2'),
                '1' => esc_html__('付费查看全文', 'rizhuti-v2'),
                '5' => esc_html__('付费观看视频', 'rizhuti-v2'),
                '6' => esc_html__('付费图片相册', 'rizhuti-v2'),
            ),
            'inline'  => true,
            'default' => _cao('wppay_type',0),
        ),

        array(
            'id'         => 'wppay_price',
            'type'       => 'text',
            'title'      => esc_html__('收费价格', 'rizhuti-v2'),
            'desc'       => esc_html__('单位RMB,价格为0时，如果启用VIP会员权限，则普通用户不能购买。只允许会员下载，反之普通用户可以购买', 'rizhuti-v2'),
            'default'    => _cao('wppay_price','0.1'),
            'validate'   => 'csf_validate_numeric',
            'dependency' => array('wppay_type', '!=', '0'),
        ),

        array(
            'id'         => 'wppay_vip_auth',
            'type'       => 'select',
            'title'      => esc_html__('VIP会员权限', 'rizhuti-v2'),
            'subtitle'   => esc_html__('权限关系是包含关系，终身可查看年月', 'rizhuti-v2'),
            'inline'     => true,
            'options'    => array(
                '0' => esc_html__('不启用', 'rizhuti-v2'),
                '1' => esc_html__('包月VIP免费', 'rizhuti-v2'),
                '2' => esc_html__('包年VIP免费', 'rizhuti-v2'),
                '3' => esc_html__('终身VIP免费', 'rizhuti-v2'),
            ),
            'default'    => _cao('wppay_vip_auth','0'),
            'dependency' => array('wppay_type', '!=', '0'),
        ),
        // 下载地址 新
        array(
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
                    'default'  => '#',
                ),
                array(
                    'id'    => 'pwd',
                    'type'  => 'text',
                    'title' => esc_html__('下载密码', 'rizhuti-v2'),
                ),
            ),
            'default' => _cao('wppay_down'),
            'dependency'             => array('wppay_type', 'any', '3,4'),
        ),
        array(
            'id'         => 'wppay_demourl',
            'type'       => 'text',
            'title'      => esc_html__('演示地址', 'rizhuti-v2'),
            'subtitle'   => esc_html__('为空则不显示', 'rizhuti-v2'),
            'sanitize'   => false,
            'dependency' => array('wppay_type', 'any', '3,4'),
        ),
        array(
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
            'default' => _cao('wppay_info'),
            'dependency' => array('wppay_type', 'any', '3,4'),
        ),

        //付费隐藏内
        array(
            'type'       => 'content',
            'content'    => '<b style="color: red;">在文章内容中插入短代码：</b> [rihide] 这里面填写要隐藏的内容 [/rihide] ',
            'dependency' => array('wppay_type', '==', '2'),
        ),
        array(
            'type'       => 'content',
            'content'    => '<b style="color: red;">在文章内容开头和结尾插入短代码：</b> [rihide] 这里面是文章内容 [/rihide] ',
            'dependency' => array('wppay_type', '==', '1'),
        ),
        //视频地址
        array(
            'title'      => esc_html__('视频地址', 'rizhuti-v2'),
            'id'         => "hero_video_data",
            'type'       => 'textarea',
            'desc'       => '（需要文章 形式 设置为视频格式才显示模块,布局风格设置为背景才显示模块）输入视频地址，支持mp4/m3u8常见格式，不支持平台解析',
            'sanitize'   => false,
            'dependency' => array('wppay_type', '==', '5'),
        ),

        // 插入图片相册
        array(
            'title'      => esc_html__('插入图片相册', 'rizhuti-v2'),
            'id'         => "hero_gallery_data",
            'type'       => 'gallery',
            'desc'       => '（需要文章 形式 设置为相册格式,布局风格设置为背景才显示模块）',
            'sanitize'   => false,
            'dependency' => array('wppay_type', '==', '6'),
        ),
        array(
            'title'      => esc_html__('前几张免费查看？', 'rizhuti-v2'),
            'id'         => "hero_gallery_data_free_num",
            'type'       => 'text',
            'desc'       => '0为不设置，如果设置2则表示前两张免费查看，其余部分需要付费',
            'default'    => '0',
            'dependency' => array('wppay_type', '==', '6'),
        ),
    ),
));

endif;

// 自定义SEO TDK
if (_cao('is_rizhuti_v2_seo', '0')):

$prefix_meta_opts = '_prefix_seo_options';
CSF::createMetabox($prefix_meta_opts, array(
    'title'     => esc_html__('自定义文章SEO信息', 'rizhuti-v2'),
    'post_type' => array('post', 'page'),
    'data_type' => 'unserialize',
));
CSF::createSection($prefix_meta_opts, array(
    'fields' => array(
        array(
            'id'       => 'custom_title',
            'type'     => 'text',
            'title'    => esc_html__('自定义SEO标题', 'rizhuti-v2'),
            'subtitle' => esc_html__('为空则不设置', 'rizhuti-v2'),
        ),
        array(
            'id'       => 'keywords',
            'type'     => 'text',
            'title'    => esc_html__('自定义SEO关键词', 'rizhuti-v2'),
            'subtitle' => esc_html__('关键词用英文逗号,隔开', 'rizhuti-v2'),
        ),
        array(
            'id'       => 'description',
            'type'     => 'textarea',
            'title'    => esc_html__('自定义SEO描述', 'rizhuti-v2'),
            'subtitle' => esc_html__('字数控制到80-180最佳', 'rizhuti-v2'),
        ),

    ),
));

endif;