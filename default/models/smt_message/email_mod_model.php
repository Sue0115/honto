<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-09
 * Time: 14:55
 */
class Email_mod_model extends MY_Model{
    public function __construst()
    {
        parent::__construct();
    }
    
    //根据父ID获取子表数据
    public function getDataByID($ID="",$type=""){
      $option = array();
      $option['where'] = array('modEnable'=>1);
      if($ID!="" && $type==1){
       $option['where'] = array('modClassID'=>$ID);
      }elseif($ID!="" && $type==2){
        $option['where'] = array('modID'=>$ID);
      }
      return $this->getAll2Array($option);
    }
}