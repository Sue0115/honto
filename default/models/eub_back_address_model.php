<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Eub_back_address_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据id获取信息
    public function getInfoByID($ID){
      $option['where'] = array('id' => $ID);
      return $this->getOne($option,true);
    }
}

/* End of file Eub_back_address_model.php */
/* Location: ./defaute/models/Eub_back_address_model.php */