<?php
// 要求noindex

if (!_cao('is_sns_weixin')) {
    wp_safe_redirect(home_url());exit;
}
do_action( 'ri_session_start' );
//是否单独静默获取openid
$is_get_openid = (empty($_REQUEST["get_openid"])) ? 0 : 1;
$_SESSION['is_get_weixin_openid'] = $is_get_openid;

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

$OAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($config['app_id'], $config['app_secret']);

if($is_get_openid){
    $OAuth->openidMode = Yurun\OAuthLogin\Weixin\OpenidMode::OPEN_ID;
}else{
    $OAuth->openidMode = Yurun\OAuthLogin\Weixin\OpenidMode::UNION_ID_FIRST;
}

//如果在微信浏览器内访问 并且打开了公众号登录 则跳转登录
if ($opt['sns_weixin_mod']=='mp' && is_weixin_visit()) {
    # 微信浏览器内...
    $url = $OAuth->getWeixinAuthUrl($config['callback'], null, $config['scope']);
} else {
    $url = $OAuth->getAuthUrl($config['callback'], null, null);
}

$_SESSION['RIPLUS_WEIXIN_STATE'] = $OAuth->state;
$_SESSION['sns_rurl'] = (empty($_REQUEST["rurl"])) ? urlencode(get_user_page_url()) : urlencode($_REQUEST["rurl"]);

header('location:' . $url);
