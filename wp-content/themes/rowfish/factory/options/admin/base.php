<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:23:20 +0800
 * @Path           : /wp-content/themes/rowfish/factory/options/admin/base.php
 * @Description    : 修改rizhuti-v2主题设置中的选项(基本设置)
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_set_admin_base_meta')) {
    /**
     * 后台-主题设置-基本设置
     */
    function rf_set_admin_base_meta()
    {
        if (
            count(CSF::$args['sections']['_riprov2_options']) <= 4
            || !isset(CSF::$args['sections']['_riprov2_options'][3]['fields'])
        ) {
            return;
        }
        $base_fields = CSF::$args['sections']['_riprov2_options'][0]['fields'];
        $top_fields = CSF::$args['sections']['_riprov2_options'][1]['fields'];
        $single_fields = CSF::$args['sections']['_riprov2_options'][3]['fields'];
        $download_index = null;
        foreach ($base_fields as $key => $value) {
            if (isset($value['id'])) {
                if (isEditorMD() && $value['id'] === 'disable_gutenberg_edit') {
                    unset($base_fields[$key]);
                }
                if ($value['id'] === 'is_site_question') {
                    if (isAnsPress()) {
                        $base_fields[$key] = [
                            'id' => 'use_anspress',
                            'type' => 'switcher',
                            'title' => esc_html__('是否整合AnsPress', 'rizhuti-v2'),
                            'desc' => esc_html__('开启后,问答系统将与课程模块深度整合', 'rizhuti-v2'),
                            'default' => true,
                        ];
                    } else {
                        unset($base_fields[$key]);
                    }
                }
                if ($value['id'] === 'is_site_tougao') {
                    $attr = isset($value['attributes']) ? $value['attributes'] : [];
                    $base_fields[$key]['class'] = 'hidden';
                    $base_fields[$key]['attributes'] = array_merge($attr, ['type' => 'hidden']);
                }
                if ($value['id'] === 'download_number_limit') {
                    $download_index = $key;
                }
            }
        }
        if (is_null($download_index) && count($top_fields) > 2) {
            $base_fields = array_merge([
                [
                    'id' => 'download_global_limit',
                    'type' => 'switcher',
                    'title' => '全局下载限速',
                    'label' => '子主题会强制关闭rizhuti-v2中针对每个会员的下载限速和数量限制,如果带宽不够可以开启此选项',
                    'default' => false,
                ],
                [
                    'id' => 'download_speed_limit',
                    'type' => 'text',
                    'title' => '下载限速',
                    'subtitle' => '0为不限速,以mb为单位(宽带够用或者使用云存储则不需要设置)',
                    'default' => 0,
                    'dependency' => array('download_global_limit', '==', '1'),
                ],
            ], $base_fields);
            array_splice($base_fields, 0, 0, [
                $top_fields[0],
                $top_fields[1]
            ]);
            foreach ($single_fields as $key => $value) {
                if (isset($value['id']) && $value['id'] === 'single_shop_template_help') {
                    unset($value['dependency']);
                    $value['default'] = [$value['default'][0], $value['default'][3], $value['default'][4]];
                    array_push($base_fields, array_merge($value, ['subtitle' => '请现在"布局风格中"启用"资源类文章启用新布局"',]));
                }
            }
            CSF::$args['sections']['_riprov2_options'][0]['fields'] = $base_fields;
            CSF::set_used_fields($base_fields);
            unset($top_fields[0]);
            unset($top_fields[1]);
            CSF::$args['sections']['_riprov2_options'][1]['fields'] = $top_fields;
            CSF::set_used_fields($top_fields);
        }
    }
}

