<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 物流匹配规则管理模型类
 */
class Shipment_rule_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
 	public function getAllRuleList(){
      $where=array(
        'ruleEnable'=>1,
      );
      $option=array(
        'where'=>$where,
        'order'=>'ruleID desc',
      );
      return $this->getAll($option);
    }

}

/* End of file Shipment_rule_model.php */
/* Location: ./defaute/models/Shipment_rule_model.php */