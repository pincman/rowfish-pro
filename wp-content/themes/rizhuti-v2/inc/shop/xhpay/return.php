<?php

/**
 * 虎皮椒同步回掉
 */

header('Content-type:text/html; Charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
ob_start();
require_once dirname(__FILE__) . "../../../../../../../wp-load.php";
ob_end_clean();

if (!session_id()) {
    session_start();
}

$orderNum = (!empty($_SESSION['current_pay_ordernum'])) ? $_SESSION['current_pay_ordernum'] : 0 ;
if (empty($orderNum)) {
    $orderNum = (!empty($_COOKIE["current_pay_ordernum"])) ? $_COOKIE['current_pay_ordernum'] : 0 ;
}

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

wp_redirect(home_url('/'));exit;

echo 'success';exit();

