<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Guangzhou_address_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //获取寄件人信息，只能获取使用次数小于50次，只限于当天
    public function getSenderInfo(){
      $option['where'] = array('useNumber <' => 50,'updateTime' => date('Y-m-d'));
      return $this->getOne($option,true);
    }
    
	//更新寄件人信息，只能更新不是今天的数据
    public function updateSenderInfo(){
      $data = array('useNumber' => 0,'updateTime' => date('Y-m-d'));
      $option['where'] = array('updateTime <' => date('Y-m-d'));
      return $this->update($data,$option);
    }
}

/* End of file Guangzhou_address_model.php */
/* Location: ./defaute/models/Guangzhou_address_model.php */