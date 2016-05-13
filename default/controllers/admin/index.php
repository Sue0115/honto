<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();
		$this->load->model(array(
								'order/pick_model','sangelfine_warehouse_model',
								'order/pick_product_model','order/orders_model',
								'order/orders_products_model','print/products_data_model'
								)
							);
		$this->model = $this->pick_model;
	}
	
	public function index(){
		
		$new = array();
		
		$warehouseArr = array();//仓库
		
		$new['show'] = 0;//是否显示数据
		
		$key = $this->user_info->key;//用户组key
		
		if($key =='manager'){
			$warehouseArr['1000'] = '深圳一仓';
			$warehouseArr['1002'] = '深圳二仓';
			$warehouseArr['1025'] = '义乌仓';
			$new = $this->deal_with_today_data($new,$warehouseArr); 
			$new['show'] = 1;
		}

		$newData = array(
		   'data'  =>  $new,
		   'warehouse'  =>  $warehouseArr
		);
	    $this->_template('admin/index',$newData);
// 		$this->_template("admin/index.html");	
	}
	
	//整理今日数据
	public function deal_with_today_data($new,$warehouseArr){
		$str_one = "LEFT JOIN erp_pick_product pd ON p.id = pd.pick_id
				INNER JOIN  `erp_products_data` d ON  `d`.`products_sku` =  `pd`.`product_sku` 
				AND d.product_warehouse_id = p.warehouse
				AND (
				d.products_location NOT LIKE  'B%'
				AND d.products_location NOT LIKE  'A%'
				AND d.products_location NOT LIKE  '.%'
				)";
		$str_two = "LEFT JOIN erp_pick_product pd ON p.id = pd.pick_id
				INNER JOIN  `erp_products_data` d ON  `d`.`products_sku` =  `pd`.`product_sku` 
				AND d.product_warehouse_id = p.warehouse
				AND (
				d.products_location LIKE  'B%'
				OR d.products_location LIKE  'A%'
				OR d.products_location LIKE  '.%'
				)";
		foreach($warehouseArr as $k=>$v){
			
		$now_time = strtotime(date('Y-m-d'));
		if($k == 1025){       //义乌仓统计
			$sql = "SELECT SUM(order_num) as num,warehouse FROM erp_pick WHERE create_time>='".$now_time."' and warehouse=1025";
		}else if($k == 1002){   //深圳2仓统计
			$sql = "SELECT COUNT( DISTINCT pd.orders_id ) AS num, p.warehouse
				FROM erp_pick p
				".$str_one."
				WHERE pd.create_time >=  '".$now_time."'
				AND p.warehouse =1000 AND p.status <4";
		}else if($k == 1000){   //深圳1仓统计
			$sql = "SELECT COUNT( DISTINCT pd.orders_id ) AS num, p.warehouse
				FROM erp_pick p
				".$str_two."
				WHERE pd.create_time >=  '".$now_time."'
				AND p.warehouse =1000 AND p.status <4
				";
		}
		
		$total = $this->model->result_array($sql);
		
		if(!empty($total)){
			foreach($total as $v){
				$new[$k]['total'] = $v['num']; //今日生成需包装订单总数
			}
		}
		
		if($k == 1025){
			$sql = "SELECT COUNT(DISTINCT pd.orders_id) as num,p.warehouse FROM erp_pick p left join erp_pick_product pd on p.id=pd.pick_id WHERE pd.STATUS=4 AND pd.scan_time>='".$now_time."' and p.warehouse=1025";
        
		}else if($k == 1002){
			$sql = "SELECT COUNT( DISTINCT pd.orders_id ) AS num, p.warehouse
				FROM erp_pick p
				".$str_one."
				WHERE pd.STATUS =4
				AND pd.scan_time >=  '".$now_time."'
				AND p.warehouse =1000";
		}else if($k == 1000){
			$sql = "SELECT COUNT( DISTINCT pd.orders_id ) AS num, p.warehouse
				FROM erp_pick p
				".$str_two."
				WHERE pd.STATUS =4
				AND pd.scan_time >=  '".$now_time."'
				AND p.warehouse =1000";
		}
		$total = $this->model->result_array($sql);
		
		if(!empty($total)){
			
			foreach($total as $v){
				$new[$k]['has_shipped'] = $v['num'];  //今日已发货数
			}
		}
		if($k == 1025){
			$sql = "SELECT COUNT(DISTINCT pd.orders_id) as num,p.warehouse FROM erp_pick p left join erp_pick_product pd on p.id=pd.pick_id WHERE pd.STATUS=1  and p.warehouse=1025";
		
		}else if($k == 1002){
			$sql = "SELECT COUNT( DISTINCT pd.orders_id ) AS num, p.warehouse
				FROM erp_pick p
				".$str_one."
				WHERE pd.STATUS =1 and p.warehouse=1000";
		}else if($k == 1000){
			$sql = "SELECT COUNT( DISTINCT pd.orders_id ) AS num, p.warehouse
				FROM erp_pick p
				".$str_two."
				WHERE pd.STATUS =1
				AND p.warehouse =1000";
		}
		$total = $this->model->result_array($sql);

		if(!empty($total)){
			
			foreach($total as $v){
				$new[$k]['need_pack'] = $v['num'];   //等待包装数
			}
		}

		//今日打印拣货单数
		$sql = "SELECT count(*) as num,warehouse FROM erp_pick WHERE status>=2 and create_time>='".$now_time."' group by warehouse";

		$print_total = $this->model->result_array($sql);

		if(!empty($print_total)){
			
			foreach($print_total as $v){
				$new[$v['warehouse']]['print_page'] = $v['num'];  //今日打印拣货单数
			}
		}
		}
		
		return $new;
	}
}

/* End of file index.php */
/* Location: ./defaute/controllers/index.php */


?>