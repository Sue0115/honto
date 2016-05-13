<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 操作日志模型
 */
class Slme_operate_log_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function add_log($data){
    	return $this->add($data);
    }
}

/* End of file Slme_operate_log_model.php */
/* Location: ./defaute/models/Slme_operate_log_model.php */