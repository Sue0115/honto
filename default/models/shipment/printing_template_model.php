<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Printing_template_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //获取所有使用的模板   1-使用  0-暂停
    public function getAllTemplate(){
      $option['where'] = array('isOpen' => 1);
      return $this->getAll($option);
    }
}

/* End of file Printing_template_model.php */
/* Location: ./defaute/models/shipment/Printing_template_model.php */