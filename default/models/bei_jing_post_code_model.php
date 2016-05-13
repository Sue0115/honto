<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Bei_jing_post_code_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
	//根据国家中文名获取序号
    public function getInfoByCn($country_cn){
    	$option['where'] = array('country' => $country_cn);
    	return $this->getOne($option,true);
    }
}

/* End of file Bei_jing_post_code_model.php */
/* Location: ./defaute/models/Bei_jing_post_code_model.php */