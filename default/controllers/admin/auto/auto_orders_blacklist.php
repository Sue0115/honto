<?php
/*
*黑名单客户数据更新脚本   crontab 一周一次周日晚上3点跑脚本  
*周日跑   更新(添加)erp_orders表中时间到现在的新增的黑名单客户的信息
*@author:hejiancheng 2016-02-23
*/
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class auto_orders_blacklist extends MY_Controller{
	
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
			$ret = $this->model->get_orders_blacklist();     //获得订单表erp_orders黑名单客户信息
			
			$i = 0;
			
			$data = array();
			
			$buyer_info = '';    //初始化买家信息
			
                        
			foreach($ret as $key=>$val){    //需求   同一 收货人 or 邮编 or 邮箱 or ID 相同，且提交退款订单个数超过5个的
				
				$buyer_info['name'][$i] = $val['buyer_name'];     //赋值一次性计算次数  避免大量循环
				
				$buyer_info['zip'][$i] = $val['buyer_zip'];
				
				$buyer_info['email'][$i] = $val['buyer_email'];
				
				$buyer_info['id'][$i] = $val['buyer_id'];
				
				$i++;
			}
			
			$buyer_name = array_merge(array_count_values($buyer_info['name']),array_count_values($buyer_info['email']));
			
			$buyer_zip = array_merge(array_count_values($buyer_info['zip']),array_count_values($buyer_info['id']));
			
			$buyer_info = array_merge($buyer_name,$buyer_zip);//array_count_values计算值出现的次数    array_merge合并为一个数组计算 
			//print_r($buyer_info);exit;
			$email =  '';
			
			$id = '';
			
			$zip = '';
			
			$name = '';
			foreach($buyer_info as $key=>$val){     //计算同一 收货人and邮编 or 邮箱 or ID相同    且提交退款订单个数超过5个的
				if($val < 6){
					unset($buyer_info[$key]);   //此处需要密集计算上亿次 很占用内存
				}else if($val > 5){
					$i = '';
					foreach($ret as $k=>$v){
						$v['times'] = $val;
						if($key == $v['buyer_email'] && $v['buyer_email'] !== ''){
							$count = $this->model->get_count_buyer($v['buyer_email'],$id,$zip,$name);   //订单总数
							$count_refund = $this->model->get_refund_buyer($v['buyer_email'],$id,$zip,$name);   //退款订单总数
							if($count['0']['num'] > 6 && $count_refund['0']['num'] > 5){
								$v['color_type'] = 2;
								$v['times'] = $count_refund['0']['num'];
								$v['orders_count'] = $count['0']['num'];
								$data[] = $v;
								break;
							}
						}else if($key == $v['buyer_id'] && $v['buyer_id'] !== ''){
							$count = $this->model->get_count_buyer($email,$v['buyer_id'],$zip,$name);
							$count_refund = $this->model->get_refund_buyer($email,$v['buyer_id'],$zip,$name);
							if($count['0']['num'] > 6 && $count_refund['0']['num'] > 5){
								$v['color_type'] = 3;
								$v['times'] = $count_refund['0']['num'];
								$v['orders_count'] = $count['0']['num'];
								$data[] = $v;
								break;
							}
						}else if($key == $v['buyer_name'] && $v['buyer_zip'] !== ''){    //计算  收货人and邮编 相同5个以上退款
						  if($val > 5){
							foreach($ret as $j=>$r){
							   	if($v['buyer_zip'] == $r['buyer_zip']){
									if($r['erp_orders_id'] == $v['erp_orders_id']){      //计算  erp_orders_id相同则为同一个已经查出来的订单中数据  符合收货人and邮编 相同5个以上退款 数据
										$count = $this->model->get_count_buyer($email,$id,$zip,$v['buyer_name']);
										$count_refund = $this->model->get_refund_buyer($email,$id,$zip,$v['buyer_name']);
										$num = $count['0']['num']/$val;
										if($num <6 && $count_refund['0']['num'] > 5){   //buyer_name退款异常的总数大于15%的
											$i = true;
											$v['color_type'] = 1;
											$v['times'] = $count_refund['0']['num'];
											$v['orders_count'] = $count['0']['num'];
											$data[] = $v;
											break;
										}
										
										
									}
								}
							}
						  }
						}
						if($i == true){
							break;
						}
					}
					
				}
			}			

		
			$j = 1;
			
			$k = 0;
			
			$add_values = '';
			
			if($data){
				
				foreach($data as $key=>$val){
					
					$add_values .= "(".$val['erp_orders_id'].",".$val['orders_type'].",'".$val['buyer_id']."','".$val['buyer_name']."','".$val['buyer_zip']."','".$val['buyer_email']."',0,'".$val['orders_export_time']."','".$val['times']."','".$val['sales_account']."',".$val['color_type'].",".$val['orders_count']."),";
					
					if(!($j%50)){     //能被50整除  批量insert
					
						$add_values = rtrim($add_values,',');
						
						if($add_values){
							
						$k++;
							if($k == 1){     //确认有数据时
								
								$this->model->del_blacklist();//清空待处理黑名单数据  留确认为黑名单status=1和导入为黑名单数据status=2
							}
						}
						
						$result = $this->model->add_black_list($add_values);    //执行insert
						
						if($result !== false){
							
							unset($add_values);    //释放
							
							$add_values = '';
						}
					}
					$j++;
				}
				$add_values = rtrim($add_values,',');
				
	$result = $this->model->add_black_list($add_values);    //执行最后的insert
				
				if($result){    //最后的  进行重复数据的计算与删除
					
				$repeat_email = '';
				
				$repeat_name = "";
				
				$email_result = $this->model->return_array("SELECT * FROM erp_orders_blacklist WHERE `status` = 0 AND buyer_email IN ( SELECT buyer_email FROM erp_orders_blacklist WHERE `status` = 1 ) ");
				
				foreach($email_result as $k=>$v){     //待处理黑名单状态中有  重复在已确认黑名单的数据   进行删除
					
					$repeat_email .= '"'.$v['buyer_email'].'",';
					
				}
				if($repeat_email){
					
					$repeat_email = rtrim($repeat_email,',');
					
					if($repeat_email){
						$sql = "delete from erp_orders_blacklist where buyer_email in(".$repeat_email.") and status=0";
						
						$ret = $this->model->return_query($sql);
						
						if($ret){
							
							echo "buyer_email为".$repeat_email."的待处理重复的黑名单数据已删除！<br>";
						}else{
							echo "待处理状态中没有重复的buyer_email数据!<br>";
						}
					}
				}
				
				$email_result = $this->model->return_array("SELECT * FROM erp_orders_blacklist WHERE `status` = 0 AND buyer_name IN ( SELECT buyer_name FROM erp_orders_blacklist WHERE `status` = 1 ) ");
				
				foreach($email_result as $k=>$v){
					
					$repeat_name .= '"'.$v['buyer_name'].'",';
					
				}
				if($repeat_name){
					
					$repeat_name = rtrim($repeat_name,',');
					
					if($repeat_name){
						$sql = "delete from erp_orders_blacklist where buyer_name in(".$repeat_name.") and status=0";
						
						$ret = $this->model->return_query($sql);
						
						if($ret){
							
							echo "buyer_name为".$repeat_name."的待处理重复的黑名单数据已删除！<br>";
						}else{
							echo "待处理黑名单中没有重复的buyer_name数据!<br>";
						}
					}
				}
		}

echo "数据更新成功！";
echo "success";exit;
	}
	exit;
	}
	        //echo $this->db->last_query();
}
