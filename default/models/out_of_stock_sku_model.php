<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-04
 * Time: 11:25
 */
class Out_of_stock_sku_model extends MY_Model {

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();

    }


    public function getPalt(){
        $sql ="SELECT * FROM erp_order_type WHERE status=1 ";
        $result = $this->query($sql)->result_array();
        $return = array();
        foreach($result as $re){
            $return[$re['id']] = $re['name'];
        }
        return $return;
    }



    public function getOrders($option,$is_mix){


        $retrun_array =array();
        $string = '';
        if(!empty($option['sku'])){
            $string .=" and  sku ='".trim($option['sku'])."'";
        }

        if(!empty($option['reason'])){
            $string .=" and  reason =".trim($option['reason']);
        }

        if(!empty($option['products_status_2'])){
            $string .=" and  products_status_2 ='".trim($option['products_status_2'])."'";
        }


        $sql1 = "SELECT sku FROM erp_out_of_stock_sku LEFT JOIN erp_products_data ON  erp_out_of_stock_sku.sku = erp_products_data.products_sku WHERE status=2 ".$string ."  group by sku ";
      //  echo $sql1;  exit;
        $result = $this->query($sql1)->result_array();

    //    var_dump($result);exit;

        if(!empty($result)){
            $sku_array= array();
            $sku_string = '';
            foreach($result as $v)
            {
                $sku_string .="'".$v['sku']."',";
                $sku_array[] =$v['sku'];
            }
            $sku_string = substr($sku_string,0,strlen($sku_string)-1);

            $platform ='';
            if(!empty($option['platform']))
            {
                $platform = ' AND o.orders_type ='.$option['platform'];
            }
            $back_day='';
            if(!empty($option['back_day'])){
                $day = $option['back_day'];
                $back_day = " AND o.orders_export_time <= '".date('Y-m-d H:i:s',strtotime('-'.$day.' day'))."'";
            }

            $is_split='';
            if(!empty($option['is_split'])){
                $is_split =' AND o.orders_is_split !=0';
            }else{
                $is_split =' AND o.orders_is_split =0';
            }

            $sql2 = "SELECT o.erp_orders_id FROM erp_orders as o LEFT JOIN erp_orders_products as op ON o.erp_orders_id=op.erp_orders_id WHERE
             orders_is_join =0 AND  ebayStatusIsMarked = 1  AND orders_is_backorder = 1 AND orders_status IN (1,3)AND op.orders_sku IN (".$sku_string.") ".$back_day.$platform.$is_split;

           // echo $sql2;exit;

            $orders_array = $this->query($sql2)->result_array();

            if(!empty($orders_array)) {
                $order_id_string = '';

                foreach ($orders_array as $orders) {
                    $order_id_string .= $orders['erp_orders_id'].",";
                }
                $order_id_string = substr($order_id_string, 0, strlen($order_id_string) - 1);
                if ($is_mix == 2)
                {
                    $string_is_mix=" HAVING NUM > 1";
                }else{
                    $string_is_mix=" HAVING NUM = 1";
                }
                $sql3 = "SELECT count(*) as num, o.erp_orders_id,o.orders_type,o.orders_export_time,o.buyer_id,o.sales_account,op.orders_sku,op.item_count,GROUP_CONCAT(op.item_count) AS mix_num,GROUP_CONCAT(op.orders_sku) AS mix_sku   FROM erp_orders as o LEFT JOIN erp_orders_products as op ON o.erp_orders_id=op.erp_orders_id WHERE
                  o.erp_orders_id IN (".$order_id_string.") GROUP BY erp_orders_id  ".$string_is_mix;

                $last_result = $this->query($sql3)->result_array();

                $retrun_array['sku']=$sku_array;
                $retrun_array['last_result'] = $last_result;

                return $retrun_array;
            }else{
                return $retrun_array;
            }
        }else{
        return  $retrun_array;
        }

    }




    public function  getSkuCount($sku){
        $last_result =array();
        $sql ="SELECT orders_sku, SUM( item_count ) AS num FROM erp_orders_products AS op LEFT JOIN erp_orders AS o ON op.erp_orders_id = o.`erp_orders_id`   WHERE o.orders_is_backorder =1 AND o.orders_status !=6  AND orders_is_join =0 AND op.orders_sku = '".$sku."'";
    //  echo $sql;exit;
        $reslut  = $this->query($sql)->result_array();
    //    var_dump($reslut);exit;
        if(!empty($reslut[0])){

            $last_result['is_success']=true;
            $last_result['num'] = $reslut[0]['num'];

        }else{
            $last_result['is_success']=false;
            $last_result['num'] = '未存在欠货订单';

        }

        return $last_result;

    }

    public function get_detele_sku($option){
        $string = '';
        if(!empty($option['sku'])){
            $string .=" and  sku ='".trim($option['sku'])."'";
        }

        if(!empty($option['reason'])){
            $string .=" and  reason =".trim($option['reason']);
        }

        if(!empty($option['status'])){
            $string .=" and  status =".trim($option['status']);
        }

        if(!empty($option['products_status_2'])){
            $string .=" and  products_status_2 ='".trim($option['products_status_2'])."'";
        }


        $sql1 = "SELECT sku FROM erp_out_of_stock_sku LEFT JOIN erp_products_data ON  erp_out_of_stock_sku.sku = erp_products_data.products_sku WHERE 1=1 ".$string ."  group by sku ";
         // echo $sql1;  exit;
        $result = $this->query($sql1)->result_array();

        return $result;









    }


}