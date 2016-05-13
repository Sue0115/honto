<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//物流管理
class Shipment_manage extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();
		
		$this->load->model(array('sharepage','shipment_model','slme_shipment_channel_model','slme_shipment_channel_shipment_model'));
		
		$this->model = $this->shipment_model;
		
	}
	
	/**
	 * 
	 * 查出所有本地仓物流
	 * @param $shipmentScanLocal
	 */
	function show_all_shipment($shipment_channel_id){
		
		//取出渠道信息
		$channel_options = array();
		$channel_options['where']['id'] = $shipment_channel_id;
		$shipment_channel = $this->slme_shipment_channel_model->getOne($channel_options);
		
		$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like  = array();
		
		$where_not_in = array();
		
		//搜索
		$search_data = $this->input->get_post('search');
		
		if(isset($search_data['shipmentTitle']) && $shipmentTitle = trim($search_data['shipmentTitle'])){
			
			$like['shipmentTitle'] = $shipmentTitle;
			
			$string .= '&serach[shipmentTitle]='.$shipmentTitle;
			
		}
		
		$where['shipmentScanLocal'] = 1;
		
		$where['shipmentEnable'] = 1;
		
		//查询出已经被绑定过的物流
		$shipment = $this->slme_shipment_channel_shipment_model->getAllObj(array('where'=>array('status'=>1)));
		if($shipment){
			$id_array = array();
			foreach ($shipment as $v){
				$id_array[] = $v->shipment_id;
			}
			$where_not_in['shipmentID'] = $id_array;
		}
		
		$options	= array(
			'page'		   => $cupage,
			'per_page'	   => $per_page,
			'where'		   => $where,
			'where_not_in' => $where_not_in,
			'like'		   => $like,
			'order'		   => "shipmentTitle desc",
		);
		
		$data_list = $this->model->getAll($options, $return_arr); //查询所有信息

		$url = admin_base_url('shipment/shipment_manage/show_all_shipment/'.$shipment_channel_id.'?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$data = array(
		    'key'                => $key,
			'data_list'	         => $data_list,
			'page'		         => $page,	
			'totals'	         => $return_arr ['total_rows'],	 //数据总数
			'search'    		 => $search_data,
			'shipment_channel' => $shipment_channel
		); 
		
		$this->template('admin/shipment/show_all_shipment_list',$data); 
		
	}
	
	/**
	 * 
	 * 把物流绑定到物流渠道
	 */
	function ajax_shipment_bind_shipment_channel(){
		
		$result = array();
		
		$result['status'] = 0;
		
		$result['info'] = '操作失败，请点击这里，重新操作';
		
		$data = array();

		$data['shipment_id'] = $this->input->get_post('shipment_id');
		
		$data['shipment_channel_id'] = $this->input->get_post('channel_id');
		
		if($data['shipment_id'] && $data['shipment_channel_id']){
			
			$data['create_time'] = time();
			
			$data['user_id'] = $this->user_info->id;
			
			$data['update_time'] = $data['create_time'];
			
			$tof = 0;
			
			$tof = $this->slme_shipment_channel_shipment_model->add($data);
			
			if($tof){
				
				$result['status'] = 1;
				
				$result['info'] = '操作成功';
				
				$log_data = array();
				
				$log_data['operate_user_id'] = $this->user_info->id;
				
				$log_data['operate_time'] = $data['create_time'];
				
				$log_data['operate_type'] = 1;
				
				$log_data['operate_mod'] = $this->router->class.'/'.$this->router->method;
				
				$log_data['operate_key'] = $data['shipment_channel_id'];
				
				$log_data['operate_text'] = '将物流ID：'.$data['shipment_id'].'，绑定到物流渠道ID：'.$data['shipment_channel_id'];
				
				$this->operate_model->add_log($log_data);
			}
		}
		
		echo json_encode($result);
	}
	
	/**
	 * 
	 * 查看物流渠道已绑定的物流
	 */
	function see_bind_shipment($shipment_channel_id){
		
		//取出渠道信息
		$channel_options = array();
		$channel_options['where']['id'] = $shipment_channel_id;
		$shipment_channel = $this->slme_shipment_channel_model->getOne($channel_options);
		
		$key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like  = array();
		
		$join = array();
		
		//搜索
		$search_data = $this->input->get_post('search');
		
		if(isset($search_data['shipmentTitle']) && $shipmentTitle = trim($search_data['shipmentTitle'])){
			
			$like['shipmentTitle'] = $shipmentTitle;
			
			$string .= '&serach[shipmentTitle]='.$shipmentTitle;
			
		}
		
		$where['shipment_channel_id'] = $shipment_channel_id;
		
		$where['status'] = 1;
		
		$join[] = array($this->model->_table,$this->model->_table.'.shipmentID='.$this->slme_shipment_channel_shipment_model->_table.'.shipment_id');
		
		$options	= array(
			'page'		   => $cupage,
			'per_page'	   => $per_page,
			'where'		   => $where,
			'join' 		   => $join,
			'like'		   => $like,
			'order'		   => "shipmentTitle desc",
		);
		
		$data_list = $this->slme_shipment_channel_shipment_model->getAll($options, $return_arr); //查询所有信息

		$url = admin_base_url('shipment/shipment_manage/see_bing_shipment/'.$shipment_channel_id.'?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$data = array(
		    'key'                => $key,
			'data_list'	         => $data_list,
			'page'		         => $page,	
			'totals'	         => $return_arr ['total_rows'],	 //数据总数
			'search'    		 => $search_data,
			'shipment_channel' => $shipment_channel
		); 
		
		$this->template('admin/shipment/see_bind_shipment_list',$data); 
	}
	
	/**
	 * 
	 * 把物流从物流渠道解除绑定
	 */
	function ajax_shipment_delete_shipment_channel(){
		
		$result = array();
		
		$result['status'] = 0;
		
		$result['info'] = '操作失败，请点击这里，重新操作';
		
		$options = array();
		
		$options['where']['id'] = intval($this->input->post('id'));
		
		$shipment_channel_id = $this->input->post('shipment_channel_id');
		
		$shipment_id = $this->input->post('shipment_id');
		
		if($options['where']['id'] && $shipment_channel_id && $shipment_id){
			
			$tof = 0;
			
			$tof = $this->slme_shipment_channel_shipment_model->delete($options);
			
			if($tof){
				
				$result['status'] = 1;
				
				$result['info'] = '操作成功';
				
				$log_data = array();
				
				$log_data['operate_user_id'] = $this->user_info->id;
				
				$log_data['operate_time'] = time();
				
				$log_data['operate_type'] = 3;
				
				$log_data['operate_mod'] = $this->router->class.'/'.$this->router->method;
				
				$log_data['operate_key'] = $shipment_channel_id;
				
				$log_data['operate_text'] = '将物流ID：'.$shipment_id.'，从物流渠道ID：'.$shipment_channel_id.'，解除绑定';
				
				$this->operate_model->add_log($log_data);
			}
		}
		
		echo json_encode($result);
	}
}
