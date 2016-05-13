<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Sf_user_tokens_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('sf_user_tokens');
        
    }
    public function getAllEbay(){
      $option['select']=array('seller_account');
      $option['group']=array('group by'=>'seller_account');
      return $this->getAll2Array($option);
    }
}

/* End of file Sf_user_tokens_model.php */
/* Location: ./defaute/models/shipment/Sf_user_tokens_model.php */