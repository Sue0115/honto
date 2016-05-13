<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Wish_message_detail_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    //更加mailID号删除记录
    public function deleteByMailID($mailID){
       $option = array();
       $option['where'] = array('mailID'=>$mailID);
       return $this->delete($option);
    }
	//根据邮件id获取信息
    public function getDeailInfoByMailID($mailID){
       $option = array();
       $option['where'] = array('mailID'=>$mailID);
       return $this->getAll2Array($option);
    }
}

/* End of file Wish_message_detail_model.php */
/* Location: ./defaute/models/wish/Wish_message_detail_model.php */