<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//物流渠道管理
class Shipment_channel extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();
		
		$this->load->model(array('sharepage','shipment_suppliers_model','slme_shipment_channel_model'));
		
		$this->model = $this->slme_shipment_channel_model;
		
	}
	
	function index(){
		
		$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like  = array();
		
		//搜索
		$search_data = $this->input->get_post('search');
		
		if(isset($search_data['channel_name']) && $channel_name = trim($search_data['channel_name'])){
			
			$like['channel_name'] = $channel_name;
			
			$string .= '&serach[channel_name]='.$channel_name;
			
		}
		
		if(isset($search_data['suppliers_id']) && $suppliers_id = $search_data['suppliers_id']){
			
			$where[$this->model->_table.'.suppliers_id'] = $suppliers_id;
			
			$string .= '&search[suppliers_id]='.$suppliers_id;
		}
		
		if($key == 'root'){//超级管理员
		
		}else if($key == 'manager'){//管理员
		
		}else{
			
			$where['user_id'] = $uid;
		}
		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		
		$data_list = $this->model->get_all_channel($options, $return_arr); //查询所有信息

		$url = admin_base_url('shipmemt/shipment_channel?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$shipment_suppliers = $this->shipment_suppliers_model->get_all_shipment_suppliers_obj();
		
		$data = array(
		    'key'                => $key,
			'data_list'	         => $data_list,
			'page'		         => $page,	
			'totals'	         => $return_arr ['total_rows'],	 //数据总数
			'search'    		 => $search_data,
			'shipment_suppliers' => $shipment_suppliers
		); 
		
		$this->_template('admin/shipment/shipment_channel_list',$data); 
		
	}
	
	function info(){
		
		parent::info();//调用父类info方法，如果有post数据则调用save方法
		
		$id	=	intval($this->input->get_post('id'));

		$options = array();
		
		if($id>0){
			$options['where'] = array('id'=>$id);
		}
		
		$item = $this->model->getOne($options);
		
		$shipment_suppliers = $this->shipment_suppliers_model->get_all_shipment_suppliers_obj();
		
		$data = array(
			'item'				 => $item,
			'shipment_suppliers' => $shipment_suppliers	
		); 

		$this->_template('admin/shipment/shipment_channel_info',$data);
		
	}
	
	function save(){
		
		$id = (int)$this->input->get_post('id');
		
		$data = array();
		
		$data['suppliers_id'] = trim($this->input->get_post('suppliers_id'));
		
		$data['channel_name'] = trim($this->input->get_post('channel_name'));
		
		if(empty($data['channel_name'])){
			echo '{"info":"物流渠道名称不允许为空","status":"n"}';exit();	
		}
		
		$log_data = array();
		
		$log_data['operate_user_id'] = $this->user_info->id;
		
		$log_data['operate_time'] = time();
		
		$log_data['operate_mod'] = 'shipment_channel/save';
		
		//事务开始
		$this->db->trans_begin();
			
		if($id){//修改
			
			$data['update_time'] = time();
			
			$options = array();
			
			$options['where']['id'] = $id;

			$tof = $this->model->update($data,$options);
			
			$log_data['operate_type'] = '2';
			
			$log_data['operate_key'] = $id;
			
			$log_data['operate_text'] = '修改物流渠道名称，改为：'.$data['channel_name'];
			
		}else{//新增
			
			$data['create_time'] = time();
			
			$data['update_time'] = $data['create_time'];
			
			$data['user_id'] = $this->user_info->id;
			
			$tof = $this->model->add($data);
			
			$log_data['operate_type'] = '1';
			
			$log_data['operate_key'] = $tof;
			
			$log_data['operate_text'] = '新增物流渠道：'.$data['channel_name'];
			
		}
		
		//写入操作日志
		$tof_log = $this->operate_model->add($log_data);
			
		$info = $id ? '修改' : '添加';
		
		if($this->db->trans_status() === TRUE && $tof && $tof_log){//操作成功
			
			$this->db->trans_commit();//事务结束
			
			$val = $id ? $id : $tof;
			
			echo '{"info":"'.$info.'成功","status":"y","id":"'.$val.'"}';
			
		}else{//操作失败
			
			$this->db->trans_rollback();
			
			echo '{"info":"'.$info.'失败","status":"n"}';
		}

		die;
	}
	
	
}