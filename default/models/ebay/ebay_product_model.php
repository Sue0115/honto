<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-15
 * Time: 10:22
 */
class Ebay_product_model extends MY_Model{
    public function __construct()
    {
        parent::__construct();
    }



    public function checkProduct($site,$account_id,$sku){
        $option =array();
        $option['where']['site'] = $site;
        $option['where']['account_id'] = $account_id;
        $option['where']['erp_sku'] = $sku;
        $option['order_by'] = 'id desc';
        $result = $this->getAll2Array($option,true);
        if($result['0']['status'] == 2){   //最后一次更改状态为上架
            return false;
        }else{
            return true;
        }

    }

    public function getNewSku($sku){
        if (stripos($sku, '[') !== false) {
            $sku = preg_replace('/\[.*\]/', '', $sku);
        }
        if (stripos($sku, '(') !== false) {
            $sku = preg_replace('/\(.*\)/', '', $sku);
        }
        //去中括号后的sku中存在*
        if(strripos($sku, '*') !== false){
            $skuArr = explode('*',$sku);
            foreach($skuArr as $va){//如果不是数字的话，就是产品sku
                if(!is_numeric($va) && !empty($va)){
                    $sku = $va;
                    break;
                }
            }
        }
        if(strripos($sku, '#') !== false){
            $skuArr = explode('#',$sku);
            foreach($skuArr as $va){//如果不是数字的话，就是产品sku
                if(!is_numeric($va) && !empty($va)){
                    $sku = $va;
                    break;
                }
            }
        }

        return $sku;
    }

    public function getProductIdWithSku($sku, $status=false){
        $sku = trim($sku);
        $sql = "SELECT DISTINCT id FROM erp_ebay_product WHERE erp_sku LIKE '".$sku."%' ";
        if (!$status){
            $sql .= " AND status = 1";
        }else{
            $sql .= " AND status = 2";
        }
        $result = $this->query($sql)->result_array();
        $rs = array();
        foreach ($result as $item){
            $rs[] = $item['id'];
        }
        return $rs;
    }

    public function getALLUser(){
        $sql = "SELECT DISTINCT add_user FROM erp_ebay_product";
        $result = $this->query($sql)->result_array();
        $rs = array();
        foreach ($result as $item){
            $rs[] = $item['add_user'];
        }
        return $rs;
    }

}