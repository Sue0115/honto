<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Orders_receivable_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //已id为键名，批次名为键值组装数据
    public function getAllInfo(){
      $option = array();
      $data = $this->getAll2Array($option);
      $new_data = array();
      foreach($data as $d){
        $new_data[$d['id']] = $d['import_name'];
      }
      return $new_data;
    }
}

/* End of file Orders_receivable_model.php */
/* Location: ./defaute/models/Orders_receivable_model.php */