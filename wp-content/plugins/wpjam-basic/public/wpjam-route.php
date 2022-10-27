<?php
class WPJAM_Route{
	public static function init(){
		$GLOBALS['wp']->add_query_var('module');
		$GLOBALS['wp']->add_query_var('action');
		$GLOBALS['wp']->add_query_var('term_id');

		add_action('parse_request',	[self::class, 'on_parse_request']);

		add_filter('determine_current_user',	[self::class, 'filter_determine_current_user']);
		add_filter('wp_get_current_commenter',	[self::class, 'filter_current_commenter']);
		add_filter('pre_get_avatar_data',		[self::class, 'filter_pre_avatar_data'], 10, 2);
	}

	public static function on_parse_request($wp){
		$wp->query_vars	= wpjam_parse_query_vars($wp->query_vars);

		$module	= wpjam_get_current_module($wp);

		if($module){
			$object	= wpjam_get_registered_object('route_module', $module);

			if($object){
				$action		= wpjam_get_current_action($wp);
				$callback	= $object->callback;

				if($callback && is_callable($callback)){
					call_user_func($callback, $action, $module);
				}
			}

			remove_action('template_redirect',	'redirect_canonical');

			add_filter('template_include',	[self::class, 'filter_template_include']);
		}
	}

	public static function filter_template_include($template){
		$module	= get_query_var('module');
		$action	= get_query_var('action');

		$file	= $action ? $action.'.php' : 'index.php';
		$file	= STYLESHEETPATH.'/template/'.$module.'/'.$file;
		$file	= apply_filters('wpjam_template', $file, $module, $action);

		return is_file($file) ? $file : $template;
	}

	public static function filter_determine_current_user($user_id){
		if(empty($user_id)){
			$wpjam_user	= wpjam_get_current_user();

			if($wpjam_user && !empty($wpjam_user['user_id'])){
				return $wpjam_user['user_id'];
			}
		}

		return $user_id;
	}

	public static function filter_current_commenter($commenter){
		if(empty($commenter['comment_author_email'])){
			$wpjam_user	= wpjam_get_current_user();

			if($wpjam_user && !empty($wpjam_user['user_email'])){
				$commenter['comment_author_email']	= $wpjam_user['user_email'];
				$commenter['comment_author']		= $wpjam_user['nickname'];
			}
		}

		return $commenter;
	}

	public static function filter_pre_avatar_data($args, $id_or_email){
		$user_id = 0;

		if(is_object($id_or_email) && isset($id_or_email->comment_ID)){
			$id_or_email	= get_comment($id_or_email);
		}

		if(is_numeric($id_or_email)){
			$user_id	= $id_or_email;
		}elseif($id_or_email instanceof WP_User){
			$user_id	= $id_or_email->ID;
		}elseif($id_or_email instanceof WP_Post){
			$user_id	= $id_or_email->post_author;
		}elseif($id_or_email instanceof WP_Comment){
			$avatarurl	= get_comment_meta($id_or_email->comment_ID, 'avatarurl', true);

			if($avatarurl){
				return array_merge($args, [
					'url'			=> wpjam_get_thumbnail($avatarurl, [$args['width'], $args['height']]),
					'found_avatar'	=> true,
				]);
			}

			$user_id	= $id_or_email->user_id;
		}

		if($user_id){
			$avatarurl	= get_user_meta($user_id, 'avatarurl', true);

			if($avatarurl){
				return array_merge($args, [
					'url'			=> wpjam_get_thumbnail($avatarurl, [$args['width'], $args['height']]),
					'found_avatar'	=> true,
				]);
			}

			$args['user_id']	= $user_id;
		}

		return $args;
	}
}

class WPJAM_Load{
	private $hooks;
	private $callback;

	public function add_action($hooks, $callback){
		$this->hooks	= $hooks;
		$this->callback	= $callback;

		foreach($hooks as $hook){
			add_action($hook, [$this, 'callback']);
		}
	}

	public function callback(){
		foreach($this->hooks as $hook){
			if(!did_action($hook)){
				return;
			}
		}

		call_user_func($this->callback);
	}
}

function wpjam_load($hooks, $callback){
	$todo 	= [];

	foreach((array)$hooks as $hook){
		if(!did_action($hook)){
			$todo[]	= $hook;
		}
	}

	if(empty($todo)){
		call_user_func($callback);
	}elseif(count($todo) == 1){
		add_action(current($todo), $callback);
	}else{
		$object	= new WPJAM_Load();
		$object->add_action($todo, $callback);
	}
}

