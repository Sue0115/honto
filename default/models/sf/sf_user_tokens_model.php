<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Sf_user_tokens_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
	function getSeller(){
    	$options = array(
    		'select' => array(
    			'token_id', 'seller_account'
    		),
    		'where' => array(
    			'token_status' => 1
    		)
    	);
    	
    	$result = $this->getAll2Array($options);
    	
    	return $result;
    }
}

/* End of file Sf_user_tokens_model.php */
/* Location: ./defaute/models/sf/Sf_user_tokens_model.php */