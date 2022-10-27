<?php

/**
 * 支付宝异步通知Mapi
 */

header('Content-type:text/html; Charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
ob_start();
require_once dirname(__FILE__) . "../../../../../../../wp-load.php";
ob_end_clean();

if (empty($_POST)) {
    exit;
}

$_Config = _cao('alipay');

if (empty($_Config['md5Key'])) {
    exit('error');
}

// 公共配置
$params = new \Yurun\PaySDK\Alipay\Params\PublicParams;
$params->md5Key = $_Config['md5Key'];

// SDK实例化，传入公共配置
$pay = new \Yurun\PaySDK\Alipay\SDK($params);

// $content = var_export($_POST, true) . PHP_EOL . 'verify:' . $pay->verifyCallback($_POST);
// file_put_contents(__DIR__ . '/notify_result.txt', $content);

if ($pay->verifyCallback($_POST)) {
    // 模式2通知验证成功，可以通过POST参数来获取支付宝回传的参数
    $this_verify = true;
} else {
    $this_verify = false;
}

//商户本地订单号
$out_trade_no = $_POST['out_trade_no'];
//支付宝交易号
$trade_no = $_POST['trade_no'];

// 处理本地业务逻辑
if ($this_verify && $_POST['trade_status'] == 'TRADE_SUCCESS') {
    //发送支付成功回调用
    $RiClass = new RiClass;
    $RiClass->send_order_trade_notify($out_trade_no,$trade_no);
}

echo 'success';exit();