function wpjam_register_route_module($name, $args){
	$model	= $args['model'] ?? '';

	if($model && class_exists($model)){
		if(empty($args['init']) && method_exists($model, 'init')){
			$args['init']	= [$model, 'init'];
		}

		if(empty($args['callback']) && method_exists($model, 'redirect')){
			$args['callback']	= [$model, 'redirect'];
		}
	}

	return wpjam_register('route_module', $name, $args);
}

function wpjam_is_module($module='', $action=''){
	$current_module	= wpjam_get_current_module();

	if($module){
		if($action && $action != wpjam_get_current_action()){
			return false;
		}

		return $module == $current_module;
	}else{
		return $current_module ? true : false;
	}
}

function wpjam_get_query_var($key, $wp=null){
	$wp	= $wp ?: $GLOBALS['wp'];

	return $wp->query_vars[$key] ?? null;
}

function wpjam_get_current_module($wp=null){
	return wpjam_get_query_var('module', $wp);
}

function wpjam_get_current_action($wp=null){
	return wpjam_get_query_var('action', $wp);
}

function wpjam_get_current_var($name, &$isset=false){
	$object	= WPJAM_Current::get_instance();

	if(isset($object->$name)){
		$isset	= true;

		return $object->$name;
	}else{
		return null;
	}
}

function wpjam_set_current_var($name, $value){
	$object	= WPJAM_Current::get_instance();

	$object->$name	= $value;
}

function wpjam_get_current_items($name){
	return (WPJAM_Current::get_instance())->get_items($name);
}

function wpjam_get_current_item($name, $key){
	return (WPJAM_Current::get_instance())->get_item($name, $key);
}

function wpjam_add_current_item($name, ...$args){
	return (WPJAM_Current::get_instance())->add_item($name, ...$args);
}

function wpjam_replace_current_item($name, $key, $item){
	return (WPJAM_Current::get_instance())->replace_item($name, $key, $item);
}

function wpjam_set_current_item($name, $key, $item){
	return (WPJAM_Current::get_instance())->set_item($name, $key, $item);
}

function wpjam_delete_current_item($name, $key){
	return (WPJAM_Current::get_instance())->delete_item($name, $key);
}

function wpjam_get_current_user($required=false){
	$user	= wpjam_get_current_var('user', $isset);

	if(!$isset){
		$user	= apply_filters('wpjam_current_user', null);

		wpjam_set_current_var('user', $user);
	}

	if($required){
		if(is_null($user)){
			return new WP_Error('bad_authentication', '无权限');
		}
	}else{
		if(is_wp_error($user)){
			return null;
		}
	}

	return $user;
}

function wpjam_get_current_commenter(){
	$commenter	= wp_get_current_commenter();

	if(empty($commenter['comment_author_email'])){
		return new WP_Error('bad_authentication', '无权限');
	}

	return $commenter;
}

function wpjam_json_encode($data){
	return wp_json_encode($data, JSON_UNESCAPED_UNICODE);
}

function wpjam_json_decode($json, $assoc=true){
	$json	= wpjam_strip_control_characters($json);

	if(!$json){
		return new WP_Error('empty_json', 'JSON 内容不能为空！');
	}

	$result	= json_decode($json, $assoc);

	if(is_null($result)){
		$result	= json_decode(stripslashes($json), $assoc);

		if(is_null($result)){
			if(wpjam_doing_debug()){
				print_r(json_last_error());
				print_r(json_last_error_msg());
			}
			trigger_error('json_decode_error '. json_last_error_msg()."\n".var_export($json,true));
			return new WP_Error('json_decode_error', json_last_error_msg());
		}
	}

	return $result;
}

function wpjam_send_json($response=[], $status_code=null){
	if(is_wp_error($response)){
		$errdata	= $response->get_error_data();
		$response	= [
			'errcode'	=> $response->get_error_code(), 
			'errmsg'	=> $response->get_error_message(),
		];

		if($errdata){
			$errdata	= is_array($errdata) ? $errdata : ['errdata'=>$errdata];
			$response 	= $response + $errdata;
		}
	}else{
		if(is_array($response)){
			if(!$response || !wp_is_numeric_array($response)){
				$response	= wp_parse_args($response, ['errcode'=>0]);
			}
		}elseif($response === true){
			$response	= ['errcode'=>0];
		}elseif($response === false || is_null($response)){
			$response	= ['errcode'=>'-1', 'errmsg'=>'系统数据错误或者回调函数返回错误'];
		}
	}

	if(!empty($response['errcode'])){
		$response	= WPJAM_Error::filter($response);
	}

	$result	= wpjam_json_encode($response);

	if(!headers_sent() && !wpjam_doing_debug()){
		if(!is_null($status_code)){
			status_header($status_code);
		}

		if(wp_is_jsonp_request()){
			$result	= '/**/' . $_GET['_jsonp'] . '(' . $result . ')';

			$content_type	= 'application/jsjavascripton';
		}else{
			$content_type	= 'application/json';
		}

		@header('Content-Type: '.$content_type.'; charset='.get_option('blog_charset'));
	}

	echo $result;

	exit;
}

