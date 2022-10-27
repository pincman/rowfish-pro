<?php

require_once __DIR__ . '/base.php';
require_once __DIR__ . '/image.php';
require_once __DIR__ . '/content.php';
require_once __DIR__ . '/../../../course/options/admin.php';
require_once __DIR__ . '/shop.php';
function rf_change_admin_options()
{
    rf_set_admin_base_meta();
    rf_set_admin_image_meta();
    rf_set_admin_content_meta();
    rf_set_admin_course_meta();
    rf_set_admin_shop_meta();
}

function rf_update_admin_options()
{
    update_option(_OPTIONS_PRE, array_merge(get_option(_OPTIONS_PRE), [
        'archive_item_style' => 'list',
        'is_compare_options_to_global' => 0,
        'is_single_shop_template' => 1,
        'is_site_tougao' => 0,
        'is_site_shop' => '1',
        'is_compare_options_to_global' => '0',
        'is_site_mycoin' => 0,
        'is_rizhuti_v2_nologin_pay' => 0,
        'is_site_author_aff' => 0
    ]));
}

add_action('after_setup_theme', 'rf_change_admin_options', -99);
add_action('init', 'rf_change_admin_options', -99);
add_action('switch_theme', 'rf_change_admin_options', -99);
add_action('after_setup_theme', 'rf_update_admin_options');
add_action('wp_ajax_csf__riprov2_options_ajax_save', 'rf_update_admin_options');
