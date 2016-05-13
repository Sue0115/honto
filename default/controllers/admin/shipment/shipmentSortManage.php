<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//物流分类
class shipmentSortManage extends Admin_Controller{
  
	function __construct(){
		
		parent::__construct();
		
		$this->load->model(array('sharepage','shipment_category_model'));
		
		$this->model = $this->shipment_category_model;

	}
	
	public function index(){

		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= config_item('site_page_num'); //每页显示个数
		
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like  = array();
		
		$string='';
		
		$search_data=$this->input->get_post('search');
		
		$shipSortName='';
		//根据物流分类短名称获取数据
		if(isset($search_data['shipSortName']) && $shipSortName=trim($search_data['shipSortName'])){
		 $like['shipmentCatName']=$shipSortName;
		 $string.='&search[shipSortName]='.$shipSortName;
		}
		$search_data['shipSortName'] = $shipSortName;
		
		$option	= array(
			'like'		=> $like,
			'page'		=> $cupage,
			'per_page'	=> $per_page,
		);
		
		$url = admin_base_url('shipment/shipmentSortManage?').$string;
		
		$shipmentSortList=$this->model->getAll($option,$return_arr);

		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

		$data=array(
			'shipmentSortList'	=>$shipmentSortList,
			'page'				=> $page,	
			'totals'			=> $return_arr ['total_rows'],	 //数据总数
			'search'			=> $search_data,
		);

		$this->_template('admin/shipment/shipmentSortManage_list',$data);
		
	}
	
	/**
	 * 新建物流查询地址
	 */
	public function info(){
		
	   if($this->input->is_post()){
	        $this->save();
	    }
	    
	    
	    //获取物流查询的具体信息并且修改物流查询信息	开始
		$id	  =	intval($this->input->get_post('id'));
		$opti = array();
		$shipmentTrackUrlInfo='';
		if($id>0){
			$opti['where'] = array('shipmentCatID'=>$id);
		}
		$shipmentSortManageInfo=$this->model->getOne($opti);

		//获取物流查询的具体信息并且修改物流查询信息      结束
	  $data=array(
	    'shipmentSortManageInfo'=>$shipmentSortManageInfo,
	  );
	    
	  $this->_template('admin/shipment/shipmentSortManage_info',$data);
	}
	
	public function save(){

	  $id = (int)$this->input->get_post('shipmentCatID');

	  $data=array(
		  'shipmentCatName'		=>$this->input->get_post('shipmentCatName'),
	    );
	    if($id>0){
	     //修改
	      $data['shipmentCatID']=$id;
	      $result=$this->model->update($data);

	    }else{
	     //添加物流查询网址
	     $result=$this->model->add($data);
	    }
		$info = $id ? '修改' : '添加';
		//信息返回操作
		if($result){
			$val = $id ? $id : $result;
			echo '{"info":"物流分类'.$info.'成功","status":"y","id":"'.$val.'"}';
		}else{
			echo '{"info":"物流分类'.$info.'失败","status":"n"}';
		}
	  die;
	}
}