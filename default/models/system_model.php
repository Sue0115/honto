<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class System_model extends MY_Model {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();


    }
    //查找系统汇率
    public function get_current_value()
    {
        $options = array();

        $where = array();

        $options['select'] ="system_value ";

        $where['system_value_name']='EXCHANGE_RATE_TO_US';

        $options['where']=$where;

        $data = $this->getOne($options);

        return $data;
    }
    //根据id获取数据行
    public function getDataByID($id){
      $option['where'] = array('system_value_id'=>$id);
      return $this->getOne($option,true);
    }
}

/* End of file Orders_model.php */
/* Location: ./defaute/models/System_model.php */

