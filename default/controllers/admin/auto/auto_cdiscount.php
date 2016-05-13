<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
/**
 * Created by PhpStorm.
 * User: Administrator
 * DateTime: 2015-08-18  10:00:00 
 */
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auto_cdiscount extends MY_Controller{  
    public $platform          = 'cdiscount';
    public $api_time          = '';
    const  API_START          = 1;
    const  API_END            = 2;
    const  EMPTY_END          = 3;
    function __construct(){
        parent::__construct();
    
        $this->load->library('MyCdiscount');
//         $this->load->library('simple_html_dom');
    
        $this->load->model(array(
            'system_model','order/orders_model','order/order_api_log_model','order/orders_products_model', 'dhgate_token_model','dhgate_logistics_service_model',
            'cdiscount/cdiscount_token_model','shipment_model','cdiscount/cdiscount_products_model'
        )
        );
        $this->cdiscount= new MyCdiscount();
    }
    
    /**
     * 授权获取access_token,refresh_token并保存
     */
    public function getTokenToSave(){
        $tokenInfo = $this->cdiscount_token_model->getAll2Array();//获取平台帐号
        foreach($tokenInfo as $key=>$val){//检测token是否已过期
            $tokenId = $this->cdiscount->getTokenWithAccountPassword($val);
            $updateOptions = array();
            $updateData = array();
            $updateOptions['where']['id'] = $val['id'];
            $updateData['token_id']  = "$tokenId";
            $updateData['modify_time'] = date('Y-m-d H:i:s');
            $updateData['expires_in'] = strtotime(date('Y-m-d',strtotime('+46 hour')));
            $this->cdiscount_token_model->update($updateData,$updateOptions);
        }
               
    }
    
    public function getOrdersList(){echo "start";
        $tokenInfo = $this->cdiscount_token_model->getAll2Array();//获取平台帐号
        foreach($tokenInfo as $key=>$val){//检测token是否已过期
            if(empty($val['expires_in']) || strtotime('+3 days')>=$val['expires_in']){
                $this->getTokenToSave();//获取或更新token
            }
        }
        //echo date('Y-m-d', $val['expires_in']).'<br>';
        //echo '<meta charset="utf-8" /><br>------<pre>File: '.__FILE__.' Line:'.__LINE__.' output:';print_r($tokenInfo);
        foreach($tokenInfo as $key =>$val){
               $OrderList = $this->cdiscount->getOrderLists($val['token_id']);
               //echo '<meta charset="utf-8" /><br>------<pre>File: '.__FILE__.' Line:'.__LINE__.' output:';print_r($OrderList);exit;
               if(isset($OrderList[0]->GetOrderListResponse->GetOrderListResult->OrderList->Order) && !is_array($OrderList[0]->GetOrderListResponse->GetOrderListResult->OrderList->Order)){
                   
                   $this->api_time =  date('Y-m-d  H:i:s');
                   $apiInsertData = array();
                   $apiInsertData = array(
                       'api_time'  => date('Y-m-d'),
                       'platform'  => $this->platform,//cd,
                       'api_name'  => 'cd_get_orders_'.date('Y-m-d'),
                       'start_time'=> $this->api_time,
                       'status'    => self::API_START ,
                   );  
                   //事务开启
                   $this->db->trans_begin();
                   $this->order_api_log_model->dhgateOrderApiLog($apiInsertData);//添加日志                  
                   //获取汇率
                   $system_value = $this->system_model->get_current_value()->system_value;                   
  //                   $currency_arr = explode(chr(13), $system_value);
                  $currency_arr =preg_split("/[\r\n]+/",$system_value);
                   $currency_money_arr = array();    //国家为K,汇率为V
                   foreach ($currency_arr as $c_v) {
                       $currency_money = explode('<-->', trim($c_v));
                       $currency_money_arr[$currency_money[0]] = $currency_money[1];
                   }                                       
                   if (empty($currency_money_arr[$val['currency_type_cn']])) {
                       echo '没有货币汇率';
                       continue;
                   }
                   $ratio_arr = $currency_arr = explode(':', $currency_money_arr[$val['currency_type_cn']]);
                    
                   $cur_value = trim($ratio_arr[1]); //汇率
                   foreach($OrderList[0]->GetOrderListResponse->GetOrderListResult->OrderList->Order as $Order){
                       $orderNumberArr = '';
                       $orderNumber    = '';
                       $orderNumberArr =  (array)$Order->OrderNumber;
                       $orderNumber    = $orderNumberArr[0];
                       //非等待发货的订单 排除
                       if($Order->OrderState!='WaitingForShipmentAcceptation') continue;
                       
                       //订单已存在的 排除
                       $selectOptions = array();
                       $selectResult  = array();
                       $selectOptions['where']['buyer_id'] = $orderNumber;
                      
                       $selectResult = $this->orders_model->getOne($selectOptions);
                       if($selectResult)continue;
                       
                       $data = array();                       
    //                    $data['notes_to_yourself']       = $msgNote;//(订单留言)
                       $data['orders_ship_fee']         = (string)$Order->ValidatedTotalShippingCharges;  //运费                       
                       $data['buyer_id']                = (string)$orderNumber;
                     
                       $paidTimeArr = array();
                       $paidTime  = '';
                       $paidTimeArr = (array)$Order->CreationDate;
                       $paidTime = $paidTimeArr[0];
                       $data['orders_paid_time']        = date('Y-m-d H:i:s',strtotime($paidTime));//付款时间
                       $data['ShippingServiceSelected'] = (string)$Order->ShippingCode;//客户选择的物流方式
                       
                       $data['orders_export_time']      = date("Y-m-d H:i:s");//订单导入数据库时间
                       
                       $data['buyer_phone']             = (string)$Order->Customer->MobilePhone;//买家电话
                       
                       if(empty($data['buyer_phone'])){
                            $data['buyer_phone']             = (string)$Order->Customer->Phone;//买家电话
                       }
                       

                       $data['buyer_name']              = (string)$Order->ShippingAddress->FirstName;
                       
                       if(!empty($Order->ShippingAddress->LastName)){
                           $data['buyer_name'] .= ' '.(string)$Order->ShippingAddress->LastName;
                       }
                       if(!empty($Order->ShippingAddress->Street)){
                       $data['buyer_address_1']         = (string)$Order->ShippingAddress->Street;//发货地址1
                       }
                       if(!empty($Order->ShippingAddress->ApartmentNumber)){
                           $data['buyer_address_1']    .= ' '.(string)$Order->ShippingAddress->ApartmentNumber;
                       }
                       
                       $data['buyer_city']              = (string)$Order->ShippingAddress->City;//城市
                       
                       if(!empty($Order->County)){//收货地址(州、省)
                           $data['buyer_state']         = (string)$Order->ShippingAddress->County;
                       }
                       
                       $data['buyer_zip']               = (string)$Order->ShippingAddress->ZipCode;//邮编
                       
                       $data['buyer_country']           = (string)$Order->ShippingAddress->Country;//国家
                       $data['buyer_country_code']      = (string)$Order->ShippingAddress->Country;//国家code
                       $data['orders_type']             = 17;  //订单类型
                       
                       $data['orders_total']            = (string)$Order->ValidatedTotalAmount;//订单实收金额 ;
                       
                       $data['orders_ship_fee']         = (string)$Order->ValidatedTotalShippingCharges;//运费
                       
                       $data['currency_value']          = 1/$cur_value; //汇率
                       
                       $data['currency_type']           = (string)$val['currency_type'] ;//币种
                       
                       $data['orders_status']           = 1; //订单状态
                        
                       $data['sales_account']           = (string)$val['account'];//销售帐号
                       
                       $data['erp_user_id']             = (string)$val['responsible'];  
                       $data['buyer_email']             =  (string)$Order->Customer->EncryptedEmail;
                       
                       $erp_orders_id = $this->orders_model->add($data);
                       unset($data);
                       if(empty($erp_orders_id)){
                           $this->db->trans_rollback();
                           return '下载订单失败，原因插入订单表失败';
                       }                                             
                       echo $erp_orders_id.'---';
                   
                   //订单明细
                   $sku_tof = true;
                   foreach($Order->OrderLineList->OrderLine as $OrderLine){
                       if(empty($OrderLine->SellerProductId)){
                           continue;
                       }
                       
                       $totalPrice = 0;
                       $quantity   = 0;
                       $totalPrice = (string)$OrderLine->PurchasePrice;
                       $quantity   = (string)$OrderLine->Quantity;
                       $skuCode   = (string)$OrderLine->SellerProductId;
                        
                       $sku= $this->cdiscount->parseSku($skuCode);
					   
					   //判断是否有005MHM033b(2)或MHM033b(2)类型
					   $item_count = (string)$OrderLine->Quantity;
					   $newskudata = $this->cdiscount->pregSku($sku);
					   if(is_array($newskudata)){
					   		$sku=$newskudata['str'];
						   	$item_count = $newskudata['numb'];
					   }
                   		//处理005MHM033b(2)类型orders_item
                   		$orders_item = (string)$OrderLine->SellerProductId;
						$neworderitem = $this->cdiscount->pregitem($orders_item);
						
                       $dataDetail = array();

                       $dataDetail['erp_orders_id']   = $erp_orders_id;                       
                        
                       $dataDetail['orderlineitemid'] = (string)$OrderLine->ProductId;
                        
                       $dataDetail['orders_sku']      = $sku;
                       
                       $dataDetail['orders_item']      = $neworderitem;
                       
                       $dataDetail['comment_text']    = (string)$OrderLine->SellerProductId ;;
                        
                       $dataDetail['orders_item_number'] =(string)$OrderLine->ProductId ;
                        
//                        if(empty( $dataDetail['orders_sku'])){                          
//                            $this->db->trans_rollback();
//                            var_dump($orderNumber);
//                            $sku_tof = false;
//                            break;
//                        }
                        
                       $dataDetail['item_price'] = number_format($totalPrice/$quantity,3);
                        
                       $dataDetail['item_count'] = (string)$OrderLine->Quantity;
                       $tof = $this->orders_products_model->add($dataDetail);
                       unset($dataDetail);                        
                       if(empty($tof)){
                           $this->db->trans_rollback();
                           $sku_tof = false;
                           break;
                       }                       
                       if(!$sku_tof){
                           return '插入订单详情表失败';
                       }
                   }//endforeach 订单明细                                      
                   
               }//endforeach 订单信息
            }//endforeach 订单循环
         }//endforeach token
         $apiUpdateData = array();
         $apiUpdateData = array(
             'end_time' => date("Y-m-d H:i:s"),
             'status'   =>self::API_END ,
         );
         $platform=$this->platform;
         $this->order_api_log_model->updatedhgateOrderApiLog($this->api_time,$platform,$apiUpdateData);
         if($this->db->trans_status() === TRUE){
             $this->db->trans_commit();//事务结束
             echo "success";
         }
    }
    

    
    /**
     * 自动标记发货
     */
    public function autoSubmitShippingCode(){
        $tokenInfo = $this->cdiscount_token_model->getAll2Array();//获取平台帐号
        foreach($tokenInfo as $key=>$val){//检测token是否已过期
            if(empty($val['expires_in']) || strtotime(date('Y-m-d'))>=$val['expires_in']){
                $this->getTokenToSave();//获取或更新token
            }
        }
        
        //挂号渠道
        $registerLogistics = array('465', '327', '202', '472');
            
            
        foreach($tokenInfo as $key =>$val){
            $result = $this->orders_model->getCdiscountTrackNo($val['account']);
            if(empty($result))continue;
            
            foreach($result as $order){
                if(!empty($order['orders_ship_fee']) && empty($order['orders_shipping_code'])){//有运费没跟踪号
                    $op = $order['erp_orders_id'] . "有运费没跟踪号,无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 17, $op, $success = 2);//log可通用,改变type
                    continue;
                }
                if(empty($order['shipmentAutoMatched'])){//没物流方式
                    $op = $order['erp_orders_id'] . "没物流方式,无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 17, $op, $success = 2);//log可通用,改变type
                    continue; 
                }
                
                //挂号渠道需要等有了追踪号才能标记发货
                if(in_array($order['shipmentAutoMatched'], $registerLogistics) && empty($order['orders_shipping_code'])){
                	continue;
                }
                
                $shipmentArr = array();
                $selectOptions = array();
                $selectOptions['where']['shipmentID'] = $order['shipmentAutoMatched'];
                $shipmentArr = $this->shipment_model->getOne($selectOptions,true);
                $productsArr = array();
                $productsOptions = array();
                $productsOptions['where']['erp_orders_id'] = $order['erp_orders_id'];
                $productsArr = $this->orders_products_model->getAll($productsOptions);
                if(empty($productsArr)){//订单明细不存在
                    $op = $order['erp_orders_id'] . "订单明细不存在,无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 17, $op, $success = 2);//log可通用,改变type
                    continue;
                }
                $uploadArr   = array();
                $uploadArr['token_id']       = $val['token_id'];
                $uploadArr['OrderNumber']    = $order['buyer_id'];
                $uploadArr['TrackingNumber'] = $order['orders_shipping_code'];
                $uploadArr['TrackingUrl']    = $shipmentArr['shipmentDescription'];
                $uploadArr['CarrierName']    = $shipmentArr['shipmentCdiscountCodeID'];
                $uploadArr['products_info']  = $productsArr;
                $flag = $this->cdiscount->uploadTrackNo($uploadArr);//标记发货api
                if ($flag=='true') {
                    $upResult = $this->orders_model->upDataOrderByShip($order['erp_orders_id']); //更新订单状态为标记发货完成
                    $op = $order['erp_orders_id'] . "上传追踪号成功，并更新对应的订单信息";
                    $this->orders_model->lazadaOrderOperating_log($type = 17, $op, $success = 1);
                    echo '标记发货成功,订单号:'.$order['erp_orders_id'];
                }else {
                    $op = $order['erp_orders_id'] . "上传追踪号失败";
                    $this->orders_model->lazadaOrderOperating_log($type = 17, $op, $success = 2);
                    echo '标记发货失败，订单号:'.$order['erp_orders_id'];
                }
            }//endforeach 订单循环
        }//endforeach token 循环
        
    }
    
    
    /**
     * 获取在售SKU
     */
    public function getCdiscountOnsellProducts(){
        $tokenInfo = $this->cdiscount_token_model->getAll2Array();//获取平台帐号
        foreach($tokenInfo as $key=>$val){//检测token是否已过期
            if(empty($val['expires_in']) || strtotime(date('Y-m-d'))>=$val['expires_in']){
                $this->getTokenToSave();//获取或更新token
            }
        }
        foreach($tokenInfo as $key =>$val){
            $result = $this->cdiscount->getOnsellProducts($val);
            if(empty($result->Offer))continue;
            foreach($result->Offer as $Offer){
                $data = array();
                $data['sellerSku'] = (string)$Offer->SellerProductId;
                $data['shopSku'] = (string)$Offer->ProductId;
                $data['sku'] =  $this->cdiscount->parseSku($data['sellerSku']);
                $data['quantity'] = (string)$Offer->Stock;
                $data['price'] = (string)$Offer->Price;
                $data['status'] = (string)$Offer->OfferState;
                $data['productId'] = (string)$Offer->ProductId;
                $data['product_ean'] = (string)$Offer->ProductEan;
                $data['account'] = $val['account'];
                $data['listingDate'] = (string)$Offer->CreationDate;//上架时间
                $data['updataDate'] = date('Y-m-d H:i:s');
                $checkOptions = array();
                $checkOptions['where']['sellerSku'] = (string)$Offer->SellerProductId;
                $checkResult = $this->cdiscount_products_model->getOne($checkOptions,true);//检测是否存在
                if($checkResult){
                    $updateOptions = array();
                    $updateOptions['where']['id'] = $checkResult['id'];
                    $this->cdiscount_products_model->update($data,$updateOptions);
                }else{
                    $this->cdiscount_products_model->add($data);
                }

            }echo 'success';
        }
    }
    
    /**
     * 测试修改价格和数量
     */
    public function testPriceAndStock(){
        $tokenInfo = $this->cdiscount_token_model->getAll2Array();//获取平台帐号
        foreach($tokenInfo as $key=>$val){//检测token是否已过期
            if(empty($val['expires_in']) || strtotime(date('Y-m-d'))>=$val['expires_in']){
                $this->getTokenToSave();//获取或更新token
            }
        }
        foreach($tokenInfo as $key =>$val){
            $this->cdiscount->editOnsellProduct($val);
        }
        
    }
    
    
    /**
     * 爬虫抓价格
     */
    public function test(){
        $html = new simple_html_dom();
//         $url = 'http://www.cdiscount.com/bagages/bagages/epaule-messenger-sac-de-toile/f-1432203-auc2009953146856.html';
        $url = 'http://www.cdiscount.com/auto/entretien-vehicules-fluides/varta-batterie-auto-e43-droite-12v-72ah-680a/f-1339103-var4016987119556.html#mpos=2|cd';
        $html->load_file("$url");
        $main = $html->find('div[id=fpBlocPrice] p[class=fpPrice price]',0)->attr;
        $price = '';      
        $price = str_replace(',','.',$main['content']);
        var_dump($price);exit;
    }
    
}