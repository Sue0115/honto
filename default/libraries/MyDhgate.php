<?php

class MyDhgate extends  MY_Model{
     public $url='http://api.dhgate.com/dop/router';//正式环境
 //   public $url='http://sandbox.api.dhgate.com/dop/router';//沙箱环境(上线删除)    
    public $version='2.0';//接口版本号(string)
        /**
         * 获取订单数据
         * @param  $accessToken
         * @param  $page
         * @param  $pagesize
         * @return array
         */
        public function getDhgateOrder($accessToken,$page,$pagesize){
            $apiParameters = array(               
                'access_token'       => $accessToken,                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.order.list.get',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,//请求时间戳(毫秒)
                'v'                  => $this->version,                
                'pageNo'             => $page,     //页码
                'pageSize'           => $pagesize, //每页记录数
                'querytimeType'      => '2',//查询类型，1是下单时间，2是付款时间
                'orderStatus'        => '103001',//等待发货,
                'startDate'          => date('Y-m-d H:i:s',strtotime('-5 day')),  //查询付款开始时间(string)
                'endDate'            => date('Y-m-d H:i:s'),
            );   

              $request = http_build_query($apiParameters);
              $orders=$this->getCurlData($this->url,$request);
            return $orders;
    
        }
        
        /**
         * 获取订单详情
         * @return array
         */
        public function getOrderDetailInfo($accessToken,$orderNo){
            $apiParameters = array(
                'access_token'       => $accessToken,                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.order.get',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,//请求时间戳(毫秒)
                'v'                  => $this->version,
                'orderNo'            => $orderNo,       //订单号 
            );
            
            $request = http_build_query($apiParameters);
            $orderDetails=$this->getCurlData($this->url,$request);
            return $orderDetails;
        }
        
        /**
         * 获取订单产品数据
         * @param  $accessToken
         * @param  $orderNo
         * @return array
         */
        public function getDhgateOrderDetailInfo($accessToken,$orderNo){
            $apiParameters = array(
                'access_token'       => $accessToken,                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.order.product.get',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,
                'v'                  => $this->version,
                'orderNo'            => $orderNo,       //订单号 
            );
            $request = http_build_query($apiParameters);
            $orderDetailInfo=$this->getCurlData($this->url,$request);
            return $orderDetailInfo;
        
        }
        
        
        /**
         * 上传运单号，标记发货
         * @param  $accessToken
         * @param  $orderInfo
         * @return array
         */
        public function uploadTrackNo($accessToken,$orderInfo=array()){
            $apiParameters = array(
                'access_token'       => $accessToken,                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.order.delivery.save',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,
                'v'                  => $this->version,
                'deliveryNo'         => $orderInfo['trankNo'],//运单号
//                 'deliveryRemark'     => $orderInfo['orders_remark'],//运单备注(不允许有中文)
                'deliveryState'      => $orderInfo['deliveryState'],//发货状态(1全部发货，2部分发货)
                'orderNo'            => $orderInfo['rfxNo'],//订单号(示例1330312162)
                'shippingType'       => $orderInfo['shippingType'],//运输方式
                             
            );
             $request = http_build_query($apiParameters);
            $orderDetailInfo=$this->getCurlData($this->url,$request);
            return $orderDetailInfo;
        
        }
    /**
     * 获取站内信留言主题ID
     * @return unknown
     */
        public function getMsgIdByOrderNo($accessToken,$orderNo,$page,$pagesize){
            $apiParameters = array(
                'access_token'       => $accessToken,                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.message.list',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,
                'v'                  => $this->version,
                'beforeDay'          => '5',//查询的时间范围,当前日期的前几天,单位天，1-366指间
                'msgType'            => '001,002,003',//站内信展现类型(001:买卖家消息-询盘,002:买卖家消息-订单,003:买卖家消息-其它,)
                'searchKey'          => $orderNo,//输入的搜索关键字,暂时只支持订单号
                'pageNo'             => $page,     //页码
                'pageSize'           => $pagesize, //每页记录数
                 
            );
            $request = http_build_query($apiParameters);
            $orderDetailInfo=$this->getCurlData($this->url,$request);
            return $orderDetailInfo;
        }
        
