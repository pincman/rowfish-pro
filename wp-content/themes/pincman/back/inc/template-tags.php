<?php

/**
 * 新增与修改:
 * 
 * 请查看函数注释
 */

use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;
// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * 用户中心页面菜单参数配置
 * @Author Dadong2g
 * @DateTime 2021-01-23T09:38:44+0800
 * @return [type] [description]
 */
function pincman_user_page_options()
{

    $param_shop = [
        'coin' => ['action' => 'coin', 'name' => esc_html__('我的余额', 'rizhuti-v2'), 'icon' => site_mycoin('icon') . ' nav-icon'],
        // 'order' => ['action' => 'order', 'name' => esc_html__('购买记录', 'rizhuti-v2'), 'icon' => 'fas fa-shopping-basket nav-icon'],
        // 'down' => ['action' => 'down', 'name' => esc_html__('下载记录', 'rizhuti-v2'), 'icon' => 'fas fa-cloud-download-alt nav-icon'],
        'fav' => ['action' => 'fav', 'name' => esc_html__('我的收藏', 'rizhuti-v2'), 'icon' => 'far fa-star nav-icon'],
        // 'qa' => ['action' => 'qa', 'name' => esc_html__('我的问答', 'rizhuti-v2'), 'icon' => 'fa fa-newspaper-o nav-icon'],
        'aff' => ['action' => 'aff', 'name' => esc_html__('推广中心', 'rizhuti-v2'), 'icon' => 'fas fa-hand-holding-usd nav-icon'],
        'tou' => ['action' => 'tou', 'name' => esc_html__('文章投稿', 'rizhuti-v2'), 'icon' => 'fa fa-newspaper-o nav-icon'],
        'msg' => ['action' => 'msg', 'name' => esc_html__('消息工单', 'rizhuti-v2'), 'icon' => 'fa fa-bell-o nav-icon'],
        'vip' => ['action' => 'vip', 'name' => esc_html__('我的赞助', 'rizhuti-v2'), 'icon' => 'fa fa-code nav-icon'],
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

    if (is_oauth_password()) {
        $password_notfy = '<span class="badge badge-danger-lighten nav-link-badge">' . esc_html__('请设置密码', 'rizhuti-v2') . '</span>';
    } else {
        $password_notfy = '';
    }
    $param_user = [
        'index' => ['action' => 'index', 'name' => esc_html__('基本资料', 'rizhuti-v2'), 'icon' => 'fas fa-id-card nav-icon nav-icon'],
        'bind' => ['action' => 'bind', 'name' => esc_html__('账号绑定', 'rizhuti-v2'), 'icon' => 'fas fa-mail-bulk nav-icon'],
        'password' => ['action' => 'password', 'name' => esc_html__('密码设置', 'rizhuti-v2') . $password_notfy, 'icon' => 'fas fa-shield-alt nav-icon'],
    ];

    return apply_filters('user_page_action_param_opt', array('shop' => $param_shop, 'info' => $param_user));
}

/**
 * 随机缩略图
 * 
 * @return mixed 
 */
function pm_the_theme_thumb()
{
    $rand_images_option = _cao('post_images');
    if (is_array($rand_images_option) && count($rand_images_option) > 0) {
        $rand_images = array_filter($rand_images_option, function ($img) {
            return $img && is_array($img) && $img['url'] && $img['url'] !== '';
        });
        if (count($rand_images)) {
            $rand_post_image = $rand_images[rand(0, count($rand_images) - 1)];
        }
    }
    return $rand_post_image ? $rand_post_image['url'] : get_template_directory_uri() . '/assets/img/thumb.jpg';
}

if (!function_exists('pm_get_size_thumbnail')) {
    function pm_get_size_thumbnail($src = null, $size = 'thumbnail')
    {
        if (!$src) $src = pm_the_theme_thumb();

        //云储存适配
        if ($size == 'thumbnail' && _cao('is_img_cloud_storage', false)) {
            // 判断裁剪模式
            $storage_domain = _cao('img_cloud_storage_domain');
            $storage_param = _cao('img_cloud_storage_param');
            if (strpos($src, $storage_domain) !== false) {
                return  $src . $storage_param;
            }
        }
        if ($size === 'thumbnail') {
            $width = 300;
            $height = 200;
        } else if (is_numeric($size)) {
            $width = $size;
            $height = $size;
        }
        $rand_thumb = pm_the_theme_thumb();
        // $cache_img =  pm_the_theme_thumb();
        if ($width && $height) {
            $img_uri_arr = explode('/', $rand_thumb);
            $img_file_arr = explode('.', $img_uri_arr[count($img_uri_arr) - 1]);
            $img_file_name = $img_file_arr[0];
            $img_file_ext = $img_file_arr[1];
            $img_file = "{$img_file_name}-{$width}x{$height}.{$img_file_ext}";
            $img_file_url_path = "assets/images/{$img_file}";
            $img_file_path = __DIR__ . "/../{$img_file_url_path}";
            if (!file_exists($img_file_path)) {
                $img = Image::make(file_get_contents($rand_thumb))->resize($width, $height);
                $img->save($img_file_path);
            }
            return trailingslashit(get_stylesheet_directory_uri()) . $img_file_url_path;
        }
    }
}
if (!function_exists('pm_get_post_thumbnail_url')) {
    /**
     * 生成文章缩略图地址
     * 如果没有缩略图则直接随机使用"主题设置>文章随机特色图片"中设置的一张
     * 
     * @param mixed|null $post 
     * @param string $size 
     * @return mixed 
     * @throws \Intervention\Image\Exception\NotReadableException 
     * @throws \Intervention\Image\Exception\NotWritableException 
     * @throws \Intervention\Image\Exception\NotSupportedException 
     */
    function pm_get_post_thumbnail_url($post = null, $size = 'thumbnail')
    {
        if (empty($post)) {
            global $post;
        }
        if (is_numeric($post)) {
            $post = get_post($post);
        }



        // $cache_thumb = Image::cache(function ($image) use ($rand_thumb) {
        //     $image->make(file_get_contents($rand_thumb))->resize(300, 200);
        // }, 10);
        // $rand_thumb_data = base64_encode($cache_thumb);
        // $rand_mime_info = getimagesize($rand_thumb);
        // $thumb = 'data: ' .  $rand_mime_info['mime'] . ';base64,' . $rand_thumb_data;
        if (has_post_thumbnail($post)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            if (!empty($thumbnail_src) && !empty($thumbnail_src[0])) {
                if (!_img_is_gif($thumbnail_src[0])) {
                    $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);
                }
                $post_thumbnail_src = $thumbnail_src[0];
            }
        }
        // elseif (_cao('is_post_one_thumbnail', true) && !empty($post->post_content)) {
        //     $output             = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        //     $one_thumbnail_src = (!empty($matches[1][0])) ? $matches[1][0] : null; //获取该图片 src
        //     if (!empty($one_thumbnail_src)) {
        //         $post_thumbnail_src = $one_thumbnail_src;
        //     }
        // }
        //云储存适配
        // if ($size == 'thumbnail' && _cao('is_img_cloud_storage', false)) {
        //     // 判断裁剪模式
        //     $storage_domain = _cao('img_cloud_storage_domain');
        //     $storage_param = _cao('img_cloud_storage_param');
        //     if (strpos($post_thumbnail_src, $storage_domain) !== false) {
        //         $post_thumbnail_src = $post_thumbnail_src . $storage_param;
        //     }
        // }
        return pm_get_size_thumbnail($post_thumbnail_src, $size);
    }
}
if (!function_exists('pm_get_post_media')) {
    function pm_get_post_media($post = null, $size = 'thumbnail')
    {
        if (empty($post)) {
            global $post;
        }
        $_size_px = _cao('post_thumbnail_size');
        if (empty($_size_px)) {
            $_size_px['width']  = 300;
            $_size_px['height'] = 200;
        }
        $src = pm_get_post_thumbnail_url($post, $size);
        //获取比例
        $ratio = ($_size_px['height'] / $_size_px['width']) * 100 . '%';

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
 *  获取当前文章所属的专题
 * 
 * @param int $num 
 * @return void 
 */
function pm_serie_dot($num = 2)
{
    global $post;
    $post_ID = $post->ID;
    $series = get_the_terms($post_ID, 'series');
    $i = 0;
    if ($series && count($series) > 0) {
        echo '<span class="meta-serie-dot">';

        foreach ($series as $v) {
            $i++;
            if ($i > $num) break;
            echo '<a href="' . esc_url(get_term_link($v->term_id)) . '" rel="category">' . esc_html($v->name) . '</a>';
        }

        echo '</span>';
    }
}
