<?php if (!defined('ABSPATH')) {
    die;
} // Cannot access directly.

$prefix = _OPTIONS_PRE;
$assets_dir =  get_template_directory_uri() . '/assets/img';


CSF::createOptions($prefix, array(
    'menu_title' => 'rizhuti-v2设置',
    'menu_slug'  => 'rizhuti-v2',
));


CSF::createSection($prefix, array(
    'title'  => '基本设置',
    'icon'   => 'fas fa-rocket',
    'fields' => array(


        // array(
        //     'id'      => 'is_site_assets_cdn',
        //     'type'    => 'switcher',
        //     'title'   => esc_html__('使用CDN加速JS', 'rizhuti-v2'),
        //     'desc'   => esc_html__('加速优化，使用jsdelivr的CDN服务加速jquery和bootstrap库', 'rizhuti-v2'),
        //     'default' => false,
        // ),

        array(
            'id'      => 'disable_gutenberg_edit',
            'type'    => 'switcher',
            'title'   => esc_html__('古滕堡编辑器', 'rizhuti-v2'),
            'default' => false,
        ),

        array(
            'id'      => 'is_search_title_only',
            'type'    => 'switcher',
            'title'   => esc_html__('站内搜索只搜索标题', 'rizhuti-v2'),
            'desc'   => esc_html__('优化用户搜索关键词时只通过文章标题进行搜索查询，如果您的网站文章数量很多，搜索很卡，可以开启此功能提升百分之70的搜索性能，但搜索精准度只搜索文章标题是否有关键词，不对文章内容进行搜索，具体效果以网站实际搜索效果为准', 'rizhuti-v2'),
            'default' => false,
        ),



        array(
            'id'      => 'is_site_comments',
            'type'    => 'switcher',
            'title'   => esc_html__('网站评论功能', 'rizhuti-v2'),
            'desc'   => esc_html__('开启后显示评论功能', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'      => 'is_site_question',
            'type'    => 'switcher',
            'title'   => esc_html__('问答社区', 'rizhuti-v2'),
            'desc'   => esc_html__('开启后支持问答', 'rizhuti-v2'),
            'default' => false,
        ),

        array(
            'id'      => 'is_site_tickets',
            'type'    => 'switcher',
            'title'   => esc_html__('工单系统', 'rizhuti-v2'),
            'desc'   => esc_html__('启用后用户可以在个人中心提交工单，站长可以后台回复管理工单', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'      => 'is_site_tougao',
            'type'    => 'switcher',
            'title'   => esc_html__('投稿功能', 'rizhuti-v2'),
            'desc'   => esc_html__('用户在个人中心可以快速简单投稿，一定程度规避版权风险', 'rizhuti-v2'),
            'default' => false,
        ),
        array(
            'id'      => 'is_site_tougao_wp',
            'type'    => 'switcher',
            'title'   => esc_html__('使用WordPress后端投稿', 'rizhuti-v2'),
            'desc'   => esc_html__('使用WordPress后端自带文章页面投稿，功能完备，注意，请在wordpress后台设置-常规中，将新用户默认角色-设置为-贡献者，则新注册用户均可以使用后端投稿', 'rizhuti-v2'),
            'default' => false,
            'dependency' => array('is_site_tougao', '==', 'true'),
        ),

        array(
            'id'      => 'md5_file_udpate',
            'type'    => 'switcher',
            'title'   => esc_html__('上传文件MD5加密重命名', 'rizhuti-v2'),
            'desc'   => esc_html__('建议开启，可以有效解决中文字符无法上传图片问题，防止付费图片被抓包等', 'rizhuti-v2'),
            'default' => false,
        ),

        array(
            'id'      => 'is_rizhuti_v2_seo',
            'type'    => 'switcher',
            'title'   => '主题内置的SEO功能',
            'label'   => '有部分用户在用插件做SEO，可以在此关闭主题自带SEO功能',
            'default' => true,
        ),

        array(
            'id'         => 'site_seo',
            'type'       => 'fieldset',
            'title'      => '内置SEO设置',
            'fields'     => array(
                array(
                    'id'      => 'separator',
                    'type'    => 'text',
                    'title'   => '全站链接符',
                    'desc'    => '一经选择，切勿中途更改，对SEO不友好，一般为“-”或“_”',
                    'default' => '-',
                ),
                array(
                    'id'         => 'keywords',
                    'type'       => 'text',
                    'title'      => '网站关键词',
                    'desc'       => '3-5个关键词，用英文逗号隔开',
                    'attributes' => array(
                        'style' => 'width: 100%;',
                    ),
                    'default'    => '日主题,日主题V2,Rizhuti,RizhutiV2,Riplus,rizhuti.com,rizhuti,Ritheme',
                ),
                array(
                    'id'      => 'description',
                    'type'    => 'textarea',
                    'sanitize' => false,
                    'title'   => '网站描述',
                    'default' => '日主题v2是一款全新架构的Wordpress主题，兼容老款日主题，支持会员商城 ，前后台界面均支持html5响应式布局，夜间模式一键切换。喜欢你就日一下。',
                ),

            ),
            'dependency' => array('is_rizhuti_v2_seo', '==', 'true'),
        ),


    ),
));

//
// Create a section
//
CSF::createSection($prefix, array(
    'title'  => esc_html__('顶部设置', 'rizhuti-v2'),
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'    => 'site_logo',
            'type'  => 'upload',
            'title' => esc_html__('网站LOGO', 'rizhuti-v2'),
            'default' => get_template_directory_uri() . '/assets/img/logo.png',
        ),

        array(
            'id'      => 'site_favicon',
            'type'    => 'upload',
            'title'   => 'favicon',
            'default' => get_template_directory_uri() . '/assets/img/favicon.png',
        ),

        array(
            'id'          => 'navbar_style',
            'type'        => 'select',
            'title'       => esc_html__('顶部菜单风格', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'regular'            => esc_html__('常规默认', 'rizhuti-v2'),
                'sticky'             => esc_html__('自适应滚动', 'rizhuti-v2'),
            ),
            'default'     => 'sticky',
        ),

        array(
            'id'      => 'navbar_disable_search',
            'type'    => 'switcher',
            'title'   => esc_html__('顶部是否显示搜索按钮', 'rizhuti-v2'),
            'default' => true,
        ),

    ),
));

