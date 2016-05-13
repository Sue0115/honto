<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class wish_product_detail_model extends MY_Model {
    
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
     * 根据广告ID删除表数据
     * 
     */
    public function deleteByProductID($productID){
      $option = array();
      $where = array();
      $where['productID'] = $productID;
      $option = array(
        'where' => $where
      );
      return $this->delete($option);
    }
    
	/**
	 * 获取广告对应的SKU信息--每个产品的SKU会合并在一起
	 * @param $product_array
	 * @param $ignoreRemoved 是否忽略删除的数据
	 * @return array
	 */
	public function getProductSkus($product_array){
		$rs = array();
		if ($product_array) {
			$options['where_in'] = array('productID' => $product_array);
			$options['select']   = array("productID", "GROUP_CONCAT(sku) AS sku_code");
			$options['group_by'] = 'productID';
			
			$array               = null;
			$data                = $this->getAll($options, $array, true);

			if ($data) {
				foreach ($data as $row) {
					$rs[$row['productID']] = $row['sku_code'];
				}
			}
		}
		return $rs;
	}
	
	/**
	 * 根据productID获取主图
	 */
	public function getMainImage($product_array){
		$rs = array();
		if ($product_array) {
			$options['where_in'] = array('productID' => $product_array);
			$options['select']   = array("productID,main_image");
			$options['group_by'] = 'productID';
			
			$array               = null;
			$data                = $this->getAll($options, $array, true);

			if ($data) {
				foreach ($data as $row) {
					$rs[$row['productID']] = $row['main_image'];
				}
			}
		}
		return $rs;
	}
	
	/**
	 * 根据SKU模糊查询产品ID
	 * @param $sku
	 * @return array
	 */
	public function getProductIdWithSku($sku){
		$sku = trim($sku);
		$sql = "SELECT DISTINCT productID FROM erp_wish_product_detail WHERE sku LIKE '".$sku."%' ";
		$result = $this->query($sql)->result_array();
		$rs = array();
		foreach ($result as $item){
			$rs[] = $item['productID'];
		}
		return $rs;
	}
	
	/**
     * 根据广告ID获取广告详情数据
     */
    public function getDetailInfoByProductID($productID){
      $option = array();
      $where = array();
      $where['productID'] = $productID;
      $option = array(
        'where' => $where
      );
      return $this->getAll2Array($option);
    }
}

/* End of file Wish_product_list_detail_model.php */
/* Location: ./defaute/models/wish/Wish_product_list_detail_model.php */