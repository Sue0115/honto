<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Cnzexpress_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getInfoByID($orderID){
      $where = array('erp_orders_id'=>$orderID);
      $option['where']=$where;
      return $this->getOne($option,true);
    }
}

/* End of file Cnzexpress_model.php */
/* Location: ./defaute/models/Cnzexpress_model.php */