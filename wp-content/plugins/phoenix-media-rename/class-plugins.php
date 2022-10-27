<?php
/*
* Phoenix Media Rename main class
*
*/

#region constants

define("pluginArchivarixExternalImagesImporter", "archivarix-external-images-importer/archivarix-external-images-importer.php");
define("pluginAmazonS3AndCloudfront", "amazon-s3-and-cloudfront/wordpress-s3.php");
define("pluginSmartSlider3", "smart-slider-3/smart-slider-3.php");
define("pluginShortpixelImageOptimiser", "shortpixel-image-optimiser/wp-shortpixel.php");
define("pluginWPML", "sitepress-multilingual-cms/sitepress.php");
define("pluginRedirection", "redirection/redirection.php");
define("pluginRankMath", "seo-by-rank-math/rank-math.php");
define("pluginElementor", "elementor/elementor.php");

abstract class elementor_element
{
	const css = 0;
	const data = 1;
}

#endregion

class pmr_plugins{

	/**
	 * check if plugin is active
	 *
	 * @param string $plugin_name
	 * @return boolean
	 */
	static function is_plugin_active($plugin_name){
		if(in_array($plugin_name, apply_filters('active_plugins', get_option('active_plugins')))){ 
			return true;
		} else {
			return false;
		}
	}

#region Smart Slider compatibility

	/**
	 * Update Smart Slider 3 custom table
	 *
	 * @param string $old_filename
	 * @param string $new_filename
	 * @param string $extension
	 * @return void
	 */
	static function update_smartslider($old_filename, $new_filename, $extension){
		global $wpdb;

		//compose file names
		$old_filename = $old_filename . '.' . $extension;
		$new_filename = $new_filename . '.' . $extension;

		if(empty($old_filename) || empty($new_filename))
		{
			return false;
		}
		if ($old_filename == ''){
			return false;
		}

		//escape filename for use in LIKE statement
		$old_filename = $wpdb->esc_like($old_filename);

		$filter = '%/'. $old_filename;

		//compose Smart Slider table name
		$tablename = $wpdb->prefix . 'nextend2_smartslider3_slides';

		if (! pmr_lib::table_exist($tablename)){
			//if table does not exist, exit and return false
			return false;
		}else{
			//if table exist, change file name
			$sqlQuery = "UPDATE ". $tablename ." SET thumbnail = REPLACE(thumbnail, %s, %s), params = REPLACE(params, %s, %s) WHERE thumbnail LIKE %s";

			$updated = $wpdb->query(
				$wpdb->prepare(
					$sqlQuery, $old_filename, $new_filename, $old_filename, $new_filename, $filter
				));
		}

		$tablename = $wpdb->prefix . 'nextend2_image_storage';

		if (pmr_lib::table_exist($tablename)){
			//if table exist, change file name (unnecessary table, does not exit if table is missing)
			$sqlQuery = "UPDATE ". $tablename ." SET image = REPLACE(image, %s, %s) WHERE image LIKE %s";

			$updated = $wpdb->query(
				$wpdb->prepare(
					$sqlQuery, $old_filename, $new_filename, $filter
				));
		}

		return true;
	}

#endregion

#region WPML compatibility

	/**
	 * Update Smart WPML custom table
	 *
	 * @param string $extension
	 * @return void
	 */
	static function update_wpml($post_id){
		// Get "trid" of the file
		$trid = apply_filters('wpml_element_trid', NULL, $post_id, 'post_attachment');

		if (empty($trid)) {
			//translation not found
		} else {
			//get all translations
			$translations = apply_filters('wpml_get_element_translations', NULL, $trid);

			//iterates through translations to update attachment metadata
			foreach ($translations as $translation) {
				if ($post_id == $translation->element_id) {
					//update filename
					update_post_meta($translation->element_id, '_wp_attached_file', get_post_meta($translation->element_id, '_wp_attached_file', true));

					//update metadata
					update_post_meta($translation->element_id, '_wp_attachment_metadata', get_post_meta($translation->element_id, '_wp_attachment_metadata', true));
				}
			}
		}
	}

#endregion

#region Redirection compatibility

