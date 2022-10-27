<?php
class WPJAM_JSON extends WPJAM_Register{
	private $response;

	public function validate(){
		if(!isset($_GET['access_token']) && is_super_admin()){
			return;
		}
		
		$grant	= WPJAM_Grant::get_instance();
		$appid	= wpjam_get_parameter('appid');

		if($this->grant){
			$token	= wpjam_get_parameter('access_token', ['required'=>true]);
			$item 	= $grant->validate_token($token);

			if(is_wp_error($item)){
				wpjam_send_json($item);
			}

			$appid	= $item['appid'];
		}

		$times	= $grant->cache_get_item($appid, $this->name);

		if($this->quota && $times > $this->quota){
			wpjam_send_json(['errcode'=>'api_exceed_quota', 'errmsg'=>'API 调用次数超限']);
		}

		$grant->cache_update_item($appid, $this->name, $times+1);
	}

	public function response(){
		$current_user	= wpjam_get_current_user($this->auth);

		if(is_wp_error($current_user)){
			wpjam_send_json($current_user);
		}

		if($current_user && !empty($current_user['user_id'])){
			wp_set_current_user($current_user['user_id']);
		}

		if($this->capability){
			if(!current_user_can($this->capability)){
				wpjam_send_json(['errcode'=>'bad_capability', 'errmsg'=>'无效的权限']);
			}
		}

		$this->response	= [
			'errcode'		=> 0,
			'current_user'	=> $current_user
		];

		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			foreach(['page_title', 'share_title', 'share_image'] as $key){
				$this->response[$key]	= (string)$this->$key;
			}
		}

		if($this->modules){
			$modules	= $this->modules;
			$modules	= wp_is_numeric_array($modules) ? $modules : [$modules];

			foreach($modules as $module){
				$result	= wpjam_parse_json_module($module);

				$this->merge_result($result);
			}
		}else{
			$result	= null;

			if($this->callback){
				if(is_callable($this->callback)){
					$result	= call_user_func($this->callback, $this->args, $this->name);
				}
			}elseif($this->template){
				if(is_file($this->template)){
					$result	= include $this->template;
				}
			}else{
				$result	= $this->args;
			}

			$this->merge_result($result);
		}

		$response	= apply_filters('wpjam_json', $this->response, $this->args, $this->name);

		if($_SERVER['REQUEST_METHOD'] != 'POST'){
			if(empty($response['page_title'])){
				$response['page_title']		= html_entity_decode(wp_get_document_title());
			}

			if(empty($response['share_title'])){
				$response['share_title']	= html_entity_decode(wp_get_document_title());
			}

			if(!empty($response['share_image'])){
				$response['share_image']	= wpjam_get_thumbnail($response['share_image'], '500x400');
			}
		}

