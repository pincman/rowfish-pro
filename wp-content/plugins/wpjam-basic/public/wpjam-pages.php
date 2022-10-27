<?php
class WPJAM_Crons_Admin{
	public static function get_primary_key(){
		return 'cron_id';
	}

	public static function get($id){
		list($timestamp, $hook, $key)	= explode('--', $id);

		$wp_crons = _get_cron_array() ?: [];

		if(isset($wp_crons[$timestamp][$hook][$key])){
			$data	= $wp_crons[$timestamp][$hook][$key];

			$data['hook']		= $hook;
			$data['timestamp']	= $timestamp;
			$data['time']		= get_date_from_gmt(date('Y-m-d H:i:s', $timestamp));
			$data['cron_id']	= $id;
			$data['interval']	= $data['interval'] ?? 0;

			return $data;
		}else{
			return new WP_Error('cron_not_exist', '该定时作业不存在');
		}
	}

	public static function insert($data){
		if(!has_filter($data['hook'])){
			return new WP_Error('invalid_hook', '非法 hook');
		}

		$timestamp	= strtotime(get_gmt_from_date($data['time']));

		if($data['interval']){
			wp_schedule_event($timestamp, $data['interval'], $data['hook'], $data['_args']);
		}else{
			wp_schedule_single_event($timestamp, $data['hook'], $data['_args']);
		}

		return true;
	}

	public static function do($id){
		$data = self::get($id);

		if(is_wp_error($data)){
			return $data;
		}

		$result	= do_action_ref_array($data['hook'], $data['args']);

		if(is_wp_error($result)){
			return $result;
		}else{
			return true;
		}
	}

	public static function delete($id){
		$data = self::get($id);

		if(is_wp_error($data)){
			return $data;
		}

		return wp_unschedule_event($data['timestamp'], $data['hook'], $data['args']);
	}

	public static function query_items($limit, $offset){
		$items		= [];
		$wp_crons	= _get_cron_array() ?: [];

		foreach($wp_crons as $timestamp => $wp_cron){
			foreach($wp_cron as $hook => $dings){
				foreach($dings as $key=>$data){
					if(!has_filter($hook)){
						wp_unschedule_event($timestamp, $hook, $data['args']);	// 系统不存在的定时作业，自动清理
						continue;
					}

					$items[] = [
						'cron_id'	=> $timestamp.'--'.$hook.'--'.$key,
						'time'		=> get_date_from_gmt(date('Y-m-d H:i:s', $timestamp)),
						'hook'		=> $hook,
						'interval'	=> $data['interval'] ?? 0
					];
				}
			}
		}

		return ['items'=>$items, 'total'=>count($items)];
	}

	public static function get_actions(){
		return [
			'add'		=> ['title'=>'新建',		'response'=>'list'],
			'do'		=> ['title'=>'立即执行',	'direct'=>true,	'confirm'=>true,	'bulk'=>2,		'response'=>'list'],
			'delete'	=> ['title'=>'删除',		'direct'=>true,	'confirm'=>true,	'bulk'=>true,	'response'=>'list']
		];
	}

	public static function get_fields($action_key='', $id=0){
		$schedule_options	= [0=>'只执行一次']+wp_list_pluck(wp_get_schedules(), 'display', 'interval');

		return [
			'hook'		=> ['title'=>'Hook',	'type'=>'text',		'show_admin_column'=>true],
			'time'		=> ['title'=>'运行时间',	'type'=>'text',		'show_admin_column'=>true,	'value'=>current_time('mysql')],
			'interval'	=> ['title'=>'频率',		'type'=>'select',	'show_admin_column'=>true,	'options'=>$schedule_options],
		];
	}
}

class WPJAM_Grants_Admin{
	public static function render_item($appid='', $secret=''){
		if($appid){
			$secret	= $secret ? '<p class="secret" id="secret_'.$appid.'" style="display:block;">'.$secret.'</p>' : '<p class="secret" id="secret_'.$appid.'"></p>';
			$times	= WPJAM_Grant::get_instance()->cache_get_item($appid, 'token.grant') ?: 0;
			$data	= ['data'=>['appid'=>$appid]];

			return '
			<tbody id="appid_'.$appid.'">
				<tr>
					<th>AppID</th>
					<td class="appid">'.$appid.'</td>
					<td>'.wpjam_get_page_button('delete_grant', $data).'</td>
				</tr>
				<tr>
					<th>Secret</th>
					<td>出于安全考虑，Secret不被明文保存，忘记密钥请点击重置：'.$secret.'</td>
					<td>'.wpjam_get_page_button('reset_secret', $data).'</td>
				</tr>
				<tr>
					<th>用量</th>
					<td>鉴权接口已被调用了 <strong>'.$times.'</strong> 次，更多接口调用统计请点击'.wpjam_get_page_button('get_stats', $data).'</td>
					<td>'.wpjam_get_page_button('clear_quota', $data).'</td>
				</tr>
			</tbody>
			';
		}else{
			return '
			<tbody id="create_grant">
				<tr>
					<th>创建</th>
					<td>点击右侧创建按钮可创建 AppID/Secret，最多可创建三个：</td>
					<td>'.wpjam_get_page_button('create_grant').'</td>
				</tr>
			</tbody>
			';
		}
	}

