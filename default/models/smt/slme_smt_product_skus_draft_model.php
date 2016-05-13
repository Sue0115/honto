<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通产品SKU模型类
 */
class Slme_smt_product_skus_draft_model extends MY_Model {
    
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
     * 根据SKU模糊查询产品ID
     * @param $sku
     * @param $status: 产品状态
     * @return array
     */
    public function getMainIdWithSku($sku){
        $sku = trim($sku);
        $sql = "SELECT main_id FROM erp_slme_smt_product_skus_draft WHERE skuCode LIKE '".$sku."%'";
        $result = $this->query($sql)->result_array();
        $rs = array();
        foreach ($result as $item){
            $rs[] = $item['main_id'];
        }
        return $rs;
    }

    /**
     * 获取广告对应的SKU信息--每个产品的SKU会合并在一起
     * @param $id_array
     * @return array
     */
    public function getProductDraftSkus($id_array){
        $rs = array();
        if ($id_array) { //没有就不要写吧，不然待会又报错
            $options['where_in'] = array('main_id' => $id_array);
            $options['select']   = array("main_id", "GROUP_CONCAT(skuCode SEPARATOR '，') AS sku_code");
            $options['group_by'] = 'main_id';
            $array               = null;
            $data                = $this->getAll($options, $array, true);

            if ($data) {
                foreach ($data as $row) {
                    $rs[$row['main_id']] = $row['sku_code'];
                }
            }
        }
        return $rs;
    }

    /**
     * 根据ID获取草稿SKU信息
     * @param $id
     * @return array
     */
    public function getProductDraftSkusWithMainId($id){
        if ($id){
            $options['where'] = array('main_id' => $id);
            $return_array = null;
            return $this->getAll($options, $return_array, true);
        }else {
            return array();
        }
    }
}

/* End of file Slme_smt_product_skus_draft_model.php */
/* Location: ./defaute/models/smt/Slme_smt_product_skus_draft_model.php */