<?php if (!defined('ABSPATH')) {die;} // Cannot access directly.




/**
 * 视差背景
 */
CSF::createWidget('rizhuti_v2_module_parallax', array(
    'title'       => esc_html__('RI-首页模块 : 视差背景', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-parallax',
    'description' => esc_html__('Displays a parallax background.', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'image',
            'type'  => 'upload',
            'title' => esc_html__('背景图', 'rizhuti-v2'),
            'default'     => get_template_directory_uri() . '/assets/img/top-bg.jpg',
        ),
        array(
            'id'       => 'text',
            'type'     => 'text',
            'title'    => esc_html__('文字描述介绍', 'rizhuti-v2'),
            'default'  => esc_html__('文字描述介绍', 'rizhuti-v2'),
            'sanitize' => false,
        ),

        array(
            'id'    => 'link',
            'type'  => 'Text',
            'title' => esc_html__('主链接', 'rizhuti-v2'),
        ),

        array(
            'id'    => 'new_tab',
            'type'  => 'switcher',
            'title' => esc_html__('新窗口打开链接？', 'rizhuti-v2'),
        ),

        array(
            'id'      => 'primary_text',
            'type'    => 'Text',
            'title'   => esc_html__('按钮1文字', 'rizhuti-v2'),
            'default' => '<i class="fa fa-credit-card"></i> 关于我们',
        ),

        array(
            'id'    => 'primary_link',
            'type'  => 'Text',
            'title' => esc_html__('按钮1链接', 'rizhuti-v2'),
        ),

        array(
            'id'    => 'primary_new_tab',
            'type'  => 'switcher',
            'title' => esc_html__('按钮1新窗口打开链接？', 'rizhuti-v2'),
        ),

        array(
            'id'      => 'secondary_text',
            'type'    => 'Text',
            'title'   => esc_html__('按钮2文本', 'rizhuti-v2'),
            'default' => '<i class="fa fa-paw"></i> 更多介绍',
        ),

        array(
            'id'    => 'secondary_link',
            'type'  => 'Text',
            'title' => esc_html__('按钮2链接', 'rizhuti-v2'),
        ),

        array(
            'id'    => 'secondary_new_tab',
            'type'  => 'switcher',
            'title' => esc_html__('按钮2新窗口打开链接？', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_parallax')) {
    function rizhuti_v2_module_parallax($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'image' => get_template_directory_uri() . '/assets/img/top-bg.jpg',
            'text' => '文字描述介绍',
        ), $instance);


        echo $args['before_widget'];

        ob_start();?>
        <div class="module parallax">
        <?php if (!empty($instance['image'])): ?>
          <img class="jarallax-img lazyload" data-src="<?php echo esc_url($instance['image']); ?>" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="<?php echo esc_attr($instance['text']); ?>">
        <?php endif;

        if ($instance['text'] != ''): ?>
          <div class="container">
            <h4 class="entry-title"><?php echo $instance['text']; ?></h4>
            <?php if ($instance['primary_text'] != ''): ?>
              <a class="btn btn-warning btn-sm" href="<?php echo esc_url($instance['primary_link']); ?>"<?php echo esc_attr($instance['primary_new_tab'] ? ' target="_blank"' : ''); ?>><?php echo $instance['primary_text']; ?></a>
            <?php endif;?>
            <?php if ($instance['secondary_text'] != ''): ?>
              <a class="btn btn-light btn-sm" href="<?php echo esc_url($instance['secondary_link']); ?>"<?php echo esc_attr($instance['secondary_new_tab'] ? ' target="_blank"' : ''); ?>><?php echo $instance['secondary_text']; ?></a>
            <?php endif;?>
          </div>
        <?php endif;

        if (!empty($instance['link'])): ?>
          <a class="u-permalink" href="<?php echo esc_url($instance['link']); ?>"<?php echo esc_attr($instance['new_tab'] ? ' target="_blank"' : ''); ?>></a>
        <?php endif;?>
        </div> <?php

        echo ob_get_clean();

        echo $args['after_widget'];

    }
}



/**
 * 首页搜索模块+背景图片 视频
 */
CSF::createWidget('rizhuti_v2_module_search', array(
    'title'       => esc_html__('RI-首页模块 : 高级搜索模块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-search-bg',
    'description' => esc_html__('Displays a parallax background.', 'rizhuti-v2'),
    'fields'      => array(

        
        array(
            'id'       => 'title',
            'type'     => 'text',
            'title'    => esc_html__('搜索介绍标题', 'rizhuti-v2'),
            'default'  => esc_html__('这是一个优雅的搜索框', 'rizhuti-v2'),
        ),
        array(
            'id'       => 'desc',
            'type'     => 'text',
            'title'    => esc_html__('搜索描述介绍', 'rizhuti-v2'),
            'default'  => esc_html__('支持图片背景，视频背景，支持分类高级筛选搜索，支持自定义搜索热门词展示', 'rizhuti-v2'),
        ),

        array(
            'id'       => 'placeholder',
            'type'     => 'text',
            'title'    => esc_html__('输入框提示文字', 'rizhuti-v2'),
            'default'  => esc_html__('优质内容等你来搜', 'rizhuti-v2'),
        ),

        array(
            'id'          => 'bg_type',
            'type'        => 'radio',
            'title'       => esc_html__('背景类型', 'rizhuti-v2'),
            'placeholder' => '',
            'options'     => array(
                'img' => esc_html__('图片背景', 'rizhuti-v2'),
                'video' => esc_html__('MP4视频背景', 'rizhuti-v2'),
            ),
            'inline'     => true,
            'default'     => 'img',
        ),

        array(
            'id'    => 'bg',
            'type'  => 'upload',
            'title' => esc_html__('背景图或背景视频', 'rizhuti-v2'),
            'default'     => get_template_directory_uri() . '/assets/img/login-bg.jpg',
        ),

        array(
            'id'    => 'is_cat',
            'type'  => 'switcher',
            'title' => esc_html__('高级分类搜索', 'rizhuti-v2'),
            'default' => true,
        ),

        array(
            'id'    => 'search_hot',
            'type'  => 'textarea',
            'title' => esc_html__('搜索热词', 'rizhuti-v2'),
            'desc' => esc_html__('每个搜索词用英文逗号隔开', 'rizhuti-v2'),
            'default' => 'wordpress,sss,测试,下载,素材,作品,主题,插件,你好',
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_search')) {
    function rizhuti_v2_module_search($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'title' => '搜索介绍标题',
            'desc' => '文字描述介绍',
            'bg_type' => 'img', //video
        ), $instance);

        if (empty($instance['bg_type']) || $instance['bg_type']=='img') {
            $data_type = 'data-bg="'.$instance['bg'].'"';
        }else{
            $data_type = 'data-jarallax-video="mp4:'.$instance['bg'].'"';
        }

        if (wp_is_mobile() && $instance['bg_type'] == 'video') {
            $data_type = 'data-bg="'.get_template_directory_uri() .'/assets/img/login-bg.jpg"';
        }

        echo $args['before_widget'];


        ob_start();?>
        <div class="module lazyload search-bg jarallax-sarch <?php echo $instance['bg_type'];?>" <?php echo $data_type;?>>
            <div class="search-bg-overlay"></div>
            <div class="container">
                <h2 class="search-title"><?php echo $instance['title'];?></h2>
                <p class="search-desc"><?php echo $instance['desc'];?></p>

                <div class="search-form">
                    <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <div class="search-select">
                        <?php wp_dropdown_categories( array(
                            'hide_empty'       => true,
                            'show_option_none' => esc_html__('全部','rizhuti-v2'),
                            'option_none_value' => '',
                            'orderby'          => 'name',
                            'hierarchical'     => true,
                            'depth'     => 1,
                            'id'     => 'modulesearch-cat',
                            'class'     => 'selectpicker',
                        ) );?>
                        </div>

                        <div class="search-fields">
                          <input type="text" class="" placeholder="<?php echo $instance['placeholder'];?>" autocomplete="off" value="<?php echo esc_attr( get_search_query() ) ?>" name="s" required="required">
                          <button class="" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
                
                <?php if ( !empty($instance['search_hot']) ) {
                    echo '<div class="popula-search-key">';
                    echo '<span>'.esc_html__('热门搜索： ','rizhuti-v2').'</span>';
                    $item_exp = explode(",", trim($instance['search_hot']));
                    foreach ($item_exp as $v) {
                        echo '<a href="'.get_search_link($v).'">'.$v.'</a>，';
                    }
                    echo '</div>';

                }?>
            </div>
        </div>

        <?php
        echo ob_get_clean();

        echo $args['after_widget'];

    }
}


/**
 * 自定义幻灯片全屏
 */
CSF::createWidget('rizhuti_v2_module_slideer_img', array(
    'title'       => esc_html__('RI-首页模块 : 全屏图片幻灯片', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-slideer-img',
    'description' => esc_html__('Displays a 图片幻灯片，支持全宽/普通', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'      => 'style',
            'type'    => 'radio',
            'title'   => '布局风格',
            'inline'  => true,
            'options' => array(
                'full' => esc_html__('全宽', 'rizhuti-v2'),
                'big'  => esc_html__('普通', 'rizhuti-v2'),
            ),
            'default' => 'full',
        ),
        array(
            'id'     => 'diy_data',
            'type'   => 'group',
            'title'  => '主幻灯片',
            'fields' => array(
                array(
                    'id'          => '_img',
                    'type'        => 'upload',
                    'title'       => '上传幻灯片',
                    'default'     => get_template_directory_uri() . '/assets/img/bg.jpg',
                ),
                array(
                    'id'      => '_blank',
                    'type'    => 'switcher',
                    'title'   => '新窗口打开链接',
                    'default' => true,
                ),
                array(
                    'id'      => '_href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'default' => '',
                ),
                array(
                    'id'      => '_desc',
                    'type'    => 'textarea',
                    'title'   => '描述内容，支持html代码',
                    'sanitize' => false,
                    'default' => '<h3 class="text-white">Hello, RizhutiV2</h3><p class="lead  text-white d-none d-lg-block">这是一个简单的内容展示，支持 <span class="badge badge-light">BootstrapV4</span> 的所有代码，您可以随意插入HTML代码任意组合显示。',
                ),

            ),
            
        ),
        array(
            'id'    => 'autoplay',
            'type'  => 'switcher',
            'title' => esc_html__('自动播放', 'rizhuti-v2'),
        ),
    ),
));
if (!function_exists('rizhuti_v2_module_slideer_img')) {
    function rizhuti_v2_module_slideer_img($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示
        $instance = array_merge( array( 
            'diy_data' => array(),
        ), $instance);

        echo $args['before_widget'];
        $style    = isset($instance['style']) ? $instance['style'] : 'full'; //big
        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : ''; //autoplay
        ob_start(); ?>
        <?php if ($style == 'big') {echo '<div class="container mt-4 big">';}?>
        <div class="module slider img-center owl<?php echo $autoplay;?>">
        <?php foreach ($instance['diy_data'] as $item) {
            echo '<div class="slider lazyload visible" data-bg="'.esc_url( $item['_img'] ).'">';
            echo '<div class="container">';
            echo $item['_desc'];
            if (!empty($item['_href'])) {
              echo '<a'.( $item['_blank'] ? ' target="_blank"' : '' ).' class="u-permalink" href="'.esc_url( $item['_href'] ).'"></a>';
            }
            echo '</div>';
            echo '</div>';
        }?>
        </div>
        <?php if ($style == 'big') {echo '</div>';}?>
        <?php echo ob_get_clean(); echo $args['after_widget'];

    }
}



/**
 * 文章幻灯片NEW
 */
CSF::createWidget('rizhuti_v2_module_slideer_post', array(
    'title'       => esc_html__('RI-首页模块 : 文章幻灯片', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-slideer-center',
    'description' => esc_html__('Displays a 文章幻灯片，支持全宽/普通', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'      => 'style',
            'type'    => 'radio',
            'title'   => '布局风格',
            'inline'  => true,
            'options' => array(
                'full' => esc_html__('全宽', 'rizhuti-v2'),
                'big'  => esc_html__('普通', 'rizhuti-v2'),
            ),
            'default' => 'full',
        ),
        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 4,
        ),
        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
            'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
            'options'     => 'categories',
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => array(
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ),
        ),
       
        array(
            'id'    => 'autoplay',
            'type'  => 'switcher',
            'title' => esc_html__('自动播放', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_slideer_post')) {
    function rizhuti_v2_module_slideer_post($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示
        $instance = array_merge( array( 
            'style' => 'full',
            'count' => 4,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'date',
            'autoplay' => true,
        ), $instance);

        echo $args['before_widget'];
        // 查询
        $_args = array(
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );
        $PostData = new WP_Query($_args);
        $style    = isset($instance['style']) ? $instance['style'] : 'full'; //big
        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : ''; //big
        if ($style == 'full') {
            $classes = 'module slider center owl' . $autoplay;
        } else {
            $classes = 'module slider big owl nav-white' . $autoplay;
        }
        ob_start(); ?>
      <?php if ($style == 'big') {echo '<div class="container mt-4">';}?>
      <div class="<?php echo esc_attr($classes); ?>">
        <?php while ($PostData->have_posts()): $PostData->the_post();
            $bg_image = _get_post_thumbnail_url(null, 'full');?>
            <article <?php post_class('post lazyload visible');?> data-bg="<?php echo esc_url($bg_image); ?>">
              <div class="entry-wrapper">
                <header class="entry-header white">
                  <?php rizhuti_v2_entry_title(array('link' => false));?>
                </header>
                <div class="entry-footer">
                  <?php rizhuti_v2_entry_meta(array('category' => true, 'author' => true, 'comment' => true, 'date' => true, 'favnum' => true, 'views' => true, 'shop' => true));?>
                </div>
              </div>
              <a class="u-permalink" href="<?php echo esc_url(get_permalink()); ?>"></a>
            </article>
          <?php endwhile;?>
      </div>
      <?php if ($style == 'big') {echo '</div>';}?>

      <?php wp_reset_postdata(); echo ob_get_clean(); echo $args['after_widget'];

    }
}



/**
 * 文章滑块
 */
CSF::createWidget('rizhuti_v2_module_post_carousel', array(
    'title'       => esc_html__('RI-首页模块 : 文章滑块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-carousel',
    'description' => esc_html__('Displays a 文章滑块', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 4,
        ),
        array(
            'id'      => 'is_excerpt',
            'type'    => 'switcher',
            'title'   => esc_html__('显示摘要', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
            'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
            'options'     => 'categories',
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => array(
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                'views'         => esc_html__('阅读量', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ),
        ),
        
        array(
            'id'    => 'autoplay',
            'type'  => 'switcher',
            'title' => esc_html__('自动播放', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_post_carousel')) {
    function rizhuti_v2_module_post_carousel($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'is_excerpt' => true,
            'count' => 4,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'date',
            'autoplay' => true,
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // 查询
        $_args = array(
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );
        if ($instance['orderby']=='views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }

        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : '';
        $PostData = new WP_Query($_args);

        ob_start();?>
        <div class="module carousel owl<?php echo $autoplay;?>">
        <?php while ($PostData->have_posts()): $PostData->the_post();?>
            <article id="post-<?php the_ID();?>" <?php post_class('post post-grid');?>>

              <?php if (_cao('is_post_grid_price',true)) {
                    echo get_post_meta_vip_price();
              }?>
              <?php echo _get_post_media(null, 'thumbnail'); ?>
              <div class="entry-wrapper">
                <?php if (_cao('is_post_grid_category',1)) {
                    rizhuti_v2_category_dot(2);
                }?>
                <header class="entry-header">
                  <?php rizhuti_v2_entry_title(array('link' => true));?>
                </header>
                <?php if (!empty($instance['is_excerpt'])): ?>
                <div class="entry-excerpt"><?php echo rizhuti_v2_excerpt(); ?></div>
                <?php endif;?>
              <div class="entry-footer">
                <?php rizhuti_v2_entry_meta(array( 
                    'author' => _cao('is_post_grid_author',1), 
                    'category' => false,
                    'comment' => _cao('is_post_grid_comment',1),
                    'date' => _cao('is_post_grid_date',1),
                    'favnum' => _cao('is_post_grid_favnum',1),
                    'views' => _cao('is_post_grid_views',1),
                    'shop' => _cao('is_post_grid_shop',1),
                ));?>
              </div>
            </div>
        </article>
        <?php endwhile;?>
        </div>
        <?php wp_reset_postdata(); echo ob_get_clean(); echo $args['after_widget'];
    }
}




/**
 * 分类展示滑块 catbox
 */
CSF::createWidget('rizhuti_v2_module_catbox_carousel', array(
    'title'       => esc_html__('RI-首页模块 : 分类BOX滑块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-catbox-carousel',
    'description' => esc_html__('Displays a 分类BOX滑块', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => '要展示的分类',
            'desc'        => '按顺序选择可以排序',
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
        ),

        array(
            'id'    => 'autoplay',
            'type'  => 'switcher',
            'title' => esc_html__('自动播放', 'rizhuti-v2'),
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_catbox_carousel')) {
    function rizhuti_v2_module_catbox_carousel($args, $instance) {
        if (!is_page_template_modular() || empty($instance['category']) ) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'title' => '分类BOX滑块',
            'category' => 0,
            'autoplay' => true,
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        $autoplay = !empty($instance['autoplay']) ? ' autoplay' : '';

        ob_start();?>

        <div class="module catbox-carousel owl<?php echo $autoplay;?>">
        <?php foreach ( (array)$instance['category'] as $key => $cat_id ): 
            $category = get_term($cat_id,'category');
            $bg_img = get_term_meta($category->term_id, 'bg-image', true);
            $bg_img = (!empty($bg_img)) ? $bg_img : get_template_directory_uri().'/assets/img/series-bg.jpg';
            $badge = array('success','info','warning','danger','light','primary','warning','danger','success','info','warning','light','primary',);
        ?>
            <div class="lazyload visible catbox-bg" data-bg="<?php echo esc_url($bg_img); ?>">
                <a href="<?php echo get_category_link($category->term_id); ?>" class="catbox-block">
                    <div class="catbox-content">
                        <p class="mb-1"><?php echo $category->description;?></p>
                        <span class="badge badge-<?php echo $badge[$key];?>-lighten mb-1"><?php echo esc_html__('文章','rizhuti-v2');?> <?php echo $category->count;?>+</span>
                        <h3 class="catbox-title"><?php echo $category->name;?></h3>
                    </div>
                </a>
            </div>
        <?php endforeach;?>
        </div>

        <?php wp_reset_postdata(); echo ob_get_clean(); echo $args['after_widget'];
    }
}




/**
 * 分类文章展示
 */
CSF::createWidget('rizhuti_v2_module_post_item', array(
    'title'       => esc_html__('RI-首页模块 : 分类文章展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-carousel',
    'description' => esc_html__('Displays a 文章展示', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
            'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
            'options'     => 'categories',
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => array(
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                'views'         => esc_html__('阅读量', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ),
        ),

        // 分类页布局
        array(
            'id'          => 'item_style',
            'type'        => 'select',
            'title'       => '布局风格',
            'placeholder' => '',
            'options'     => array(
                'list' => esc_html__('列表', 'rizhuti-v2'),
                'grid' => esc_html__('网格', 'rizhuti-v2'),
            ),
            'default'     => 'list',
        ),
        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 4,
        ),
        array(
            'id'      => 'is_excerpt',
            'type'    => 'switcher',
            'title'   => esc_html__('显示摘要', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_post_item')) {
    function rizhuti_v2_module_post_item($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'title' => '分类文章展示',
            'item_style' => 'list',
            'is_excerpt' => true,
            'count' => 4,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'date',
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] .'<a href="'.esc_url( get_category_link( $instance['category'] ) ).'" class="float-right btn btn-outline-secondary btn-sm">'.esc_html__('查看全部','rizhuti-v2').'<i class="fa fa-angle-right ml-1"></i></a>'. $args['after_title'];
        }
        // 查询
        $_args = array(
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );

        if ($instance['orderby']=='views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }
        

        $PostData = new WP_Query($_args);
        $col_classes = ($instance['item_style'] == 'list') ? 'col-lg-6 col-12' : 'col-lg-5ths col-lg-3 col-md-4 col-6';
        ob_start();?>
        <div class="module posts-wrapper <?php echo esc_attr($instance['item_style']); ?>">
            <div class="row">
              <?php while ($PostData->have_posts()): $PostData->the_post();?>
                <div class="<?php echo esc_attr($col_classes); ?>">
                  <article id="post-<?php the_ID();?>" <?php post_class('post post-' . $instance['item_style']);?>>
                      <?php if (_cao('is_post_'.$instance['item_style'].'_price',true)) {
                          echo get_post_meta_vip_price();
                      }?>

                      <?php echo _get_post_media(null, 'thumbnail'); ?>
                      <div class="entry-wrapper">
                        <?php if (_cao('is_post_'.$instance['item_style'].'_category',1)) {
                            rizhuti_v2_category_dot(2);
                        }?>
                        <header class="entry-header">
                          <?php rizhuti_v2_entry_title(array('link' => true));?>
                        </header>
                        <?php if (!empty($instance['is_excerpt'])): ?>
                        <div class="entry-excerpt"><?php echo rizhuti_v2_excerpt(); ?></div>
                        <?php endif;?>
                      <div class="entry-footer">
                        <?php rizhuti_v2_entry_meta(array( 
                            'author' => _cao('is_post_'.$instance['item_style'].'_author',1), 
                            'category' => false,
                            'comment' => _cao('is_post_'.$instance['item_style'].'_comment',1),
                            'date' => _cao('is_post_'.$instance['item_style'].'_date',1),
                            'favnum' => _cao('is_post_'.$instance['item_style'].'_favnum',1),
                            'views' => _cao('is_post_'.$instance['item_style'].'_views',1),
                            'shop' => _cao('is_post_'.$instance['item_style'].'_shop',1),
                        ));?>
                      </div>
                    </div>
                </article>
              </div>
              <?php endwhile;?>
            </div>
        </div>
      <?php wp_reset_postdata(); echo ob_get_clean(); echo $args['after_widget'];
    }
}


/**
 * 分类CMS文章展示块
 */
CSF::createWidget('rizhuti_v2_module_cms_post', array(
    'title'       => esc_html__('RI-首页模块 : 分类CMS文章展示块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-cms',
    'description' => esc_html__('Displays a 文章展示', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'style',
            'type'    => 'select',
            'title'   => esc_html__('布局风格', 'rizhuti-v2'),
            'options' => array(
                'list'          => esc_html__('左大图-右列表', 'rizhuti-v2'),
                'grid'          => esc_html__('左大图-右网格', 'rizhuti-v2'),
            ),
            'default' => 'list',
        ),
        array(
            'id'      => 'is_box_right',
            'type'    => 'switcher',
            'title'   => esc_html__('大图右侧显示', 'rizhuti-v2'),
            'default' => false,
        ),
       
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
            'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
            'options'     => 'categories',
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => array(
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                'views'         => esc_html__('阅读量', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ),
        ),
       
        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_cms_post')) {
    function rizhuti_v2_module_cms_post($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'title' => '分类CMS文章展示块',
            'style' => 'list',
            'is_box_right' => false,
            'offset' => 0,
            'category' => 0,
            'orderby' => 'date',
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // 查询
        $_args = array(
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => 5,
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );
        
        if ($instance['orderby']=='views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }

        $PostData = new WP_Query($_args);
        $i = 0;
        $style = $instance['style'];
        $style_order = (!empty($instance['is_box_right'])) ? ' order-first' : '' ;

        ob_start();?>

        <div class="module posts-wrapper post-cms">
            <div class="row">
              <?php while ($PostData->have_posts()): $PostData->the_post();
                $i++;?>
                  <?php if ($i == 1): ?>
                    <div class="col-lg-6 col-sm-12">
                      <div class="cms_grid_box lazyload" data-bg="<?php echo _get_post_thumbnail_url(null, 'full'); ?>">
                        <article id="post-<?php the_ID();?>" <?php post_class('post');?>>
                            <div class="entry-wrapper">
                              <header class="entry-header">
                                <?php rizhuti_v2_entry_title(array('link' => true));?>
                              </header>
                              <div class="entry-footer">
                                <?php rizhuti_v2_entry_meta(array('category' => true, 'author' => true, 'views' => true, 'date' => true));?>
                              </div>
                            </div>
                        </article>
                      </div>
                    </div>

                    <div class="col-lg-6 col-sm-12<?php echo esc_attr($style_order);?>">
                      <div class="cms_grid_list">
                        <?php if ($style=='grid'){echo '<div class="row">';} ?>
                  <?php else: ?>
                        <?php if ($style=='grid'){echo '<div class="col-6">';} ?>
                        <article id="post-<?php the_ID();?>" <?php post_class('post post-'.$style);?>>

                            <?php echo _get_post_media(null, 'thumbnail'); ?>
                            <div class="entry-wrapper">
                              <header class="entry-header">
                                <?php rizhuti_v2_entry_title(array('link' => true));?>
                              </header>
                              <div class="entry-footer">
                                <?php rizhuti_v2_entry_meta(array( 
                                    'author' => _cao('is_post_'.$style.'_author',1), 
                                    'category' => _cao('is_post_'.$style.'_category',1),
                                    'comment' => _cao('is_post_'.$style.'_comment',1),
                                    'date' => _cao('is_post_'.$style.'_date',1),
                                    'favnum' => _cao('is_post_'.$style.'_favnum',1),
                                    'views' => _cao('is_post_'.$style.'_views',1),
                                    'shop' => _cao('is_post_'.$style.'_shop',1),
                                ));?>
                              </div>
                            </div>
                        </article>
                        <?php if ($style=='grid'){echo '</div>';} ?>
                <?php endif;?>
              <?php endwhile;?></div><?php if ($style='grid'){echo '</div>';} ?>

            </div>
        </div>
    <?php wp_reset_postdata(); echo ob_get_clean(); echo $args['after_widget'];

    }
}



/**
 * 标题文章展示模块
 */
CSF::createWidget('rizhuti_v2_module_cms_title', array(
    'title'       => esc_html__('RI-首页模块 : 纯标题文章列表块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post-cms',
    'description' => esc_html__('Displays a 文章展示', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 6,
        ),
        array(
            'id'         => 'category_data',
            'type'       => 'group',
            'title'      => '新建',
            'fields'     => array(

                array(
                    'id'      => 'note',
                    'type'    => 'text',
                    'title'   => esc_html__('角标文字', 'rizhuti-v2'),
                    'default' => '热门',
                    'desc' => '不填写则不显示',
                ),

                array(
                    'id'          => 'category',
                    'type'        => 'select',
                    'title'       => esc_html__('选择分类', 'rizhuti-v2'),
                    'placeholder' => esc_html__('选择分类', 'rizhuti-v2'),
                    'options'     => 'categories',
                ),
                array(
                    'id'      => 'orderby',
                    'type'    => 'select',
                    'title'   => esc_html__('排序方式', 'rizhuti-v2'),
                    'options' => array(
                        'date'          => esc_html__('日期', 'rizhuti-v2'),
                        'rand'          => esc_html__('随机', 'rizhuti-v2'),
                        'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                        'views'          => esc_html__('阅读量', 'rizhuti-v2'),
                        'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                        'title'         => esc_html__('标题', 'rizhuti-v2'),
                        'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
                    ),
                ),
                array(
                    'id'      => 'offset',
                    'type'    => 'text',
                    'title'   => esc_html__('第几页', 'rizhuti-v2'),
                    'default' => 0,
                ),
            ),
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_cms_title')) {
    function rizhuti_v2_module_cms_title($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'title' => '标题文章展示块',
            'category_data' => array(),
            'count' => 6,
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        
        
        ob_start();?>

        <div class="module posts-wrapper post-cms-title-list">
            <div class="row">

                <?php foreach ( $instance['category_data'] as $item ){
                    $category = $item['category'];
                    if (empty($category)) {
                        continue;
                    }

                    $category = get_term_by('ID', $category,'category');

                    $meta_image = get_term_meta($category->term_id, 'bg-image', true);


                    // 查询
                    $_args = array(
                        'cat'                 => $category->term_id,
                        'ignore_sticky_posts' => true,
                        'post_status'         => 'publish',
                        'posts_per_page'      => (int)$instance['count'],
                        'paged'              => (int)$item['offset'],
                        'orderby'             => $item['orderby'],
                    );

                    if ($item['orderby'] == 'views') {
                        $_args['meta_key'] = '_views';
                        $_args['orderby'] = 'meta_value_num';
                        $_args['order'] = 'DESC';
                    }

                    $i = 0;
                    
                    $PostData = new WP_Query($_args);

                    echo '<div class="col-lg-4 col-12">';
                    echo '<div class="card mb-4">';

                    echo '<div class="card-header text-center bg-dark lazyload visible"  data-bg="'.esc_url( $meta_image ).'">';
                    echo '<a href="'.esc_url( get_term_link( $category->term_id ) ).'" rel="category">'.$category->name.'<span class="badge badge-danger ml-2"></span></a>';
                    if (!empty($item['note'])) {
                        echo '<span class="note">'.$item['note'].'</span>';
                    }
                    echo '</div>';

                    echo '<ul class="list-group list-group-flush mt-0 mb-2">';

                    while ($PostData->have_posts()): $PostData->the_post(); $i++;

                    echo '<li class="list-group-item py-2 text-nowrap-ellipsis position-relative">';
                    echo '<a href="' . esc_url( get_permalink() ) . '" title="' . get_the_title() . '" rel="bookmark"><span class="badge badge-danger mr-1">'.$i.'</span>' . get_the_title() . '</a>';
                    if (true) {
                        echo '<span class="meta-views"><i class="fa fa-eye"></i> '._get_post_views().'</span>';
                    }
                    echo '</li>';
                    endwhile;
                    wp_reset_postdata();

                    echo '</ul>';
                    echo '</div>';
                    echo '</div>';

                }?>


              
            </div>
        </div>
    <?php wp_reset_postdata(); echo ob_get_clean(); echo $args['after_widget'];

    }
}



/**
 * 横条小块介绍模块
 */
CSF::createWidget('rizhuti_v2_module_division', array(
    'title'       => esc_html__('RI-首页模块 : 横条小块介绍模块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-division',
    'description' => esc_html__('Displays a 首页模块', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'      => 'is_rounded',
            'type'    => 'switcher',
            'title'   => esc_html__('是否圆形图标', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'         => 'div_data',
            'type'       => 'group',
            'title'      => '新建',
            'fields'     => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => esc_html__('标题文字', 'rizhuti-v2'),
                    'default' => '标题文字',
                ),
                array(
                    'id'         => 'icon',
                    'type'       => 'icon',
                    'title'      => esc_html__('图标', 'rizhuti-v2'),
                    'desc'       => '设置站内币图标，部分页面展示需要',
                    'default'    => 'fab fa-buffer',
                ),
                array(
                    'id'      => 'desc',
                    'type'    => 'text',
                    'title'   => esc_html__('描述内容', 'rizhuti-v2'),
                    'default' => '这里是描述内容介绍',
                ),
                array(
                    'id'      => 'link',
                    'type'    => 'text',
                    'title'   => esc_html__('链接', 'rizhuti-v2'),
                    'desc'   => esc_html__('不填写则不启用链接', 'rizhuti-v2'),
                    'default' => '',
                ),

            ),
        ),

    ),
));
if (!function_exists('rizhuti_v2_module_division')) {
    function rizhuti_v2_module_division($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'div_data' => array(),
            'is_rounded' => true,
        ), $instance);
        
        echo $args['before_widget'];

        $col_classes = ( count($instance['div_data'])>3 ) ? 'col-xl-3' : 'col-xl-4' ;
        $rounded_classes = ( empty($instance['is_rounded']) ) ? '' : ' rounded-circle' ;
        ob_start();?>

        <div class="module division">
            <div class="container">
                <div class="row align-items-center no-gutters">
        
                <?php foreach ( $instance['div_data'] as $item ){ ?>
                   <div class="<?php echo $col_classes;?> col-lg-4 col-6 mb-4">
                    <?php if ( !empty($item['link']) ) { echo '<a href="'.$item['link'].'" title="'.$item['title'].'">'; }?>
                      <div class="d-flex align-items-center hover-rounded">
                        <span class="icon-sahpe text-center<?php echo $rounded_classes;?>"> <i class="<?php echo $item['icon'];?>"> </i></span>
                        <div class="ml-3">
                          <h4 class="title mb-0"><?php echo $item['title'];?></h4>
                          <p class="desc mb-0"><?php echo $item['desc'];?></p>
                        </div>
                      </div>
                    <?php if ( !empty($item['link']) ) { echo '</a>'; }?>
                    </div>
                <?php } ?>

                </div>
            </div>
        </div>

    <?php echo ob_get_clean(); echo $args['after_widget'];

    }
}



/**
 * 按钮小工具
 */
CSF::createWidget('rizhuti_v2_widget_btns', array(
    'title'       => 'Ri-按钮链接',
    'classname'   => 'rizhuti-v2-widget-btns',
    'description' => 'Ri主题的小工具',
    'fields'      => array(
        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => '外部推荐',
        ),
        array(
            'id'                     => 'btns',
            'type'                   => 'group',
            'title'                  => '按钮-链接',
            'desc'                   => '支持多个',
            'accordion_title_number' => true,
            'fields'                 => array(
                array(
                    'id'      => 'name',
                    'type'    => 'textarea',
                    'title'   => '标题',
                    'default' => '按钮',
                ),
                array(
                    'id'      => 'size',
                    'type'    => 'select',
                    'title'   => '按钮大小',
                    'options' => array(
                        'btn-no' => '常规',
                        'btn-sm' => '小',
                        'btn-lg' => '大',
                    ),
                    'default' => 'btn-sm',
                ),
                array(
                    'id'      => 'type',
                    'type'    => 'select',
                    'title'   => '按钮颜色',
                    'options' => array(
                        'btn-primary'   => '蓝色',
                        'btn-success'   => '绿色',
                        'btn-danger'    => '红色',
                        'btn-warning'   => '黄色',
                        'btn-secondary' => '灰色',
                        'btn-dark'      => '黑色',
                        'btn-light'      => '亮色',
                    ),
                ),
                array(
                    'id'      => 'href',
                    'type'    => 'text',
                    'title'   => '按钮-链接',
                    'default' => '#',
                ),
                
            ),
            'default'                => array(
                array(
                    'type' => 'btn-light',
                    'href' => 'https://ritheme.com/',
                    'name' => 'WordPresss主题推荐',
                    'size' => 'btn-sm',
                ),
                array(
                    'type' => 'btn-light',
                    'href' => 'https://www.aliyun.com/minisite/goods?userCode=u4kxbrjo',
                    'name' => '阿里云服务器推荐',
                    'size' => 'btn-sm',
                ),
                array(
                    'type' => 'btn-light',
                    'href' => '#',
                    'name' => '关于本站',
                    'size' => 'btn-sm',
                ),
                
            ),
        ),

    ),
));
if (!function_exists('rizhuti_v2_widget_btns')) {
    function rizhuti_v2_widget_btns($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $instance = array_merge( array( 
            'btns' => array(),
        ), $instance);


        // start
        $btns  = $instance['btns'];
        foreach ($btns as $key => $btn) {
            echo '<a target="_blank" class="btn ' . $btn['type'] . ' btn-block ' . $btn['size'] . '" href="' . $btn['href'] . '" rel="nofollow noopener noreferrer">' . $btn['name'] . '</a>';
        }
        // end
        echo $args['after_widget'];
    }
}



/**
 * 会员介绍小工具
 */
CSF::createWidget('rizhuti_v2_module_vip_price', array(
    'title'       => 'RI-首页模块 : 会员价格介绍模块',
    'classname'   => 'rizhuti-v2-module-vip-price',
    'description' => 'Ri主题的小工具',
    'fields'      => array(
        array(
            'id'    => 'image',
            'type'  => 'upload',
            'title' => esc_html__('背景图', 'rizhuti-v2'),
            'default' => get_template_directory_uri() . '/assets/img/top-bg.jpg',
        ),
        array(
            'id'      => 'title',
            'type'    => 'textarea',
            'sanitize'   => false,
            'title'   => '标题',
            'default' => '<i class="fa fa-diamond text-info"></i> 加入本站VIP会员，海量资源免费下载查看',
        ),
        array(
            'id'      => 'desc',
            'type'    => 'textarea',
            'sanitize'   => false,
            'title'   => '描述介绍',
            'default' => '本站已有 <span class="badge badge-pill badge-info">1388</span> 位优秀的VIP会员加入',
        ),
    ),
));
if (!function_exists('rizhuti_v2_module_vip_price')) {
    function rizhuti_v2_module_vip_price($args, $instance) {
        if (!is_page_template_modular() || is_close_site_shop()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'image' => get_template_directory_uri() . '/assets/img/top-bg.jpg',
            'title' => '<i class="fa fa-diamond text-info"></i> 加入本站VIP会员，海量资源免费下载查看',
            'desc' => '本站已有 <span class="badge badge-pill badge-info">1388</span> 位优秀的VIP会员加入',
        ), $instance);


        echo $args['before_widget'];

        $vip_options = _get_ri_vip_options();
        unset($vip_options[0]);
        $vip_options['31']['css'] = 'success';
        $vip_options['365']['css'] = 'danger';
        $vip_options['3600']['css'] = 'warning';
        $vip_options['31']['day_text'] = esc_html__('31天','rizhuti-v2');
        $vip_options['365']['day_text'] = esc_html__('1年','rizhuti-v2');
        $vip_options['3600']['day_text'] = esc_html__('永久','rizhuti-v2');

        global $ri_vip_options;
        $vip_options['31']['tq_text'] = $ri_vip_options['31'];
        $vip_options['365']['tq_text'] = $ri_vip_options['31'].'/'.$ri_vip_options['365'];
        $vip_options['3600']['tq_text'] = $ri_vip_options['31'].'/'.$ri_vip_options['365'].'/'.$ri_vip_options['3600'];


        ob_start();?>
        <div class="module parallax">
        <?php if (!empty($instance['image'])): ?>
          <img class="jarallax-img lazyload" data-src="<?php echo esc_url($instance['image']); ?>" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="<?php echo esc_attr($instance['title']); ?>">
        <?php endif;?>
        <div class="container position-relative">
            <div class="row mb-5 justify-content-center text-center">
                <div class="col-lg-7 col-md-9">
                    <h3 class="h4 text-white"><?php echo $instance['title'];?></h3>
                    <p class="text-white"><?php echo $instance['desc'];?></p>
                </div>
            </div>
            <div class="row justify-content-center">
                <?php foreach ($vip_options as $key => $item): ?>
                <div class="col-lg-4">
                    <div class="card card-pricing card-hover text-center px-3 mb-4 mb-lg-0 text-<?php echo $item['css'];?>">
                        <div class="card-header py-5 border-0 delimiter-bottom position-relative">
                            <div class="h1 text-center mb-0 ">
                                <span class="font-weight-bolder">￥<?php echo $item['price'];?></span>
                            </div>
                            <span class="h6"><i class="fa fa-diamond"></i> <?php echo $item['name'];?> / <?php echo $item['day_text'];?></span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled text-sm m-0 text-muted">
                                <?php 
                                printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
                                printf('<li>平均每天仅需 %s元</li>', round($item['price']/$item['day'],2) );
                                printf('<li>享受%s所有特权</li>', $item['tq_text'] );
                                if ( empty($item['download_rate']) ) {
                                    printf('<li>全站文件下载速度不限速</li>');
                                }else{
                                    printf('<li>文件下载速度最高可达 %s KB/秒</li>',$item['download_rate']);
                                }
                                if ( _cao('is_site_aff') ) {
                                    printf('<li>用户推广奖励佣金比例高达 %s%%</li>', round($item['aff_ratio']*100,1) );
                                }?>
                            </ul>
                            
                        </div>
                        <div class="card-footer">
                            <a class="btn btn-block btn-rounded m-0 btn-<?php echo $item['css'];?>" href="<?php echo get_user_page_url('vip'); ?>"><i class="fa fa-diamond"></i> 升级<?php echo $item['name'];?>会员</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        </div>
        <?php echo ob_get_clean(); echo $args['after_widget'];
    }
}



/**
 * 专题文章CMS展示块
 */
CSF::createWidget('rizhuti_v2_module_cms_list', array(
    'title'       => esc_html__('RI-首页模块 : 专题文章CMS展示块', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-list-cms',
    'description' => esc_html__('Displays a 文章展示', 'rizhuti-v2'),
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 2,
        ),
        array(
            'id'         => 'category_data',
            'type'       => 'group',
            'title'      => '新建',
            'fields'     => array(
                array(
                    'id'          => 'category',
                    'type'        => 'select',
                    'title'       => esc_html__('选择专题', 'rizhuti-v2'),
                    'placeholder' => esc_html__('选择专题', 'rizhuti-v2'),
                    'options'     => 'categories',
                    'query_args'  => array(
                        'taxonomy'  => 'series',
                    ),
                ),
                array(
                    'id'      => 'orderby',
                    'type'    => 'select',
                    'title'   => esc_html__('排序方式', 'rizhuti-v2'),
                    'options' => array(
                        'date'          => esc_html__('日期', 'rizhuti-v2'),
                        'rand'          => esc_html__('随机', 'rizhuti-v2'),
                        'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                        'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                        'title'         => esc_html__('标题', 'rizhuti-v2'),
                        'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
                    ),
                ),
                array(
                    'id'      => 'offset',
                    'type'    => 'text',
                    'title'   => esc_html__('第几页', 'rizhuti-v2'),
                    'default' => 0,
                ),
            ),
        ),

    ),
));

if (!function_exists('rizhuti_v2_module_cms_list')) {
    function rizhuti_v2_module_cms_list($args, $instance) {
        if (!is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'title' => '专题文章CMS展示块',
            'category_data' => array(),
            'count' => 2,
        ), $instance);

        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        
        ob_start();?>

        <div class="module posts-wrapper post-cms-title">
            <div class="row">
            <?php foreach ( $instance['category_data'] as $item ){
                $category = get_term_by('ID', $item['category'],'series');

                if (empty($category)) {
                    continue;
                }

                // 查询
                $tax_query = array('relation' => 'OR',array(
                    'taxonomy' => 'series',
                    'field'    => 'term_id',
                    'terms' => $item['category'],
                    'operator' => 'IN'
                ));
                $_args = array(
                    'tax_query' => $tax_query,
                    'ignore_sticky_posts' => true,
                    'post_status'         => 'publish',
                    'posts_per_page'      => (int)$instance['count'],
                    'paged'              => (int)$item['offset'],
                    'orderby'             => $item['orderby'],
                );
                $PostData = new WP_Query($_args);
                $i = 0;
                echo '<div class="col-lg-4 col-12"><div class="card mb-4">';
                echo '<div class="cat-info">';
                echo '<span class="text-light">'.sprintf(__('共%s个文章', 'rizhuti-v2'),$category->count).'</span>';
                echo '<h3><a href="'.esc_url( get_term_link( $category->term_id ) ).'" rel="category">'.$category->name.'<span class="badge badge-danger ml-2"></span></a></h3>';
                echo '</div>';
                while ($PostData->have_posts()): $PostData->the_post(); $i++;
                if ($i==1) {
                    echo _get_post_media(null, 'thumbnail');
                    echo '<div class="card-body">';
                    rizhuti_v2_entry_title(array('link' => true,'tag'=>'h5','classes'=>'card-title text-nowrap-ellipsis'));
                    echo '<p class="card-text text-muted small text-nowrap-ellipsis">'.rizhuti_v2_excerpt().'</p>';
                    echo '</div>';
                    echo '<ul class="list-group list-group-flush mt-0 mb-2">';
                }else{
                    echo '<li class="list-group-item py-2 text-nowrap-ellipsis"><a href="' . esc_url( get_permalink() ) . '" title="' . get_the_title() . '" rel="bookmark">' . get_the_title() . '</a></li>';
                }
                endwhile;
                wp_reset_postdata();
                echo '<li class="list-group-item py-2 text-nowrap-ellipsis"><a href="'.esc_url( get_term_link( $category->term_id ) ).'" rel="category" class="btn btn-light btn-block">'.esc_html__('查看专题','rizhuti-v2').'<i class="fa fa-angle-right ml-1"></i></a></li>';
                echo '</ul></div></div>';
            }?>
            </div>
        </div>
        <?php echo ob_get_clean(); echo $args['after_widget'];

    }
}



/**
 * 侧边栏分类文章展示
 */
CSF::createWidget('rizhuti_v2_widget_post_item', array(
    'title'       => esc_html__('RI-文章侧边栏 : 文章展示', 'rizhuti-v2'),
    'classname'   => 'rizhuti_v2-widget-post',
    'description' => esc_html__('Displays a 文章展示', 'rizhuti-v2'),
    'fields'      => array(
        array(
            'id'    => 'title',
            'type'  => 'text',
            'title' => esc_html__('标题', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'is_media',
            'type'    => 'switcher',
            'title'   => esc_html__('显示缩略图', 'rizhuti-v2'),
            'default' => true,
        ),
        array(
            'id'      => 'is_one_maxbg',
            'type'    => 'switcher',
            'title'   => esc_html__('第一篇文章大图', 'rizhuti-v2'),
            'default' => false,
        ),
        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => esc_html__('只显示分类下', 'rizhuti-v2'),
            'placeholder' => esc_html__('最新文章', 'rizhuti-v2'),
            'options'     => 'categories',
            'desc'     => esc_html__('不选择分类则显示为最新文章，建议没有特殊需求选择其他分类文章展示，有利于seo爬虫爬取', 'rizhuti-v2'),
        ),
        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => esc_html__('排序方式', 'rizhuti-v2'),
            'options' => array(
                'date'          => esc_html__('日期', 'rizhuti-v2'),
                'rand'          => esc_html__('随机', 'rizhuti-v2'),
                'comment_count' => esc_html__('评论数', 'rizhuti-v2'),
                'views'         => esc_html__('阅读量', 'rizhuti-v2'),
                'modified'      => esc_html__('最近编辑时间', 'rizhuti-v2'),
                'title'         => esc_html__('标题', 'rizhuti-v2'),
                'ID'            => esc_html__('文章ID', 'rizhuti-v2'),
            ),
        ),
       
        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => esc_html__('文章数量', 'rizhuti-v2'),
            'default' => 4,
        ),
        array(
            'id'      => 'offset',
            'type'    => 'text',
            'title'   => esc_html__('第几页', 'rizhuti-v2'),
            'default' => 0,
        ),

    ),
));
if (!function_exists('rizhuti_v2_widget_post_item')) {
    function rizhuti_v2_widget_post_item($args, $instance) {
        if (is_page_template_modular()) {return false;} //非模块页面不显示

        $instance = array_merge( array( 
            'is_media' => true,
            'is_one_maxbg' => false,
            'category' => 0,
            'orderby' => 'date',
            'count' => 4,
            'offset' => 0,
        ), $instance);


        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // 查询
        $_args = array(
            'cat'                 => (int)$instance['category'],
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'posts_per_page'      => (int)$instance['count'],
            'paged'              => (int)$instance['offset'],
            'orderby'             => $instance['orderby'],
        );
        if ($instance['orderby']=='views') {
            $_args['meta_key'] = '_views';
            $_args['orderby'] = 'meta_value_num';
            $_args['order'] = 'DESC';
        }
        
        $PostData = new WP_Query($_args);
        $i = 0;
        
        ob_start();?>
        <div class="posts-wrapper list"> 
              <?php while ($PostData->have_posts()): $PostData->the_post();
                $i++; 
                $maxbg = ( $i==1 && !empty($instance['is_one_maxbg']) ) ? ' maxbg' : '' ;
              ?>
                  <article id="post-<?php the_ID();?>" <?php post_class('post post-list' . $maxbg);?>>
                      <?php if (!empty($instance['is_media'])) {
                        echo _get_post_media(null, 'thumbnail');
                      }?>
                      <div class="entry-wrapper">
                        <header class="entry-header">
                          <?php rizhuti_v2_entry_title(array('link' => true));?>
                        </header>
                    </div>
                </article>
              <?php endwhile;?>
        </div>
        <?php wp_reset_postdata(); echo ob_get_clean(); echo $args['after_widget'];
    }
}

