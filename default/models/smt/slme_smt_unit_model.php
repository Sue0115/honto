<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通单位model
 * suwei 20141124
 */
class Slme_smt_unit_model extends MY_Model {

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
     * 获取单位列表，并格式化
     * @return [type] [description]
     */
    public function getUnitList(){
		$rs     = array();
		$result = $this->getAll2Array();
    	if ($result) {
    		foreach ($result as $row) {
    			$rs[$row['id']] = $row;
    		}
    	}
    	return $rs;
    }
}

/* End of file Slme_smt_unit_model.php */
/* Location: ./defaute/models/smt/Slme_smt_unit_model.php */