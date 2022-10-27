<?php
// 要求noindex

if (!_cao('is_sns_qq')) {
    wp_safe_redirect(home_url());exit;
}

$opt    = _cao('sns_qq');
$Config = array(
    'app_id'     => $opt['app_id'],
    'app_secret' => $opt['app_secret'],
    'scope'      => 'get_user_info',
    'callback'   => esc_url(home_url('/oauth/qq/callback')),
);

$_appid = trim($Config['app_id']);
$_appkey = trim($Config['app_secret']);
do_action( 'ri_session_start' );
$OAuth                       = new \Yurun\OAuthLogin\QQ\OAuth2($_appid, $_appkey, $Config['callback']);
$url                         = $OAuth->getAuthUrl();
$_SESSION['RIPLUS_QQ_STATE'] = $OAuth->state;
$_SESSION['sns_rurl']        = (empty($_REQUEST["rurl"])) ? urlencode(get_user_page_url()) : urlencode($_REQUEST["rurl"]);

header('location:' . $url);
