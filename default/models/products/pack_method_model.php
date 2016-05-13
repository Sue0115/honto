<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Pack_method_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    public function get_pack_name($id){

    	$name = '';

    	$data = array();

    	if($id > 0){
    		$data = $this->getOne(array('id'=>$id),true);
    	}

    	if(!empty($data)){
    		$name = $data['title'];
    	}

    	return $name;
    }
    
    /**
     * 根据id获取该包装的详细信息
     */
    public function getPackInfo($id){
      $option = array();
      $where = array();
      $where['id'] = $id;
      $option['where'] = $where;
      return $this->getOne($option,true);
    }
}

/* End of file Pack_method_model.php */
/* Location: ./defaute/models/products/Pack_method_model.php */