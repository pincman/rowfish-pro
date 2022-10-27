<?php
// 要求noindex

if (!_cao('is_sns_qq')) {
    wp_safe_redirect(home_url());exit;
}

//获取后台配置
do_action( 'ri_session_start' );
if (!$_SESSION['RIPLUS_QQ_STATE']) {
    riplus_wp_die('非法访问','没有经过第三方登录返回');die;
}

$opt    = _cao('sns_qq');
$config = array(
    'app_id'     => $opt['app_id'],
    'app_secret' => $opt['app_secret'],
    'scope'      => 'get_user_info',
    'callback'   => esc_url(home_url('/oauth/qq/callback')),
);
$_appid = trim($config['app_id']);
$_appkey = trim($config['app_secret']);


$qqOAuth = new \Yurun\OAuthLogin\QQ\OAuth2($_appid, $_appkey, $config['callback']);

try {
   //验证AccessToken是否有效
    $AccessToken = $qqOAuth->getAccessToken($_SESSION['RIPLUS_QQ_STATE']);
} catch (Exception $e) {
    riplus_wp_die($e->getMessage(),'取信息获失败，请检查登录配置的appid和密钥是否正确');die;
    //TODO:处理支付调用异常的情况
}



//获取格式化后的第三方用户信息
$_snsInfo = $qqOAuth->getUserInfo();

$openid = $qqOAuth->openid;
$snsInfo = [
    'openid'=>$openid,
    'unionid'=>'',
    'nick'=>$_snsInfo['nickname'],
    'avatar'=>$_snsInfo['figureurl_qq_2'],
];
if (empty($openid) || empty($snsInfo)) {
    riplus_wp_die('登录失败', '未获取到第三方平台返回的用户信息');
}

//处理本地业务逻辑
$RiPlusSNS = new RiPlusSNS();
$RiPlusSNS->go_callback('qq', $openid, $snsInfo, 'openid');

exit;
