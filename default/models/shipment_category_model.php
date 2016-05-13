<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 物流分类管理模型类
 */
class Shipment_category_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function get_all_category(){
    	return $this->getAll();
    }
}

/* End of file Shipment_category_model.php */
/* Location: ./defaute/models/Shipment_suppliers_model.php */