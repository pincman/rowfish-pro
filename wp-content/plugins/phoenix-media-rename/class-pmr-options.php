<?php
/*
* Phoenix Media Rename options management
*
*/

class pmr_options {

	private $options;
	public $option_update_revisions;
	public $option_sanitize_filename;
	public $option_remove_accents;
	public $option_debug_mode;
	public $option_create_redirection;
	public $option_serialize_if_filename_present;
	public $option_filename_header;
	public $option_filename_trailer;
	public $option_convert_to_lowercase;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->options = get_option('pmr_options');

		if (isset($this->options)){
			$this->option_update_revisions = $this->get_option_boolean($this->options, 'pmr_update_revisions', true);
			$this->option_sanitize_filename = $this->get_option_boolean($this->options, 'pmr_sanitize_filenames', true);
			$this->option_remove_accents = $this->get_option_boolean($this->options, 'pmr_remove_accents', true);
			$this->option_debug_mode = $this->get_option_boolean($this->options, 'pmr_debug_mode', false);
			$this->option_create_redirection = $this->get_option_boolean($this->options, 'pmr_create_redirection', false);
			$this->serialize_if_filename_present = $this->get_option_boolean($this->options, 'pmr_serialize_if_filename_present', true);
			$this->option_convert_to_lowercase = $this->get_option_boolean($this->options, 'pmr_filename_lowercase', true);
			$this->option_filename_header = $this->get_option_text($this->options, 'pmr_filename_header', '');
			$this->option_filename_trailer = $this->get_option_text($this->options, 'pmr_filename_trailer', '');

			$this->clear_options();
		}
	}

	/**
	 * Updates options if necessary
	 * Some option value could be changed during the rename process
	 */
	public function update_options(){
		$local_options = $this->get_all_options();

		foreach ($local_options as $option) {
			$option['value'] = pmr_lib::unserialize_deep($option['value']);
			$new_option = pmr_lib::replace_media_urls($option['value'], $searches, $replaces);
			if ($new_option != $option['value']) update_option($option['name'], $new_option);
		}
	}

#region private methods

	/**
	 * Clears options
	 *
	 * @return void
	 */
	private function clear_options(){
		//clear header and trailer
		if ($this->option_filename_header){
			$this->option_filename_header = trim($this->option_filename_header, " -");
		} else {
			$this->option_filename_header = '';
		}

		if ($this->option_filename_trailer){
			$this->option_filename_trailer = trim($this->option_filename_trailer, " -");
		} else {
			$this->option_filename_trailer = '';
		}
	}

	/**
	 * Retrive boolean option value
	 *
	 * @param array $options
	 * @param string $name
	 * @param boolean $default
	 * @return boolean
	 */
	private function get_option_boolean($options, $name, $default = true){
		$result = $default;

		if (isset($options[$name])){
			if ($options[$name]) {
				$result = true;
			}else{
				$result = false;
			}
		} else {
			// default
			$result = $default;
		}

		return $result;
	}

	/**
	 * Retrive text option value
	 *
	 * @param array $options
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	private function get_option_text($options, $name, $default = ''){
		if (isset($options[$name])){
			if ($options[$name]) {
				return $options[$name];
			}else{
				return '';
			}
		} else {
			// default
			return $default;
		}
	}

	/**
	 * Get all options
	 *
	 * @return array
	 */
	private function get_all_options() {
		return $GLOBALS['wpdb']->get_results("SELECT option_name as name, option_value as value FROM {$GLOBALS['wpdb']->options}", ARRAY_A);
	}

#endregion

}