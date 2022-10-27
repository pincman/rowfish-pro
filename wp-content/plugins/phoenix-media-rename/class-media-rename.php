<?php
/*
* Phoenix Media Rename main class
*
*/

require_once('class-plugins.php');

#region constants

define("actionRename", "rename");
define("actionRenameRetitle", "rename_retitle");
define("actionRetitle", "retitle");
define("actionRetitleFromPostTitle", "retitle_from_post_title");
define("actionRenameFromPostTitle", "rename_from_post_title");
define("actionRenameRetitleFromPostTitle", "rename_retitle_from_post_title");
define("success", "pmr_renamed");
define("pmrTableName", "pmr_status");

abstract class Operation
{
	const search = 0;
	const replace = 1;
}

#endregion

class Phoenix_Media_Rename {

	private $is_media_rename_page;
	private $nonce_printed;

	/**
	 * Initializes the plugin
	 */
	function __construct() {
		$post = isset($_REQUEST['post']) ? get_post($_REQUEST['post']) : NULL;
		$is_media_edit_page = $post && $post->post_type == 'attachment' && $GLOBALS['pagenow'] == 'post.php';
		$is_media_listing_page = $GLOBALS['pagenow'] == 'upload.php';
		$this->is_media_rename_page = $is_media_edit_page || $is_media_listing_page;
		self::frontend_support();
	}

	/**
	 * Adds the "Filename" column at the media posts listing page
	 *
	 * @param array $columns
	 * @return void
	 */
	function add_filename_column($columns) {
		$columns['filename'] = 'Filename';
		return $columns;
	}

	/**
	 * Adds the "Filename" column content at the media posts listing page
	 *
	 * @param string $column_name
	 * @param integer $post_id
	 * @return void
	 */
	function add_filename_column_content($column_name, $post_id) {
		if ($column_name == 'filename') {

			//set bulk rename process as stopped
			$this->reset_bulk_rename();

			$file_parts = $this->get_file_parts($post_id);
			echo $this->get_filename_field($post_id, $file_parts['filename'], $file_parts['extension']);
		}
	}

	/**
	 * Add the "Filename" field to the Media form
	 *
	 * @param array $form_fields
	 * @param WP_Post $post
	 * @return array form fields
	 */
	function add_filename_field($form_fields, $post) {
		if (isset($GLOBALS['post']) && $GLOBALS['post']->post_type=='attachment') {
			$file_parts=$this->get_file_parts($GLOBALS['post']->ID);
			$form_fields['mr_filename']=array(
				'label' => __('Filename', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')),
				'input' => 'html',
				'html' => $this->get_filename_field($GLOBALS['post']->ID, $file_parts['filename'], $file_parts['extension'])
			);
		}
		return $form_fields;
	}

	/**
	 * Reset the bulk rename process
	 *
	 * @return void
	 */
	function reset_bulk_rename(){
		//set index for group rename
		$this->write_db_value('current_image_index', 0);
		//reset the bulk rename flag
		$this->write_db_value('bulk_rename_in_progress', false);
		//reset the bulk rename from post flag
		$this->write_db_value('bulk_rename_from_post_in_progress', false);
		//reset the bulk rename filename header
		$this->write_db_value('bulk_filename_header', '');
}

	/**
	 * Makes sure that the success message will be shown on bulk rename
	 *
	 * @return void
	 */
	function handle_bulk_pnx_rename_form_submit() {
		if (
			array_search(constant("actionRename"), $_REQUEST, true) !== FALSE
			|| array_search(constant("actionRenameRetitle"), $_REQUEST, true) !== FALSE
			|| array_search(constant("actionRetitle"), $_REQUEST, true) !== FALSE
			|| array_search(constant("actionRetitleFromPostTitle"), $_REQUEST, true) !== FALSE
			) {

			//set bulk rename process as stopped
			$this->reset_bulk_rename();

			wp_redirect(add_query_arg(array(constant("success") => 1), wp_get_referer()));
			exit;
		}
	}

	/**
	 * Shows bulk rename success notice
	 *
	 * @return void
	 */
	function show_bulk_pnx_rename_success_notice() {
		if(isset($_REQUEST[constant("success")])) {
			echo '<div class="updated"><p>'. __('Medias successfully renamed!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')) .'</p></div>';
		}
	}

