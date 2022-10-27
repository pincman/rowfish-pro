<?php
$current_url = (empty($_REQUEST["redirect_to"])) ? urlencode(get_user_page_url()) : $_REQUEST["redirect_to"];

$sns_opt = [
    'qq' => ['name'=>'QQ','icon'=>'fa-qq','css'=>'primary'],
    'weixin' => ['name'=>'微信','icon'=>'fa-weixin','css'=>'success'],
    'weibo' => ['name'=>'微博','icon'=>'fa-weibo','css'=>'danger']
];
?>
<div class="d-block mt-1 mb-0 text-center">
<?php 
if (_cao('is_sns_qq') || _cao('is_sns_weixin') || _cao('is_sns_weibo')) {
	echo '<div class="social-text">
                  <hr class="text-300">
                  <div class="absolute-centered px-3">社交登录</div>
                </div>';
	echo '<ul class="oauth list-unstyled social-icon mb-0 mt-3">';
	foreach ($sns_opt as $type => $opt) {
		$typeclass = $type;
		if (_cao('is_sns_'.$type)) {
			if ($type=='weixin') {
				$sns_weixin = _cao('sns_weixin');
				$typeclass = ($sns_weixin['sns_weixin_mod']=='mp') ? 'weixin mpweixin' : $type ;
				$typeclass = (is_weixin_visit()) ? 'weixin mp' : $typeclass ;
			}
		echo '<li class="list-inline-item"><a href="'.get_open_oauth_url($type, $current_url).'" class="btn btn-sm btn-outline-'.$opt['css'].' oauth-btn '.$typeclass.'"><i class="fa '.$opt['icon'].' mr-1" title="'.$opt['name'].'登录"></i>'.$opt['name'].'</a></li>';
		}
	}
	echo '</ul>';
} 
?>
</div>