	/**
	 * Add a redirection from the old URL to the NEW one using Redirection plugin
	 *
	 * @param string $old_filename
	 * @param string $new_filename
	 * @param string $extension
	 * @param boolean $option_create_redirection
	 * @return void
	 */
	static function add_redirection($old_filename, $new_filename, $extension, $file_subfolder, $option_create_redirection, $plugin){
		if ($option_create_redirection){
			//option is active

			//fix file name
			if ($file_subfolder){
				$old_filename = $file_subfolder . $old_filename . '.' . $extension;
				$new_filename = $file_subfolder . $new_filename .'.' . $extension;
			} else {
				$old_filename = $old_filename . '.' . $extension;
				$new_filename = $new_filename .'.' . $extension;
			}

			//add upload folder
			if (defined('UPLOADS')) {
				$upload_folder = UPLOADS;

				$old_filename = get_site_url() . '/' . $upload_folder . '/' . $old_filename;
				$new_filename = get_site_url() . '/' . $upload_folder . '/' . $new_filename;
			} else {
				$upload_folder = wp_upload_dir()['baseurl'] . '/';

				$old_filename = $upload_folder . $old_filename;
				$new_filename = $upload_folder . $new_filename;
			}

			$old_filename = str_replace('\\', '/' , $old_filename);
			$new_filename = str_replace('\\', '/' , $new_filename);

			switch ($plugin){
				case constant("pluginRedirection"):
				//Redirection
					try{
						if (class_exists('Red_Item')){
							//include Redirection code
							require_once WP_PLUGIN_DIR . '/redirection/models/group.php';

							$details = [
								'url'			=> $old_filename,
								'action_data'	=> [ 'url' => $new_filename ],
								'action_type'	=> 'url',
								'title'			=> 'Phoenix Media Rename',
								'status'		=> 'enabled',
								'regex'			=> false,
								'group_id'		=> 2, //set group to "updated posts"
								'match_type'	=> 'url',
							];

							//add redirection via Redirection's functions
							$result = Red_Item::create($details);
						}
					}catch(exception $e){
					}

					break;
				case constant("pluginRankMath"):
				//Rank Math SEO
					try{
						$redirection = RankMath\Redirections\Redirection::from(
							[
							'url_to' => $new_filename,
							'header_code' => '301',
							]
							);

						$redirection->set_nocache(true);
						$redirection->add_source($old_filename, 'exact');
						$redirection->add_destination($new_filename);
						$redirection->save();
					}catch(exception $e){
					}
					break;
			}
		}
	}

#endregion

#region ShortPixel compatibility

	/**
	 * Update all ShortPixel metadata to avoid another compression of the image after the renaming process
	 *
	 * @param array $result
	 * @param string $old_filename
	 * @param string $new_filename
	 * @param integer $attachment_id
	 * @param string $file_path
	 * 
	 * @return array
	 */
	static function update_shortpixel_metadata($result, $old_filename, $new_filename, $attachment_id, $file_path){
		if (pmr_plugins::is_plugin_active(constant("pluginShortpixelImageOptimiser"))) {
			//change filename in thumnail list
			$shortpixelKey = 'thumbsOptList';
			$result = self::update_single_shortpixel_metadata($result, $shortpixelKey, $old_filename, $new_filename);

			//change filename in exclusion list
			$shortpixelKey = 'excludeSizes';
			$result = self::update_single_shortpixel_metadata($result, $shortpixelKey, $old_filename, $new_filename);

			self::update_shortpixel_filenames($attachment_id, $old_filename, $new_filename, $file_path);

			//update shortpixel custom tables
			self::update_single_shortpixel_table($attachment_id, $old_filename, $new_filename);
			}

		//return result even if plugin is inactive
		return $result;
	}

	/**
	 * Update shortpixel custom tables
	 *
	 * @param integer $attachment_id
	 * @param string $old_filename
	 * @param string $new_filename
	 * @param string $file_path
	 * @return void
	 */
	static function update_shortpixel_filenames($attachment_id, $old_filename, $new_filename, $file_path){
		global $wpdb;

		try {
			$table_name = $wpdb->prefix . 'shortpixel_postmeta';

			$query = $wpdb->prepare('SELECT extra_info
			FROM ' . $table_name . '
			WHERE parent = %d
			OR attach_id = %d',
			$attachment_id, $attachment_id
			);

			$thumbnails = $wpdb->get_results($query, ARRAY_A);

			foreach ($thumbnails as $thumbnail){
				try{
					//get the webp filename
					$image = json_decode($thumbnail['extra_info'])->webp;

					//rename the file
					self::rename($file_path, $image, $new_filename);

					//get the avif filename
					$image = json_decode($thumbnail['extra_info'])->avif;

					//rename the file
					self::rename($file_path, $image, $new_filename);
				} catch (exception $e){

				}
			}

		} catch(exception $e){

		}
	}

