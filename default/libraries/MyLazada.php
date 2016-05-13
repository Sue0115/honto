<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/2
 * Time: 13:00
 */
class MyLazada
{
    //获取订单数据
    public function getLazadaOrder($api_host,$token,$user, $limit, $offset)
    {
        //$api_key = '39a0bcdb752b23a5a687dbee0ee32a403c4d88db';
        $api_key = $token;

        $now = new DateTime();

      // $after = new DateTime(date("Y-m-d H:i:s",time()-3600));
        $after = new DateTime(date("Y-m-d",strtotime('-9 day')));
      // $after = new DateTime('2015-10-27 23:17:00');
        ////var_dump($after);exit;
        $parameters = array(
            //'UserID' => 'lazada.api@moonar.com',
            'UserID' => $user,
            'Version' => '1.0',
            'Action' => 'GetOrders',
            'Timestamp' => $now->format(DateTime::ISO8601),
          //  'UpdatedAfter' => $after->format(DateTime::ISO8601),
            'Status' => 'pending', // 只抓pending 状态下的订单

            'CreatedAfter'=>$after->format(DateTime::ISO8601),
        	'Limit'	=> $limit,
        	'Offset'	=> $offset,
        );
        ksort($parameters);

        $params = array();

        foreach ($parameters as $name => $value)
        {
            $params[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        $strToSign = implode('&', $params);

        $parameters['Signature'] = rawurlencode(hash_hmac('sha256', $strToSign, $api_key, false));

        $request = http_build_query($parameters);

        $orders=$this->getCurlData($api_host.'/?'.$request);

        return $orders;

    }



    //根据订单号获取订单产品信息
    public function get_OrderItem($api_host,$token,$user,$order_id)
    {
        //$api_key = '39a0bcdb752b23a5a687dbee0ee32a403c4d88db';
        $api_key = $token;
        $now = new DateTime();

        $parameters = array(
            //'UserID' => 'lazada.api@moonar.com',
            'UserID' => $user,
            'Version' => '1.0',
            'Action' => 'GetOrderItems',
            'Timestamp' => $now->format(DateTime::ISO8601),
            //'OrderId' => '989325',
            'OrderId' => $order_id,
        );
        ksort($parameters);

        $params = array();

        foreach ($parameters as $name => $value) {
            $params[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        $strToSign = implode('&', $params);

        $parameters['Signature'] = rawurlencode(hash_hmac('sha256', $strToSign, $api_key, false));

        $request = http_build_query($parameters);

        $order_item=$this->getCurlData($api_host.'/?'.$request);

        return $order_item;

    }

    //上传追踪号，进行标记发货
    public function upLoadShippingCode($api_host,$token,$user,$TrackNumber,$OrderItemIds,$ShippingProvider)
    {
        $now = new DateTime();

        $parameters = array(
            'UserID' => $user,
            'Action' => 'SetStatusToReadyToShip',
            'OrderItemIds'=>'['.$OrderItemIds.']',
            'DeliveryType' => 'dropship',
            'ShippingProvider' =>$ShippingProvider ,
            'TrackingNumber' => $TrackNumber,
            'Timestamp' => $now->format(DateTime::ISO8601),
          //  'UserID' => 'lazada.api@moonar.com',
            'Version' => '1.0',

        );
        ksort($parameters);
        $params = array();

        foreach ($parameters as $name => $value) {

            $params[] = rawurlencode($name) . '=' . rawurlencode($value);

        }
        $strToSign = implode('&', $params);

        $parameters['Signature'] = rawurlencode(hash_hmac('sha256', $strToSign, $token, false));

        $request = http_build_query($parameters);

       $info =$this->getCurlData($api_host.'/?'.$request);


        return $info;

    }

    public function getshippingcode($api_host,$token,$user,$OrderIdList)
    {
        $now = new DateTime();

        $parameters = array(
            'UserID' => $user,
            'Action' => 'GetMultipleOrderItems',
            'OrderIdList'=>'['.$OrderIdList.']',
            'Timestamp' => $now->format(DateTime::ISO8601),
            'Version' => '1.0',

        );
        ksort($parameters);
        $params = array();

        foreach ($parameters as $name => $value) {

            $params[] = rawurlencode($name) . '=' . rawurlencode($value);

        }
        $strToSign = implode('&', $params);

        $parameters['Signature'] = rawurlencode(hash_hmac('sha256', $strToSign, $token, false));

        $request = http_build_query($parameters);

        $info =$this->getCurlData($api_host.'/?'.$request);


        return $info;
    }



    //通过curl会话发送API请求获取数据
    public function getCurlData($queryString)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $queryString);

        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec($curl);

        curl_close($curl);

        return $content;
    }


    public function getLazadaOrderBystatus($api_host,$token,$user,$status,$limit, $offset)
    {
        $api_key = $token;
        $now = new DateTime();
        $after = new DateTime(date("Y-m-d",strtotime('-30 day')));
        $parameters = array(
            //'UserID' => 'lazada.api@moonar.com',
            'UserID' => $user,
            'Version' => '1.0',
            'Action' => 'GetOrders',
            'Timestamp' => $now->format(DateTime::ISO8601),
            //  'UpdatedAfter' => $after->format(DateTime::ISO8601),
            'Status' => $status,

            'CreatedAfter'=>$after->format(DateTime::ISO8601),
            'Limit'	=> $limit,
            'Offset'	=> $offset,
        );
        ksort($parameters);
        $params = array();
        foreach ($parameters as $name => $value)
        {
            $params[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        $strToSign = implode('&', $params);
        $parameters['Signature'] = rawurlencode(hash_hmac('sha256', $strToSign, $api_key, false));
        $request = http_build_query($parameters);
        $orders=$this->getCurlData($api_host.'/?'.$request);
        return $orders;

    }
}