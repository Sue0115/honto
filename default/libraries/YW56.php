<?php
/*
*
* 燕文API
* author: helen5106
* 2014-06-18
*/

function rand_string($len, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
    $string = '';
    for ($i = 0; $i < $len; $i++)
    {
        $pos = rand(0, strlen($chars)-1);
        $string .= $chars{$pos};
    }
    return $string;
}



class YW56 {

    public $userID;
    public $token;
    private $db;
    private $serverUrl;


    
    public function __construct() 
    {
    
        //$this->token = YANWEN_API_TOKEN;
        //$this->userID = YANWEN_API_USERID;
        $this->serverUrl = $this->userID == '100000'  ? 'http://online.yw56.com.cn/service_sandbox/'  : 'http://online.yw56.com.cn/service/' ;
		$this->CI = & get_instance();

    }
    
    /**	sendHttpRequest
     Sends a HTTP request to the server for this session
     Input:	$requestBody
     Output:	The HTTP Response as a String
     */
    public function sendHttpRequest($url, $post, $requestBody) 
    {
        //build eBay headers using variables passed via constructor
        $headers = $this->buildHeaders();
        //initialise a CURL session
        $connection = curl_init();
        
        curl_setopt($connection, CURLOPT_VERBOSE, 1);
        //set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $url);
        //stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        //set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

        if ($post) {
            curl_setopt($connection, CURLOPT_POST, 1);
            //set the XML body of the request
            curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
        }
        //set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($connection, CURLOPT_TIMEOUT, 30);
        //Send the Request
        $response = curl_exec($connection);
        
        $curl_errno = curl_errno($connection); 

        $curl_error = curl_error($connection); 
   
        curl_close($connection);
        
        if( $curl_errno > 0 ){ 
            return array('msg' => "请求错误: ($curl_errno): $curl_error\n", 'result' => false);
        }       

        return $response;
    }
    
    /**	buildHeaders
     Generates an array of string to be used as the headers for the HTTP request to eBay
     Output:	String Array of Headers applicable for this call
     */
    private function buildHeaders() 
    {
        $headers = array(
            'Authorization: basic ' . $this->token,
            'Content-Type: text/xml; charset=utf-8',
        );
        return $headers;
    }
    
    function createApiCallLog($callName, $file, $xml)
    {
        $diskAndRoot = '/data/yw56';
        $dir         = $diskAndRoot . '/' . $callName;
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
            chown($dir, 'apache');
        }
        $date    = date('Y-m-d');
        $dateDir = $dir . '/' . $date;
        if (!is_dir($dateDir)) {
            mkdir($dateDir, 0777);
            chown($dateDir, 'apache');
        }
        
