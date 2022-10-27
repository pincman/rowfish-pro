<?php
/*
Plugin Name: WPJAM Table Shortcode
Plugin URI: http://blog.wpjam.com/m/wordpress-shortcode-for-table/
Description: 使用 Shortcode 快速输入表格。
Version: 0.1
Author: Denis
*/

function tabs_group_shortcode($atts, $content = null)
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


function tab_shortcode($atts, $content = null)
{
    global $tabs_content;
    extract(shortcode_atts(array(
        'name' => '',
    ), $atts));
    $active = is_int(array_search('active', $atts));
    if (empty($id))
        $id = 'nav-' . rand(1, 100) . '-' . rand(1, 100);
    $label_by = "{$id}-tab";
    $classes = ['content' => ["tab-pane", "fade", "show"], 'nav' => ['nav-link']];
    if ($active) {
        $classes['content'] = array_merge($classes['content'], ["show", "active"]);
        $classes['nav'] = array_merge($classes['nav'], ['active']);
    }
    $content_classes = implode(' ', $classes['content']);
    $nav_classes = implode(' ', $classes['nav']);

    $tabs_content .=
        "<div class='{$content_classes}' id='{$id}' role='tabpanel' aria-labelledby='{$label_by}' markdown='1'>" . trim($content) . "</div>";

    $nav = "<a class='{$nav_classes}' id='{$label_by}' data-toggle='tab' href='#{$id}' role='tab' aria-controls='${id}' aria-selected='true'>{$name}</a>";

    return $nav;
}

function alert_shortcode($atts, $content = '')
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

add_shortcode('tabs', 'tabs_group_shortcode');
add_shortcode('tab', 'tab_shortcode');
add_shortcode('scode', 'alert_shortcode');

function the_content_filter($content)
{
    $block = join("|", array("tabs", "tab"));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep);
    return $rep;
}
add_filter("the_content", "the_content_filter");
