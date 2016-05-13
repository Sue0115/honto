<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Info extends Admin_Controller{
	
	function __construct(){
		parent::__construct();
		$this->model = $this->slme_user_model;
	}
	
	/**
	 * 
	 *修改个人资料
	 */
	function index(){

	    if($this->input->is_post()){
	        $this->updateUserInfo();
	    }
	    
		$user_id = $this->user_info->id;
		
		$user = $this->model->getOne(intval($user_id),TRUE);
		
		$data = array(
			'item'	=> $user,
		);
		
		$this->_template('admin/user/info/index',$data);
	}
	
	/**
	 * 
	 * 修改密码
	 */
	function modify_pwd(){

	    if($this->input->is_post()){
	        $this->updatePassword();
	    }
		$this->_template('admin/user/info/pwd');
		
	}
	
	function updateUserInfo(){
		
		$uid = $this->user_info->id;
		
		if(!$uid){
			echo '{"info":"修改失败，请重试","status":"n"}';exit();
		}
		
		$options = array();
		
		$options['where']['id'] = $uid;
		
		$data = array();
		
		$data['nickname'] = trim($this->input->get_post('nickname'));
		
		$data['email'] = trim($this->input->get_post('email'));
		
		$tof = $this->model->update($data,$options);
		
		//信息返回操作
		if($tof){
			echo '{"info":"修改成功","status":"y"}';exit();
		}else{
			echo '{"info":"修改失败","status":"n"}';exit();
		}
	}
	
	function updatePassword(){
		
		$uid = $this->user_info->id;
		
		if(!$uid){
			echo '{"info":"修改失败，请重试","status":"n"}';exit();
		}
		
		$options = array();
		
		$options['where']['id'] = $uid;
		
		$password = $this->input->get_post('password');
		
		$old_password = trim($this->input->get_post('old_password'));
		
		$user = $this->model->getOne($options,TRUE);
		
		$this->load->library('passwordhash');
		
		//判断旧密码是否正确
		if(!($this->passwordhash->CheckPassword($old_password, $user['password']))){
			echo '{"info":"旧密码不正确,修改失败","status":"n"}';exit();		
		}
		
		if(!$password){
			echo '{"info":"请输入新密码","status":"n"}';exit();
		}
		
		if(!is_password($password)){
				
			echo '{"info":"密码只充许字母、数字、下划线以及6到16个字符","status":"n"}';exit();		
		}
		
		
		
		$data = array();
		
		$data['password'] = $this->passwordhash->HashPassword($password);
		
		$tof = $this->model->update($data,$options);
		
		//信息返回操作
		if($tof){
			
			$this->load->helper('cookie');
        	
// 			delete_cookie('P');
        
//         	$this->auth->process_logout();
        	
			echo '{"info":"修改成功","status":"y"}';exit();
		}else{
			echo '{"info":"修改失败","status":"n"}';exit();
		}
		
		die;
	}
}
