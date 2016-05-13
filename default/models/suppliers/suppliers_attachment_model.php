<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Suppliers_attachment_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    /**
     * 获取供应商的附件信息
     * @param unknown_type $suppliers_id
     * @return unknown
     */
    public function getAttachemntsWithSuppliersID($suppliers_id){
    	
    	$options = array(
    		'where' => array(
    			'suppliers_id' => $suppliers_id
    		)
    	);
    	
    	$result = $this->getAll2Array($options);
    	
    	return $result;
    }
}

/* End of file Suppliers_attachment_model.php */
/* Location: ./defaute/models/suppliers/Suppliers_attachment_model.php */