//
// Create a section
//
CSF::createSection($prefix, array(
    'title'  => esc_html__('布局风格', 'rizhuti-v2'),
    'icon'   => 'fas fa-rocket',
    'fields' => array(

        array(
            'id'      => 'is_site_wide_screen',
            'type'    => 'switcher',
            'title'   => esc_html__('全站宽屏模式', 'rizhuti-v2'),
            'desc' => '宽屏模式仅你设备实际尺寸超过1200px才有效，宽屏模式内容宽度为1440PX',
            'default' => false,
        ),

        array(
            'id'      => 'is_compare_options_to_global',
            'type'    => 'switcher',
            'title'   => esc_html__('强制所有内页，文章页布局风格全局统一', 'rizhuti-v2'),
            'default' => false,
        ),

        array(
            'id'          => 'hero_single_style',
            'type'        => 'select',
            'title'       => esc_html__('文章内页顶部风格', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'none' => esc_html__('默认常规', 'rizhuti-v2'),
                'wide' => esc_html__('顶部半高背景', 'rizhuti-v2'),
                'full' => esc_html__('顶部全屏背景', 'rizhuti-v2'),
            ),
            'default'     => 'none',
        ),

        array(
            'id'          => 'sidebar_single_style',
            'type'        => 'select',
            'title'       => esc_html__('文章内页侧边栏', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'none'  => esc_html__('无', 'rizhuti-v2'),
                'right' => esc_html__('右侧', 'rizhuti-v2'),
                'left'  => esc_html__('左侧', 'rizhuti-v2'),
            ),
            'default'     => 'right',
        ),

        //////////////
        array(
            'id'          => 'archive_single_style',
            'type'        => 'select',
            'title'       => '分类页侧边栏',
            'placeholder' => '',
            'options'     => array(
                'none'  => esc_html__('无', 'rizhuti-v2'),
                'right' => esc_html__('右侧', 'rizhuti-v2'),
                'left'  => esc_html__('左侧', 'rizhuti-v2'),
            ),
            'default'     => 'none',
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
            'id'      => 'is_post_thumb_has_box',
            'type'    => 'switcher',
            'title'   => esc_html__('无缩略图不显示', 'rizhuti-v2'),
            'default' => false,
        ),

        //列表设置
        array(
            'id'      => 'is_post_list_date_diff',
            'type'    => 'switcher',
            'title'   => esc_html__('文章时间显示格式为XX前', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'      => 'is_post_list_author',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示作者和头像', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_list_category',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示分类信息', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_list_date',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示发布时间', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'      => 'is_post_list_comment',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示评论信息', 'rizhuti-v2'),
            'default' => false,
        ),

        array(
            'id'      => 'is_post_list_favnum',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示收藏数量', 'rizhuti-v2'),
            'default' => false,
        ),
        array(
            'id'      => 'is_post_list_views',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示阅读数量', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'      => 'is_post_list_shop',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示资源类型图标', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_list_price',
            'type'    => 'switcher',
            'title'   => esc_html__('列表-显示资源价格角标', 'rizhuti-v2'),
            'default' => true,
        ),

        ////////////////////////////////////////
        array(
            'id'      => 'is_post_grid_excerpt',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示文章摘要', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_grid_author',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示作者和头像', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_grid_category',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示分类信息', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_grid_date',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示发布时间', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'      => 'is_post_grid_comment',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示评论信息', 'rizhuti-v2'),
            'default' => false,
        ),

        array(
            'id'      => 'is_post_grid_favnum',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示收藏数量', 'rizhuti-v2'),
            'default' => false,
        ),
        array(
            'id'      => 'is_post_grid_views',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示阅读数量', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_grid_shop',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示资源类型图标', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_post_grid_price',
            'type'    => 'switcher',
            'title'   => esc_html__('网格-显示资源价格角标', 'rizhuti-v2'),
            'default' => true,
        ),




        // 分页布局
        array(
            'id'          => 'site_pagination',
            'type'        => 'select',
            'title'       => '分页风格',
            'placeholder' => '',
            'options'     => array(
                'navigation'      => esc_html__('上下页', 'rizhuti-v2'),
                'numeric'         => esc_html__('数字', 'rizhuti-v2'),
                'infinite_button' => esc_html__('按钮加载下一页', 'rizhuti-v2'),
                'infinite_scroll' => esc_html__('下拉自动翻页', 'rizhuti-v2'),
            ),
            'default'     => 'numeric',
        ),




    ),
));

CSF::createSection($prefix, array(
    'title'  => '文章内页',
    'icon'   => 'fa fa-circle',
    'fields' => array(

        array(
            'id'      => 'is_single_shop_template',
            'type'    => 'switcher',
            'title'   => '资源类文章启用新布局',
            'desc'   => '开启后，如果文章是下载类或者付费类型文章，自动切换新布局模式，新布局可以设置场景问题，TAB切换按钮，直观方便，建议开启',
            'default' => false,
        ),

        array(
            'id'      => 'is_single_shop_template_img',
            'type'    => 'switcher',
            'title'   => '资源类新布局顶部图片',
            'desc'   => '开启后，文章顶部显示特色图片大图',
            'default' => true,
            'dependency' => array('is_single_shop_template', '==', 'true'),
        ),

        array(
            'id'         => 'single_shop_template_help',
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
                ['title' => '免费下载或者VIP会员资源能否直接商用？', 'desc' => '本站所有资源版权均属于原作者所有，这里所提供资源均只能用于参考学习用，请勿直接商用。若由于商用引起版权纠纷，一切责任均由使用者承担。更多说明请参考 VIP介绍。'],
                ['title' => '提示下载完但解压或打开不了？', 'desc' => '最常见的情况是下载不完整: 可对比下载完压缩包的与网盘上的容量，若小于网盘提示的容量则是这个原因。这是浏览器下载的bug，建议用百度网盘软件或迅雷下载。 若排除这种情况，可在对应资源底部留言，或联络我们。'],
                ['title' => '找不到素材资源介绍文章里的示例图片？', 'desc' => '对于会员专享、整站源码、程序插件、网站模板、网页模版等类型的素材，文章内用于介绍的图片通常并不包含在对应可供下载素材包内。这些相关商业图片需另外购买，且本站不负责(也没有办法)找到出处。 同样地一些字体文件也是这种情况，但部分素材会在素材包内有一份字体下载链接清单。'],
                ['title' => '付款后无法显示下载地址或者无法查看内容？', 'desc' => '如果您已经成功付款但是网站没有弹出成功提示，请联系站长提供付款信息为您处理'],
                ['title' => '购买该资源后，可以退款吗？', 'desc' => '源码素材属于虚拟商品，具有可复制性，可传播性，一旦授予，不接受任何形式的退款、换货要求。请您在购买获取之前确认好 是您所需要的资源'],
            ],
            'dependency' => array('is_single_shop_template', '==', 'true'),
        ),





        array(
            'id'      => 'is_single_meta_favnum',
            'type'    => 'switcher',
            'title'   => '文章内容页顶部显示收藏数量',
            'default' => true,
        ),
        array(
            'id'      => 'is_single_meta_views',
            'type'    => 'switcher',
            'title'   => '文章内容页顶部显示阅读数量',
            'default' => true,
        ),

        array(
            'id'      => 'is_single_breadcrumb',
            'type'    => 'switcher',
            'title'   => '文章内容页显示面包屑导航',
            'default' => true,
        ),

        array(
            'id'      => 'single_copyright',
            'type'    => 'textarea',
            'title'   => '文章底部版权内容',
            'sanitize' => false,
            'desc'    => '不填写则不显示，如果是html标签，请注意 / 结尾标签，例如：' . esc_html('<p>内容</p>') . '，必须闭合，否则界面错乱',
            'default' => '<small><strong>声明：</strong>本站所有文章，如无特殊说明或标注，均为本站原创发布。任何个人或组织，在未征得本站同意时，禁止复制、盗用、采集、发布本站内容到任何网站、书籍等各类媒体平台。如若本站内容侵犯了原著者的合法权益，可联系我们进行处理。</small>',
        ),

        array(
            'id'      => 'is_single_tags',
            'type'    => 'switcher',
            'title'   => '文章底部显示本文标签',
            'default' => true,
        ),

        array(
            'id'      => 'is_single_share',
            'type'    => 'switcher',
            'title'   => '文章底部显示分享组件',
            'default' => true,
        ),

        array(
            'id'      => 'is_single_entry_page',
            'type'    => 'switcher',
            'title'   => '是否在文章底部显示上一篇下一篇导航',
            'default' => true,
        ),

        // 相关列表风格
        array(
            'id'          => 'related_posts_item_style',
            'type'        => 'select',
            'title'       => esc_html__('相关列表风格', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'list' => esc_html__('列表', 'rizhuti-v2'),
                'grid' => esc_html__('网格', 'rizhuti-v2'),
                'none' => esc_html__('不显示', 'rizhuti-v2'),
            ),
            'default'     => 'list',
        ),

        array(
            'id'         => 'single_related_posts_num',
            'type'       => 'text',
            'title'      => '相关文章显示数量',
            'dsec'       => '',
            'default'    => '4',
            'dependency' => array('related_posts_item_style', '==', 'list'),
        ),
        array(
            'id'      => 'is_single_share_poser',
            'type'    => 'switcher',
            'title'   => '文章底部显示生成海报组件',
            'default' => true,
            'desc' => '如果已经登录用户，则海报二维码中会自动携带用户的推广链接',
        ),

        array(
            'id'         => 'single_share_poser_logo',
            'type'       => 'upload',
            'title'      => '海报底部LOGO（尺寸220*58）',
            'dsec'       => '',
            'default'    => get_template_directory_uri() . '/assets/img/logo.png',
            'dependency' => array('is_single_share_poser', '==', 'true'),
        ),
        array(
            'id'         => 'single_share_poser_desc',
            'type'       => 'text',
            'title'      => '海报LOGO底部描述，最多38个字符',
            'dsec'       => '',
            'default'    => '扫码识别右侧二维码阅读全文',
            'dependency' => array('is_single_share_poser', '==', 'true'),
        ),

    ),
));



