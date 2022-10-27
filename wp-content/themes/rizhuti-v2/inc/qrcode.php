<?php

if (empty($_GET['data'])) {
    exit('QR code not found');
}

$url = urldecode($_GET['data']);

//引入phpqrcode类库
require_once dirname(__FILE__) . '/class/qrcode.class.php';

QRcode::png($url, false, 'L', 6, 2);
