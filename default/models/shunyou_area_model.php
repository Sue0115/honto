<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Shunyou_area_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getInfoByCode($code){
      $option = array();
      $option['where'] = array('country_code'=>$code);
      return $this->getOne($option,true);
    }
}

/* End of file Shunyou_area_model.php */
/* Location: ./defaute/models/Shunyou_area_model.php */