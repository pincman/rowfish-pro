<?php

namespace EditormdApp;
use EditormdUtils\Config;

class Emoji {

    public function __construct() {

        add_action("wp_enqueue_scripts", array($this, "emoji_enqueue_scripts"));

        if(!in_array($GLOBALS["pagenow"], array("wp-login.php", "wp-register.php"))) {
            add_action("wp_print_footer_scripts", array($this, "emoji_enqueue_footer_js"));
        }

        //禁用WordPress自带Emoji表情 ==> 排除干扰
        add_action("init", array($this, "disable_emojis"));
    }

    /**
     * 前端Emoji表情
     */
    public function emoji_enqueue_scripts() {

        wp_enqueue_style("Emojify.js", Config::get_option("editor_addres","editor_style") . "/assets/Emojify.js/css/basic/emojify.min.css", array(), WP_EDITORMD_VER, "all");
        wp_enqueue_script("Emojify.js", Config::get_option("editor_addres","editor_style") . "/assets/Emojify.js/js/emojify.min.js", array(), WP_EDITORMD_VER, true);
    }

    public function emoji_enqueue_footer_js() {
        ?>
        <script type="text/javascript">
            window.onload = function () {
                emojify.setConfig({
                    img_dir: "<?php echo Config::get_option('editor_addres','editor_style') . '/assets/Emojify.js/images/basic' ?>",//前端emoji资源地址
                    blacklist: {
                        "ids": [],
                        "classes": ["no-emojify"],
                        "elements": ["^script$", "^textarea$", "^pre$", "^code$"]
                    }
                });
                emojify.run();
            }
        </script>
        <?php
    }

    /**
     * Disable the emoji"s
     */
    public function disable_emojis() {
        remove_action("wp_head", "print_emoji_detection_script", 7);
        remove_action("admin_print_scripts", "print_emoji_detection_script");
        remove_action("wp_print_styles", "print_emoji_styles");
        remove_action("admin_print_styles", "print_emoji_styles");
        remove_filter("the_content_feed", "wp_staticize_emoji");
        remove_filter("comment_text_rss", "wp_staticize_emoji");
        remove_filter("wp_mail", "wp_staticize_emoji_for_email");
        add_filter("tiny_mce_plugins", array($this, "disable_emojis_tinymce"));
        add_filter("wp_resource_hints", array($this, "disable_emojis_remove_dns_prefetch"), 10, 2);
    }

    /**
     * Filter function used to remove the tinymce emoji plugin.
     *
     * @param    array  $plugins
     * @return   array             Difference betwen the two arrays
     */
    public function disable_emojis_tinymce($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, array("wpemoji"));
        } else {
            return array();
        }
    }

    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     *
     * @param  array  $urls          URLs to print for resource hints.
     * @param  string $relation_type The relation type the URLs are printed for.
     * @return array                 Difference betwen the two arrays.
     */
    public function disable_emojis_remove_dns_prefetch($urls, $relation_type) {

        if ("dns-prefetch" == $relation_type) {

            // Strip out any URLs referencing the WordPress.org emoji location
            $emoji_svg_url_bit = "https://s.w.org/images/core/emoji/";
            foreach ($urls as $key => $url) {
                if (strpos($url, $emoji_svg_url_bit) !== false) {
                    unset($urls[$key]);
                }
            }

        }

        return $urls;
    }
}