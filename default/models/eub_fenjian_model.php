<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Eub_fenjian_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    //根据邮编获取分拣码
	public function getInfoByCode($code){
      $option = array();
      $where = array();
      $where['zip'] = $code;
      $option['where'] = $where;
      return $this->getOne($option,true);
    }
}

/* End of file Eub_fenjian_model.php */
/* Location: ./defaute/models/Eub_fenjian_model.php */