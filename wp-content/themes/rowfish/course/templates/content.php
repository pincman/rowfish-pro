<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:57:56 +0800
 * @Path           : /wp-content/themes/rowfish/course/templates/content.php
 * @Description    : 课程页面内容模块
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

$_widget_wap_position = _cao('show_shop_widget_wap_position', 'bottom');
$is_faq_tab = _cao('is_course_template_help') == '1';
$faq_tab_data = _cao('course_template_help', array());
// 获取文章关联的综合信息
$info = rf_get_post_info();
// 打开课程时默认的课时序号,如果有介绍课时则为介绍课时的序号,如果没有则为第一集,即为 1
$default_chapter_num = is_array($info['course']['intro']) ? 0 : 1;
// 当前课时序号,如果有URL中没有指定则为默认课时
$current_chapter_num = !empty($_GET['chapter']) ? (int)$_GET['chapter'] : $default_chapter_num;
// 如果当前课时序号<1,则把它设置为默认课时序号
if ($current_chapter_num < 1) $current_chapter_num = $default_chapter_num;
// 教程章节列表
$chapters = isset($info['course']['chapters']) ? $info['course']['chapters'] : [];
// 课程关联的文档
$enabled_doc = false;
$doc_content = null;
$doc = '';
if ($current_chapter_num > 0) {
    $current_chapter = $chapters[$current_chapter_num - 1] ?? [];
    // 当前课时关联的文档
    $enabled_doc = !is_null($info['course']['document']) && isset($current_chapter['enabled_doc']) && (bool)$current_chapter['enabled_doc'] && !empty($current_chapter['doc']);
    if ($enabled_doc) {
        $doc = $current_chapter['doc'];
        $doc_content = get_the_content(null, false,  $current_chapter['doc']);
        if (!is_null($doc_content)) {
            $doc_content = apply_filters('the_content', $doc_content);
            $doc_content = str_replace(']]>', ']]&gt;', $doc_content);
        }
    }
}
?>


<div class="single-download-nav single-course-nav-flex">
    <ul class="nav nav-pills" id="pills-tab" role="tablist">

        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if (!$enabled_doc) : ?> active<?php endif; ?>" id="pills-details-tab" data-toggle="pill" data-target="#pills-details" role="tab" aria-controls="pills-details" aria-selected="true"><i class="fab fa-canadian-maple-leaf mr-1"></i><?php _e('序言', 'ripro-v2'); ?></a>
        </li>

        <?php if ($enabled_doc) : ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="pills-doc-tab" data-toggle="pill" data-target="#pills-doc" role="tab" aria-controls="pills-doc" aria-selected="false">
                    <i class="fas fa-book mr-1"></i>文档
                </a>
            </li>
        <?php endif; ?>
        <?php if ($is_faq_tab) : ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="pills-faq-tab" data-toggle="pill" data-target="#pills-faq" role="tab" aria-controls="pills-faq" aria-selected="false"><i class="fas fa-fire-alt mr-1"></i>必读</a>
            </li>
        <?php endif; ?>

    </ul>
    <div class="download-nav-right">
        <?php if (!is_null($info['course']['question'])) : ?>
            <a class='btn btn-outline-primary me-md-2 btn-sm' data-target="<?php echo get_category_link($info['course']['question']); ?>" target='_blank'">
            <i class=" far fa-question-circle mr-1"></i>提问</a>
        <?php endif; ?>
        <?php if (!empty($chapters) && count($chapters) > 0) : ?>
            <button id='goto-video' type='button' class='btn btn-outline-danger me-md-2 btn-sm'><i class='fas fa-angle-double-up mr-1'></i>学习
            </button>
        <?php endif; ?>
        <?php
        if (is_fav_post($info['post_id'])) {
            $arr = array(1 => esc_html__('取消收藏', 'rizhuti-v2'), 2 => ' ok');
        } else {
            $arr = array(1 => esc_html__('收藏', 'rizhuti-v2'), 'rizhuti-v2', 2 => '');
        }
        echo '<button class="go-star-btn btn btn-sm btn-outline-info me-md-2' . $arr[2] . '" data-id="' . $info['post_id'] . '"><i class="far fa-star"></i> ' . $arr[1] . '</button>';
        ?>
    </div>
</div>


