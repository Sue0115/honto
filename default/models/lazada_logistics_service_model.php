<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * lazada承运商管理模型类
 */
class lazada_logistics_service_model extends MY_Model {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct('erp_lazada_logistics_service');

    }

    public function getLazadaShipName($shipmentLazadaCodeID)
    {
        $options = array();

        $options['select'] = array('logistics_name');

        $where['logistics_id ']=$shipmentLazadaCodeID;

        $options['where']=$where;


        $data = $this->getOne($options,true);

        return $data;
    }

    public function getErpidByOrderItemId($orderlineitemid)
    {
        $sql="SELECT * FROM erp_orders as o  LEFT JOIN erp_orders_products as p ON o.erp_orders_id = p.erp_orders_id  WHERE o.orders_is_join=0 AND p.orderlineitemid=".$orderlineitemid;
        $result = $this->query($sql);
    }
    public function countOrderByAccount()
    {
        $time1 = date('Y-m-d');
        $time2 = date('Y-m-d',strtotime('+1 day'));
        $sql="SELECT count( * ) AS num, sales_account FROM `erp_orders` WHERE `orders_type` =15 AND `orders_export_time` > '".$time1."' AND orders_export_time < '".$time2."'GROUP BY sales_account";
        $data = $this->query($sql)->result_array();
        return $data;
    }

    public function checkOrder()
    {
        $time1 = date('Y-m-d');
        $time2 = date('Y-m-d',strtotime('+1 day'));
        $sql ="SELECT COUNT( * ) AS num, `buyer_id`FROM `erp_orders`WHERE `orders_export_time` > '".$time1."' AND `orders_export_time` < '".$time2."'
         AND orders_type =15 AND orders_is_join =0 AND orders_status !=6 AND orders_is_split =0 GROUP BY buyer_id HAVING num >1 ORDER BY COUNT( * ) DESC";
        $data = $this->query($sql)->result_array();
        return $data;
    }
}