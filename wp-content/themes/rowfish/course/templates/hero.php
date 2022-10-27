<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:56:28 +0800
 * @Path           : /wp-content/themes/rowfish/course/templates/hero.php
 * @Description    : 课程页顶部视频组件
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
// 用户组名称列表
$vip_names = rf_get_vip_enabled_names();
// 获取文章关联的综合信息
$info = rf_get_post_info();
// 视频半高背景图
$hero_image = rf_get_hero_image();
$has_intro = isset($info['course']['intro']) && $info['course']['intro'];
// 打开课程时默认的课时序号,如果有介绍课时则为介绍课时的序号,如果没有则为第一集,即为 1
$default_chapter_num = $has_intro ? 0 : 1;
// 当前课时序号,如果有URL中没有指定则为默认课时
$current_chapter_num = !empty($_GET['chapter']) ? (int)$_GET['chapter'] : $default_chapter_num;
// 如果当前课时序号<1,则把它设置为默认课时序号
if ($current_chapter_num < 1) $current_chapter_num = $default_chapter_num;
// 教程章节列表
$chapters = isset($info['course']['chapters']) ? $info['course']['chapters'] : [];
// 视频url地址
$js_video_url = '';

if ($current_chapter_num < 1) {
    // 在当前课时序号为0时,展示介绍课时
    $js_video_url = $info['course']['intro']['video'];
    $_content = '<div class="content-do-video"><div class="views text-muted"></div></div>';
} else {
    // if ($chapter_online && isset($current_chapter['video'])) {
    //     if ($info['has_permission']) $enabled_play = true;
    //     $chapter_free =  (isset($current_chapter['free']) && $current_chapter['free'] == '1');
    //     if ($check_no_login_play && $chapter_free) $enabled_play = true;
    // }
    // 默认展示无权播放
    $_content = '<div class="content-do-video"><div class="views text-muted"><span class="badge badge-light note"><i class="fa fa-info-circle"></i> ' . esc_html__('暂无权限播放', 'rizhuti-v2') . '</span>';
    // 当前课时
    $current_chapter = isset($chapters[$current_chapter_num - 1]) ? $chapters[$current_chapter_num - 1] : null;
    if (!is_null($current_chapter)) {
        // 当前课时是否上线
        $chapter_online = isset($current_chapter['online']) && $current_chapter['online'] == '1';
        // 当前课时的视频是否可以被播放
        // 只有在课时满足已经上线且(当前课时为独立免费或者整个课程免费或者课程已经被购买)的时候才可以被播放
        $enabled_play = false;
        $chapter_free =  (isset($current_chapter['free']) && $current_chapter['free'] == '1');
        $check_no_login_play = _cao('free_onlogin_play') == '1' || $info['user']['id'];
        if ($info['has_permission'] || ($check_no_login_play && $chapter_free)) {
            $enabled_play = true;
        }
        if ($enabled_play) {
            if (!$chapter_online || !$current_chapter['video']) {
                $_content .= '当前课时还未上线,尽请期待';
            } else {
                $js_video_url = $current_chapter['video'];
                $_content .= '</div><div class="mb-2 text-muted">';
                if (isset($info['auth_type']) && (int)$info['auth_type'] != '0') {
                    $_content .= '<span class="ml-2"></span>' . rf_get_post_vip_auth_badge($info['auth_type']) . esc_html__('视频可播放', 'rizhuti-v2');
                }
            }
        } else {
            if (!$info['user']['id']) {
                $_content .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-primary btn-rounded btn-sm my-2"><i class="fa fa-user"></i> ' . esc_html__('登录后才可播放', 'rizhuti-v2') . '</a>';
            } else {
                if ((int)$info['auth_type'] > 0) {
                    $_content .= $info['vip_only'] ? '当前视频只有' . rf_post_vip_label() . '才可观看' : '当前视频需要购买本课程或' . _cao('vip_dopay_name', '订阅本站') . '后才可观看';
                }
                $_content .= '<div class="mb-4 text-center">';
                if (!$info['vip_only']) {
                    if (site_mycoin('is')) {
                        $_content .= '<button type="button" class="click-pay-post btn btn-primary btn-rounded btn-sm mx-2 mt-lg-4"';
                        $_content .= ' data-postid="' . $info['post_id'] . '"';
                        $_content .= ' data-nonce="' . wp_create_nonce('rizhuti_click_' . $info['post_id']) . '"';
                        $_content .= ' data-price="' . $info['price'] . '">支付';
                        $_content .= convert_site_mycoin($info['price'], 'coin');
                        $_content .= ' <i class="' . site_mycoin('icon') . '"></i> ' . site_mycoin('name') . '播放</button>';
                    } else {
                        $_content .= '<button type="button" class="click-pay-post btn btn-primary btn-rounded btn-sm mx-2 mt-lg-4"';
                        $_content .= ' data-postid="' . $info['post_id'] . '"';
                        $_content .= ' data-nonce="' . wp_create_nonce('rizhuti_click_' . $info['post_id']) . '"';
                        $_content .= ' data-price="' . $info['price'] . '">支付 ￥' . $info['price'] . ' 播放</button>';
                    }
                }
                $_content .= '<a href="' . get_user_page_url('vip') . '" class="btn btn-outline-success btn-rounded btn-sm mx-2 mt-lg-4"><i class="' . _cao('vip_icon', 'fab fa-codepen') . '"></i> ' . _cao('vip_dopay_name', '订阅本站') . '</a>';
            }
        }
    }

    $_content .= '</div></div>';
}

