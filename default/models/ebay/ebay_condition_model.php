<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/29
 * Time: 14:36
 */
class Ebay_condition_model extends MY_Model{
    public function __construct()
    {
        parent::__construct('erp_ebay_condition');
    }


    public function getConditionOne($info)
    {
        $options['where'] =  $info;
        return $this->getOne($options, true);
    }


    public function updateInfo($id,$data)
    {
        $data['update_time'] =date('Y-m-d H:i:s',time());
        $this->db->where('id', $id);

        $tof = $this->db->update('erp_ebay_condition', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }

    public function getConditionAll($info)
    {
        $option['where'] =$info;
        return $this->getAll2Array($option);
    }



    public function getConditionBySite($categoryid,$siteid){
        $option = array();
        $option['where']['categoryid'] = $categoryid;
        $option['where']['siteid'] = $siteid;
        $option['order '] ='id asc';
        $result = $this->getAll2Array($option);
        if(empty($result)){
            return false;
        }else{
            return $result;
        }

    }






}