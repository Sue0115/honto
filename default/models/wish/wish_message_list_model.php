<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Wish_message_list_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
	//根据主键id获取信息
    public function getInfoByID($id){
       $option = array();
       $option['where'] = array('id'=>$id);
       return $this->getOne($option,true);
    }
    
    
    //根据邮件id获取信息
    public function getInfoByMailID($mailID){
       $option = array();
       $option['where'] = array('mailID'=>$mailID);
       return $this->getOne($option,true);
    }
    
    //根据邮件id更改邮件信息
    public function updateMailByID($where,$up_data=array()){
       $option = array();
       $data = $up_data;
       $option['where'] = $where;
       return $this->update($data,$option);
    }
    
 	//根据跟踪号查询信息
    public function track_info($code)
    {

        $sql = 'SELECT orders_shipping_code, description, carrier FROM sellertool_api_info_detail WHERE orders_shipping_code = "'.$code.'" ORDER BY reTime DESC limit 1';

        $query_arr = $this->result_array($sql);

        if (!empty($query_arr)){
        	
            $api_sql = 'SELECT carrier1, carrier2 FROM sellertool_api_info WHERE orders_shipping_code = "'.$query_arr[0]['orders_shipping_code'].'" limit 1';
            $query_api = $this->result_array($api_sql);

            if (!empty($query_api)) {
                $query_arr[0]['carrier1'] = $query_api[0]['carrier1'];
                $query_arr[0]['carrier2'] = $query_api[0]['carrier2'];
            }
            
            return $query_arr[0];
        }else {
            return null;
        }
    }
}

/* End of file Wish_message_list_model.php */
/* Location: ./defaute/models/wish/Wish_message_list_model.php */