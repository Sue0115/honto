<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
/**
 * Created by PhpStorm.
 * User: Administrator
 * DateTime: 2015-05-09  10:00:00 
 */
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auto_dhgate extends MY_Controller{
    public $authorizationCode = '';
    public $accessToken       = '' ;
    public $refreshToken      = '';  // 	用于刷新Access Token 的 Refresh Token
    public $api_time          = '';
    public $platform          = 'dh';
    const  API_START          = 1;
    const  API_END            = 2;
    const  EMPTY_END          = 3;
    function __construct(){
        parent::__construct();
    
        $this->load->library('MyDhgate');
    
        $this->load->model(array(
            'system_model','order/orders_model','order/order_api_log_model','order/orders_products_model', 'dhgate_token_model','dhgate_logistics_service_model')
        );
        $this->dhgate= new MyDhgate();
    }
    
    /**
     * 授权获取access_token,refresh_token并保存
     */
    public function getAccessTokenToSave(){
        $token_info = $this->dhgate_token_model->getAll2Array();//获取平台帐号
        foreach($token_info as $key=>$val){
            $result=$this->dhgate->getAccessTokenWithAccountPassword($val);
            if(!isset($result->error)){
                $updateInfo=array("token_id"=>$val['token_id'],"access_token"=>$result->access_token,"refresh_token"=>$result->refresh_token,
                    'expires_in' =>$result->expires_in,'create_time'=>date('Y-m-d H:i:s')
                );
                $flag=$this->dhgate_token_model->update($updateInfo);
            }
        }
    }
    
    
    /**
     * refresh_token刷新access_token,并保存
     */
    public function updateAccessTokenToSave(){
        $token_info = $this->dhgate_token_model->getAll2Array();//获取平台帐号
        foreach($token_info as $key=>$val){
            $result=$this->dhgate->refreshAccessToken($val);
            if(!isset($result->error)){
                $updateInfo=array("token_id"=>$val['token_id'],"access_token"=>$result->access_token,"refresh_token"=>$result->refresh_token,
                    'expires_in' =>$result->expires_in,'create_time'=>date('Y-m-d H:i:s')
                );
                $flag=$this->dhgate_token_model->update($updateInfo);
            }
        }
    }
    

    /**
     * 自动拉单前先检测access_token
     */
    public function autoUpdateAccessTokenToGetOrders(){
        $token_info = $this->dhgate_token_model->getAll2Array();//获取平台帐号
        foreach($token_info as $key=>$val){
            //如果没有access_token 或refresh_token 超时了(有效期30天) 就先获取
            if(empty($val['access_token']) || (strtotime($val['create_time'])+30*24*3600<=strtotime(date('Y-m-d H:i:s')))){
                $this->getAccessTokenToSave();
            }
            //一月内如果access_token过期 则用refresh_token去刷新
            if(strtotime(date('Y-m-d H:i:s'))*1000 >=$val['expires_in']){
                $this->updateAccessTokenToSave();
            }
        }
        $this->autoGetOrderList();
    }
    
    /**
     * 自动标记发货前先检测access_token
     */
    public function autoUpdateAccessTokenToUpLoadShippingCode(){
        $token_info = $this->dhgate_token_model->getAll2Array();//获取平台帐号
        foreach($token_info as $key=>$val){
            //如果没有access_token 或refresh_token 超时了(有效期30天) 就先获取
            if(empty($val['access_token']) || (strtotime($val['create_time'])+30*24*3600<=strtotime(date('Y-m-d H:i:s')))){
                $this->getAccessTokenToSave();
            }
            //一月内如果access_token过期 则用refresh_token去刷新
            if(strtotime(date('Y-m-d H:i:s'))*1000 >=$val['expires_in']){
                $this->updateAccessTokenToSave();
            }
        }
        $this->autoUpLoadShippingCode();
    }
    
    /**
     * 自动拉单程序
     */
    public function autoGetOrderList(){
         $token_info = $this->dhgate_token_model->getAll2Array();//获取平台帐号	
         foreach($token_info as $key=>$val){               
           $page=1;$pagesize=100;//初始值 每页记录数100
           $j=1; $orderFlag=$flag=false;         
           for($i=0;$i<$j;$i++){ 
               $orderInfo=array();
               if($i==0){//首次交互开始
                   $this->api_time =  date('Y-m-d  H:i:s');
                   $apiInsertData = array(
                       'api_time'  => date('Y-m-d'),
                       'platform'  => 'dh',//敦煌,
                       'api_name'  => 'dh_get_orders_'.date('Y-m-d'),
                       'start_time'=> $this->api_time,
                       'status'    => self::API_START ,
                   ); 
                                  
                    $this->order_api_log_model->dhgateOrderApiLog($apiInsertData);
               }              
               $orderInfo= $this->dhgate->getDhgateOrder(trim($val['access_token']),$page,$pagesize); 
               $count=$orderInfo->count;     //总长度
               $j=$orderInfo->pages; //总页数
               if($i==($j-1)) $orderFlag=true;
               $page++;
               if($orderInfo->status->message=='OK'){
                   foreach ($orderInfo->orderBaseInfoList as $k => $v){
                       if(count($orderInfo->orderBaseInfoList)==($k+1))$flag=true;
                     $this->insert_orders($v, trim($val['access_token']),$val,$orderFlag,$flag);
                   }
               }
           }
        }
    }
    
    //获取订单产品信息
    public function insert_orders ($order_info, $accessToken,$tokenInfo,$orderFlag,$flag){ 
        $erp_order_data = $this->orders_model->getErpIdByDhOrderid($order_info->orderNo);//根据订单内单号查找外单号
        if(!empty($erp_order_data)){ //判断订单是否重复
            //更新api日志状态           
            if($orderFlag && $flag){
                $apiUpdateDataEmpty = array(
                    'end_time'=> date("Y-m-d H:i:s"),
                    'status'    =>self::EMPTY_END ,
                );
                $platform=$this->platform;
                $this->order_api_log_model->updatedhgateOrderApiLog($this->api_time,$platform,$apiUpdateDataEmpty);
            }
            return FALSE;
        }
         $productInfo     = $this->dhgate->getDhgateOrderDetailInfo(trim($accessToken),$order_info->orderNo);//orderProductList
         $orderDetailInfo = $this->dhgate->getOrderDetailInfo(trim($accessToken),$order_info->orderNo);      //orderdetail
        
        if(empty($productInfo) || empty($orderDetailInfo)) {
            return false;
        }    
        $items=$productInfo->orderProductList;
        if(empty($items)){
            return FALSE;
        }
         
    if($productInfo->status->message=='OK')
        $this->auto_download_order($order_info,$orderDetailInfo,$items,$tokenInfo,$orderFlag,$flag);
    }
    
    //数据插入到数据库
    public function auto_download_order($order_info,$orderDetailInfo,$item_info,$tokenInfo,$orderFlag,$flag){ 
        $msgNote='';
        if(!empty($item_info)){                  
               foreach($item_info as $k=>$v){
                $msgNote .= $v->buyerRemark;//买家备注
               }
        }
        $total = $order_info->orderTotalPrice;//$sku_data['total'];//订单总价格    
        $orders_ship_fee =$orderDetailInfo->shippingCost; //$sku_data['orders_ship_fee'];   
        $data = array();
        $data['notes_to_yourself']       = $msgNote;//(订单留言)
        $data['orders_ship_fee']         = $orderDetailInfo->shippingCost;
        $data['ebay_orders_id']          = $order_info->orderNo;
    
        $data['buyer_id']                = $order_info->orderNo;
    
        $data['buyer_name']              = $orderDetailInfo->orderContact->firstName;
    
        if(!empty($orderDetailInfo->orderContact->lastName)){
            $data['buyer_name'] .= ' '.$orderDetailInfo->orderContact->lastName;
        }
  
        if(!empty($order_info->orderRemark)){//订单备注
            $data['orders_remark']       = $order_info->orderRemark;
        }
    
        $data['orders_paid_time']        = $order_info->payDate;//设为付款时间
    
        $data['ShippingServiceSelected'] = $orderDetailInfo->shippingType;//客户选择的物流方式
    
        $data['orders_export_time']      = date("Y-m-d H:i:s");//订单导入数据库时间
    
        $data['buyer_phone']             = $orderDetailInfo->orderContact->telephone;//买家电话
    
        $data['buyer_address_1']         = $orderDetailInfo->orderContact->addressLine1;//发货地址1
    
        if(!empty($orderDetailInfo->orderContact->addressLine2)){
            $data['buyer_address_1']    .= ' '.$orderDetailInfo->orderContact->addressLine2;
        }
    
        $data['buyer_city']              = $orderDetailInfo->orderContact->city;//城市
    
        if(!empty($orderDetailInfo->orderContact->state)){//收货地址(州、省)
            $data['buyer_state']         = $orderDetailInfo->orderContact->state;
        }
    
        $data['buyer_zip']               = empty($orderDetailInfo->orderContact->postalcode)?" ":$orderDetailInfo->orderContact->postalcode;//邮编
    
        $data['buyer_country']           = $order_info->country;//国家
    
        $data['orders_type']             = 16;  //订单类型
    
        $data['orders_total']            = $orderDetailInfo->actualPrice;//订单实收金额 ;
    
        $data['orders_ship_fee']         = $orderDetailInfo->shippingCost;//运费
    
        $data['currency_value']          = 1; //汇率
        
        $data['currency_type']           = 'USD' ;//币种
    
        $data['orders_status']           = 1; //订单状态
         
        $data['sales_account']           = $tokenInfo['sales_account'];//销售帐号
        //事务开启
        $this->db->trans_begin();
    

         $erp_orders_id = $this->orders_model->add($data);
    
        if(empty($erp_orders_id)){
            $this->db->trans_rollback();
            return '下载订单失败，原因插入订单表失败';
        }
  
         $sku_tof = true;
    
        foreach ($item_info as $k => $v) {
            $arr=array();
            $arr[0]['skuCode']   = $v->skuCode;
            $arr[0]['itemPrice'] = $v->itemPrice;
            
            $arrSku= $this->dhgate->resetTransactionDetail($arr);
   
            $data = array();
    
            $data['erp_orders_id']   = $erp_orders_id;
    
            $data['ebay_orders_id']  = $order_info->orderNo;
    
            $data['orderlineitemid'] = $v->itemcode;
    
            $data['orders_sku']      = trim($arrSku[0]['skuCode']);
            $data['comment_text']    = $v->itemcode.'@'.$v->skuCode;
//             $data['comment_text'] = ;//这2行不知道要显示什么
    
             $data['orders_item_number'] =$v->itemcode ;
    
//             if(empty( $data['orders_sku'])){
//                 var_dump($sku_data);
//                 $this->db->trans_rollback();
//                 var_dump($item);
//                 $sku_tof = false;
//                 break;
//             }
    
            $data['item_price'] = $arrSku[0]['itemPrice'];
    
            $data['item_count'] = $v->itemCount;

             $tof = $this->orders_products_model->add($data);
    
            if(empty($tof)){
                $this->db->trans_rollback();
                $sku_tof = false;
                break;
            }
        }
    
        if(!$sku_tof){
            return '插入订单详情表失败';
        }
   //更新api日志状态 
       if($orderFlag && $flag){
           $apiUpdateData = array(
               'end_time' => date("Y-m-d H:i:s"),
               'status'   =>self::API_END ,
           );
           $platform=$this->platform;
           $this->order_api_log_model->updatedhgateOrderApiLog($this->api_time,$platform,$apiUpdateData);
       }
        if($this->db->trans_status() === TRUE){
            echo '插入成功,订单号:'.$erp_orders_id.'<br/>';
            $this->db->trans_commit();//事务结束
        }
    }
    
        

    
    //自动上传追踪号
     public  function autoUpLoadShippingCode(){
    
        $token_info = $this->dhgate_token_model->getAll2Array();//获取账号信息
       
        foreach ($token_info as $val){ //循环账号          
            $result = $this->orders_model->getDhgateTrackNo($val['sales_account']);// 根据账号找出订单
            foreach ($result as $v) {       
                $dhgateOrderItemId = $this->orders_products_model->getLazadaOrderItemId($v['erp_orders_id']); //找出订单对应产品的OrderItemId
                $shipmentDhgateCodeID = $this->orders_model->dhgateOrderGetShipName($v['erp_orders_id']);
                if (empty($v['orders_shipping_code'])) {
                    echo  $v['erp_orders_id'] . "追踪号为空,无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 2);//log可通用,改变type
                    continue;
                }  
                if (empty($shipmentDhgateCodeID)) {
                    echo  $v['erp_orders_id'] . "没有物流编号,无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 2);//log可通用,改变type
                    continue;
                }
                $ShippingProvider = $this->dhgate_logistics_service_model->getDhgateShipName($shipmentDhgateCodeID['shipmentDhgateCodeID']);
              
                if (empty($ShippingProvider)) {
                    echo $v['erp_orders_id'] . "没有物流名称,无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 2);
                    continue;
                }
                if (empty($dhgateOrderItemId)) {
                    echo $v['erp_orders_id'] . "没有找到对应产品,无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 2);
                    continue;
                }
                $uploadTrackStatus = $this->dhgate->uploadTrackNo(trim($val['access_token']),array(
//                  'deliveryRemark'    => $v['orders_remark'],//运单备注                
                'deliveryState'      => '1',//发货状态(1全部发货，2部分发货)
                'rfxNo'              => $v['buyer_id'],//订单号
                'shippingType'       => $ShippingProvider['logistics_name'],//运输方式
                'trankNo'            => $v['orders_shipping_code'],//运单号
                ));
               
                if ($uploadTrackStatus->result=="SUCCESS") {
                    $upResult = $this->orders_model->upDataOrderByShip($v['erp_orders_id']); //更新订单状态为标记发货完成
                    $op = $v['erp_orders_id'] . "上传追踪号成功，并更新对应的订单信息";
                    $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 1);
                    echo '标记发货成功,订单号:'.$v['erp_orders_id'];
                }else {               
                    $op = $v['erp_orders_id'] . "上传追踪号失败，信息为 " . $uploadTrackStatus->error;
                    $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 2);
                    echo '标记发货失败，订单号:'.$v['erp_orders_id'].'，原因：'.$uploadTrackStatus->error.', '.$uploadTrackStatus->status->message;
                }

            }
        }
    }
    
    /**
     *自动获取平台物流方式  
     */
    public function getShippingTypeList(){
        $token_info = $this->dhgate_token_model->getAll2Array();//获取账号信息
        foreach ($token_info as $val){ //循环账号
            //如果没有access_token 或refresh_token 超时了(有效期30天) 就先获取
            if(empty($val['access_token']) || (strtotime($val['create_time'])+30*24*3600<=strtotime(date('Y-m-d H:i:s')))){
                $this->getAccessTokenToSave();
            }
            //一月内如果access_token过期 则用refresh_token去刷新
            if(strtotime(date('Y-m-d H:i:s'))*1000 >=$val['expires_in']){
                $this->updateAccessTokenToSave();
            }
            $result=$this->dhgate->shippingTypeList($val);
            foreach($result->shippingTypeList as $shipName){
                $data=array();
                $data['logistics_name']=$shipName->name;
                $flag=$this->dhgate_logistics_service_model->addShippingType($data);
            }
            break;
            
        }
    }
    
    

    
    
}