<?php

/*
Plugin Name: Phoenix Media Rename
Plugin URI: https://www.eurosoftlab.com/en/phoenix-media-rename/
Description: The Phoenix Media Rename plugin allows you to simply rename your media files, once uploaded.
Version: 3.8.8
Author: crossi72
Author URI: https://eurosoftlab.com
Text Domain: phoenix-media-rename
License: GPL3
Phoenix Media Rename is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Phoenix Media Rename is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.

Phoenix icon http://sid-raphael.deviantart.com/art/Fire-Phoenix-Full-Feather-192575471 by http://sid-raphael.deviantart.com/ is licenced under https://creativecommons.org/licenses/by-sa/3.0/ 
*/

defined('ABSPATH') or die();
define('PHOENIX_MEDIA_RENAME_SCHEMA_VERSION', '1.0.1');
define ('PMR_BASENAME', plugin_basename(__FILE__));
define ('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN', 'phoenix-media-rename');

require_once('class-media-rename.php');
require_once('class-pmr-options.php');
require_once('pmr-settings.php');
require_once('lib.php');
require_once('pmr-db.php');

add_action('plugins_loaded', 'phoenix_media_rename_init');
function phoenix_media_rename_init() {
	if (is_admin()){
		//instantiate class only in admin area
		$mr = new Phoenix_Media_Rename;

		add_filter('manage_media_columns', array($mr, 'add_filename_column'), 99);
		add_filter('attachment_fields_to_edit', array($mr, 'add_filename_field'), 10, 2); 
		add_filter('sanitize_file_name_chars', array($mr, 'add_special_chars'), 10, 1);

		add_action('load-upload.php', array($mr, 'handle_bulk_pnx_rename_form_submit'));
		add_action('admin_notices', array($mr, 'show_bulk_pnx_rename_success_notice'));
		add_action('manage_media_custom_column', array($mr, 'add_filename_column_content'), 10, 2);
		add_action('wp_ajax_phoenix_media_rename', array($mr, 'ajax_pnx_rename'));
		add_action('admin_enqueue_scripts', array($mr, 'print_js'));
		add_action('admin_enqueue_scripts', 'pmr_lib::print_options_js');
		add_action('admin_enqueue_scripts', array($mr, 'print_css'));
	}
}

add_action('plugins_loaded', 'phoenix_media_rename_load_plugin_textdomain');

function phoenix_media_rename_load_plugin_textdomain() {
	load_plugin_textdomain(constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'), FALSE, basename(dirname(__FILE__)) . '/languages/');
}

register_deactivation_hook(__FILE__, 'pmr_deactivate');

/**
 * Deactivation hook: it will delete Phoenix Media Rename table from db
 */
function pmr_deactivate() {
	pmr_db::pmr_drop_tables();
}

register_activation_hook(__FILE__, 'pmr_activate');

function pmr_activate() {
	add_option('Activated_phoenix_media_rename', 'phoenix-media-rename');
	add_option('pmr_update_db_table', constant('PHOENIX_MEDIA_RENAME_SCHEMA_VERSION'));

	// is_multisite() check is important here because get_sites() is not available on single site installs.
	if (is_multisite()) {
	//multisite
		foreach (get_sites() as $subsite) {
			//change active site
			switch_to_blog($subsite->blog_id);
			//create table in site database
			pmr_db::pmr_update_db_table();

			restore_current_blog();
			//update plugin option
			update_option('pmr_table_installed', true);
		}
	} else {
	//single site
		//create table
		pmr_db::pmr_update_db_table();
		//update plugin option
		update_option('pmr_table_installed', true);
	}
}

add_action('plugins_loaded', 'pmr_update_db');

function pmr_update_db() {
	if (get_option('pmr_db_version') !== constant('PHOENIX_MEDIA_RENAME_SCHEMA_VERSION')) {
		pmr_db::pmr_update_db_table();

		update_option('pmr_db_version', constant('PHOENIX_MEDIA_RENAME_SCHEMA_VERSION'));
	}
}

add_action('in_plugin_update_message-phoenix-media-rename/phoenix-media-rename.php', 'pmr_plugin_update_message', 10, 2);

function pmr_plugin_update_message($plugin_data, $new_data) {
	if (isset($plugin_data['update']) && $plugin_data['update'] && isset($new_data->upgrade_notice)) {
		printf(
			'<div class="update-message"><p><strong>%s</strong>: %s</p></div>',
			$new_data -> new_version,
			wpautop($new_data -> upgrade_notice)
		);
	}
}
