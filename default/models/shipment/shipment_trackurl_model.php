<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Shipment_trackurl_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getAllUrl(){
      $option['select']=array('track_id','track_short_name');
      return $this->getAll($option);
    }
    
}

/* End of file Shipment_trackurl_model.php */
/* Location: ./defaute/models/shipment/Shipment_trackurl_model.php */