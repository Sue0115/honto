<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Smt_area_code_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据国家简码获取国家分区信息
    public function getArea($code){
      $option = array();
      $where = array();
      $option['where'] = array('countryCode'=>$code);
      return $this->getOne($option,true);
    }
}

/* End of file Smt_area_code_model.php */
/* Location: ./defaute/models/Smt_area_code_model.php */