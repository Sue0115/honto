<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 亚马逊产品数据模板SKU模型类
 */
class Slme_amz_data_skus_model extends MY_Model {
    
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
     * 获取模板数据SKU列表
     * @param $id
     * @param $fields
     * @return Ambigous
     */
    public function getTemplateDataSkuList($id, $fields='*'){
        if (empty($fields)){
            $fields = '*';
        }elseif (is_array($fields)){
            $fields = implode(',', $fields);
        }elseif (is_string($fields)){
            $fields = $fields;
        }else {
            $fields = '*';
        }
        $options = array(
            'select' => $fields,
            'where' => array('pid' => $id)
        );
        $total_rows = null;
        return $this->getAll($options, $total_rows, true);
    }

    /**
     * 格式化数据信息
     * @param $id
     * @param $upperCase:大写处理标识
     * @return array
     */
    public function formatTemplateDataSkuList($id, $upperCase=true){
        $data = $this->getTemplateDataSkuList($id, 'sku');
        $rs = array();
        if (!empty($data)){
            foreach ($data as $row){
                $rs[] = $upperCase ? strtoupper($row['sku']) : $row['sku'];
            }
        }
        return $rs;
    }
}

/* End of file Slme_amz_data_skus_model.php */
/* Location: ./defaute/models/amz/Slme_amz_data_skus_model.php */