//
// Create a section
//
CSF::createSection($prefix, array(
    'title'  => esc_html__('缩略图设置', 'rizhuti-v2'),
    'icon'   => 'fas fa-rocket',
    'fields' => array(

        array(
            'id'        => 'default_thumb',
            'type'      => 'upload',
            'title'     => '文章默认缩略图',
            'add_title' => '上传图片',
            'desc'      => '设置文章默认缩略图（建议和自定义文章缩略图宽高保持一致）',
            'default'   => get_template_directory_uri() . '/assets/img/thumb.jpg',
        ),

        // array(
        //     'id'      => 'is_post_one_thumbnail',
        //     'type'    => 'switcher',
        //     'title'   => esc_html__('自动抓取第一张图片', 'rizhuti-v2'),
        //     'desc'   => esc_html__('没设置特色图自动获取文章中第一张图片作为特色图', 'rizhuti-v2'),
        //     'default' => true,
        // ),


        array(
            'id'      => 'post_thumbnail_size',
            'type'    => 'dimensions',
            'title'   => esc_html__('自定义文章缩略图尺寸', 'rizhuti-v2'),
            'desc'    => esc_html__('宽度，高度，设置文章缩略图尺寸，特色图像', 'rizhuti-v2'),
            'units'   => array('px'),
            'default' => array(
                'width'  => '300',
                'height' => '200',
                'unit'   => 'px',
            ),
        ),
        array(
            'id'      => 'post_thumbnail_crop',
            'type'    => 'switcher',
            'title'   => esc_html__('总是裁剪缩略图到这个尺寸', 'rizhuti-v2'),
            'label'   => '',
            'default' => true,
        ),

        array(
            'id'      => 'post_medium_size',
            'type'    => 'dimensions',
            'title'   => esc_html__('自定义中等大小尺寸', 'rizhuti-v2'),
            'desc'    => esc_html__('宽度，高度，一般关闭 设置为0 ，可以减少wordpres自带缩略图裁剪多个无用的图片', 'rizhuti-v2'),
            'units'   => array('px'),
            'default' => array(
                'width'  => '0',
                'height' => '0',
                'unit'   => 'px',
            ),
        ),
        array(
            'id'      => 'post_large_size',
            'type'    => 'dimensions',
            'title'   => esc_html__('自定义大尺寸', 'rizhuti-v2'),
            'desc'    => esc_html__('宽度，高度，一般关闭 设置为0 ，可以减少wordpres自带缩略图裁剪多个无用的图片', 'rizhuti-v2'),
            'units'   => array('px'),
            'default' => array(
                'width'  => '0',
                'height' => '0',
                'unit'   => 'px',
            ),
        ),


        array(
            'id'      => 'is_img_cloud_storage',
            'type'    => 'switcher',
            'title'   => esc_html__('适配云储存裁剪尺寸', 'rizhuti-v2'),
            'desc'    => esc_html__('如果您网站使用了OSS等云储存插件，可以在此设置缩略图自定义裁剪参数，达到完美展示缩略图和节约百分之90的带宽速度', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'         => 'img_cloud_storage_domain',
            'type'       => 'text',
            'title'      => '云储存域名',
            'desc'       => '这里举例是阿里云OSS的地址,COS，qiniu等',
            'default'    => 'https://xxxxx.oss-cn-qingdao.aliyuncs.com/',
            'dependency' => array('is_img_cloud_storage', '==', 'true'),
        ),

        array(
            'id'         => 'img_cloud_storage_param',
            'type'       => 'text',
            'title'      => '云储存裁剪缩略图参数地址',
            'desc'       => '这里举例是阿里云OSS的裁剪参数方法，裁剪为高200px，宽300px的缩略图',
            'default'    => '?x-oss-process=image/resize,m_fill,h_200,w_300',
            'dependency' => array('is_img_cloud_storage', '==', 'true'),
        ),

        array(
            'type'    => 'content',
            'content' => '该功能只是适配缩略图裁剪，不是插件，只对网站已经做了云储存的用户有效果，一般用于第三方储存裁剪，例如OSS在图片地址后加（?x-oss-process=image/resize,m_fill,h_180,w_300）,h_高度，w_宽度 ,<br>原理说明，例如本身图片地址是：https://本站域名.com/wp-content/uploads/2019/08/a.jpg，使用插件接入oss后是https://oss域名.com/wp-content/uploads/2019/08/a.jpg，加入自定义参数后是，https://oss域名.com/wp-content/uploads/2019/08/a.jpg?x-oss-process=image/resize,m_fill,h_180,w_300，这样就实现oss裁剪 ',
            'dependency' => array('is_img_cloud_storage', '==', 'true'),
        ),





    ),
));




CSF::createSection($prefix, array(
    'title'  => '分类筛选',
    'icon'   => 'fa fa-circle',
    'fields' => array(
        array(
            'id'      => 'is_archive_top_bg',
            'type'    => 'switcher',
            'title'   => '内页筛选顶部启用背景图像模块',
            'label'   => '启用后自动获取分类下第一个文章的缩略图作为背景',
            'default' => true,
        ),
        array(
            'id'         => 'is_archive_top_bg_one',
            'type'       => 'switcher',
            'title'      => '是否所有分类等内页顶部背景图固定一张',
            'default'    => false,
            'dependency' => array('is_archive_top_bg', '==', 'true'),
        ),
        array(
            'id'         => 'archive_top_bg_one_img',
            'type'       => 'upload',
            'title'      => '分类等内页顶部背景图片',
            'dsec'       => '如果没有文章图片或者其他页面没有图片则用此图片显示美化',
            'default'    => get_template_directory_uri() . '/assets/img/top-bg.jpg',
            'dependency' => array('is_archive_top_bg_one', '==', 'true'),
        ),

        array(
            'id'      => 'is_archive_filter',
            'type'    => 'switcher',
            'title'   => '启用内页筛选条功能',
            'label'   => '',
            'default' => true,
        ),
        array(
            'id'         => 'is_archive_filter_cat',
            'type'       => 'switcher',
            'title'      => '启用一级主分类筛选',
            'label'      => '当前分类页面主分类筛选',
            'default'    => true,
            'dependency' => array('is_archive_filter', '==', 'true'),
        ),
        array(
            'id'          => 'archive_filter_cat_1',
            'type'        => 'select',
            'title'       => '一级主分类筛选设置',
            'desc'        => '排序规则以设置的顺序为准',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'orderby' => 'count',
                'order'   => 'DESC',
                'parent'  => 0,
            ),
            'dependency' => array('is_archive_filter_cat', '==', 'true'),
        ),

        array(
            'id'         => 'is_archive_filter_cat_child',
            'type'       => 'switcher',
            'title'      => '启用二级分类筛选',
            'label'      => '当前分类页面有二级分类的时候显示子分类,当前分类下没有子分类则显示全部分类，如果当前二级分类下有三级分类，则显示到三级',
            'default'    => true,
            'dependency' => array('is_archive_filter', '==', 'true'),
        ),

        array(
            'id'         => 'archive_filter_cat_orderby',
            'type'       => 'radio',
            'title'      => '二,三级分类筛选排序方式',
            'inline'     => true,
            'options'    => array(
                'id'    => esc_html__('分类ID', 'riplus'),
                'name'  => esc_html__('分类名称', 'riplus'),
                'count' => esc_html__('文章数量', 'riplus'),
            ),
            'default'    => 'id',
            'dependency' => array('is_archive_filter_cat_child', '==', 'true'),
        ),
        array(
            'id'         => 'is_archive_filter_tag',
            'type'       => 'switcher',
            'title'      => '启用标签筛选',
            'label'      => '显示分类下文章相关标签20个,如果没有则显示全部其他标签，此处标签为当前分类下文章相关标签',
            'default'    => true,
            'dependency' => array('is_archive_filter', '==', 'true'),
        ),
        array(
            'id'         => 'is_archive_filter_price',
            'type'       => 'switcher',
            'title'      => '启用价格筛选',
            'label'      => '显示价格筛选',
            'default'    => true,
            'dependency' => array('is_archive_filter', '==', 'true'),
        ),
        array(
            'id'         => 'is_archive_filter_order',
            'type'       => 'switcher',
            'title'      => '启用排序筛选',
            'label'      => '显示排序筛选',
            'default'    => true,
            'dependency' => array('is_archive_filter', '==', 'true'),
        ),

        array(
            'id'      => 'is_archive_breadcrumb',
            'type'    => 'switcher',
            'title'   => '内页筛选显示面包屑导航',
            'default' => true,
        ),

    ),
));




