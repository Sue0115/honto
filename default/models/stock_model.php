<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Stock_model extends MY_Model {
    
	public $_table = 'erp_products_data';
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getStockList($where_arr, &$return_arr=array()){
    	
    	$per_page = $where_arr['per_page'];
    	$cupage = $where_arr['cupage'];
    	
    	$fields = " p.`products_sku` `sku`, p.`products_name_cn` , p.`products_location` , 
    				s.`actual_stock` ,s.stock_warehouse_id,
     					CASE WHEN p.`products_status_2` = 'selling' THEN '在售'
					       	WHEN p.`products_status_2` = 'sellWaiting' THEN '待售'
					       	WHEN p.`products_status_2` = 'stopping' THEN '停产'
					       	WHEN p.`products_status_2` = 'saleOutStopping' THEN '卖完下架'
					       	WHEN p.`products_status_2` = 'unSellTemp' THEN '货源待定'
					       	WHEN p.`products_status_2` = 'trySale' THEN '试销(卖多少采多少)'
					    END `sku_status`,
					       p.`products_weight` 
      ";
    	$where = "";	

    	if($where_arr['products_sku']){
    		$where .= " and p.`products_sku` = '".$where_arr['products_sku']."'  ";	
    	}
    	
    	if($where_arr['products_location']){
    		$where .= " and p.`products_location` = '".$where_arr['products_location']."'  ";	
    	}
    	
    	if($where_arr['productsStauts']){
    		$where .= " and p.`products_status_2` = '".$where_arr['productsStauts']."'  ";	
    	}
    	
    	if($where_arr['product_warehouse_id']){
    		$where .= " and `product_warehouse_id` = '".$where_arr['product_warehouse_id']."' AND `stock_warehouse_id` = '".$where_arr['product_warehouse_id']."' ";	
    	}
    	
    	$where .= " and  p.productsIsActive = 1 and (p.products_father_sku = '' or p.products_father_sku is null ) ";
    	
    	if(preg_match('/^\s*and/i', $where) !== false){
    		$where = preg_replace('/^\s*and/i', ' ', $where);
    	}
    	
	 static $total_rows;
	 
	 if(!$total_rows && $return_arr['total_rows'] === true){
	 	
	 	 $this->db->select("*", false);
	     $this->db->from("`erp_products_data` p");
	     $this->db->join("`erp_stock_detail` s", "p.products_id=s.products_id", 'left');
	     
	     if($where){
	     	$this->db->where($where, null, false);
	     }
	     
	     $total_rows = $this->db->count_all_results();
	 }
     
     $return_arr['total_rows'] = $total_rows;
     
     $this->db->select($fields, false);
     
     $this->db->from("`erp_products_data` p");
     $this->db->join("`erp_stock_detail` s", "p.products_id=s.products_id", 'left');
     
     if($where){
     	$this->db->where($where,  null, false);
     }
     
     $this->db->limit($cupage, $per_page);
     
     $result = $this->db->get()->result();
    
     return $result;
     
    }
    
    
}

/* End of file Country_model.php */
/* Location: ./defaute/models/Country_model.php */