<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//多品扫描欠货
class scan_lacking extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'order/pick_model','operate_log_model',
								'order/pick_product_model','order/orders_model',
								'order/orders_products_model','print/products_data_model',
								)
							);
		$this->model = $this->orders_model;
	}
	
	function index(){
	  $this->_template('admin/order/scan_lacking');
	}
	
	function ajax_deal_order(){
		
	 $uid = $this->user_info->id;//登录用户id
		
	 $result['status'] = 0;
		
	 $data = $this->input->get_post('data');
	 
	 //拆分data，用_拆分
	 $arr = explode('_',$data);
	 
	 $pick_id = $arr[0];//拣货单id
	 
	 $basket_num = $arr[1];//篮子号
	 
	 if( !empty($pick_id) && !empty($basket_num) ){
	       $option = array();
		   $where = array();
		   $where = array('pick_id' => $pick_id,'basket_num' => $basket_num);
		   $option = array('where'=>$where);
		   $orders = $this->pick_product_model->getOne($option,true);
		   
	 	   if(empty($orders)){
	 	     $result['msg'] = '该拣货单下不存在该订单号';
	 	     echo json_encode($result);
	 	     die;
	 	   }
	 	   
	 	   //根据订单id获取订单信息
	 	   $orderInfo = $this->model->getOrderInfoByID($orders['orders_id']);
	 	   
	 	   if($orderInfo['orders_status'] !=4){
	 	     $result['msg'] = '该订单'.$orderInfo['erp_orders_id'].'不是已打印状态，不能扫描缺货';
	 	     echo json_encode($result);
	 	     die;
	 	   }
	 	   
	 	   //开启事务
	 	   $this->db->trans_begin();
	 	   
	 	   //订单更改为已通过，欠货
	 	   $datas['orders_status'] = 3;
	 	   $datas['orders_is_backorder'] = 1;
	 	   $options['where'] = array('erp_orders_id'=>$orders['orders_id']);
	 	   $tof = $this->model->update($datas,$options);
	 	   
	 	   if(empty($tof)){
	 	     $this->db->trans_rollback();
	 	     $result['msg'] = '修改订单状态失败';
	 	     echo json_encode($result);
	 	     die;
	 	   }
	 	   
	 	   //更改拣货单列表下sku的状态为8，已发货的不能更改
	 	   $datap['status'] = 8;
	 	   $optionp['where'] = array(
	 	     'orders_id' => $orders['orders_id'],
	 	     'pick_id' => $pick_id,
	 	     'status !=' => 4 
	 	   );
	 	   $pof = $this->pick_product_model->update($datap,$optionp);
	 	   if(empty($pof)){
	 	     $this->db->trans_rollback();
	 	     $result['msg'] = '该拣货单下'.$orders['orders_id'].'修改该订单的状态恢复为已通过失败';
	 	     echo json_encode($result);
	 	     die;
	 	   }
	 	   if($this->db->trans_status() === TRUE && $tof && $pof){
              $this->db->trans_commit();//事务结束
              //加入日志
			  $logData= array(
						      	'operateUser' =>$uid,
						      	'operateTime '=>date('Y-m-d H:i:s',time()),
					            'operateType' => 'update',
					            'operateMod' => 'ordersManage',
					            'operateKey' => $orders['orders_id'],
					            'operateText' => '货找面单退回,订单状态变为已通过,拣货单号'.$pick_id, 
					         );
			  $insertLog=$this->operate_log_model->add($logData);
			  $result['status'] = 1;
			  $result['msg'] = '扫描多品欠货订单退回成功，订单号'.$orders['orders_id'];
           }else{
              $this->db->trans_rollback();
              $result['msg'] = '扫描多品欠货订单退回失败';
           } 
	  }else{
	     $result['msg'] = '拣货单号或者订单号不存在，请重新扫描';
	  }
	  echo json_encode($result);
	  die;
	}
}