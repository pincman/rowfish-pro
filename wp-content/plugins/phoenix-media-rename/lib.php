<?php
/*
* support functions
*
*/

class pmr_lib{

	/**
	 * Replace the media url and fix serialization if necessary
	 *
	 * @param string $subj
	 * @param string $searches
	 * @param string $replaces
	 * @return void
	 */
	static function replace_media_urls($subj, &$searches, &$replaces) {
		$subj = is_object($subj) ? clone $subj : $subj;

		if (!is_scalar($subj) && is_countable($subj) && count($subj)) {
			//iterates elements and replace old filename with new one
			//code suggested by alx359
			foreach($subj as $key => $f) {
				$item = &$subj[$key];
				$item = self::replace_media_urls($item, $searches, $replaces);
		 }

		} else {
			$subj = is_string($subj) ? str_replace($searches, $replaces, $subj) : $subj;
		}
		
		return $subj;
	}

	/**
	 * Unserializes a variable until reaching a non-serialized value
	 *
	 * @param string $var
	 * @return void
	 */
	static function unserialize_deep($var) {
		while (is_serialized($var)) {
			$var = @unserialize($var);
		}

		return $var;
	}

	/**
	 * Check if table exists
	 *
	 * @param string $tablename
	 * @return boolean
	 */
	static function table_exist($tablename){
		global $wpdb;

		if($wpdb->get_var("SHOW TABLES LIKE '$tablename'") == $tablename){
			//table is not present
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Prints the javascript used by Phoenix Media Rename options page
	 */
	static function print_options_js(){
		if(get_current_screen() -> id == 'settings_page_pmr-setting-admin') {
			wp_enqueue_script(constant('PHOENIX_MEDIA_RENAME_TEXT_DOMAIN'), plugins_url('js/options.min.js', __FILE__), array('jquery'), '1.0.0');
		}
	}
}