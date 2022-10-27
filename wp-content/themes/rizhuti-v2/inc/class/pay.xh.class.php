<?php
// XunhuPAY API in tizhuti-v2 by ritheme.com
class Xhpay{
    private $mchid;
    private $key;
    private $api_url_native;
    private $api_url_cashier;
    private $api_url_alipaycashier;
    private $api_url_jsapi;
    private $api_url_query;
    private $api_url_refund;

    public function __construct(array $config)
    {
        $this->mchid = $config['mchid'];
        $this->key   = $config['private_key'];
        $api_url     = $config['url_do'];
        $this->api_url_native  = $api_url . '/pay/payment';
        $this->api_url_cashier = $api_url . '/pay/cashier';
        $this->api_url_alipaycashier = $api_url . '/alipaycashier';
        $this->api_url_jsapi   = $api_url . '/pay/jsapi';
        $this->api_url_query   = $api_url . '/pay/query';
        $this->api_url_refund   = $api_url . '/pay/refund';
    }

    // 扫码支付
    public function native(array $data)
    {
        $this->url = $this->api_url_native;
        return $this->post($data);
    }

    // JSAPI 模式
    public function jsapi(array $data)
    {
        $this->url = $this->api_url_jsapi;
        return $this->post($data);
    }

    // 收银台模式
    public function cashier(array $data)
    {
        if ($data['type'] == "wechat") {
            $this->url = $this->api_url_cashier;
        }else{
            $this->url = $this->api_url_alipaycashier;
        }
        $data      = $this->sign($data);
        $pay_url     = $this->data_link($this->url, $data);
        return $pay_url;
    }

    public function h5(array $data)
    {
        $this->url = $this->api_url_native;
        $data['type'] = "wechat";
        $data['trade_type'] = "WAP";
        return $this->post($data);
    }

    public function query(array $data)
    {
        $this->url = $this->api_url_query;
        return $this->post($data);
    }

    // 退款
    public function refund(array $data)
    {
        $this->url = $this->api_url_refund;
        return $this->post($data);
    }

    // 异步通知接收
    public function getNotify()
    {
        $data=json_decode(file_get_contents('php://input'),true);
        if(!$data){
            exit('faild!');
        }
        if ($this->checkSign($data) === true) {
            return $data;
        } else {
            return ['return_code'=>'error','msg'=>'验证签名失败'];
        }
    }
    /**
     * 获取随机数
     */
    public function getNonce(){
        static $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // 数据签名
    public function sign(array $data, $buff = '')
    {
        $data['mchid'] = $this->mchid;
        $data['nonce_str'] = $this->getNonce();
        ksort($data);
        reset($data);
        if (isset($data['sign'])) unset($data['sign']);
        foreach ($data as $k => $v) $buff .= "{$k}={$v}&";
        $buff .= ("key=" . $this->key);
        $data['sign'] = strtoupper(md5($buff));
        return $data;
    }

    // 校验数据签名
    public function checkSign($data, $buff = '')
    {
        $in_sign = $data['sign'];
        ksort($data);
        reset($data);
        if (isset($data['sign'])) unset($data['sign']);
        foreach ($data as $k => $v) $buff .= "{$k}={$v}&";
        $buff .= ("key=" . $this->key);
        $sign = strtoupper(md5($buff));
        return $in_sign == $sign ? true : false;
    }

    public function data_link($url,$datas){
        ksort($datas);
        reset($datas);
        $pre =array();
        foreach ($datas as $key => $data){
            if(is_null($data)||$data===''){
                continue;
            }
   
            $pre[$key]=$data;
        }
        $arg  = '';
        $qty = count($pre);
        $index=0;
         foreach ($pre as $key=>$val){
                $val=urlencode($val);
                $arg.="$key=$val";
                if($index++<($qty-1)){
                    $arg.="&";
                }
        }
        return $url.'?'.$arg;
    }

    // 数据发送
    public function post($data)
    {
        $data   = json_encode($this->sign($data));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 信任任何证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);        // 表示不检查证书
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return json_decode($response, true);
    }
}