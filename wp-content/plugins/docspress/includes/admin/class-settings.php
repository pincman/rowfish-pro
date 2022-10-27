<?php
/**
 * Settings.
 *
 * @package docspress
 */

/**
 * Settings Class
 */
class DocsPress_Settings {
    /**
     * Construct
     */
    public function __construct() {
        $this->settings_api = new DocsPress_Settings_API();

        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /**
     * Initialize the settings
     */
    public function admin_init() {

        // set the settings.
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        // initialize settings.
        $this->settings_api->admin_init();
    }

    /**
     * Register the admin settings menu
     */
    public function admin_menu() {
        add_submenu_page(
            'docspress',
            __( 'DocsPress Settings', 'docspress' ),
            __( 'Settings', 'docspress' ),
            'manage_options',
            'docspress-settings',
            array( $this, 'plugin_page' )
        );
    }

    /**
     * Plugin settings sections
     *
     * @return array
     */
    public function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'docspress_settings',
                'title' => __( 'General', 'docspress' ),
            ),
            array(
                'id'    => 'docspress_single',
                'title' => __( 'Single Document', 'docspress' ),
            ),
            array(
                'id'    => 'docspress_archive',
                'title' => __( 'Archive', 'docspress' ),
            ),
            array(
                'id'    => 'docspress_export',
                'title' => __( 'Export', 'docspress' ),
            ),
        );

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields() {
        include_once dirname( __FILE__ ) . '/../class-export.php';
        $export_class = new DocsPress_Export();

        $settings_fields = array(
            'docspress_settings' => array(
                array(
                    'name'    => 'docs_page_id',
                    'label'   => __( 'Documentation Archive Page', 'docspress' ),
                    'desc'    => __( 'Page to display documentations list. <br> If you see the 404 error, please go to Settings > Permalinks and press "Save Changes" button.', 'docspress' ),
                    'type'    => 'select',
                    'options' => $this->get_pages(),
                ),
            ),
            'docspress_single' => array(
                array(
                    'name'    => 'show_comments',
                    'label'   => __( 'Display Comments', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_feedback_buttons',
                    'label'   => __( 'Display Feedback Buttons', 'docspress' ),
                    'desc'    => __( 'Helpful feedback', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_feedback_buttons_likes',
                    'desc'    => __( 'Display Likes / Dislikes Count', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'show_feedback_suggestion',
                    'desc'    => __( 'Display Suggestion Form After Like', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ),
                array(
                    'name'        => 'show_feedback_suggestion_email',
                    'desc'        => __( 'Suggestion Email', 'docspress' ),
                    'type'        => 'text',
                    'placeholder' => get_option( 'admin_email' ),
                    'default'     => '',
                ),
                array(
                    'name'    => 'show_anchor_links',
                    'label'   => __( 'Display Heading Anchors', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'sidebar',
                    'label'   => __( 'Sidebar', 'docspress' ),
                    'type'    => 'html',
                ),
                array(
                    'name'    => 'sidebar_show_search',
                    'label'   => __( 'Display Search', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'sidebar_show_nav_parents',
                    'label'   => __( 'Display Parent Links', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ),
                array(
                    'name'    => 'sidebar_show_nav_childs',
                    'label'   => __( 'Display Child Links', 'docspress' ),
                    'desc'    => __( 'Always display child navigation links (by default displayed only for active parent doc)', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'off',
                ),
                array(
                    'name'    => 'sidebar_show_nav_number_of_childs',
                    'label'   => __( 'Display Number of Childs', 'docspress' ),
                    'desc'    => __( 'Display in the title of parent link the number of childs', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),

                array(
                    'name'    => 'ajax',
                    'label'   => __( 'AJAX loading', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'ajax_custom_js',
                    'label'   => __( 'AJAX Custom JS', 'docspress' ),
                    'desc'    => __( 'Run custom JS after document loaded via AJAX', 'docspress' ),
                    'type'    => 'textarea',
                    'size'    => 'large',
                    'default' => "/*\n * New page content loaded via ajax you can get in variable 'new_page'\n * Example: console.log(new_page);\n */",
                ),
            ),
            'docspress_archive' => array(
                array(
                    'name'    => 'show_articles',
                    'label'   => __( 'Display Articles', 'docspress' ),
                    'desc'    => __( 'Top level articles list', 'docspress' ),
                    'type'    => 'checkbox',
                    'default' => 'on',
                ),
                array(
                    'name'    => 'articles_number',
                    'label'   => __( 'Number of Articles', 'docspress' ),
                    'desc'    => __( 'Type -1 to display all available articles', 'docspress' ),
                    'type'    => 'number',
                    'default' => 3,
                ),
            ),
            'docspress_export' => array(
                array(
                    'name'    => 'custom_css',
                    'label'   => __( 'Custom CSS', 'docspress' ),
                    'desc'    => __( 'Added in exported HTML files', 'docspress' ),
                    'type'    => 'textarea',
                    'size'    => 'large',
                    'default' => $export_class->custom_css,
                ),
                array(
                    'name'    => 'custom_js',
                    'label'   => __( 'Custom JS', 'docspress' ),
                    'desc'    => __( 'Added in exported HTML files', 'docspress' ),
                    'type'    => 'textarea',
                    'size'    => 'large',
                    'default' => $export_class->custom_js,
                ),
                array(
                    'name'    => 'clean_html',
                    'label'   => __( 'Clean HTML RegExp', 'docspress' ),
                    'desc'    => __( 'Each regexp on new line (change it only if you understand what you do)', 'docspress' ),
                    'type'    => 'textarea',
                    'size'    => 'large',
                    'default' => str_replace( '\'', "\\'", $export_class->clean_html_regexp ),
                ),
            ),
        );

        return $settings_fields;
    }

    /**
     * The plguin page handler
     *
     * @return void
     */
    public function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        $this->scripts();

        echo '</div>';
    }
    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    public function get_pages() {
        $pages_options = array( '' => __( '&mdash; Select Page &mdash;', 'docspress' ) );
        $pages         = get_pages(
            array(
                'numberposts' => -1, // phpcs:ignore
            )
        );

        if ( $pages ) {
            foreach ( $pages as $page ) {
                $pages_options[ $page->ID ] = $page->post_title;
            }
        }
        return $pages_options;
    }

    /**
     * Scripts
     *
     * @return void
     */
    public function scripts() {
        ?>
        <script type="text/javascript">
            jQuery(function($) {
                $('input[name="docspress_single[show_feedback_buttons]"]:checkbox').on( 'change', function() {

                    $('tr.show_feedback_buttons_likes')[ $(this).is(':checked' ) ? 'show' : 'hide' ]();

                }).change();

                $('input[name="docspress_single[show_feedback_suggestion]"]:checkbox, input[name="docspress_single[show_feedback_buttons]"]:checkbox').on( 'change', function() {
                    const isCheckedFeedback = $('input[name="docspress_single[show_feedback_buttons]"]').is(':checked' );
                    const isCheckedSuggestion = $('input[name="docspress_single[show_feedback_suggestion]"]').is(':checked' );

                    $('tr.show_feedback_suggestion')[ isCheckedFeedback ? 'show' : 'hide' ]();

                    $('tr.show_feedback_suggestion_email')[ isCheckedFeedback && isCheckedSuggestion ? 'show' : 'hide' ]();

                }).change();

                $('input[name="docspress_single[ajax]"]:checkbox').on( 'change', function() {

                    $('tr.ajax_custom_js')[ $(this).is(':checked' ) ? 'show' : 'hide' ]();

                }).change();

                $('input[name="docspress_archive[show_articles]"]:checkbox').on( 'change', function() {

                    $('tr.articles_number')[ $(this).is(':checked' ) ? 'show' : 'hide' ]();

                }).change();
            });
        </script>
        <?php
    }

}
