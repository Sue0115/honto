<?php
/*
*黑名单客户订单数据备注脚本 
*周日跑   更新(添加)erp_orders黑名单客户订单的备注信息
*@author:hejiancheng 2016-03-08
*/
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class auto_blacklist_remark extends MY_Controller{
	
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array(
				'order/orders_blacklist_model'
          )
        );
       $this->model = $this->orders_blacklist_model;
	   $this->load->model(array('operate_log_model'));

    }
	/*
	*黑名单客户数据更新管理
	*/
	function index(){
		    
			$ret = $this->model->get_success_blacklist();     //获得erp_orders_blacklist黑名单客户信息
			
			$buyer_id = '';
			
			$buyer_email = '';
			
			$buyer_name = '';
			
			if($ret){
			foreach($ret as $k=>$v){
				
				if($v['color_type'] == 0 || $v['color_type'] == 3 ){   //搜索条件color_type 1=buyer_name+buyer_zip 2 =buyer_email 0=导入数据buyer_id 3=buyer_id
					
					$buyer_id .= '"'."".$v['buyer_id']."".'",';
				}else if($v['color_type'] == 1){
					
					$buyer_name .= '"'."".$v['buyer_name']."".'",';
				}else if($v['color_type'] == 2){
					
					$buyer_email .= '"'."".$v['buyer_email']."".'",';   //防止特殊字符导致sql错误  如 buyer_name = Kuziura Valentyna Mykolai'vn
				}
				
			  }
			  
			  if($buyer_id){
				  
				  $buyer_id = rtrim($buyer_id,',');
				  
				  $list = $this->model->up_blacklist_remark($email = '',$name = '',$buyer_id);
				  if($list){
					  
					  echo "收货人id为".$buyer_id."的黑名单客户订单备注成功！<br>";
				  }else{
					  echo "收货人id为".$buyer_id."的黑名单客户订单备注失败！请检查！<br>";
				  }
			  }
			  if($buyer_email){
				  
				  $buyer_email = rtrim($buyer_email,',');
				  
				  $list = $this->model->up_blacklist_remark($buyer_email,$name = '',$id = '');
				  if($list){
					  
					  echo "收货人email为".$buyer_email."的黑名单客户订单备注成功！<br>";
				  }else{
					  echo "收货人email为".$buyer_email."的黑名单客户订单备注失败！请检查！<br>";
				  }
			  }
			  if($buyer_name){
				  $buyer_name = rtrim($buyer_name,',');
				  
				  $list = $this->model->up_blacklist_remark($email = '',$buyer_name,$id = '');
				  if($list){
					  
					  echo "收货人为".$buyer_name."的黑名单客户订单备注成功！<br>";
				  }else{
					  echo "收货人为".$buyer_name."的黑名单客户订单备注失败！请检查！<br>";
				  }
				  
			  }
			  
			}
		    
	}
}
