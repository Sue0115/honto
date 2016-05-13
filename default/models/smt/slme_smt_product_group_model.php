<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Slme_smt_product_group_model extends MY_Model {
    
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
     * 检测产品分组是否存在
     * @param  [type] $token_id [description]
     * @param  [type] $group_id [description]
     * @return [type]           [description]
     */
    public function checkProductGroupIsExists($token_id, $group_id){
		$options['where'] = array('token_id' => $token_id, 'group_id' => $group_id);
		$rs               = $this->getOne($options, true);
    	return $rs ? $rs['id'] : false;
    }

    /**
     * 获取账号的产品分组列表并组装成原获取的数据格式
     * @param  [type] $token_id 账号ID
     * @return [type]           [description]
     */
    public function getProductGroupList($token_id){
        $options          = array();
        if ($token_id){
            $options['where'] = array('token_id' => $token_id);
        }
		$array            = null;
		$rs               = array();
		$result           = $this->getAll($options, $array, true);
    	if ($result) {
    		foreach ($result as $row) {
    			if ($row['parent_id'] == '0') { //说明是一级产品分组
    				$rs[$row['group_id']] = $row;
    			}else {
    				$rs[$row['parent_id']]['child'][] = $row;
    			}
    		}
    	}
    	return $rs;
    }
    
    //删除过期的产品分组
    public function deleteExpiredProductGroup($token_id){
        $sql = "DELETE FROM erp_slme_smt_product_group WHERE last_update_time < NOW() - INTERVAL 1 DAY AND token_id = $token_id";
        $this->query($sql);
    }
}

/* End of file Slme_smt_product_group_model.php */
/* Location: ./defaute/models/smt/Slme_smt_product_group_model.php */