		wpjam_send_json($response);
	}

	private function merge_result($result){
		if(is_wp_error($result)){
			wpjam_send_json($result);
		}elseif(is_array($result)){
			$except	= [];

			foreach(['page_title', 'share_title', 'share_image'] as $key){
				if(!empty($this->response[$key]) && isset($result[$key])){
					$except[]	= $key;
				}
			}

			if($except){
				$result	= wpjam_array_except($result, $except);
			}

			$this->response	= array_merge($this->response, $result);
		}
	}

	public static function init(){
		add_rewrite_rule($GLOBALS['wp_rewrite']->root.'api/([^/]+)/(.*?)\.json?$',	'index.php?module=json&action=mag.$matches[1].$matches[2]', 'top');
		add_rewrite_rule($GLOBALS['wp_rewrite']->root.'api/([^/]+)\.json?$',		'index.php?module=json&action=$matches[1]', 'top');

		add_action('wpjam_api',	[self::class, 'register_default']);
	}

	public static function redirect($action){
		if(!wpjam_doing_debug()){
			self::send_headers();
		}

		if(!str_starts_with($action, 'mag.')){
			return;
		}

		$name	= wpjam_remove_prefix($action, 'mag.');
		$name	= wpjam_remove_prefix($name, 'mag.');	// 兼容
		$name	= str_replace('/', '.', $name);
		$name	= apply_filters('wpjam_json_name', $name);

		wpjam_set_current_var('json', $name);

		do_action('wpjam_api', $name);

		$object	= self::get($name);

		if(!$object){
			wpjam_send_json(['errcode'=>'api_not_defined',	'errmsg'=>'接口未定义！']);
		}

		$object->validate();
		$object->response();
	}

	public static function send_headers(){
		header('X-Content-Type-Options: nosniff');

		if($origin	= get_http_origin()){
			// Requests from file:// and data: URLs send "Origin: null"
			if('null' !== $origin){
				$origin	= esc_url_raw($origin);
			}

			@header('Access-Control-Allow-Origin: ' . $origin);
			@header('Access-Control-Allow-Methods: GET, POST');
			@header('Access-Control-Allow-Credentials: true');
			@header('Access-Control-Allow-Headers: Authorization, Content-Type');
			@header('Vary: Origin');

			if('OPTIONS' === $_SERVER['REQUEST_METHOD']){
				exit;
			}
		}

		if('OPTIONS' === $_SERVER['REQUEST_METHOD']){
			status_header(403);
			exit;
		}

		$type	= wp_is_jsonp_request() ? 'javascript' : 'json';

		@header('Content-Type: application/'.$type.'; charset='.get_option('blog_charset'));
	}

	public static function register_default($json){
		if(self::get($json)){
			return;
		}

		if($json == 'post.list'){
			$modules	= [];
			$post_type	= wpjam_get_parameter('post_type');

			$modules[]	= [
				'type'	=> 'post_type',
				'args'	=> ['post_type'=>$post_type, 'action'=>'list', 'posts_per_page'=>10, 'output'=>'posts']
			];

			if($post_type && is_string($post_type)){
				foreach(get_object_taxonomies($post_type, 'objects') as $taxonomy => $tax_obj){
					if($tax_obj->hierarchical && $tax_obj->public){
						$modules[]	= ['type'=>'taxonomy',	'args'=>['taxonomy'=>$taxonomy, 'hide_empty'=>0]];
					}
				}
			}

			self::register($json,	['modules'=>$modules]);
		}elseif($json == 'post.get'){
			self::register($json,	['modules'=>['type'=>'post_type',	'args'=>['action'=>'get', 'output'=>'post']]]);
		}elseif($json == 'media.upload'){
			self::register($json,	['modules'=>['type'=>'media',	'args'=>['media'=>'media']]]);
		}elseif($json == 'token.grant'){
			self::register($json,	['modules'=>['type'=>'token'],	'quota'=>1000]);
		}elseif($json == 'token.validate'){
			self::register($json,	['quota'=>10,	'grant'=>true]);
		}
	}

	public static function __callStatic($method, $args){
		if(in_array($method, ['parse_post_list_module', 'parse_post_get_module'])){
			$args	= $args[0] ?? [];
			$action	= str_replace(['parse_post_', '_module'], '', $method);

			return wpjam_parse_json_module(['type'=>'post_type', 'args'=>array_merge($args, ['action'=>$action])]);
		}
	}
}

class WPJAM_JSON_Module{
	protected $type;
	protected $args;

	public function __construct($type, $args=[]){
		if(!is_array($args)){
			$args	= wpjam_parse_shortcode_attr(stripslashes_deep($args), 'module');
		}

		$this->type	= $type;
		$this->args	= $args;
	}

	public function __get($name){
		return $this->args[$name] ?? null;
	}

	public function __isset($name){
		return isset($this->args[$name]);
	}

	public function parse(){
		$method	= 'parse_'.$this->type;

		if(method_exists($this, $method)){
			return call_user_func([$this, $method]);
		}else{
			return $this->args;
		}
	}

	public function parse_post_type(){
		$action	= wpjam_array_pull($this->args, 'action');

		if(!$action){
			wpjam_send_json(['errcode'=>'empty_action',	'errmsg'=>'没有设置 action']);
		}

		$wp	= $GLOBALS['wp'];

		if(isset($wp->raw_query_vars)){
			$wp->query_vars		= $wp->raw_query_vars;
		}else{
			$wp->raw_query_vars	= $wp->query_vars;
		}

		if($action == 'list'){
			return $this->parse_post_list();
		}elseif($action == 'get'){
			return $this->parse_post_get();
		}elseif($action == 'upload'){
			return $this->parse_media();
		}
	}

