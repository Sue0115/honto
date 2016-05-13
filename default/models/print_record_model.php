<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Print_record_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据订单id获取该订单的打印记录
    public function getInfoByID($id){
      $option = array();
      $where['erp_orders_id'] = $id;
      $option['where'] = $where;
      return $this->getOne($option,true);
    }
}

/* End of file Print_record_model.php */
/* Location: ./defaute/models/Print_record_model.php */