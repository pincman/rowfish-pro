<?php

/**
 * 讯虎支付宝异步通知
 */

header('Content-type:text/html; Charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
ob_start();
require_once dirname(__FILE__) . "../../../../../../../wp-load.php";
ob_end_clean();

$_Config = _cao('xunhupay_alipay');
if (empty($_Config['mchid']) || empty($_Config['private_key'])) {
    exit('error');
}

// 接收异步通知,无需关注验签动作,已自动处理

$xhpay = new Xhpay($_Config);
$data  = $xhpay->getNotify();
if ($data['return_code'] == 'SUCCESS') {
    //商户本地订单号
    $out_trade_no = $data['out_trade_no'];
    //交易号
    $trade_no = $data['transaction_id'];
    //发送支付成功回调用
    $RiClass = new RiClass;
    $RiClass->send_order_trade_notify($out_trade_no, $trade_no);
    print 'success'; //当支付平台接收到此消息后，将不再重复回调当前接口
} else {
    echo $data['msg'];
}
exit();
