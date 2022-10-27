<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 05:11:32
 * @updated_at: 2021-05-31 02:31:24
 * @description:  媒体文件
 * @homepage: https://pincman.cn
 */

use Intervention\Image\ImageManagerStatic as Image;

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

        // $cache_img =  pm_the_theme_thumb();
        if ($width && $height) {
            $img_uri_arr = explode('/', $src);
            $img_file_arr = explode('.', $img_uri_arr[count($img_uri_arr) - 1]);
            $img_file_name = $img_file_arr[0];
            $img_file_ext = $img_file_arr[1];
            if ($img_file_ext === 'svg') return $src;
            $img_file = "{$img_file_name}-{$width}x{$height}.{$img_file_ext}";
            $img_file_url_path = "assets/images/{$img_file}";
            $img_file_path = __DIR__ . "/../{$img_file_url_path}";
            if (!file_exists($img_file_path)) {
                $img = Image::make(file_get_contents($src))->resize($width, $height);
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

        if (has_post_thumbnail($post)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
            if (!empty($thumbnail_src) && !empty($thumbnail_src[0])) {
                if (!_img_is_gif($thumbnail_src[0])) {
                    $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);
                }
                $post_thumbnail_src = $thumbnail_src[0];
            }
        }
        return pm_get_size_thumbnail($post_thumbnail_src, $size);
    }
}
if (!function_exists('pm_get_post_media')) {
    /**
     * 获取文章缩略图
     * 如果没有缩略图则直接随机使用"主题设置>文章随机特色图片"中设置的一张
     * 
     * @param mixed|null $post 
     * @param string $size 
     * @return string 
     * @throws \Intervention\Image\Exception\NotReadableException 
     * @throws \Intervention\Image\Exception\NotWritableException 
     * @throws \Intervention\Image\Exception\NotSupportedException 
     */
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
 * 上传文件重命名
 * @param mixed $file 
 * @return mixed 
 */
function pm_upload_filter($file)
{
    $time = date("YmdHis");
    $file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    return $file;
}