	/* 规则：
	** 1. 分成主的查询和子查询（$query_args['sub']=1）
	** 2. 主查询支持 $_GET 参数 和 $_GET 参数 mapping
	** 3. 子查询（sub）只支持 $query_args 参数
	** 4. 主查询返回 next_cursor 和 total_pages，current_page，子查询（sub）没有
	** 5. $_GET 参数只适用于 post.list
	** 6. term.list 只能用 $_GET 参数 mapping 来传递参数
	*/
	public function parse_post_list(){
		$output	= wpjam_array_pull($this->args, 'output');
		$sub	= wpjam_array_pull($this->args, 'sub');

		$is_main_query	= !$sub;	// 子查询不支持 $_GET 参数，置空之前要把原始的查询参数存起来

		if($is_main_query){
			$wp	= $GLOBALS['wp'];

			foreach($this->args as $query_key => $query_valeu){
				$wp->set_query_var($query_key, $query_valeu);
			}

			if($posts_per_page = (int)wpjam_get_parameter('posts_per_page')){
				$wp->set_query_var('posts_per_page', ($posts_per_page > 20 ? 20 : $posts_per_page));
			}

			if($offset = (int)wpjam_get_parameter('offset')){
				$wp->set_query_var('offset', $offset);
			}

			$orderby	= $wp->query_vars['orderby'] ?? 'date';
			$paged		= $wp->query_vars['paged'] ?? null;
			$use_cursor	= (empty($paged) && is_null(wpjam_get_parameter('s')) && !is_array($orderby) && in_array($orderby, ['date', 'post_date']));

			if($use_cursor){
				if($cursor = (int)wpjam_get_parameter('cursor')){
					$wp->set_query_var('cursor', $cursor);
					$wp->set_query_var('ignore_sticky_posts', true);
				}

				if($since = (int)wpjam_get_parameter('since')){
					$wp->set_query_var('since', $since);
					$wp->set_query_var('ignore_sticky_posts', true);
				}
			}

			$post_type	= $wp->query_vars['post_type'] ?? '';
			$taxonomies	= $post_type ? get_object_taxonomies($post_type) : get_taxonomies(['public'=>true]);
			$taxonomies	= array_diff($taxonomies, ['post_format']);

			foreach($taxonomies as $taxonomy){	// taxonomy 参数处理，同时支持 $_GET 和 $query_vars 参数
				$query_key	= wpjam_get_taxonomy_query_key($taxonomy);

				if($taxonomy == 'category'){
					if(empty($wp->query_vars['cat'])){
						foreach(['category_id', 'cat_id'] as $cat_key){
							$term_id	= (int)wpjam_get_parameter($cat_key);

							if($term_id){
								$wp->set_query_var($query_key, $term_id);
								break;
							}
						}
					}
				}else{
					$term_id	= (int)wpjam_get_parameter($query_key);

					if($term_id){
						$wp->set_query_var($query_key, $term_id);
					}
				}
			}

			$term_id	= (int)wpjam_get_parameter('term_id');
			$taxonomy	= wpjam_get_parameter('taxonomy');

			if($term_id && $taxonomy){
				$wp->set_query_var('term_id', $term_id);
				$wp->set_query_var('taxonomy', $taxonomy);
			}

			$wp->query_vars	= wpjam_parse_query_vars($wp->query_vars);

			$wp->query_posts();

			$wp_query	= $GLOBALS['wp_query'];
		}else{
			$post_type	= $this->post_type;
			$query_vars	= wpjam_parse_query_vars($this->args);
			$wp_query	= new WP_Query($query_vars);
		}

		if(empty($output)){
			if($post_type && is_string($post_type)){
				$output	= wpjam_get_post_type_setting($post_type, 'plural') ?: $post_type.'s';
			}else{
				$output	= 'posts';
			}
		}

		$_posts = [];

		while($wp_query->have_posts()){
			$wp_query->the_post();

			$_posts[]	= wpjam_get_post(get_the_ID(), $this->args);
		}

		$posts_json = [];

		if($is_main_query){
			if(is_category() || is_tag() || is_tax()){
				if($current_term = get_queried_object()){
					$taxonomy		= $current_term->taxonomy;
					$current_term	= wpjam_get_term($current_term, $taxonomy);

					$posts_json['current_taxonomy']		= $taxonomy;
					$posts_json['current_'.$taxonomy]	= $current_term;
				}else{
					$posts_json['current_taxonomy']		= null;
				}
			}elseif(is_author()){
				if($author = $wp_query->get('author')){
					$posts_json['current_author']	= wpjam_get_user($author);
				}else{
					$posts_json['current_author']	= null;
				}
			}

			$posts_json['total']		= (int)$wp_query->found_posts;
			$posts_json['total_pages']	= (int)$wp_query->max_num_pages;
			$posts_json['current_page']	= (int)($wp_query->get('paged') ?: 1);

			if($use_cursor){
				$posts_json['next_cursor']	= ($_posts && $wp_query->max_num_pages > 1) ? end($_posts)['timestamp'] : 0;
			}
		}

		$posts_json[$output]	= $_posts;

		return apply_filters('wpjam_posts_json', $posts_json, $wp_query, $output);
	}

