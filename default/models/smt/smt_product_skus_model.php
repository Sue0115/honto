<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通产品SKU模型类
 */
class Smt_product_skus_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('smt_product_skus');
        
    }

    /**
     * 获取SMT SKU列表
     * @param  [type] $productId [description]
     * @return [type]            [description]
     */
    public function getProductSkuList($productId){
    	$options['where'] = array('productId' => (string)$productId);
    	$options['select'] = 'smtSkuCode';
    	$rs     = array();
    	$result = $this->getAll2Array($options);
    	if ($result) {
    		foreach ($result as $r) {
    			$rs[] = strtoupper(trim($r['smtSkuCode']));
    		}
    	}
    	return array_unique($rs);
    }

	/**
	 * 根据SKU模糊查询产品ID
	 * @param $sku
	 * @param $ignoreRemoved: 忽略删除的
	 * @return array
	 */
	public function getProductIdWithSku($sku, $ignoreRemoved=false){
		$sku = trim($sku);
		$sql = "SELECT DISTINCT productId FROM smt_product_skus WHERE skuCode LIKE '".$sku."%' ";
		if ($ignoreRemoved){
			$sql .= " AND isRemove = 0";
		}
		$result = $this->query($sql)->result_array();
		$rs = array();
		foreach ($result as $item){
			$rs[] = $item['productId'];
		}
		return $rs;
	}

	/**
	 * 获取广告对应的SKU信息--每个产品的SKU会合并在一起
	 * @param $product_array
	 * @param $ignoreRemoved 是否忽略删除的数据
	 * @return array
	 */
	public function getProductSkus($product_array, $ignoreRemoved=false){
		$rs = array();
		if ($product_array) { //没有就不要写吧，不然待会又报错
			$options['where_in'] = array('productId' => $product_array);
			$options['select']   = array("productId", "GROUP_CONCAT(skuCode) AS sku_code");
			$options['group_by'] = 'productId';
			if ($ignoreRemoved){
				$options['where'] = array('isRemove' => 0);
			}
			$array               = null;
			$data                = $this->getAll($options, $array, true);

			if ($data) {
				foreach ($data as $row) {
					$rs[$row['productId']] = $row['sku_code'];
				}
			}
		}
		return $rs;
	}

	/**
	 * 根据产品ID获取产品在售的SKU属性
	 * @param $productId
	 * @return mixed
	 */
	public function getProductSkuProperty($productId){
		//只查找没有被移出的部分isRemove=0
		$options['where']    = array('productId' => (string)$productId, 'isRemove' => 0);
		$options['select']   = array('productId', 'smtSkuCode', 'skuPrice', 'skuStock', 'ipmSkuStock', 'aeopSKUProperty', 'overSeaValId');
		$options['group_by'] = 'smtSkuCode, overSeaValId';
		$options['order_by'] = 'sku_id';
		$array               = null;
		return $this->getAll($options, $array, true);
	}
	
	/**
	 * 获取产品的SKU信息
	 * @param unknown $productId
	 * @param string $fields
	 * @return multitype:
	 */
	public function getProductSkuInfoList($productId, $fields='*'){
		$rs = array();
		
		if ($productId){
			$option['where'] = array('productId' => (string)$productId);
			$option['select'] = $fields ? $fields : '*';
			$rs = $this->getAll2Array($option);
		}
		
		return $rs;
	}

	/**
	 * 获取产品的最小售价
	 * @param $productIds 产品id数组
	 * @param bool $ignoreRemoved
	 * @return array
	 */
	public function getProductsMinPriceList($productIds, $ignoreRemoved=true){
		$rs = array();
		if ($productIds) { //没有就不要写吧，不然待会又报错
			$options['where_in'] = array('productId' => $productIds);
			$options['select']   = array("productId", "MIN(skuPrice) AS price");
			$options['group_by'] = 'productId';
			if ($ignoreRemoved){
				$options['where'] = array('isRemove' => 0);
			}
			$array               = null;
			$data                = $this->getAll($options, $array, true);

			if ($data) {
				foreach ($data as $row) {
					$rs[$row['productId']] = $row['price'];
				}
			}
		}
		return $rs;
	}

	/**
	 * 判断同一属性下的速卖通SKU是否已存在
	 * @param $productId
	 * @param $smtSkuCode
	 * @param $skuCode:这个最好添加下，不然用 +连接的可能就只保存了后一个
	 * @param $overSeaValId
	 * @return bool
	 */
	public function checkProductAndSmtSkuCodeIsExists($productId, $smtSkuCode, $skuCode, $overSeaValId){
		$where['productId']    = (string)$productId;
		$where['smtSkuCode']   = $smtSkuCode;
		$where['skuCode']      = $skuCode;
		$where['overSeaValId'] = $overSeaValId;
		$options = array(
			'select' => 'sku_id',
			'where'  => $where
		);
		$rs = $this->getOne($options, true);
		return !empty($rs['sku_id']) ? true : false;
	}
}

/* End of file Smt_product_skus_model.php */
/* Location: ./defaute/models/smt/Smt_product_skus_model.php */