<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class colum extends Admin_Controller {
	  
	function __construct() {
		   
		parent::__construct();
		
		$this->load->model('sharepage');
		$this->model = $this->slme_colum_model;
	} 
		 
	function index(){
		
		$return_arr = null;
		
		$where = array();
		
		if($this->user_info->id != 1){
			$where = array('status >='=>0);
		}
		
		$type = $this->input->get_post('type');
		
		$type = $type ? $type : 'admin';
		
		if($type != ''){
			$where['type'] = $type;
		}
			
		
		
		$title = htmlspecialchars(trim($this->input->get_post("title")));
			
		$options	= array(
			'where'		=> $where,
			'order'		=> " order_id desc,cid desc",
		);
		
		$lc_list = $this->slme_colum_model->getAll($options, $return_arr,true); //查询所有信息
		$tree = new Tree();
		$tree->init($lc_list);
		if($title){
			$newTree = $tree->findTreeByName($title);
			if($newTree){
				$tree->setTree(array($newTree['cid']=>$newTree));
			}
		}
		
		$data = array(
        			'lc_tree'    => $tree,
        			'title'	     => $title,
        	        'fileter_options' => array('title'=>$title,'type'=>$type),
        	        'page'       => ''
        		);
		$this->_template('admin/setting/columlist',$data);
	} 
	 

	public function info(){
	    parent::info();
	    
		$cid	=	intval($this->input->get_post('cid'));

		$options = array();
		if($cid>0){
			$options['where'] = array('cid'=>$cid);
		}
		
		$item = $this->slme_colum_model->getOne($options,true);
		$this->parameters->fromArray(json_to_array($item['params']));
		$item['params'] = $this->parameters;
		
		$parents = array('顶级栏目');
		foreach ($this->tree->getValueOptions() as $k=>$tmp){
		    $parents[$k] = $tmp['title'];
		}
		$data = array(
			'parents' => $parents,
			'item'	=> $item,	
		); 

		$this->_template('admin/setting/columinfo',$data);
	}  
	
	public function save(){
		
		$cid = (int)$this->input->get_post('cid');
		
		$data['title'] = htmlspecialchars($this->input->get_post('title'));
		
		$data['directory'] = htmlspecialchars($this->input->get_post('directory'));

		$data['con_name'] = htmlspecialchars($this->input->get_post('con_name'));
		
		$data['parents'] = intval($this->input->get_post('parents'));
		
		$data['type'] = htmlspecialchars($this->input->get_post('type'));
		
		$data['type'] = $data['type'] ? $data['type'] : 'admin';
		
		$data['params'] = json_encode($this->input->get_post('params'));
		//保存信息
		if($cid>0){
			$data['cid'] = $cid;
			$result=$this->slme_colum_model->update($data);
		}else{
			
			$result=$this->slme_colum_model->add($data);
		}
		
		$info = $cid ? '修改' : '添加';
		
		//信息返回操作
		if($result){
			
			$val = $cid ? $cid : $result;
			
			echo '{"info":"'.$info.'成功","status":"y","id":"'.$val.'"}';
		}else{
			
			echo '{"info":"'.$info.'失败","status":"n"}';
		}
		
		die;  
	}
	
	//ajax排序
	public function order_insert(){
		
		$val = intval($this->input->get_post('val'));
		
		$id = intval($this->input->get_post('id'));
		
		$field = trim($this->input->get_post('field'));
		
		if($id>0){
			
			$data[$field] = $val;
			$data['cid'] = $id;
			$this->model->update($data);
			
			echo json_encode(array('status'=> '1','msg'=>'修改成功'));exit();
		}else{
			
			echo json_encode(array('status'=> '2','msg'=>"出错啦"));exit();
		}
	}
	
	
}
 
?>