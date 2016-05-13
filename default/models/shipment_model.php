<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 * 物流方式管理模型类
 */
class Shipment_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
  	public function get_all_shipment($options ,&$return_arr ,$is_array=false){

    	return $this->getAll($options,$return_arr);
    
    }
    
    //获取所有未禁用的物流
    public function getAllShipment(){
      $option = array();
      $where  = array();
      $where['shipmentEnable'] = 1;
      $where['shipmentID >'] = 1;
      $option['where']  =  $where;
      $option['order']  = 'shipmentID asc';
      return $this->getAll2Array($option);
    }
    

}

/* End of file Shipment_model.php */
/* Location: ./defaute/models/Shipment_model.php */