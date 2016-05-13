<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Country_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getAllCountry(){
     $option['select']=array('country_en','country_cn');
     $option['group']=array('group by'=>'country_en');
     return $this->getAll2Array($option);
    }
    
    /**
     * 根据国家英文名称获取国家信息
     */
    public function getCountryByEN($country){
     $option['where']=array('country_en'=>$country);
     return $this->getOne($option,true);
    }
    
 	/**
     * 根据国家全称获取国家信息
     */
    public function getCountryByDisplayName($country){
     $option['where']=array('display_name'=>$country);
     return $this->getOne($option,true);
    }
    
    /**
     * 根据国家英文名称获取国家的简称
     */
    public function getCountryCodeByEN($country){
     $option['select']='country_en';
     $option['where']=array('country_cn'=>$country);
     $option['order']='LENGTH(country_en) ASC';
     return $this->getAll2array($option);
    }
    
    /**
     * 根据订单id链接country表称获取转接口
     */
    public function getAdapterByOrderId($oID){
      $option['select']=array($this->_table.".adapter_spec");
      $option['where']=array("o.erp_orders_id" => $oID);
      $join[]=array("erp_orders o","o.buyer_country={$this->_table}.country_en");
      $option['join']=$join;
      return $this->getOne($option,true);
    }
    
}

/* End of file Country_model.php */
/* Location: ./defaute/models/Country_model.php */