<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Lazada_pagenumber_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getInfoByOrderID($where){
       $option = array();
       $option['where'] = $where;
       return $this->getAll2Array($option);
    }
}

/* End of file Lazada_pagenumber_model.php */
/* Location: ./defaute/models/Lazada_pagenumber_model.php */