<?php

/**
 * 支付宝异步通知Mapi
 */

header('Content-type:text/html; Charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
ob_start();
require_once dirname(__FILE__) . "../../../../../../../wp-load.php";
ob_end_clean();

if (empty($_GET)) {
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

if (!$pay->verifyCallback($_GET)) {
   	echo 'verify false';exit();
}

// $orderNum = (!empty($_SESSION['current_pay_ordernum'])) ? $_SESSION['current_pay_ordernum'] : 0 ;
// if (empty($orderNum)) {
//     $orderNum = (!empty($_COOKIE["current_pay_ordernum"])) ? $_COOKIE['current_pay_ordernum'] : 0 ;
// }

$orderNum = $_GET['out_trade_no'];

if(!empty($orderNum)){
    
	$RiClass = new RiClass;
	$order = $RiClass->get_order_info($orderNum);
	// 有订单并且已经支付
    if (!empty($order) && $order->status == 1) {
        if (!is_user_logged_in() && _cao('is_riplus_nologin_pay')){
    		$RiPlusShop->AddPayPostCookie(0, $order->post_id, $order->order_trade_no);
        	unset( $_SESSION['current_pay_ordernum'] );
    	}
    	
        if( $order->post_id>0 && $order->order_type==1 ){
            wp_redirect( get_the_permalink( $order->post_id ) );exit;
        }else{
            wp_redirect(get_user_page_url());exit;
        }
    }
    
}
wp_safe_redirect(home_url('/'));exit;
echo 'success';exit();

