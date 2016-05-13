<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * lazada承运商管理模型类
 */
class order_api_log_model extends MY_Model {

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
     * api交互状态日志
     * @param unknown $type
     * @param unknown $success
     */
    public function dhgateOrderApiLog($data){   
      $this->order_api_log_model->add($data);
    }
    /**
     * 更新api交互日志状态
     * @param unknown $apiInfo
     * @param unknown $data
     * @return unknown
     */
    public function updatedhgateOrderApiLog($startTime,$platform,$data){         
        $options = array();  
            
        $options['where']['start_time'] = $startTime;
        
        $options['where']['platform']   = $platform;               

        $this->order_api_log_model->update($data,$options);        
    }
    

    /**
     * 查询未完成的api交互
     * @param unknown $erp_orders_id
     * @return unknown
     */
    public function getFailApiOrderLog($api_name,$platform){
        $options = array();
    
        $options['select'] = 'count(*)';
    
        $where['api_name']=$api_name;
        $where['platform']=$platform;
        $where['status']  =1;
         
        $options['where']=$where;
    
    
        $data = $this->getOne($options,true);
    
        return $data;
    }
}