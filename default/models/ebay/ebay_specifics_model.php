<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/6
 * Time: 15:19
 */
class Ebay_specifics_model extends MY_Model{

    public function __construct()
    {
        parent::__construct('erp_ebay_specifics');
    }

    public function getSpecificsAll($info)
    {
        $option['where'] =$info;
        return $this->getAll2Array($option);
    }

    public function getSpecificsOne($info)
    {
        $option['where'] =$info;
        return $this->getOne($option,true);
    }

    public function update($info,$id)
    {
        $info['updatetime'] =date('Y-m-d H:i:s',time());
        $this->db->where('id', $id);

        $tof = $this->db->update('erp_ebay_specifics', $info);

        $tof=$this->db->affected_rows();

        return $tof;
    }




}