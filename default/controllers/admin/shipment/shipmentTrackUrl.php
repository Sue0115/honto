<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//物流查询网址
class shipmentTrackUrl extends Admin_Controller{
  
	function __construct(){
		
		parent::__construct();
		
		$this->load->model(array('sharepage','shipment_trackurl_model','sangelfine_warehouse_model','shipment/smt_user_tokens_model'));
		
		$this->model = $this->shipment_trackurl_model;

	}
	
	public function index(){

		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= config_item('site_page_num'); //每页显示个数
		
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like  = array();
		
		$string='';
	 
		$search_data=$this->input->get_post('search');
		
		$shortName='';
		//根据物流分类短名称获取数据
		if(isset($search_data['shortName']) && $shortName=trim($search_data['shortName'])){
		 $like['track_short_name']=$shortName;
		 $string.='&search[shortName]='.$shortName;
		}
		$search_data['shortName'] = $shortName;
		
		$select=array('track_id', 'track_short_name', 'track_url', 'track_method');

		$option	= array(
			'select'	=>$select,
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'like'		=> $like,
		);
		
		$url = admin_base_url('shipment/shipmentTrackUrl?').$string;
		
		$urlList=$this->model->getAll($option,$return_arr);
		
		$pageList = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

		$data=array(
			'urlList'	=>$urlList,
			'page'		=> $pageList,	
			'totals'	=> $return_arr ['total_rows'],	 //数据总数
			'search'	=> $search_data,
		);

		$this->_template('admin/shipment/shipmentTrackUrl_list',$data);
		
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
			$opti['where'] = array('track_id'=>$id);
		}
		$shipmentTrackUrlInfo=$this->model->getOne($opti);

		//获取物流查询的具体信息并且修改物流查询信息      结束
	  $data=array(
	    'shipmentTrackUrlInfo'=>$shipmentTrackUrlInfo,
	  );
	    
	  $this->_template('admin/shipment/shipmentTrackUrl_info',$data);
	}
	
	public function save(){

	  $id = (int)$this->input->get_post('track_id');
	  $user_id = $this->user_info->id;
	  $user = $this->slme_user_model->getOne(intval($user_id),TRUE);
	  $data=array(
		  'track_short_name'		=> addslashes($this->input->get_post('track_short_name')),
		  'track_url'				=> addslashes($this->input->get_post('track_url')),
		  'track_method'			=> addslashes($this->input->get_post('track_method')),
		  'track_data'				=> addslashes($this->input->get_post('track_data')),
		  'track_return_value'		=> addslashes(htmlspecialchars($this->input->get_post('track_return_value'))),
	  	  'track_query_url'			=> addslashes($this->input->get_post('track_query_url')),
	    );
	    if($id>0){
	     //修改
	      $data['track_id']=$id;
	      $result=$this->model->update($data);

	    }else{
	     //添加物流查询网址
	     $result=$this->model->add($data);
	    }
		$info = $id ? '修改' : '添加';
		//信息返回操作
		if($result){
			$val = $id ? $id : $result;
			echo '{"info":"物流查询网址'.$info.'成功","status":"y","id":"'.$val.'"}';
		}else{
			echo '{"info":"物流查询网址'.$info.'失败","status":"n"}';
		}
	  die;
	}
	public function delete(){
	  $id	  =	intval($this->input->get_post('id'));
	  $where  =array();
	  if($id>0){
	    $where['track_id']=$id;
	  }else{
	    echo '{"info":"该查询网址不存在","status":"n"}';
	  }
	  $options['where']=$where;
	  $result=$this->model->delete($options);
	  if($result){
	  	echo '{"info":"物流查询网址删除成功","status":"y","id":"'.$id.'"}';
	  }else{
	    echo '{"info":"物流查询网址删除失败","status":"n"}';
	  }
	}
}