	public static function get_fields($name){
		if($name == 'get_stats'){
			$appid	= wpjam_get_data_parameter('appid');
			$caches	= WPJAM_Grant::get_instance()->cache_get($appid);
			$fields	= [];

			if($appid){
				$fields['appid']	= ['title'=>'APPID',	'type'=>'view', 'value'=>$appid];

				$caches['token.grant']	= $caches['token.grant'] ?? 0;
			}

			if($caches){
				foreach($caches as $json => $times){
					$fields[$json]	= ['title'=>$json,	'type'=>'view', 'value'=>$times];
				}
			}else{
				$fields['no']	= ['type'=>'view', 'value'=>'暂无数据'];
			}

			return $fields;
		}elseif($name == 'get_doc'){
			$doc	= '
			<p>access_token 是开放接口的全局<strong>接口调用凭据</strong>，第三方调用各接口时都需使用 access_token，开发者需要进行妥善保存。</p>
			<p>access_token 的有效期目前为2个小时，需定时刷新，重复获取将导致上次获取的 access_token 失效。</p>

			<h4>请求地址</h4>

			<p><code>'.home_url('/api/').'token/grant.json?appid=APPID&secret=APPSECRET</code></p>

			<h4>参数说明<h4>

			'.do_shortcode('[table th=1 class="form-table striped"]
			参数	
			是否必须
			说明

			appid
			是
			第三方用户凭证

			secret
			是
			第三方用户证密钥。
			[/table]').'
			
			<h4>返回说明</h4>

			<p><code>
				{"errcode":0,"access_token":"ACCESS_TOKEN","expires_in":7200}
			</code></p>';

			return ['access_token'=>['type'=>'view', 'value'=>$doc]];
		}			
	}

	public static function ajax_response($name){
		$object	= WPJAM_Grant::get_instance();

		if($name == 'create_grant'){
			$result	= $object->create();

			if(is_wp_error($result)){
				wpjam_send_json($result);
			}

			$appid	= is_array($result) ? $result['id'] : $result;
			$secret	= $object->reset_secret($appid);

			wpjam_send_json(['item'=>self::render_item($appid, $secret)]);
		}else{
			$appid	= wpjam_get_data_parameter('appid');

			if($name == 'delete_grant'){
				$result	= $object->delete($appid);

				if(is_wp_error($result)){
					wpjam_send_json($result);
				}else{
					wpjam_send_json(compact('appid'));
				}
			}elseif($name == 'reset_secret'){
				$secret	= $object->reset_secret($appid);

				if(is_wp_error($secret)){
					wpjam_send_json($secret);
				}else{
					wpjam_send_json(compact('appid', 'secret'));
				}
			}elseif($name == 'clear_quota'){
				$object->cache_delete($appid);

				wpjam_send_json(['errmsg'=>'接口已清零']);
			}
		}
	}

