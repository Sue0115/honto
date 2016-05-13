<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/11
 * Time: 10:41
 */
class Ebay_template_model extends MY_Model{

    public function __construct()
    {
        parent::__construct('erp_ebay_template');
    }

    public function getTemplateAll($info)
    {
        $option['where'] =$info;
        return $this->getAll2Array($option);
    }
    public function add($info)
    {
        $this->db->insert('erp_ebay_template', $info);

    }

    public function update($info,$id)
    {
        $info['updatetime'] =date('Y-m-d H:i:s',time());
        $this->db->where('id', $id);

        $tof = $this->db->update('erp_ebay_template', $info);

        $tof=$this->db->affected_rows();

        return $tof;
    }

    public function delect($id)
    {
        $sql = "DELETE FROM erp_ebay_template WHERE id = $id";
        return  $this->query($sql);
    }

}