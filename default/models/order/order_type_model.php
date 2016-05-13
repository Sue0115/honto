<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 
 */
class Order_type_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    public function get_all_used_order_type($options = array(),$is_array = false){

    	$options['where']['status'] = 1;
    	if($is_array){
    		$data = $this->getAll2Array($options);
    	}else{
    		$data = $this->getAllObj($options);
    	}

    	return $data;
    }
}

/* End of file Order_type_model.php */
/* Location: ./defaute/models/order/Order_type_model.php */