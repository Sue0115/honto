<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Shipping_by_orders_old_shipping_code_model extends MY_Model {
    
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
        $option = array();
        $re = array();
        $result = $this->getAll2array($option);
        foreach($result as $arr){
            $re[] = $arr['shipmentID'];
        }
        return $re;
    }
}

/* End of file Shipping_by_orders_old_shipping_code_model.php */
/* Location: ./defaute/models/Shipping_by_orders_old_shipping_code_model.php */