<?php
// 要求noindex

if (!_cao('is_sns_weibo')) {
    wp_safe_redirect(home_url());exit;
}

$opt    = _cao('sns_weibo');
$config = array(
    'app_id'     => $opt['app_id'],
    'app_secret' => $opt['app_secret'],
    'scope'      => 'all',
    'callback'   => home_url('/oauth/weibo/callback'),
);
do_action( 'ri_session_start' );
$OAuth                          = new \Yurun\OAuthLogin\Weibo\OAuth2($config['app_id'], $config['app_secret'], $config['callback']);
$url                            = $OAuth->getAuthUrl();
$_SESSION['RIPLUS_WEIBO_STATE'] = $OAuth->state;
$_SESSION['sns_rurl']        = (empty($_REQUEST["rurl"])) ? urlencode(get_user_page_url()) : urlencode($_REQUEST["rurl"]);
header('location:' . $url);
