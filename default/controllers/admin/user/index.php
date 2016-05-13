<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Index extends Admin_Controller {
	  
	function __construct() {
		   
		parent::__construct();
		
		$this->load->model(array('slme_group_model','sharepage','sangelfine_warehouse_model','manages_model'));
		$this->model = $this->slme_user_model;

	} 

	function index(){

		$key = $this->user_info->key;//用户组key

		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= intval($this->config->item('site_page_num')); //每页显示个数

		//用户组
		$options = array();

		$where = array();

		$where['gid >'] = 1;

		$where['status'] = 1;

		$options['where'] = $where;
		$options['order'] = 'title';
		$group_list = array();

		foreach ($this->slme_group_model->getAll($options) as $tmp){
			$group_list[$tmp->gid] = $tmp->title;
		}

		//所在仓库
		$warehouse = $this->sangelfine_warehouse_model->get_all_warehouse(true);

		//用户状态
		$user_status = array('0'=>'禁用','1'=>'启用');

		$options = array();

		$return_arr = array ('total_rows' => true );
		$where = array();
		$like  = array();
		if($key != 'root' && $key != 'manager'){
			$where[$this->model->_table.'.status >='] = 0;
			$where['pid']        = $this->user_info->id;
		}else{
			$where['id !=']        = 1000;
		}

		if($key !='root'){
			$where[$this->model->_table.'.status >='] = 0;
		}

		$search = array();

		$search['status_id'] = '';

		$search['group_id'] = '';

		$search['warehouse_id'] = '';

		$user_name = htmlspecialchars($this->input->get_post("user_name"));
		$string    = '';
		if($user_name=trim($user_name)){
			
			$like['user_name']  = $user_name;
			$string .="user_name=".$user_name;
		}

		$nickname     = htmlspecialchars($this->input->get_post("nickname"));
		
		if($nickname  = trim($nickname)){
			
			$like['nickname']  = $nickname;
			$string .="nickname=".$nickname;
		}

		$group_id = $this->input->get_post("group_id");

		if($group_id){
			$where['erp_slme_user.gid'] = $group_id;
			$string .="group_id=".$group_id;
			$search['group_id'] = $group_id;
		}

		$status_id = $this->input->get_post("status_id");
		
		if($status_id != false && $status_id >= 0){
			$where['erp_slme_user.status'] = $status_id;
			$string .="status_id=".$status_id;
			$search['status_id'] = $status_id;
		}

		$warehouse_id = $this->input->get_post("warehouse_id");
		if($warehouse_id){
			$where['warehouse_id'] = $warehouse_id;
			$string .="warehouse_id=".$warehouse_id;
			$search['warehouse_id'] = $warehouse_id;
		}

		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where,
			'like'		=> $like,
			'order'		=> "id desc",
		);
		
		$lc_list = $this->slme_user_model->getAllLc($options, $return_arr); //查询所有信息
		
		//print_r($options);die;
		
		$url = admin_base_url('user/index?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$data = array(
			'lc_list'	=> $lc_list,
			'page'		=> $page,	
			'totals'	=> $return_arr ['total_rows'],	 //数据总数
			'group_list'=> $group_list,
			'user_status'=> $user_status,
			'warehouse' => $warehouse,
			'search'    => $search
		); 
		
		$this->_template('admin/user/userlist',$data);  
	} 
	 

	public function info(){

		$key = $this->user_info->key;//用户组key

		//所在仓库
		$warehouse = $this->sangelfine_warehouse_model->get_all_warehouse(true);

	    if($this->input->is_post()){
	        $this->save();
	    }
	    
		$id	  =	intval($this->input->get_post('uid'));
		$options = array();
		if($id>0){
			$options['where'] = array('id'=>$id);
		}
		$item = $this->slme_user_model->getOne($options);

		$where['status'] = 1;
		$options['order'] = 'title';
		if($this->user_info->gid == 0){
			
		}else if ($this->user_info->gid == 1){
			$where['gid >'] = 1;
		}else{
			$where['gid >'] = 2;
		}

		$options['where'] = $where;

		$group_list = array();
		foreach ($this->slme_group_model->getAll($options) as $tmp){
			$group_list[$tmp->gid] = $tmp->title;
		}
		$data = array(
			'item'	        => $item,	
			'group_list'	=> $group_list,
			'warehouse'     => $warehouse,
			'key'           => $key 
		); 
		$this->_template('admin/user/userinfo',$data); 
	}  
	
	public function save(){
		
		$key = $this->user_info->key;//用户组key

		$id = (int)$this->input->get_post('id');

		//erp_manages表中的ID，速卖通要用
		$data['old_id'] = (int)$this->input->get_post('old_id');
		
		$data['user_name'] = trim($this->input->get_post('user_name'));

		$data['warehouse_id'] = trim($this->input->get_post('warehouse_id'));
		
		$data['pid'] = $this->user_info->id;
		
		//地区
		$data['district'] = $this->input->get_post('district');

		$password = trim($this->input->get_post('password'));

		//判断用户名是否已经存在
		$user_options['where']['user_name'] = $data['user_name'];
		$has_user = $this->slme_user_model->getOne($user_options,true);

		if($has_user && $id != $has_user['id']){
			echo '{"info":"账号已存在，请修改账号","status":"n"}';exit();	
		}
		
		if(!$id){
			
			if(!is_username($data['user_name'])){
				
				echo '{"info":"账号只充许字母开头，允许5-16字节，允许字母数字下划线","status":"n"}';exit();		
			}
			
			if(!is_password($password)){
				
				echo '{"info":"密码只充许字母、数字、下划线以及6到16个字符","status":"n"}';exit();		
			}
			
			$this->load->library('passwordhash');
			
			$data['password'] = $this->passwordhash->HashPassword($password);
						
		}

		if($id && ($key == 'root' || $key == 'manager')){

			if($password){

				if(!is_password($password)){
					
					echo '{"info":"密码只充许字母、数字、下划线以及6到16个字符","status":"n"}';exit();		
				}
				
				$this->load->library('passwordhash');
				
				$data['password'] = $this->passwordhash->HashPassword($password);

			}

		}
		
		$data['gid'] = (int)$this->input->get_post('gid');
		
		// if (!$data['old_id']){ //速卖通业务
		// 	echo json_encode(array('info' => '必须老ERP账号ID', 'status' => 'n'));
		// 	exit();
		// }
		//新增用户时旧ID处理
		if(!$id){
			$olddata = $this->manages_model->getAll2Array();
			$oldids = array();//旧erp的id
			foreach($olddata as $v){
				$oldids[] = $v['id'];
			}
			$newdata = $this->model->getAll2Array();
			$newids = array();//新erp的id
			foreach($newdata as $v2){
				$newids[] = $v2['id'];
			}
			//判断ID是否存在;
			if($data['old_id'] != '0'){
				if(in_array($data['old_id'],$newids)){
					echo json_encode(array('info' => '老ERP账号ID已经存在,请重新输入', 'status' => 'n'));
			 		exit();
				}
			}
			
			$newid = $this->urandom(6,$oldids,$newids);//随机生成6位的ID
			$data['old_id']=$newid;
		}
		
		$data['email'] = $this->input->get_post('email');
		
		if(!is_email($data['email']) and trim($data['email'])){
			
			echo '{"info":"邮箱地址错误,长度在于6,格式xxx@xxx.com","status":"n"}';exit();		
		}
		
		$data['nickname'] = htmlspecialchars($this->input->get_post('nickname'));
		
		//保存信息
		if($id>0){
			$data['id'] = $id;
			$result=$this->slme_user_model->update($data);
		}else{
			
			$data['regip'] = $this->egetip();
			
			$data['regtime'] = time();

			$result=$this->slme_user_model->add($data);

			$data['id'] = $result;
		}

		if($result){
				$options['where']['id'] = $data['id'];
				$old_id = $data['old_id'];
				$data = array();
				$data['id'] = $old_id;
				$tof = $this->slme_user_model->update($data,$options);
		}
		
		$info = $id ? '修改' : '添加';
		//信息返回操作
		if($result){
			
			$val = $id ? $id : $result;
			
			echo '{"info":"'.$info.'成功","status":"y","id":"'.$val.'"}';
		}else{
			
			echo '{"info":"'.$info.'失败","status":"n"}';
		}
		die;
	}
	//随机生成ID
	public function urandom($length,$oldids,$newids) {
		$hash = '';
		$chars = '0123456789';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
		if(in_array($hash,$oldids) || in_array($hash, $newids)){
			 return $this->urandom($length,$oldids,$newids);
		}
		return $hash;
	}
	//取得IP
	public function egetip(){
		if(getenv('HTTP_CLIENT_IP')&&strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) 
		{
			$ip=getenv('HTTP_CLIENT_IP');
		} 
		elseif(getenv('HTTP_X_FORWARDED_FOR')&&strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown'))
		{
			$ip=getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(getenv('REMOTE_ADDR')&&strcasecmp(getenv('REMOTE_ADDR'),'unknown'))
		{
			$ip=getenv('REMOTE_ADDR');
		}
		elseif(isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']&&strcasecmp($_SERVER['REMOTE_ADDR'],'unknown'))
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		$ip=trim(preg_replace("/^([\d\.]+).*/","\\1",$ip));
		return $ip;
	}
	
	
	/**
	 * 
	 * ajax判断用户名是否存在
	 * 
	 */
	function ajaxUserName(){
		
		$status = 'y';
		
		$user_name = trim($this->input->get_post('param'));
		
		$user_info = $this->model->getOne(array('user_name'=>$user_name),TRUE);
		if($user_info){
			$status = '账号已经存在，请重新输入!';
		}
		
		echo $status;
	}
	
}
 
?>