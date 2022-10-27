<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:25:35 +0800
 * @Path           : /wp-content/themes/rowfish/factory/helpers/post.php
 * @Description    : 文章相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

use Intervention\Image\ImageManagerStatic as Image;

defined('ABSPATH') || exit;
/**
 * 检测当前文章ID与另一个文章的ID是否相同
 * @param mixed $current_id
 * @param mixed|null $post_ID
 * @return bool
 */
if (!function_exists('rf_check_is_current_post')) {
    function rf_check_is_current_post($current_id, $post_ID = null)
    {
        if (empty($post_ID)) {
            global $post;
            $post_ID = $post->ID;
        }
        return $current_id == $post_ID;
    }
}
if (!function_exists('rf_get_post_id')) {
    /**
     * 获取文章的ID
     *
     * @return int|null
     */
    function rf_get_post_id()
    {
        global $post;
        if (!$post) return null;
        return $post->ID;
    }
}

if (!function_exists('rf_get_query_post')) {
    /**
     * 获取一个文章数据对象
     * @return array|WP_Post|null
     */
    function rf_get_query_post()
    {
        global $post;
        if (empty($post)) {
            if (array_key_exists('post', $_GET)) {
                $post = get_post($_GET['post']);
            } elseif (array_key_exists('post_type', $_GET)) {
                $object = new stdClass();
                $object->post_type = $_GET['post_type'];
                return new WP_Post($object);
            } else {
                return null;
            }
        }
        return $post;
    }
}
if (!function_exists('rf_get_post_info')) {
    /**
     * 核心函数,获取一个文章的所有信息
     * @param null $post_id
     * @return array
     */
    function rf_get_post_info($post_id = null)
    {
        $data = [
            'post_id' => null,
            'post_type' => 'post',
            'is_paid' => 0,
            'has_permission' => false,
            'is_course' => false,
            'summary' => null,
            'user' => ['id' => null],
            'course' => [],
        ];
        global $current_user;
        $data['post_id'] = is_null($post_id) ? rf_get_post_id() : $post_id;
        if ($data['post_id']) {
            $data['is_recommand'] = (bool)get_post_meta($data['post_id'], 'is_recommand', true);
            $disable_top_thumbnail = (bool)get_post_meta($data['post_id'], 'disable_top_thumbnail', true);
            $data['top_thumbnail'] = (bool)_cao('is_single_template_top_img', true) && !$disable_top_thumbnail;
            $block_style = _cao('archive_list_block_style', 'fix');
            if ($block_style == 'fix') {
                $block_style = rand(0, 2) >= 1 ? 'big' : 'small';
            }
            $meta_block_style = get_post_meta($data['post_id'], 'archive_block_style', true);
            if (!empty($meta_block_style) && $meta_block_style != '1') {
                $block_style = $meta_block_style == '2' ? 'small' : 'big';
            }
            $data['block_style'] = $block_style;
            $merge_thumbnail = false;
            if ($data['block_style'] == 'big') {
                $merge_thumbnail = (bool)_cao('archive_list_merge_thumbnail', false);
                $meta_merge_thumbnail = get_post_meta($data['post_id'], 'is_merge_thumbnail', true);
                if (!empty($meta_merge_thumbnail) && $meta_merge_thumbnail != '1') {
                    $merge_thumbnail = $meta_merge_thumbnail == '2';
                }
            }
            $data['merge_thumbnail'] = $merge_thumbnail;
            $data['is_course'] = get_post_type($data['post_id']) === 'course';
            $wppay_price = get_post_meta($data['post_id'], 'wppay_price', true);
            // 文章价格
            $data['price'] = empty($wppay_price) ? 0 : (float)$wppay_price;
            $data['auth_type'] = (int)get_post_meta($data['post_id'], 'wppay_vip_auth', true);
            // 是否免费
            $data['is_free'] = $data['price'] <= 0 && $data['auth_type'] == 0;
            if ($data['is_course']) {
                $data['is_free'] = empty(get_post_meta($data['post_id'], 'shop_enabled', true)) || $data['is_free'];
            }
            // 是否VIP专属
            $data['vip_only'] = $data['price'] <= 0 && $data['auth_type'] > 0;
            // 非课程文章的文章类型
            $data['source_type'] = null;
            // 文章描述
            $data['summary'] = get_post_meta($data['post_id'], 'content_summary', true);
            if (isset($current_user->ID) && $current_user->ID > 0) {
                $data['user']['id'] = $current_user->ID;
                $auth_options = rf_get_vip_options();
                $RiClass = new RiClass($data['post_id'], $data['user']['id']);
                $IS_PAID = $RiClass->is_pay_post();
                $data['is_free'] = $data['is_free'] || $IS_PAID == 4;
                // 当前用户所属用户组类型
                $data['user']['auth_type'] = (int)_get_user_vip_type($data['user']['id']);
                // 当前用户所属用户组名称
                $data['user']['auth_name'] = $auth_options[$data['user']['auth_type']]['name'];
                // 文章是否已购买
                $data['is_paid'] = in_array($IS_PAID, [1, 2, 3]);
            }
            if (!$data['is_course']) {
                // 文章类型
                $data['post_type'] = get_post_meta($data['post_id'], 'wppay_type', true);
                $data['has_permission'] = (_cao('free_onlogin_down') == '1' && $data['is_free']) ||
                    (!_cao('free_onlogin_down') && $data['user']['id'] && $data['is_free']) ||
                    $data['is_paid'];
            } else {
                $data['has_permission'] = (_cao('free_onlogin_play') == '1' && $data['is_free']) ||
                    (!_cao('free_onlogin_play') && $data['user']['id'] && $data['is_free']) ||
                    $data['is_paid'];
                $data['course']['paynum'] = 0;
                if (!empty(get_post_meta($data['post_id'], '_paynum', true))) {
                    $course['paynum'] = get_post_meta($data['post_id'], '_paynum', true);
                }
                $data['course']['status'] = get_post_meta($data['post_id'], 'course_status', true);
                $data['course']['level'] = get_post_meta($data['post_id'], 'course_level', true);
                $data['course']['intro'] = null;
                $data['course']['document'] = null;
                $data['course']['question'] = null;
                if (isDocsPress() && !empty(get_post_meta($data['post_id'], 'course_document', true))) {
                    $data['course']['document'] = (int)get_post_meta($data['post_id'], 'course_document', true);
                }
                if (isAnsPress() && !empty(get_post_meta($data['post_id'], 'course_question', true))) {
                    $data['course']['question'] = (int)get_post_meta($data['post_id'], 'course_question', true);
                }
                $data['course']['chapters'] = get_post_meta($data['post_id'], 'course_chapter_info', true);
                if (!is_array($data['course']['chapters'])) $data['course']['chapters'] = [];
                if (get_post_meta($data['post_id'], 'course_intro', true) == '1') {
                    $data['course']['intro'] = [
                        'title' => get_post_meta($data['post_id'], 'course_intro_title', true),
                        'video' => get_post_meta($data['post_id'], 'course_intro_video', true),
                    ];
                }
                $data['course']['download'] = get_post_meta($data['post_id'], 'course_wppay_down', true);
                if (!is_array($data['course']['download'])) $data['course']['download'] = [];
            }
        }

        return $data;
    }
}
if (!function_exists('rf_rand_theme_thumb')) {
    /**
     * 获取一张后台主题设置中添加的随机缩略图
     * @return mixed|string
     */
    function rf_rand_theme_thumb()
    {
        $rand_images_option = _cao('default_thumb_images');
        if (is_array($rand_images_option) && count($rand_images_option) > 0) {
            $rand_images = array_filter($rand_images_option, function ($img) {
                return $img && is_array($img) && $img['url'] && !empty($img['url']);
            });
            if (count($rand_images)) {
                $rand_post_image = $rand_images[rand(0, count($rand_images) - 1)];
            }
        }
        return $rand_post_image ? $rand_post_image['url'] : get_template_directory_uri() . '/assets/img/thumb.jpg';
    }
}
if (!function_exists('rf_get_thumbnail_size')) {
    /**
     * 根据设置获取缩略图的尺寸
     * @param string $size
     * @return int[]|string[]
     */
    function rf_get_thumbnail_size($size = 'thumbnail')
    {
        $width = 255;
        $height = 170;
        $_size_px = _cao('post_thumbnail_size');
        if (is_array($_size_px)) {
            if (isset($_size_px['width'])) $width = (int)$_size_px['width'];
            if (isset($_size_px['height'])) $height = (int)$_size_px['height'];
        }
        if ($size !== 'thumbnail') {
            if (is_array($size)) {
                if (isset($size['width']) && is_numeric($size['width'])) $width = $size['width'];
                if (isset($size['height']) && is_numeric($size['height'])) $height = $size['height'];
            } elseif (is_numeric($size)) {
                $width = $size;
                $height = $size;
            }
        }
        return [$width, $height];
    }
}
if (!function_exists('rf_cute_thumbnail')) {
    /**
     * 裁剪缩略图并保存在/uploads目录下
     * @param null $src
     * @param string $size
     * @return mixed|string|void|null
     */
    function rf_cute_thumbnail($src = null, $size = 'thumbnail')
    {
        if (!$src) $src = rf_rand_theme_thumb();
        if ($size === 'full' || !$size) return $src;
        list($width, $height) = rf_get_thumbnail_size($size);

        //云储存适配
        if (_cao('is_img_cloud_storage', false)) {
            // 判断裁剪模式
            $storage_domain = _cao('img_cloud_storage_domain');
            $storage_param = _cao('img_cloud_storage_param');
            if (strpos($src, $storage_domain) !== false) {
                return $src . $storage_param;
            }
        }
        $path_parts = pathinfo($src);
        if (!isset($path_parts['extension']) || $path_parts['extension'] === 'svg') return $src;
        $img_file = "{$path_parts['filename']}-{$width}x{$height}.{$path_parts['extension']}";
        $img_dir = 'uploads/images/rowfish';
        $img_dir_path = ABSPATH . $img_dir;
        if (!is_dir($img_dir_path)) mkdir($img_dir_path, 0777, true);
        $img_file_path = "{$img_dir_path}/{$img_file}";
        if (!file_exists($img_file_path)) {
            $img = Image::make(file_get_contents($src))->resize($width, $height);
            $img->save($img_file_path);
        }
        return site_url("{$img_dir}/{$img_file}");
        // return trailingslashit(get_stylesheet_directory_uri()) . $img_file_url_path;
    }
}
if (!function_exists('rf_get_post_thumbnail_url')) {
    /**
     * 获取裁剪后的缩略图url
     * @param null $post
     * @param string $size
     * @return mixed|string|void|null
     */
    function rf_get_post_thumbnail_url($post = null, $size = 'thumbnail')
    {
        if (empty($post)) {
            global $post;
        }

        if (is_numeric($post)) {
            $post = get_post($post);
        }

        $post_thumbnail_src = null;
        if (has_post_thumbnail($post)) {
            $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');

            if (isset($thumbnail_src) && !empty($thumbnail_src[0])) {
                $post_thumbnail_src = $thumbnail_src[0];
            }
        }
        // 自动抓取第一张图片作为缩略图
        // elseif (_cao('is_post_one_thumbnail', true) && !empty($post->post_content)) {
        //     $post_thumbnail_src = '';
        //     $output             = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        //     $post_thumbnail_src = (!empty($matches[1][0])) ? $matches[1][0] : null; //获取该图片 src
        // }

        return rf_cute_thumbnail($post_thumbnail_src, $size);
    }
}
if (!function_exists('rf_get_post_media')) {
    /**
     * 计算并裁剪后输出缩略图
     * @param null $post
     * @param string $size
     * @param false $real_size
     * @return string
     */
    function rf_get_post_media($post = null, $size = 'thumbnail', $real_size = false)
    {
        if (empty($post)) {
            global $post;
        }
        list($width, $height) = rf_get_thumbnail_size($size);
        $src = rf_get_post_thumbnail_url($post, $size);

        //获取比例
        $ratio = ($height / $width) * 100 . '%';
        // $realSize = $real_size ? ' style="width:' . $width . 'px;height:' . $height . 'px;"' : '';
        $img = '<div class="entry-media">';
        $img .= '<div class="placeholder" style="padding-bottom: ' . esc_attr($ratio) . '">';
        $img .= '<a href="' . get_permalink($post->ID) . '" title="' . get_the_title($post->ID) . '" rel="nofollow noopener noreferrer">';
        $img .= '<img class="lazyload" data-src="' . $src . '" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" alt="' . get_the_title() . '" />';

        $img .= '</a>';
        $img .= '</div>';
        $img .= '</div>';
        return $img;
    }
}
if (!function_exists('rf_get_hero_image')) {
    /**
     * 获取文章/课程顶部视频或半高背景图
     * 如果没有设置则返回后台主题设置中配置的随机半高图
     * @return mixed|string|void|null
     */
    function rf_get_hero_image()
    {
        $post_id = rf_get_post_id();
        $hero_image = null;
        $post_hero_image = $post_id ? get_post_meta($post_id, 'hero_image', true) : null;
        $global_hero_image = null;
        $global_options_images = _cao('default_hero_images');
        if (is_array($global_options_images) && count($global_options_images) > 0) {
            $global_images = array_filter($global_options_images, function ($img) {
                return $img && is_array($img) && $img['url'] && !empty($img['url']);
            });
            if (count($global_images)) {
                $global_hero_image = $global_images[rand(0, count($global_images) - 1)];
            }
        }
        if ($post_hero_image) $hero_image = $post_hero_image;
        else if ($global_hero_image && isset($global_hero_image['url'])) $hero_image = $global_hero_image['url'];
        else $hero_image = rf_get_post_thumbnail_url(null, 'full');
        return $hero_image;
    }
}
if (!function_exists('rf_get_comment_time')) {
    /**
     * 获取评论时间
     * @param null $comment
     * @param string $format
     * @param false $gmt
     * @param bool $translate
     * @return mixed|void
     */
    function rf_get_comment_time($comment = null, $format = '', $gmt = false, $translate = true)
    {
        $comment = is_null($comment) ? get_comment() : $comment;

        $comment_date = $gmt ? $comment->comment_date_gmt : $comment->comment_date;

        $_format = !empty($format) ? $format : get_option('time_format');

        $date = mysql2date($_format, $comment_date, $translate);

        return apply_filters('get_comment_time', $date, $format, $gmt, $translate, $comment);
    }
}
