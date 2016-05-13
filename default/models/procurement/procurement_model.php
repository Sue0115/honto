<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Procurement_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //供应商管理 本月采购额
    public function getPurchaseTotalWithSuppliersID($supplierID = '')
    {
    		$total = 0;
    	
    		$sql = array(
    			'select' => array(
    				'po_id'
    			),
    			
    			'where' => array(
    				'po_parent_id'	=> 0,	//没有父ID的
    				'DATE_FORMAT(po_times,"%Y%m") = DATE_FORMAT(NOW(),"%Y%m")' => NULL,	//本月的记录
    				'supplierID' => $supplierID
    			),
    		);
    		$query = $this->getAll2Array($sql);
    		
    		if (!empty($query))
    		{
    			$this->load->model(array('procurement/procurement_products_model'));
    			
    			foreach ($query as $v) {
    				$money = 0;
    				
	    			$options = array(
	    				'select' => array(
	    					'op_pro_count_op', 'op_pro_cost'
	    				),
	    				
	    				'where' => array(
	    					'po_id' => $v['po_id']
	    				)
	    				
	    			);
	    			$result = $this->procurement_products_model->getAll2Array($options);
	    			
	    			foreach ($result as $value) {
	    				$money += $value['op_pro_count_op'] * $value['op_pro_cost'];
	    			}
	    			
	    			$total += $money;
    			}
    		}
    	return $total;
    }
}

/* End of file Procurement_model.php */
/* Location: ./defaute/models/procurement/Procurement_model.php */