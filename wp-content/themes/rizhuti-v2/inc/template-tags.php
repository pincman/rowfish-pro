<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;


/**
 * 是否首页模块化页面
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:54:03+0800
 * @return   boolean                  [description]
 */
function is_page_template_modular()
{
    $modular_arr = apply_filters('page_template_modular_php', array('pages/page-modular.php'));
    foreach ($modular_arr as $slug) {
        if (is_page_template($slug)) return true;
    }
    return false;
}


/**
 * js控制台输出php调试信息
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:46:10+0800
 * @param    [type]                   $data [description]
 * @return   [type]                         [description]
 */
function php_logger($data)
{
    $output = $data;
    if (is_array($output)) {
        $output = implode(',', $output);
    }
    // print the result into the JavaScript console
    echo "<script>console.log( 'PHP LOG: " . $output . "' );</script>";
}



/**
 * 注册 wp_body_open
 * @Author   Dadong2g
 * @DateTime 2021-01-30T21:15:47+0800
 * @return   [type]                   [description]
 */
if (!function_exists('wp_body_open')) {
    function wp_body_open()
    {
        /**
         * Triggered after the opening <body> tag.
         */
        do_action('wp_body_open');
    }
}


/**
 * 是否启用评论功能
 * @Author   Dadong2g
 * @DateTime 2021-04-02T20:02:21+0800
 * @return   boolean                  [description]
 */
function is_site_comments()
{
    return _cao('is_site_comments');
}




/**
 * 是否启用问答社区
 * @Author   Dadong2g
 * @DateTime 2021-04-02T20:02:12+0800
 * @return   boolean                  [description]
 */
function is_site_question()
{
    return _cao('is_site_question');
}






/**
 * 全站弹窗提示信息
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:46:20+0800
 * @param    string                   $title     [description]
 * @param    string                   $msg       [description]
 * @param    string                   $back_link [description]
 * @return   [type]                              [description]
 */
function riplus_wp_die($title = '', $msg = '', $back_link = '')
{
    ob_start(); ?>
    <!doctype html>
    <html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="https://gmpg.org/xfn/11"><?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <script type="text/javascript">
            jQuery(function() {
                Swal.fire({
                    title: '<?php echo $title; ?>',
                    html: '<?php echo $msg; ?>',
                    icon: "warning",
                    allowOutsideClick: !1
                }).then(e => {
                    e.isConfirmed && (window.location.href = document.referrer)
                })
            });
        </script>
        <?php wp_footer(); ?>
    </body>

    </html>
    <?php echo ob_get_clean();
    exit;
}

/**
 * 是否GIF格式
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:46:27+0800
 * @param    [type]                   $url [description]
 * @return   [type]                        [description]
 */
function _img_is_gif($url)
{
    if (!empty($url)) {
        $path_parts = pathinfo($url);
        $extension  = $path_parts['extension'];
        return $extension == 'gif' ? true : false;
    }
    return false;
}

/**
 * 默认缩略图
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:46:33+0800
 * @return   [type]                   [description]
 */
function _the_theme_thumb()
{
    return _cao('default_thumb') ? _cao('default_thumb') : get_template_directory_uri() . '/assets/img/thumb.jpg';
}


/**
 * 默认头像
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:46:40+0800
 * @return   [type]                   [description]
 */
function _the_theme_avatar()
{
    return get_template_directory_uri() . '/assets/img/avatar.png';
}

/**
 * 默认主题名称
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:46:57+0800
 * @return   [type]                   [description]
 */
function _the_theme_name()
{
    $current_theme = wp_get_theme();
    return $current_theme->get('Name');
}


/**
 * 默认主题版本
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:47:11+0800
 * @return   [type]                   [description]
 */
function _the_theme_version()
{
    $current_theme = wp_get_theme();
    return $current_theme->get('Version');
}


/**
 * 主题地址
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:47:20+0800
 * @return   [type]                   [description]
 */
function _the_theme_aurl()
{
    $current_theme = wp_get_theme();
    return $current_theme->get('ThemeURI');
}




/**
 * 输出缩略图地址
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:47:20+0800
 * @return   [type]                   [description]
 */
if (!function_exists('_get_post_thumbnail_url')) {
    function _get_post_thumbnail_url($post = null, $size = 'thumbnail')
    {
        if (empty($post)) {
            global $post;
        }

        if (is_numeric($post)) {
            $post = get_post($post);
        }

        if (empty($post)) {
            return _the_theme_thumb();
        }

        if (has_post_thumbnail($post)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

            if (empty($thumbnail_src) || empty($thumbnail_src[0])) {
                return _the_theme_thumb();
            }

            if (!_img_is_gif($thumbnail_src[0])) {
                $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);
            }

            $post_thumbnail_src = $thumbnail_src[0];
        } elseif (_cao('is_post_one_thumbnail', true) && !empty($post->post_content)) {
            $post_thumbnail_src = '';
            $output             = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            $post_thumbnail_src = (!empty($matches[1][0])) ? $matches[1][0] : null; //获取该图片 src
            if (empty($post_thumbnail_src)) {
                $post_thumbnail_src = _the_theme_thumb(); //如果日志中没有图片，则显示默认图片
            }
        } else {
            $post_thumbnail_src = _the_theme_thumb(); //如果日志中没有图片，则显示默认图片
        }

        //云储存适配
        if ($size == 'thumbnail' && _cao('is_img_cloud_storage', false)) {
            // 判断裁剪模式
            $storage_domain = _cao('img_cloud_storage_domain');
            $storage_param = _cao('img_cloud_storage_param');
            if (strpos($post_thumbnail_src, $storage_domain) !== false) {
                $post_thumbnail_src = $post_thumbnail_src . $storage_param;
            }
        }

        return $post_thumbnail_src;
    }
}


/**
 * 获取商品类型文章缩略图
 * @Author   Dadong2g
 * @DateTime 2021-04-05T18:43:11+0800
 * @param    [type]                   $post [description]
 * @param    string                   $size [description]
 * @return   [type]                         [description]
 */
