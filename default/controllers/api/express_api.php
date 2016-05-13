<?php
class express_api extends MY_Controller{
	
	function __construct(){
		
		parent::__construct();
		$this->load->model(
							array(
								'order/orders_model','cnzexpress_model'
							)
		);
		$this->model = $this->orders_model;
	}
	
	public function get_api_data(){
	  
	  $track_number = strtoupper(trim($this->input->get_post('TrackNums')));

	  $is_orders = false;//是否根据订单号查找

	  if($track_number{0} == 'S'){
	  		$is_orders = true;
	  }

	  $token = $this->input->get_post('token');
	  
	  if($token !='salamoer.moonarstore'){
	  	die('');
	  }
	  
	  $orderInfo = array();//存放订单信息
	  
	  $shipmentSelect = array();//存放smt客户选择物流
	  
	  //根据挂号码获取订单信息是否存在
	  $where = array();
	  $option = array();
	  
	  $where = array(
	    'orders_status' => 5,
	    'orders_export_time >=' => date('Y-m-d',strtotime('-180 day'))
	  );

	  if($is_orders){
	  	$erp_orders_id = str_replace('S', '', $track_number);
	  	$where['erp_orders_id'] = $erp_orders_id;
	  }else{
	  	$where['orders_shipping_code'] = $track_number;
	  }

	  $option = array(
	    'where'  => $where
	  );
	  
	  $orderInfo = $this->model->getOne($option,true);

	  //如果找不到，再根据cnz_code 去查找
	  if(empty($orderInfo)){
		  unset($where['erp_orders_id']);
		  unset($where['orders_shipping_code']);
		  $where['cnz_code'] = $track_number;
		  $option['where'] = $where;
		  $orderInfo = $this->model->getOne($option,true);
	  }
	  
	  //如果订单号存在，查询erp_cnzexpress表
	  if($orderInfo){

	  	  if($is_orders){
	  	  	$orderInfo['orders_shipping_code'] = $track_number;
	  	  }

	      $cnexpress = $this->cnzexpress_model->getInfoByID($orderInfo['erp_orders_id']);
	      $num_array = array();
	      if($cnexpress){
	            $num_array[] = $cnexpress['num1'];
            	$num_array[] = $cnexpress['num2'];
            	$num_array[] = $cnexpress['num3'];
            	$num_array[] = $cnexpress['num4'];
	      }else{
	      		$data = array();
	      	    $data['erp_orders_id'] = $orderInfo['erp_orders_id'];
	            $num_array[] = $data['num1'] = rand(3,7);
            	$num_array[] = $data['num2'] = rand(11,20);
            	$num_array[] = $data['num3'] = rand(19,33);
            	$num_array[] = $data['num4'] = rand(29,53);
            	//插入erp_cnzexpress表
            	$this->cnzexpress_model->add($data);
	      }
	      
	      //处理smt的订单
	      if($orderInfo['orders_type']==6){
	         $shipmentSelect = explode('-',$orderInfo['ShippingServiceSelected']);
	      }
	      
	  }
	  
	  $return_data = array(
	    'orderInfo' => $orderInfo,
	    'num_array' => $num_array,
	    'shipmentSelect' => $shipmentSelect
	  );

	  echo json_encode($return_data);
	}
	
}