<?php

/**
 * 虎皮椒微信异步通知
 */

header('Content-type:text/html; Charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
ob_start();
require_once dirname(__FILE__) . "../../../../../../../wp-load.php";
ob_end_clean();

if (empty($_POST)) {exit;}

$_Config = _cao('hupijiao_weixin');
if (empty($_Config['appsecret'])) {
    exit('error');
}

$RiPlusPay = new RiPlusPay;

$data = $_POST;
foreach ($data as $k => $v) {
    $data[$k] = stripslashes($v);
}
if (!isset($data['hash']) || !isset($data['trade_order_id'])) {
    exit('error');
}
//自定义插件ID,请与支付请求时一致
if (isset($data['plugins']) && $data['plugins'] != 'rizhuti-xunhupay-v3') {
    exit('failed');
}

//APP SECRET
$appkey = $_Config['appsecret'];
$hash   = $RiPlusPay->generate_xh_hash($data, $appkey);
if ($data['hash'] != $hash) {
    //签名验证失败
    exit('error');
}

if ($data['status'] == 'OD') {
    //商户本地订单号
    $out_trade_no = $data['trade_order_id'];
    //交易号
    $trade_no = $data['transaction_id'];
    //发送支付成功回调用
    $RiClass = new RiClass;
    $RiClass->send_order_trade_notify($out_trade_no, $trade_no);
}
echo 'success';exit();