	public function parse_post_get(){
		global $wp, $wp_query;

		if($this->id){
			$post_id	= $this->id;
		}else{
			$post_id	= (int)wpjam_get_parameter('id');
		}

		if($this->post_type){
			$post_type	= $this->post_type;
		}else{
			$post_type	= wpjam_get_parameter('post_type');
			$post_type	= $post_type ?: 'any';
		}

		if($post_type == 'any'){
			if($post_id){
				$post_type	= get_post_type($post_id);

				if(!$post_type){
					wpjam_send_json(['errcode'=>'invalid_post_id',	'errmsg'=>'无效的 ID']);
				}
			}else{
				wpjam_send_json(['errcode'=>'empty_post_id',	'errmsg'=>'文章ID不能为空']);
			}
		}else{
			if(!post_type_exists($post_type)){
				wpjam_send_json(['errcode'=>'post_type_not_exists',	'errmsg'=>'post_type 未定义']);
			}
		}

		if(empty($post_id)){
			$orderby	= wpjam_get_parameter('orderby');

			if($orderby == 'rand'){
				$wp->set_query_var('orderby', 'rand');
			}else{
				$name_key	= is_post_type_hierarchical($post_type) ? 'pagename' : 'name';

				$wp->set_query_var($name_key,	wpjam_get_parameter($name_key,	['required'=>true]));
			}
		}else{
			$wp->set_query_var('p', $post_id);
		}

		$wp->set_query_var('post_type', $post_type);
		$wp->set_query_var('posts_per_page', 1);
		$wp->set_query_var('cache_results', true);

		$wp->query_posts();

		if(!$wp_query->have_posts() && get_query_var('name')){
			$post_id	= apply_filters('old_slug_redirect_post_id', null);
		
			if($post_id){
				$wp->set_query_var('post_type', 'any');
				$wp->set_query_var('p', $post_id);
				$wp->set_query_var('name', '');
				$wp->set_query_var('pagename', '');

				$wp->query_posts();
			}
		}

		if(!$wp_query->have_posts()){
			wpjam_send_json(['errcode'=>'empty_query',	'errmsg'=>'查询结果为空']);
		}

		$wp_query->the_post();

		$_post	= wpjam_get_post(get_the_ID(), $this->args);

		$post_json	= [];

		foreach(['share_title', 'share_image'] as $key){
			$value	= wpjam_array_pull($_post, $key);

			if($value){
				$post_json[$key]	= $value;
			}
		}

		$output	= $this->output ?: $_post['post_type'];

		$post_json[$output]	= $_post;

		return $post_json;
	}

