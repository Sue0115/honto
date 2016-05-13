<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Ebay_store_category_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }


    public function getALLCategoryWithAccountID(){
        $result = $this->getAll2Array();
        $return_array =array();
        foreach($result as $re){
            $return_array[$re['token_id']][$re['category_id']] = $re['category_name'];
        }
        return $return_array;
    }


    public function getCategoryNameById($token_id,$category_id){

        $option =array();
        $option['where']['category_id'] =$category_id;
        $option['where']['token_id'] = $token_id;
         $result = $this->getOne($option,true);
       // echo  $this->db->last_query();

        $category_name = isset($result['category_name'])?$result['category_name']:"";

        return $category_name;
    }

    public function getALLCategory($token_id){
        $result_all=array();
        $option =array();
        $option['where']['token_id'] = $token_id;
        $option['select'] = 'category_id,category_name,level,category_parent';
        $result = $this->getAll2Array($option);
        foreach($result as $re){
            if($re['level']==1){
                $result_all['root'][] = $re;
            }else{
                    $result_all['child'][$re['category_parent']][] = $re;
                }
            }

         return  $result_all;



    }
}

/* End of file Ebay_store_category_model.php */
/* Location: ./defaute/models/ebay/Ebay_store_category_model.php */