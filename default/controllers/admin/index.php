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
		
//		$data = array();

		//今日生成订单总数,今日已发货总数
//		$data['total'] = 0;
//
//		$data['has_shipped'] = 0;
//
//		$data['need_pack'] = 0; //还需包装数
//
//		$data['print_page'] = 0;//今天已打印拣货单
		
		$new = array();
		
		$warehouseArr = array();//仓库
		
		$new['show'] = 0;//是否显示数据
		
		$key = $this->user_info->key;//用户组key
		
		if($key =='manager'){
			$warehouseArr['1000'] = '深圳一仓';
			$warehouseArr['1002'] = '深圳二仓';
			$warehouseArr['1025'] = '义乌仓';
			$new = $this->deal_with_today_data($new); 
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
	public function deal_with_today_data($new){
		
		$now_time = strtotime(date('Y-m-d'));
		
		$sql = "SELECT SUM(order_num) as num,pick_warehouse FROM erp_pick WHERE create_time>='".$now_time."' group by pick_warehouse";

		$total = $this->model->result_array($sql);
		
		if(!empty($total)){
			foreach($total as $v){
				$new[$v['pick_warehouse']]['total'] = $v['num'];
			}
		}

		$sql = "SELECT COUNT(DISTINCT pd.orders_id) as num,p.pick_warehouse FROM erp_pick p left join erp_pick_product pd on p.id=pd.pick_id WHERE pd.STATUS=4 AND pd.scan_time>='".$now_time."' group by p.pick_warehouse";

		$total = $this->model->result_array($sql);
		
		if(!empty($total)){
			
			foreach($total as $v){
				$new[$v['pick_warehouse']]['has_shipped'] = $v['num'];
			}
		}

		$sql = "SELECT COUNT(DISTINCT pd.orders_id) as num,p.pick_warehouse FROM erp_pick p left join erp_pick_product pd on p.id=pd.pick_id WHERE pd.STATUS=1 group by p.pick_warehouse";

		$total = $this->model->result_array($sql);

		if(!empty($total)){
			
			foreach($total as $v){
				$new[$v['pick_warehouse']]['need_pack'] = $v['num'];
			}
		}

		//今日打印拣货单数
		$sql = "SELECT count(*) as num,pick_warehouse FROM erp_pick WHERE status>=2 and create_time>='".$now_time."' group by pick_warehouse";

		$print_total = $this->model->result_array($sql);

		if(!empty($print_total)){
			
			foreach($print_total as $v){
				$new[$v['pick_warehouse']]['print_page'] = $v['num'];
			}
		}
		
		return $new;
	}
}

/* End of file index.php */
/* Location: ./defaute/controllers/index.php */


?>