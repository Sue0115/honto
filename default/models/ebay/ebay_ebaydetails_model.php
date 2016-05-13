<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 11:52
 */
class Ebay_ebaydetails_model extends MY_Model{
    public function __construct()
    {
        parent::__construct('erp_ebay_shippingdetails');
    }

    public function inserinfo($data)
    {
        $this->db->insert('erp_ebay_shippingdetails', $data);
    }

    public function getdetails($info)
    {
        $options['where'] =  $info;
        return $this->getOne($options, true);
    }

    public function getDetailsAll($info)
    {
        $option['where'] = $info;
        return $this->getAll2Array($option);
    }

    public function updatedetails($shippingserviceid,$data)
    {
        $data['updatetime'] =date('Y-m-d H:i:s',time());
        $this->db->where('shippingserviceid', $shippingserviceid);

        $tof = $this->db->update('erp_ebay_shippingdetails', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }

    public function getAccountList()
    {
        $sql = 'select * from erp_paypal_list WHERE paypal_enable=1';
        return $this->db->query($sql)->result_array();
    }
    public function getSkuLike($sku)
    {
        $sql = "SELECT products_sku  FROM  erp_products_data WHERE product_warehouse_id=1000 AND products_sku LIKE '".$sku."%'";
        return $this->db->query($sql)->result_array();
    }



}