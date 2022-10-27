<?php if (!defined('ABSPATH')) {die;} // Cannot access directly.

//post
$prefix = 'cat_taxonomy_options';

CSF::createTaxonomyOptions($prefix, array(
    'taxonomy'  => array('post_tag', 'category'),
    'data_type' => 'unserialize',
));

CSF::createSection($prefix, array(
    'fields' => array(

        array(
            'id'      => 'bg-image',
            'type'    => 'upload',
            'title'   => esc_html__('特色图片', 'rizhuti-v2'),
            'desc'    => esc_html__('用于展示背景图，缩略图', 'rizhuti-v2'),
            'default' => get_template_directory_uri() . '/assets/img/series-bg.jpg',
        ),
        array(
            'id'          => 'archive_single_style',
            'type'        => 'select',
            'title'       => esc_html__('侧边栏', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'none'  => esc_html__('无', 'rizhuti-v2'),
                'right' => esc_html__('右侧', 'rizhuti-v2'),
                'left'  => esc_html__('左侧', 'rizhuti-v2'),
            ),
            'default'     => _cao('archive_single_style'),
        ),

        // 分类页布局
        array(
            'id'          => 'archive_item_style',
            'type'        => 'select',
            'title'       => esc_html__('分类页列表风格', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'list' => esc_html__('列表', 'rizhuti-v2'),
                'grid' => esc_html__('网格', 'rizhuti-v2'),
            ),
            'default'     => 'list',
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

    ),
));

//series taxonomy-series
$prefix = 'series_taxonomy_options';
CSF::createTaxonomyOptions($prefix, array(
    'taxonomy'  => 'series',
    'data_type' => 'unserialize',
));

/*
彩蛋功能 专题可见权限
 */
if (apply_filters('is_site_series_cap', false) && !is_close_site_shop()) {

    CSF::createSection($prefix, array(
        'fields' => array(

            array(
                'id'          => 'series_cap',
                'type'        => 'select',
                'title'       => esc_html__('专题可见权限', 'rizhuti-v2'),
                'desc'        => esc_html__('设置专题可见权限后，只有符合该会员类型权限的才可以看见，没有权限的用户全站不展示，达到全站隐藏效果', 'rizhuti-v2'),
                'options'     => array(
                    'no' => esc_html__('常规默认', 'rizhuti-v2'),
                    'vip'  => esc_html__('自适应滚动', 'rizhuti-v2'),
                ),
            ),

        ),
    ));
}

CSF::createSection($prefix, array(
    'fields' => array(

        array(
            'id'      => 'bg-image',
            'type'    => 'upload',
            'title'   => esc_html__('特色图片', 'rizhuti-v2'),
            'desc'    => esc_html__('用于展示背景图，缩略图', 'rizhuti-v2'),
            'default' => get_template_directory_uri() . '/assets/img/series-bg.jpg',
        ),
        array(
            'id'          => 'archive_item_style',
            'type'        => 'select',
            'title'       => esc_html__('专题页列表风格', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'list' => esc_html__('列表', 'rizhuti-v2'),
                'grid' => esc_html__('网格', 'rizhuti-v2'),
            ),
            'default'     => 'list',
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

    ),
));

unset($prefix);