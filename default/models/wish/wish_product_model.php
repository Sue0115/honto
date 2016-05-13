<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class wish_product_model extends MY_Model {
    
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
     * 根据广告ID获取广告数据
     */
    public function getInfoByProductID($productID){
      $option = array();
      $where = array();
      $where['productID'] = $productID;
      $option = array(
        'where' => $where
      );
      return $this->getOne($option,true);
    }
    
    /**
     * 根据广告ID更新广告数据
     */
    public function updateDataByProductID($data){
      $option = array();
      $where = array();
      $newData = array();//要更新的数据
      $newData['is_promoted'] = $data['is_promoted'];
      $newData['review_status'] = $data['review_status'];
      $newData['status'] = $data['is_promoted'];
      $newData['updateTime'] = date('Y-m-d H:i:s');
      $newData['product_description'] = $data['product_description'];
      $newData['product_name'] = $data['product_name'];
      $newData['parent_sku'] = $data['parent_sku'];
      $newData['Tags'] = $data['Tags'];
      $newData['sellerID'] = $data['sellerID'];
      $where['productID'] = $data['productID'];
      $option['where'] = $where;
      return $this->update($newData,$option);
    }
    
    /**
     * 获取所有的广告ID
     */
    public function getAllProductID($account){
      $option = array();
      $newProductID = array();//重组后的productID
      $where = array();
      $select = array('productID');
      if(!empty($account)){
        $where['account'] = $account;
      }
      $option['select'] = $select;
      $option['where'] = $where;
      $produtIDs = $this->getAll2Array($option);
      foreach($produtIDs as $p){
       $newProductID[] = $p['productID'];
      }
      return $newProductID;
    }
    
 	/**
     * 根据广告ID删除表数据
     * 
     */
    public function deleteByProductIDs($productID){
      $option = array();
      $where = array();
      $where['productID'] = $productID;
      $option = array(
        'where' => $where
      );
      return $this->delete($option);
    }
    
}

/* End of file Wish_product_list_model.php */
/* Location: ./defaute/models/wish/Wish_product_list_model.php */