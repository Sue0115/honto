<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Use_shipped_num_model extends MY_Model {
    
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
     * 根据用户id和月份获取当月工时和发错数
     */
    public function getTimeAndCountByTime($uid,$month,$year=""){
      $option = array();
      $select = array();
      $where = array();
      $select = array('monthTotalTime','monthErrorNum','uid');
      $where = array(
        'uid'  => $uid,
        'months'=> $month
      );
      if(!empty($year)){
        $where['years'] = $year;
      }
      $option = array(
        'select'  => $select,
        'where'   => $where
      );
      $result = $this->getOne($option,true);
      return $result;
    }
}

/* End of file Use_shipped_num_model.php */
/* Location: ./defaute/models/order/Use_shipped_num_model.php */