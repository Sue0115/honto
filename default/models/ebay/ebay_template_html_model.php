<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/31
 * Time: 18:20
 */

class Ebay_template_html_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct('erp_ebay_template_html');
    }

    public function getTemplateAll($info)
    {
        $option['where'] = $info;
        return $this->getAll2Array($option);
    }

    public function add($info)
    {
        $this->db->insert('erp_ebay_template_html', $info);

    }

    public function update($info, $id)
    {
       // $info['updatetime'] = date('Y-m-d H:i:s', time());
        $this->db->where('id', $id);

        $tof = $this->db->update('erp_ebay_template_html', $info);

        $tof = $this->db->affected_rows();

        return $tof;
    }

    public function delect($id)
    {
        $sql = "DELETE FROM erp_ebay_template_html WHERE id = $id";
        return $this->query($sql);
    }
}