//
// Basic 商城设置
//
CSF::createSection($prefix, array(
    'id'    => 'shop_fields',
    'title' => '商城设置',
    'icon'  => 'fa fa-plus-circle',
));


// 商城-基本设置
CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => '初始设置',
    'icon'   => 'fa fa-circle',
    'fields' => array(
        array(
            'id'      => 'is_site_shop',
            'type'    => 'switcher',
            'title'   => '启用商城和会员中心',
            'label'   => '一键开启关闭商城和会员功能，默认开启，关闭后所有商城相关功能都关闭，这样可以直接以博客主题展示',
            'default' => true,
        ),

        array(
            'id'      => 'is_site_mycoin',
            'type'    => 'switcher',
            'title'   => '启用网站站内币功能',
            'label'   => '开启后可以自定义站内币种名称，充值兑换比例等',
            'default' => true,
        ),

        array(
            'id'         => 'site_mycoin_name',
            'type'       => 'text',
            'title'      => '站内币名称',
            'desc'       => '设置站内币名称，例如：金币、下载币、积分、资源币、BB币、USDT等',
            'default'    => '金币',
            'attributes' => array(
                'style'    => 'width: 100px;'
            ),
            'dependency' => array('is_site_mycoin', '==', 'true'),
        ),


        array(
            'id'      => 'site_mycoin_rate',
            'type'    => 'text',
            'title'   => '站内币充值比例',
            'default' => '10',
            'desc'    => '默认：1元等于10个站内币(必须是正整数1~10000，建议一次设置好，后续谨慎更改，会影响后台订单的汇率)',
            'attributes' => array(
                'style'    => 'width: 100px;'
            ),
            'dependency' => array('is_site_mycoin', '==', 'true'),
        ),

        array(
            'id'         => 'site_mycoin_icon',
            'type'       => 'icon',
            'title'      => '站内币图标',
            'desc'       => '设置站内币图标，部分页面展示需要',
            'default'    => 'fas fa-coins',
            'dependency' => array('is_site_mycoin', '==', 'true'),
        ),

        array(
            'id'      => 'site_mycoin_pay_minnum',
            'type'    => 'text',
            'title'   => '站内币最小充值数量限制',
            'default' => '1',
            'desc'    => '',
            'attributes' => array(
                'style'    => 'width: 100px;'
            ),
            'dependency' => array('is_site_mycoin', '==', 'true'),
        ),
        array(
            'id'      => 'site_mycoin_pay_maxnum',
            'type'    => 'text',
            'title'   => '站内币最大充值数量限制',
            'default' => '9999',
            'desc'    => '',
            'attributes' => array(
                'style'    => 'width: 100px;'
            ),
            'dependency' => array('is_site_mycoin', '==', 'true'),
        ),
        array(
            'id'         => 'site_mycoin_pay_arr',
            'type'       => 'repeater',
            'title'      => esc_html__('站内币充值活动套餐设置', 'rizhuti-v2'),
            'fields'     => array(
                array(
                    'id'      => 'num',
                    'type'    => 'text',
                    'title'   => esc_html__('充值站内币数量', 'rizhuti-v2'),
                    'default' => '1',
                ),
                array(
                    'id'       => 'give',
                    'type'     => 'text',
                    'title'    => esc_html__('活动赠送数量', 'rizhuti-v2'),
                    'default'  => '0',
                ),
            ),
            'desc'    => '设置充值套餐，可以设置赠送活动，赠送数量填写0则无活动',
            'dependency' => array('is_site_mycoin', '==', 'true'),
        ),


        // 

        array(
            'id'          => 'show_shop_widget_wap_position',
            'type'        => 'select',
            'title'       => esc_html__('手机内下载组件显示位置', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'top'            => esc_html__('文章内容顶部', 'rizhuti-v2'),
                'bottom'             => esc_html__('文章内容底部', 'rizhuti-v2'),
            ),
            'default'     => 'bottom',
        ),


        array(
            'id'      => 'is_rizhuti_v2_nologin_pay',
            'type'    => 'switcher',
            'title'   => '开启免登陆购买支持',
            'label'   => '',
            'default' => true,
        ),
        array(
            'id'         => 'rizhuti_v2_nologin_payKey',
            'type'       => 'text',
            'title'      => '免登陆购买临时密钥',
            'desc'       => '设置随机6-18位字符串，防止被人恶意请求安全验证和检测用',
            'default'    => 'WOAI_riplus_666',
            'attributes' => array(
                'type' => 'password',
            ),
            'dependency' => array('is_rizhuti_v2_nologin_pay', '==', 'true'),
        ),
        array(
            'id'         => 'rizhuti_v2_nologin_days',
            'type'       => 'slider',
            'title'      => '免登录购买后有效期天数',
            'desc'       => '游客购买的时候会利用浏览器缓存技术记录游客购买的凭证密钥，在游客不清楚自己浏览器缓存数据的情况下，默认有效期天数',
            'default'    => 7,
            'dependency' => array('is_rizhuti_v2_nologin_pay', '==', 'true'),
        ),


    ),
));


