<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通产品详情模型类
 */
class Slme_smt_product_detail_draft_model extends MY_Model {
    
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
     * 查找草稿详情
     * @param $id
     * @return Ambigous|array
     */
    public function getProductDetail($id){
        if ($id){
            $options['where'] = array('main_id' => $id);
            return $this->getOne($options, true);
        }else {
            return array();
        }
    }

    public function getProductsFields($ids_array, $fields='*'){
        if ($ids_array){
            $option['select'] = $fields ? $fields : '*';
            if (is_array($ids_array)){
                $option['where_in']  = array('main_id' => $ids_array);
            }else {
                $option['where']  = array('main_id' => $ids_array);
            }

            $data = $this->getAll2Array($option);
            $rs = array();
            if ($data){
                foreach ($data as $row){
                    $rs[$row['main_id']] = $row;
                }
            }
            return $rs;
        }else {
            return array();
        }
    }
}

/* End of file Slme_smt_product_detail_draft_model.php */
/* Location: ./defaute/models/smt/Slme_smt_product_detail_draft_model.php */