	/**
	 * Print the JS code only on media.php and media-upload.php pages
	 *
	 * @return void
	 */
	function print_js() {
		if ($this->is_media_rename_page) {
			wp_enqueue_script(constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'), plugins_url('js/scripts.min.js', __FILE__), array('jquery'), '3.1.0');
			?>

			<script type="text/javascript">
				MRSettings = {
					'labels': {
						'<?php echo constant("actionRename") ?>': '<?php echo __('Rename', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')) ?>',
						'<?php echo constant("actionRenameRetitle") ?>': '<?php echo __('Rename & Retitle', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')) ?>',
						'<?php echo constant("actionRetitle") ?>': '<?php echo __('Retitle', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')) ?>',
						'<?php echo constant("actionRetitleFromPostTitle") ?>': '<?php echo __('Retitle from Post', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')) ?>',
						'<?php echo constant("actionRenameFromPostTitle") ?>': '<?php echo __('Rename from Post', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')) ?>',
						'<?php echo constant("actionRenameRetitleFromPostTitle") ?>': '<?php echo __('Rename & Retitle from Post', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN')) ?>'
					}
				};
			</script>

			<?php
		}
	}

	/**
	 * Print the CSS styles only on media.php and media-upload.php pages
	 *
	 * @return void
	 */
	function print_css() {
		if ($this->is_media_rename_page) {
			wp_enqueue_style('phoenix-media-rename', plugins_url('css/style.css', __FILE__));
		}
	}

	/**
	 * Prints the "Filename" textfield
	 *
	 * @param integer $post_id
	 * @param string $filename
	 * @param string $extension
	 * @return void
	 */
	function get_filename_field($post_id, $filename, $extension) {
		if (!isset($this->nonce_printed)) $this->nonce_printed=0;
		ob_start(); ?>

			<div class="phoenix-media-rename">
				<input type="text" class="text phoenix-media-rename-filename" autocomplete="post_title" value="<?php echo $filename ?>" title="<?php echo $filename ?>" data-post-id="<?php echo $post_id ?>" />
				<span class="file_ext">.<?php echo $extension ?></span>
				<span class="loader"></span>
				<span class="success"></span>
				<span class="error"></span>
				<?php if (!$this->nonce_printed) {
					wp_nonce_field('phoenix_media_rename', '_mr_wp_nonce');
					$this->nonce_printed++;
				} ?>
			</div>

		<?php return ob_get_clean();
	}

	/**
	 * Create a unique filename
	 *
	 * @param string $filename: filename
	 * @param string $extension: filename extension
	 * @param string $file_subfolder: folder containing the file
	 * @return void
	 */
	static function serialize_if_file_exists($filename, $extension, $file_subfolder){
		clearstatcache();

		//check normal and lowercase filename to ensure compatibility with case insensitive file systems
		while (
			(file_exists(wp_upload_dir()['basedir'] . '/' . $file_subfolder . $filename . '.' . $extension))
			||
			(file_exists(strtolower(wp_upload_dir()['basedir'] . '/' . $file_subfolder . $filename . '.' . $extension)))
			) {
			//filename exists: create a new filename
			$filename = self::increment_filename($filename);
		}

		return $filename;
	}

	/**
	 * Add a progessive number to the filename
	 *
	 * @param string $filename
	 * @return void
	 */
	static function increment_filename($filename){
		//if filename ends with '-scaled', remove the string
		if (self::ends_with($filename, '-scaled')){
			$filename = substr($filename, 0, strlen($filename) - strlen('-scaled'));
			$add_suffix = true;
		} else {
			$add_suffix = false;
		}

		//check if filename ends with a number
		$pattern = '(\d+$)';

		preg_match($pattern, $filename, $matches);

		if ($matches){
			//filename ends with a number: increase the value
			$number = $matches[0];
			$number++;

			$filename = preg_replace($pattern, $number, $filename);
		} else {
			//filename doesn't end with a number: add it
			$filename .= '-1';
		}

		//restore '-scaled' suffix if it was present
		if ($add_suffix){
			$filename .= '-scaled';
		}

		return $filename;
	}

	/**
	 * Read a value from Phoenix Media Rename table
	 *
	 * @param string $field
	 * @return string
	 */
	function read_db_value($field){
		global $wpdb;

		//check if there are values in table
		$result = $wpdb->get_var("SELECT " . $field . " FROM " . $wpdb->prefix . constant('pmrTableName'));

		return $result;
	}

	/**
	 * Insert a value in Phoenix Media Rename table
	 *
	 * @param string $field
	 * @param any $value
	 * @return void
	 */
	function write_db_value($field, $value){
		global $wpdb;

		//check if there are values in table
		$records = $wpdb->get_var("SELECT IFNULL(COUNT(*), 0) FROM " . $wpdb->prefix . constant('pmrTableName'));

		if ($records > 1){
			//error in table content, truncate table to reset data
			$wpdb->query(
				$wpdb->prepare(
					"TRUNCATE TABLE " . $wpdb->prefix . constant('pmrTableName')
				)
			);
		}elseif ($records == 0){
			//table is empty, insert new row
			$wpdb->insert(
				$wpdb->prefix . constant('pmrTableName'), 
				array(
					$field => $value, 
				)
			);
		} else {
			//table contains a record, update data
			$wpdb->update(
				$wpdb->prefix . constant('pmrTableName'), 
				array(
					$field => $value, 
				),
				array(
					'ID' => 1, 
				)
			);
		}
	}

	/**
	 * Handles AJAX rename queries
	 *
	 * @return void
	 */
	function ajax_pnx_rename() {
		if (check_ajax_referer('phoenix_media_rename', '_wpnonce', 0)) {
			//check if retitle and rename are required
			$retitle = $this->retitle_required();
			$rename = $this->rename_required();
			$name_from_post = $this->name_from_post();
			$title_from_post = $this->title_from_post();

			$new_filename = $_REQUEST['new_filename'];
			$bulk_rename_in_progress = $this->read_db_value('bulk_rename_in_progress');
			$bulk_rename_from_post_in_progress = $this->read_db_value('bulk_rename_from_post_in_progress');
			$attachment_id = $_REQUEST['post_id'];
			$force_serializiation = false;

			if (! current_user_can('edit_post', $attachment_id)){
				//the user can't modify the file: log the error and exit function to prevent code excecution
				echo __("user doesn't have permission to edit the post", constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));

				wp_die();
			} else{
				//the user can modify the file: execute the code

				if ($name_from_post || $title_from_post){
					//if filename has to be generated from parent post the do_rename function will get the filename
					// $new_filename = '';
					$post = get_post($attachment_id);

					if (! $this->check_post_parent($post)){
						//media is not attached to a post, or post has no title
						return;
					}else{
						$new_filename = $this->get_filename_from_post_parent($post, true, $post_parent);

						$force_serializiation = true;
					};

				}elseif ($bulk_rename_in_progress){
					//bulk rename in progress: build filename
					//increment image name index
					$current_image_index = $this->read_db_value('current_image_index');
					$bulk_filename_header = $this->read_db_value('bulk_filename_header');

					$this->write_db_value('current_image_index', ++$current_image_index);

					//create filename
					$new_filename = $this->build_filename($bulk_filename_header, $current_image_index);
				}else{
					//bulk rename not in progress: check if filename contains {}
					//search pattern {number}
					$re = '/[{][0-9]{1,10}[}]/m';

					preg_match($re, $new_filename, $matches);

					//if new filename contains {number}, serialize following file names
					if ($matches){
						//notify the start of bulk rename process
						$this->write_db_value('bulk_rename_in_progress', true);

						//extract file header
						$bulk_filename_header = preg_replace($re, '', $new_filename);

						//if this is the first iteration, extract the number from filename
						$re = '/[0-9]{1,10}/m';

						preg_match($re, $matches[0], $matches);

						$current_image_index = $matches[0];

						//check if image index start with '0'
						$zeroes = self::starts_with($current_image_index, '0');

						if ($zeroes != -1){
							//image index start with one or more '0'
							//add zeroes to header
							$bulk_filename_header .= $zeroes;

							//remove zeroes from image index
							$current_image_index = intval($current_image_index);
						}

						$this->write_db_value('bulk_filename_header', $bulk_filename_header);

						$this->write_db_value('current_image_index', $current_image_index);

						//create filename
						$new_filename = $this->build_filename($bulk_filename_header, $current_image_index);
					}	
				}

				echo $this->do_rename($attachment_id, $new_filename, $retitle, $title_from_post, $name_from_post, true, false, $force_serializiation, $rename);
				}

			wp_die();
		}

		wp_die();
	}

	/**
	 * Check if rename is needed
	 *
	 * @return boolean
	 */
	private function title_from_post(){
		//if action is "actionRenameFromPostTitle" or "actionRenameRetitleFromPostTitle" retrieve title for post related to media file to generate attachment title
		if ($_REQUEST['type'] == constant("actionRenameRetitleFromPostTitle")
			|| $_REQUEST['type'] == constant("actionRetitleFromPostTitle")
			) {
			$result = true;
		}else{
			$result = false;
		}

		return $result;
	}

	/**
	 * Check if rename is needed
	 *
	 * @return boolean
	 */
	private function name_from_post(){

		//if action is "actionRenameFromPostTitle" or "actionRenameRetitleFromPostTitle" retrieve title for post related to media file to generate filename
		if (($_REQUEST['type'] == constant("actionRenameFromPostTitle"))
			|| ($_REQUEST['type'] == constant("actionRenameRetitleFromPostTitle"))
			){
			$result = true;
		}else{
			$result = false;
		}

		return $result;
	}

	/**
	 * Check if rename is needed
	 *
	 * @return boolean
	 */
	private function rename_required(){
		//set default
		$result = true;

		if (
			$_REQUEST['type'] == constant("actionRetitle")
			|| $_REQUEST['type'] == constant("actionRetitleFromPostTitle")
			) {
			//disable renaming if needed
			$result = false;
		}

		return $result;
	}

	/**
	 * Check if retitle is needed
	 *
	 * @return boolean
	 */
	private function retitle_required(){
		//set default
		$result = false;

		//check if retitle is needed
		if (
			$_REQUEST['type'] == constant("actionRenameRetitleFromPostTitle")
			|| $_REQUEST['type'] == constant("actionRetitleFromPostTitle")
			|| $_REQUEST['type'] == constant("actionRenameRetitle")
			|| $_REQUEST['type'] == constant("actionRetitle")
			) {
			//enable retitling if needed
			$result = true;
		}

		return $result;
	}

	/**
	 * build a filename from filename parts
	 *
	 * @param string $header
	 * @param string $trailer
	 * @return void
	 */
	function build_filename($header, $trailer){
		return $header . $trailer;
	}

	static function get_filename_from_post_parent($post, $name_from_post, &$post_parent){
		//retrive post_parent
		$post_parent = get_post($post->post_parent);

		if (($name_from_post)){
			//generate filename from post_parent title
			$new_filename = $post_parent->post_title;
		} else {
			$new_filename = '';
		}

		return $new_filename;
	}

	/**
	 * Check if media is attached to a post and if the post have a title
	 *
	 * @param object $post
	 * @return void
	 */
	static function check_post_parent($post){
		$post_parent = $post->post_parent;

		if (! $post_parent){
			//no post found
			echo __('The media is not attached to a post!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));
			return false;
		}

		$new_filename = self::get_filename_from_post_parent($post, true, $post_parent);

		if (! $new_filename){
			//no title set
			echo __('The post has no title!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));
			return false;
		}

		//everything is ok
		return true;
	}

	/**
	 * update post title
	 * 
	 * @param object $post_changes
	 * @param string $post_title
	 * @return object
	 */
	static function do_retitle($post_changes, $post_title)
	{
		$post_changes['post_title'] = $post_title;

		return $post_changes;
	}

	/**
	 * Handles the actual rename process
	 *
	 * @param integer $attachment_id
	 * @param string $new_filename
	 * @param boolean $retitle
	 * @param boolean $title_from_post
	 * @param boolean $name_from_post
	 * @param boolean $check_post_parent
	 * @param boolean $force_lowercase
	 * @param boolean $force_serializiation
	 * @param boolean $rename
	 * @return void
	 */
	static function do_rename($attachment_id, $new_filename, $retitle = 0, $title_from_post = 0, $name_from_post = 0, $check_post_parent = true, $force_lowercase = false, $force_serializiation = false, $rename = true) {
		//Variables
		$options = new pmr_options();
		$post = get_post($attachment_id);
		$file_parts = self::get_file_parts($attachment_id);
		$file_path = $file_parts['filepath'];
		$file_subfolder = $file_parts['subfolder'];
		$file_old_filename = $file_parts['filename'];
		$file_original_filename = $file_parts['originalfilename'];
		$file_filename_ends_with = $file_parts['endswith'];
		$file_extension = $file_parts['extension'];
		$file_edited = $file_parts['edited'];

		//Change the attachment post
		$post_changes['ID'] = $post->ID;

		if ($force_serializiation){
			$options->serialize_if_filename_present = true;
		}

		if (($title_from_post) || ($name_from_post)){
			if ($check_post_parent){
				if (! self::check_post_parent($post)){
					//the media is not attached to a post or the post has no title
					//this check is needed to avoid issues with third party code that calls directly pmr->do_rename
					return;
				}else{
					$post_parent = get_post($post->post_parent);
					$new_filename = self::get_filename_from_post_parent($post, true, $post_parent);
				}
			} else {
				$post_parent = get_post($post->post_parent);
				$new_filename = self::get_filename_from_post_parent($post, true, $post_parent);
			}
		}

		if ((! $rename) && ($retitle)){
			//renaming is disabled and retitle is enabled
			//change post title
			$post_changes = self::do_retitle($post_changes, $new_filename);

			//update post in databse
			wp_update_post($post_changes);

			return 1;
		}

		$new_filename_unsanitized = $new_filename;

		$new_filename = self::clear_filename($options, $new_filename, $file_edited, $file_filename_ends_with, $file_extension, $file_subfolder);

		$file_abs_path = $file_path . $file_old_filename . '.' .$file_extension;
		$file_abs_dir = $file_path;
		
		$file_rel_path = $file_subfolder . $file_old_filename . '.' .$file_extension;

		$new_file_rel_path = preg_replace('~[^/]+$~', $new_filename . '.' . $file_extension, $file_rel_path);
		$new_file_abs_path = preg_replace('~[^/]+$~', $new_filename . '.' . $file_extension, $file_abs_path);

		if (pmr_plugins::is_plugin_active(constant("pluginAmazonS3AndCloudfront"))) {
			//plugin is active
			add_filter('as3cf_get_attached_file_copy_back_to_local', '__return_true');
		}

		//attachment miniatures
		$searches = self::get_attachment_urls($attachment_id, '', $file_edited, Operation::search);

		//Validations
		$validation_message = self::validate($file_abs_path, $post, $attachment_id, $new_filename, $options, $new_file_abs_path, $file_abs_dir);

		if ($validation_message != ''){
			return $validation_message;
		}

		//change post data
		$post_changes['guid'] = preg_replace('~[^/]+$~', $new_filename . '.' . $file_extension, $post->guid);

		//Change post title
		//if action is "actionRenameFromPostTitle" retrieve title for post related to media file
		if ($retitle){
			$post_changes = self::do_retitle($post_changes, self::filename_to_title($new_filename_unsanitized));
		}elseif ($title_from_post){
			$post_changes['post_title'] = self::filename_to_title($post_parent->post_title);
		}else{
			$post_changes = self::do_retitle($post_changes, $post->post_title);
		}

		$post_changes['post_name'] = wp_unique_post_slug($new_filename, $post->ID, $post->post_status, $post->post_type, $post->post_parent);
		wp_update_post($post_changes);

		// Change attachment post metas & rename files
		if ($options->option_debug_mode){
			//execute rename showing errors (if present)
			//read error reporting settings
			$error_level = error_reporting();
			$display_errors = ini_get('display_errors');

			//enable errors display
			error_reporting(E_ALL); 
			ini_set('display_errors', 1);

			try{
				//copy old file to new one
				if (!copy($file_abs_path, $new_file_abs_path)) return __('File renaming error! Tried to copy ' . $file_abs_path . ' to ' . $new_file_abs_path);

				update_attached_file($post->ID , $new_filename);

				//delete old media file, thumbnails will be deleted later
				if (!unlink($file_abs_path)) return __('File renaming error! Tried to delete ' . $file_abs_path);
			}catch(exception $e){
				//reset error reporting settings
				error_reporting($error_level);
				ini_set('display_errors', $display_errors);

				//avoid to update posts due to renaming failure
				return;
			}

			//reset error reporting settings
			error_reporting($error_level);
			ini_set('display_errors', $display_errors);
		} else {
			//execute rename hiding errors (if present)
			//copy old file to new one
			if (!@copy($file_abs_path, $new_file_abs_path)) return __('File renaming error! Tried to copy ' . $file_abs_path . ' to ' . $new_file_abs_path);

			update_attached_file($post->ID , $new_filename);

			//delete old media file, thumbnails will be deleted later
			if (!@unlink($file_abs_path)) return __('File renaming error! Tried to delete ' . $file_abs_path);
		}

		//delete thumbnails (they will be recreated)
		self::delete_files($attachment_id, $file_old_filename, $file_extension, $options);

		//update metadata for media file
		update_post_meta($attachment_id, '_wp_attached_file', $new_file_rel_path);

		$metas = self::update_metadata(wp_get_attachment_metadata($attachment_id), wp_generate_attachment_metadata($attachment_id, $new_file_abs_path), $new_filename, $file_old_filename, $attachment_id, $file_path);

		wp_update_attachment_metadata($attachment_id, $metas);

		// Replace the old with the new media link in the content of all posts and metas
		$replaces = self::get_attachment_urls($attachment_id, $file_filename_ends_with, $file_edited, Operation::replace);

		$i = 0;
		$post_types = get_post_types();

		if (! $options->option_update_revisions) {
			unset($post_types ['revision']);
		}

		unset($post_types['attachment']);

		while ($posts = get_posts(array('post_type' => $post_types, 'post_status' => 'any', 'numberposts' => 100, 'offset' => $i * 100))) {
			foreach ($posts as $post) {
				// Updating post content if necessary
				$new_post = array('ID' => $post->ID);
				$new_post['post_content'] = str_replace('\\', '\\\\', $post->post_content);
				$new_post['post_content'] = str_replace($searches, $replaces, $new_post['post_content']);
				try{
					if ($new_post['post_content'] != $post->post_content) wp_update_post($new_post);
				}catch(exception $e){
				}

				// Updating post metas if necessary
				$metas = get_post_meta($post->ID);
				foreach ($metas as $key => $meta) {
					if (str_contains($key, '_elementor_')){
						pmr_plugins::update_elementor_data($post->ID, $key, $searches, $replaces);
					} else {
							//update wp_postmeta
							$meta[0] = pmr_lib::unserialize_deep($meta[0]);
							$new_meta = pmr_lib::replace_media_urls($meta[0], $searches, $replaces);
							if ($new_meta != $meta[0]) update_post_meta($post->ID, $key, $new_meta, $meta[0]);
					}
				}
			}

			$i++;
		}

		$options->update_options();

		do_action('pmr_renaming_successful', $file_old_filename, $new_filename);

		if (pmr_plugins::is_plugin_active(constant("pluginWPML"))) {
			//plugin is active
			//Updating WPML tables
			pmr_plugins::update_wpml($attachment_id);
		}

		if (pmr_plugins::is_plugin_active(constant("pluginSmartSlider3"))) {
			//plugin is active
			//Updating SmartSlider 3 tables
			pmr_plugins::update_smartslider($file_old_filename, $new_filename, $file_extension);
		}

		if (pmr_plugins::is_plugin_active(constant("pluginRedirection"))) {
			//plugin is active
			//Adding Redirection from old ORL to the new one
			pmr_plugins::add_redirection($file_old_filename, $new_filename, $file_extension, $file_subfolder, $options->option_create_redirection, constant("pluginRedirection"));
		}

		if (pmr_plugins::is_plugin_active(constant("pluginRankMath"))) {
			//plugin is active
			//Adding Redirection from old ORL to the new one
			pmr_plugins::add_redirection($file_old_filename, $new_filename, $file_extension, $file_subfolder, $options->option_create_redirection, constant("pluginRankMath"));
		}

		return 1;
	}

	private static function validate($file_abs_path, $post, $attachment_id, $new_filename, $options, $new_file_abs_path, $file_abs_dir){
		//check if old file still exists
		if (! file_exists($file_abs_path)) return __('Can\'t find original file in the folder. Tried to rename ' . $file_abs_path, constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));

		//check if post containing media file exists
		if (!$post) return __('Post with ID ' . $attachment_id . ' does not exist!');

		//check if type of post containing media file is "attachment"
		if ($post && $post->post_type != 'attachment') return __('Post with ID ' . $attachment_id . ' is not an attachment!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));

		//check if new filename has been compiled
		if (!$new_filename) return __('The field is empty!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));

		//check if new filename contains bad characters
		if ($options->option_remove_accents){
			if ($new_filename != remove_accents($new_filename)) return __('Bad characters or invalid filename!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));
		}else{
			//accent removal disabled by user
		}

		if ($options->option_sanitize_filename){
			if ($new_filename != sanitize_file_name($new_filename)) return __('Bad characters or invalid filename!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));
		}else{
			//filename sanitizazion disabled by user
		}

		//check if destination folder already contains a file with the target filename
		if (file_exists($new_file_abs_path)) return __('A file with that name already exists in the containing folder!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));

		//check if destination folder is writable
		if (!is_writable(realpath($file_abs_dir))) return __('The media containing directory is not writable!', constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'));

		return '';
	}

	/**
	 * Undocumented function
	 *
	 * @param pmr_options $options
	 * @param string $new_filename
	 * @param string $file_edited
	 * @param string $file_filename_ends_with
	 * @param string $file_extension
	 * @param string $file_subfolder
	 * @return string
	 */
	private static function clear_filename($options, $new_filename, $file_edited, $file_filename_ends_with, $file_extension, $file_subfolder){
		$result = $new_filename;

		//restore '-scaled' filename part if user removed it (due to poor code implementation in WordPress core)
		if (($file_edited) && ($file_filename_ends_with == '-scaled') && ! (self::ends_with($result, '-scaled'))){
			$result = $result . $file_filename_ends_with;
		}

		//sanitizing file name (using sanitize_title because sanitize_file_name doesn't remove accents)
		if ($options->option_remove_accents){
			$result = remove_accents($result);
		} else{
			//accent removal disabled by user
		}

		//add header to filename only if it is not already present
		if (($options->option_filename_header != "") && (self::starts_with($result, $options->option_filename_header) == -1)){
			$result = $options->option_filename_header . ' ' . $result;
		} else{
			//no header entered by user
		}

		//add trailer to filename only if it is not already present
		if (($options->option_filename_trailer != "") && ! (self::ends_with($result, $options->option_filename_trailer))){
			$result = $result . ' ' . $options->option_filename_trailer;
		} else{
			//no trailer entered by user
		}

		if ($options->option_sanitize_filename){
			$result = sanitize_file_name($result);
		} else{
			//sanitization disabled by user
		}

		//force lowercase if requested
		if ($options->option_convert_to_lowercase){
			$result = strtolower($result);
		}

		//serialize filename if option is enabled
		if ($options->serialize_if_filename_present){
			$result = self::serialize_if_file_exists($result, $file_extension, $file_subfolder);
		} else{
			//don't serialize filename: can result in "filename already exists" error
		}

		try{
			if (pmr_plugins::is_plugin_active(constant("pluginArchivarixExternalImagesImporter"))) {
				//plugin is active, remove last . added by archivarix
				$result = rtrim($result, '.');
			}
		}catch(exception $e){
		}

		return $result;
	}

	/**
	 * Updates metadata array
	 *
	 * @param array $old_meta
	 * @param array $new_meta
	 * @param string $new_filename
	 * @param string $old_filename
	 * @param integer $attachment_id
	 * 
	 * @return void
	 */
	static function update_metadata($old_meta, $new_meta, $new_filename, $old_filename, $attachment_id, $file_path){
		$result = $old_meta;

		//update ShortPixel thumbnails data
		$result = pmr_plugins::update_shortpixel_metadata($result, $old_filename, $new_filename, $attachment_id, $file_path);

		//replace original filename (needed to ensure correct wp-cli management of thumbnails renegeration)
		if (array_key_exists('original_image', $result)){
			$result['original_image'] = $new_filename;
			}

		foreach ($new_meta as $key => $value) {
			switch ($key){
				case 'file':
					//change the file name in meta
					$result[$key] = $value;
					break;
				case 'sizes':
					//change the file name in miniatures
					$result[$key] = $value;
					break;
				default:
					if (is_array($result)){
						//$result is an array
						if (! array_key_exists($key, $result)){
							//add missing keys (if needed)
							// array_push($result[$key], $value);
							$result[$key] = $value;
						}
					} else {
						//$result is not an array
						$result[$key] = $value;
					}

			}
		}

		return $result;
	}

	/**
	 * Delete thumbnail files from upload folder
	 *
	 * @param integer $attachment_id
	 * @param string $original_filename
	 * @param string $extension
	 * @param array $$option_debug_mode
	 * @return void
	 */
	static function delete_files($attachment_id, $original_filename, $extension, $option_debug_mode){
		$uploads_path = wp_upload_dir();
		$uploads_path = $uploads_path['path'];

		foreach (get_intermediate_image_sizes() as $size) {
			$size_data = image_get_intermediate_size($attachment_id, $size);
			if (is_bool($size_data)){
				//image intermediate sizes not found
			} else {
				if (! array_key_exists('file', $size_data)){
					//array key is missing
					if ($option_debug_mode){
						echo 'array key is missing';
					}
				} else{
					if ($size_data['file'] == ''){
						//filename is missing
						if ($option_debug_mode){
							echo 'filename is missing';
						}
					} else {
						//delete the file
						@unlink (realpath($uploads_path . DIRECTORY_SEPARATOR . $size_data['path']));
					}
				}
			}
		}
	}

#region support functions

	/**
	 * Get attachment filename
	 *
	 * @param integer $post_id
	 * @return void
	 */
	static function get_filename($post_id) {
		$filename = get_attached_file($post_id);

		return $filename;
	}

	/**
	 * Get attachment filename
	 *
	 * @param integer $post_id
	 * @return void
	 */
	static function get_file_parts($post_id) {
		$filename = self::get_filename($post_id);

		return self::file_parts($filename, $post_id);
	}

	/**
	 * Extract filename and extension
	 *
	 * @param string $filename
	 * @param integer $post_id
	 * @return void
	 */
	static function file_parts($filename, $post_id){
		//read post meta to check if image has been edited
		$post_meta = get_post_meta($post_id, '_wp_attachment_metadata', 1);
		$file_path = wp_upload_dir();

		if (isset($post_meta['original_image'])){
			$edited = true;
			$original_filename = $post_meta['original_image'];
		} else {
			$edited = false;
			$original_filename = "";
		}

		//separate filename and extension
		preg_match('~([^/]+)\.([^\.]+)$~', basename($filename), $file_parts);

		$filepath = str_replace(basename($filename), '', $filename);
		$subfolder = str_replace($file_path['basedir'], '', $filepath);

		//remove first slash from subfolder (it breaks image metadata)
		if (strlen($subfolder) > 0){
			if (substr($subfolder, 0, 1) == '/') {
				$subfolder = substr($subfolder, 1, strlen($subfolder) -1);
			}
		}

		if ((! is_array($file_parts)) || (sizeof($file_parts) < 2)){
			//file name or extension is missing
			echo "file name or extension is missing";
			$result = array(
				'filepath'			=> $filepath,
				'subfolder'			=> $subfolder,
				'filename'			=> "",
				'extension'			=> "",
				'endswith'			=> "",
				'edited'			=> $edited,
				'originalfilename'	=> $original_filename
			);
		} else {
			$filename = $file_parts[1];

			//check if filename ends with "-scaled"
			if (($edited) && (self::ends_with($file_parts[1], '-scaled'))) {
				$endsWith = '-scaled';
			} else {
				$endsWith = '';
			}

			$result = array(
				'filepath'			=> $filepath,
				'subfolder'			=> $subfolder,
				'filename'			=> $filename,
				'extension'			=> $file_parts[2],
				'endswith'			=> $endsWith,
				'edited'			=> $edited,
				'originalfilename'	=> $original_filename
			);
		}

		return $result;
	}

	/**
	 * add support for calling Phoenix Media Rename from frontend
	 *
	 * @return boolean
	 */
	static function frontend_support(){
		if (! function_exists('wp_crop_image')) {
			include(ABSPATH . 'wp-admin/includes/image.php');
		}
	}

	/**
	 * Check if strings ends with a sequence of characters
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @return void
	 */
	static function ends_with($haystack, $needle) {
		$length = strlen($needle);
		if(!$length) {
			return true;
		}
		return substr($haystack, -$length) === $needle;
	}

	/**
	 * Search a substring at the start of a string
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @return the match if found (i.e. $haystack = '0001523', $needle = '0', returns '000'), -1 otherwise
	 */
	static function starts_with($haystack, $needle) {
		$re = '/^[' . $needle . ']+/';

		if (preg_match($re, $haystack, $matches, PREG_OFFSET_CAPTURE)){
			return $matches[0][0];
		} else {
			return -1;
		}
	}

	/**
	 * Adds more problematic characters to the "sanitize_file_name_chars" filter
	 *
	 * @param string $special_chars
	 * @return void
	 */
	static function add_special_chars($special_chars) {
		return array_merge($special_chars, array('%', '^'));
	}

	/**
	 * Returns the attachment URL and sizes URLs, in case of an image
	 *
	 * @param integer $attachment_id
	 * @param string $filename_ends_with
	 * @param boolean $remove_suffix
	 * @param Operation $operation
	 * @return array
	 */
	static function get_attachment_urls($attachment_id, $filename_ends_with, $remove_suffix, $operation) {
		$urls = array(wp_get_attachment_url($attachment_id));
		// $filename = '';

		if (wp_attachment_is_image($attachment_id)) {
			foreach (get_intermediate_image_sizes() as $size) {
				$image = wp_get_attachment_image_src($attachment_id, $size);

				// if (($operation == Operation::replace) && (remove_suffix)) {
				// 	// get filename
				// 	preg_match('~([^/]+)(-scaled-)(.)+\.([^\.]+)$~', $image[0], $file_parts);

				// 	if ($file_parts[2] = '-scaled-'){
				// 		//image is a miniature, remove -scaled to obtain original filename
				// 		$image[0] = preg_replace('~([^/]+)(-scaled-)(.+)\.([^\.]+)$~', '\1-\3', $image[0]);

				// 		// //get file path
				// 		// $filepath = substr($image[0], 0, strrpos($image[0], '/')) . '/';

				// 		//scaled image, add scaled filename
				// 		$image[0] = $image[0] . '.' . end($file_parts);
				// 	}
				// }

				$urls[] = $image[0];
			}

		}

		return array_unique($urls);
	}

	/**
	 * Convert filename to post title
	 *
	 * @param string $filename
	 * @return void
	 */
	static function filename_to_title($filename) {
		return $filename;
	}

#endregion
}

/**
 * Polyfill for compatibility with old PHP versions (less than 7)
 */
if (!function_exists('is_countable')) {
	function is_countable($var) {
		return (is_array($var) || $var instanceof Countable);
	}
}