function wpjam_register_json($name, $args=[]){
	return WPJAM_JSON::register($name, $args);
}

function wpjam_register_api($name, $args=[]){
	return wpjam_register_json($name, $args);
}

function wpjam_get_json_object($name){
	return WPJAM_JSON::get($name);
}

function wpjam_parse_json_module($module){
	$module	= wp_parse_args($module, ['type'=>'', 'args'=>[]]);				
	$object	= new WPJAM_JSON_Module($module['type'], $module['args']);

	return $object->parse();
}

function wpjam_get_current_json($return='name'){
	$json	= wpjam_get_current_var('json');

	if($return == 'object'){
		return $json ? wpjam_get_json_object($json) : null;
	}else{
		return $json;
	}
}

function wpjam_is_json_request(){
	if(get_option('permalink_structure')){
		if(preg_match("/\/api\/(.*)\.json/", $_SERVER['REQUEST_URI'])){ 
			return true;
		}
	}else{
		if(isset($_GET['module']) && $_GET['module'] == 'json'){
			return true;
		}
	}

	return false;
}


function wpjam_get_parameter($name, $args=[]){
	$object	= new WPJAM_Parameter($name, $args);

	return $object->get_value();
}

function wpjam_get_data_parameter($name='', $args=[]){
	if($name){
		$object	= new WPJAM_Parameter($name, $args);

		return $object->get_value('data');
	}else{
		return WPJAM_Parameter::get_data();
	}
}

function wpjam_get_defaults_parameter(){
	return WPJAM_Parameter::get_defaults();
}


function wpjam_method_allow($method, $send=true){
	if($_SERVER['REQUEST_METHOD'] != strtoupper($method)){
		$wp_error = new WP_Error('method_not_allow', '接口不支持 '.$_SERVER['REQUEST_METHOD'].' 方法，请使用 '.$method.' 方法！');

		return $send ? wpjam_send_json($wp_error): $wp_error;
	}

	return true;
}

function wpjam_http_request($url, $args=[], $err_args=[]){
	$object	= new WPJAM_Request($url, $args);

	return $object->request($err_args);
}

function wpjam_remote_request($url, $args=[], $err_args=[]){
	return wpjam_http_request($url, $args, $err_args);
}


function wpjam_register_verify_txt($name, $args){
	return WPJAM_Verify_TXT::register($name, $args);
}

function wpjam_register_gravatar_services($name, $args){
	return WPJAM_Gravatar::register($name, $args);
}

function wpjam_register_google_font_services($name, $args){
	return WPJAM_Google_Font::register($name, $args);
}

