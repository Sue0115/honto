<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/15
 * Time: 13:26
 */
class Smt_auto_draft_list_model extends MY_Model{
    public function __construct() {
        parent::__construct();

    }



    public function getSkuKeyWord($sku)
    {
        $sql = "SELECT must_keyword,option_keyword  FROM erp_products_data WHERE products_sku ='".$sku."'";
        return  $result = $this->query_array($sql);
    }


    public function getSmtAccount()
    {
        $sql = "SELECT *  FROM  smt_user_tokens";
        return  $result = $this->query($sql)->result_array();
    }


    public function updateListInfo($productid,$data)
    {
        $this->db->where('productId', $productid);

        $tof = $this->db->update('smt_product_list', $data);

        $tof=$this->db->affected_rows();

        return $tof;
    }




    public function get_skus_by_productId($productId)
    {
        $sql = "SELECT *  FROM smt_product_skus WHERE productId ='".$productId."'";
        return  $result = $this->query($sql)->result_array();
    }


    public function get_list_by_productId($productId)
    {
        $sql = "SELECT *  FROM smt_product_list WHERE productId ='".$productId."'";
        return  $result = $this->query_array($sql);
    }

    public function get_detail_by_productId($productId)
    {
        $sql = "SELECT *  FROM smt_product_detail WHERE productId ='".$productId."'";
        return  $result = $this->query_array($sql);
    }

    public function inset_sku_info($data)
    {
        $this->db->insert('smt_product_skus', $data);
        return $this->db->insert_id();
    }

    public function inset_list_info($data)
    {
        $this->db->insert('smt_product_list', $data);
        return $this->db->insert_id();
    }


    public function inset_datail_info($data)
    {
        $this->db->insert('smt_product_detail', $data);
        return $this->db->insert_id();
    }


    public function updateWord($word,$data,$sku)
    {
        $sql = "SELECT $word  FROM erp_products_data WHERE products_sku ='".$sku."'";
        $result = $this->query_array($sql);
        $str = isset($result[$word])?$result[$word]:'';
        $str = $str.','.$data;
        $sql = "UPDATE  erp_products_data SET $word='". $str."' WHERE products_sku ='".$sku."'";
        $re=  $this->query($sql);
        return $re;
    }


    public function updateInfoByProductid($productid,$info)
    {
        $this->db->where('productId', $productid);

        $tof = $this->db->update('smt_product_detail', $info);

        $tof=$this->db->affected_rows();

        return $tof;
    }
}