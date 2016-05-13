<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Orders_products_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getProdcutList($id,$warehouse){
    	
     	$select = array($this->_table.'.erp_orders_id',$this->_table.'.orders_sku',$this->_table.'.item_count',$this->_table.'.item_price',$this->_table.'.orders_item_number',
     							'pd.products_name_cn', 'pd.products_location','pd.products_declared_cn','pd.products_declared_en',
     							'pd.products_declared_value','pd.products_sort', 'pd.products_weight','pd.products_with_battery','pd.products_unit1','pd.products_value','pd.products_is_declare');
    	
    	$join[] = array('erp_products_data pd',$this->_table.'.orders_sku=pd.products_sku','left');
		
    	$where=array(
    	   $this->_table.'.erp_orders_id'=> $id,
    	   'pd.product_warehouse_id'=>$warehouse,
    	);

    	$options=array(
    	  'select' => $select,
    	  'join'   => $join,
    	  'where'  => $where,
    	  'order'  => $this->_table.'.orders_products_id asc',
    	);

    	return $this->getAll2Array($options);
    }
    
    //获取erp_orders_products and erp_products_data 表的所有内容
 	public function getAllProdcutList($id,$warehouse){
    	
     	$select = array($this->_table.'.*','pd.*');
    	
    	$join[] = array('erp_products_data pd',$this->_table.'.orders_sku=pd.products_sku','left');
		
    	$where=array(
    	   $this->_table.'.erp_orders_id'=> $id,
    	   'pd.product_warehouse_id'=>$warehouse,
    	);

    	$options=array(
    	  'select' => $select,
    	  'join'   => $join,
    	  'where'  => $where,
    	  'order'  => $this->_table.'.orders_products_id asc',
    	);

    	return $this->getAll2Array($options);
    }
    
	/**
	 * 获取产品重量
	 * @param int $oID
	 */
	public function getOrdersTotalWeight($oID) {

	    $weightTotal = 0;
	    
	    $select = array($this->_table.'.item_count',
     							'pd.products_weight', 'pd.products_weight_maybe');
    	
    	$join[] = array('erp_products_data pd',$this->_table.'.orders_sku=pd.products_sku','left');
		
    	$where=array(
    	   $this->_table.'.erp_orders_id'=> $oID,
    	);

    	$options=array(
    	  'select' => $select,
    	  'join'   => $join,
    	  'where'  => $where,
    	  'group'  => 'group by '.$this->_table.'.orders_sku',
    	);

    	$orInfo=$this->getAll2Array($options);
    	
    	foreach($orInfo as $v){
    		 if ($v['products_weight'] > 0 && $v['products_weight'] != '' && $v['products_weight'] != NULL) {
	            $weightTotal = $weightTotal + $v['item_count'] * $v['products_weight'];
	        } else {
	            $weightTotal = $weightTotal + $v['item_count'] * $v['products_weight'];
	        }
    	}
    	return $weightTotal;
	}
    
}

/* End of file Orders_products_model.php */
/* Location: ./defaute/models/print/Orders_products_model.php */