	/**
	 * Renames a file
	 *
	 * @param string $path
	 * @param string $old_filename
	 * @param string $new_filename
	 * 
	 * @return void
	 */
	private static function rename($path, $old_filename, $new_filename){
		//get filename
		$file_parts = pathinfo($old_filename);
		$namepart = $file_parts['filename'];
		$pattern = "/[-][0-9]+[x][0-9]+$/i";

		if (preg_match($pattern, $namepart)){
			//filename ends with resolution, it is a thumbnail
			$position = strrpos($namepart, "-");

			//remove the resolution to get the real name
			$real_name = substr($namepart, 0, $position);
		} else {
			//filename doesn't ends with resolution, it is the main file
			$real_name = $namepart;
		}

		//create full filenames
		$full_old_filename = $path . $old_filename;
		$full_new_filename = $path . str_replace($real_name, $new_filename, $old_filename);

		//create the new file
		if (!copy($full_old_filename, $full_new_filename)) return __('File renaming error! Tried to copy ' . $full_old_filename . ' to ' . $full_new_filename);

		//delete old media file, thumbnails will be deleted later
		if (!unlink($full_old_filename)) return __('File renaming error! Tried to delete ' . $full_old_filename);
	}

	/**
	 * Update shortpixel custom tables
	 *
	 * @param integer $attachment_id
	 * @param string $old_filename
	 * @param string $new_filename
	 * @return void
	 */
	static function update_single_shortpixel_table($attachment_id, $old_filename, $new_filename){
		global $wpdb;

		$table_name = $wpdb->prefix . 'shortpixel_postmeta';

		try {
			$wpdb->query(
				$wpdb->prepare('UPDATE ' . $table_name . '
					SET extra_info = REPLACE (extra_info, %s, %s)
					WHERE parent = %d
					OR attach_id = %d',
					array(str_replace('/', '\/', $old_filename) , str_replace('/', '\/', $new_filename), $attachment_id, $attachment_id)
				)
			);
		} catch(exception $e){

		}
	}

	/**
	 * Update single ShortPixel metadata
	 *
	 * @param array $result
	 * @param string $key: metadata to update
	 * @param string $old_filename
	 * @param string $new_filename
	 * 
	 * @return array
	 */
	static function update_single_shortpixel_metadata($result, $key, $old_filename, $new_filename){
		//check if Shortpixel data contains thumbs data
		try{
			//check if result contains ShortPixel data
			if (array_key_exists('ShortPixel', $result)){
				//check if ShortPixel data contains key
				if (array_key_exists($key, $result['ShortPixel'])){
					//iterates through ShortPixel data to update filename
					for ($i = 0; $i < count($result['ShortPixel'][$key]); $i++){
						try{
							$shortpixel_meta = $result['ShortPixel'][$key][$i];

							$new_shortpixel_meta = str_replace($old_filename, $new_filename, $shortpixel_meta);

							if ($shortpixel_meta != $new_shortpixel_meta){
								$result['ShortPixel'][$key][$i] = $new_shortpixel_meta;
							}
						}catch(exception $e){

						}
					}
				}
			}
		} catch(exception $e) {

		}

		return $result;
	}
#endregion

#region Elementor compatibility

	/**
	 * update elementor data, it will be used by Elementor to regenerate css file
	 *
	 * @param integer $post_id
	 * @param string $key
	 * @param array $searches
	 * @param array $replaces
	 * @return void
	 */
	static function update_elementor_data($post_id, $key = '', $searches = '', $replaces = ''){
		global $wpdb;

		if (pmr_plugins::is_plugin_active(constant("pluginElementor"))) {
			$table_name = $wpdb->prefix . 'postmeta';

			switch ($key){
				case '_elementor_css':
					//delete elementor css, it will be generated at first page visit
					try {
						$wpdb->query(
							$wpdb->prepare('DELETE FROM ' . $table_name . '
								WHERE post_id = %d
								AND meta_key = %s',
								array($post_id, '_elementor_css')
							)
						);
					} catch(exception $e){

					}
					break;
				case '_elementor_data':
					try{
						for ($i = 0; $i < sizeof($searches); $i++){
							$wpdb->query(
								$wpdb->prepare('UPDATE ' . $table_name . '
									SET meta_value = REPLACE (meta_value, %s, %s)
									WHERE post_id = %d
									AND meta_key = %s',
									array(str_replace('/', '\/', $searches[$i]) , str_replace('/', '\/', $replaces[$i]), $post_id, $key)
								)
							);
						}
					} catch(exception $e){

					}
				break;
				default:
					//update wp_postmeta
					// $meta[0] = pmr_lib::unserialize_deep($meta[0]);
					// $new_meta = pmr_lib::replace_media_urls($meta[0], $searches, $replaces);
					// //there is an issue with Elementor, check when _wp_page_template changes
					// if ($new_meta != $meta[0]) update_post_meta($post_id, $key, $new_meta, $meta[0]);
			}
		}
	}

#endregion

}