if (!function_exists('_get_post_shop_thumbnail_url')) {

    function _get_post_shop_thumbnail_url($post = null, $size = 'thumbnail')
    {
        if (empty($post)) {
            global $post;
        }

        if (is_numeric($post)) {
            $post = get_post($post);
        }

        if (empty($post)) {
            return false;
        }

        if (has_post_thumbnail($post)) {

            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);

            if (empty($thumbnail_src) || empty($thumbnail_src[0])) {
                return false;
            }

            if (!_img_is_gif($thumbnail_src[0])) {
                $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);
            } else {
                $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            }

            $post_thumbnail_src = $thumbnail_src[0];
        } elseif (_cao('is_post_one_thumbnail', true) && !empty($post->post_content)) {
            $post_thumbnail_src = '';
            $output             = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
            $post_thumbnail_src = (!empty($matches[1][0])) ? $matches[1][0] : null; //获取该图片 src
            if (empty($post_thumbnail_src)) {
                $post_thumbnail_src = _the_theme_thumb(); //如果日志中没有图片，则显示默认图片
            }
        } else {
            return false;
        }

        //云储存适配
        if ($size == 'thumbnail' && _cao('is_img_cloud_storage', false)) {
            // 判断裁剪模式
            $storage_domain = _cao('img_cloud_storage_domain');
            $storage_param = _cao('img_cloud_storage_param');
            if (strpos($post_thumbnail_src, $storage_domain) !== false) {
                $post_thumbnail_src = $post_thumbnail_src . $storage_param;
            }
        }

        return $post_thumbnail_src;
    }
}


/**
 * 根据模式输出缩略图img 延迟加载html标签
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:47:20+0800
 * @return   [type]                   [description]
 */
if (!function_exists('_get_post_media')) {
    function _get_post_media($post = null, $size = 'thumbnail')
    {
        if (empty($post)) {
            global $post;
        }
        $_size_px = _cao('post_thumbnail_size');
        if (empty($_size_px)) {
            $_size_px['width']  = 300;
            $_size_px['height'] = 200;
        }
        $src = _get_post_thumbnail_url($post, $size);

        //获取比例
        $ratio = $_size_px['height'] / $_size_px['width'] * 100 . '%';

        $img   = '<div class="entry-media">';
        $img .= '<div class="placeholder" style="padding-bottom: ' . esc_attr($ratio) . '">';
        $img .= '<a href="' . get_permalink($post->ID) . '" title="' . get_the_title($post->ID) . '" rel="nofollow noopener noreferrer">';
        $img .= '<img class="lazyload" data-src="' . $src . '" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="' . get_the_title() . '" />';

        $img .= '</a>';
        $img .= '</div>';
        $img .= '</div>';
        return $img;
    }
}


/**
 * 获取文章标题
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:48:23+0800
 * @param    array                    $options [description]
 * @return   [type]                            [description]
 */
if (!function_exists('rizhuti_v2_entry_title')) {
    function rizhuti_v2_entry_title($options = array())
    {
        $options = array_merge(array(
            'outside_loop' => false,
            'classes' => 'entry-title',
            'tag' => 'h2',
            'link' => true,
        ), $options);

        $post_id = $options['outside_loop'] ? get_queried_object() : get_the_ID();
        if ($options['link']) {
            echo '<' . $options['tag'] . ' class="' . esc_attr($options['classes']) . '"><a href="' . esc_url(get_permalink($post_id)) . '" title="' . get_the_title($post_id) . '" rel="bookmark">' . get_the_title($post_id) . '</a></' . $options['tag'] . '>';
        } else {
            echo '<' . $options['tag'] . ' class="' . esc_attr($options['classes']) . '">' . get_the_title($post_id) . '</' . $options['tag'] . '>';
        }
    }
}


/**
 * 获取meta字段
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:48:34+0800
 * @param    array                    $options [description]
 * @return   [type]                            [description]
 */
if (!function_exists('rizhuti_v2_entry_meta')) {
    function rizhuti_v2_entry_meta($opt = array())
    {
        $options = array_merge(array(
            'outside_loop' => false,
            'author' => false,
            'category' => false,
            'date' => false,
            'comment' => false,
            'favnum' => false,
            'views' => false,
            'shop' => true,
            'edit' => false,
        ), $opt);

        $post_id = $options['outside_loop'] ? get_queried_object() : get_the_ID();

        if (in_array(true, $options)) : ?>
            <div class="entry-meta">

                <?php if ($options['author']) :
                    $author_id = (int)get_post_field('post_author', $post_id); ?>
                    <span class="meta-author">
                        <a href="<?php echo esc_url(get_author_posts_url($author_id, get_the_author_meta('display_name', $author_id))); ?>"><?php
                                                                                                                                            echo get_avatar($author_id);
                                                                                                                                            echo get_the_author_meta('display_name', $author_id);
                                                                                                                                            ?>
                        </a>
                    </span>
                <?php endif;

                //分类信息
                if ($options['category'] && $categories = get_the_category($post_id)) : ?>
                    <span class="meta-category">
                        <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" rel="category"><?php echo esc_html($categories[0]->name); ?></a>
                    </span>
                <?php endif;

                //时间日期
                if ($options['date']) : ?>
                    <span class="meta-date">
                        <a href="<?php echo esc_url(get_the_permalink($post_id)); ?>" rel="nofollow">
                            <time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>">
                                <i class="fa fa-clock-o"></i>
                                <?php
                                if (_cao('is_post_list_date_diff', true)) {
                                    echo sprintf(__('%s前', 'rizhuti-v2'), human_time_diff(get_the_time('U', $post_id), current_time('timestamp')));
                                } else {
                                    echo esc_html(get_the_date(null, $post_id));
                                }
                                ?>
                            </time>
                        </a>
                    </span>
                <?php endif;


                if ($options['comment'] && !post_password_required($post_id) && (comments_open($post_id) || get_comments_number($post_id))) : ?>
                    <span class="meta-comment">
                        <a href="<?php echo esc_url(get_the_permalink($post_id) . '#comments'); ?>">
                            <i class="fa fa-comments-o"></i>
                            <?php printf(_n('%s', esc_html(get_comments_number($post_id)), 'rizhuti-v2')); ?>
                        </a>
                    </span>
                <?php endif;

                if ($options['favnum']) : ?>
                    <span class="meta-favnum"><i class="far fa-star"></i> <?php echo _get_post_fav($post_id); ?></span>
                <?php endif;

                if ($options['views']) : ?>
                    <span class="meta-views"><i class="fa fa-eye"></i> <?php echo _get_post_views($post_id); ?></span>
                <?php endif;

                //付费文章类型
                if ($options['shop'] && !is_close_site_shop() && $shop_type = _get_post_shop_type()) :
                    $_icon = array(
                        '3' => 'fas fa-download',
                        '4' => 'fas fa-download',
                        '2' => 'fas fa-lock',
                        '1' => 'fas fa-lock',
                        '5' => 'fas fa-play-circle',
                        '6' => 'far fa-images'
                    );
                    echo '<span class="meta-shhop-icon"><i class="' . $_icon[$shop_type] . '"></i></span>';
                endif;

                //编辑按钮
                if ($options['edit']) : ?>
                    <span class="meta-edit"><?php edit_post_link('[编辑]'); ?></span>
                <?php endif; ?>


            </div>
    <?php endif;
    }
}