// 商城-支付接口配置
CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => '支付接口配置',
    'icon'   => 'fa fa-circle',
    'fields' => array(

        // 站内币支付 mycoin
        array(
            'id'      => 'is_mycoin_pay',
            'type'    => 'switcher',
            'title'   => '站内' . _cao('site_mycoin_name', '下载币') . '-余额支付',
            'label'   => '开启后网站支持站内币充值，付款等',
            'default' => true,
        ),

        array(
            'id'      => 'is_cdk_pay',
            'type'    => 'switcher',
            'title'   => '站内卡密-CDK支付',
            'label'   => '开启后网站支持卡密CDK支付，充值余额',
            'default' => true,
        ),

        array(
            'id'    => 'cdk_pay_pay_link',
            'type'  => 'text',
            'title' => '卡密购买地址',
            'desc' => '不想用站自己支付的可以用卡密规避风险，自己生产充值卡密去第三方平台发卡，用户购买卡密后回来充值消费。',
            'dependency' => array('is_cdk_pay', '==', 'true'),
        ),

        // 支付宝配置
        array(
            'id'      => 'is_alipay',
            'type'    => 'switcher',
            'title'   => '支付宝（官方企业支付）',
            'label'   => '支付宝商户后台推荐签约签约当面付，电脑网站支付，手机网站支付',
            'default' => true,
        ),
        array(
            'id'         => 'alipay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(

                // 是否mapi老接口
                array(
                    'id'      => 'is_mapi',
                    'type'    => 'switcher',
                    'title'   => '使用老(mapi网关)接口支付',
                    'label'   => '支付宝商户需要签约电脑网站支付产品',
                    'default' => false,
                ),

                array(
                    'id'      => 'is_mobile',
                    'type'    => 'switcher',
                    'title'   => '手机端自动跳转支付',
                    'label'   => '(需签约手机网站支付产品，只支持手机浏览器打开唤醒APP支付，并不能在应用内，如QQ/微信/支付宝内部浏览器无效)',
                    'default' => false,
                ),

                array(
                    'id'    => 'pid',
                    'type'  => 'text',
                    'title' => '(mapi网关)-合作伙伴身份PID',
                    'dependency' => array('is_mapi', '==', 'true'),
                ),
                array(
                    'id'    => 'md5Key',
                    'type'  => 'text',
                    'title' => '(mapi网关)-MD5密钥',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                    'dependency' => array('is_mapi', '==', 'true'),
                ),


                array(
                    'id'         => 'appid',
                    'type'       => 'text',
                    'title'      => '开放平台-应用appid',
                    'dependency' => array('is_mapi', '==', 'false'),
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'         => 'privateKey',
                    'type'       => 'textarea',
                    'title'      => '开放平台-应用私钥',
                    'dependency' => array('is_mapi', '==', 'false'),
                ),
                array(
                    'id'         => 'publicKey',
                    'type'       => 'textarea',
                    'title'      => '开放平台-支付宝公钥',
                    'dependency' => array('is_mapi', '==', 'false'),
                ),

            ),
            'dependency' => array('is_alipay', '==', 'true'),
        ),

        // 微信支付配置
        array(
            'id'      => 'is_weixinpay',
            'type'    => 'switcher',
            'title'   => '微信支付（官方企业支付）',
            'label'   => '微信官方商户后台推荐签约native产品，JSAPI产品，h5支付产品',
            'default' => false,
        ),
        array(
            'id'         => 'weixinpay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'id'      => 'mch_id',
                    'type'    => 'text',
                    'title'   => '微信支付商户号',
                    'desc'    => '微信支付商户号 PartnerID 通过微信支付商户资料审核后邮件发送',
                    'default' => '',
                ),
                array(
                    'id'      => 'appid',
                    'type'    => 'text',
                    'title'   => '公众号或小程序APPID',
                    'desc'    => '公众号APPID 通过微信支付商户资料审核后邮件发送,开通jsapi支付和配置公众号手机内直接登录的用户注意,如果是小程序的appid,请到支付商户绑定公众号appid授权,这里填写为公众号即可',
                    'default' => '',
                ),
                array(
                    'id'         => 'key',
                    'type'       => 'text',
                    'title'      => '微信支付API密钥',
                    'desc'       => '帐户设置-安全设置-API安全-API密钥-设置API密钥',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'is_jsapi',
                    'type'    => 'switcher',
                    'title'   => 'JSAPI支付',
                    'label'   => '微信端内打开可以直接发起支付，开启此项需要登录注册里开启公众号登录，开启后网站用户在微信内登录后可以直接支付',
                    'default' => true,
                ),
                array(
                    'id'      => 'is_mobile',
                    'type'    => 'switcher',
                    'title'   => '手机跳转H5支付',
                    'label'   => '移动端自动自动切换为跳转支付（需开通H5支付，只支持手机浏览器打开唤醒APP支付，并不能在应用内，如QQ/微信/支付宝内部浏览器无效）',
                    'default' => false,
                ),
            ),
            'dependency' => array('is_weixinpay', '==', 'true'),
        ),

        //讯虎新支付 微信
        array(
            'id'      => 'is_xunhupay_weixin',
            'type'    => 'switcher',
            'title'   => '迅虎(微信H5支付)',
            'label'   => '无需企业资质，个人用户推荐，支持电PC端扫码，移动端H5唤醒支付，微信内JSAPI支付，无资质可以用此方法完美替代*_*',
            'default' => false,
        ),
        array(
            'id'         => 'xunhupay_weixin',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '讯虎支付 <a target="_blank" href="https://admin.xunhuweb.com/register/0ddbfa6c701f4f309e8d0bd99cdcc513">-->>注册地址</a><br/>如测试可使用测试mchid和密钥进行。但只支持0.1元付款测试<br/>',
                ),

                array(
                    'id'      => 'mchid',
                    'type'    => 'text',
                    'title'   => 'MCHID',
                    'desc'    => 'MCHID（测试：a0e5b19a8b4047c88184412997a421d1）',
                    'default' => '',
                ),
                array(
                    'id'      => 'private_key',
                    'type'    => 'text',
                    'title'   => 'Private Key',
                    'desc'    => '密钥（测试：a0c76773b8ca44ac9fa5100f5675c95f）',
                    'default' => '',
                    'attributes'  => array(
                        'type'      => 'password',
                    ),
                ),
                array(
                    'id'      => 'url_do',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
                    'default' => 'https://admin.xunhuweb.com',
                ),

            ),
            'dependency' => array('is_xunhupay_weixin', '==', 'true'),
        ),

        //讯虎新支付 支付宝
        array(
            'id'      => 'is_xunhupay_alipay',
            'type'    => 'switcher',
            'title'   => '迅虎(支付宝H5支付)',
            'label'   => '无需企业资质，个人用户推荐，支持电PC端扫码，移动端H5唤醒支付，微信内JSAPI支付，无资质可以用此方法完美替代*_*',
            'default' => false,
        ),
        array(
            'id'         => 'xunhupay_alipay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '讯虎支付 <a target="_blank" href="https://admin.xunhuweb.com/register/0ddbfa6c701f4f309e8d0bd99cdcc513">-->>注册地址</a><br/>如测试可使用测试mchid和密钥进行。但只支持0.1元付款测试<br/>',
                ),
                array(
                    'id'      => 'mchid',
                    'type'    => 'text',
                    'title'   => 'MCHID',
                    'desc'    => 'MCHID（测试：a0e5b19a8b4047c88184412997a421d1）',
                    'default' => '',
                ),
                array(
                    'id'      => 'private_key',
                    'type'    => 'text',
                    'title'   => 'Private Key',
                    'desc'    => '密钥（测试：a0c76773b8ca44ac9fa5100f5675c95f）',
                    'default' => '',
                    'attributes'  => array(
                        'type'      => 'password',
                    ),
                ),
                array(
                    'id'      => 'url_do',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
                    'default' => 'https://admin.xunhuweb.com',
                ),

            ),
            'dependency' => array('is_xunhupay_alipay', '==', 'true'),
        ),

        //虎皮椒 weixin
        array(
            'id'      => 'is_hupijiao_weixin',
            'type'    => 'switcher',
            'title'   => '虎皮椒V3(微信)',
            'label'   => '当前是最新的虎皮椒V3版，微信完美收款，无资质可以用此方法完美替代*_*',
            'default' => false,
        ),
        array(
            'id'         => 'hupijiao_weixin',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '虎皮椒V3  <a target="_blank" href="https://admin.xunhupay.com/sign-up/4123.html">注册地址</a><br/>如测试可使用测试APPID和密钥进行。但只支持0.1元付款测试<br/>',
                ),
                array(
                    'id'      => 'appid',
                    'type'    => 'text',
                    'title'   => 'APPID',
                    'desc'    => 'APPID（测试：201906130530）',
                    'default' => '',
                ),
                array(
                    'id'         => 'appsecret',
                    'type'       => 'text',
                    'title'      => 'APPSECRET',
                    'desc'       => '密钥（测试：e97a75d2ee14e353fa745f7c47d23ed0）',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'url_do',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
                    'default' => 'https://api.xunhupay.com/payment/do.html',
                ),

            ),
            'dependency' => array('is_hupijiao_weixin', '==', 'true'),
        ),

        //虎皮椒 alpay
        array(
            'id'      => 'is_hupijiao_alipay',
            'type'    => 'switcher',
            'title'   => '虎皮椒V3(支付宝)',
            'label'   => '当前是最新的虎皮椒V3版，支付宝完美收款，无资质可以用此方法完美替代*_*',
            'default' => false,
        ),
        array(
            'id'         => 'hupijiao_alipay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '虎皮椒（讯虎支付）V3  <a target="_blank" href="https://admin.xunhupay.com/sign-up/4123.html">注册地址</a><br/>如测试可使用测试APPID和密钥进行。但只支持0.1元付款测试<br/>',
                ),
                array(
                    'id'      => 'appid',
                    'type'    => 'text',
                    'title'   => 'APPID',
                    'desc'    => 'APPID',
                    'default' => '',
                ),
                array(
                    'id'         => 'appsecret',
                    'type'       => 'text',
                    'title'      => 'APPSECRET',
                    'desc'       => '密钥',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'url_do',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
                    'default' => 'https://api.xunhupay.com/payment/do.html',
                ),

            ),
            'dependency' => array('is_hupijiao_alipay', '==', 'true'),
        ),




    ),
));

