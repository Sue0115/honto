<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//产品管理 库存销量
class Inventory_sales extends Admin_Controller{
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	public function index ()
	{
		header("Content-type: text/html; charset=utf-8");
		
		$this->load->model(array('category_model', 'products/products_data_model', 'sangelfine_warehouse_model'));
		
		$key = $this->user_info->key;//用户组key
		$get_arr = $this->input->get();
		
		$get = array();
		if (!empty($get_arr))
		{
			//去掉空格
			foreach ($get_arr as $get_k => $get_v) {
				$get[$get_k] = trim($get_v);
			}
		}
		
		$productsTypeArray = $this->category_model->defineProductsType();	//物品分类
		$categoryArray = array();
		foreach ( $productsTypeArray as $cA ) {
			$categoryArray[$cA['category_parent_id']][] = $cA;
		}
		$productSort = $this->products_data_model->getProductSortNum();
		
		$string = '';
		foreach ($get as $k=>$v)
		{
			if ($k !== 'per_page')
			{
				$string .= '&'.$k.'='.$v;
			}
		}
		$url = admin_base_url('product/inventory_sales/index?').$string;
			
		$per_page	= (int)$this->input->get_post('per_page');	//当前页
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		$return_arr = array ('total_rows' => true );	//数据总数
		$paging = array(
				'per_page' => $per_page,
				'cupage' => $cupage,
		);
		$list = $this->products_data_model->category_products($get, $paging, $return_arr);	//得到显示数据
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );	//HTML分页条
		
		$warehouse_data =  $this->sangelfine_warehouse_model->get_all_warehouse();
		foreach ($warehouse_data as $k => $v) {
			$warehouse[$v['warehouseID']] = $v['warehouseTitle'];
		}
		
		$data = array(
			'key' => $key,
			'get' => $get,
			'categoryArray'	=> $categoryArray,	//分类
			'productSort' => $productSort,	//分类的产品个数
			'list'	=> $list,	//显示数据
			'warehouse'	=> $warehouse,	//仓库 key=>ID value=>仓库名
			'page'	=> $page,
			'totals'	=> $return_arr['total_rows'],	//数据总数
		);
		$this->_template('admin/product/inventory_sales_list', $data);
	}
	
	public function actual_stock_record ()
	{
		$this->load->model(array('stock_detail_operate_record_detail_model'));
		
		$get_arr = $this->input->get();
		
		if (!empty($get_arr['product_id']))
		{
			
			$get = array();
			//去掉空格
			foreach ($get_arr as $get_k => $get_v) {
				$get[$get_k] = trim($get_v);
			}
			
			$product_id = $get['product_id'];
			
			$list = $this->stock_detail_operate_record_detail_model->conditions_query($get);
			
			$text = array(
					'out'	=>	'出库',
					'in'	=>	'入库'
			);
			
			$data = array(
				'get'	=>	$get,
				'list'	=> $list,	//显示数据
				'product_id'	=>	$product_id,
				'text'	=>	$text
			);
			$this->template('admin/product/actual_stock_record_list', $data);
		}
	}
}