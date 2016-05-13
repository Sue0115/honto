<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//产品管理 出入库统计
class Input_output_total extends Admin_Controller{
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$key = $this->user_info->key;//用户组key
		$get = $this->input->get();
		$data = array(
			'key' => $key,
			'get' => $get
		);
		
		if (!empty($_GET))
		{
			
			$string = '';
			foreach ($_GET as $k=>$v)
			{
				if ($k !== 'per_page')
				{
					if ($k == 'method')
					{
						foreach ($v as $value){
							$string .= '&method[]'.'='.$value;
						}
					}else {
						$string .= '&'.$k.'='.$v;
					}
				}
			}
			$url = admin_base_url('product/input_output_total/index?').$string;
			
			$per_page	= (int)$this->input->get_post('per_page');	//当前页
			$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
			$return_arr = array ('total_rows' => true );	//数据总数
			$paging = array(
				'per_page' => $per_page,
				'cupage' => $cupage,
			);
			
			$this->load->model(array('products/products_data_model', 'slme_user_model'));
			$recordList = $this->products_data_model->createTempTable($_GET, $paging, $return_arr);
			
			$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
			
			if (!empty($_GET['method']) && in_array(6, $_GET['method']))
			{
				foreach ($recordList as $k=>$v){
					$recordList[$k]['type'] = 6;
					$recordList[$k]['reason'] = '到货未入';
				}
			}
			
			if (!empty($_GET['method']) && in_array(4, $_GET['method']))	//销售出库的表没有产品名跟价格 可通过SKU获取
			{
				foreach ($recordList as $k=>$v){
					$recordList[$k]['type'] = 4;
					$recordList[$k]['reason'] = '销售';
					
					$cn_ay = $this->products_data_model->get_products_data(array('products_name_cn', 'products_value'), array('products_sku'=> $v['sku']));
					$recordList[$k]['cn'] = (empty($cn_ay['products_name_cn'])) ? NULL : $cn_ay['products_name_cn'];
					$recordList[$k]['value'] = (empty($cn_ay['products_value'])) ? NULL : $cn_ay['products_value'];
				}
			}
			
			$user_info = $this->slme_user_model->get_all_user_info();
			foreach ($recordList as $user_k => $user_v){
				$recordList[$user_k]['user_id'] = (empty($user_info[$user_v['user_id']])) ? $user_v['user_id'] : $user_info[$user_v['user_id']];
			}
			
			$data['recordList'] = $recordList;
			$data['page'] = $page;
			$data['totals'] = $return_arr ['total_rows'];	 //数据总数
			
		}
		
		$this->_template('admin/product/input_output_total_list', $data);
	}

	/**
	 * 统计采购到货的SKU总数和销售的订单总数(可用发货统计来计算)
	 */
	public function statistics(){
		$gets = $this->input->get();

		$start_date = empty($gets['start_date']) ? date('Y-m-d', strtotime('-1 day')) : $gets['start_date'];
		$end_date = empty($gets['end_date']) ? date('Y-m-d', strtotime('-1 day')) : $gets['end_date'];

		$this->load->model(array('products/products_data_model'));
		$recordList = $this->products_data_model->countPurchaseArrivalAndOrderShipped($start_date, $end_date);

		$data = array(
			'recordList' => $recordList,
			'start_date' => $start_date,
			'end_date'   => $end_date
		);
		$this->_template('admin/product/statistics_list', $data);
	}
}