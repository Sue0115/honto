<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-11
 * Time: 10:29
 */

class Ebay_specifics_new_model extends MY_Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function getSpecificsBySite($categoryid,$siteid){
        $option = array();
        $option['where']['categoryid'] = $categoryid;
        $option['where']['site'] = $siteid;
        $option['order by '] ='id asc';
        $result =  $this->getAll2Array($option);
        if(!empty($result)){
            return $result;
        }else{
            return false;
        }
    }
}