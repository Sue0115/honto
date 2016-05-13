<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class allow extends Admin_Controller {
	  
	function __construct() {
		   
		parent::__construct();
		
		$this->load->model('allow_ip_model');
		$this->model = $this->allow_ip_model;
	} 

	function index(){
		//管理权限判断
		$qx = 1;//权限
		if ($this->user_info->key != 'root') {
			$list        = explode(",",$this->user_info->items);
			if(!in_array('176', $list)){
				echo "<meta charset='utf-8'/>你没有权限操作，请联系管理员!!";exit;
			}
			if(!in_array('177', $list)){
				//没有增加权限，不去取出修改状态功能
				$qx = 2;
			}
		}
		$searip =  $this->input->get_post('searip');
		$searstatus =  $this->input->get_post('searstatus');
		$where = array();
		if(($searip=='') && ($searstatus =='')){
			$where = array('status'=>1);//默认选出可用IP
			$searstatus=1;
		}
		if($searip){
			if($searstatus ==2){
				//全部状态
				$where = array('ip'=>$searip);
			}else{
				$where = array('ip'=>$searip,'status'=>$searstatus);
			}
			
		}
		if(($searstatus != '')  && ($searstatus != 2) && !$searip){
			$where = array('status'=>$searstatus);
		}
		$options['where'] = $where;
		$list=$this->model->getAll2Array($options);
		$lists =array();
		foreach($list as $v){
			if($v['ip'] != '116.25.34.25'){
				//IT部所用IP禁止被管理
				$lists[]=$v;
			}
		}
		$data = array(
        			'list'    => $lists,
        			'page'       => '',
        			'searip'	=>$searip,
        			'searstatus'=>$searstatus,
        			'qx'=>$qx
        		);
		$this->_template('admin/setting/allowlist',$data);
	}
	public function ipadd(){
		//管理权限判断
		if ($this->user_info->key != 'root') {
			$list        = explode(",",$this->user_info->items);
			if(!in_array('177', $list)){
				echo "<meta charset='utf-8'/>你没有权限操作，请联系管理员!!";exit;
			}
		}
		if($_POST){
			$ip = $this->input->get_post('ip');
			$remark = $this->input->get_post('remark');
			$status = $this->input->get_post('status');
			$ips = $this->model->getAll2Array();
			$allowIp = array();
	            foreach($ips as $v){
	                    $allowIp[] = $v['ip'];
	         }
			if(in_array($ip,$allowIp)){
				ajax_return('IP地址已经存在，无需再次添加',0);
			}
			$data = array('ip'=>$ip,'remark'=>$remark,'status'=>$status);
			$res=$this->model->add($data);
			if($res){
				ajax_return($ip);
			}else{
				ajax_return('失败',0);
			}
		}
		$this->_template('admin/setting/ipAdd');
	}
	public function ipdel(){
		if($_POST){
			$id = $this->input->get_post('id'); 
			$options['where'] = array('id'=>$id);
			$res = $this->model->delete($options);
			if($res){
				ajax_return('删除成功');
			}else{
				ajax_return('删除失败',0);
			}
		}
	}
	public function ipModify(){

		if($_POST){
			$id = $this->input->get_post('id');
			$ip = $this->input->get_post('ip');
			$remark = $this->input->get_post('remark');
			$status = $this->input->get_post('status');
			$where['where'] = array('id'=>$id);
			$data = array('ip'=>$ip,'remark'=>$remark,'status'=>$status);
			$res = $this->model->update($data,$where);
			if($res){
				ajax_return('修改成功');
			}else{
				ajax_return('修改失败',0);
			}
		}
		$id = (int)$_GET['id']; 
		$options['where'] = array('id'=>$id);
		$ipdata = $this->model->getOne($options);
		$data['ipdata']=$ipdata;
		$this->_template('admin/setting/ipModify',$data);
	}
}