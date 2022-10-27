<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 06:29:49 +0800
 * @Path           : /wp-content/themes/rowfish/factory/shortcodes.php
 * @Description    : 主题自定义的短代码
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */


if (!function_exists('rf_tabs_group_shortcode')) {
    /**
     * 标签切换短代码
     *
     * @param $atts
     * @param null $content
     * @return string
     */
    function rf_tabs_group_shortcode($atts, $content = null)
    {
        global $tabs_content;

        $tabs_content = '';
        $nav_id = 'nav-tab-' . rand(1, 100) . '-' . rand(1, 100);

        $content_id = 'tab-content-' . rand(1, 100) . '-' . rand(1, 100);

        $output = "<nav> <div class='nav nav-tabs short-code-tabs scroll-hide' id='${nav_id}' role='tablist'>" . do_shortcode(trim($content, "\n")) . "</div></nav>";
        $output .= "<div class='tab-content' id='{$content_id}'>" . do_shortcode($tabs_content) . "</div>";
        $output = "<div class='card tabs-card'>
  <div class='card-body'>{$output}</div></div>";

        return $output;
    }
}


/**
 * 标签主题短代码,配合`rf_tabs_group_shortcode`使用
 * @param mixed $atts
 * @param mixed|null $content
 * @return string
 */
if (!function_exists('rf_tab_shortcode')) {
    function rf_tab_shortcode($atts, $content = null)
    {
        global $tabs_content;
        extract(shortcode_atts(array(
            'name' => '',
        ), $atts));
        $active = is_int(array_search('active', $atts));
        if (empty($id))
            $id = 'nav-' . rand(1, 100) . '-' . rand(1, 100);
        $label_by = "{$id}-tab";
        $classes = ['content' => ["tab-pane", "fade"], 'nav' => ['nav-link']];
        if ($active) {
            $classes['content'] = array_merge($classes['content'], ["show", "active"]);
            $classes['nav'] = array_merge($classes['nav'], ['active']);
        }
        $content_classes = implode(' ', $classes['content']);
        $nav_classes = implode(' ', $classes['nav']);

        $tabs_content .=
            "<div class='{$content_classes}' id='{$id}' role='tabpanel' aria-labelledby='{$label_by}' markdown='1'>" . trim($content) . "</div>";

        $nav = "<a class='{$nav_classes}' id='{$label_by}' data-toggle='tab' data-target='#{$id}' role='tab' aria-controls='${id}' aria-selected='true'>{$name}</a>";

        return $nav;
    }
}

/**
 * 多色提示框短代码
 * @param mixed $atts
 * @param string $content
 * @return string
 */
if (!function_exists('rf_alert_shortcode')) {
    function rf_alert_shortcode($atts, $content = '')
    {
        extract(shortcode_atts(array(
            'type' => 'green',
        ), $atts));
        switch ($type) {
            case 'share':
                $color = 'secondary';
                break;
            case 'yellow':
                $color = 'warning';
                break;
            case 'red':
                $color = 'danger';
                break;
            case 'lblue':
                $color = 'primary';
                break;
            case 'green':
                $color = 'success';
                break;
            default:
                $color = 'info';
                break;
        }
        $classes = "alert alert-{$color}";
        return "<div class='{$classes}' role='alert'>{$content}</div>";
    }
}

add_shortcode('tabs', 'rf_tabs_group_shortcode');
add_shortcode('tab', 'rf_tab_shortcode');
add_shortcode('scode', 'rf_alert_shortcode');

