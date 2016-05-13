<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Orders_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function if_owe_goods($orders_sku = FALSE)
    {
    	if ($orders_sku)
    	{
    		$options = array(
    			'select' => array(
    				'erp_orders.erp_orders_id', 'op.orders_sku as sku', 'sum(op.item_count) as co'
    			),
    			'join' => array(
    				array(
    					'erp_orders_products as op', 'erp_orders.erp_orders_id = op.erp_orders_id', 'inner'
    				),
    			),
    			'where' => array(
    				'erp_orders.orders_is_backorder' => 1,
    				'erp_orders.orders_is_join' => 0,
    				'erp_orders.orders_status <>' => 6,
    				'op.orders_sku' => $orders_sku,
    			),
    			'group_by' => array(
    				'op.erp_orders_id' => null,
    				'op.orders_sku' => null,
    			)
    		);
    		
    		$result = $this->getAll2Array($options);
    		
    		return $result;
    	}
    	else
    	{
    		return FALSE;
    	}
    }
}

/* End of file Orders_model.php */
/* Location: ./defaute/models/orders/Orders_model.php */