<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-09-16
 * Time: 16:37
 */
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");

class Auto_lazada_shippingcode extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->library('MyLazada');

        $this->load->model(array(
                'system_model', 'order/orders_model', 'order/orders_products_model', 'lazada_token_model', 'lazada_logistics_service_model')
        );
        $this->lazada = new MyLazada();
    }


    public function auto_get_shippingcode()
    {
        $time = date('Y-m-d H:i:s');
        $time1= date('Y-m-d').' 23:47:00';
        $time2= date('Y-m-d').' 23:59:00';
        if(($time>$time1)&&($time<$time2))
        {
            $this->countOrderByAccount();
            $this->checkOrder();
        }
        $token_info = $this->lazada_token_model->getAll2Array();

        $site_array=array('https://sellercenter-api.lazada.com.my','https://sellercenter-api.lazada.co.id','https://sellercenter-api.lazada.com.ph','https://sellercenter-api.lazada.co.th','https://sellercenter-api.lazada.sg');
        foreach ($token_info as $token) {
            if(!in_array($token['api_host'],$site_array))
            {
                continue;
            }
            $orders_mail = array();
            $orders_single_sku = array();
            //处理欠货的订单 超过2天 没有标记的订单
            $orders_option = array();
            $orders_option['where']['orders_type'] = 15;
            $orders_option['where']['orders_status <='] = 5;
            $orders_option['where']['orders_status >='] = 3;
            $orders_option['where']['ebayStatusIsMarked'] = 0;
            $orders_option['where']['orders_is_join'] = 0;
            $orders_option['where']['orders_is_backorder'] =1;
            $orders_option['where']['orders_export_time >='] = '2015-09-19 00:00:00';
            $orders_option['where']['orders_export_time <='] = date('Y-m-d H:i:s',strtotime('-1.5 day'));
            $orders_option['where']['sales_account'] = $token['sales_account'];
            $orders_data_backorder = $this->orders_model->getAll2Array($orders_option);
            if(!empty($orders_data_backorder)){

                foreach($orders_data_backorder as $order)
                {
                    $product_option = array();
                    $product_option['where']['erp_orders_id'] = $order['erp_orders_id'];

                    $product_result = $this->orders_products_model->getAll2Array($product_option);

                    if(isset($product_result[1])) //sku 种类大于2
                    {
                        //当执行时间-(导入时间+2天）<30*60 发送邮件，不然然一直发邮件。。。
                        if(  time()-(strtotime($order['orders_export_time'])+48*60*60)<20*60 )
                        {
                            $orders_mail[] = $order['erp_orders_id'];
                        }

                    }
                    else
                    {
                        $orders_single_sku[] = $order;
                    }
                }
            }
            if(!empty($orders_mail))
            {
                $string='';
                foreach($orders_mail as $mail)
                {
                    $string .=$mail.' 订单需要检查或拆分 ';
                }
                $this->checkAPICount($string,'Lazada多品欠货');
            }
           /* //处理欠货超过3天的
            $orders_option = array();
            $orders_option['where']['orders_type'] = 15;
            $orders_option['where']['orders_status <='] = 5;
            $orders_option['where']['orders_status >='] = 3;
            $orders_option['where']['ebayStatusIsMarked'] = 0;
            $orders_option['where']['orders_is_join'] = 0;
            $orders_option['where']['orders_is_backorder'] =1;
            $orders_option['where']['orders_export_time >='] = '2015-09-19 00:00:00';
            $orders_option['where']['orders_export_time <='] = date('Y-m-d H:i:s',strtotime('-3 day'));
            $orders_option['where']['sales_account'] = $token['sales_account'];
            $orders_data_three_day = $this->orders_model->getAll2Array($orders_option);
            $orders_data_three_day_mid =array();
            if(!empty($orders_data_three_day)){
                foreach($orders_data_three_day as $order){
                    if($order['orders_is_split']==0){  // 说明这个订单欠货 没有拆分过， 如果是单品欠货 也会跳过，但是没有影响，上面 {处理欠货的订单 超过2天 没有标记的订单} 已经包含了单品欠货的订单

                        continue;
                    }else{
                        $orders_data_three_day_mid[]=$order;
                    }
                }
            }

            $orders_data_three_day =$orders_data_three_day_mid;
            unset($orders_data_three_day_mid);*/





            //处理不欠货的订单
            $orders_option = array();
            $orders_option['where']['orders_type'] = 15;
            $orders_option['where']['orders_status <='] = 5;
            $orders_option['where']['orders_status >='] = 3;
            $orders_option['where']['ebayStatusIsMarked'] = 0;
            $orders_option['where']['orders_is_join'] = 0;
            $orders_option['where']['orders_is_backorder'] =0;
            $orders_option['where']['orders_export_time >='] = date('Y-m-d H:i:s',strtotime('-10 day'));
           // $orders_option['where']['orders_export_time <='] = date('Y-m-d H:i:s',strtotime('-1 day'));
            $orders_option['where']['sales_account'] = $token['sales_account'];
            $orders_option['limit'] = 100;

            $orders_data = $this->orders_model->getAll2Array($orders_option);


            shuffle($orders_data);

            $orders_data = array_slice($orders_data,0,20);
            $orders_data = array_merge($orders_data,$orders_single_sku);
           // $orders_data = array_merge($orders_data,$orders_data_three_day);




            foreach ($orders_data as $v) {
                $lazadaOrderItemId = $this->orders_products_model->getLazadaOrderItemId($v['erp_orders_id']); //找出订单对应产品的OrderItemId

                $shipmentLazadaCodeID = $this->orders_model->lazadaOrderGetShipName($v['erp_orders_id']);
                // echo $v['erp_orders_id'];

                if (empty($shipmentLazadaCodeID)) {
                    $op = $v['erp_orders_id'] . "没有物流编号，无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 2);
                    continue;
                }

                $ShippingProvider = $this->lazada_logistics_service_model->getLazadaShipName($shipmentLazadaCodeID['shipmentLazadaCodeID']);

                if (empty($ShippingProvider)) {
                    $op = $v['erp_orders_id'] . "没有物流名称，无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 2);
                    continue;
                }

                if (empty($lazadaOrderItemId)) {
                    $op = $v['erp_orders_id'] . "没有找到对应产品，无法上传追踪号";
                    $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 2);
                    continue;
                }


                $OrderItemIds = '';
                foreach ($lazadaOrderItemId as $ItemId) {
                    if ($ItemId['comment_text'] == '') // comment_text 为空的订单，不进行上传追踪号操作
                    {
                        $op = $v['erp_orders_id'] . "没有进行上传追踪号操作，存在为空的orderlineitemid";
                        $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 2);
                        break;
                    }
                    $str = explode(',', $ItemId['comment_text']);
                    foreach ($str as $st) {
                        $t = explode('@', $st);
                        $OrderItemIds = $t[0] . ',' . $OrderItemIds;
                    }
                }


                $OrderItemIds = substr($OrderItemIds, 0, strlen($OrderItemIds) - 1); //取出字符串最后一个,

                sleep(2);// 上传追踪号先等15S 在请求
                //处理一下打包的sku 例如 A+B    在系统内为2个sku 信息，即会出现2个相同的Item-Id
                $OrderItemIds = explode(',', $OrderItemIds);
                $OrderItemIds = array_unique($OrderItemIds);
                $OrderItemIds = implode(",", $OrderItemIds);
                $relut = $this->lazada->upLoadShippingCode($token['api_host'], $token['Key'], $token['lazada_user_id'], trim($v['orders_shipping_code']), $OrderItemIds, $ShippingProvider['logistics_name']);
                $re = $this->XmlToArray($relut);
                print_r($re);

                if (isset($re['Body']['OrderItems']['OrderItem'])) {
                    $upResult = $this->orders_model->upDataOrderByShip($v['erp_orders_id']); //更新订单状态
                    $op = $v['erp_orders_id'] . "上传空追踪号成功，并更新对应的订单信息";
                    $this->orders_model->lazadaOrderOperating_log($type = 5, $op, $success = 1);

                } else {
                    $op = $v['erp_orders_id'] . "上传追踪号失败，信息为 " . $re['Head']['ErrorMessage'];
                    $this->orders_model->lazadaOrderOperating_log($type = 5, $op, $success = 2);
                }
            }
        }

    }


    public function XmlToArray($xml)
    {
        $array = (array)(simplexml_load_string($xml));
        foreach ($array as $key => $item) {

            $array[$key] = $this->struct_to_array((array)$item);
        }
        return $array;
    }

    public function struct_to_array($item)
    {
        if (!is_string($item)) {

            $item = (array)$item;
            foreach ($item as $key => $val) {

                $item[$key] = $this->struct_to_array($val);
            }
        }
        return $item;
    }


    public function checkAPICount($string,$string2){

        $mailArr=array('lilifeng@moonarstore.com','liufei@moonarstore.com','yangleen@moonarstore.com');

            $this->load->library('mail/phpmailer');


            $mail = new PHPMailer();

            $mail->IsSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.exmail.qq.com';                   // Specify main and backup server
            $mail->Port = 25;  //:465

            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'stockupdate@moonarstore.com';                            // SMTP username
            $mail->Password = 'salamoer1234';                           // SMTP password
            // 	$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted

            $mail->CharSet ="UTF-8";
            $mail->From = 'stockupdate@moonarstore.com';
            $mail->FromName = $string2;

            foreach($mailArr as $m){
                $mail->AddAddress($m);
            }


            $mail->IsHTML(true);                                  // Set email format to HTML
            $mail->Subject = $string2;
            $mail->Body = $string;

            //判断邮件是否发送成功
            $isSend = $mail->Send();
            echo $isSend."<br/>";

        }

    public function countOrderByAccount()
    {

        $result = $this->lazada_logistics_service_model->countOrderByAccount();
        if(!empty($result))
        {
            $string ='';
            foreach($result as $re)
            {
                $string = $string.'账号:'.$re['sales_account'].' 今日订单导入量 '.$re['num'].'  ';
            }

            $today = date('Y-m-d');
            $this->checkAPICount($string,$today.'订单导入情况');
        }
    }

    public function checkOrder()
    {
        $result = $this->lazada_logistics_service_model->checkOrder();
        if(empty($result))
        {
            $string ='';
            foreach($result as $re)
            {
                $string = $string.'  可能重复订单：'.$re['buyer_id'].'   ';
            }
            $this->checkAPICount($string,'订单可能重复');
        }

    }


}