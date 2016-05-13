<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Group extends Admin_Controller {
	  
	function __construct() {
		   
		parent::__construct();
		
		$this->load->model(array('slme_group_model','sharepage','slme_user_model'));
		$this->model = $this->slme_group_model;
	} 
		 
	function index(){
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= config_item('site_page_num'); //每页显示个数
		
		$return_arr = array ('total_rows' => true );

		$key = $this->user_info->key;//用户组key
		
		$where = array();
		if($key != 'root'){
			$where['status >='] = 0;
			$where['gid !='] = 1;
		}
		
		$title = htmlspecialchars($this->input->get_post("title"));
		
		$like = array();
		$string = '';
		if($title=trim($title)){
			
			$like['title'] = $title;
			$string .="title=".$title;
		}

		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'like'		=> $like,
			'where'		=> $where,
			'order'		=> 'gid desc',
		);
		
		$lc_list = $this->slme_group_model->getAll($options, $return_arr); //查询所有信息
		
		foreach ($lc_list as $k => $v) {
			$num = $this->slme_user_model->get_totoal_by_pid($v->gid);
			$lc_list[$k]->title = $v->title."({$num})";

		}
		
		$url = admin_base_url('user/group?');
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

		$data = array(
			'lc_list'	=> $lc_list,
			'page'		=> $page,	
			'totals'	=> $return_arr ['total_rows'],	 //数据总数
		); 
		
		$this->_template('admin/user/grouplist',$data);  
	} 
	 

	public function info(){

	    if($this->input->is_post()){
	        $this->save();
	    }
	    
		$gid	=	intval($this->input->get_post('gid'));
		$options = array();
		if($gid>0){
			$options['where'] = array('gid'=>$gid);
		}
		$item = $this->slme_group_model->getOne($options);
		if($item){
			$item->array_group = explode(",",$item->items);
		}else{
			$item->array_group = array();
		}
		
		$data = array(
		
			'item'	=> $item,	
			'colum_list' => $this->tree->getValueOptions()
		); 

		$this->_template('admin/user/groupinfo',$data); 
	}  
	
	public function save(){
		
		$gid = (int)$this->input->get_post('gid');
		
		$data['title'] = htmlspecialchars($this->input->get_post('title'));
		
		$items = $this->input->get_post('items');
		
		$data['items'] = @implode(",",$items);
		
		//保存信息
		if($gid>0){
			$data['gid'] = $gid;
			$result=$this->slme_group_model->update($data);
		}else{
			
			$result=$this->slme_group_model->add($data);
		}
		
		$info = $gid ? '修改' : '添加';

		//信息返回操作
		if($result){
			
			$val = $gid ? $gid : $result;
			
			echo '{"info":"'.$info.'成功","status":"y","id":"'.$val.'"}';
		}else{
			
			echo '{"info":"'.$info.'失败","status":"n"}';
		}
		
		die;
	}
	
	
}
 
?>