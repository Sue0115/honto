<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-05
 * Time: 13:54
 */

class Smt_msg_list_model extends MY_Model{
    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

    }

    function check_issue( $buyer_id )
    {
        $sql = "select count(*) as num from erp_orders_details where plat_order_id = '$buyer_id' and smt_issue = 1";
        $result = $this->query($sql)->result_array();

        return $result;

    }


    //根据跟踪号查询信息
     function track_info($code)
    {

        $sql = 'SELECT orders_shipping_code, description, carrier FROM sellertool_api_info_detail WHERE orders_shipping_code = "'.$code.'" ORDER BY reTime DESC limit 1';

        $query_arr = $this->query($sql)->result_array();

        if (!empty($query_arr))
        {
            $api_sql = 'SELECT carrier1, carrier2 FROM sellertool_api_info WHERE orders_shipping_code = "'.$query_arr[0]['orders_shipping_code'].'" limit 1';
            $query_api = $this->query($api_sql)->result_array();

            if (!empty($query_api)) {
                $query_arr[0]['carrier1'] = $query_api[0]['carrier1'];
                $query_arr[0]['carrier2'] = $query_api[0]['carrier2'];
            }
            return $query_arr;
        }else {
            return null;
        }
    }


    function getOrderMoneyBackInfo($orderID){
        $sql = "SELECT * FROM erp_moneyback WHERE erp_orders_id = ".$orderID;
        $data = $this->query($sql)->result_array();
        return $data;
    }


    function messageCount($sql)
    {
        $sql = "SELECT COUNT(*) AS num , SUM(isRead) AS isRead , SUM(reply_no) AS reply_no ,SUM(isReturn) AS isReturn,  token_id FROM erp_smt_msg_list WHERE ".$sql." GROUP BY token_id ORDER BY token_id ASC";

        $data = $this->query($sql)->result_array();
        return $data;
    }


    function messageCountByUser($sql)
    {
        $sql ="SELECT COUNT(*) AS num ,user_id  FROM  erp_smt_msg_reply WHERE ".$sql."  GROUP BY user_id ";

        $data = $this->query($sql)->result_array();
        return $data;
    }
}