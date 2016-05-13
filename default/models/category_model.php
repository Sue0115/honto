<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Category_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
	public function defineProductsType($tID = '') //定义产品类型
    {
        $where = array(
        	'category_status' => 0
        );
        
        if ($tID)
        {
        	$where['category_id'] = $tID;
        }
        
        $options = array(
        	'where' => $where
        );
        
        $result = $this->getAll2Array($options);
        $newArr = array();
        foreach ($result as $rs)
        {
        	$newArr[$rs['category_id']] = $rs;
        }
        return $newArr;
    }
    
    
	public function getAllCategory(){
     $where=array(
       'category_status'=>0,
     );

     $option=array(
       'where' => $where,

     );
     
     return $this->getAll2Array($option);
    }	
    
    //根据分类id获取分类信息
    public function getCateInfoById($id){
      $option = array();
      $where = array();
      $where['category_id'] = $id;
      $option['where']  = $where;
      return $this->getOne($option,true);
    }
    
}

/* End of file Category_model.php */
/* Location: ./defaute/models/Category_model.php */