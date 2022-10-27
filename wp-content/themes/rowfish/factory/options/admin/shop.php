<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:24:43 +0800
 * @Path           : /wp-content/themes/rowfish/factory/options/admin/shop.php
 * @Description    : 修改rizhuti-v2主题设置中的选项(商城设置)
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_set_admin_shop_meta')) {
    /**
     * 后台-主题设置-商城设置
     */
    function rf_set_admin_shop_meta()
    {
        $vip_services = [
            'no_vip' => '可以学习免费课程,查看免费文档以及下载免费资源',
            '31_vip' => '一月内可享受本站所有的课程学习,文档查看,资源下载以及问答服务',
            '365_vip' => '一年内可享受本站所有的课程学习,文档查看,资源下载以及问答服务',
            '3600_vip' => '永久享受所有的课程学习,文档查看,资源下载以及问答服务,可加入订阅者QQ群学习'
        ];
        $shop_fields_index = null;
        foreach (CSF::$args['sections']['_riprov2_options'] as $key => $value) {
            if (isset($value['parent']) && $value['parent'] === 'shop_fields') {
                $shop_fields_index = $key;
                break;
            }
        }
        if (!is_null($shop_fields_index)) {
            $shop_fields = CSF::$args['sections']['_riprov2_options'][$shop_fields_index]['fields'];
            $show_shop_widget_wap_position_index = null;
            if (count(array_filter($shop_fields, function ($arr) {
                    return $arr['id'] === 'vip_name';
                })) < 1) {
                $shop_fields = array_merge([
                    [
                        'id' => 'vip_name',
                        'type' => 'text',
                        'title' => '通用会员名称',
                        'desc' => '这个是通用的名称,用于前台显示',
                        'default' => '订阅者',
                    ],
                    [
                        'id' => 'vip_dopay_name',
                        'type' => 'text',
                        'title' => '会员开通行为',
                        'desc' => '用于显示开通会员的行为',
                        'default' => '订阅本站',
                    ],
                    [
                        'id' => 'vip_icon',
                        'type' => 'icon',
                        'title' => '会员标识图标',
                        'desc' => '在会员称谓前显示的图标',
                        'default' => 'fab fa-codepen',
                    ]
                ], $shop_fields);
                foreach ($shop_fields as $key => $value) {
                    if (isset($value['id'])) {
                        if (
                            $value['id'] == 'is_site_shop' ||
                            $value['id'] == 'is_rizhuti_v2_nologin_pay' ||
                            $value['id'] == 'is_site_mycoin'
                        ) {
                            $attr = isset($value['attributes']) ? $value['attributes'] : [];
                            $shop_fields[$key]['class'] = 'hidden';
                            $shop_fields[$key]['attributes'] = array_merge($attr, ['type' => 'hidden']);
                        }
                        if ($value['id'] == 'show_shop_widget_wap_position') {
                            $show_shop_widget_wap_position_index = $key;
                        }
                    }
                }
                $vip_fields_index = $shop_fields_index + 2;
                $vip_fields = CSF::$args['sections']['_riprov2_options'][$vip_fields_index]['fields'];
                foreach ($vip_fields as $key => $value) {
                    if (isset($value['id'])) {
                        if ($value['id'] == 'is_site_author_aff') {
                            $attr = isset($value['attributes']) ? $value['attributes'] : [];
                            $vip_fields[$key]['class'] = 'hidden';
                            $vip_fields[$key]['attributes'] = array_merge($attr, ['type' => 'hidden']);
                        }
                    }
                    if (isset($value['id']) && $value['id'] === 'site_vip_options') {
                        foreach ($value['tabs'] as $k => $v) {
                            if (count(array_filter($v['fields'], function ($arr) use ($v) {
                                    return $arr['id'] === '0_vip_downnum';
                                })) > 0) {
                                unset($v['fields'][0]);
                                unset($v['fields'][1]);
                            }
                            if (count(array_filter($v['fields'], function ($arr) use ($v) {
                                    return $arr['id'] === $v['id'] . '_service';
                                })) < 1) {
                                if ($v['id'] !== 'no_vip') {
                                    unset($v['fields'][1]);
                                    unset($v['fields'][2]);
                                    $v['fields'] = array_merge([
                                        [
                                            'id' => $v['id'] . '_enabled',
                                            'type' => 'switcher',
                                            'title' => esc_html__('启用此会员组', 'rizhuti-v2'),
                                            'default' => $v['id'] !== '31_vip',
                                        ]
                                    ], $v['fields']);
                                }
                                array_splice($v['fields'], $v['id'] !== 'no_vip' ? 1 : 0, 0, [[
                                    'id' => $v['id'] . '_service',
                                    'type' => 'textarea',
                                    'title' => '服务描述',
                                    'desc' => '在用户中心内的服务表格显示',
                                    'default' => $vip_services[$v['id']],
                                ]]);
                            }
                            $vip_fields[$key]['tabs'][$k] = $v;
                        }
                    }
                }
                $source_fields_index = $shop_fields_index + 3;
                $source_fields = CSF::$args['sections']['_riprov2_options'][$source_fields_index]['fields'];
                if (!is_null($show_shop_widget_wap_position_index)) {
                    $source_fields = array_merge([$shop_fields[$show_shop_widget_wap_position_index]], $source_fields);
                }
                unset($shop_fields[$show_shop_widget_wap_position_index]);
                CSF::$args['sections']['_riprov2_options'][$source_fields_index]['title'] = '收费资源设置';
                CSF::$args['sections']['_riprov2_options'][$source_fields_index]['fields'] = $source_fields;
                CSF::set_used_fields($source_fields);
                unset(CSF::$args['sections']['_riprov2_options'][$vip_fields_index]);
                CSF::$args['sections']['_riprov2_options'][$shop_fields_index]['title'] = '会员与组设置';
                CSF::$args['sections']['_riprov2_options'][$shop_fields_index]['fields'] = array_merge($shop_fields, $vip_fields);
                CSF::set_used_fields($shop_fields);
            }
        }
    }
}

