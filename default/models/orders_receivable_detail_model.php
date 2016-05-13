<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Orders_receivable_detail_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据平台订单号获取该数据信息
    public function getPlatInfoByID($ID){
      $option = array();
      $option['where'] = array('erp_orders_id'=>$ID);
      return $this->getAll2Array($option);
    }
}

/* End of file Orders_receivable_detail_model.php */
/* Location: ./defaute/models/Orders_receivable_detail_model.php */