	public static function load_plugin_page(){
		wpjam_register_page_action('create_grant', [
			'button_text'	=> '创建',
			'class'			=> 'button',
			'direct'		=> true,
			'confirm'		=> true,
			'callback'		=> [self::class, 'ajax_response']
		]);

		wpjam_register_page_action('reset_secret', [
			'button_text'	=> '重置',
			'class'			=> 'button',
			'direct'		=> true,
			'confirm'		=> true,
			'callback'		=> [self::class, 'ajax_response']
		]);

		wpjam_register_page_action('delete_grant', [
			'button_text'	=> '删除',
			'class'			=> 'button',
			'direct'		=> true,
			'confirm'		=> true,
			'callback'		=> [self::class, 'ajax_response']
		]);

		wpjam_register_page_action('clear_quota', [
			'button_text'	=> '清零',
			'class'			=> 'button button-primary',
			'direct'		=> true,
			'confirm'		=> true,
			'callback'		=> [self::class, 'ajax_response']
		]);

		wpjam_register_page_action('get_stats', [
			'button_text'	=> '用量',
			'submit_text'	=> '',
			'class'			=> '',
			'width'			=> 500,
			'fields'		=> [self::class, 'get_fields']
		]);

		wpjam_register_page_action('get_doc', [
			'button_text'	=> '接口文档',
			'submit_text'	=> '',
			'page_title'	=> '获取access_token',
			'class'			=> 'page-title-action button',
			'fields'		=> [self::class, 'get_fields']
		]);

		wp_add_inline_style('list-tables', str_replace("\t\t",'',"\ndiv.card{max-width:640px;}
		
		div.card table.form-table{border: none;}
		div.card table.form-table tbody:after{content: ' '; margin-bottom: 40px; display: block;}
		div.card table.form-table tbody:nth-of-type(4){display: none;}
		
		table.form-table th{width: 60px; padding-left: 10px;}

		table.form-table td.appid{font-weight: bold;}
		table.form-table p.secret{display: none; background: #ffc; padding:4px 8px; font-weight: bold;}

		div.card h3{margin-bottom: 40px;}
		div.card h3 span.page-actions{float:right;}"));
	}

	public static function plugin_page(){
		echo '<div class="card">';

		echo '<h3>开发者 ID<span class="page-actions">'.wpjam_get_page_button('get_doc').'</span></h3>';

		echo '<table class="form-table widefat striped">';

		foreach(WPJAM_Grant::get_instance()->get_items() as $item){
			echo self::render_item($item['appid']); 
		}

		echo self::render_item();

		echo '</table>';
		
		echo '</div>';

		?><script type="text/javascript">
		jQuery(function($){
			$('body').on('page_action_success', function(e, response){
				if(response.page_action == 'create_grant'){
					$('#create_grant').before(response.item);
				}else if(response.page_action == 'delete_grant'){
					$('#appid_'+response.appid).remove();
				}else if(response.page_action == 'reset_secret'){
					$('#secret_'+response.appid).show().html(response.secret);
				}
			});
		});
		</script><?php
	}
}

class WPJAM_Errors_Admin extends WPJAM_Model{
	public static function export(){
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment;filename=wpjam_errors.txt');
		header('Pragma: no-cache');
		header('Expires: 0');

		$handle	= fopen('php://output', 'w');
		$data	= self::get_handler()->get_items();

		fputs($handle, maybe_serialize($data));

		fclose($handle);

		exit;
	}

	public static function import($data){
		$items	= $data['items'] ? maybe_unserialize($data['items']) : [];

		foreach($items as $errcode => $item){
			if(self::get($errcode)){
				if($data['overwrite']){
					self::update($errcode, $item);
				}
			}else{
				self::insert($item);
			}
		}

		return true;
	}

	public static function query_items($limit, $offset){
		if(isset($_REQUEST['export_action'])){
			self::export();
		}

		return parent::query_items($limit, $offset);
	}

	public static function get_fields($action_key='', $id=0){
		if($action_key == 'import'){
			return [
				'items'		=> ['type'=>'textarea',],
				'overwrite'	=> ['type'=>'checkbox',	'description'=>'覆盖已有的设置']
			];
		}else{
			return [
				'errcode'	=> ['title'=>'代码',	'type'=>'text',		'show_admin_column'=>true,	'class'=>'all-options'],
				'errmsg'	=> ['title'=>'信息',	'type'=>'text',		'show_admin_column'=>true],
				'modalset'	=> ['title'=>'弹窗',	'type'=>'fieldset',	'fields'=>[
					'show_modal'=> ['title'=>'',	'type'=>'checkbox',	'description'=>'显示弹窗'],
					'title'		=> ['title'=>'标题',	'type'=>'text',		'name'=>'modal[title]',		'show_if'=>['key'=>'show_modal', 'value'=>1]],
					'cotent'	=> ['title'=>'内容',	'type'=>'textarea',	'name'=>'modal[content]',	'show_if'=>['key'=>'show_modal', 'value'=>1],	'rows'=>3],
				]]	
			];
		}
	}

	public static function get_actions(){
		return [
			'add'		=> ['title'=>'新建',	'dismiss'=>true],
			'edit'		=> ['title'=>'编辑'],
			'delete'	=> ['title'=>'删除',	'direct'=>true, 	'confirm'=>true,	'bulk'=>true],
			'import'	=> ['title'=>'导入',	'overall'=>true,	'response'=>'list'],
			'export'	=> ['title'=>'导出',	'overall'=>true,	'direct'=>true, 	'redirect'=>admin_url('admin.php?page=wpjam-errors&export_action')],
		];
	}

	public static function get_handler(){
		return WPJAM_Error::get_instance();
	}
}

function wpjam_dashicons_page(){
	$file	= fopen(ABSPATH.'/'.WPINC.'/css/dashicons.css','r') or die("Unable to open file!");
	$html	= '';

	while(!feof($file)) {
		if($line = fgets($file)){
			if(preg_match_all('/.dashicons-(.*?):before/i', $line, $matches) && $matches[1][0] != 'before'){
				$html .= '<p data-dashicon="dashicons-'.$matches[1][0].'"><span class="dashicons-before dashicons-'.$matches[1][0].'"></span> <br />'.$matches[1][0].'</p>'."\n";
			}
		}
	}

	fclose($file);

	echo '<div class="wpjam-dashicons">'.$html.'</div>'.'<div class="clear"></div>';
	?>
	<style type="text/css">
	div.wpjam-dashicons{max-width: 800px; float: left;}
	div.wpjam-dashicons p{float: left; margin:0px 10px 10px 0; padding: 10px; width:70px; height:70px; text-align: center; cursor: pointer;}
	div.wpjam-dashicons .dashicons-before:before{font-size:32px; width: 32px; height: 32px;}
	div#TB_ajaxContent p{font-size:20px; float: left;}
	div#TB_ajaxContent .dashicons{font-size:100px; width: 100px; height: 100px;}
	</style>
	<script type="text/javascript">
	jQuery(function($){
		$('body').on('click', 'div.wpjam-dashicons p', function(){
			let dashicon	= $(this).data('dashicon');
			let html 		= '<p><span class="dashicons '+dashicon+'"></span></p><p style="margin-left:20px;">'+dashicon+'<br /><br />HTML：<br /><code>&lt;span class="dashicons '+dashicon+'"&gt;&lt;/span&gt;</code></p>';
			
			$.wpjam_show_modal('tb_modal', html, dashicon, 680);
		});
	});
	</script>
	<?php
}

function wpjam_about_page(){
	$jam_plugins = get_transient('about_jam_plugins');

	if($jam_plugins === false){
		$response	= wpjam_remote_request('https://jam.wpweixin.com/api/template/get.json?id=5644');

		if(!is_wp_error($response)){
			$jam_plugins	= $response['template']['table']['content'];
			set_transient('about_jam_plugins', $jam_plugins, DAY_IN_SECONDS );
		}
	}

	?>
	<div style="max-width: 900px;">
		<table id="jam_plugins" class="widefat striped">
			<tbody>
			<tr>
				<th colspan="2">
					<h2>WPJAM 插件</h2>
					<p>加入<a href="https://97866.com/s/zsxq/">「WordPress果酱」知识星球</a>即可下载：</p>
				</th>
			</tr>
			<?php foreach($jam_plugins as $jam_plugin){ ?>
			<tr>
				<th style="width: 100px;"><p><strong><a href="<?php echo $jam_plugin['i2']; ?>"><?php echo $jam_plugin['i1']; ?></a></strong></p></th>
				<td><?php echo wpautop($jam_plugin['i3']); ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>

		<div class="card">
			<h2>WPJAM Basic</h2>

			<p><strong><a href="https://blog.wpjam.com/project/wpjam-basic/">WPJAM Basic</a></strong> 是 <strong><a href="https://blog.wpjam.com/">我爱水煮鱼</a></strong> 的 Denis 开发的 WordPress 插件。</p>

			<p>WPJAM Basic 除了能够优化你的 WordPress ，也是 「WordPress 果酱」团队进行 WordPress 二次开发的基础。</p>
			<p>为了方便开发，WPJAM Basic 使用了最新的 PHP 7.2 语法，所以要使用该插件，需要你的服务器的 PHP 版本是 7.2 或者更高。</p>
			<p>我们开发所有插件都需要<strong>首先安装</strong> WPJAM Basic，其他功能组件将以扩展的模式整合到 WPJAM Basic 插件一并发布。</p>
		</div>

		<div class="card">
			<h2>WPJAM 优化</h2>
			<p>网站优化首先依托于强劲的服务器支撑，这里强烈建议使用<a href="https://wpjam.com/go/aliyun/">阿里云</a>或<a href="https://wpjam.com/go/qcloud/">腾讯云</a>。</p>
			<p>更详细的 WordPress 优化请参考：<a href="https://blog.wpjam.com/article/wordpress-performance/">WordPress 性能优化：为什么我的博客比你的快</a>。</p>
			<p>我们也提供专业的 <a href="https://blog.wpjam.com/article/wordpress-optimization/">WordPress 性能优化服务</a>。</p>
		</div>
	</div>
	<style type="text/css">
		.card {max-width: 320px; float: left; margin-top:20px;}
		.card a{text-decoration: none;}
		table#jam_plugins{margin-top:20px; width: 520px; float: left; margin-right: 20px;}
		table#jam_plugins th{padding-left: 2em; }
		table#jam_plugins td{padding-right: 2em;}
		table#jam_plugins th p, table#jam_plugins td p{margin: 6px 0;}
	</style>
<?php }