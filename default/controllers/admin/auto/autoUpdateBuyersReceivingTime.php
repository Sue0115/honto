<?php 
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
/**
 * Created by PhpStorm.
 * @author Darren
 * DateTime: 2015-06-06
 */
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AutoUpdateBuyersReceivingTime extends MY_Controller{
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
     * 自动获取买家确认到货时间程序
     */
    public function autoGetOrderListWithBuyersReceivingTime(){
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
                       'api_name'  => 'dh_update_buyers_receiving_time',
                       'start_time'=> $this->api_time,
                       'status'    => self::API_START ,
                   ); 
                                  
                    $this->order_api_log_model->dhgateOrderApiLog($apiInsertData);
               }              
               $orderInfo= $this->dhgate->getBuyersReceivingTime(trim($val['access_token']),$page,$pagesize); //获取期限内交易完成订单
               $count=$orderInfo->count;     //总长度
               $j=$orderInfo->pages; //总页数
               if($i==($j-1)) $orderFlag=true;
               $page++;
               if($orderInfo->status->message=='OK'){
                   foreach ($orderInfo->orderBaseInfoList as $k => $v){
                       if(count($orderInfo->orderBaseInfoList)==($k+1))$flag=true;
                     $this->insert_orders($v, $val,$orderFlag,$flag);
                   }
               }
           }
        }
    }
    
    //更新买家确认到货时间
    public function insert_orders ($order_info, $tokenInfo,$orderFlag,$flag){ 
        $erp_order_data = $this->orders_model->getErpIdByDhOrderid($order_info->orderNo);//根据订单内单号查找外单号       
        if(empty($erp_order_data) || !empty($erp_order_data['end_time'])){ //判断是否已经更新过买家确认到货时间
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
        $this->auto_download_order($order_info,$tokenInfo,$orderFlag,$flag);
    }
    
    //数据更新到数据库
    public function auto_download_order($order_info,$tokenInfo,$orderFlag,$flag){   
        $data = array();
        $data['end_time']          = strtotime($order_info->buyerConfirmDate);//买家确认收货时间   
        //事务开启
        $this->db->trans_begin();

        $erp_orders_id = $this->orders_model->UpdateDhBuyersReceivingTime($data,$order_info->orderNo);// 
    
        if(empty($erp_orders_id)){
            $this->db->trans_rollback();
            return '更新买家确认到货时间失败，原因插入订单表失败';
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
            echo '插入成功,订单号:'.$order_info->orderNo.'<br/>';
            $this->db->trans_commit();//事务结束
        }
        
    }

    

    
    
}