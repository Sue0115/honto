<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Guangzhou_gekou_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据国家中文名称获取格口信息
    public function getInfoByCountryCn($country_cn){
      $option['where'] = array('country' => $country_cn);
      return $this->getAll2Array($option);
    }
}

/* End of file Guangzhou_gekou_model.php */
/* Location: ./defaute/models/Guangzhou_gekou_model.php */