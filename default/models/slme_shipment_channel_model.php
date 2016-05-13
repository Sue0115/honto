<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 渠道管理模型类
 */
class Slme_shipment_channel_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function get_all_channel($options ,&$return_arr ,$is_array=false){
    	
    	$options['select'] = array($this->_table.'.*','sm.suppliers_company','u.user_name');
    	
    	$join[] = array('erp_shipment_suppliers sm',$this->_table.'.suppliers_id=sm.suppliers_id');
    	
    	$join[] = array('erp_slme_user u',$this->_table.'.user_id=u.id');
    	
    	$options['join'] = $join;
    	
    	return $this->getAll($options,$return_arr);
    
    }
}

/* End of file Slme_shipment_channel_model.php */
/* Location: ./defaute/models/Slme_shipment_channel_model.php */