	public function parse_media(){
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$media_id	= $this->media ?: 'media';
		$output		= $this->output ?: 'url';

		if (!isset($_FILES[$media_id])) {
			wpjam_send_json(['errcode'=>'empty_media',	'errmsg'=>'媒体流不能为空！']);
		}

		if($this->type == 'post_type'){
			$post_id		= (int)wpjam_get_parameter('post_id',	['method'=>'POST', 'default'=>0]);
			$attachment_id	= media_handle_upload($media_id, $post_id);

			if(is_wp_error($attachment_id)){
				wpjam_send_json($attachment_id);
			}

			$url	= wp_get_attachment_url($attachment_id);
		}else{
			$upload_file	= wp_handle_upload($_FILES[$media_id], ['test_form'=>false]);

			if(isset($upload_file['error'])){
				wpjam_send_json(['errcode'=>'upload_error',	'errmsg'=>$upload_file['error']]);
			}

			$url	= $upload_file['url'];
		}

		return [$output => $url];
	}

	public function parse_taxonomy(){
		$taxonomy	= $this->taxonomy;
		$tax_obj	= $taxonomy ? get_taxonomy($taxonomy) : null;

		if(empty($tax_obj)){
			wpjam_send_json(['errcode'=>'invalid_taxonomy',	'errmsg'=>'无效的自定义分类']);
		}

		$args	= $this->args;

		if($mapping = wpjam_array_pull($args, 'mapping')){
			$mapping	= wp_parse_args($mapping);

			if($mapping && is_array($mapping)){
				foreach($mapping as $key => $get){
					if($value = wpjam_get_parameter($get)){
						$args[$key]	= $value;
					}
				}
			}
		}

		$number		= (int)wpjam_array_pull($args, 'number');
		$output		= wpjam_array_pull($args, 'output');
		$output		= $output ?: wpjam_get_taxonomy_setting($taxonomy, 'plural');
		$max_depth	= wpjam_array_pull($args, 'max_depth');

		$terms_json	= [];

		if($terms = wpjam_get_terms($args, $max_depth)){
			if($number){
				$paged	= $args['paged'] ?? 1;
				$offset	= $number * ($paged-1);

				$terms_json['current_page']	= (int)$paged;
				$terms_json['total_pages']	= ceil(count($terms)/$number);
				$terms = array_slice($terms, $offset, $number);
			}

			$terms_json[$output]	= array_values($terms);
		}else{
			$terms_json[$output]	= [];
		}

		return $terms_json;
	}

	public function parse_setting(){
		if(!$this->option_name){
			return null;
		}

		$option_name	= $this->option_name;
		$setting_name	= $this->setting_name ?? ($this->setting ?? '');

		if($this->output){
			$output	= $this->output;
		}else{
			$output	= $setting_name ? $setting_name : $option_name;
		}

		if($object = WPJAM_Option_Setting::get($option_name)){
			$value	= $object->prepare();

			if($object->option_type == 'single'){
				$value	= $value[$option_name] ?? null;

				return [$output=>$value];
			}
		}else{
			$value	= wpjam_get_option($option_name);
		}

		if($setting_name){
			$value	= $value[$setting_name] ?? null;
		}

		return [$output => $value];
	}

	public function parse_token(){
		$appid	= wpjam_get_parameter('appid',	['required'=>true]);
		$secret	= wpjam_get_parameter('secret', ['required'=>true]);
		$token	= WPJAM_Grant::get_instance()->reset_token($appid, $secret);

		return is_wp_error($token) ? $token : ['access_token'=>$token, 'expires_in'=>7200];
	}
}

class WPJAM_Grant extends WPJAM_Option_Items{
	protected function __construct(){
		$items	= get_option('wpjam_grant');

		if(is_array($items)){
			if(isset($items['appid'])){
				$items	= [$items];
			}

			if(wp_is_numeric_array($items)){
				$_items	= [];

				foreach($items as $item){
					if($item && is_array($item) && isset($item['appid'])){
						$appid	= $item['appid'];

						$_items[$appid]	= $item;
					}
				}

				update_option('wpjam_grant', $_items);
			}
		}

		parent::__construct('wpjam_grant', ['total'=>3, 'primary_key'=>'appid', 'primary_title'=>'AppID']);
	}

