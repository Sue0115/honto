<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通销售代码模型类
 */
class Smt_user_sale_code_model extends MY_Model {
    
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
     * 获取销售前缀列表
     * @param  [type] $options [description]
     * @return [type]          [description]
     */
    public function getSalersPrefixList($options=array()){
		$rs     = array();
		$array  = null;
		$result = $this->getAll($options, $array, true);
		if ($result) {
			foreach ($result as $r) {
				$rs[$r['user_id']]   = $r;
				$rs[$r['sale_code']] = $r;
			}
		}
		return $rs;
    }
}

/* End of file Smt_user_sale_code_model.php */
/* Location: ./defaute/models/smt/Smt_user_sale_code_model.php */