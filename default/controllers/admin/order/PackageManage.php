<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//拣货单包裹管理
class PackageManage extends Admin_Controller{
	
	private $status_text = array(//拣货单状态
		'3' => '正在包装',
		'4' => '包装完成',
		'5' => '已标记发货'
	);

	private $type_text = array(//拣货单类型
		'1' => '单品单件',
		//'2' => '单品多件',
		//'3' => '多品多件'
	);
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'sharepage','order/pick_model','order/pick_product_model',
								)
							);

		$this->model = $this->pick_product_model;
		
	}
	
	//异常包裹列表方法
    function index(){
    	
        $key = $this->user_info->key;//用户组key
		
		$uid = $this->user_info->id;//登录用户id
		
		$string = '';
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array('status'=>9);
		
		$option=array(
		  'page'		=> $cupage,
		  'per_page'	=> $per_page,
		  'where' => $where,
		);
		
		$data_list=$this->model->getAll($option,$return_arr);

		$url = admin_base_url('order/PackageManage?').$string;
		
 		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

		$returnData=array(
		  'page'   => $page,	
		  'data'  => $data_list,
		);
		
		$this->_template('admin/order/abnormalPackage',$returnData);
		
    }
    
    //已包装的拣货单列表
    function hasPacking(){
    	
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

		$pick_id='';//拣货单号
		$pick_type='';//拣货单类型
		$pick_status='';//拣货单状态


		if(isset($search_data['pick_id']) && $pick_id = trim($search_data['pick_id'])){
			$where[$this->pick_model->_table.'.id'] = $pick_id;
			$string .= '&search[pick_id]='.$pick_id;
		}
		if(isset($search_data['pick_type']) && $pick_type = trim($search_data['pick_type'])){
			$where[$this->pick_model->_table.'.type'] = $pick_type;
			$string .= '&search[pick_type]='.$pick_type;
		}
		if(isset($search_data['pick_status']) && $pick_status = trim($search_data['pick_status'])){
			$where[$this->pick_model->_table.'.status'] = $pick_status;
			$string .= '&search[pick_status]='.$pick_status;
		}
	
		if($key == 'root'){//超级管理员
		
		}else if($key == 'manager'){//管理员
		
		}else{
			
		}
		
		$where['status >=']=3;
		//$where['pick_uid']=$uid;
		
		$search_data['pick_id'] = $pick_id;
		$search_data['pick_type'] = $pick_type;
		$search_data['pick_status'] = $pick_status;

		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		
		$data_list = $this->pick_model->getAll($options, $return_arr); //查询所有信息

		$c_url='order/PackageManage/hasPacking';
		
		$url = admin_base_url('order/PackageManage/hasPacking?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$returnData = array(
		    'c_url'              => $c_url, 
		    'key'                => $key,
			'data_list'	         => $data_list,
			'page'		         => $page,	
			'totals'	         => $return_arr ['total_rows'],	 //数据总数
			'search'    		 => $search_data,
		    'type_text'          => $this->type_text,
		    'status_text'        => $this->status_text,

		); 

		$this->_template('admin/order/hasPacking',$returnData);
		
    }
}
