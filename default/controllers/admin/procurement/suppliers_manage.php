<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//产品管理 库存销量
class Suppliers_manage extends Admin_Controller{
	
	function __construct()
	{
		parent::__construct();
		header("Content-type: text/html; charset=utf-8");
		$this->load->model(array('suppliers/suppliers_model', 'products/products_data_model'));
	}
	
	
	public function index ()
	{
		
		$get_arr = $this->input->get();
		
		//必须加载 products/products_data_model 才可以调用$this->sharepage
		$this->load->model(array('manages_model', 'slme/slme_group_model', 'procurement/procurement_model'));
		
		$procurement_options = array(
				'select' => array(
						'erp_slme_user.id', 'erp_slme_user.nickname'
				),
		
				'where' => array(
						'erp_slme_group.status'	=> 1,	//启用
						'erp_slme_user.status'	=> 1,
						'erp_slme_group.key'	=> 'purchase'	//采购
				),
				
				'join' => array(
					array('erp_slme_user', 'erp_slme_group.gid = erp_slme_user.gid', 'left')
				),
		);
		$procurement_user = $this->slme_group_model->getAll2Array($procurement_options);
		
		$procurement = array();
		foreach ($procurement_user as $value) {
			$procurement[$value['id']] = $value;
		}
		$procurement_user = $procurement;
		
		$data = array(
			'procurement_user' => $procurement_user,	//筛选 采购
		);
		
		if (!empty($get_arr))
		{
			
			$key = $this->user_info->key;//用户组key
			
			$get = array();
			//去掉空格
			foreach ($get_arr as $get_k => $get_v) {
				$get[$get_k] = trim($get_v);
			}
			
			
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
			
			$list = $this->suppliers_model->get_suppliers_list($get, $paging, $return_arr);	//得到显示数据
			$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );	//HTML分页条
			
			//每个供应商本月的采购总额
			$procurement_model = $this->procurement_model;
			
			$data['get'] = $get;
			$data['list'] = $list;
			$data['key']	= $key;
			$data['page']	= $page;
			$data['totals']	= $return_arr['total_rows'];	//数据总数
			$data['procurement_model']	= $procurement_model;
		}
		$this->_template('admin/procurement/suppliers_manage_list', $data);
	}
	
	//供应商 修改 添加
	public function info()
	{
		parent::info();//调用父类info方法，如果有post数据则调用save方法
		
		$get = $this->input->get();
		
		$options = array();
		
		$where = array();
		
		if (!empty($get['id']))
		{
			$where['suppliers_id'] = $get['id'];

			$options['where'] = $where;
		}

		$info = $this->suppliers_model->getOne($options, TRUE);
			
// 		$this->load->model(array('suppliers/suppliers_attachment_model'));
// 		$attachmentData = $this->suppliers_attachment_model->getAttachemntsWithSuppliersID($get['id']);
		
		$data = array(
			'info' => $info,
// 			'attachmentData' => $attachmentData	//附件 供应商营业证
		);
			
		$this->_template('admin/procurement/suppliers_manage_info', $data);
		
	}
	
	public function save()
	{
	
		$post = $this->input->post();
		
		if (!empty($post))
		{
			$post = arr_trim($post);
			
			$id = $post['id'];
			unset($post['id']);
			
			$data = array();
			foreach ($post as $k => $v)
			{
				if (!empty($v) OR $v === '0')
				{
					$data[$k] = $v;
				}
			}
			
			$info = $id ? '修改' : '添加';
			
			if (!empty($id))
			{
				$options = array(
					'where' => array(
						'suppliers_id' => $id
					)
				);
				
				$tof = $this->suppliers_model->update($data,$options);
				
			}else {
				$tof = $this->suppliers_model->add($data);
			}
			
			if ($tof)
			{
				echo '{"info":"'.$info.'成功","status":"y","id":"'.$id.'"}';
			}else {
				echo '{"info":"'.$info.'失败","status":"n"}';
			}
			
			
		}else {
			echo '{"info":"请填写数据","status":"n"}';
		}
		exit;
		
	}
	
	//显示供应商信息
	public function show()
	{
		$get = $this->input->get();
		
		$data = array();
		
		if (!empty($get['id']))
		{
			$options['where']['suppliers_id'] = $get['id'];
			
			$info = $this->suppliers_model->getOne($options, TRUE);
			
			$productsArray = $this->products_data_model->getAllProducts($get['id']);
			
			$data['info'] = $info;	//基本信息
			$data['productsArray'] = $productsArray;	//物品信息
		}
		
		$this->template('admin/procurement/suppliers_show_list', $data);
	}
	
	/**
	 * 操作日志
	 */
	public function log_list()
	{
		
		$get = $this->input->get();
		
		$data = array();
		
		if (!empty($get['operateKey']) && !empty($get['operateMod']))
		{
			$this->load->model(array('operate_log_model', 'manages_model'));
			
			$where = array(
				'operateKey'	=> $get['operateKey'],
				'operateMod'	=> $get['operateMod']
			);
			$logList = $this->operate_log_model->getLogList($where);
			$data['logList'] = $logList; //日志信息
			
			$manages_model = $this->manages_model;
			$data['manages_model'] = $manages_model; //操作人
		}
		
		$data['title'] = '操作日志';
		
		$this->template('admin/procurement/log_list', $data);
	}
}