	public function validate_token($token){
		foreach($this->get_items() as $item){
			if(isset($item['token']) && $item['token'] == $token && (time()-$item['time'] < 7200)){
				return $item;
			}
		}

		return new WP_Error('invalid_access_token', '无效的 Access Token');
	}

	public function create(){
		$items	= $this->get_items();

		do{
			$appid	= 'jam'.strtolower(wp_generate_password(15, false, false));
		}while(isset($items[$appid]));

		return $this->insert(['appid'=>$appid], true);
	}

	public function reset_secret($appid){
		$secret	= strtolower(wp_generate_password(32, false, false));
		$result	= $this->update($appid, [
			'secret'	=> md5($secret),
			'token'		=> '',
			'time'		=> ''
		]);

		if(is_wp_error($result)){
			return $result;
		}

		return $secret;
	}

	public function reset_token($appid, $secret){
		$item	= $this->get($appid);

		if(!$item || empty($item['secret']) || $item['secret'] != md5($secret)){
			return new WP_Error('invalid_secret', '无效的密钥');
		}

		$token	= wp_generate_password(64, false, false);
		$result	= $this->update($appid, [
			'token'	=> $token,
			'time'	=> time()
		]);

		if(is_wp_error($result)){
			return $result;
		}

		return $token;
	}

	public function __call($method, $args){
		if(str_starts_with($method, 'cache_')){
			$today	= date('Y-m-d', current_time('timestamp'));
			$object	= WPJAM_Cache_Group::get_instance('wpjam_api_times', ['prefix'=>$today]);
			
			return call_user_func_array([$object, $method], $args);
		}
	}
}

class WPJAM_Error extends WPJAM_Option_Items{
	protected function __construct(){
		parent::__construct('wpjam_errors', ['primary_key'=>'errcode', 'primary_title'=>'errcode']);
	}

	public static function filter($response){
		$object	= self::get_instance();
		$data	= $object->get($response['errcode']);

		if($data){
			$response['errmsg']	= $data['errmsg'];

			if($data['show_modal']){
				if(!empty($data['modal']['title']) && !empty($data['modal']['content'])){
					$response['modal']	= $data['modal'];
				}
			}
		}

		return $response;
	}
}

class WPJAM_Parameter{
	private $name;
	private $args;

	public function __construct($name, $args=[]){
		$this->name	= $name;
		$this->args	= $args;
	}

	public function __get($name){
		return $this->args[$name] ?? null;
	}

	public function __isset($name){
		return isset($this->args[$name]);
	}

	public function get_value($type=''){
		$value	= null;
		$name	= $this->name;

		if($type == 'data'){
			if(isset($_GET[$name])){
				$value	= wp_unslash($_GET[$name]);
			}else{
				$data	= $this->get_data();
				$value	= $data[$name] ?? null;
			}
		}else{
			$method	= $this->method ? strtoupper($this->method) : 'GET';

			if($method == 'GET'){
				if(isset($_GET[$name])){
					$value	= wp_unslash($_GET[$name]);
				}
			}elseif($method == 'POST'){
				if(empty($_POST)){
					$input	= $this->get_input();
					$value	= $input[$name] ?? null;
				}elseif(isset($_POST[$name])){
					$value	= wp_unslash($_POST[$name]);
				}
			}else{
				if(isset($_REQUEST[$name])){
					$value	= wp_unslash($_REQUEST[$name]);
				}elseif(empty($_POST)){
					$input	= $this->get_input();
					$value	= $input[$name] ?? null;
				}
			}
		}

		if(is_null($value) && isset($this->default)){
			return $this->default;
		}

		$value	= $this->validate_value($value);

		if(is_wp_error($value)){
			if($this->send === false){
				return $value;
			}else{
				wpjam_send_json($value);
			}
		}

		return $this->sanitize_value($value);
	}

	public function validate_value($value){
		$name	= $this->name;
	
		if($this->validate_callback){
			if(is_callable($this->validate_callback)){
				$result	= call_user_func($this->validate_callback, $value);

				if($result === false){
					return new WP_Error('invalid_parameter', '无效参数：'.$name);
				}elseif(is_wp_error($result)){
					return $result;
				}
			}
		}else{
			if($this->required){
				if(is_null($value)){
					return new WP_Error('missing_parameter', '缺少参数：'.$name);
				}
			}

			if($this->length){
				if(is_numeric($this->length) && mb_strlen($value) < $this->length){
					return new WP_Error('short_parameter', $name.' 参数长度不能少于 '.$this->length);
				}
			}
		}

		return $value;
	}

