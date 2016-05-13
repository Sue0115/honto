<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-08
 * Time: 15:27
 */
class Smt_issue_detail_model extends MY_Model{
    public function __construct(){
        parent::__construct();
    }


    public function getOneDetail($orderId){
        $option = array();
        $option['where']['orderId'] = $orderId;
        return  $this->getone($option,true);
    }
}