<div class="tab-content" id="pills-tabContent">

    <div class="tab-pane fade<?php if (!$enabled_doc) : ?> show active<?php endif; ?>" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab">
        <article id="post-<?php the_ID(); ?>" <?php post_class('article-content'); ?>>
            <div class="container">
                <?php if ($_widget_wap_position == 'top') {
                    shop_widget_wap_position();
                } ?>

                <div class="entry-wrapper">
                    <div class="entry-content u-text-format u-clearfix">

                        <?php
                        the_content();
                        rizhuti_v2_pagination(5);
                        if ($_widget_wap_position == 'bottom') {
                            shop_widget_wap_position();
                        }
                        // if ($copyright = _cao('single_copyright')) {
                        //     echo '<div class="post-note alert alert-info mt-2" role="alert">' . $copyright . '</div>';
                        // }
                        if (_cao('is_single_tags', '1')) {
                            get_template_part('template-parts/content/entry-tags');
                        }
                        // if (_cao('is_single_share', '1')) {
                        //     get_template_part('template-parts/content/entry-share');
                        // }
                        ?>
                    </div>
                </div>
            </div>
        </article>
    </div>

    <?php if ($enabled_doc) : ?>
        <div class="tab-pane fade show active" id="pills-doc" role="tabpanel" aria-labelledby="pills-doc-tab">
            <article id="doc-<?php echo $doc; ?>" <?php post_class('article-content'); ?>>
                <div class="container">
                    <?php if ($_widget_wap_position == 'top') {
                        shop_widget_wap_position();
                    } ?>

                    <div class="entry-wrapper">
                        <div class="entry-content u-text-format u-clearfix">
                            <?php
                            // 当前课时是否上线
                            $chapter_online = isset($current_chapter['online']) && $current_chapter['online'] == '1';
                            // 当前课时的文档是否可以阅读
                            // 只有在课时满足已经上线且(当前课时为独立免费或者整个课程免费或者课程已经被购买)的时候才可以被阅读
                            $enabled_read = false;
                            $chapter_free =  (isset($current_chapter['free']) && $current_chapter['free'] == '1');
                            $check_no_login_play = _cao('free_onlogin_play') == '1' || $info['user']['id'];
                            if ($info['has_permission'] || ($check_no_login_play && $chapter_free)) {
                                $enabled_read = true;
                            }
                            if ($enabled_read) {
                                if (!$chapter_online || strlen(trim(strip_tags($doc_content))) <= 0) {
                                    echo '<div class="alert alert-secondary" role="alert">本集教程文档还在准备中.敬请期待! </div>';
                                } else {
                                    echo $doc_content;
                                }
                            } else {
                                if (!$info['user']['id']) {
                                    $_content = '<div class="ripay-content card mb-4">';
                                    $_content .= '<div class="card-body">';
                                    $_content .= '<h5 class="card-title text-center text-muted"><i class="fa fa-unlock-alt mr-2"></i>' . esc_html__('当前文档需要登录后查看', 'rizhuti-v2') . '</h5>';
                                    $_content .= '<div class="card-body text-center"><a href="' . wp_login_url(curPageURL()) . '" class="btn btn-outline-primary btn-sm my-2"><i class="fa fa-user"></i> ' . esc_html__('立即登录', 'rizhuti-v2') . '</a></div>';
                                    $_content .= '</div></div>';
                                } else {
                                    $_content = '<div class="ripay-content card text-center mb-4">';
                                    $_content .= '<div class="card-body">';
                                    $_content .= '<h5 class="card-title text-muted"><i class="fa fa-lock mr-2"></i>' . esc_html__('您无权查看此文档', 'rizhuti-v2') . '</h5>';
                                    $_content .= '<p class="card-text mb-0 py-4">';
                                    if (!$info['vip_only']) {
                                        if ((int)$info['auth_type'] > 0) {
                                            $_content .= '<span>当前文档需要购买本课程或' . _cao('vip_dopay_name', '订阅本站') . '后才可查看';
                                            $_content .= '<span class="ml-2"></span>' . rf_get_post_vip_auth_badge($info['auth_type']) . '可免费查看';
                                        } else {
                                            $_content .= '<span>当前文档需要购买本课程后才可查看';
                                        }
                                    } else {
                                        $_content .= '<span>当前文档只有' . rf_post_vip_label() . '才可以查看</span>';
                                    }

                                    $_content .= '</p>';

                                    if (!$info['vip_only']) {
                                        if (!$info['user']['id']) {
                                            $_content .= '<a href="' . wp_login_url(curPageURL()) . '" class="btn btn-outline-primary btn-sm mx-2 mt-2"><i class="fa fa-user"></i> ' . esc_html__('登录后购买', 'rizhuti-v2') . '</a>';
                                        } else {
                                            if (site_mycoin('is')) {
                                                $_content .= '<button type="button" class="click-pay-post btn btn-outline-primary btn-sm mx-2 mt-2" data-postid="' . $info['post_id'] . '" data-nonce="' . wp_create_nonce('rizhuti_click_' .  $info['post_id']) . '" data-price="' . $info['price'] . '">支付 ' . convert_site_mycoin($info['price'], 'coin') . ' <i class="' . site_mycoin('icon') . '"></i> ' . site_mycoin('name') . '查看</button>';
                                            } else {
                                                $_content .= '<button type="button" class="click-pay-post btn btn-outline-primary btn-sm mx-2 mt-2" data-postid="' .  $info['post_id']  . '" data-nonce="' . wp_create_nonce('rizhuti_click_' .  $info['post_id']) . '" data-price="' . $info['price'] . '">支付 ￥' . $info['price'] . ' 查看</button>';
                                            }
                                        }
                                    }

                                    if ((int)$info['auth_type'] > 0) {
                                        $_content .= '<a href="' . get_user_page_url('vip') . '" class="btn btn-outline-warning btn-sm mx-2 mt-2"><i class="' . _cao('vip_icon', 'fab fa-codepen') . '"></i> ' . _cao('vip_dopay_name', '订阅本站') . '</a>';
                                    }

                                    $_content .= '</div></div>';
                                }
                                echo $_content;
                            }
                            if ($_widget_wap_position == 'bottom') {
                                shop_widget_wap_position();
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    <?php endif; ?>


    <?php if ($is_faq_tab) : ?>
        <div class="tab-pane fade" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab">

            <div class="accordion" id="accordionhelp">
                <?php foreach ($faq_tab_data as $key => $item) : ?>
                    <div class="card">
                        <div class="card-header" id="heading-<?php echo $key; ?>">
                            <h2 class="mb-0">
                                <button class="btn btn-sm btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $key; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $key; ?>">
                                    <?php echo $item['title']; ?><span class="fa fa-plus"></span><span class="fa fa-minus"></span>
                                </button>

                            </h2>
                        </div>
                        <div id="collapse-<?php echo $key; ?>" class="collapse" aria-labelledby="heading-<?php echo $key; ?>" data-parent="#accordionhelp">
                            <div class="card-body bg-primary text-white">
                                <?php echo $item['desc']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>


</div>