/**
 * 获取摘要描述
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:48:45+0800
 * @param    string                   $limit [description]
 * @return   [type]                          [description]
 */
if (!function_exists('rizhuti_v2_excerpt')) {
    function rizhuti_v2_excerpt($limit = '46')
    {
        $excerpt = get_the_excerpt();
        if (empty($excerpt)) {
            $excerpt = get_the_content();
        }
        return wp_trim_words(strip_shortcodes($excerpt), $limit, '...');
    }
}


/**
 * 获取分类多个html标签
 * @Author   Dadong2g
 * @DateTime 2021-01-26T01:34:13+0800
 * @param    integer                  $num [description]
 * @return   [type]                        [description]
 */
function rizhuti_v2_category_dot($num = 2)
{
    $i = 0;
    $categories = get_the_category();
    echo '<span class="meta-category-dot">';

    foreach ($categories as $k => $c) {
        $i++;
        if ($i > $num) break;
        echo '<a href="' . esc_url(get_category_link($c->term_id)) . '" rel="category"><i class="dot"></i>' . esc_html($c->name) . '</a>';
    }

    echo '</span>';
}

/**
 * logo html
 * @Author   Dadong2g
 * @DateTime 2021-01-16T13:49:54+0800
 * @param    array                    $options [description]
 * @return   [type]                            [description]
 */
