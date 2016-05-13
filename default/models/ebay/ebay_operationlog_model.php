<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/9
 * Time: 14:23
 */


class Ebay_operationlog_model extends MY_Model{
    public function __construct()
    {
        parent::__construct('erp_ebay_operation_log');
    }

    public function inserinfo($data)
    {
        $this->db->insert('erp_ebay_operation_log', $data);
    }

    public function getOperationOne($info)
    {
        $options['where'] =  $info;
        return $this->getOne($options, true);
    }

    public function updateOperation($id,$data)
    {
        //$data['updatetime'] =date('Y-m-d H:i:s',time());
        $this->db->where('id', $id);

        $tof = $this->db->update('erp_ebay_operation_log', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }

    public function getOperationAll($info)
    {
        $options['where'] = $info;
        return $this->getAll2Array($options);
    }

}