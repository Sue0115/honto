<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-08
 * Time: 15:26
 */

class Smt_issue_list_model extends MY_Model{
    public function __construct(){
        parent::__construct();
    }


    public function getOneList($order_id){
        $option = array();
        $option['where']['order_id_child'] = (string)$order_id;
       return  $this->getone($option,true);
    }


    public function getNum(){
        $sql ="SELECT COUNT(*) AS num  ,order_id FROM erp_smt_issue_list WHERE is_new = 1  GROUP BY order_id HAVING num>1";
        return  $this->query($sql)->result_array();

    }

    public function getReason(){
        $sql ="SELECT issue_reason_cn FROM erp_smt_issue_list WHERE is_new=1 GROUP BY issue_reason_cn";
        return  $this->query($sql)->result_array();
    }

    public function getMoneyBackAmountArray($id, $type = 'orders'){
        $sqlMoneyBack = " select moneyBackProductsSKU as sku,moneyBackProductsAmount as amount,moneyBackProductsQuantity as quantity from erp_moneyback_products where moneyBackID = '" . $id . "'";
        //$sqlOrders = " select orders_sku as sku,(item_price * item_count) as amount,item_count as quantity from erp_orders_products where erp_orders_id = '".$id."' union select '运费' as sku,orders_ship_fee as amount,1 as quantity from erp_orders where erp_orders_id = '".$id."' ";

        $sqlOrders = " select orders_sku as sku,(item_price * item_count) as amount,item_count as quantity from erp_orders_products where erp_orders_id = '" . $id . "' ";

        if ( $type == 'orders' ) {
            $rs =  $this->query($sqlOrders)->result_array();
        } elseif ( $type == 'moneyBack' ) {
            $rs = $this->query($sqlMoneyBack)->result_array();
        } else {
            $rs = false;
        }
        return $rs;
    }




}