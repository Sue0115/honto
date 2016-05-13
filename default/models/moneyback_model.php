<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Moneyback_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getInfoByID($where){
      $option['where'] = $where;
      return $this->getAll2Array($option);
    }
}

/* End of file Moneyback_model.php */
/* Location: ./defaute/models/Moneyback_model.php */