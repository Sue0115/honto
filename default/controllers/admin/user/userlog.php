<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Userlog extends Admin_Controller {
	  
	function __construct() {
		   
		parent::__construct();
		
		$this->load->model(array('slme_userlog_model','sharepage'));
		$this->model = $this->slme_userlog_model;
	} 
		 
	function index(){
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= config_item('site_page_num'); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		if($this->user_info->id==1){
			
			$where['status >='] = -1;
		}else{
			
			$where['status'] = 1;
		}
		
		$loginname = htmlspecialchars($this->input->get_post("loginname"));
		
		$like = array();
		$string = '';
		if($loginname=trim($loginname)){
			
			$like['loginname'] = $loginname;
			
			$string .="loginname=".$loginname;
		}

		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where,
			'like'		=> $like,
			'order'		=> "logintime desc,lid desc",
		);
		
		$lc_list = $this->slme_userlog_model->getAll($options, $return_arr); //查询所有信息
		
		$url = admin_base_url('user/userlog?').$string;
		
		$page = $this->sharepage->showPage($url, $return_arr ['total_rows'], $cupage );

		$data = array(
			'lc_list'	=> $lc_list,
			'page'		=> $page,	
			'totals'	=> $return_arr ['total_rows'],	 //数据总数
		); 
		
		$this->_template('admin/user/userlog',$data);  
	} 
	 
}
 
?>