// 视频选集
$_content2 =  '<p class="head-con text-muted"><i class="far fa-list-alt"></i> ' . esc_html__('课程选集', 'rizhuti-v2') . ' (' . count($chapters) . ')<b class="small text-muted">' . get_the_title() . '</b></p>';
$_content2 .=  '';
$_content2 .= '<ul class="list-box">';
if ($info['course']['intro']) {
    $v_name  = !empty($intro_title) ?  $info['course']['intro']['title'] : '课程介绍';
    $v_url = !empty($js_video_url) ? $js_video_url : '';
    $_content2 .= '<li>';
    $actived = ($current_chapter_num === 0) ? ' active' : '';
    $disabled = !empty($js_video_url) ? '' : 'disabled';
    $_content2 .= '<a href="' . remove_query_arg("chapter") . '" class="switch-video' . esc_attr($actived) . '" data-index="0" data-url="' . $v_url . '"' . $disabled . '><span class="mr-2">' . $v_name . '</span></a >';
    $_content2 .= '</li>';
}

foreach ($chapters as $key => $v) {
    $v_name = (!empty($v['title'])) ?  $v['title'] : '第' . ($key + 1) . '集';
    $v_online =  $v['online'] === '1';
    $offline_class = !$v_online ? ' offline' : '';
    $v_url = !empty($js_video_url) ? $js_video_url : '';
    $_content2 .= '<li>';
    $actived = ($key == $current_chapter_num - 1) ? ' active' : '';
    $disabled = !empty($js_video_url) ? '' : 'disabled';
    $_content2 .= '<a href="' . add_query_arg("chapter", $key + 1) . '" class="switch-video' .    $offline_class . esc_attr($actived) . '" data-index="' . ($key + 1) . '" data-url="' . $v_url . '"' . $disabled . '><span class="mr-2">第' . ($key + 1) . '集: </span><span>' . $v_name . '</span></a >';
    $_content2 .= '</li>';
}
$_content2 .= '</ul>';

$classes_col = ['col-lg-9 col-12', 'col-lg-3 col-12'];

?>

<div class="hero lazyload visible" data-bg="<?php echo esc_url($hero_image); ?>">
    <div class="hero-media video">
        <div class="container-lg">
            <div class="row no-gutters">
                <div class="<?php echo esc_attr($classes_col[0]); ?>">
                    <div id="rizhuti-video"></div>
                </div>
                <?php if (!empty($_content2)) { ?>
                    <div class="<?php echo esc_attr($classes_col[1]); ?>">
                        <div id="rizhuti-video-page"><?php echo $_content2; ?></div>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>
<!-- JS脚本 -->

<script type="text/javascript">
    jQuery(function() {
        'use strict';
        var js_video_url = '<?php echo htmlspecialchars_decode($js_video_url); ?>';
        var js_video_content = '<?php echo $_content; ?>';
        const dp = new DPlayer({
            container: document.getElementById("rizhuti-video"),
            theme: "#fd7e14",
            screenshot: !1,
            video: {
                url: js_video_url,
                type: "auto",
                pic: ""
            }
        });
        var video_vh = "inherit";
        if ($(".dplayer-video").bind("loadedmetadata", function() {
                var e = this.videoWidth || 0,
                    i = this.videoHeight || 0,
                    a = $("#rizhuti-video").width();
                i > e && (video_vh = e / i * a, $(".dplayer-video").css("max-height", video_vh))
            }), "" == js_video_url) {
            var mask = $(".dplayer-mask");
            mask.show(), mask.hasClass("content-do-video") || (mask.append(js_video_content), $(".dplayer-video-wrap").addClass("video-filter"))
        } else {
            var notice = $(".dplayer-notice");
            notice.hasClass("dplayer-notice") && (notice.css("opacity", "0.8"), notice.append('<i class="fa fa-unlock-alt"></i> 您已获得当前视频播放权限'), setTimeout(function() {
                notice.css("opacity", "0")
            }, 2e3)), dp.on("fullscreen", function() {
                $(".dplayer-video").css("max-height", "unset")
            }), dp.on("fullscreen_cancel", function() {
                $(".dplayer-video").css("max-height", video_vh)
            })
        }
        var vpage = $("#rizhuti-video-page .switch-video");
        vpage.on("click", function() {
            var e = $(this);
            vpage.removeClass("active"), e.addClass("active"), dp.switchVideo({
                url: e.data("url"),
                type: "auto"
            })
        });
    });
</script>