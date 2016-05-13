<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Stock_detail_operate_record_detail_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function conditions_query ($arr = array())
    {
    	if (!empty($arr))
    	{
    		$options = array(
    			'select'	=> array(
    				'operate_time', 'operate_type', 'operate_count', 'Stock'
    			)
    		);
    		
	    	foreach ($arr as $k => $v)
	    	{
	    		if (!empty($v))
	    		{
	    			switch ($k)
	    			{
	    				case 'product_id':
	    					$options['where']['product_id'] = $v;
	    					break;
	    				
    					case 'operate_type':
    						$options['where']['operate_type'] = $v;
    						break;
	    				
    					case 'start_date':
    						$options['where']['operate_time >='] = $v;
    						break;
    						
    					case 'end_date':
    						$options['where']['operate_time <='] = $v;
    						break;
	    			}
	    		}
	    	}
	    	
	    	$result = $this->getAll2Array($options);
	    	return $result;
    	}
    }
}

/* End of file Stock_detail_operate_record_detail_model.php */
/* Location: ./defaute/models/Stock_detail_operate_record_detail_model.php */