<?php
/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-19 05:25:49 +0800
 * @Path           : /wp-content/themes/rowfish/factory/helpers/menu.php
 * @Description    : 修改导航菜单类
 * Copyright 2021 pincman, All Rights Reserved. 
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
// Exit if accessed directly.
defined('ABSPATH') || exit;
require_once get_theme_file_path('inc/template-navwalker.php');

/**
 * 导航栏菜单生成类
 */
class Rowfish_Walker_Nav_Menu extends rizhuti_v2_Walker_Nav_Menu
{

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {


        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array)$item->classes;
        // $classes[] = 'menu-item-' . $item->ID;

        $is_mega_nav = get_post_meta($item->ID, 'is_mega_nav', true);

        if ($depth == 0 && $this->mega && !empty($is_mega_nav)) {
            if ($item->object == 'category' || $item->object == 'post_tag') {
                $classes[] = 'menu-item-has-children';
                $classes[] = 'menu-item-mega';
            }
        }

        // $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        // $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        // $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        // $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        $id = '';
        // $output .= $indent . '<li' . $id . $class_names . '>';

        $atts = array();
        $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
        $atts['href'] = !empty($item->url) ? $item->url : '';
        $class_names = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth);
        $info = rf_get_post_info();
        $termObj = get_queried_object();
        $taxonomy = (!empty($termObj) && !empty($termObj->taxonomy)) ? $termObj->taxonomy : 'category';
        $isCourse = is_post_type_archive('course') || $taxonomy == 'course_category' || $taxonomy == 'course_series' || get_post_type() === 'course';
        // $isdocs = get_post_type();
        $isAnpress = is_post_type_archive('question') || $taxonomy == 'question_tag' || $taxonomy == 'question_category' || get_post_type() === 'question';
        $marchHref = trim(trim($atts['href']), '/');
        if (is_category()) {
            $match_link = get_category_link($termObj->term_id);
            $link_include = !empty($match_link) && !empty($marchHref) && strpos($match_link, $marchHref) !== false;
        } else {
            $link_include = false;
            $post_link = get_permalink($info['post_id']);
            if (!empty($post_link) && !empty($marchHref) && strpos($post_link, $marchHref) !== false) {
                $link_include = true;
            } else {
                if ($isCourse) {
                    $cats = $taxonomy == 'course_series' ? rf_get_course_series($info['post_id']) : rf_get_course_categories($info['post_id']);
                } else {
                    $cats = wp_get_post_categories($info['post_id']);
                }
                $catlinks = array_map(function ($c) {
                    return trim(trim(get_category_link($c), '/'));;
                }, $cats);
                if ($isCourse) {
                    $catlinks = array_merge($catlinks, array_map(function ($id) {
                        return get_permalink($id);
                    }, rf_get_course_page_id()));
                }
                foreach ($catlinks as $index => $cl) {
                    if (!empty($cl) && !empty($marchHref) && strpos($cl, $marchHref) !== false) {
                        $link_include = true;
                    }
                }
            }
        }
        $is_link_include = $marchHref !== trim(trim(home_url()), '/') && $link_include;
        // $cate_link = get_category_link(get_queried_object_id());
        // $post_link = get_permalink($uinfo['post_id']);
        // $link_include = strpos($cate_link, $atts['href']) !== false || strpos($post_link, $atts['href']) !== false;
        if (($is_link_include || $isAnpress) && !is_author()) {
            // $page_id = docspress()->get_option('docs_page_id', 'docspress_settings');
            $class_names = array_filter($class_names, function ($item) {
                return $item !== 'current-menu-item';
            });
            if (($isAnpress && in_array('anspress-menu-base', $class_names)) || $is_link_include) {
                array_push($class_names, 'current-menu-item');
            }
        }
        $class_names = join(' ', $class_names);
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        $nav_icon = get_post_meta($item->ID, 'nav_icon', true);
        if (!empty($nav_icon)) {
            $item->title = '<i class="' . $nav_icon . '"></i>' . $item->title;
        }
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
        // 高级菜单
        if ($depth == 0 && $this->mega && ($item->object == 'category' || $item->object == 'post_tag') && !empty($is_mega_nav)) {
            $term_id = $item->object_id;
            $term_args = array('posts_per_page' => 8);
            switch ($item->object) {
                case 'category':
                    $term_args['cat'] = $term_id;
                    break;
                case 'post_tag':
                    $term_args['tag_id'] = $term_id;
                    break;
            }
            $term_posts = new WP_Query($term_args);

            $item_output .= '<div class="mega-menu">';

            if ($term_posts->have_posts()) {
                $item_output .= '<div class="menu-posts owl">';
                while ($term_posts->have_posts()) : $term_posts->the_post();
                    $item_output .= '<div class="menu-post">';
                    ob_start();
                    echo _get_post_media(null, 'thumbnail');
                    rizhuti_v2_entry_title(array('link' => true));
                    $item_output .= ob_get_clean();
                    $item_output .= '</div>';
                endwhile;
                $item_output .= '</div>';
            }

            wp_reset_postdata();

            $item_output .= '</div>';
        }
        // 高级菜单END

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
