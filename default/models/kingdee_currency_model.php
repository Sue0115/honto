<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-08-13
 * Time: 17:35
 */
class Kingdee_currency_model extends MY_Model
{

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 销售订单导出  -》导入金蝶
     */

    public function exportOrder($where)
    {
        $sql= "SELECT o.`erp_orders_id`, o.`orders_shipping_time`,o.`orders_ship_fee`, o.`orders_type`, o.`currency_type`, `p`.`orders_sku`, `p`.`item_count`, `p`.`item_price`,pd.products_name_cn FROM `erp_orders`AS o LEFT JOIN `erp_orders_products` p ON `p`.`erp_orders_id`=o.`erp_orders_id` LEFT JOIN  erp_products_data AS pd ON p.orders_sku=pd.products_sku  WHERE  o.orders_warehouse_id =pd.product_warehouse_id AND o.`orders_is_join` = 0   ".$where;
//echo $sql;exit;
            $result = $this->query($sql)->result_array();
        return $result;
    }


    /*
     *  采购订单导入-》导入金蝶
     */
    public function  exportPurchaseOrder($where)
    {
      /*  $sql ="SELECT p.po_id ,p.po_times,pp.op_pro_sku ,pp.op_pro_item,pp.op_pro_count_op,pp.op_pro_cost FROM erp_procurement AS p LEFT JOIN erp_procurement_products AS pp
ON p.po_id =pp.po_id  WHERE p.po_status=2 AND p.procurement_warehouse_id = 1000";*/


        $sql="SELECT  p.po_id ,p.procurement_warehouse_id ,p.po_user,p.po_shipping_fee,p.po_sp_company, a.arrival_id, a.arrival_sku,a.arrival_count_real,a.arrival_chk_time ,pd.products_name_cn FROM erp_procurement AS p INNER JOIN erp_procurement_arrivel AS a ON  p.po_id = a.erp_procurement_id LEFT JOIN erp_products_data AS pd ON pd.products_sku=a.arrival_sku WHERE p.procurement_warehouse_id =pd.product_warehouse_id  AND a.arrivalIsChecked=1  ".$where;

        $result = $this->query($sql)->result_array();
        return $result;
    }


    public function getPurchaseSkuPirce()
    {

        $sql = "SELECT p.po_id ,pp.op_pro_sku,pp.op_pro_cost FROM erp_procurement AS p LEFT JOIN erp_procurement_products AS pp ON p.po_id=pp.po_id WHERE p.po_times>'2015-05-01 00:00:00'";
        $result = $this->query($sql)->result_array();
        return $result;
    }

}