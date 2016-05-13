<?php
/*
*确认为黑名单数据  自动匹配重新计算订单总数和退款数脚本(1周1次 确保数据准确)
*周日跑   1周1次 确保数据准确
*@author:hejiancheng 2016-04-25
*/
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
error_reporting(0);  //屏蔽页面报错信息
header("content-type:text/html; charset=utf-8");
class auto_blacklist_refresh extends MY_Controller{
	
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
	*确认为黑名单数据  重新计算订单总数和退款数脚本(1周1次 确保数据准确)
	*/
	function index(){
		
		    $result = $this->model->get_success_blacklist();     
			
			if($result){
				
				$email =  '';
			
				$id = '';
				
				$zip = '';
				
				$name = '';
				
				$i = 0;
				
				$color = '';
				
				foreach($result as $k=>$v){
					
					$count = array();   //循环初始化
					
					$count_refund = array();
					
					if($v['color_type'] == 2){   //根据color标识判断buyer_id,buyer_email,buyer_name搜索
						
						$count = $this->model->get_count_buyer($v['buyer_email'],$id,$zip,$name);   //订单总数
						
						$count_refund = $this->model->get_refund_buyer($v['buyer_email'],$id,$zip,$name);   //退款订单总数
						
						$this->model->change_order_num($color,$v['color_type'],$count['0']['num'],$count_refund['0']['num'],$v['erp_orders_id'],$v['buyer_email'],$id,$name);
					}else if($v['color_type'] == 1){
						
						$count = $this->model->get_count_buyer($email,$id,$zip,$v['buyer_name']);
						
						$count_refund = $this->model->get_refund_buyer($email,$id,$zip,$v['buyer_name']);
						
						$this->model->change_order_num($color,$v['color_type'],$count['0']['num'],$count_refund['0']['num'],$v['erp_orders_id'],$email,$id,$v['buyer_name']);
										
					}else if($v['color_type'] == 3){
						
						$count = $this->model->get_count_buyer($email,$v['buyer_id'],$zip,$name);
						
						$count_refund = $this->model->get_refund_buyer($email,$v['buyer_id'],$zip,$name);
						
						$this->model->change_order_num($color,$v['color_type'],$count['0']['num'],$count_refund['0']['num'],$v['erp_orders_id'],$email,$v['id'],$name);
					
					}else if($v['color_type'] == 0){    //如果是导入订单  自动匹配总订单数多的匹配结果
						
						if($v['buyer_email']){
							
							$count_email = $this->model->get_count_buyer($v['buyer_email'],$id,$zip,$name);   //订单总数
						
					    	$count_refund_email = $this->model->get_refund_buyer($v['buyer_email'],$id,$zip,$name);   //退款订单总数
						
						}else if($v['buyer_name']){    
							
							$count_name = $this->model->get_count_buyer($email,$id,$zip,$v['buyer_name']);
						
						    $count_refund_name = $this->model->get_refund_buyer($email,$id,$zip,$v['buyer_name']);
						
						}
						if($count_email['0']['num'] >$count_name['0']['num']){   //根据自动匹配结果判断总订单数多的匹配结果进行更改
							
							$this->model->change_order_num(2,$v['color_type'],$count_email['0']['num'],$count_refund_email['0']['num'],$v['erp_orders_id'],$v['buyer_email'],$id,$name);
					
					        $ret = $count_email['0']['num'].'--'.$count_refund_email['0']['num'].'--'.'<br>';
						
					      }else if($count_name['0']['num'] > $count_email['0']['num']){
							  
							  $this->model->change_order_num(1,$v['color_type'],$count_name['0']['num'],$count_refund_name['0']['num'],$v['erp_orders_id'],$email,$id,$v['buyer_name']);
								
							  $ret = $count_name['0']['num'].'--'.$count_refund_name['0']['num'].'--'.'<br>';
								
					    }
						
					}
					
					$i++;
				}
				
			}else{
				
				echo "没有要进行重新计算的黑名单客户数据！";
			}
			
			//echo $this->db->last_query();
	}
	
}
