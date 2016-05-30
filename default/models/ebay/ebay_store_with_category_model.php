<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Ebay_store_with_category_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }

    public function getStoreCategoryBySkuTokenId($sku,$token_id){

        if (stripos($sku, '[') !== false) {
            $sku = preg_replace('/\[.*\]/', '', $sku);
        }
        if (stripos($sku, '(') !== false) {
            $sku = preg_replace('/\(.*\)/', '', $sku);
        }
        //去中括号后的sku中存在*
        if(strripos($sku, '*') !== false){
            $skuArr = explode('*',$sku);
            foreach($skuArr as $va){//如果不是数字的话，就是产品sku
                if(!is_numeric($va) && !empty($va)){
                    $sku = $va;
                    break;
                }
            }
        }
        if(strripos($sku, '#') !== false){
            $skuArr = explode('#',$sku);
            foreach($skuArr as $va){//如果不是数字的话，就是产品sku
                if(!is_numeric($va) && !empty($va)){
                    $sku = $va;
                    break;
                }
            }
        }


        $sql="SELECT products_sort   FROM  erp_products_data where productsIsActive = 1 AND products_sku like  '".$sku."%' ";


        $result =  $this->db->query($sql)->result_array();
        if(isset($result[0]['products_sort'])&&!empty($result[0]['products_sort'])){
            $option = array();
            $option['where']['erp_category'] = $result[0]['products_sort'];

           $one_info =  $this->getOne($option,true);
            if(!empty($one_info)){
                $category_with_store = json_decode($one_info['category_with_store'],true);

                if(isset($category_with_store[$token_id])){

                    return $category_with_store[$token_id];
                }else{
                    return false;
                }

            }else{
                return false;
            }

        }else{
            return false;
        }
    }
}


/* End of file Ebay_store_with_category_model.php */
/* Location: ./defaute/models/ebay/Ebay_store_with_category_model.php */