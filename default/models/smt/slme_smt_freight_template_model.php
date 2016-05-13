<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通运费模板
 * suwei 20141125
 */
class Slme_smt_freight_template_model extends MY_Model {

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
     * 判断属性是否已存在
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function checkTemplateIsExists($token_id, $templateId){
        $token_id         = (int)$token_id;
        $templateId       = (int)$templateId;
        $options['where'] = array('token_id' => $token_id, 'templateId' => $templateId);
        $result           = $this->getOne($options, true);
    	return $result ? $result['id'] : false;
    }

    /**
     * 获取某个账号的运费模板列表
     * @param  integer $token_id [description]
     * @return [type]            [description]
     */
    public function getFreightTemplateList($token_id = 0){
        $token_id         = (int)$token_id;
        $options['where'] = array('token_id' => $token_id);
        $array            = null;
        $result           = $this->getAll($options, $array, true);
        $rs               = array();
        if ($result) {
            foreach ($result as $row) {
                $rs[$row['templateId']] = $row;
            }
        }
        return $rs;
    }
    
    /**
     * 删除各账号过期的模板
     * @param unknown $token_id
     */
    public function deleteExpiredFreightTemplate($token_id){
        $sql = "DELETE FROM erp_slme_smt_freight_template WHERE last_update_time < NOW() - INTERVAL 1 DAY AND token_id = $token_id";
        $this->query($sql);
    }
}

/* End of file Slme_smt_freight_template_model.php */
/* Location: ./defaute/models/smt/Slme_smt_freight_template_model.php */