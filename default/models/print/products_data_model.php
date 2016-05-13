<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Products_data_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    public function getProductsInfoWithSku($sku,$product_warehouse_id){
	    $where_warehosue = '';
	    $group_by = '';
	    $where['products_sku']=$sku;
	    if(intval($product_warehouse_id) > 0){
		  $where['product_warehouse_id']=$product_warehouse_id;
	    }else{
	        $group_by = ' group by products_sku';
	        $option['group']=$group_by;
	    }
		$option=array(
		 'where' =>$where,
		);
		return $this->getOne($option,true);
    }
    
   //根据产品sku获取产品中文名称
    public function getProductCnBySku($sku){
      $select=array(
        'products_name_cn',
      );
      $where=array(
        'products_sku' => $sku, 
      );
      $option=array(
        'select' => $select,
        'where'  => $where,
      );
      $name=$this->products_data_model->getOne($option,true);
      return $name;
    }
    
//根据产品sku获取产品信息
    public function getProductInfoBySku($sku){
      $where=array(
        'products_sku' => $sku, 
      );
      $option=array(
        'where'  => $where,
      );
      $productInfo=$this->products_data_model->getOne($option,true);
      return $productInfo;
    }
}

/* End of file Products_data_model.php */
/* Location: ./defaute/models/print/Products_data_model.php */