        /**
         * 根据站内信id获取内容(订单留言)
         * @param unknown $accessToken
         * @param unknown $msgId
         * @return unknown
         */
        public function getMessageByMsgId($accessToken,$msgId){
            $apiParameters = array(
                'access_token'       => $accessToken,                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.message.get',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,
                'v'                  => $this->version,
                'msgId'              => $msgId,//输入的搜索关键字,暂时只支持订单号                
            );
            $request = http_build_query($apiParameters);
            $orderDetailInfo=$this->getCurlData($this->url,$request);
            return $orderDetailInfo;
        }
//         /**
//          * 获取access_token,通过Authorization Code获取Access Token
//          */
//         public function getAccessToken($authorizationCode,$appkey,$appSecret){
//            $url='https://secure.dhgate.com/dop/oauth2/authorize';            
//             $accessTokenApiParameters=array(
//                 'grant_type'    => 'authorization_code',
//                 'code'          => $authorizationCode,//$_request['code']
//                 'client_id'     => $appkey,
//                 'client_secret' => $appSecret,
//                 'redirect_uri'  => 'http://www.slme.com/admin/auto/auto_dhgate/getRedirectUrl',           
//             );

//             return $this->getCurlData($url,$accessTokenApiParameters);
            //return $result->access_token;//$_REQUEST['access_token'];
//         }
        
        /**
         * 获取access_token,通过帐号和密码获取Access Token
         */
        public function getAccessTokenWithAccountPassword($userInfo){
            $url='https://secure.dhgate.com/dop/oauth2/access_token';
            $accessTokenApiParameters=array(
                'grant_type'    => 'password',
                'username'      => trim($userInfo['sales_account']),
                'password'      => trim(base64_decode($userInfo['pw'])),
                'client_id'     => trim($userInfo['app_key']),
                'client_secret' => trim($userInfo['client_secret']),
                'scope'         => 'basic',
            );
            $request = http_build_query($accessTokenApiParameters);
            $result= $this->getCurlAccessToken($url.'?'.$request);
            return json_decode($result);
        }
        
        /**
         * 根据refresh_token 刷新Access_token
         * @param unknown $userInfo
         */
        public function refreshAccessToken($userInfo){
            $url='https://secure.dhgate.com/dop/oauth2/access_token';
            $accessTokenApiParameters=array(
                'grant_type'    => 'refresh_token',
                'refresh_token' => trim($userInfo['refresh_token']),
                'client_id'     => trim($userInfo['app_key']),
                'client_secret' => trim($userInfo['client_secret']),
                'scope'         => 'basic',
            );
            $request = http_build_query($accessTokenApiParameters);
            $result= $this->getCurlAccessToken($url.'?'.$request);
            return json_decode($result);
        }
        
        /**
         * 获取物流运输方式
         * @return array
         */
       public function shippingTypeList($userInfo){
           $apiParameters = array(               
                'access_token'       => trim($userInfo['access_token']),                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.shipping.typelist',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,//请求时间戳(毫秒)
                'v'                  => $this->version,                
            );   

              $request = http_build_query($apiParameters);
              $shippingList=$this->getCurlData($this->url,$request);
            return $shippingList;
       }
       
     
    
