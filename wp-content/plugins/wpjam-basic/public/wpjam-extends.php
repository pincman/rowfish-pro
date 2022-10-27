<?php
class WPJAM_Extend_Type extends WPJAM_Register{
	public function get_file($extend){
		$file	= '';

		if($this->hierarchical){
			if(is_dir($this->dir.'/'.$extend)){
				$file	= $this->dir.'/'.$extend.'/'.$extend.'.php';
			}
		}else{
			if(pathinfo($extend, PATHINFO_EXTENSION) == 'php'){
				$file	= $this->dir.'/'.$extend;
			}
		}

		return ($file && is_file($file)) ? $file : '';
	}

	public function get_values($for=''){
		if($for == 'blog'){
			$values	= wpjam_get_option($this->name);
			$values	= $values ? array_filter($values) : [];
		}elseif($for == 'network'){
			$values	= wpjam_get_site_option($this->name);
			$values	= $values ? array_filter($values) : [];		
		}else{
			$values	= $this->get_values('blog');

			if($this->sitewide && is_multisite()){
				$values	= array_merge($values, $this->get_values('network'));
			}
		}

		return $values;
	}

	public function get_fields(){
		if($this->sitewide && is_multisite() && is_network_admin()){
			$values	= $this->get_values('network');
		}else{
			$values	= $this->get_values('blog');
		}

		$fields	= [];
		$handle	= opendir($this->dir);

		if($handle){
			while(($extend = readdir($handle)) !== false){
				if($extend == '.' || $extend == '..'){
					continue;
				}

				$file	= $this->get_file($extend);

				if($file){
					$data	= $this->get_file_data($file);

					if($data['Name']){
						$title	= $data['Name'];

						if($data['URI']){
							$title	= '<a href="'.$data['URI'].'" target="_blank">'.$title.'</a>';	
						}
						
						$fields[$extend] = [
							'title'			=> $title,
							'type'			=> 'checkbox',
							'value'			=> isset($values[$extend]) ? 1 : 0,
							'description'	=> $data['Description']
						];
					}
				}
			}

			closedir($handle);
		}

		$fields	= wp_list_sort($fields, 'value', 'DESC', true);

		if($this->sitewide && is_multisite() && !is_network_admin()){
			foreach($this->get_values('network') as $extend => $value){
				unset($fields[$extend]);
			}
		}

		return $fields;
	}

	public function register_option(){
		wpjam_register_option('wpjam-extends', [
			'fields'	=> $this->get_fields(),
			'ajax'		=> false,
			'summary'	=> ($this->sitewide && is_network_admin()) ? '在管理网络激活将整个站点都会激活！' : ''
		]);
	}

	public function load(){
		foreach($this->get_values() as $extend => $value){
			$file	= $this->get_file($extend);

			if($file && is_file($file)){
				include $file;
			}
		}
	}

	public static function get_file_data($file){
		return get_file_data($file, [
			'Name'			=> 'Name',
			'URI'			=> 'URI',
			'PluginName'	=> 'Plugin Name',
			'PluginURI'		=> 'Plugin URI',
			'Version'		=> 'Version',
			'Description'	=> 'Description'
		]);
	}
}

class WPJAM_Extend{
	public static function get_instance(){
		$object	= WPJAM_Extend_Type::get('wpjam-extends');

		if(!$object){
			$object	= wpjam_register_extend_type('wpjam-extends', [
				'sitewide'	=> true,
				'dir'		=> WPJAM_BASIC_PLUGIN_DIR.'extends',
			]);
		}

		return $object;
	}

