<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class User_ship_statistical_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据时间段 获取数据
    public function getDataByDate($start_time,$end_time){
      $option = array();
      $where['ship_time >='] = $start_time;
      $where['ship_time <']  = $end_time;
      $option['where']  =  $where;
      return $this->getAll2Array($option);
    }
    
    //根据月获取数据
    public function getDataByMonth($start,$end){
	  	$where = array();
	    $option = array();
	    $where = array(
	      'ship_time >='=> $start,
	      'ship_time <' => $end
	    );
	    $select = array();
	    $select = array('sum(ship_orderCount) as orderCount','sum(ship_productCount) as productCount','ship_uid');
	    $option = array(
	      'select'  =>  $select,
	      'where'   =>  $where,
	      'group_by'=>  'ship_uid'
	      
	    );
	    return $this->getAll2array($option);
    }
}

/* End of file User_ship_statistical_model.php */
/* Location: ./defaute/models/User_ship_statistical_model.php */