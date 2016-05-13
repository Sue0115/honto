<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 库存类
 */
class Stock_detail_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    //根据sku减去或加上相应的库存
    //$type=+加库存
    //$type=-减库存
    public function add_or_reduce_stock ($sku,$num,$waerhouse,$type='+'){

    	$this->db->set('actual_stock','actual_stock'.$type.$num,false);
    
    	$this->db->where('products_sku', $sku);

    	$this->db->where('stock_warehouse_id', $waerhouse);

    	$tof = $this->db->update($this->_table);

    	$tof=$this->db->affected_rows();

    	return $tof;
    }
    
    //根据产品sku和产品所属仓库获取库存
    public function getStockBySku($sku,$warehouse){
      $option['where']=array(
        'products_sku' => $sku,
      	'stock_warehouse_id' => $warehouse,
      );
      return $this->getOne($option,true);
    }
    
    /**
     * 获取sku实际库存
     * type不为空，库存以sku为键名重组数组
     * $warehouse仓库，默认为空
     */
    public function getSkuStockNumInfo($type="",$warehouse=""){
       if($type !=""){
       	 $option = array();
       	 $where = array();
       	 if($warehouse !=""){
       	   $option['where'] = array('stock_warehouse_id'=>$warehouse);
       	 }
       	 $stockArr = array();
         $stock = $this->getAll2Array($option);
         foreach($stock as $s){
           $stockArr[$s['products_sku']] = $s['actual_stock'];
         }
         return $stockArr;
       }else{
         return  $this->getAll();
       }
       
    }
   
    
}

/* End of file Stock_detail_model.php */
/* Location: ./defaute/models/stock/Stock_detail_model.php */