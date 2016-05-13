<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Smt_user_tokens_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('smt_user_tokens');
        
    }
	public function getAllSmt(){
	  
      $option['select']=array('seller_account');
      $option['group']=array('group by'=>'seller_account');
      return $this->getAll2Array($option);
    }
}

/* End of file Smt_user_tokens_model.php */
/* Location: ./defaute/models/shipment/Smt_user_tokens_model.php */