<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Shipment_suppliers_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function get_all_shipment_suppliers_obj($options = array()){
    	
    	$options['order'] = 'suppliers_company asc';
    	
    	return $this->getAllObj(array('order'=>'suppliers_company asc'));
    	
    }
}

/* End of file Shipment_suppliers_model.php */
/* Location: ./defaute/models/Shipment_suppliers_model.php */