add_action('wpjam_loaded',	function(){
	wpjam_register_verification_code_group('default');

	wpjam_register_route_module('json',	['model'=>'WPJAM_JSON']);
	wpjam_register_route_module('txt',	['model'=>'WPJAM_Verify_TXT']);

	wpjam_register_platform('weapp',	['bit'=>1,	'order'=>4,		'title'=>'小程序',	'verify'=>'is_weapp']);
	wpjam_register_platform('weixin',	['bit'=>2,	'order'=>4,		'title'=>'微信网页',	'verify'=>'is_weixin']);
	wpjam_register_platform('mobile',	['bit'=>4,	'order'=>8,		'title'=>'移动网页',	'verify'=>'wp_is_mobile']);
	wpjam_register_platform('web',		['bit'=>8,	'order'=>10,	'title'=>'网页',		'verify'=>'__return_true']);
	wpjam_register_platform('template',	['bit'=>8,	'order'=>10,	'title'=>'网页',		'verify'=>'__return_true']);

	wpjam_register_path('home',		['path_type'=>'template',	'title'=>'首页',			'path'=>home_url()]);
	wpjam_register_path('category',	['path_type'=>'template',	'title'=>'分类页',		'path'=>'',	'page_type'=>'taxonomy',	'taxonomy'=>'category']);
	wpjam_register_path('post_tag',	['path_type'=>'template',	'title'=>'标签页',		'path'=>'',	'page_type'=>'taxonomy',	'taxonomy'=>'post_tag']);
	wpjam_register_path('author',	['path_type'=>'template',	'title'=>'作者页',		'path'=>'',	'page_type'=>'author']);
	wpjam_register_path('post',		['path_type'=>'template',	'title'=>'文章详情页',	'path'=>'',	'page_type'=>'post_type',	'post_type'=>'post']);
	wpjam_register_path('external', ['path_type'=>'template',	'title'=>'外部链接',		'path'=>'',	'fields'=>[
			'url'	=> ['title'=>'',	'type'=>'url',	'required'=>true,	'placeholder'=>'请输入外部链接地址，仅适用网页版。']
		]]);

	wpjam_register_data_type('post_type',	['model'=>'WPJAM_Post_Type_Data_Type',	'meta_type'=>'post']);
	wpjam_register_data_type('taxonomy',	['model'=>'WPJAM_Taxonomy_Data_Type',	'meta_type'=>'term']);
	wpjam_register_data_type('author',		['model'=>'WPJAM_Author_Data_Type',		'meta_type'=>'user']);
	wpjam_register_data_type('model',		['model'=>'WPJAM_Model_Data_Type']);
	wpjam_register_data_type('video',		['model'=>'WPJAM_Video_Data_Type']);

	wpjam_register_meta_type('post');
	wpjam_register_meta_type('term');
	wpjam_register_meta_type('user');
	wpjam_register_meta_type('comment');

	if(is_multisite()){
		wpjam_register_meta_type('blog');
		wpjam_register_meta_type('site');
	}

	wpjam_register_lazyloader('user',		['filter'=>'wpjam_get_userdata',	'callback'=>'cache_users']);
	wpjam_register_lazyloader('post_term',	['filter'=>'loop_start',			'callback'=>'update_object_term_cache',	'accepted_args'=>2]);

	wpjam_register_gravatar_services('cravatar',	['title'=>'Cravatar',	'url'=>'https://cravatar.cn/avatar/']);
	wpjam_register_gravatar_services('geekzu',		['title'=>'极客族',		'url'=>'https://sdn.geekzu.org/avatar/']);
	wpjam_register_gravatar_services('loli',		['title'=>'loli',		'url'=>'https://gravatar.loli.net/avatar/']);
	wpjam_register_gravatar_services('sep_cc',		['title'=>'sep.cc',		'url'=>'https://cdn.sep.cc/avatar/']);

	wpjam_register_google_font_services('geekzu',	['title'=>'极客族',		'replace'=>[
		'//fonts.geekzu.org',
		'//gapis.geekzu.org/ajax',
		'//gapis.geekzu.org/g-themes',
		'//gapis.geekzu.org/g-fonts'
	]]);

	wpjam_register_google_font_services('loli',		['title'=>'loli',		'replace'=>[
		'//fonts.loli.net',
		'//ajax.loli.net',
		'//themes.loli.net',
		'//gstatic.loli.net'
	]]);

	wpjam_register_google_font_services('ustc',		['title'=>'中科大',		'replace'=>[
		'//fonts.lug.ustc.edu.cn',
		'//ajax.lug.ustc.edu.cn',
		'//google-themes.lug.ustc.edu.cn',
		'//fonts-gstatic.lug.ustc.edu.cn'
	]]);
});

add_action('init',	['WPJAM_Route', 'init']);
add_action('init',	['WPJAM_Post_Type', 'init']);
add_action('init',	['WPJAM_Taxonomy', 'init']);
add_filter('init',	['WPJAM_Cron', 'init']);

add_filter('register_post_type_args',	['WPJAM_Post_Type', 'filter_register_args'], 999, 2);
add_filter('register_taxonomy_args',	['WPJAM_Taxonomy', 'filter_register_args'], 999, 3);

if(version_compare($GLOBALS['wp_version'], '6.0', '<')){
	add_action('registered_post_type',	['WPJAM_Post_Type', 'on_registered'], 1, 2);
	add_action('registered_taxonomy',	['WPJAM_Taxonomy', 'on_registered'], 1, 3);
}

if(wpjam_is_json_request()){
	ini_set('display_errors', 0);

	remove_filter('the_title', 'convert_chars');

	remove_action('init', 'wp_widgets_init', 1);
	remove_action('init', 'maybe_add_existing_user_to_blog');
	remove_action('init', 'check_theme_switched', 99);

	remove_action('plugins_loaded', '_wp_customize_include');

	remove_action('wp_loaded', '_custom_header_background_just_in_time');
}
