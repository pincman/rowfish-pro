<?php
// 要求noindex

if (!_cao('is_sns_weibo')) {
    wp_safe_redirect(home_url());exit;
}
do_action( 'ri_session_start' );
if (!$_SESSION['RIPLUS_WEIBO_STATE']) {
    riplus_wp_die('非法访问','没有经过第三方登录返回');
}

//获取后台配置
$opt    = _cao('sns_weibo');
$config = array(
    'app_id'     => $opt['app_id'],
    'app_secret' => $opt['app_secret'],
    'scope'      => 'get_user_info',
    'callback'   => home_url('/oauth/weibo/callback'),
);


$weiboOAuth = new \Yurun\OAuthLogin\Weibo\OAuth2($config['app_id'], $config['app_secret'], $config['callback']);

try {
   //验证AccessToken是否有效
   $weiboOAuth->getAccessToken($_SESSION['RIPLUS_WEIBO_STATE']);
} catch (Exception $e) {
    riplus_wp_die($e->getMessage(),'取信息获失败，请检查登录配置的appid和密钥是否正确');die;
    //TODO:处理支付调用异常的情况
}

//获取第三方openid
$openid = $weiboOAuth->openid;
//获取格式化后的第三方用户信息
$_snsInfo = $weiboOAuth->getUserInfo();
$snsInfo = [
    'openid'=>$openid,
    'unionid'=>'',
    'nick'=>$_snsInfo['screen_name'],
    'avatar'=>$_snsInfo['avatar_large'],
];

if (empty($openid) || empty($snsInfo)) {
    riplus_wp_die('登录失败', '未获取到第三方平台返回的用户信息');
}

//处理本地业务逻辑
$RiPlusSNS = new RiPlusSNS();
$RiPlusSNS->go_callback('weibo', $openid, $snsInfo, 'openid');

exit;
