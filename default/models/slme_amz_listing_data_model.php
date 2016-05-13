<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 自定义AMZ上架数据类
 */
class Slme_amz_listing_data_model extends MY_Model {
    
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
     * 获取AMZ上架数据表字段
     * @param $params
     * @param $return_arr
     * @param $fields: 字段信息
     * @return mixed
     */
    public function getColumnsList(){
       return $this->getFields();
    }
    
    /**
     * 修改单条记录
     * @param unknown $data
     * @param unknown $id
     * @return Ambigous <object, boolean>
     */
    public function updateTemplateInfo($data,$id){
        $options = array();
        $options['where']['id'] = $id;
        return $this->update($data,$options);
    }
    
    /**
     * 删除单条记录
     * @param unknown $data
     * @param unknown $id
     * @return Ambigous <object, boolean>
     */
    function deleteTemplateInfo($id){
        $same_product_idArr = array();
        $options = array();
        $deleteOptions = array();
        $options['where']['id'] = $id;
        $same_product_idArr = $this->getOne($options,true);
        $deleteOptions['same_product_id'] = $same_product_idArr['same_product_id'];
        return $this->delete($deleteOptions);
    }
}

/* End of file Slme_amz_category_model.php */
/* Location: ./defaute/models/amz/Slme_amz_category_model.php */