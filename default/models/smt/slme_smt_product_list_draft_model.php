<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 速卖通产品列表模型类
 */
class Slme_smt_product_list_draft_model extends MY_Model {
    
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
     * 获取草稿基本数据信息
     * @param $id
     * @return Ambigous|array
     */
    public function getProductDraftInfo($id){
        if ($id){
            $options['where'] = array('id' => $id);
            return $this->getOne($options, true);
        }else {
            return array();
        }
    }
}

/* End of file Slme_smt_product_list_draft_model.php */
/* Location: ./defaute/models/smt/Slme_smt_product_list_draft_model.php */