// 商城-网站会员设置
CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => 'VIP会员组设置',
    'icon'   => 'fa fa-user',
    'fields' => array(

        array(
            'id'    => 'site_vip_options',
            'type'  => 'tabbed',
            'title' => '会员组设置',
            'tabs'  => array(
                array(
                    'id'     => 'no_vip',
                    'title'  => '普通用户',
                    'icon'   => 'fa fa-circle',
                    'fields' => array(
                        array(
                            'id'    => '0_vip_downnum',
                            'type'  => 'text',
                            'title' => '每日可下载次数',
                            'default' => '5',
                        ),
                        array(
                            'id'    => '0_vip_download_rate',
                            'type'  => 'text',
                            'title' => '下载速度限制(kb/s)',
                            'desc' => '本地文件最大下载带宽速度限制，单位kb/s',
                            'default' => '100',
                        ),
                        array(
                            'id'    => '0_vip_aff_ratio',
                            'type'  => 'text',
                            'title' => '推广佣金比例',
                            'desc' => '通过该会员推广链接购买奖励比例，0为关闭，0.05为百分之5',
                            'default' => '0.05',
                        ),

                        array(
                            'id'      => '0_vip_author_aff_ratio',
                            'type'    => 'text',
                            'title'   => '网站作者提成',
                            'desc'    => '如果文章是用户发布的，被购买时奖励此作者佣金比例，0为不开启,0.1等于百分之10',
                            'default' => '0',
                        ),
                    ),
                ),
                array(
                    'id'     => '31_vip',
                    'title'  => '月卡VIP',
                    'icon'   => 'fa fa-diamond',
                    'fields' => array(
                        array(
                            'id'    => '31_vip_price',
                            'type'  => 'text',
                            'title' => 'VIP售价/1个月',
                            'default' => '19',
                        ),
                        array(
                            'id'    => '31_vip_downnum',
                            'type'  => 'text',
                            'title' => '每日可下载次数',
                            'default' => '10',
                        ),
                        array(
                            'id'    => '31_vip_download_rate',
                            'type'  => 'text',
                            'title' => '下载速度限制(kb/s)',
                            'desc' => '本地文件最大下载带宽速度限制，单位kb/s',
                            'default' => '200',
                        ),
                        array(
                            'id'    => '31_vip_aff_ratio',
                            'type'  => 'text',
                            'title' => '推广佣金比例',
                            'desc' => '通过该会员推广链接购买奖励比例，0为关闭，0.1为百分之10',
                            'default' => '0.1',
                        ),

                        array(
                            'id'      => '31_vip_author_aff_ratio',
                            'type'    => 'text',
                            'title'   => '网站作者提成',
                            'desc'    => '如果文章是用户发布的，被购买时奖励此作者佣金比例，0为不开启,0.1等于百分之10',
                            'default' => '0',
                        ),

                    ),
                ),
                array(
                    'id'     => '365_vip',
                    'title'  => '年卡VIP',
                    'icon'   => 'fa fa-diamond',
                    'fields' => array(
                        array(
                            'id'    => '365_vip_price',
                            'type'  => 'text',
                            'title' => 'VIP售价/1年',
                            'default' => '99',
                        ),
                        array(
                            'id'    => '365_vip_downnum',
                            'type'  => 'text',
                            'title' => '每日可下载次数',
                            'default' => '20',
                        ),

                        array(
                            'id'    => '365_vip_download_rate',
                            'type'  => 'text',
                            'title' => '下载速度限制(kb/s)',
                            'desc' => '本地文件最大下载带宽速度限制，单位kb/s',
                            'default' => '300',
                        ),
                        array(
                            'id'    => '365_vip_aff_ratio',
                            'type'  => 'text',
                            'title' => '推广佣金比例',
                            'desc' => '通过该会员推广链接购买奖励比例，0为关闭，0.15为百分之15',
                            'default' => '0.15',
                        ),

                        array(
                            'id'      => '365_vip_author_aff_ratio',
                            'type'    => 'text',
                            'title'   => '网站作者提成',
                            'desc'    => '如果文章是用户发布的，被购买时奖励此作者佣金比例，0为不开启,0.1等于百分之10',
                            'default' => '0',
                        ),

                    ),
                ),
                array(
                    'id'     => '3600_vip',
                    'title'  => '永久VIP',
                    'icon'   => 'fa fa-diamond',
                    'fields' => array(
                        array(
                            'id'    => '3600_vip_price',
                            'type'  => 'text',
                            'title' => 'VIP售价/永久',
                            'default' => '199',
                        ),
                        array(
                            'id'    => '3600_vip_downnum',
                            'type'  => 'text',
                            'title' => '每日可下载次数',
                            'default' => '99',
                        ),
                        array(
                            'id'    => '3600_vip_download_rate',
                            'type'  => 'text',
                            'title' => '下载速度限制(kb/s)',
                            'desc' => '本地文件最大下载带宽速度限制，单位kb/s',
                            'default' => '2000',
                        ),
                        array(
                            'id'    => '3600_vip_aff_ratio',
                            'type'  => 'text',
                            'title' => '推广佣金比例',
                            'desc' => '通过该会员推广链接购买奖励比例，0为关闭，0.2为百分之20',
                            'default' => '0.2',
                        ),

                        array(
                            'id'      => '3600_vip_author_aff_ratio',
                            'type'    => 'text',
                            'title'   => '网站作者提成',
                            'desc'    => '如果文章是用户发布的，被购买时奖励此作者佣金比例，0为不开启,0.1等于百分之10',
                            'default' => '0',
                        ),

                    ),
                ),
            ),
        ),


        array(
            'id'         => 'is_site_aff',
            'type'       => 'switcher',
            'title'      => '会员推广奖励',
            'desc'      => '关闭后网站不涉及推广奖励功能',
            'default'    => true,
        ),

        array(
            'id'         => 'is_site_author_aff',
            'type'       => 'switcher',
            'title'      => '作者文章提成',
            'desc'      => '关闭后网站不涉及作者文章提成',
            'default'    => true,
        ),


    ),
));


// 商城-付费资源默认发布字段配置
CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => '付费字段默认配置',
    'icon'   => 'fa fa-user',
    'fields' => array(

        array(
            'id'      => 'wppay_type',
            'type'    => 'select',
            'title'   => esc_html__('默认发布-资源类型', 'rizhuti-v2'),
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
            'default' => 0,
        ),

        array(
            'id'         => 'wppay_price',
            'type'       => 'text',
            'title'      => esc_html__('默认发布-收费价格', 'rizhuti-v2'),
            'desc'       => esc_html__('单位RMB,价格为0时，如果启用VIP会员权限，则普通用户不能购买。只允许会员下载，反之普通用户可以购买', 'rizhuti-v2'),
            'default'    => '0.1',
            'validate'   => 'csf_validate_numeric',
        ),

        array(
            'id'         => 'wppay_vip_auth',
            'type'       => 'select',
            'title'      => esc_html__('默认发布-VIP会员权限', 'rizhuti-v2'),
            'subtitle'   => esc_html__('权限关系是包含关系，终身可查看年月', 'rizhuti-v2'),
            'inline'     => true,
            'options'    => array(
                '0' => esc_html__('不启用', 'rizhuti-v2'),
                '1' => esc_html__('包月VIP免费', 'rizhuti-v2'),
                '2' => esc_html__('包年VIP免费', 'rizhuti-v2'),
                '3' => esc_html__('终身VIP免费', 'rizhuti-v2'),
            ),
            'default'    => 0,
        ),

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
        ),


    ),
));





