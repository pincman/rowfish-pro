<?php

/**
 * 支付宝当面付异步通知
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

if (empty($_Config['publicKey']) || empty($_Config['privateKey']) ) {
    exit('error');
}


//验证签名
$AlipayServiceCheck = new AlipayServiceCheck($_Config['publicKey']);
if ($AlipayServiceCheck->rsaCheck($_POST, $_POST['sign_type']) === true) {
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

echo 'success';exit;



// 调用其他类 AlipayServiceCheck
class AlipayServiceCheck {
    //支付宝公钥
    protected $alipayPublicKey;
    protected $charset;
    public function __construct($alipayPublicKey) {
        $this->charset         = 'utf8';
        $this->alipayPublicKey = $alipayPublicKey;
    }
    public function rsaCheck($params) {
        $sign     = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }
    public function verify($data, $sign, $signType = 'RSA') {
        $pubKey = $this->alipayPublicKey;
        $res    = "-----BEGIN PUBLIC KEY-----\n" .
        wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool) openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION, '5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool) openssl_verify($data, base64_decode($sign), $res);
        }
        return $result;
    }
    protected function checkEmpty($value) {
        if (!isset($value)) {
            return true;
        }
        if ($value === null) {
            return true;
        }
        if (trim($value) === "") {
            return true;
        }
        return false;
    }
    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i                = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 修复转义导致签名失败
                $v = stripslashes($v);
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset($k, $v);
        return $stringToBeSigned;
    }
    public function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }
}