	public static function page_load($plugin_page){
		if($plugin_page == 'wpjam-extends'){
			$object	= self::get_instance();

			$object->register_option();
		}else{
			wpjam_register_page_action('verify_wpjam', [
				'submit_text'	=> '验证',
				'response'		=> 'redirect',
				'callback'		=> [self::class, 'ajax_verify'],
				'fields'		=> [
					'qr_set'	=> ['title'=>'1. 二维码',	'type'=>'fieldset',	'fields'=>[
						'qrcode_view'	=> ['type'=>'view',	'value'=>'使用微信扫描下面的二维码：'],
						'qrcode2'		=> ['type'=>'view',	'value'=>'<img src="https://open.weixin.qq.com/qr/code?username=wpjamcom" style="max-width:250px;" />']
					]],
					'keyword'	=> ['title'=>'2. 关键字',	'type'=>'view',	'value'=>'回复关键字「<strong>验证码</strong>」。'],
					'code_set'	=> ['title'=>'3. 验证码',	'type'=>'fieldset',	'fields'=>[
						'code_view'		=> ['type'=>'view',	'value'=>'将获取验证码输入提交即可！'],
						'code'			=> ['type'=>'number',	'class'=>'all-options',	'description'=>'验证码5分钟内有效！'],
					]],
					'notes'		=> ['title'=>'4. 注意事项',	'type'=>'view',	'value'=>'验证码5分钟内有效！<br /><br />如果验证不通过，请使用 Chrome 浏览器验证，并在验证之前清理浏览器缓存。'],
				]
			]);

			wp_add_inline_style('list-tables', "\n".'.form-table th{width: 100px;}');
		}
	}

	public static function on_wpjam_loaded(){
		$object	= self::get_instance();

		$object->load();
	}

	public static function on_plugins_loaded(){
		$dir	= get_template_directory().'/extends';

		if(is_dir($dir) && ($handle = opendir($dir))){
			while(($extend = readdir($handle)) !== false){
				if($extend != '.' && $extend != '..' && is_dir($dir.'/'.$extend) && is_file($dir.'/'.$extend.'/'.$extend.'.php')){
					include $dir.'/'.$extend.'/'.$extend.'.php';
				}
			}

			closedir($handle);
		}

		$actives = get_option('wpjam-actives', null);

		if(is_array($actives)){
			foreach($actives as $active){
				if(is_array($active) && isset($active['hook'])){
					add_action($active['hook'], $active['callback']);
				}else{
					add_action('wp_loaded', $active);
				}
			}

			update_option('wpjam-actives', []);
		}elseif(is_null($actives)){
			update_option('wpjam-actives', []);
		}
	}

	public static function on_admin_init(){
		wpjam_add_menu_page('wpjam-extends', [
			'parent'		=> 'wpjam-basic',
			'menu_title'	=> '扩展管理',
			'order'			=> 3,
			'function'		=> 'option',
			'load_callback'	=> [self::class, 'page_load']
		]);

		$menu_filter	= (is_multisite() && is_network_admin()) ? 'wpjam_network_pages' : 'wpjam_pages';

		if(get_transient('wpjam_basic_verify')){
			add_filter($menu_filter, [self::class, 'filter_menu_pages']);
		}else{
			$verified	= self::verify();

			if($verified){
				if(isset($_GET['unbind_wpjam_user'])){
					delete_user_meta(get_current_user_id(), 'wpjam_weixin_user');

					wp_redirect(admin_url('admin.php?page=wpjam-verify'));
				}
			}else{
				add_filter($menu_filter, [self::class, 'filter_menu_pages']);

				wpjam_add_menu_page('wpjam-verify', [
					'parent'		=> 'wpjam-basic',
					'order'			=> 3,
					'menu_title'	=> '扩展管理',
					'page_title'	=> '验证 WPJAM',
					'function'		=> 'form',
					'form_name'		=> 'verify_wpjam',
					'load_callback'	=> [self::class, 'page_load']
				]);
			}
		}
	}

	public static function filter_menu_pages($menu_pages){
		$subs	= $menu_pages['wpjam-basic']['subs'];

		if(isset($subs['wpjam-verify'])){
			$menu_pages['wpjam-basic']['subs']	= wp_array_slice_assoc($subs, ['wpjam-basic', 'wpjam-verify']);
		}else{
			$menu_pages['wpjam-basic']['subs']	= wpjam_array_except($subs, ['wpjam-about']);
		}

		return $menu_pages;
	}

