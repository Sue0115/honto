<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 产品信息模块模型类
 */
class Slme_smt_product_module_model extends MY_Model {
    
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
     * 怕断产品信息模块是否存在
     * @param unknown $token_id
     * @param unknown $module_id
     * @return boolean
     */
    public function checkModuleIsExists($token_id, $module_id){
        $options['where'] = array(
                'token_id'  => $token_id,
                'module_id' => $module_id,
        );
        $data = $this->getOne($options, true);
        return $data ? $data['id'] : false;
    }
    
    /**
     * 删除本账号下，过期没同步的产品信息模块
     * @param unknown $token_id
     */
    public function deleteExpiredModule($token_id){
        $sql = "DELETE FROM erp_slme_smt_product_module WHERE last_update_time < NOW() - INTERVAL 1 DAY AND token_id = $token_id";
        $this->query($sql);
    }

    /**
     * 获取某账号某状态的产品信息模板列表
     * @param $token_id
     * @param $status
     * @return array
     */
    public function getModuleList($token_id, $status='approved'){
        $options['where'] = array('token_id' => $token_id, 'module_status' => $status);
        $return_arr = null;
        $result = $this->getAll($options, $return_arr, true);
        $rs = array();
        if ($result){
            foreach ($result as $item){
                $rs[$item['module_id']] = $item;
            }
        }
        return $rs;
    }

    /**
     * 查询一个产品信息模块的字段信息
     * @param $moduleId
     * @param $fields
     * @return Ambigous|array
     */
    public function getModuleFields($moduleId, $fields){
        if ($moduleId){
            $option['where'] = array('module_id' => $moduleId);
            $option['select'] = $fields ? $fields : '*';

            return $this->getOne($option, true);
        }else {
            return array();
        }
    }
}

/* End of file Slme_smt_product_module_model.php */
/* Location: ./defaute/models/smt/Slme_smt_product_module_model.php */