	public function sanitize_value($value){
		if($this->sanitize_callback){
			if(is_callable($this->sanitize_callback)){
				$value	= call_user_func($this->sanitize_callback, $value);
			}
		}else{
			if($this->type == 'int' && !is_null($value)){
				$value	= (int)$value;
			}
		}

		return $value;
	}

	public static function get_input($name=''){
		$input	= wpjam_get_current_var('php_input');

		if(is_null($input)){
			$input	= file_get_contents('php://input');

			if(is_string($input)){
				$input	= @wpjam_json_decode($input);
			}

			$input	= is_array($input) ? $input : [];

			wpjam_set_current_var('php_input', $input);
		}

		return $input;
	}

	public static function get_data(){
		$data	= wpjam_get_current_var('data_parameter');

		if(is_null($data)){
			$data	= wpjam_get_parameter('data', ['method'=>'POST', 'sanitize_callback'=>'wp_parse_args', 'default'=>[]]);
			$data	= wpjam_array_merge(self::get_defaults(), $data);

			wpjam_set_current_var('data_parameter', $data);
		}

		return $data;
	}

	public static function get_defaults(){
		return wpjam_get_parameter('defaults', ['method'=>'POST', 'sanitize_callback'=>'wp_parse_args', 'default'=>[]]);
	}
}

class WPJAM_Request{
	private $url;
	private $args;

	public function __construct($url, $args=[]){
		if(wpjam_doing_debug()){
			print_r($url);
			print_r($args);
		}

		$this->url	= $url;
		$this->args = wp_parse_args($args, [
			'body'			=> [],
			'headers'		=> [],
			'sslverify'		=> false,
			'blocking'		=> true,	// 如果不需要立刻知道结果，可以设置为 false
			'stream'		=> false,	// 如果是保存远程的文件，这里需要设置为 true
			'filename'		=> null,	// 设置保存下来文件的路径和名字
			// 'headers'	=> ['Accept-Encoding'=>'gzip;'],	//使用压缩传输数据
			// 'compress'	=> false,
		]);

		if($this->method){
			$this->method	= strtoupper($this->method);
		}else{
			$this->method	= $this->body ? 'POST' : 'GET';
		}
	}

	public function __get($name){
		return $this->args[$name] ?? null;
	}

	public function __isset($name){
		return isset($this->args[$name]);
	}

	public function __set($name, $value){
		$this->args[$name]	= $value;
	}

	public function request($err_args=[]){
		if($this->method == 'GET'){
			$response	= wp_remote_get($this->url, $this->args);		
		}elseif($this->method == 'FILE'){	// 上传文件
			$response	= (new WP_Http_Curl())->request($this->url, wp_parse_args($this->args, [
				'method'			=> $this->body ? 'POST' : 'GET',
				'sslcertificates'	=> ABSPATH.WPINC.'/certificates/ca-bundle.crt',
				'user-agent'		=> 'WordPress',
				'decompress'		=> true,
			]));
		}else{
			$json_encode_required	= wpjam_array_pull($this->args, ['json_encode_required', 'need_json_encode'], false);

			if($json_encode_required){
				if(is_array($this->body)){
					$this->body	= wpjam_json_encode($this->body);
				}

				if($this->method == 'POST' && empty($this->headers['Content-Type'])){
					$this->headers	+= ['Content-Type'=>'application/json'];
				}
			}

			$response	= wp_remote_request($this->url, $this->args);
		}

		if(wpjam_doing_debug()){
			print_r($response);
		}

		if(is_wp_error($response)){
			trigger_error($this->url."\n".$response->get_error_code().' : '.$response->get_error_message()."\n".var_export($this->body,true));
			return $response;
		}

		if(!empty($response['response']['code']) && $response['response']['code'] != 200){
			return new WP_Error($response['response']['code'], '远程服务器错误：'.$response['response']['code'].' - '.$response['response']['message']);
		}

		if(!$this->blocking){
			return true;
		}

		$response	= $this->decode($response['body'], $response['headers']);
		$wp_error	= $this->is_error($response, $err_args);

		return $wp_error ?: $response;
	}

