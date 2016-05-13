<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//eub回邮地址管理
class backAddress extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								 'eub_back_address_model'
								)
							);
		$this->model = $this->eub_back_address_model;
		
	}
    
	function index(){
	  $option['select']=array('eub_setting_title','id');
	  $result=$this->model->getAll2array($option);
	  $data=array(
	    'eubList' => $result
	  );
	  $this->_template('admin/shipment/backAddress',$data);
	}
	
	//添加或者修改eub列表
	function info(){
	   if($this->input->is_post()){
	        $this->save();
	   }
	   $id=$this->input->get_post('id');
	   //根据id获取详细信息
	   $option = array();
	   if($id > 0){
	    	$option['where']=array('id' => $id);
	   }
	   $data=$this->model->getOne($option,true);
	   $returnData=array(
	     'backData' => $data,
	   );
	  $this->_template('admin/shipment/backAddressInfo',$returnData);
	}
	
	//保存传过来的数据
	function save(){
	  $data=$this->input->post();
	  $id = (int)$this->input->get_post('id');
	   if($id>0){
	     //修改
	      $data['id']=$id;
	      $result=$this->model->update($data);
	    }else{
	     //添加回邮地址
	     $result=$this->model->add($data);
	    }
		$info = $id ? '修改' : '添加';
		//信息返回操作
		if($result){
			$val = $id ? $id : $result;
			echo '{"info":"回调地址'.$info.'成功","status":"y","id":"'.$val.'"}';
		}else{
			echo '{"info":"回调地址'.$info.'失败","status":"n"}';
		}
	  die;
	}
}

