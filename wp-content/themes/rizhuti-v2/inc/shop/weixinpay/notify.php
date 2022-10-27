<?php

/**
 * 微信异步通知
 */

header('Content-type:text/html; Charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
ob_start();
require_once dirname(__FILE__) . "../../../../../../../wp-load.php";
ob_end_clean();

$_Config = _cao('weixinpay');

if (empty($_Config['appid']) || empty($_Config['key'])) {
    exit('error');
}


// 公共配置
$params         = new \Yurun\PaySDK\Weixin\Params\PublicParams;
$params->appID  = $_Config['appid'];
$params->mch_id = $_Config['mch_id'];
$params->key    = $_Config['key'];

// SDK实例化，传入公共配置
$sdk = new \Yurun\PaySDK\Weixin\SDK($params);

class PayNotify extends \Yurun\PaySDK\Weixin\Notify\Pay {
    /**
     * 后续执行操作
     * @return void
     */
    protected function __exec() {
        // 支付成功处理，一般做订单处理，$this->data 是从微信发送来的数据
        // file_put_contents(__DIR__ . '/notify_result.txt', date('Y-m-d H:i:s') . ':' . var_export($this->data, true));
        //商户本地订单号
        $out_trade_no = $this->data['out_trade_no'];
        //支付宝交易号
        $trade_no = $this->data['transaction_id'];
        //发送支付成功回调用
	    $RiClass = new RiClass;
        $RiClass->send_order_trade_notify($out_trade_no, $trade_no);

        // 告诉微信我处理过了，不要再通过了
        $this->reply(true, 'OK');
    }
}
$payNotify = new PayNotify;
try {
    $sdk->notify($payNotify);
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/notify_result.txt', $e->getMessage() . ':' . var_export($payNotify->data, true));
}

exit();
