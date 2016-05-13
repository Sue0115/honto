<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 退件地址管理模型类
 */
class Postpacket_config_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getAllBackInfo($shipmentID){
      $back=$this->db->query("SELECT * FROM erp_postpacket_config WHERE FIND_IN_SET($shipmentID, shipment_id_string)");
      $backInfo=$back->result_array();
      return $backInfo;
    }

     //根据id获取信息
    public function getInfoByID($ID){
      $option['where'] = array('id' => $ID);
      return $this->getOne($option,true);
    }
    
}

/* End of file Postpacket_config_model.php */
/* Location: ./defaute/models/print/Postpacket_config_model.php */