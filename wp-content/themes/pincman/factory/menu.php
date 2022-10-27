<?php
/*
 * @Author: Pincman
 * @created_at: 2021-05-21 07:00:10
 * @updated_at: 2021-05-30 20:52:24
 * @description: 自定义菜单
 * @homepage: https://pincman.cn
 */

/**
 * 修改默认菜单以适配anspress
 * @package pincman
 */
class Pincman_Walker_Nav_Menu extends Walker_Nav_Menu
{

    public $tree_type = array('post_type', 'taxonomy', 'custom');
    public $db_fields = array('parent' => 'menu_item_parent', 'id' => 'db_id');
    protected $mega;

    public function __construct($mega = false)
    {
        $this->mega = $mega;
    }

    public function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    /**
     * 菜单激活状态修改
     * @param string $output 
     * @param \WP_Post $item 
     * @param int $depth 
     * @param \stdClass|array $args 
     * @param int $id 
     * @return void 
     */
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {

        $uinfo = pm_shop_post_info();
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes   = empty($item->classes) ? array() : (array) $item->classes;
        // $classes[] = 'menu-item-' . $item->ID;

        $is_mega_nav = get_post_meta($item->ID, 'is_mega_nav', true);

        if ($depth == 0 && $this->mega && !empty($is_mega_nav)) {
            if ($item->object == 'category' || $item->object == 'post_tag') {
                $classes[] = 'menu-item-has-children';
                $classes[] = 'menu-item-mega';
            }
        }
        $termObj = get_queried_object();
        $taxonomy = (!empty($termObj) && !empty($termObj->taxonomy)) ? $termObj->taxonomy : 'category';
        $isdocs = get_post_type();
        $isAnpress = is_post_type_archive('question') || $taxonomy == 'question_tag' || $taxonomy == 'question_category'  || get_post_type() === 'question';
        $class_names = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth);

        // $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        // $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        $id = '';

        $atts           = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';
        $marchHref = trim(trim($atts['href']), '/');
        if (is_category()) {
            $match_link = get_category_link($termObj->term_id);
            $link_include = strpos($match_link, $marchHref) !== false;
        } else {
            $link_include = false;
            $post_link = get_permalink($uinfo['post_id']);
            if (strpos($post_link, $marchHref) !== false) {
                $link_include = true;
            } else {
                $cats = wp_get_post_categories($uinfo['post_id']);
                $catlinks = array_map(function ($c) {
                    return trim(trim(get_category_link($c), '/'));;
                }, $cats);
                foreach ($catlinks as $index => $cl) {
                    if (strpos($cl, $marchHref) !== false) {
                        $link_include = true;
                    }
                }
            }
        }
        // $cate_link = get_category_link(get_queried_object_id());
        // $post_link = get_permalink($uinfo['post_id']);
        // $link_include = strpos($cate_link, $atts['href']) !== false || strpos($post_link, $atts['href']) !== false;
        if ($marchHref !== trim(trim(home_url()), '/') && $link_include) {
            // $page_id = docspress()->get_option('docs_page_id', 'docspress_settings');
            $class_names = array_filter($class_names, function ($item) {
                return $item !== 'current-menu-item';
            });
            array_push($class_names, 'current-menu-item');
        } else  if ($isAnpress) {
            $class_names = array_filter($class_names, function ($item) {
                return $item !== 'current-menu-item';
            });
            if (in_array('anspress-menu-base', $class_names)) {
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
            $term_id   = $item->object_id;
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

    public function end_el(&$output, $item, $depth = 0, $args = array())
    {
        $output .= "</li>\n";
    }

    public function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
    {
        $id_field = $this->db_fields['id'];
        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }
        return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    public static function fallback($args)
    {
        extract($args);

        $fb_output = null;

        if ($container) {
            $fb_output = '<' . $container;

            if ($container_id) {
                $fb_output .= ' id="' . $container_id . '"';
            }

            if ($container_class) {
                $fb_output .= ' class="' . $container_class . '"';
            }

            $fb_output .= '>';
        }

        $fb_output .= '<ul';

        if ($menu_id) {
            $fb_output .= ' id="' . $menu_id . '"';
        }

        if ($menu_class) {
            $fb_output .= ' class="' . $menu_class . '"';
        }

        $fb_output .= '>';
        $fb_output .= '<li class="menu-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '">' . esc_html__('添加菜单', 'rizhuti-v2') . '</a></li>';
        $fb_output .= '</ul>';

        if ($container) {
            $fb_output .= '</' . $container . '>';
        }

        echo wp_kses($fb_output, array(
            'ul'   => array('id' => array(), 'class' => array()),
            'li'   => array('class' => array()),
            'a'    => array('href' => array()),
            'span' => array(),
        ));
    }
}
