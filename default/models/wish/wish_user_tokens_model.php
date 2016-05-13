<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Wish_user_tokens_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('wish_user_tokens');
        
    }
    
	/**
     * 获取wish账号列表
     * @param  integer $status [description]
     * @return [type]          [description]
     */
    public function getWishTokenList($options){
    	$rs = array();
    	$array   = null;
    	$result  = $this->getAll($options, $array, true);
    	if ($result) {
    		foreach ($result as $r) {
    			$rs[$r['token_id']] = $r;
    		}
    	}
    	return $rs;
    }
    
    /**
     * 根据账号获取key值
     */
    public function getKeyByAccount($account){
       $where = array();
       $option = array();
       $where['account_name'] = $account;
       $option['where'] = $where;
       return $this->getOne($option,true);
    }
}

/* End of file Wish_user_tokens_model.php */
/* Location: ./defaute/models/wish/Wish_user_tokens_model.php */