<?php
// 要求noindex

if (!_cao('is_sns_weixin')) {
    wp_safe_redirect(home_url());exit;
}
do_action( 'ri_session_start' );
if (!$_SESSION['RIPLUS_WEIXIN_STATE']) {
    riplus_wp_die('非法访问，没有经过第三方登录返回');
}


$is_get_openid = (empty($_SESSION['is_get_weixin_openid'])) ? 0 : 1;

$opt = _cao('sns_weixin');

// 判断微信模式
if ($opt['sns_weixin_mod']=='open') {
    # 开放平台...
    $app_id     = $opt['app_id'];
    $app_secret = $opt['app_secret'];
    $scope      = 'snsapi_login';

}elseif ($opt['sns_weixin_mod']=='mp'){
    # 公众号... mp_app_token
    $app_id     = $opt['mp_app_id'];
    $app_secret = $opt['mp_app_secret'];
    $scope      = 'snsapi_userinfo';
}

//是否在微信内获取jsapi
$weixinpay = _cao('weixinpay');
$is_wxpay_openid = !empty($weixinpay) && $weixinpay['appid'] && $weixinpay['is_jsapi'];
if ($is_get_openid && $is_wxpay_openid && is_weixin_visit()) {
    $app_id     = $weixinpay['appid']; //使用支付配置中的appid
    // $app_id     = $opt['mp_app_id']; //使用公众号登录中的appid
    $app_secret = $opt['mp_app_secret'];
    $scope      = 'snsapi_base'; //snsapi_base
}

$config = array(
    'app_id'     => $app_id,
    'app_secret' => $app_secret,
    'scope'      => $scope, //如果需要静默授权，这里改成snsapi_base，扫码登录系统会自动改为snsapi_login
    'callback'   => home_url('/oauth/weixin/callback'),
);

$wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($config['app_id'], $config['app_secret']);
if($is_get_openid){
    $wxOAuth->openidMode = Yurun\OAuthLogin\Weixin\OpenidMode::OPEN_ID;
}else{
    if ($opt['sns_weixin_mod']=='open') {
       $wxOAuth->openidMode = Yurun\OAuthLogin\Weixin\OpenidMode::UNION_ID_FIRST;
    }elseif ($opt['sns_weixin_mod']=='mp'){
       $wxOAuth->openidMode = Yurun\OAuthLogin\Weixin\OpenidMode::OPEN_ID;
    }
}

try {
   //获取AccessToken
   $AccessToken = $wxOAuth->getAccessToken($_SESSION['RIPLUS_WEIXIN_STATE']);
} catch (Exception $e) {
    riplus_wp_die($e->getMessage(),'取信息获失败，请检查登录配置的appid和密钥是否正确');die;
    //TODO:处理支付调用异常的情况
}


//获取第三方openid
$openid = $wxOAuth->openid;

//如果在微信浏览器内访问 并且打开了公众号登录 则储存
if ($is_get_openid) {
    $_SESSION['current_weixin_openid'] = $openid;
    wp_safe_redirect(urldecode($_SESSION['sns_rurl']));exit;
}


//获取格式化后的第三方用户信息
$_snsInfo = $wxOAuth->getUserInfo();

$snsInfo  = [
    'openid'  => $openid,
    'unionid' => $_snsInfo['unionid'],
    'nick'    => $_snsInfo['nickname'],
    'avatar'  => $_snsInfo['headimgurl'],
];


if (empty($openid) || empty($snsInfo)) {
    riplus_wp_die('登录失败', '未获取到第三方平台返回的用户信息');
}

//处理本地业务逻辑
$RiPlusSNS = new RiPlusSNS();
// 判断微信模式
if ($opt['sns_weixin_mod']=='open') {
   $wexin_key_type = 'weixin';
   $mpwx_get_user_id_type = 'unionid';
}elseif ($opt['sns_weixin_mod']=='mp'){
   $wexin_key_type = 'mpweixin';
   $mpwx_get_user_id_type = 'openid';
}
$RiPlusSNS->go_callback($wexin_key_type, $openid, $snsInfo, $mpwx_get_user_id_type);

exit;
