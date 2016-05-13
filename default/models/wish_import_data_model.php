<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Wish_import_data_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据父sku和子sku查找数据
    public function getDataBySku($parent_sku,$sku,$account){
      $option = array();
      $where = array();
      $where = array(
      	'account' => $account,
        'parent_sku' => $parent_sku,
      	'original_sku' => $sku
      ); 
      $option['where'] = $where;
      return $this->getOne($option,true);
    }
    
	//根据id查找数据
    public function getDataById($id){
      $option = array();
      $where = array();
      $where = array(
      	'id' => $id
      ); 
      $option['where'] = $where;
      return $this->getOne($option,true);
    }
    
	//根据父sku和子sku删除数据
    public function deleteDataBySku($parent_sku,$sku,$account){
      $option = array();
      $where = array();
      $where = array(
        'account'  => $account,
        'parent_sku' => $parent_sku,
      	'original_sku' => $sku
      ); 
      $option['where'] = $where;
      return $this->delete($option);
    }
    
}

/* End of file Wish_import_data_model.php */
/* Location: ./defaute/models/Wish_import_data_model.php */