        $xmlfile = $dateDir . '/' . $file;
        //delete
        if (file_exists($xmlfile)) unlink( $xmlfile );
        //write
        file_put_contents($xmlfile, $xml);
        //return
        return $xmlfile;
    } 
    

    
    function f_date($timestamp=0) 
    {

        if (!$timestamp) {
            $timestamp = time();
        }
        $date = date('Y-m-d\TH:i:s\.000\Z', $timestamp);
        return $date;
    }
    
    function get_country()
    {
        $country = $this->db->doSelect("SELECT DISTINCT country_en, country_cn FROM erp_country");
        $return = array();
        foreach($country as $c ) {
            $return[ strtoupper($c['country_en']) ] = $c['country_cn'];
        }
        return $return;
    }
    
    function get_product_info ($sku, $cangku) 
    {
        //$sql1 = "SELECT * FROM erp_products_data WHERE products_sku = '$sku' AND productsIsActive = 1 ";
        //$product1 = $this->db->getOne($sql1);
        $sql2 = "SELECT * FROM erp_products_data WHERE products_sku = '$sku' AND productsIsActive = 1 AND product_warehouse_id = '$cangku'";
        $product2 = $this->db->getOne($sql2);

        //$product = array_merge($product1, $product2);
        return $product2;
    }
    
    function filter_zipCode( $zipCode )
    {
        if ( $zipCode ) {
            $zipCode = preg_replace("'[\s]*'si", "", $zipCode);
            return strlen($zipCode) < 6 ? str_pad($zipCode, 6, '0') : substr($zipCode, 0, 6);
        }
        return '000000';
    }
    
    
    //新建快件
    function add_express ($ids, $cangku)
    {
        $return = '';
        if ( count($ids) ) {
            
            $country = $this->get_country();
            $xml = array();
            foreach ( $ids as $id ) {
                unset( $order );
                $sql = "
                    SELECT op.*, o.*, es.yw_channel FROM erp_orders_products op 
                    LEFT JOIN erp_orders o USING(erp_orders_id) 
                    LEFT JOIN erp_shipment es ON o.shipmentAutoMatched = es.shipmentID
                    WHERE o.erp_orders_id = '$id'";
                $order = $this->db->doSelect($sql);

                $quantity = 0;
                $weight = 0;
                $goods_str = '';
                $note_str = '';
                $memo_str = '';
                $requestXmlBody  = '';
                $cname = '';
                $ename = '';
                $shenbao = 0;
                foreach ( $order as $o ) {
                
                    //判断仓库
                    if ( $cangku == 'upyw' ) {
                        if ( $o['orders_warehouse_id'] != 1000 ) {
                            $return .= "$id : 非深圳仓，跳过<br />\n";  
                                continue;
                        }
                    } else if ( $cangku == 'upyw_yw' ) {
                        if ( $o['orders_warehouse_id'] != 1025 ) {
                            $return .= "$id : 非义乌仓，跳过<br />\n";   
                                continue;
                        }
                    }                
                    $product = $this->get_product_info( trim($o['orders_sku']), $o['orders_warehouse_id'] );              
                    $quantity += $o['item_count'];
                    $weight += $product['products_weight'] * $o['item_count'];
                    $goods_str .= $o['orders_item'] . "\r\n";
                    $location = empty($product['products_location']) ? '-' : $product['products_location'];
                    $note_str .= $o['orders_sku'] . ' * ' . $o['item_count'] . " (". $location . ")\r\n";//$product['products_name_cn'] . ' * '
                    $memo_str .= $o['orders_sku'] . ' * ' . $o['item_count'] . " (". $location . ")\r\n";
                    $cname = $product['products_name_cn'];
                    $ename = $product['products_declared_en'];//getEnNameNosku($o['orders_sku']);
                    $shenbao = $shenbao == 0 ? $product['products_declared_value'] : $shenbao;
                }
                reset( $order );
                $epcode = '';
                $userID = $this->userID;
                $wuliu = explode(',', $order[0]['yw_channel']);
                $channel = $wuliu[1];
                //$package = '无';
                $order_number = "$id" . rand_string(5);
                $send_date = $this->f_date( strtotime('now') );
                $receiver_name = trim($order[0]['buyer_name']);
                $receiver_phone = trim($order[0]['buyer_phone']);
                $receiver_email = trim($order[0]['buyer_email']);
                if ( empty($order[0]['buyer_country_code']) ) {
                    $receiver_country = $country[ strtoupper($order[0]['buyer_country']) ];
                } else if ( array_key_exists(strtoupper($order[0]['buyer_country_code']), $country) ) {
                    $receiver_country = $country[ strtoupper($order[0]['buyer_country_code']) ];
                } else {
                    $receiver_country = $country[ strtoupper($order[0]['buyer_country']) ];
                }
                //$receiver_country = empty($order[0]['buyer_country_code']) ? $country[ strtoupper($order[0]['buyer_country']) ] : $country[ strtoupper($order[0]['buyer_country_code']) ];
                $receiver_zipcode = $this->filter_zipCode( $order[0]['buyer_zip'] );
                
                $receiver_state = $order[0]['buyer_state'];
                
                $receiver_city = $order[0]['buyer_city'];
                $receiver_addr1 = trim($order[0]['buyer_address_1']);
                $receiver_addr2 = trim($order[0]['buyer_address_2']);
                
                if ( empty($receiver_state) ) $receiver_state = $receiver_city;
                if ( empty($receiver_addr1) ) {
                    $receiver_addr1 = $receiver_addr2;
                    $receiver_addr2 = '';
                }
                $address = $receiver_addr1 . ' ' . $receiver_addr2;
                
                $d_value = $shenbao;//申报价值
                //$d_currency = $order[0]['currency_type'];
                $d_currency = 'USD';
                $memo = $note_str;
                
                $nameEn = $ename . ' #' . $memo_str;
                //if ( strlen($nameEn) > 119 ) $nameEn = substr($nameEn, strlen($nameEn) - 119);
                
                $goods = "
        <NameCh>". $cname ."</NameCh>
        <NameEn>" . substr($nameEn, 0, 190) . "</NameEn>                    
        <MoreGoodsName>" . $cname . "</MoreGoodsName>                    
                ";

                //weight
                $weight = $weight * 1000;
                
                $requestXmlBody = <<<body
<?xml version="1.0" encoding="utf-8"?>
<ExpressType>
    <Epcode>$epcode</Epcode>
    <Userid>$userID</Userid>
    <Channel>$channel</Channel>
    <Package>无</Package>
    <UserOrderNumber>$order_number</UserOrderNumber>
    <SendDate>$send_date</SendDate>
    <Receiver>
        <Userid>$userID</Userid>
        <Name>$receiver_name</Name>
        <Phone>$receiver_phone</Phone>
        <Mobile>NULL</Mobile>
        <Email>$receiver_email</Email>
        <Company>NULL</Company>
        <Country>$receiver_country</Country>
        <Postcode>$receiver_zipcode</Postcode>
        <State>$receiver_state</State>
        <City>$receiver_city</City>
        <Address1>$address</Address1>
        <Address2>NULL</Address2>
    </Receiver>
    <Memo>$memo</Memo>
    <Quantity>$quantity</Quantity>
    <GoodsName>
        <Id>$order_number</Id>
        <Userid>$userID</Userid>
        $goods
        <Weight>$weight</Weight>
        <DeclaredValue>$d_value</DeclaredValue>
        <DeclaredCurrency>$d_currency</DeclaredCurrency>
    </GoodsName>
</ExpressType>
body;
                
                
                $xml[$id] = str_replace('&', '', $requestXmlBody);
                //echo $requestXmlBody;
            }

            if ( count( $xml ) ) {
            
                foreach( $xml as $_id => $_x ) {
                    
                    $url = $this->serverUrl . 'Users/'.$this->userID.'/Expresses';
                    $xmlFile = $this->createApiCallLog('add_express', $_id.'_request.xml', $_x);                    
                    
                    $result = $this->sendHttpRequest($url, 1, $_x);
                    
                    if ( is_array($result) ) {
                        if ( $result['result'] == false ) {
                            $return .= $result['msg'];
                            continue;
                        }
                    }
                    
                    
                    $result_xml = simplexml_load_string($result);
                    $this->createApiCallLog('add_express', $_id.'_response.xml', $result);
                    

                    list(, $success) = each($result_xml->xpath('//Success'));

                    if ( $success == 'true' ) {
                        //写入数据库
                        $epcodeNode = $result_xml->xpath('CreatedExpress/Epcode');
                        list(, $epcode) = each($epcodeNode);
                        $this->db->query("UPDATE erp_orders SET orders_shipping_code = '$epcode' WHERE erp_orders_id = '$_id'");//,orders_status = 3
                        $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('" . $_COOKIE['id'] . "','update','ordersManage','$_id','上传到燕文系统')");//，订单变为【已通过】
                        $return .= "$_id : OK<br />\n";
                    } else {
                        
                        list(, $errorMsg) = each($result_xml->xpath('//ReasonMessage'));

                        $return .= "$_id : " . $errorMsg . "<br />\n";
                    }
                }
                
            }
            

        }

        return $return;
    }
    
    function add_label ($orderInfo, $cangku, $size,$uid, $single = true) 
    {

        if ( $size && count($orderInfo) ) {

            $url = $this->serverUrl . 'Users/'.$this->userID.'/Expresses';
            $header = false;

            if ( $single ) {
                //判断仓库
                if ( $cangku == 'ywlabel' ) {
                    if ( $orderInfo['orders_warehouse_id'] != 1000 ) {
                        $return = $orderInfo['erp_orders_id']." : 非深圳仓，跳过<br />\n";
                        continue;
                    }
                } else if ( $cangku == 'ywlabel_yw' ) {
                    if ( $orderInfo['orders_warehouse_id'] != 1025 ) {
                        $return = $orderInfo['erp_orders_id']." : 非义乌仓，跳过<br />\n";
                        continue;
                    }
                }
                
                $epcode = $orderInfo['orders_shipping_code'];
                if ( $epcode ) {
                    $url .= "/$epcode/{$size}Label";
                   
                    $result = $this->sendHttpRequest($url, 0, '');

                    $newfile='attachments/yanwen'.'/'.$epcode .'.pdf';
                    if ( file_exists($newfile) ) unlink( $newfile );
                    $write = @fopen($newfile ,"w");
                    fwrite($write, $result);
                    fclose($write); 
                    $header = true;
                    $this->CI->db->query("UPDATE erp_orders SET orders_status = 4, orders_print_time = NOW() WHERE erp_orders_id = ".$orderInfo['erp_orders_id']." AND orders_status != 5");
                    $this->CI->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('" . $uid . "','update','ordersManage','".$orderInfo['erp_orders_id']."','打印订单')");

                }
            } else {
                $epcodes = array();
                $mailnum = array();
                foreach( $id as $oid ) {
                    $order = $this->db->getOne("SELECT * FROM erp_orders WHERE erp_orders_id = '$oid'");
                    
                    //判断仓库
                    if ( $cangku == 'ywlabel' ) {
                        if ( $order['orders_warehouse_id'] != 1000 ) {
                            $return .= "$oid : 非深圳仓，跳过<br />\n";
                            continue;
                        }
                    } else if ( $cangku == 'ywlabel_yw' ) {
                        if ( $order['orders_warehouse_id'] != 1025 ) {
                            $return .= "$oid : 非义乌仓，跳过<br />\n";
                            continue;
                        }
                    }
                
                    if ( $order['orders_shipping_code'] ) {
                        $epcodes[] = $order['orders_shipping_code'];
                        $mailnum[$oid] = $order['orders_shipping_code'];
                    }
                }

                if ( count($epcodes) ) {
                    $url .= "/{$size}Label";
                    $codes = implode(',', $epcodes);
                    $requestXmlBody = <<<body
<?xml version="1.0" encoding="utf-8"?>
<string>$codes</string>
body;
                    $result = $this->sendHttpRequest($url, 1, $requestXmlBody);

                    $newfile = '/var/www/html/erp/download/ywlabel/' . date('YmdHis') . '.pdf';
                    if ( file_exists($newfile) ) unlink( $newfile );
                    $write = @fopen($newfile,"w");
                    fwrite($write, $result);
                    fclose($write);
                    $header = true;
                    if(!empty($uid)){
	                    foreach( $mailnum as $_id => $code ) {
	                        $this->db->query("UPDATE erp_orders SET orders_status = 4, orders_print_time = NOW() WHERE erp_orders_id = '$_id' AND orders_status != 5");
	                        $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('" . $uid . "','update','ordersManage','$_id','打印订单')");                    
	                    }	
                    }
                    
                }
                
            }
            
            if ( $header ) {
                header('Content-type: application/pdf');  
                header('Content-Disposition: attachment; filename="'.basename($newfile).'"');  
                readfile($newfile);
            } else {
                echo '<script>alert("尚未打印，有些可能没有追踪码。");window.opener=null;window.close();</script>';
            }
            
        }
    }


     function add_label_to_jpg ($orderInfo, $cangku, $label_size,$uid, $single = true) 
    {

        $size = $label_size[1];

        $newfile = '';

        if ( $size && count($orderInfo) ) {

            $url = $this->serverUrl . 'Users/'.$this->userID.'/Expresses';
           
            if ( $single ) {
                //判断仓库
                if ( $cangku == 'ywlabel' ) {
                    if ( $orderInfo['orders_warehouse_id'] != 1000 ) {
                        $return = $orderInfo['erp_orders_id']." : 非深圳仓，跳过<br />\n";
                        continue;
                    }
                } else if ( $cangku == 'ywlabel_yw' ) {
                    if ( $orderInfo['orders_warehouse_id'] != 1025 ) {
                        $return = $orderInfo['erp_orders_id']." : 非义乌仓，跳过<br />\n";
                        continue;
                    }
                }
                
                $epcode = $orderInfo['orders_shipping_code'];
                if ( $epcode ) {
                    $url .= "/$epcode/{$size}Label";
                   
                    $result = $this->sendHttpRequest($url, 0, '');

                    $newfile='attachments/yanwen'.'/'.$epcode .'.pdf';
                    if ( file_exists($newfile) ) unlink( $newfile );
                    $write = @fopen($newfile ,"w");
                    fwrite($write, $result);
                    fclose($write);

                    $image_file =  'attachments/yanwen/'.$epcode .'.jpg';

                    pdf_jpg($newfile,$image_file);

                }
            } 
            
           
        }


        $result = array();

        $num = 1;
        if(isset($label_size[2]) && $label_size[2]>1){
            $num = $label_size[2];
        }

        if(!empty($newfile) && file_exists($newfile)){
            $image = 'attachments/yanwen/'.$epcode;
            for ($i=0; $i <$num ; $i++) { 
                if($num == 1){
                   $result[] = $image.'.jpg';
                   break; 
                }else{
                    $result[] = $image.'-'.$i.'.jpg';
                }
            }
        }

        return $result;
    }
    
    
}

?>