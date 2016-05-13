<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/30
 * Time: 13:39
 */

class Ebay_transtemplate_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct('erp_ebay_transtemplate');
    }

    public function getTemplateAll($info)
    {
        $option['where'] = $info;
        return $this->getAll2Array($option);
    }

    public function addinfo($info)
    {
        $this->db->insert('erp_ebay_transtemplate', $info);

    }

    public function update($info, $id)
    {
        // $info['updatetime'] = date('Y-m-d H:i:s', time());
        $this->db->where('id', $id);

        $tof = $this->db->update('erp_ebay_transtemplate', $info);

        $tof = $this->db->affected_rows();

        return $tof;
    }

    public function delect($id)
    {
        $sql = "DELETE FROM erp_ebay_transtemplate WHERE id = $id";
        return $this->query($sql);
    }
}