<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 自定义AMZ上架模型类
 */
class Slme_amz_listing_template_model extends MY_Model {
    
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


}

/* End of file Slme_amz_category_model.php */
/* Location: ./defaute/models/amz/Slme_amz_category_model.php */