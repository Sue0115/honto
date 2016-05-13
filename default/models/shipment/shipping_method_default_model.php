<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Shipping_method_default_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getAllShipment(){
     $option['select']=array('methodTitle');
     return $this->getAll2Array($option);
    }
}

/* End of file Shipment_method_default_model.php */
/* Location: ./defaute/models/shipment/Shipment_method_default_model.php */