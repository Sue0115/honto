<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-09
 * Time: 14:54
 */
class Email_mod_class_model extends MY_Model{
	
   public  function __construct()
    {
        parent::__construct();
    }
    
    
    //获取订单平台的回信模块
    public function getMessageTemplateByType($type=""){
      $option = array();
      $where = array();
      if($type!=""){
        $where['platform'] = $type;
        $option['where'] = $where;
      }
      return $this->getAll2Array($option);
    }
}