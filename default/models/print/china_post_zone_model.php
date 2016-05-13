<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class China_post_zone_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    /**
     * 根据国家中文名称获取分区
     */
    public function getAreaByCn($country_cn){
      $option = array();
      $where['country_cn'] = $country_cn;
      $option['where'] = $where;
      return $this->getOne($option,true);
    }
}

/* End of file China_post_zone_model.php */
/* Location: ./defaute/models/print/China_post_zone_model.php */