<?php if (!defined('ABSPATH')) {
    die;
}
if (class_exists('toc_widget') && !class_exists('Pincman_Toc_Widget')) :
    class Pincman_Toc_Widget extends toc_widget
    {

        /**
         * Constructs the new widget.
         *
         * @see WP_Widget::__construct()
         */
        function __construct()
        {
            // Instantiate the parent object.
            $widget_options = array(
                'classname' => 'Pincman_Toc_Widget',
                'description' => __('Display the table of contents in the sidebar with this widget', 'table-of-contents-plus')
            );
            $control_options = array(
                'width' => 250,
                'height' => 350,
                'id_base' => 'pincman-toc-widget'
            );
            WP_Widget::__construct('pincman-toc-widget', 'PM: 文章目录', $widget_options, $control_options);
        }

        function widget($args, $instance)
        {
            global $tic, $wp_query;
            $items = $custom_toc_position = '';
            $find = $replace = array();

            $toc_options = $tic->get_options();
            $post = get_post($wp_query->post->ID);
            $custom_toc_position = strpos($post->post_content, '[toc]');    // at this point, shortcodes haven't run yet so we can't search for <!--TOC-->
            // if (in_array((int) get_post_meta($post->ID, 'wppay_type', true), [4, 5, 6, 7])) return;
            if ($tic->is_eligible($custom_toc_position)) {

                extract($args);

                $items = $tic->extract_headings($find, $replace, wptexturize($post->post_content));
                $title = (array_key_exists('title', $instance)) ? apply_filters('widget_title', $instance['title']) : '';
                if (strpos($title, '%PAGE_TITLE%') !== false) $title = str_replace('%PAGE_TITLE%', get_the_title(), $title);
                if (strpos($title, '%PAGE_NAME%') !== false) $title = str_replace('%PAGE_NAME%', get_the_title(), $title);
                $hide_inline = $toc_options['show_toc_in_widget_only'];

                $css_classes = '';
                // bullets?
                if ($toc_options['bullet_spacing'])
                    $css_classes .= ' have_bullets';
                else
                    $css_classes .= ' no_bullets';

                if ($items) {
                    // before widget (defined by themes)
                    echo $before_widget;

                    // display the widget title if one was input (before and after titles defined by themes)
                    if ($title) echo $before_title . $title . $after_title;

                    // display the list
                    echo '<ul class="toc_widget_list' . $css_classes . '">' . $items . '</ul>';

                    // after widget (defined by themes)
                    echo $after_widget;
                }
            }
        }
    }
    add_action('widgets_init', 'pm_toc_register_widgets');

    function pm_toc_register_widgets()
    {
        register_widget('Pincman_Toc_Widget');
    }

endif;
