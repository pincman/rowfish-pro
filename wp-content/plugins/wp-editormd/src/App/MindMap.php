<?php

namespace EditormdApp;

use EditormdUtils\Config;

class MindMap {

    public function __construct() {

        add_action("wp_enqueue_scripts", array($this, "mindmap_enqueue_scripts"));
        if(!in_array($GLOBALS["pagenow"], array("wp-login.php", "wp-register.php"))) {
            add_action("wp_print_footer_scripts", array($this, "mindmap_wp_footer_script"));
        }
    }

    public function mindmap_enqueue_scripts() {

        //兼容模式 - jQuery
        if(Config::get_option("jquery_compatible", "editor_advanced") !== "off") {
            wp_enqueue_script("jquery", null, null, array(), false);
        } else {
            wp_deregister_script("jquery");
            wp_enqueue_script("jQuery-CDN", Config::get_option("editor_addres","editor_style") . "/assets/jQuery/jquery.min.js", array(), WP_EDITORMD_VER, true);
        }

        wp_enqueue_script("MindMap",  Config::get_option("customize_mindmap", "editor_mindmap"), array(), WP_EDITORMD_VER, true);
    }

    public function mindmap_wp_footer_script() {
        ?>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    $(".mind p").remove();
                    $(".mind .mindTxt script").remove();
                    var mind = $(".mind");
                    if (mind.drawMind !== undefined) {
                        mind.drawMind();
                    }
                })
            })(jQuery)
        </script>
        <?php
    }
}