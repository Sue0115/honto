<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * 用户组管理模型类
 */
class Currency_info_model extends MY_Model {
    
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        
    }
    
    //根据币种获取兑换值
    public function getValue($type){
      $select=array('currency_value');
      $where=array(
        'currency_type'=>$type,
      );
      $option=array(
        'select' => $select,
        'where'  => $where,
      );
      $value=$this->getOne($option,true);
      return $value['currency_value'];
    }
    //根据 currency_id 获取兑换值
	public function getValueByID($ID){
      $select=array('currency_value');
      $where=array(
        'currency_id'=>$ID,
      );
      $option=array(
        'select' => $select,
        'where'  => $where,
      );
      $value=$this->getOne($option,true);
      return $value['currency_value'];
    }
    //获取所有的币种数据
    public function getAllInfo($type=""){
      $option = array();
      $data = $this->getAll2Array($option);
      $new_data = array();
      foreach($data as $d){
      	if($type=="currency_value"){
      	  $new_data[$d['currency_type']] = $d['currency_value'];
      	}else{
      	  $new_data[$d['currency_type']] = $d['currency_name'];
      	}
        
      }
      return $new_data;
    }
}

/* End of file Currency_info_model.php */
/* Location: ./defaute/models/order/Currency_info_model.php */