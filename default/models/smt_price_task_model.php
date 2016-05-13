<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/11
 * Time: 9:04
 */

class smt_price_task_model extends MY_Model {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

    }


    // 获取需要执行的数据
    public function getinfo()
    {
        $sql= "SELECT  DISTINCT  account FROM erp_smt_price_task WHERE STATUS=1";
        $result = $this->query($sql)->result_array();
        return $result;
    }
    //获取平台费用率
    public function getProductsSalePlatList($platId)
    {
        $sql = "select * from erp_sales_platform where platID=$platId";
        $result = $this->query($sql)->result_array();
       return  $result[0]['platFeeRate'];
    }

    public function getShipFee($sID)
    {
        $sql = " select * from erp_shipment where shipmentID = '" . $sID . "'";
        $result = $this->query($sql)->result_array();
        return  $result[0];
    }

    /**
     * 根据币种求汇率
     * @return [type] [description]
     */
    public function getExchangeRateByType($type="RMB"){
        $sql = 'SELECT currency_value FROM `erp_currency_info` WHERE currency_type = "'.$type.'"';
        $result = $this->query($sql)->result_array();
        return  $result[0]['currency_value'];
    }

    /*
     *
     */
    public function getSkuInfo($sku)
    {
        $sql  = 'SELECT `products_sku`,`products_value`, `products_weight`, `products_with_battery`, products_with_fluid, products_with_powder, `products_sort` FROM erp_products_data WHERE products_sku = "'.$sku.'"';
        $result = $this->query($sql)->result_array();
        return  $result[0];
    }


}