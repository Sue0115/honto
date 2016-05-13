<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Mdd_country_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
     //根据国家中文名称获取国家分区
    public function getRegionByCountryCn($country_cn){
		$select=array('region');
		$where['country_cn']=$country_cn;
		$option=array(
		  'select' => $select,
		  'where'  => $where,
		);
		return $this->getOne($option,true);
    }
}

/* End of file Mdd_country_model.php */
/* Location: ./defaute/models/Mdd_country_model.php */