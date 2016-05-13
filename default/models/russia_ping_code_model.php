<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Russia_ping_code_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据国家简码获取整个信息
    public function getInfoByCountryCode($code){
       $option = array();
       $option['where'] = array('country_code'=>$code,'type'=>'p');
       return $this->getOne($option,true);
    }
}

/* End of file Russia_ping_code_model.php */
/* Location: ./defaute/models/Russia_ping_code_model.php */