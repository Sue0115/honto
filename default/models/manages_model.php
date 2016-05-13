<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Manages_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
	public function getmanagefields($fields = FALSE, $key = FALSE)
	{
	    if (is_numeric($key) && $key != 0)
	    {
	    	$select = array($fields);
	    	$where = array(
	    		'id' => $key,
	    	);
	    	$options = array(
	    		'select' => $select,
	    		'where' => $where,
	    	);
	    	$rs = $this->getOne($options, true);
	    	return $rs[$fields];
	    }
	    else
	    {
	    	return '';
	    }
	}
	
	function checkpermission($str = '', $type = FALSE)
	{
	    if ($str != '') {
	        $permissionArray = $this->get_user_permission($this->user_info->id);
	        
	        if (in_array($str, $permissionArray)) {
	            return true;
	        } else {
	            return false;
	        }
	    } else {
	        return false;
	    }
	}
	
	//获取权限
	function get_user_permission($id)
	{
	    $pArr = '';
	    $GArr = '';
	    
	    $options = array(
	    	'select' => array(
	    		'erp_manages.permissionId', 'b.group_permission_id'
	    	),
	    	'join' => array(
	    		array(
	    			'erp_user_group as b', 'erp_manages.userGroup=b.group_id', 'INNER'
	    		)
	    	),
	    	'where' => array(
	    		'erp_manages.id' => $id
	    	)
	    );
	    $re = $this->getOne($options, true);
	    return get_user_permission($re);
	}
}

/* End of file Manages_model.php */
/* Location: ./defaute/models/Manages_model.php */