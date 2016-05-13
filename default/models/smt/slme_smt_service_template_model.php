<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Slme_smt_service_template_model extends MY_Model {
    
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
     * 判断下服务模板是否存在
     * @param  [type] $token_id  [description]
     * @param  [type] $serviceID [description]
     * @return [type]            [description]
     */
    public function checkServiceTemplateIsExists($token_id, $serviceID){
		$options['where'] = array('token_id' => $token_id, 'serviceID' => $serviceID);
		$result           = $this->getOne($options, true);
    	return $result ? $result['id'] : false;
    }

    /**
     * 获取某个账号下的所有服务模板
     * @param  [type] $token_id [description]
     * @return [type]           [description]
     */
    public function getServiceTemplateList($token_id){
		$options['where'] = array('token_id' => $token_id);
		$array            = null;
		$rs               = array();
    	$result = $this->getAll($options, $array, true);
    	if ($result) {
    		foreach ($result as $row) {
    			$rs[$row['serviceID']] = $row;
    		}
    	}
    	return $rs;
    }
    
    /**
     * 删除过期未同步的服务模板
     * @param unknown $token_id
     */
    public function deleteExpiredServiceTemplate($token_id){
        $sql = "DELETE FROM erp_slme_smt_service_template WHERE last_update_time < NOW() - INTERVAL 1 DAY AND token_id = $token_id";
        $this->query($sql);
    }
}

/* End of file Slme_smt_service_template_model.php */
/* Location: ./defaute/models/smt/Slme_smt_service_template_model.php */