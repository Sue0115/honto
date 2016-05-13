<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//扫描分货
class Shipment_scan extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();
		
		$this->load->model(array('sharepage','order/orders_model','shipment/shipped_scan_model','shipment/shipment_scan_temporary_model'));
		
		$this->model = $this->shipped_scan_model;
		
	}
	
	function index(){
		
		$uid = $this->user_info->id;//登录用户id

		$now_day = strtotime(date('Y-m-d'));

		$data['total'] = 0;

		//今日已分货
		$sql = "select count(*) as num from erp_shipped_scan where uid='{$uid}' and scan_time>='{$now_day}'";
		
		$total = $this->model->query_array($sql);

		$options = array();

		$where = array();

		$where['uid'] = $uid;

		$options['where'] = $where;

		$data['shipment_data'] = $this->shipment_scan_temporary_model->getAll2Array($options);

		if(!empty($total['num'])){
			$data['total'] = $total['num'];
		}
		
		$this->_template('admin/shipment/shipment_scan_index',$data);  
		
	}
	
	function info(){
		
		
	}
	
	function save(){
		
		
	}

	function ajax_scan_for_shipping(){

		$uid = $this->user_info->id;//登录用户id

		$result = array();

		$result['info'] = '非法请求';

		$result['status'] = 0; //1扫描成功

		$result['shipment_id'] = 0;

		$shipping_code = trim($this->input->get_post('shipping_code'));

		$order = $this->orders_model->get_order_info_and_shipment_info($shipping_code);

		//找不到订单
		if(empty($order)){
			$result['info'] = '挂号码：'.$shipping_code.'有误，找不到订单';
			echo json_encode($result);die;
		}

		$count = count($order);

		//挂号码存在2个订单
		if($count !=1){
			$str = '';
			foreach ($order as $o) {
				$str .= $o['erp_orders_id'].' ';
			}
			$result['info'] = '挂号码：'.$shipping_code.'存在超过一个订单（'.$str.'），请检查';
			echo json_encode($result);die;
		}

		$order = $order[0];

		//订单不是发货状态
		if($order['orders_status'] != 5){
			$result['info'] = '挂号码：'.$shipping_code.'订单（'.$order['erp_orders_id'].'）,不是发货状态，请拿回重新扫描出库扣库存';
			echo json_encode($result);die;
		}

		//
		$result['info'] = $order['shipmentID'].'-'.$order['shipmentTitle'].' (追踪码：'.$shipping_code.',订单号：'.$order['erp_orders_id'].')';

		$result['shipment_id'] = $order['shipmentID'];
		//看下是否已经存在扫描发货表中
		$options = array();

		$where = array();

		$where['orders_id'] = $order['erp_orders_id'];

		$where['shipping_code'] = $shipping_code;

		$options['where'] = $where;

		$shipment_scan = $this->model->getOne($options,true);

		$tof = false;
		//不存在表中
		if(empty($shipment_scan)){

			$data = array();

			$data['orders_id'] = $order['erp_orders_id'];

			$data['shipping_code'] = $shipping_code;

			$data['shipment_id'] = $order['shipmentID'];

			$data['shipment_name'] = $order['shipmentTitle'];

			$data['shipment_category_id'] = $order['shipmentCategoryID'];

			$data['uid'] = $uid;

			$data['scan_time'] = time();

			//事务开始
        	$this->db->trans_begin();

			$tof = $this->model->add($data);

			if(empty($tof)){
				$this->db->trans_rollback();
				$result['info'] = '请重新扫描';
				echo json_encode($result);die;
			}

			//写入临时表
			$data = array();

			$data['shipment_id'] = $order['shipmentID'];

			$data['shipment_name'] = $order['shipmentTitle'];

			$data['uid'] = $uid;

			$temporary = $this->shipment_scan_temporary_model->add_num($data,$data);

			if(!$temporary){
				$this->db->trans_rollback();
				$result['info'] = '请重新扫描';
				echo json_encode($result);die;
			}

			if($tof && $temporary && $this->db->trans_status() === TRUE){
				$this->db->trans_commit();//事务结束
				$result['status'] = 1;
			}

		}

		if(!empty($shipment_scan)){
			$result['info'] .= '-已扫描过';
		}

		echo json_encode($result);die;

	}

	//清空物流分货临时表
	function ajax_clean_erp_shipment_scan_temporary(){

		$id = $this->input->get_post('id');

		$result['status'] = 0;

		$result['info'] = '清零失败，请重新操作';

		$where = array();

		$where['id'] = (int)$id;

		$tof = $this->shipment_scan_temporary_model->delete($where);

		if($tof){
			$result['status'] = 1;
		}

		echo json_encode($result);

	}

	function ajax_erp_shipment_scan_temporary_info(){

		$result = array();

		$uid = $this->user_info->id;//登录用户id

		$options = array();

		$where = array();

		$where['uid'] = $uid;

		$options['where'] = $where;

		$data = $this->shipment_scan_temporary_model->getAll2Array($options);

		$result['data'] = $data;

		echo json_encode($result);
	}
	
	
}