function rizhuti_v2_logo($options = array())
{
    $options  = array_merge(array('contrary' => true), $options);
    $logo_src = _cao('site_logo'); ?>
    <div class="logo-wrapper">
        <?php if (!empty($logo_src)) : ?>
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img class="logo regular" src="<?php echo esc_url($logo_src); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
            </a>
        <?php else : ?>
            <a class="logo text" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a>
        <?php endif; ?>

    </div> <?php
        }


        /**
         * 设置字段优先级比对
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:50:06+0800
         * @param    [type]                   $global   [description]
         * @param    [type]                   $override [description]
         * @return   [type]                             [description]
         */
        function rizhuti_v2_compare_options($global, $override)
        {
            if (_cao('is_compare_options_to_global', false)) {
                return $global;
            }
            if ($global == $override || empty($override)) {
                return $global;
            } else {
                return $override;
            }
        }

        /**
         * 顶部是否显示hero效果
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:50:20+0800
         * @return   [type]                   [description]
         */
        function rizhuti_v2_show_hero()
        {
            return (is_singular('post')) && rizhuti_v2_compare_options(_cao('hero_single_style', 'none'), get_post_meta(get_the_ID(), 'hero_single_style', 1)) != 'none';
        }

        /**
         * 侧边栏风格
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:50:35+0800
         * @return   [type]                   [description]
         */
        function rizhuti_v2_sidebar()
        {
            if (is_singular('post') || (is_page() && !is_page_template_modular())) {
                return rizhuti_v2_compare_options(_cao('sidebar_single_style', 'right'), get_post_meta(get_the_ID(), 'sidebar_single_style', 1));
            } elseif (is_category()) {
                $term_meta = get_term_meta(get_queried_object_id(), 'archive_single_style', true);
                return rizhuti_v2_compare_options(_cao('archive_single_style', 'right'), $term_meta);
            } elseif (is_archive() || is_search()) {
                return 'none';
            } elseif (is_home()) {
                return 'right';
            }
            return 'none';
        }

        /**
         * 列表风格
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:50:41+0800
         * @return   [type]                   [description]
         */
        function rizhuti_v2_item_style()
        {
            $options = _cao('archive_item_style', 'list');
            if (is_category() || taxonomy_exists('series')) {
                $term_meta = get_term_meta(get_queried_object_id(), 'archive_item_style', true);
                return rizhuti_v2_compare_options($options, $term_meta);
            } elseif (is_archive() || is_search()) {
                return $options;
            } elseif (is_home()) {
                return $options;
            }
            return 'list';
        }


        /**
         * col属性
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:50:45+0800
         * @param    [type]                   $sidebar [description]
         * @return   [type]                            [description]
         */
        function rizhuti_v2_column_classes($sidebar)
        {
            $content_column_class = 'content-column col-lg-9';
            $sidebar_column_class = 'sidebar-column col-lg-3';
            if ($sidebar == 'none') {
                $content_column_class = 'col-lg-12';
            }
            return apply_filters('rizhuti_v2_column_classes', array($content_column_class, $sidebar_column_class));
        }

        /**
         * 翻页导航
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:50:49+0800
         * @param    integer                  $range [description]
         * @param    array                    $args  [description]
         * @return   [type]                          [description]
         */
        function rizhuti_v2_pagination($range = 9, $args = array())
        {
            global $paged, $wp_query, $page, $numpages, $multipage;
            $site_pagination = _cao('site_pagination', 'numeric');
            if ($site_pagination == 'navigation') {
                $range = 0;
            }

            if (($args && $args['numpages'] > 1) || (isset($multipage) && $multipage && is_single())) {
                if ($args) {
                    $page     = $args['paged'];
                    $numpages = $args['numpages'];
                }
                echo '<div class="pagination justify-content-center">';
                $prev = $page - 1;
                if ($prev > 0) {
                    echo str_replace('<a', '<a class="prev"', _wp_link_page($prev) . __('<i class="fa fa-chevron-left"></i> 上一页', 'rizhuti-v2') . '</a>');
                }

                if ($numpages > $range) {
                    if ($page < $range) {
                        for ($i = 1; $i <= ($range + 1); $i++) {
                            if ($i == $page) {
                                echo str_replace('<a', '<a class="current"', _wp_link_page($i)) . $i . "</a>";
                            } else {
                                echo _wp_link_page($i) . $i . "</a>";
                            }
                        }
                    } elseif ($page >= ($numpages - ceil(($range / 2)))) {
                        for ($i = $numpages - $range; $i <= $numpages; $i++) {
                            if ($i == $page) {
                                echo str_replace('<a', '<a class="current"', _wp_link_page($i)) . $i . "</a>";
                            } else {
                                echo _wp_link_page($i) . $i . "</a>";
                            }
                        }
                    } elseif ($page >= $range && $page < ($numpages - ceil(($range / 2)))) {
                        for ($i = ($page - ceil($range / 2)); $i <= ($page + ceil(($range / 2))); $i++) {
                            if ($i == $page) {
                                echo str_replace('<a', '<a class="current"', _wp_link_page($i)) . $i . "</a>";
                            } else {
                                echo _wp_link_page($i) . $i . "</a>";
                            }
                        }
                    }
                } else {
                    for ($i = 1; $i <= $numpages; $i++) {
                        if ($i == $page) {
                            echo str_replace('<a', '<a class="current"', _wp_link_page($i)) . $i . "</a>";
                        } else {
                            echo _wp_link_page($i) . $i . "</a>";
                        }
                    }
                }

                $next = $page + 1;
                if ($next <= $numpages) {
                    echo str_replace('<a', '<a class="next"', _wp_link_page($next) . __('下一页 <i class="fa fa-chevron-right"></i>', 'rizhuti-v2') . '</a>');
                }
                echo '</div>';
            } else if (($max_page = $wp_query->max_num_pages) > 1) {
                echo ' <div class="pagination justify-content-center">';
                if (!$paged) {
                    $paged = 1;
                }

                echo '<span>' . $paged . '/' . $max_page . '</span>';
                previous_posts_link(__('<i class="fa fa-chevron-left"></i> 上一页', 'rizhuti-v2'));
                if ($max_page > $range) {
                    if ($paged < $range) {
                        for ($i = 1; $i <= ($range + 1); $i++) {
                            echo "<a href='" . get_pagenum_link($i) . "'";
                            if ($i == $paged) {
                                echo " class='current'";
                            }

                            echo ">" . $i . "</a>";
                        }
                    } elseif ($paged >= ($max_page - ceil(($range / 2)))) {
                        for ($i = $max_page - $range; $i <= $max_page; $i++) {
                            echo "<a href='" . get_pagenum_link($i) . "'";
                            if ($i == $paged) {
                                echo " class='current'";
                            }

                            echo ">" . $i . "</a>";
                        }
                    } elseif ($paged >= $range && $paged < ($max_page - ceil(($range / 2)))) {
                        for ($i = ($paged - ceil($range / 2)); $i <= ($paged + ceil(($range / 2))); $i++) {
                            echo "<a href='" . get_pagenum_link($i) . "'";
                            if ($i == $paged) {
                                echo " class='current'";
                            }

                            echo ">" . $i . "</a>";
                        }
                    }
                } else {
                    for ($i = 1; $i <= $max_page; $i++) {
                        echo "<a href='" . get_pagenum_link($i) . "'";
                        if ($i == $paged) {
                            echo " class='current'";
                        }

                        echo ">$i</a>";
                    }
                }
                next_posts_link(__('下一页 <i class="fa fa-chevron-right"></i>', 'rizhuti-v2'));
                echo '</div>';
            }

            if (!is_singular() && strpos($site_pagination, 'infinite') !== false) : ?>
        <div class="infinite-scroll-status">
            <div class="infinite-scroll-request"><i class="fa fa-spinner fa-spin " style=" font-size: 30px; "></i></div>
        </div>
        <div class="infinite-scroll-action">
            <div class="infinite-scroll-button btn btn-dark"><?php echo apply_filters('rizhuti_v2_infinite_button_load', esc_html__('加载更多', 'rizhuti-v2')); ?></div>
        </div>
<?php endif;
        }

        /**
         * 主题自定义参数类分页导航
         * @Author   Dadong2g
         * @DateTime 2021-06-01T20:52:09+0800
         * @param    integer                  $pagenum       [description]
         * @param    integer                  $max_num_pages [description]
         * @return   [type]                                  [description]
         */
        function rizhuti_v2_custom_pagination($pagenum = 0, $max_num_pages = 0)
        {

            $page_links = paginate_links(array(
                'base' => add_query_arg('pagenum', '%#%'),
                'format' => '',
                'prev_text' => __('<i class="fa fa-chevron-left"></i> 上一页', 'rizhuti-v2'),
                'next_text' => __('下一页 <i class="fa fa-chevron-right"></i>', 'rizhuti-v2'),
                'total' => intval($max_num_pages),
                'current' => intval($pagenum)
            ));

            if ($page_links) {
                echo '<div class="pagination justify-content-center">' . $page_links . '</div>';
            }
        }



        /**
         * 上一页翻页钩子替换
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:50:56+0800
         * @param    [type]                   $attr [description]
         * @return   [type]                         [description]
         */
        function rizhuti_v2_prev_posts_link_attr($attr)
        {
            return $attr . ' class="prev"';
        }
        add_filter('previous_posts_link_attributes', 'rizhuti_v2_prev_posts_link_attr');

        /**
         * 下一页翻页钩子替换
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:01+0800
         * @param    [type]                   $attr [description]
         * @return   [type]                         [description]
         */
        function rizhuti_v2_next_posts_link_attr($attr)
        {
            return $attr . ' class="next"';
        }
        add_filter('next_posts_link_attributes', 'rizhuti_v2_next_posts_link_attr');



        /**
         * 面包屑导航
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:06+0800
         * @param    string                   $class [description]
         * @return   [type]                          [description]
         */
        function rizhuti_v2_breadcrumb($class = 'breadcrumb')
        {
            global $post, $wp_query;
            echo '<ol class="' . $class . '">' . __('当前位置：', 'rizhuti-v2') . '<li class="home"><i class="fa fa-home"></i> <a href="' . home_url() . '">' . __('首页', 'rizhuti-v2') . '</a></li>';

            if (is_category()) {
                $cat_obj   = $wp_query->get_queried_object();
                $thisCat   = $cat_obj->term_id;
                $thisCat   = get_category($thisCat);
                $parentCat = get_category($thisCat->parent);

                if ($thisCat->parent != 0) {
                    echo rizhuti_get_category_parents($parentCat);
                }

                echo '<li class="active">';
                single_cat_title();
                echo '</li>';
            } elseif (is_day()) {
                echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> </li>';
                echo '<li><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> </li>';
                echo '<li class="active">' . get_the_time('d') . '</li>';
            } elseif (is_month()) {
                echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> </li>';
                echo '<li class="active">' . get_the_time('F') . '</li>';
            } elseif (is_year()) {
                echo '<li class="active">' . get_the_time('Y') . '</li>';
            } elseif (is_attachment()) {
                echo '<li class="active">';
                the_title();
                echo '</li>';
            } elseif (is_single()) {
                $post_type = get_post_type();
                if ($post_type == 'post') {
                    $cat = get_the_category();
                    $cat = isset($cat[0]) ? $cat[0] : 0;
                    echo rizhuti_get_category_parents($cat);
                } else if ($post_type == 'product') {
                    global $post;

                    $taxonomy = 'product_cat';

                    $terms = get_the_terms($post->ID, $taxonomy);
                    $links = array();
                    if ($terms && !is_wp_error($terms)) {

                        foreach ($terms as $c) {

                            $parents = riplus_get_term_parents($c->term_id, $taxonomy, true, ', ', false, array($c->term_id));
                            if ($parents != '') {
                                $parents_arr = explode(', ', $parents);

                                foreach ($parents_arr as $p) {
                                    if ($p != '') {
                                        $links[] = $p;
                                    }
                                }
                            }
                        }
                        foreach ($links as $link) {
                            echo '<li>' . $link . '</li>';
                        }
                    }
                } else {
                    $obj = get_post_type_object($post_type);
                    echo '<li class="active">';
                    echo $obj->labels->singular_name;
                    echo '</li>';
                }
            } elseif (is_page() && !$post->post_parent) {
                echo '<li class="active">';
                the_title();
                echo '</li>';
            } elseif (is_page() && $post->post_parent) {
                $parent_id   = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page          = get_post($parent_id);
                    $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
                    $parent_id     = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb) {
                    echo $crumb;
                }

                echo '<li class="active">';
                the_title();
                echo '</li>';
            } elseif (is_search()) {
                $kw = get_search_query();
                $kw = !empty($kw) ? $kw : __('无', 'rizhuti-v2');
                echo '<li class="active">' . sprintf(__('搜索: %s', 'rizhuti-v2'), $kw) . '</li>';
            } elseif (is_tag()) {
                echo '<li class="active">';
                single_tag_title();
                echo '</li>';
            } elseif (is_author()) {
                global $author;
                $userdata = get_userdata($author);
                echo '<li class="active">' . $userdata->display_name . '</li>';
            } elseif (is_404()) {
                echo '<li class="active">' . __('404 ERROR', 'rizhuti-v2') . '</li>';
            }

            if (get_query_var('paged')) {
                echo '<li class="active">';
                echo sprintf(__('第%s页', 'rizhuti-v2'), get_query_var('paged'));
                echo '</li>';
            }

            echo '</ol>';
        }




        /**
         * 生产二维码地址
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:21+0800
         * @param    [type]                   $text [description]
         * @return   [type]                         [description]
         */
        function getQrcodeApi($text)
        {
            $api_url = get_template_directory_uri() . '/inc/qrcode.php?data=';
            return $api_url . $text;
        }


        /**
         * 判断是否微信访问
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:26+0800
         * @return   boolean                  [description]
         */
        function is_weixin_visit()
        {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                return true;
            } else {
                return false;
            }
        }


        /**
         * 获取客户端IP
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:31+0800
         * @return   [type]                   [description]
         */
        function get_client_ip()
        {
            if (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            }
            if (getenv('HTTP_X_REAL_IP')) {
                $ip = getenv('HTTP_X_REAL_IP');
            } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip  = getenv('HTTP_X_FORWARDED_FOR');
                $ips = explode(',', $ip);
                $ip  = $ips[0];
            } elseif (getenv('REMOTE_ADDR')) {
                $ip = getenv('REMOTE_ADDR');
            } else {
                $ip = '0.0.0.0';
            }
            return $ip;
        }


        /**
         * 生产邮箱验证码
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:36+0800
         * @param    [type]                   $email [description]
         */
        function set_verify_email_code($email)
        {
            if (!session_id()) {
                session_start();
            }
            $originalcode = '0,1,2,3,4,5,6,7,8,9';
            $originalcode = explode(',', $originalcode);
            $countdistrub = 10;
            $_dscode      = "";
            $counts       = 6;
            for ($j = 0; $j < $counts; $j++) {
                $dscode = $originalcode[rand(0, $countdistrub - 1)];
                $_dscode .= $dscode;
            }
            $_SESSION['riplus_verify_email_code'] = strtolower($_dscode);
            $_SESSION['riplus_verify_email']      = $email;
            $message = '验证码：' . $_dscode;
            $send_email = _sendMail($email, '验证码', $message);
            if ($send_email) {
                return true;
            }
            return false;
        }

        /**
         * 发送html格式邮件
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:41+0800
         * @param    [type]                   $email   [description]
         * @param    [type]                   $title   [description]
         * @param    [type]                   $message [description]
         * @return   [type]                            [description]
         */
        function _sendMail($email, $title, $message)
        {
            $headers    = array('Content-Type: text/html; charset=UTF-8');
            $message    = tpl_email_html($email, $title, $message);
            $send_email = wp_mail($email, $title, $message, $headers);
            if ($send_email) {
                return true;
            }
            return false;
        }



        /**
         * html格式邮件
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:51:54+0800
         * @param    [type]                   $user  [description]
         * @param    [type]                   $title [description]
         * @param    [type]                   $desc  [description]
         * @return   [type]                          [description]
         */
        function tpl_email_html($user, $title, $desc)
        {
            $html = '<div style="background-color:#eef2fa;border:1px solid #d8e3e8;color: #111;padding:0 15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;">';
            $html .= '<p style="font-weight: bold;color: #2196F3;font-size: 18px;">' . $title . '</p>';
            $html .= sprintf("<p>您好，%s</p>", $user);
            $html .= sprintf("<p>内容: %s</p>", $desc);
            $html .= sprintf("<p>时间: %s</p>", date("Y-m-d H:i:s"));
            $a_href = '<a href="' . home_url() . '">' . get_bloginfo('name') . '</a>';
            $html .= sprintf("<p>官网： %s</p>", $a_href);
            $html .= '</div>';
            return $html;
        }


        /**
         * 是否一键登录密码
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:52:01+0800
         * @return   boolean                  [description]
         */
        function is_oauth_password()
        {
            global $current_user;
            $array = array('qq', 'weixin', 'mpweixin', 'weibo');
            foreach ($array as $type) {
                $p2 = get_user_meta($current_user->ID, 'open_' . $type . '_openid', 1);
                if (wp_check_password(md5($p2), $current_user->data->user_pass, $current_user->ID)) {
                    return true;
                }
            }
            return false;
        }


        /**
         * 获取腾讯防水墙按钮
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:52:07+0800
         * @param    boolean                  $script [description]
         * @return   [type]                           [description]
         */
        function qq_captcha_btn($script = true, $clsses = 'col-12')
        {
            $id = _cao('qq_007_captcha_appid');
            if (!_cao('is_qq_007_captcha')) return;
            if ($script) wp_enqueue_script('captcha');
            echo '<div class="' . $clsses . '"><button type="button" class="TencentCaptcha btn btn-light w-100 mb-3" id="TencentCaptcha" data-appid="' . $id . '" data-cbfn="qq_aptcha_callback"><span class="spinner-grow spinner-grow-sm text-primary mr-2" role="status" aria-hidden="true"></span>点击按钮进行验证</button></div>';
        }



        /**
         * 验证防水墙
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:52:24+0800
         * @return   [type]                   [description]
         */
        function ajax_qq_captcha_verify()
        {
            header('Content-type:application/json; Charset=utf-8');
            $url = 'https://ssl.captcha.qq.com/ticket/verify';
            $appid = _cao('qq_007_captcha_appid');
            $AppSecretKey = _cao('qq_007_captcha_appkey');
            $Ticket = isset($_POST['Ticket']) ? $_POST['Ticket'] : '';
            $Randstr = isset($_POST['Randstr']) ? $_POST['Randstr'] : '';
            $UserIP = get_client_ip();
            $params = array(
                "aid" => $appid,
                "AppSecretKey" => $AppSecretKey,
                "Ticket" => $Ticket,
                "Randstr" => $Randstr,
                "UserIP" => $UserIP
            );
            $data = http_build_query($params);
            $result = RiPlusNetwork::get($url . '?' . $data);
            $res = json_decode($result, true);
            if (isset($res) && $res['response'] == 1) {
                $_SESSION['is_qq_captcha_verify'] = 1;
            } else {
                $_SESSION['is_qq_captcha_verify'] = 0;
            }
            echo $result;
            exit;
        }
        add_action('wp_ajax_qq_captcha_verify', 'ajax_qq_captcha_verify');
        add_action('wp_ajax_nopriv_qq_captcha_verify', 'ajax_qq_captcha_verify');


        /**
         * 是否需要腾讯验证码验证
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:52:46+0800
         * @return   [type]                   [description]
         */
        function qq_captcha_verify()
        {
            $is_verify = _cao('is_qq_007_captcha');
            $is_verify = apply_filters('is_qq_007_captcha', $is_verify);
            if (!$is_verify) return true;
            if ($_SESSION['is_qq_captcha_verify'] != 1) return false;
            return true;
        }

        /**
         * 是否需要邮箱验证
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:52:51+0800
         * @param    string                   $email [description]
         * @param    string                   $code  [description]
         * @return   [type]                          [description]
         */
        function email_captcha_verify($email = '', $code = '')
        {
            $is_verify = _cao('is_site_email_captcha_verify');
            // $is_verify = apply_filters('is_email_captcha_verify',$is_verify);
            if (!$is_verify) {
                return true;
            }
            if (empty($email) || empty($code)) {
                return false;
            }
            if (empty($_SESSION['riplus_verify_email_code']) || empty($_SESSION['riplus_verify_email'])) {
                return false;
            }

            if ($code != $_SESSION['riplus_verify_email_code'] || $email != $_SESSION['riplus_verify_email']) {
                return false;
            }

            return true;
        }

        /**
         * 当前页面url
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:53:00+0800
         * @return   [type]                   [description]
         */
        function curPageURL()
        {
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }

        /**
         * 用户中心页面url
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:53:05+0800
         * @param    string                   $action [description]
         * @return   [type]                           [description]
         */
        function get_user_page_url($action = '')
        {
            $url = apply_filters('rizhuti_user_page_url', esc_url(home_url('/user')));
            if (!empty($action)) {
                $url = $url . '/' . $action;
            }
            return esc_url($url);
        }


        /**
         * 用户中心页面菜单参数配置
         * @Author   Dadong2g
         * @DateTime 2021-01-23T09:38:44+0800
         * @return   [type]                   [description]
         */
        function user_page_action_param_opt()
        {

            $param_shop = [
                'coin'   => ['action' => 'coin', 'name' => esc_html__('我的余额', 'rizhuti-v2'), 'icon' => site_mycoin('icon') . ' nav-icon'],
                'vip'    => ['action' => 'vip', 'name' => esc_html__('我的会员', 'rizhuti-v2'), 'icon' => 'fa fa-diamond nav-icon'],
                'order'  => ['action' => 'order', 'name' => esc_html__('购买记录', 'rizhuti-v2'), 'icon' => 'fas fa-shopping-basket nav-icon'],
                'down'   => ['action' => 'down', 'name' => esc_html__('下载记录', 'rizhuti-v2'), 'icon' => 'fas fa-cloud-download-alt nav-icon'],
                'fav'    => ['action' => 'fav', 'name' => esc_html__('我的收藏', 'rizhuti-v2'), 'icon' => 'far fa-star nav-icon'],
                'aff'    => ['action' => 'aff', 'name' => esc_html__('推广中心', 'rizhuti-v2'), 'icon' => 'fas fa-hand-holding-usd nav-icon'],
                'tou'    => ['action' => 'tou', 'name' => esc_html__('文章投稿', 'rizhuti-v2'), 'icon' => 'fa fa-newspaper-o nav-icon'],
                'shouru' => ['action' => 'shouru', 'name' => esc_html__('作者收入', 'rizhuti-v2'), 'icon' => 'fas fa-hand-holding-usd nav-icon'],
                'msg'    => ['action' => 'msg', 'name' => esc_html__('消息工单', 'rizhuti-v2'), 'icon' => 'fa fa-bell-o nav-icon'],
            ];

            if (!_cao('is_site_mycoin', true)) {
                unset($param_shop['coin']);
            }
            if (!_cao('is_site_tickets', true)) {
                unset($param_shop['msg']);
            }
            if (!_cao('is_site_aff')) {
                unset($param_shop['aff']);
            }
            if (!_cao('is_site_tougao')) {
                unset($param_shop['tou']);
            }

            if (!_cao('is_site_author_aff', false)) {
                unset($param_shop['shouru']);
            }

            if (is_oauth_password()) {
                $password_notfy = '<span class="badge badge-danger-lighten nav-link-badge">' . esc_html__('请设置密码', 'rizhuti-v2') . '</span>';
            } else {
                $password_notfy = '';
            }
            $param_user = [
                'index'    => ['action' => 'index', 'name' => esc_html__('基本资料', 'rizhuti-v2'), 'icon' => 'fas fa-id-card nav-icon nav-icon'],
                'bind'     => ['action' => 'bind', 'name' => esc_html__('账号绑定', 'rizhuti-v2'), 'icon' => 'fas fa-mail-bulk nav-icon'],
                'password' => ['action' => 'password', 'name' => esc_html__('密码设置', 'rizhuti-v2') . $password_notfy, 'icon' => 'fas fa-shield-alt nav-icon'],
            ];

            return apply_filters('user_page_action_param_opt', array('shop' => $param_shop, 'info' => $param_user));
        }


        /**
         * 下载地址url
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:53:11+0800
         * @param    string                   $type [description]
         * @param    string                   $str  [description]
         * @return   [type]                         [description]
         */
        function get_goto_url($type = '', $str = '')
        {
            $url = apply_filters('rizhuti_goto_url', home_url('/goto'));
            if (!empty($type)) {
                $url = add_query_arg(array($type => $str), $url);
            }
            return esc_url($url);
        }


        /**
         * 第三方登陆地址
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:53:18+0800
         * @param    [type]                   $type     [description]
         * @param    [type]                   $redirect [description]
         * @return   [type]                             [description]
         */
        function get_open_oauth_url($type, $redirect)
        {
            $oauth = apply_filters('rizhuti_open_oauth_url', 'oauth');
            $url = home_url('/' . $oauth . '/' . $type . '?rurl=' . $redirect);
            return esc_url($url);
        }


        /**
         * 通过子分类id获取父分类id
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:53:26+0800
         * @param    [type]                   $cat [description]
         * @return   [type]                        [description]
         */
        function get_category_root_id($cat)
        {
            $this_category = get_category($cat); // 取得当前分类
            while ($this_category->category_parent) {
                $this_category = get_category($this_category->category_parent); //将当前分类设为上级分类（往上爬）
            }
            return $this_category->term_id; // 返回根分类的id号
        }


        /**
         * 顶级分类id
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:53:33+0800
         * @param    [type]                   $id      [description]
         * @param    array                    $visited [description]
         * @return   [type]                            [description]
         */
        function rizhuti_get_category_parents($id, $visited = array())
        {
            if (!$id) {
                return '';
            }
            $chain  = '';
            $parent = get_term($id, 'category');
            if (is_wp_error($parent)) {
                return '';
            }
            $name = $parent->name;
            if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
                $visited[] = $parent->parent;
                $chain .= rizhuti_get_category_parents($parent->parent, $visited);
            }
            $chain .= '<li><a href="' . esc_url(get_category_link($parent->term_id)) . '">' . $name . '</a></li>';
            return $chain;
        }


        /**
         * 顶级分类递归
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:53:42+0800
         * @param    [type]                   $id        [description]
         * @param    [type]                   $taxonomy  [description]
         * @param    boolean                  $link      [description]
         * @param    string                   $separator [description]
         * @param    boolean                  $nicename  [description]
         * @param    array                    $visited   [description]
         * @return   [type]                              [description]
         */
        function riplus_get_term_parents($id, $taxonomy, $link = false, $separator = '', $nicename = false, $visited = array())
        {
            $chain  = '';
            $parent = get_term($id, $taxonomy);
            if (is_wp_error($parent)) {
                return $parent;
            }
            if ($nicename) {
                $name = $parent->slug;
            } else {
                $name = $parent->name;
            }
            if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited) && !in_array($parent->term_id, $visited)) {
                $visited[] = $parent->parent;
                $visited[] = $parent->term_id;
                $chain .= riplus_get_term_parents($parent->parent, $taxonomy, $link, $separator, $nicename, $visited);
            }
            if ($link) {
                $chain .= '<a href="' . get_term_link($parent, $taxonomy) . '" title="' . esc_attr($parent->name) . '">' . $name . '</a>' . $separator;
            } else {
                $chain .= $name . $separator;
            }

            return $chain;
        }

        /**
         * 获取文章标签 10条
         * @Author   Dadong2g
         * @DateTime 2019-05-28T12:20:43+0800
         * @param    [type]                   $args [description]
         * @return   [type]                         [description]
         */
        function rizhuti_get_category_tags($cat_id = 0)
        {
            global $wpdb;
            $tags = $wpdb->get_results("
        SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name
        FROM
            $wpdb->posts as p1
            LEFT JOIN $wpdb->term_relationships as r1 ON p1.ID = r1.object_ID
            LEFT JOIN $wpdb->term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
            LEFT JOIN $wpdb->terms as terms1 ON t1.term_id = terms1.term_id,
            $wpdb->posts as p2
            LEFT JOIN $wpdb->term_relationships as r2 ON p2.ID = r2.object_ID
            LEFT JOIN $wpdb->term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
            LEFT JOIN $wpdb->terms as terms2 ON t2.term_id = terms2.term_id
        WHERE
            t1.taxonomy = 'category' AND p1.post_status = 'publish' AND terms1.term_id IN (" . $cat_id . ") AND
            t2.taxonomy = 'post_tag' AND p2.post_status = 'publish'
            AND p1.ID = p2.ID
        ORDER by tag_name LIMIT 10
    ");
            $count = 0;

            if ($tags) {
                foreach ($tags as $tag) {
                    $mytag[$count] = get_term_by('id', $tag->tag_id, 'post_tag');
                    $count++;
                }
            } else {
                $mytag = null;
            }

            return $mytag;
        }


        /**
         * 根据页面别名（slug）获取页面id
         * @Author   Dadong2g
         * @DateTime 2021-01-23T00:50:22+0800
         * @param    [type]                   $page_name [description]
         * @return   [type]                              [description]
         */
        function get_page_id($page_name)
        {
            global $wpdb;
            $page_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '" . $page_name . "' AND post_status = 'publish' AND post_type = 'page'");
            return $page_name;
        }






        /**
         * 添加文章阅读数量
         * @Author   Dadong2g
         * @DateTime 2021-01-25T20:13:59+0800
         */
        function add_post_views($post_id = null)
        {
            if (empty($post_id)) {
                global $post;
                $post_id = $post->ID;
            }
            $this_num = (int)get_post_meta($post_id, '_views', true);
            $new_num = $this_num + 1;
            if ($new_num < 0) {
                $new_num = 1;
            }
            return update_post_meta($post_id, '_views', $new_num);
        }

        /**
         * 获取文章查看数量
         * @Author   Dadong2g
         * @DateTime 2021-01-25T20:14:33+0800
         */
        function _get_post_views($post_id = null)
        {
            if (empty($post_id)) {
                global $post;
                $post_id = $post->ID;
            }
            $this_num = (int)get_post_meta($post_id, '_views', true);
            if (1000 <= $this_num) {
                $this_num = sprintf('%0.1f', $this_num / 1000) . 'K';
            }
            return $this_num;
        }


        // 获取文章收藏数量
        function _get_post_fav($post_id = null)
        {
            if (empty($post_id)) {
                global $post;
                $post_id = $post->ID;
            }
            return (int)get_post_meta($post_id, '_favnum', true);
        }


        /**
         * 收藏文章
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:57:47+0800
         * @param    string                   $user_id [description]
         * @param    string                   $to_post [description]
         */
        function add_fav_post($user_id = '', $to_post = '')
        {
            $_meta_key = 'fav_post';
            $to_post = (int)$to_post;
            if (get_post_status($to_post) === false) return 'false';

            $old_follow = get_user_meta($user_id, $_meta_key, true); # 获取...

            if (is_array($old_follow)) {
                $new_follow = $old_follow;
            } else {
                $new_follow = array(0);
            }
            if (!in_array($to_post, $new_follow)) {
                // 新关注 开始处理
                array_push($new_follow, $to_post);
            }

            $this_favnum = (int)get_post_meta($to_post, '_favnum', true);
            $new_num = $this_favnum + 1;
            if ($new_num < 0) {
                $new_num = 0;
            }

            update_post_meta($to_post, '_favnum', $new_num);

            return update_user_meta($user_id, $_meta_key, $new_follow);
        }

        /**
         * 是否收藏过此文章
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:57:51+0800
         * @param    [type]                   $post_id [description]
         * @return   boolean                           [description]
         */
        function is_fav_post($post_id = null)
        {
            if (empty($post_id)) {
                global $post;
                $post_id = $post->ID;
            }

            $user_id = get_current_user_id();
            if (!$user_id) {
                return false;
            }

            $old_follow = get_user_meta($user_id, 'fav_post', true); # 获取...
            if (empty($old_follow) || !is_array($old_follow)) {
                return false;
            }

            if (in_array($post_id, $old_follow)) {
                return true;
            } else {
                return false;
            }
        }


        /**
         * 取消收藏文章
         * @Author   Dadong2g
         * @DateTime 2021-01-16T13:58:00+0800
         * @param    string                   $user_id [description]
         * @param    string                   $to_post [description]
         * @return   [type]                            [description]
         */
        function del_fav_post($user_id = '', $to_post = '')
        {
            $_meta_key = 'fav_post';
            if (get_post_status($to_post) === false) return 'false';
            $follow_post = get_user_meta($user_id, $_meta_key, true); # 获取...

            if (!is_array($follow_post)) {
                return false;
            }

            if (!in_array($to_post, $follow_post)) {
                return false;
            }

            foreach ($follow_post as $key => $post_id) {
                if ($post_id == $to_post) {
                    unset($follow_post[$key]);
                    break;
                }
            }
            $this_favnum = (int)get_post_meta($to_post, '_favnum', true);
            $new_num = $this_favnum - 1;
            if ($new_num < 0) {
                $new_num = 0;
            }
            update_post_meta($to_post, '_favnum', $new_num);

            return update_user_meta($user_id, $_meta_key, $follow_post);
        }



        if (is_site_question()) {
            require_once get_template_directory() . '/inc/template-question.php';
        }
