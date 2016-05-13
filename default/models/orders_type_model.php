<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 订单平台模型类
 */
class Orders_type_model extends MY_Model {
    
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
     * 获取平台类型
     * @param boolean $arr_flag:true就是二维数组，false就是一维数组
     * @param array $array
     * @return array
     */
    public function getOrdersType($arr_flag=true, $array=array()){
        $options = array();
        $where   = array();
        //组织查询条件
        if (isset($array['statistics_sold']) && $array['statistics_sold'] != ''){
            $where['statistics_sold'] = $array['statistics_sold'];
        }
        if (isset($array['publish_show']) && $array['publish_show'] != ''){
            $where['publish_show'] = $array['publish_show'];
        }

        $options['where'] = $where;
        $return_data = null;
        $result = $this->getAll($options, $return_data, true);

        $rs   = array();
        if ($result){
            foreach ($result as $row){
                $rs[$row['typeID']] = $arr_flag ? $row : $row['typeName'];
            }
        }
        return $rs;
    }
}

/* End of file Orders_type_model.php */
/* Location: ./defaute/models/Orders_type_model.php */