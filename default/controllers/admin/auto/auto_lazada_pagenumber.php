<?php
/**
 */
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");

class Auto_lazada_pagenumber extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->library('MyLazada');

        $this->load->model(array(
                'system_model', 'order/orders_model', 'order/orders_products_model', 
                'lazada_token_model', 'lazada_logistics_service_model','lazada_pagenumber_model'
        )
        	
        );
        $this->lazada = new MyLazada();
    }


    public function auto_get_pagenumber()
    {
       
        $token_info = $this->lazada_token_model->getAll2Array();
        
        $host_array = array(
        				'https://sellercenter-api.lazada.co.id','https://sellercenter-api.lazada.com.my',
        				'https://sellercenter-api.lazada.com.ph','https://sellercenter-api.lazada.co.th',
        				'https://sellercenter-api.lazada.sg'
        );//跑印尼
        
        foreach ($token_info as $token) {
        	
            if (!in_array($token['api_host'],$host_array)) {
                continue; //除了My站点，其他都要获取pagenumber
            }

            $new_option = array();
            
		    $where_in= array(1,3,4);
		   
            $new_option['where']['orders_type'] =15;
            $new_option['where']['ebayStatusIsMarked'] =1;
            $new_option['where']['orders_is_join'] = 0;
            $new_option['where']['ordersID'] = null;
            $new_option['where']['sales_account']=$token['sales_account'];
            $new_option['where']['orders_export_time >']='2015-10-03';
			$new_option['where_in']['orders_status'] = $where_in;
			$new_option['where']['orders_shipping_code']='';
			
            $new_option['limit'] = 50;
            
            $join=array();
         
            $join[] = array( 'erp_lazada_pagenumber as p', "p.ordersID={$this->orders_model->_table}.erp_orders_id");

            $new_option['select'] = array('p.*', "{$this->orders_model->_table}.*");
            
            $new_option['join'] = $join;

            $result_order = $this->orders_model->getAll2Array($new_option);

            if(empty($result_order))
            {
                continue;
            }

            $need_get_shippingcode_array=array();

            foreach($result_order as $v)
            {
                $need_get_shippingcode_array[] = $v['ebay_orders_id'];
            }

       
           $OrderIdList = implode(",", $need_get_shippingcode_array);
    
           $Xml_data = $this->lazada->getshippingcode($token['api_host'], $token['Key'], $token['lazada_user_id'], $OrderIdList);
           
           $orders_data = $this->XmlToArray($Xml_data);
           
           
           
		   print_r($orders_data);
		   
           if (isset($orders_data['Body']['Orders']['Order'])) {//获取成功

                    $last_orders_data = $orders_data['Body']['Orders']['Order'];
                    
		 			$need_get_shippingcode_array = array_unique($need_get_shippingcode_array);
		 			
                    $count_order = count($need_get_shippingcode_array);//传递的外单号的个数，1个和多个数据结构不一样

                    if($count_order>1){//获取多订单情况下的pagenumber
                    	
                       foreach ($last_orders_data as $order){
                      	
                         $this->tabledeal($order,$token);
                    
                       }
                       
                    }else{
                        $this->tabledeal($last_orders_data,$token);
                    }
                    
                   
                }

        }

    }
    
    //手动传递一个内单号获取pagenumber
    public function get_one_pagenumber(){
    	
       $order_array = array(
			14303472,
			14433637,
			14431871,
			14433639
       );
       
       foreach($order_array as $orderID){
       	
	       $orderInfo = array();
	       
	       $orderInfo = $this->orders_model->get_orders_info($orderID);
	   
	      // $sales_account = array('99706454@qq.com_ID','lixuanpengwu@126.com_ID');
//	       
//	       if(empty($orderInfo)){
//	         echo $orderID.'的订单号不存在';
//	         exit;
//	       }
//	       	
//	       if($orderInfo['ebayStatusIsMarked']==0){
//	         echo $orderID.'的订单号未标记发货';
//	         continue;
//	       }
//	       
//	       if($orderInfo['orders_shipping_code']!=''){
//	         echo $orderID.'的订单未获取追踪号';
//	         exit;
//	       }
//	       
//	       if(!in_array($orderInfo['sales_account'],$sales_account)){
//	         echo '该账号下的订单不允许获取pagenumber';
//	         continue;
//	       }
//	       
	       //获取token的信息
	       $op = array();
	       $op['where'] = array('sales_account'=>$orderInfo['sales_account']);
	       $token = $this->lazada_token_model->getOne($op,true);
	       
	       $OrderIdList = $orderInfo['ebay_orders_id'];
    
           $Xml_data = $this->lazada->getshippingcode($token['api_host'], $token['Key'], $token['lazada_user_id'], $OrderIdList);
           
           $orders_data = $this->XmlToArray($Xml_data);
print_r($orders_data);
           if (isset($orders_data['Body']['Orders']['Order'])) {//获取成功

                    $last_orders_data = $orders_data['Body']['Orders']['Order'];

                    $this->tabledeal2($last_orders_data,$token);
                    
           }
       }
    	
       
    }
    
    
    
	public function tabledeal2($order,$token){
    	
     					$pagenumber = array();
     					
     					$order_shipping_code = array();
                         if(isset($order['OrderItems']['OrderItem'][0])) //多SKU的
                         {
                         	
                            foreach ($order['OrderItems']['OrderItem'] as $items)//循环订单产品
                            {
                            	
                            	   if(is_array($items['PackageId'])){
                            	   	echo '未标记<br/>';
                            	     continue;
                            	   }
                            	
                                   if(!empty($items['PackageId']))
                                   {
                                       $pagenumber[$order['OrderNumber']][$items['OrderItemId']] = $items['PackageId'];
                                   }
                           		   if(!empty($items['TrackingCode']))
                                   {
                                       $order_shipping_code[$order['OrderNumber']][$items['OrderItemId']] = $items['TrackingCode'];
                                   }
                                   
                            }
                         }
                         else
                         {
                         	
                         		if(is_array($order['OrderItems']['OrderItem']['PackageId'])){
                         			echo '未标记<br/>';
                            	     return false;
                            	}
                            	
                         	  
                            $pagenumber[$order['OrderNumber']][$order['OrderItems']['OrderItem']['OrderItemId']]=$order['OrderItems']['OrderItem']['PackageId'];
                            $order_shipping_code[$order['OrderNumber']][$order['OrderItems']['OrderItem']['OrderItemId']]=$order['OrderItems']['OrderItem']['TrackingCode'];
                         }
                         print_r($pagenumber);
                         print_r($order_shipping_code);
                      
                         //根据订单产品表里的orderlineitemid和买家ID查找订单号，即OrderItemId，查找内单号
                         /**
                          * Array
							(
							    [353894796] => Array
							        (
							            [5975883] => MPDS-353894796-5785
							            [5975884] => MPDS-353894796-5785
							        )
							
							)
							353894796订单的买家ID；5975883订单产品表的orderlineitemid；MPDS-353894796-5785获取的pagenumber
                          */
                         foreach($pagenumber as $key => $v){
                         	
                         	foreach($v as $k => $va){
                         		
                         	    $options = array();
	                            $join=array();
	                            $where = array();
	                            $where['orders_is_join'] = 0;
	                            $where['sales_account'] = $token['sales_account'];
	                            $where['orders_type'] = 15;
	                            $where['buyer_id'] = $key;
	                            $where['p.orderlineitemid'] = $k;
	                            $join[] = array( 'erp_orders_products as p', "p.erp_orders_id={$this->orders_model->_table}.erp_orders_id");
	                            $options['where'] = $where;
	                            $options['select'] = array('p.orderlineitemid', "{$this->orders_model->_table}.erp_orders_id");
	                            $options['join'] = $join;
	                            $orders_data_result = $this->orders_model->getAll2array($options);
	                          
	                            if(empty($orders_data_result)){
	                              continue;
	                            }
                         	    if(count($orders_data_result)>1){//说明有重单出现
                         	    	echo $orders_data_result[0]['erp_orders_id'].'可能是重单的状况<br/>';
	                              continue;
	                            }
	                            
	                            //更新订单表的追踪号
	                            $up = array();
	                            $op = array();
	                            $up['orders_shipping_code'] = $order_shipping_code[$key][$k];
	                            $op['where'] = array('erp_orders_id'=>$orders_data_result[0]['erp_orders_id']);
	                            $this->orders_model->update($up,$op);
	                            
	                            //插入erp_lazada_pagenumber
	                            $in = array();
	                            $in['ordersID'] = $orders_data_result[0]['erp_orders_id'];
	                            $in['orderlineitemid'] = $orders_data_result[0]['orderlineitemid'];
	                            $in['pagenumber'] = $va;
	                            $in['createTime'] = date('Y-m-d H:i:s');
	                            //插入之前先判断packagenumber是否存在，存在的话判断存在的订单号是不是母单，母单的话要插入，否则不插入
	                            $res = $this->lazada_pagenumber_model->getInfoByOrderID(array('pagenumber'=>$va));
	              
	                            if(!empty($res)){
	                            	
	                              foreach($res as $r){
	                                $order_array = array();
	                                $order_array = $this->orders_model->get_orders_info($r['ordersID']);
	                         echo $this->db->last_query();
	                                if($order_array['orders_is_join']==0){
	                                  echo '订单号'.$r['ordersID'].'下packagenumber'.$va.'已经存在<br/>';
	                                  continue;
	                                }else{
	                                  $this->lazada_pagenumber_model->add($in);
	                                }
	                              }
	                             
	                              continue;
	                              
	                            }
	                            $this->lazada_pagenumber_model->add($in);
	                            
	                            echo '订单号'.$in['ordersID'].'下skuID号'.$in['orderlineitemid'].'插入成功<br/>';
                         	}
                            
                         }
                         
                        
                         
    }

    //把插入表的操作封装成方法，方便调用
    public function tabledeal($order,$token){
    	
     					$pagenumber = array();
     					
     					$order_shipping_code = array();
                         if(isset($order['OrderItems']['OrderItem'][0])) //多SKU的
                         {
                         	
                            foreach ($order['OrderItems']['OrderItem'] as $items)//循环订单产品
                            {
                            	  
                                   if(!empty($items['PackageId']))
                                   {
                                       $pagenumber[$order['OrderNumber']][$items['OrderItemId']] = $items['PackageId'];
                                   }
                           		   if(!empty($items['TrackingCode']))
                                   {
                                       $order_shipping_code[$order['OrderNumber']][$items['OrderItemId']] = $items['TrackingCode'];
                                   }
                                   
                            }
                         }
                         else
                         {
                         	 
                            $pagenumber[$order['OrderNumber']][$order['OrderItems']['OrderItem']['OrderItemId']]=$order['OrderItems']['OrderItem']['PackageId'];
                            $order_shipping_code[$order['OrderNumber']][$order['OrderItems']['OrderItem']['OrderItemId']]=$order['OrderItems']['OrderItem']['TrackingCode'];
                         }
                         print_r($pagenumber);
                         print_r($order_shipping_code);
                      
                         //根据订单产品表里的orderlineitemid和买家ID查找订单号，即OrderItemId，查找内单号
                         /**
                          * Array
							(
							    [353894796] => Array
							        (
							            [5975883] => MPDS-353894796-5785
							            [5975884] => MPDS-353894796-5785
							        )
							
							)
							353894796订单的买家ID；5975883订单产品表的orderlineitemid；MPDS-353894796-5785获取的pagenumber
                          */
                         foreach($pagenumber as $key => $v){
                         	
                         	foreach($v as $k => $va){
                         		
                         	    $options = array();
	                            $join=array();
	                            $where = array();
	                            $where['orders_is_join'] = 0;
	                            $where['sales_account'] = $token['sales_account'];
	                            $where['orders_type'] = 15;
	                            $where['buyer_id'] = $key;
	                            $where['p.orderlineitemid'] = $k;
	                            $join[] = array( 'erp_orders_products as p', "p.erp_orders_id={$this->orders_model->_table}.erp_orders_id");
	                            $options['where'] = $where;
	                            $options['select'] = array('p.orderlineitemid', "{$this->orders_model->_table}.erp_orders_id");
	                            $options['join'] = $join;
	                            $orders_data_result = $this->orders_model->getAll2array($options);
	                          
	                            if(empty($orders_data_result)){
	                              continue;
	                            }
                         	    if(count($orders_data_result)>1){//说明有重单出现
                         	    	echo $orders_data_result[0]['erp_orders_id'].'可能是重单的状况<br/>';
	                              continue;
	                            }
	                            
	                            //更新订单表的追踪号
	                            $up = array();
	                            $op = array();
	                            $up['orders_shipping_code'] = $order_shipping_code[$key][$k];
	                            $op['where'] = array('erp_orders_id'=>$orders_data_result[0]['erp_orders_id']);
	                            $this->orders_model->update($up,$op);
	                            
	                            //插入erp_lazada_pagenumber
	                            $in = array();
	                            $in['ordersID'] = $orders_data_result[0]['erp_orders_id'];
	                            $in['orderlineitemid'] = $orders_data_result[0]['orderlineitemid'];
	                            $in['pagenumber'] = $va;
	                            $in['createTime'] = date('Y-m-d H:i:s');
	                            //插入之前先判断packagenumber是否存在，存在的话判断存在的订单号是不是母单，母单的话要插入，否则不插入
	                            $res = $this->lazada_pagenumber_model->getInfoByOrderID(array('pagenumber'=>$va));
	                          
	                            if(!empty($res)){
	                              
	                              if(count($res)>1){
	                                echo 'packagenumber'.$va.'已经存在两个以上，母单和子单一条，另一个子单不允许插入<br/>';
	                                continue;
	                              }
	                            	
	                              foreach($res as $r){
	                                $order_array = array();
	                                $order_array = $this->orders_model->get_orders_info($r['erp_orders_id']);
	                                if($order_array['orders_is_join']==0){
	                                  echo '订单号'.$in['ordersID'].'下packagenumber'.$va.'已经存在<br/>';
	                                  continue;
	                                }else{
	                                  $this->lazada_pagenumber_model->add($in);
	                                }
	                              }
	                             
	                              continue;
	                              
	                            }
	                            $this->lazada_pagenumber_model->add($in);
	                            
	                            echo '订单号'.$in['ordersID'].'下skuID号'.$in['orderlineitemid'].'插入成功<br/>';
                         	}
                            
                         }
                         
                        
                         
    }

    public function XmlToArray($xml)
    {
        $array = (array)(simplexml_load_string($xml));
        foreach ($array as $key => $item) {

            $array[$key] = $this->struct_to_array((array)$item);
        }
        return $array;
    }

    public function struct_to_array($item)
    {
        if (!is_string($item)) {

            $item = (array)$item;
            foreach ($item as $key => $val) {

                $item[$key] = $this->struct_to_array($val);
            }
        }
        return $item;
    }


    public function checkAPICount($string,$string2){

        $mailArr=array('lilifeng@moonarstore.com','liufei@moonarstore.com','yangleen@moonarstore.com');

            $this->load->library('mail/phpmailer');


            $mail = new PHPMailer();

            $mail->IsSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.exmail.qq.com';                   // Specify main and backup server
            $mail->Port = 25;  //:465

            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'stockupdate@moonarstore.com';                            // SMTP username
            $mail->Password = 'salamoer1234';                           // SMTP password
            // 	$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted

            $mail->CharSet ="UTF-8";
            $mail->From = 'stockupdate@moonarstore.com';
            $mail->FromName = $string2;

            foreach($mailArr as $m){
                $mail->AddAddress($m);
            }


            $mail->IsHTML(true);                                  // Set email format to HTML
            $mail->Subject = $string2;
            $mail->Body = $string;

            //判断邮件是否发送成功
            $isSend = $mail->Send();
            echo $isSend."<br/>";

        }

    public function countOrderByAccount()
    {

        $result = $this->lazada_logistics_service_model->countOrderByAccount();
        if(!empty($result))
        {
            $string ='';
            foreach($result as $re)
            {
                $string = $string.'账号:'.$re['sales_account'].' 今日订单导入量 '.$re['num'].'  ';
            }

            $today = date('Y-m-d');
            $this->checkAPICount($string,$today.'订单导入情况');
        }
    }


}