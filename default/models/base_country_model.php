<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Base_country_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    public function getCountryInfoByCode($code){
      $option = array();
      $where = array();
      $where['country_code'] = $code;
      $option['where'] = $where;
      return $this->getOne($option,true);
    }
}

/* End of file Base_country_model.php */
/* Location: ./defaute/models/Base_country_model.php */