<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Yanwen_zone_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function get_yw_partition($code)
    {
    	$options = array(
    		'select' => array('partition'),
    		'where' => array(
    			'country_code' => strtoupper(trim($code))
    		),
    	);
    	
    	return $this->getOne($options, true);
    }
}

/* End of file Yanwen_zone_model.php */
/* Location: ./defaute/models/Yanwen_zone_model.php */