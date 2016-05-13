<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 4PX新加坡国家分区
 */
class erp_4px_country_code_model extends MY_Model {

    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
	
    public function __construct() {
       parent::__construct('erp_4px_country_code'); 
    }
    
    public function get_one_data($where)
    {
    	$options = array(
    		'where' => $where
    	);
    	
    	return $this->getOne($options, TRUE);
    }
}

/* End of file sangelfine_warehouse_model.php */
/* Location: ./defaute/models/Shipment_suppliers_model.php */