<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Youzheng_country_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据国家中文名获取国家简码
    public function getCountryCodeByChineseName($chineseName){
     $select = array('CountryCode');
     $where['ChineseName'] = $chineseName;
     $option = array(
      'select' => $select,
      'where'  => $where,
     );
     return $this->getOne($option,true);
    }
}

/* End of file Youzheng_country_model.php */
/* Location: ./defaute/models/Youzheng_country_model.php */