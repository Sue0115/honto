<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Out_data_template_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //获取登录用户创建的并且可用的模板
    public function getAllTemplate(){
      $option = array();
      $option['where'] = array('status'=>1);
      return $this->getAll2Array($option);
    }
    
    //根据模板id获取模板信息
    public function getTemplateInfo($tid){
      $option = array();
      $option['where'] = array('id'=>$tid);
      return $this->getOne($option,true);
    }
}

/* End of file Out_data_template_model.php */
/* Location: ./defaute/models/Out_data_template_model.php */