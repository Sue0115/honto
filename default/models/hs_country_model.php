<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Hs_country_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    //根据国家中文名称获取信息
    public function getInfoByCn($country_cn){
      $option['where'] = array('country_cn'=>$country_cn);
      return $this->getOne($option,true);
    }
}

/* End of file Hs_country_model.php */
/* Location: ./defaute/models/Hs_country_model.php */