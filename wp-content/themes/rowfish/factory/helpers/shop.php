<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 22:15:40 +0800
 * @Path           : /wp-content/themes/rowfish/factory/helpers/shop.php
 * @Description    : 商城相关函数
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */

if (!function_exists('rf_get_base_vip_name')) {
    /**
     * VIP用户基础称谓
     * @return mixed|string|null
     */
    function rf_get_base_vip_name()
    {
        return !empty(_cao('vip_name')) ? _cao('vip_name') : 'VIP';
    }
}
if (!function_exists('rf_get_vip_icon')) {
    /**
     * 会员图标标识
     * @param string $classes
     * @return string
     */
    function rf_get_vip_icon($classes = '')
    {
        $icon = _cao('vip_icon', 'fab fa-codepen');
        return "<i class='" . $icon . $classes . "'></i>";
    }
}
if (!function_exists('rf_get_vip_options')) {
    /**
     * 获取所有会员组信息(包含普通用户组)
     * @return array
     */
    function rf_get_vip_options()
    {
        global $ri_vip_options;
        $cao_opt = _cao('site_vip_options');
        $vip_opt = [];
        foreach ($ri_vip_options as $index => $opt) {
            $key = $index == 0 ? 'no' : $index;
            $service = (isset($cao_opt[$key . '_vip_service'])) ? $cao_opt[$key . '_vip_service'] : '';
            $price = (isset($cao_opt[$key . '_vip_price'])) ? $cao_opt[$key . '_vip_price'] : 0;
            $enabled = (isset($cao_opt[$key . '_vip_enabled'])) ? (bool)$cao_opt[$key . '_vip_enabled'] : false;
            $aff_ratio = (isset($cao_opt[$key . '_vip_aff_ratio'])) ? (float)$cao_opt[$key . '_vip_aff_ratio'] : 0;
            $author_aff_ratio = (isset($cao_opt[$key . '_vip_author_aff_ratio'])) ? (float)$cao_opt[$key . '_vip_author_aff_ratio'] : 0;
            $vip_opt[$index] = [
                'name' => $opt, //会员组名称
                'enabled' => $enabled,
                'day' => (int)$key, //会员有效天数
                'price' => $price, //会员价格
                'service' => $service,
                'aff_ratio' => $aff_ratio, //推广佣金
                'author_aff_ratio' => $author_aff_ratio,
            ];
            switch ($index) {
                case 31:
                    $vip_opt[$index]['name'] = '月费' . rf_get_base_vip_name();
                    break;
                case 365:
                    $vip_opt[$index]['name'] = '年费' . rf_get_base_vip_name();
                    break;
                case 3600:
                    $vip_opt[$index]['name'] = '永久' . rf_get_base_vip_name();
                    break;
            }
            if ($index == 0) {
                unset($vip_opt[$index]['enabled']);
            }
        }

        return $vip_opt;
    }
}
if (!function_exists('rf_lowest_vip')) {
    /**
     * 获取启用的最低等级的会员组,如果没有则返回普通用户组
     * @return mixed
     */
    function rf_lowest_vip()
    {
        $options = rf_get_vip_options();
        foreach ($options as $key => $value) {
            if (isset($value['enabled']) && $value['enabled']) return $value;
        }
        return $options[0];
    }
}
if (!function_exists('rf_vip_options_map')) {
    /**
     * 返回会员组的索引构成的数组
     * @return string[]
     */
    function rf_vip_options_map()
    {
        return ['0' => '0', '1' => '31', '2' => '365', '3' => '3600'];
    }
}
if (!function_exists('rf_get_vip_enabled_names')) {
    /**
     * 获取所有已经启用的会员组名称(包含普通用户组)
     * @return array
     */
    function rf_get_vip_enabled_names()
    {
        $names = [];
        $options = rf_get_vip_options();
        foreach ($options as $key => $value) {
            if (!isset($value['enabled']) || $value['enabled']) {
                $names[$key] = $value['name'];
            }
        }
        return $names;
    }
}
if (!function_exists('rf_get_vip_enabled_names_for_options')) {
    /**
     * 获取用于后台选项的所有已经启用的会员组名称(包含普通用户组)
     * 对于不启用的用户组则跳过其索引
     * @return array
     */
    function rf_get_vip_enabled_names_for_options()
    {
        $vip_options = rf_get_vip_options();
        $options = ['0' => esc_html__('不启用', 'rizhuti-v2')];
        if ($vip_options['31']['enabled']) $options['1'] = $vip_options['31']['name'];
        if ($vip_options['365']['enabled']) $options['2'] = $vip_options['365']['name'];
        if ($vip_options['3600']['enabled']) $options['3'] = $vip_options['3600']['name'];
        return $options;
    }
}
if (!function_exists('rf_get_post_vip_auth_badge')) {
    function rf_get_post_vip_auth_badge($vip_auth = 0)
    {
        $vip_options = rf_get_vip_options();
        $_icon = '<i class="' . _cao('vip_icon', 'fab fa-codepen') . '"></i>';
        $_badge = array(
            '0' => '',
        );
        foreach ($vip_options as $key => $value) {
            if ($key != '0') {
                $_badge[$key] = $value['enabled'] ? '<b class="badge badge-success-lighten mr-2">' . $_icon . $value['name'] . '</b>' : '';
            }
        }
        $_vip_auth = array(
            '0' => '',
            '1' => $_badge['31'] . $_badge['365'] . $_badge['3600'],
            '2' => $_badge['365'] . $_badge['3600'],
            '3' => $_badge['3600'],
        );
        return $_vip_auth[$vip_auth];
    }
}

if (!function_exists('rf_get_vip_badge')) {
    /**
     * 获取会员名+图标的标识
     * @param null $user_id 用户ID,如果没有设置则必须传入vip_type
     * @param null $vip_type 会员组类型索引
     * @param string $icon_classes 自定义图标类
     * @return string
     */
    function rf_get_vip_badge($user_id = null, $vip_type = null, $icon_classes = '')
    {
        $vip_options = rf_get_vip_options();
        if (is_null($vip_type)) {
            if (is_null($user_id)) return '';
            $vip_type = _get_user_vip_type($user_id);
        }
        $_icon = rf_get_vip_icon($icon_classes);
        $_badge = array(
            '0' => '<span class="badge badge-secondary-lighten mx-2">' . $_icon . $vip_options['0']['name'] . '</span>',
            '31' => '<span class="badge badge-success-lighten mx-2">' . $_icon . $vip_options['31']['name'] . '</span>',
            '365' => '<span class="badge badge-info-lighten mx-2">' . $_icon . $vip_options['365']['name'] . '</span>',
            '3600' => '<span class="badge badge-warning-lighten mx-2">' . $_icon . $vip_options['3600']['name'] . '</span>',
        );
        return $_badge[$vip_type];
    }
}
if (!function_exists('rf_post_vip_label')) {
    /**
     * 阅读一篇收费或会员专属文章最低需要的权限
     * @param null $post_id
     * @return mixed|string|null
     */
    function rf_post_vip_label($post_id = null)
    {
        $lowest_vip = rf_lowest_vip();
        $vip_options_map = rf_vip_options_map();
        $info = rf_get_post_info($post_id);
        $vip_text = rf_get_base_vip_name();
        $current_vip_key = $vip_options_map[$info['auth_type']];
        if ($current_vip_key > $lowest_vip && isset($vip_names[$current_vip_key])) {
            $vip_text = $vip_names[$current_vip_key];
        }
        return $vip_text;
    }
}
