<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通刊登广告详情模型类
 */
class Smt_product_detail_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('smt_product_detail');
        
    }

    /**
     * 判断产品详情是存在
     * @param  [type] $productId [description]
     * @return [type]            [description]
     */
    public function check_detail_is_exists($productId){
        $options['where'] = array('productId' => (string)$productId);
        $result  = $this->getOne($options, true);
        return $result ? true : false;
    }

    /**
     * 根据产品ID获取产品详情信息
     * @param $productId
     * @param $fields 查询的字段
     * @return Ambigous
     */
    public function getProductDetailInfo($productId, $fields='*'){
        if (!empty($fields) && trim($fields) != '*'){
            $options['select'] = $fields;
        }
        $options['where'] = array('productId' => (string)$productId);
        return $this->getOne($options, true);
    }
    
    /**
     * 获取产品详情的部分字段列表
     * @param unknown $productIds:数组
     * @param string $fields
     * @return multitype:unknown |multitype:
     */
    public function getProductDetailsFields($productIds, $fields='*'){
    	if ($productIds){
    		$option['select'] = $fields ? $fields : '*';
    		if (is_array($productIds)){
    			$option['where_in'] = array('productId' => $productIds);
    		}else {
    			$option['where'] = array('productId' => $productIds);
    		}
    		$data = $this->getAll2Array($option);
    		
    		$rs = array();
    		if ($data){
    			foreach ($data as $row){
    				$rs[$row['productId']] = $row;
    			}
    		}
    		return $rs;
    	}else {
    		return array();
    	}
    }
}

/* End of file Smt_product_skus_model.php */
/* Location: ./defaute/models/smt/Smt_product_skus_model.php */