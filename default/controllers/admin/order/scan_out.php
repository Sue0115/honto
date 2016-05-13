<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//订单出库管理
class scan_out extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'sharepage','order/pick_model','stock_detail_operate_record_detail_model',
								'order/pick_product_model','order/orders_model',
								'order/orders_products_model','shipment/shipment_model',
								'order/order_type_model','sangelfine_warehouse_model',
								'order/pick_print_model','print/products_data_model',
								'slme_user_model','stock/stock_detail_model','country_model'
								)
							);
		$this->model = $this->orders_model;
	}
	
	function index(){
	   $this->_template('admin/order/orders_scan_out');
	}
	
	/**
	 * 查找该订单是否可以扫描
	 * 只有订单状态为已打印的订单才可以扫描出库
	 */
	function check_do_scan(){
		
		  $TrackNumber = trim($this->input->get_post('TrackNumber'));//传过来的挂号码

		  $result['status'] = false;//默认不能扫描出库
		  
		  $option = array();
		  $where = array();
		  $where = array('orders_shipping_code'=>$TrackNumber);
		  $option['where']=$where;
		  $orderInfo = $this->model->getOne($option,true);
	
		  /**
		   * 订单信息是否存在
		   */
		  if(empty($orderInfo)){
		      $result['msg'] = '订单不存在或者已拆分';
		      echo json_encode($result);die;
		  }

		  //订单处于打印状态
		  if($orderInfo['orders_status']!=4){
		      $result['msg']= '订单号'.$orderInfo['erp_orders_id'].'不是已打印的状态，不能出库';
		      echo json_encode($result);die;
		  }

		  $result['status'] = true;
		  
		  $result['erp_orders_id'] = $orderInfo['erp_orders_id'];
		 
		  echo json_encode($result);die;
	}
	
	/**
	 * 更加订单号获取sku信息
	 */
	function sku_info(){
	  
	  $orderID = $this->input->get_post('orderID');
	  
	  //根据订单号获取sku信息
	  $product_sku = $this->orders_products_model->get_product_by_order_id($orderID);
	  
	  $data = array('productData' => $product_sku);
	  
	  $this->_template('admin/order/scan_sku_info',$data);

	}
	
	/**
	 * 执行扣库存的方法
	 */
	function dealStock(){
		
	   $orderID = $this->input->get_post('orderID');
	  
	   //获取订单详情
	   $ordersInfo = $this->model->getOrderInfoByID($orderID);
	   
	   //获取订单产品表详情
	   $order_product = $this->orders_products_model->getProductSkuByOrderId($ordersInfo['erp_orders_id'],$ordersInfo['orders_warehouse_id']);
	   
	    $result['status'] = 0;

        $result['info'] = '数据为空';
        
        $tof_stock = true;//是否执行减库存
        
	   //事务开始
       $this->db->trans_begin();

	  foreach($order_product as $v){
	    //减掉相应库存
	     $tof = $this->stock_detail_model->add_or_reduce_stock($v['orders_sku'],$v['item_count'],$ordersInfo['orders_warehouse_id'],'-');
	  
	     //获取该产品的实库存
         $stockArr=$this->stock_detail_model->getStockBySku($v['orders_sku'],$ordersInfo['orders_warehouse_id']);
         if(!empty($stockArr)){
            $stock=$stockArr['actual_stock'];
         }else{
            $stock=0;
         }  
                 
		 if(empty($tof)){
	         $tof_stock = false;
	         $this->db->trans_rollback();
	         $result['info'] = 'SKU：'.$v['orders_sku'].'减库存失败';
	         return $result;
	     }else{
	       //减库存成功,添加库存进出记录，erp_stock_detail_operate_record_detail 表
	        $reData=array();
	        $reData=array(
	           'operate_type' => 'out',
	           'product_id'   => $v['products_id'],
	           'operate_count'=> $v['item_count'],
	           'stock'		   => $stock,
	           'operate_time' => date('Y-m-d H:i:s',time()),
	         );
	              
	        $this->stock_detail_operate_record_detail_model->add($reData);
	              
	     }
	
	  }
	  
	  if($tof_stock && $this->db->trans_status() === TRUE){
            $this->db->trans_commit();//事务结束
            $result['info'] = '扣库存成功';
            $result['status'] = 1;
            
      }
	 
     echo json_encode($result);
		 
	}
	
}