CSF::createSection($prefix, array(
    'title'  => '登录注册',
    'icon'   => 'fa fa-circle',
    'fields' => array(

        array(
            'id'         => 'site_login_bg_img',
            'type'       => 'upload',
            'title'      => '登录页背景',
            'dsec'       => '',
            'default'    => get_template_directory_uri() . '/assets/img/login-bg.jpg',
        ),

        array(
            'id'      => 'site_login_reg_href1',
            'type'    => 'text',
            'title'   => '用户协议页面地址',
            'default' => '#',
        ),
        array(
            'id'      => 'site_login_reg_href2',
            'type'    => 'text',
            'title'   => '隐私政策页面地址',
            'default' => '#',
        ),

        array(
            'id'         => 'site_profile_bg_img',
            'type'       => 'upload',
            'title'      => '个人中心页顶部背景',
            'dsec'       => '',
            'default'    => get_template_directory_uri() . '/assets/img/profile-bg.jpg',
        ),

        array(
            'id'         => 'is_site_register_bind_email',
            'type'       => 'switcher',
            'title'      => '第三方登录新用户注册必须绑定邮箱',
            'desc'      => '开启后将输入邮箱验证，第三方登录新用户注册必须绑定邮箱账号',
            'default'    => false,
        ),

        array(
            'id'         => 'is_site_email_captcha_verify',
            'type'       => 'switcher',
            'title'      => '邮箱验证码',
            'desc'      => '<b style="color:red;">网站需配置smtp发信</b> 启用后绑定/更换邮箱/等敏感操作需要邮箱验证码确认，保证账号安全',
            'default'    => false,
        ),

        array(
            'id'      => 'is_qq_007_captcha',
            'type'    => 'switcher',
            'title'   => '腾讯云验证码防水墙',
            'desc'   => '同时兼容原腾讯007防水墙和腾讯云验证码，启用后登录，注册，绑定等敏感操作需要安全验证，可以有效防止恶意注册登录爆破，<br>注册地址：https://console.cloud.tencent.com/captcha',
            'default' => false,
        ),
        array(
            'id'      => 'qq_007_captcha_appid',
            'type'    => 'text',
            'title'   => 'App ID',
            'desc' => '查看地址：https://007.qq.com/captcha/?ADTAG=acces.head#/gettingStart',
            'dependency' => array('is_qq_007_captcha', '==', 'true'),
            'default' => false,
        ),
        array(
            'id'      => 'qq_007_captcha_appkey',
            'type'    => 'text',
            'title'   => 'App Secret Key',
            'attributes' => array(
                'type' => 'password',
            ),
            'desc' => '查看地址：https://007.qq.com/captcha/?ADTAG=acces.head#/gettingStart',
            'dependency' => array('is_qq_007_captcha', '==', 'true'),
        ),



        array(
            'id'      => 'is_site_user_login',
            'type'    => 'switcher',
            'title'   => '启用登录模块',
            'default' => true,
        ),

        array(
            'id'      => 'is_site_user_register',
            'type'    => 'switcher',
            'title'   => '启用注册模块',
            'default' => true,
        ),

        array(
            'id'      => 'is_sns_qq',
            'type'    => 'switcher',
            'title'   => 'QQ登录',
            'label'   => '申请地址：https://connect.qq.com/',
            'default' => false,
        ),
        array(
            'id'         => 'sns_qq',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'id'      => 'app_id',
                    'type'    => 'text',
                    'title'   => 'Appid',
                    'default' => '',
                ),
                array(
                    'id'      => 'app_secret',
                    'type'    => 'text',
                    'title'   => 'Appkey',
                    'default' => '',
                ),
                array(
                    'type'    => 'subheading',
                    'content' => '回调地址：' . esc_url(home_url('/oauth/qq/callback')),
                ),
            ),
            'dependency' => array('is_sns_qq', '==', 'true'),
        ),
        array(
            'id'      => 'is_sns_weixin',
            'type'    => 'switcher',
            'title'   => '微信登录',
            'label'   => '申请地址：https://open.weixin.qq.com/',
            'default' => false,
        ),
        array(
            'id'         => 'sns_weixin',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                // 微信登陆模式
                array(
                    'id'          => 'sns_weixin_mod',
                    'type'        => 'select',
                    'title'       => esc_html__('微信登陆模式', 'rizhuti-v2'),
                    'placeholder' => '',
                    'options'     => array(
                        'open'            => esc_html__('微信开放平台', 'rizhuti-v2'),
                        'mp'             => esc_html__('微信公众号（认证服务号）', 'rizhuti-v2'),
                    ),
                    'default'     => 'mp',
                    'desc'     => '推荐使用公众号模式，因微信官方openid和unionid模式错综复杂，建议不要中途更换模式',
                ),

                array(
                    'id'      => 'app_id',
                    'type'    => 'text',
                    'title'   => '开放平台 Appid',
                    'default' => '',
                    'dependency' => array('sns_weixin_mod', '==', 'open'),
                ),
                array(
                    'id'      => 'app_secret',
                    'type'    => 'text',
                    'title'   => '开放平台 AppSecret',
                    'default' => '',
                    'dependency' => array('sns_weixin_mod', '==', 'open'),
                ),
                array(
                    'type'    => 'subheading',
                    'content' => '开放平台-应用官网：' . home_url() . '<br>开放平台-授权回调域：' . parse_url(home_url(), PHP_URL_HOST),
                    'dependency' => array('sns_weixin_mod', '==', 'open'),
                ),
                array(
                    'id'         => 'mp_app_id',
                    'type'       => 'text',
                    'title'      => '公众号 Appid',
                    'default'    => '',
                    'dependency' => array('sns_weixin_mod', '==', 'mp'),
                ),
                array(
                    'id'         => 'mp_app_secret',
                    'type'       => 'text',
                    'title'      => '公众号 AppSecret',
                    'default'    => '',
                    'dependency' => array('sns_weixin_mod', '==', 'mp'),
                ),
                array(
                    'id'         => 'mp_app_token',
                    'type'       => 'text',
                    'title'      => '公众号配置 token',
                    'default'    => 'rizhutiv28888',
                    'desc'    => '填写-公众号后台->基本配置->服务器配置->令牌(Token)',
                    'dependency' => array('sns_weixin_mod', '==', 'mp'),
                ),
                array(
                    'id'      => 'is_mp_bind_open',
                    'type'    => 'switcher',
                    'title'   => '公众号已经绑定开放平台',
                    'label'   => '申请地址：https://open.weixin.qq.com/',
                    'default' => false,
                    'dependency' => array('sns_weixin_mod', '==', 'mp'),
                ),

                //自定义公众号菜单
                array(
                    'id'         => 'custom_wxmenu_opt',
                    'type'       => 'group',
                    'title'      => '自定义公众号菜单',
                    'max'        => '3',
                    'fields'     => array(

                        array(
                            'id'      => 'name',
                            'type'    => 'text',
                            'title'   => '菜单名称',
                            'default' => '首页',
                        ),
                        array(
                            'id'      => 'url',
                            'type'    => 'text',
                            'title'   => '链接',
                            'default' => '',
                        ),

                        array(
                            'id'     => 'sub_button',
                            'type'   => 'group',
                            'title'  => '二级菜单',
                            'max'        => '3',
                            'fields' => array(
                                array(
                                    'id'      => 'name',
                                    'type'    => 'text',
                                    'title'   => '菜单名称',
                                    'default' => '二级菜单',
                                ),
                                array(
                                    'id'      => 'url',
                                    'type'    => 'text',
                                    'title'   => '链接',
                                    'default' => '',
                                ),
                            ),
                        ),

                    ),
                    'dependency' => array('sns_weixin_mod', '==', 'mp'),
                ),
                array(
                    'type'       => 'subheading',
                    'content'    => '保存好后刷新公众号菜单：<button type="button" class="rest_mpweixin_menu">点击刷新公众号菜单</button>',
                    'dependency' => array('sns_weixin_mod', '==', 'mp'),
                ),

                array(
                    'type'       => 'subheading',
                    'content'    => '1，公众号设置->功能设置->网页授权域名填写：' . parse_url(home_url(), PHP_URL_HOST) . '</br> 2，基本配置->服务器配置->服务器地址(URL)填写：' . home_url('/oauth/mpweixin/callback') . '</br>3，基本配置->服务器配置->明文模式，启用配置即可',
                    'dependency' => array('sns_weixin_mod', '==', 'mp'),
                ),
            ),
            'dependency' => array('is_sns_weixin', '==', 'true'),
        ),
        array(
            'id'      => 'is_sns_weibo',
            'type'    => 'switcher',
            'title'   => '微博登录',
            'label'   => '申请地址：https://open.weibo.com/authentication/',
            'default' => false,
        ),
        array(
            'id'         => 'sns_weibo',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'id'      => 'app_id',
                    'type'    => 'text',
                    'title'   => 'Appid',
                    'default' => '',
                ),
                array(
                    'id'      => 'app_secret',
                    'type'    => 'text',
                    'title'   => 'Appkey',
                    'default' => '',
                ),
                array(
                    'type'    => 'subheading',
                    'content' => '回调地址：' . esc_url(home_url('/oauth/weibo/callback')),
                ),
            ),
            'dependency' => array('is_sns_weibo', '==', 'true'),
        ),

    ),
));



