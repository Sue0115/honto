<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Suppliers_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function get_suppliers_list ($get = array(), $paging = FALSE, & $total = FALSE)
    {
    	$options = array(
    		'select' => array(
    			'suppliers_id', 'suppliers_company', 'suppliers_name', 
    			'suppliers_phone', 'suppliers_qq', 'suppliers_wangwang',
    			'suppliers_bank', 'suppliers_address',	'user_id',
    			'supplierArrivalMinDays', 'suppliers_status'
    		)
    	);
    	
    	//筛选
    	if (!empty($get))
    	{
    		foreach ($get as $k=>$v) {
    			if (!empty($v))
    			{
    				switch ($k) {
    					case 'suppliers_id' :	//供应商编号
    						$options['where'][$k] = $v;
    						break;
    						
    					case 'suppliers_company' :	//供应商名
    						$options['like'][$k] = $v;
    						break;
    						
    					case 'suppliers_name' ;	//联系人
    						$options['like'][$k] = $v;
    						break;
    						
    					case 'suppliers_contact' ;	//电话 QQ
    						$options['where']['(suppliers_phone like "'.$v.'" or suppliers_mobile like "'.$v.'" or suppliers_fax like "'.$v.'")'] = NULL;
    						break;
    						
    					case 'suppliers_status' ;	//状态 
    						$options['like'][$k] = $v;
    						break;
    						
    					case 'user_id' ;	//采购
    						$options['where'][$k] = $v;
    						break;
    						
    					case 'sku' ;	//SKU
    					
    						$this->load->model(array('products/products_data_model'));
    						$sql = array(
    							'select' => array(
    								'products_suppliers_ids'
    							),
    							'like' => array(
    								'products_sku' => $v
    							)
    						);
    						$suppliers_ids = $this->products_data_model->getAll2Array($sql);	//查询SKU对应的供应商
    						
    						if (!empty($suppliers_ids[0]['products_suppliers_ids']))
    						{
    							$suppliers_id = explode(',', $suppliers_ids[0]['products_suppliers_ids']);
    							$options['where_in']['suppliers_id'] = $suppliers_id;
    						}else {
    							$options['where']['suppliers_id'] = 0;
    						}
    						break;
    				}
    			}
    		}
    	}
    	
    	if ($paging)
    	{
    		$options['page'] = $paging['cupage'];
    		$options['per_page'] = $paging['per_page'];
    	}
    	
    	if ($total)
    	{
    		$rsArr = $this->getAll($options, $total, true);
    	}else {
    		$rsArr = $this->getAll2Array($options);
    	}
    	return $rsArr;
    }
}

/* End of file Suppliers_model.php */
/* Location: ./defaute/models/Suppliers_model.php */