        //通过curl会话发送API请求获取数据(POST提交)
        public function getCurlData($url,$data){
            $ch = curl_init();//初始化curl
            curl_setopt($ch, CURLOPT_URL, $url);//设置请求地址
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回值
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);//设置请求方式POST
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//请求所带变量数据
            $output = curl_exec($ch);//执行获取返回数据，返回的数据建议json_encode($return_data);
            curl_close($ch);
          return json_decode($output);                      
        }
        
        
        //通过curl会话发送API请求获取数据
        public function getCurlAccessToken($queryString){
            $curl = curl_init();        
            curl_setopt($curl, CURLOPT_URL, $queryString);        
            curl_setopt($curl, CURLOPT_HEADER, 0);       
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);        
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
            $content = curl_exec($curl);       
            curl_close($curl);       
            return $content;
        }
        
        // 解析dh SKU信息
        function resetTransactionDetail($array) {
            $newArray = array();
            if ($array) {
                foreach ($array as $row) {
                    //1.先去掉'+'
                    $tmpSkuArray = explode('+', $row['skuCode']);
                    $tmpCount    = count($tmpSkuArray); //SKU种类总数
                    foreach ($tmpSkuArray as $tmpSku) {
                        //先用一个数组保存最原始的一维数组信息
                        $data = $row;
                        $data['skuCode'] = $tmpSku; //SKU信息暂时已变更，重新赋值下就行
                        $data['itemPrice'] = round($data['itemPrice'] / $tmpCount, 2); //组合SKU的单价平均处理
        
                        //2.再去掉‘*’,可以直接取星号之后的部分
                        $tmp = explode('*', $tmpSku);
                        $tmpSku = trim(array_pop($tmp));
        
                        //3.忽略中括号内的信息
                        if (stripos($tmpSku, '[') !== false) {
                            $tmpSku = preg_replace('/\[.*\]/', '', $tmpSku);
                        }
        
                        //4.处理小括号及其单价数量
                        if (stripos($tmpSku, '(') !== false) {
                            $sku = trim(getStringBetween($tmpSku, '', '('));
                            $qty = trim(getStringBetween($tmpSku, '(', ')'));
                            $data['skuCode'] = $sku;
                            $data['count'] = $qty * $data['count'];
                            $data['itemPrice'] = round($data['itemPrice'] / $qty, 2);
                            $newArray[] = $data;
                        }else {
                            $data['skuCode'] = trim($tmpSku);
                            $newArray[] = $data;
                        }
                    }
                }
            }
            return $newArray;
        }
        
        function getStringBetween($string, $start = '', $end = ''){ //取从某个字符首次出现的位置开始到另一字符首次出现的位置之间的字符串        
            //$s = ($start != '') ? stripos($string,$start)+1 : 0 ;$e = ($end != '' ) ? stripos($string,$end) : strlen($string) ;
            //if($s <= $e){return substr($string,$s,$e-$s);}else{return false;}
            $s = ($start != '') ? stripos($string, $start) : 0;
            $e = ($end != '') ? stripos($string, $end) : strlen($string);
            if ($s <= $e) {
                $string = substr($string, $s, $e - $s);
                return str_replace($start, '', $string);
            } else {
                return false;
            }
        }
        
        
        /**
         * 获取买家确认收货时间
         * @param  $accessToken
         * @param  $page
         * @param  $pagesize
         * @return array
         */
        public function getBuyersReceivingTime($accessToken,$page,$pagesize){
            $apiParameters = array(
                'access_token'       => $accessToken,                                //请求令牌,需要提前通过OAuth2授权接口获取令牌
                'method'             => 'dh.order.list.get',
                'timestamp'          => strtotime(date('Y-m-d H:i:s'))*1000,//请求时间戳(毫秒)
                'v'                  => $this->version,
                'pageNo'             => $page,     //页码
                'pageSize'           => $pagesize, //每页记录数
                'querytimeType'      => '2',//查询类型，1是下单时间，2是付款时间
                'orderStatus'        => '102111',//交易成功,
                'startDate'          => date('Y-m-d H:i:s',strtotime('-60 day')),  //查询付款开始时间(string)
                'endDate'            => date('Y-m-d H:i:s'),
            );
        
            $request = http_build_query($apiParameters);
            $orders=$this->getCurlData($this->url,$request);
            return $orders;
        
        }
        
        
    }