	public static function verify(){
		$verify_user	= get_user_meta(get_current_user_id(), 'wpjam_weixin_user', true);

		if(empty($verify_user) || empty($verify_user['subscribe'])){
			return false;
		}elseif(time() - $verify_user['last_update'] < DAY_IN_SECONDS){
			return true;
		}

		$openid		= $verify_user['openid'];
		$hash		= $verify_user['hash']	?? '';
		$user_id	= get_current_user_id();

		if(get_transient('fetching_wpjam_weixin_user_'.$openid)){
			return false;
		}

		set_transient('fetching_wpjam_weixin_user_'.$openid, 1, 10);
		
		if($hash){
			$response	= wpjam_remote_request('http://wpjam.wpweixin.com/api/weixin/verify.json', [
				'method'	=> 'POST',
				'body'		=> ['openid'=>$openid, 'hash'=>$hash]
			]);
		}else{
			$response	= wpjam_remote_request('http://jam.wpweixin.com/api/topic/user/get.json?openid='.$openid);
		}

		if(is_wp_error($response) && $response->get_error_code() != 'invalid_openid'){
			$failed_times	= (int)get_user_meta($user_id, 'wpjam_weixin_user_failed_times');
			$failed_times ++;

			if($failed_times >= 3){	// 重复三次
				delete_user_meta($user_id, 'wpjam_weixin_user_failed_times');
				delete_user_meta($user_id, 'wpjam_weixin_user');
			}else{
				update_user_meta($user_id, 'wpjam_weixin_user_failed_times', $failed_times);
			}

			return false;
		}

		if($hash){
			$verify_user	= $response;
		}else{
			$verify_user	= $response['user'];
		}

		delete_user_meta($user_id, 'wpjam_weixin_user_failed_times');

		if(empty($verify_user) || !$verify_user['subscribe']){
			delete_user_meta($user_id, 'wpjam_weixin_user');

			return false;
		}else{
			update_user_meta($user_id, 'wpjam_weixin_user', array_merge($verify_user, ['last_update'=>time()]));

			return true;
		}
	}

	public static function ajax_verify(){
		// $url	= 'http://jam.wpweixin.com/api/weixin/qrcode/verify.json';
		$url	= 'https://wpjam.wpweixin.com/api/weixin/verify.json';
		$data	= wpjam_get_parameter('data', ['method'=>'POST', 'sanitize_callback'=>'wp_parse_args']);

		$verify_user	= wpjam_remote_request($url, [
			'method'	=> 'POST',
			'body'		=> $data
		]);

		if(is_wp_error($verify_user)){
			return $verify_user;
		}

		update_user_meta(get_current_user_id(), 'wpjam_weixin_user', array_merge($verify_user, ['last_update'=>time()]));

		return ['url'=>admin_url('admin.php?page=wpjam-extends')];
	}
}

function wpjam_register_extend_type($name, $args){
	return WPJAM_Extend_Type::register($name, $args);
}

function wpjam_get_extend_summary($file){
	$data	= WPJAM_Extend_Type::get_file_data($file);

	foreach(['URI', 'Name'] as $key){
		if(empty($data[$key])){
			$data[$key]	= $data['Plugin'.$key] ?? '';
		}
	}

	return str_replace('。', '，', $data['Description']).'详细介绍请点击：<a href="'.$data['URI'].'" target="_blank">'.$data['Name'].'</a>。';
}

add_action('wpjam_loaded',		['WPJAM_Extend', 'on_wpjam_loaded']);
add_action('plugins_loaded',	['WPJAM_Extend', 'on_plugins_loaded'], 0);

if(is_admin()){
	add_action('wpjam_admin_init',	['WPJAM_Extend', 'on_admin_init'], 11);
}

class_alias('WPJAM_Extend', 'WPJAM_Verify');