	public function decode($response, $headers){
		if(empty($headers['content-disposition']) || strpos($headers['content-disposition'], 'attachment;') === false){
			$json_decode_required	= wpjam_array_pull($this->args, ['json_decode_required', 'need_json_decode'], ($this->stream ? false : true));

			if(!$json_decode_required){
				$content_type	= $headers['content-type'] ?? '';
				$content_type	= is_array($content_type) ? implode(' ', $content_type) : $content_type;

				$json_decode_required	= $content_type && strpos($content_type, '/json');
			}

			if($json_decode_required){
				if($this->stream){
					$response	= file_get_contents($this->filename);
				}

				if(empty($response)){
					trigger_error(var_export($response, true).var_export($headers, true));
				}else{
					$response	= wpjam_json_decode($response);
				}
			}
		}

		return $response;
	}

	public function is_error($response, $err_args=[]){
		if(is_wp_error($response)){
			return $response;
		}

		$err_args	= wp_parse_args($err_args,  [
			'errcode'	=>'errcode',
			'errmsg'	=>'errmsg',
			'detail'	=>'detail',
			'success'	=>'0',
		]);

		if(isset($response[$err_args['errcode']]) && $response[$err_args['errcode']] != $err_args['success']){
			$errcode	= wpjam_array_pull($response, $err_args['errcode']);
			$errmsg		= wpjam_array_pull($response, $err_args['errmsg']);
			$detail		= wpjam_array_pull($response, $err_args['detail']);
			$detail		= is_null($detail) ? array_filter($response) : $detail;

			if(apply_filters('wpjam_http_response_error_debug', true, $errcode, $errmsg, $detail)){
				trigger_error($this->url."\n".$errcode.' : '.$errmsg."\n".($detail ? var_export($detail,true)."\n" : '').var_export($this->body,true));
			}

			return new WP_Error($errcode, $errmsg, $detail);
		}

		return false;
	}
}

class WPJAM_Current{
	protected $data;

	private function __construct(){
		$this->data	= [];
	}

	public function __get($name){
		return $this->data[$name] ?? null;
	}

	public function __set($name, $value){
		$this->data[$name]	= $value;
	}

	public function __isset($name){
		return array_key_exists($name, $this->data);
	}

	public function get_items($name){
		$value	= $this->$name;

		return is_array($value) ? $value : [];
	}

	public function get_item($name, $key){
		$items	= $this->get_items($name);

		return $items[$key] ?? null;
	}

	public function add_item($name, ...$args){
		$value	= $this->get_items($name);

		if(count($args) >= 2){
			$key	= $args[0];
			
			if(array_key_exists($key, $value)){
				return false;
			}

			$value[$key]	= $args[1];
		}else{
			$value[]		= $args[0];
		}

		$this->$name	= $value;

		return true;
	}

	public function replace_item($name, $key, $item){
		$value	= $this->get_items($name);

		if(array_key_exists($key, $value)){
			$value[$key]	= $item;

			$this->$name	= $value;

			return true;
		}

		return false;
	}

	public function set_item($name, $key, $item){
		$value	= $this->get_items($name);

		$value[$key]	= $item;

		$this->$name	= $value;

		return true;
	}

	public function delete_item($name, $key){
		$value	= $this->get_items($name);

		if(array_key_exists($key, $value)){
			unset($value[$key]);

			$this->$name	= $value;

			return true;
		}

		return false;
	}

	public static $instance	= null;

	public static function get_instance(){
		if(is_null(self::$instance)){
			self::$instance	= new self();
		}

		return self::$instance;
	}
}

class WPJAM_API{
	public static function __callStatic($method, $args){
		$function	= 'wpjam_'.$method;

		if(function_exists($function)){
			return call_user_func($function, ...$args);
		}
	}

	public static function get_apis(){	// 兼容
		return WPJAM_JSON::get_registereds();
	}
}