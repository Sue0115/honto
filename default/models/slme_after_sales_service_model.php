<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 售后模板模型类
 */
class Slme_after_sales_service_model extends MY_Model {
    
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
     * 获取一个模板的详情信息
     * @param $id
     * @return Ambigous
     */
    public function getTemplateInfo($id){
        $options = array();
        if ($id){
            $options['where'] = array('id' => $id);
        }

        $result = $this->getOne($options, true);
        return $result;
    }

    /**
     * 获取售后模板数组
     * @param array $array 查询条件
     * @return array 返回结果数组
     */
    public function getTemplateList($array = array())
    {
        $options = array();
        $where   = array();

        if (array_key_exists('select', $array) && $array['select']) {
            $select = $array['select'];
        } else {
            $select = '*';
        }

        if (isset($array['token_id']) && $array['token_id'] > 0) {
            $where['token_id'] = $array['token_id'];
        }
        if (isset($array['plat']) && $array['plat'] > 0) {
            $where['plat'] = $array['plat'];
        }
        $options = array(
            'select' => $select,
            'where'  => $where
        );

        $return_data = null;
        $result      = array();
        $result      = $this->getAll($options, $return_data, true);

        return $result;
    }
}

/* End of file Slme_after_sales_service_model.php */
/* Location: ./defaute/models/Slme_after_sales_service_model.php */