CSF::createSection($prefix, array(
    'title'  => '底部设置',
    'icon'   => 'fa fa-circle',
    'fields' => array(

        // rollbar
        array(
            'id'      => 'site_footer_rollbar',
            'type'    => 'group',
            'title'   => '全站右下角菜单（返回顶部+）',
            'max'     => '10',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '菜单名称',
                    'default' => '首页',
                ),
                array(
                    'id'    => 'icon',
                    'type'  => 'icon',
                    'title' => '图标',
                ),
                array(
                    'id'      => 'is_blank',
                    'type'    => 'switcher',
                    'title'   => '新窗口打开',
                    'default' => true,
                ),
                array(
                    'id'      => 'href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'desc'    => '比如用户中心，填写' . home_url('/user'),
                    'default' => home_url(),
                ),

            ),
            'default' => array(
                array(
                    'title' => '客服',
                    'icon'  => 'fas fa-headset',
                    'href'  => 'http://wpa.qq.com/msgrd?v=3&uin=6666666&site=qq&menu=yes',
                ),
                array(
                    'title' => '云服务器推荐',
                    'icon'  => 'far fa-hdd',
                    'href'  => 'https://www.aliyun.com/minisite/goods?userCode=u4kxbrjo',
                ),
                array(
                    'title' => '会员',
                    'icon'  => 'fa fa-diamond',
                    'href'  => home_url('/user?action=vip'),
                ),
                array(
                    'title' => '我的',
                    'icon'  => 'far fa-user',
                    'href'  => home_url('/user'),
                ),
            ),
        ),



        array(
            'id'      => 'is_site_footer_widget',
            'type'    => 'switcher',
            'title'   => '是否开启底部自定义小工具模块',
            'desc'    => '在外观-小工具添加底部小工具即可',
            'default' => true,
        ),

        array(
            'id'         => 'site_footer_logo',
            'type'       => 'upload',
            'title'      => '底部LOGO',
            'default'    => get_template_directory_uri() . '/assets/img/logo.png',
            'dependency' => array('is_site_footer_widget', '==', 'true'),
        ),
        array(
            'id'         => 'site_footer_desc',
            'type'       => 'textarea',
            'sanitize' => false,
            'title'      => '底部LOGO下文字介绍',
            'subtitle'   => '自定义文字介绍',
            'default'    => '日主题v2是一款全新架构的Wordpress主题，兼容老款日主题，支持会员商城 ，前后台界面均支持html5响应式布局，夜间模式一键切换。',
            'dependency' => array('is_site_footer_widget', '==', 'true'),
        ),

        array(
            'id'          => 'site_footer_links',
            'type'        => 'select',
            'title'       => esc_html__('选择友情链接', 'rizhuti-v2'),
            'placeholder' => esc_html__('不开启', 'rizhuti-v2'),
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'  => 'link_category',
            ),
        ),

        array(
            'id'       => 'site_copyright_text',
            'type'     => 'textarea',
            'title'    => '底部版权信息',
            'sanitize' => false,
            'subtitle' => '自定义版权信息',
            'default'  => 'Copyright © 2021 <a href="http://ritheme.com/">日主题V2</a> - All rights reserved',
        ),
        array(
            'id'       => 'site_ipc_text',
            'type'     => 'textarea',
            'sanitize' => false,
            'title'    => '网站备案链接',
            'subtitle' => '',
            'default'  => '<a href="https://beian.miit.gov.cn" target="_blank" rel="noreferrer nofollow">京ICP备18888888号-1</a>',
        ),
        array(
            'id'       => 'site_ipc2_text',
            'type'     => 'textarea',
            'sanitize' => false,
            'title'    => '网站公安备案链接',
            'subtitle' => '',
            'default'  => '<a href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=188888888" target="_blank" rel="noreferrer nofollow">京公网安备 188888888</a>',
        ),

        array(
            'id'       => 'web_js',
            'type'     => 'textarea',
            'title'    => '网站底部自定义JS代码',
            'desc' => '位于底部，用于添加第三方流量数据统计代码，如：Google analytics、百度统计、CNZZ,例如：' . esc_attr('<script>统计代码</script>'),
            'sanitize' => false,
            'default'  => '',
        ),

    ),
));


CSF::createSection($prefix, array(
    'title'       => '主题授权',
    'icon'        => 'fa fa-handshake-o',
    'description' => '<i class="fa fa-heart" style=" color: red; "></i> 感谢您使用本主题进行创作，请先填写ritheme.com个人中心您的授权ID和授权码点击保存验证。<i class="fa fa-heart" style=" color: red; "></i>
  <br/>------授权错误会提示错误消息，成功则顶部显示-设置已保存。</br></br><i class="fa fa-heart" style=" color: red; "></i> PS：承蒙您对本主题的喜爱，我们愿向小三一样，做大哥的女人，做大哥网站中最想日的一个。开发者不易，我们真诚的希望和感谢每一个真心建站运营的朋友能支持日系列的正版主题，更好的更用心的等你来调教。如此强大好用的日系列主题，包含支付等重要安全功能，我们希望用户一定要重视自己的数据安全，不要相信所谓破解等盗版，盗版无利不起早，一切以安全为己任。共同维护创造国内健康的WordPress使用环境。开发者油条敬上。</br>',
    'fields'      => array(

        array(
            'id'    => 'ri_active_id',
            'type'  => 'text',
            'title' => '会员ID',
            'after' => '<p><b><font color="red">注意不要有空格 </font></b>会员购买授权中心 <a href="https://ritheme.com/" target="_blank"> RI-VIP官网</a></p>',

        ),
        array(
            'id'    => 'ri_active_key',
            'type'  => 'text',
            'title' => '授权码',
            'after' => '<p><b><font color="red">注意不要有空格</font></b> 会员购买授权中心 <a href="https://ritheme.com/" target="_blank"> RI-VIP官网</a></p>',
            'attributes'  => array(
                'type'      => 'password',
                'autocomplete' => 'off',
            ),
        ),

    ),
));
