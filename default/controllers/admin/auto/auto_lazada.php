<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/02
 * Time: 13:18
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Auto_lazada extends MY_Controller
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

    //递归方法获取订单。暂时不用改方法
    public function get_gtr_100($token, $i = 0, $data = array())
    {
        $Key = trim($token['Key']);
        $UserId = trim($token['lazada_user_id']);
        $limit = 100;
        $offset = $i * $limit;

        $Xml_data = $this->lazada->getLazadaOrder($Key, $UserId, $limit, $offset);

        $order_data = $this->XmlToArray($Xml_data);

        if (empty($order_data) || !is_array($order_data)) {
            return FALSE;
        }
        $orders = (!empty($order_data['Body']['Orders']['Order'])) ? $order_data['Body']['Orders']['Order'] : FALSE;

        if (!empty($orders)) {
            if (!empty($orders[0])) {
                foreach ($orders as $o_v) {
                    $data[] = $o_v;
                }
            } else {
                $data[] = $orders;
            }
            $i++;
            $data = $this->get_gtr_100($token, $i, $data);
        }
        return $data;
    }

    //自动抓取lazada平台订单
    public function index($token_id='')
    {
       // echo 123;
        $account_option = array();
        if($token_id !=''){
            $account_option['token_id'] = $token_id;
        }
        $token_info = $this->lazada_token_model->getAll2Array($account_option);

        $current_value = $this->system_model->get_current_value();

        $system_value = $current_value->system_value;

     
        $currency_arr =preg_split("/[\r\n]+/",$system_value);
       // $currency_arr = explode(chr(13), $system_value);

        $currency_money_arr = array();    //国家为K,汇率为V
        foreach ($currency_arr as $c_v) {
            $currency_money = explode('<-->', trim($c_v));
            $currency_money_arr[$currency_money[0]] = $currency_money[1];
        }

        foreach ($token_info as $token) {
           /* if($token['sales_accountl']!='lixuanpengwu@126.com_ID')
            {
                continue;
            }*/
            if (empty($currency_money_arr[$token['currency_type_cn']])) {
                echo '没有货币汇率';
                continue;
            }
            $ratio_arr = $currency_arr = explode(':', $currency_money_arr[$token['currency_type_cn']]);

            $cur_value = trim($ratio_arr[1]);    //得到此帐号与美金的汇率

            $Key = trim($token['Key']);
            $UserId = trim($token['lazada_user_id']);
            $api_host = trim($token['api_host']);
            for ($i = 1; $i > 0; $i++) {// 死循环获取全部订单
                $limit = 100;
                $offset = ($i - 1) * $limit;
                sleep(1);// 在执行抓单前休息20S 减少出现请求太频繁的问题
                $Xml_data = $this->lazada->getLazadaOrder($api_host, $Key, $UserId, $limit, $offset);
              // var_dump($Xml_data);

                $orders_data = $this->XmlToArray($Xml_data);

                if(isset($orders_data['Head']['ErrorMessage']))
                {
                    if($orders_data['Head']['ErrorMessage']=='Too many API requests')
                    {
                        sleep(1); //先休息40S
                        $i--;  //重新执行这次
                        continue;
                    }
                    else
                    {
                        //不是请求频繁，保存下错误原因
                        $op = "获取订单失败，信息为 " . $orders_data['Head']['ErrorMessage'];
                        $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 2);
                    }
                }

                if (!isset($orders_data['Body']['Orders']['Order'])) { // 没有订单的时候 跳出死循环
                    break;
                }
                if(isset($orders_data['Body']['Orders']['Order'][0])){
                    $orders = $orders_data['Body']['Orders']['Order'];//获取订单信息数组
                    foreach ($orders as $key => $order_info) {
                        if ($order_info['OrderId'] == 0) //OrderId为0 的订单会导致获取产品的时候出问题
                        {
                            continue;
                        }
                        $this->insert_orders($api_host, $order_info, $token, $cur_value);

                    }
                }else{
                    $order_info = $orders_data['Body']['Orders']['Order'];//获取订单信息数组
                    $this->insert_orders($api_host, $order_info, $token, $cur_value);

                }

            }
        }

    }

    //获取订单产品信息
    public function insert_orders($api_host, $order_info, $token, $cur_value)

    {

        $erp_order_data = $this->orders_model->getIdByEid($order_info['OrderNumber'],$token['sales_account']);//根据订单内单号查找外单号
        if (!empty($erp_order_data)) { //判断订单是否重复
            return FALSE;
        }
        $Key = trim($token['Key']);
        $UserId = trim($token['lazada_user_id']);

        $it = $this->lazada->get_OrderItem($api_host, $Key, $UserId, $order_info['OrderId']);

        $itemarr = $this->XmlToArray($it);
        if (isset($itemarr['Head']['ErrorCode'])) {
            if($itemarr['Head']['ErrorMessage']=='Too many API requests')
            {
                sleep(2);//等30S在执行吧
              $this->insert_orders($api_host, $order_info, $token, $cur_value);
                return false;
            }
            else
            {
                //不是请求频繁，保存下错误原因
                $op = $order_info['OrderId']."获取订单产品失败，信息为 " . $itemarr['Head']['ErrorMessage'];
                $this->orders_model->lazadaOrderOperating_log($type = 2, $op, $success = 2);
                return false;
            }
        }

        $items = $itemarr['Body']['OrderItems']['OrderItem'];

        if (empty($items)) {
            return FALSE;
        }

        if (isset($items['OrderItemId'])) {
            $temp = $items;
            $items = array();
            $items[0] = $temp;
        }

        $this->auto_download_order($order_info, $items, $cur_value, $token);
        sleep(1); //每获取一次产品休息10S
    }

    //获取产品信息
    public function get_product($item_info)
    {
        //整理数据
        $sku_data = array();

        $total = 0;//订单总价格

        $orders_ship_fee = 0;

        foreach ($item_info as $item) {

            $sku_info = array(
                0 => array(
                    'sku' => $item['Sku'],
                    'count' => 1,
                    'price' => $item['ItemPrice']
                )
            );

            $data = resetTransactionDetail($sku_info);

            foreach ($data as $v) {

                $total += $item['ItemPrice'];

                $orders_ship_fee += $item['ShippingAmount'];

                if (isset($sku_data[$v['sku']])) {
                    $sku_data[$v['sku']]['item_count'] += 1;
                    $sku_data[$v['sku']]['comment_text'] = $item['OrderItemId'] . '@' . $v['sku'] . ',' . $sku_data[$v['sku']]['comment_text'];
                } else {
                    $sku_data[$v['sku']]['OrderItemId'] = $item['OrderItemId'];
                    $sku_data[$v['sku']]['comment_text'] = $item['OrderItemId'] . '@' . $v['sku'];;
                    $sku_data[$v['sku']]['item_count'] = $v['count'];
                    $sku_data[$v['sku']]['item_price'] = $v['price'];
                    $sku_data[$v['sku']]['orders_sku'] = $v['sku'];
                }
            }
        }
        $sku_data['total'] = $total;
//     	$sku_data['orders_ship_fee'] = $orders_ship_fee;

        $sku_data['orders_ship_fee'] = 0;    //lazada不需要运费

        return $sku_data;
    }

    //数据插入到数据库
    public function auto_download_order($order_info, $item_info, $cur_value, $token)
    {
        print_r($order_info);

        //整理数据
        $sku_data = $this->get_product($item_info);

        $total = $sku_data['total'];//订单总价格
        unset($sku_data['total']);

        $orders_ship_fee = $sku_data['orders_ship_fee'];
        unset($sku_data['orders_ship_fee']);

        print_r($sku_data);

        $data = array();

        $data['ebay_orders_id'] = $order_info['OrderId'];

        $data['buyer_id'] = $order_info['OrderNumber'];

        $data['buyer_name'] = $order_info['AddressShipping']['FirstName'];

        if (!empty($order_info['AddressShipping']['LastName'])) {
            $data['buyer_name'] .= ' ' . $order_info['AddressShipping']['LastName'];
        }

        $data['pay_method'] = $order_info['PaymentMethod'];

        $data['currency_value'] = 1 / $cur_value; //变幻成美元对马拉西亚币的汇率

        if (!empty($order_info['Remarks'])) {
            $data['orders_remark'] = $order_info['Remarks'];
        }

        $data['orders_paid_time'] = $order_info['CreatedAt'];//设为付款时间

        $data['ShippingServiceSelected'] = "dropshipping";

        $data['orders_export_time'] = date("Y-m-d H:i:s");//订单导入数据库时间

        $data['buyer_phone'] = (!empty($order_info['AddressShipping']['Phone'])) ? $order_info['AddressShipping']['Phone'] : $order_info['AddressShipping']['Phone2'];

        $data['buyer_address_1'] = (!empty($order_info['AddressShipping']['Address1']))?$order_info['AddressShipping']['Address1']:'';


        if (!empty($order_info['AddressShipping']['Address2'])) {
            $data['buyer_address_1'] .= ' ' . $order_info['AddressShipping']['Address2'];
        }

        //多地址
        $address = array();
        if (!empty($order_info['AddressShipping']['Address3'])) {
            $address[] = $order_info['AddressShipping']['Address3'];
        }

        if (!empty($order_info['AddressShipping']['Address4'])) {
            $address[] = $order_info['AddressShipping']['Address4'];
        }

        if (!empty($order_info['AddressShipping']['Address5'])) {
            $address[] = $order_info['AddressShipping']['Address5'];
        }

        if (!empty($address)) {
            $data['buyer_address_2'] = join(' ', $address);
        }

        $data['buyer_city'] = $order_info['AddressShipping']['City'];

        if (!empty($order_info['AddressShipping']['Region'])) {
            $data['buyer_state'] = $order_info['AddressShipping']['Region'];
        }

        $data['buyer_zip'] = empty($order_info['AddressShipping']['PostCode']) ? " " : $order_info['AddressShipping']['PostCode'];

        $data['buyer_country'] = $order_info['AddressShipping']['Country'];

        $data['buyer_country_code'] = $token['site'];

        $data['orders_type'] = 15;

        $data['orders_total'] = $total;

        $data['orders_ship_fee'] = $orders_ship_fee;

        $data['currency_type'] = $token['currency_type'];

        $data['orders_status'] = 1;

        $data['sales_account'] = $token['sales_account'];

        //事务开启
        $this->db->trans_begin();
        //这里在进行一次订单是否重复判断
        $erp_order_data = $this->orders_model->getIdByEid($order_info['OrderNumber'],$token['sales_account']);//根据订单buy_id+销售账号查找外单号
        if (!empty($erp_order_data)) { //判断订单是否重复
            $this->db->trans_rollback();
            return FALSE;
        }

        print_r($data);
        $erp_orders_id = $this->orders_model->add($data);

        if (empty($erp_orders_id)) {
            $this->db->trans_rollback();
            return '下载订单失败，原因插入订单表失败';
        }

        $sku_tof = true;

        foreach ($sku_data as $k => $v) {

            $data = array();

            $data['erp_orders_id'] = $erp_orders_id;

            $data['ebay_orders_id'] = $erp_orders_id;

            $data['orderlineitemid'] = $v['OrderItemId'];

            $data['orders_sku'] = $v['orders_sku'];

            $data['comment_text'] = $v['comment_text'];

            $data['orders_item_number'] = $v['comment_text'];

            if (empty($data['orders_sku'])) {
                print_r($sku_data);
                $this->db->trans_rollback();
                print_r($item);
                die;
            }

            $data['item_price'] = $v['item_price'];

            $data['item_count'] = $v['item_count'];
            print_r($data);
            $tof = $this->orders_products_model->add($data);

            if (empty($tof)) {
                $this->db->trans_rollback();
                $sku_tof = false;
                break;
            }
        }

        if (!$sku_tof) {
            return '插入订单详情表失败';
        }

        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();//事务结束
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

                $item[$key] = $this->struct_to_array($val);//wudequan:此处一定要注意XBug的最大嵌套数，可以修改配置文件加大最大嵌套数
            }
        }
        return $item;
    }


    public function analysis_sku($sku, $price)
    {

        $result = array();

        $result[0]['sku'] = '';

        $result[0]['price'] = '';

        $tmp = explode('*', $sku);

        $tmpSku = trim(array_pop($tmp));

        //忽略中括号内的信息
        if (stripos($tmpSku, '[') !== false) {
            $tmpSku = preg_replace('/\[.*\]/', '', $tmpSku);
        }

        $result[0]['sku'] = $tmpSku;

        $result[0]['price'] = $price;

        return $result;
    }

    //自动上传追踪号
    public function autoUpLoadShippingCode()
    {

        exit;
        $token_info = $this->lazada_token_model->getAll2Array();//获取账号信息
        foreach ($token_info as $toke) //循环账号
        {
            /*if(($toke['sales_account'] =='99706454@qq.com_ID')||($toke['api_host']=='https://sellercenter-api.lazada.com.my'))
            {
                continue;
            }*/
            if(($toke['api_host']!='https://sellercenter-api.lazada.sg'))
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
            $orders_option['where']['orders_export_time <='] = date('Y-m-d H:i:s',strtotime('-2 day'));
            $orders_option['where']['sales_account'] = $toke['sales_account'];
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
                        if(  time()-(strtotime($order['orders_export_time'])+48*60*60)<50*60 )
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



            //处理欠货超过3天的
            $orders_option = array();
            $orders_option['where']['orders_type'] = 15;
            $orders_option['where']['orders_status <='] = 5;
            $orders_option['where']['orders_status >='] = 3;
            $orders_option['where']['ebayStatusIsMarked'] = 0;
            $orders_option['where']['orders_is_join'] = 0;
            $orders_option['where']['orders_is_backorder'] =1;
            $orders_option['where']['orders_export_time >='] = '2015-09-19 00:00:00';
            $orders_option['where']['orders_export_time <='] = date('Y-m-d H:i:s',strtotime('-3 day'));
            $orders_option['where']['sales_account'] = $toke['sales_account'];

            $orders_data_three_day = $this->orders_model->getAll2Array($orders_option);


            $result = $this->orders_model->getLazadaTacking($toke['sales_account']);// 根据账号找出订单

            $result = array_merge($result,$orders_single_sku);
            $result = array_merge($result,$orders_data_three_day);

            foreach ($result as $v) {

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

                sleep(5);// 上传追踪号先等15S 在请求

                //处理一下打包的sku 例如 A+B    在系统内为2个sku 信息，即会出现2个相同的Item-Id
                $OrderItemIds = explode(',',$OrderItemIds);
                $OrderItemIds = array_unique($OrderItemIds);
                $OrderItemIds = implode(",",$OrderItemIds);
                $relut = $this->lazada->upLoadShippingCode($toke['api_host'], $toke['Key'], $toke['lazada_user_id'], trim($v['orders_shipping_code']), $OrderItemIds, $ShippingProvider['logistics_name']);
                $re = $this->XmlToArray($relut);
                if (isset($re['Body']['OrderItems']['OrderItem'])) {
                    $upResult = $this->orders_model->upDataOrderByShip($v['erp_orders_id']); //更新订单状态
                    $op = $v['erp_orders_id'] . "上传追踪号成功，并更新对应的订单信息";
                    $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 1);
                }
                else
                {
                    if(isset($re['Head']['ErrorMessage']))
                    {
                      /*  if(isset($re['Head']['ErrorCode'])&&($re['Head']['ErrorCode']==63||$re['Head']['ErrorCode']=73) )
                        {
                            /*$upResult = $this->orders_model->upDataOrderByShip($v['erp_orders_id']); //更新订单状态
                            $op = $v['erp_orders_id'] . "已经上传追踪号了，更新订单状态";
                            $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 1);
                        }
                        else*/
                        {
                            $op = $v['erp_orders_id'] . "上传追踪号失败，信息为 " . $re['Head']['ErrorMessage'];
                            $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 2);
                        }

                    }
                    else
                    {
                        $op = $v['erp_orders_id'] . "上传追踪号失败，信息为 " . $re['Head']['ErrorMessage'];
                        $this->orders_model->lazadaOrderOperating_log($type = 1, $op, $success = 2);
                    }
                }
            }
        }
    }

    //LAZADA 自动撤单
    public  function  auto_cancel_orders()
    {
        //exit;
        $option = array();
        $token_info = $this->lazada_token_model->getAll2Array($option);
        foreach($token_info as $token){

            $Key = trim($token['Key']);
            $UserId = trim($token['lazada_user_id']);
            $api_host = trim($token['api_host']);
            for ($i = 1; $i > 0; $i++) {// 死循环获取全部订单
                $limit = 100;
                $offset = ($i - 1) * $limit;
                sleep(1);// 在执行抓单前休息20S 减少出现请求太频繁的问题
                $Xml_data = $this->lazada->getLazadaOrderBystatus($api_host, $Key, $UserId, 'canceled',$limit, $offset);


                $orders_data = $this->XmlToArray($Xml_data);
               // var_dump($orders_data);exit;

                if (!isset($orders_data['Body']['Orders']['Order'])) { // 没有订单的时候 跳出死循环
                    break;
                }


                $orders_arr = $orders_data['Body']['Orders']['Order'];
                foreach($orders_arr as $order)
                {

                    $OrderIdList =$order['OrderId'];
                    $order_xml = $this->lazada->getshippingcode($token['api_host'], $token['Key'], $token['lazada_user_id'], $OrderIdList);
                    $order_data = $this->XmlToArray($order_xml);
                    if(!isset($order_data['Body']['Orders']['Order']['OrderItems'])){
                        continue;
                    }else{
                        $type = true;
                        if(isset($order_data['Body']['Orders']['Order']['OrderItems']['OrderItem'][0])){  //多个sku
                          foreach($order_data['Body']['Orders']['Order']['OrderItems']['OrderItem'] as $cancel){
                              if($cancel['Status'] !='canceled'){
                                  $type = false;
                              }
                          }
                        }
                    }
                    if($type){
                        $option = array();
                        $option['where']['buyer_id'] = $order['OrderNumber'];
                        $option['where']['orders_is_join'] =0;

                        $result_arr = $this->orders_model->getAll2Array($option);

                        foreach($result_arr as $v)
                        {
                            if($v['orders_status']==6||$v['orders_status']==5){  //订单如果是撤单 或者已经发货的就不管了
                                continue;
                            }else{ // 不是 以上状态 进行撤单，增加操作日志
                                $updata_data =array();
                                $updata_data['orders_status'] = 6;

                                $updata_option = array();
                                $updata_option['where']['erp_orders_id'] = $v['erp_orders_id'];

                                $uodata_result = $this->orders_model->update($updata_data,$updata_option);
                                if($uodata_result>0){
                                    mysql_query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText)
VALUES('30','update','ordersManage','".$v['erp_orders_id']."','平台状态为CANCEL,系统自动撤单')");
                                }
                            }
                        }
                        echo $order['OrderNumber'].'完全撤单</br>';
                    }else{
                        echo $order['OrderNumber'].'不完全撤单</br>';
                    }
                  //  var_dump($order_data);exit;
                }
            }
        }
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


    //手动方法 请无视
    public  function mualUpLoadShippingCode()
    {

        $token_info = $this->lazada_token_model->getAll2Array();//获取账号信息
        foreach ($token_info as $toke) //循环账号
        {
            if($toke['sales_account'] !='99706454@qq.com_ID')
            {
                continue;
            }

            $v['orders_shipping_code']= '0113101511560260';
            $ShippingProvider['logistics_name']='AS-JNE';
           // $OrderItemIds='3850779,3850786,3850791,3850799,3850802,3850739,3850740,3850751,3850785,3850819,3850744,3850745,3850746,3850814,3850818,3850737,3850742,3850752,3850771,3850792,3850774,3850784,3850789,3850804,3850811,3850736,3850741,3850787,3850810,3850815,3850747,3850780,3850788,3850793,3850816,3850748,3850749,3850757,3850763,3850805,3850738,3850759,3850770,3850773,3850803';
            $OrderItemIds ='6448291,6448294,6448299,6448301';
            $relut = $this->lazada->upLoadShippingCode($toke['api_host'], $toke['Key'], $toke['lazada_user_id'], $v['orders_shipping_code'], $OrderItemIds, $ShippingProvider['logistics_name']);
            print_r($relut);exit;

        }
    }


    public  function getinfo()
    {

        $token_info = $this->lazada_token_model->getAll2Array();//获取账号信息
        foreach ($token_info as $toke) //循环账号
        {
            if($toke['sales_account'] !='99706454@qq.com_TH')
            {
                continue;
            }

            $v['orders_shipping_code']= '';
            $ShippingProvider['logistics_name']='LGS-TH1';
            // $OrderItemIds='3850779,3850786,3850791,3850799,3850802,3850739,3850740,3850751,3850785,3850819,3850744,3850745,3850746,3850814,3850818,3850737,3850742,3850752,3850771,3850792,3850774,3850784,3850789,3850804,3850811,3850736,3850741,3850787,3850810,3850815,3850747,3850780,3850788,3850793,3850816,3850748,3850749,3850757,3850763,3850805,3850738,3850759,3850770,3850773,3850803';
            $OrderItemIds ='11456348';
            $relut = $this->lazada->upLoadShippingCode($toke['api_host'], $toke['Key'], $toke['lazada_user_id'], $v['orders_shipping_code'], $OrderItemIds, $ShippingProvider['logistics_name']);
            print_r($relut);exit;

        }
    }

    public  function getinfo2()
    {

        $token_info = $this->lazada_token_model->getAll2Array();//获取账号信息
        foreach ($token_info as $token) //循环账号
        {
            if($token['sales_account'] !='99706454@qq.com_PH')
            {
                continue;
            }


            // $OrderItemIds='3850779,3850786,3850791,3850799,3850802,3850739,3850740,3850751,3850785,3850819,3850744,3850745,3850746,3850814,3850818,3850737,3850742,3850752,3850771,3850792,3850774,3850784,3850789,3850804,3850811,3850736,3850741,3850787,3850810,3850815,3850747,3850780,3850788,3850793,3850816,3850748,3850749,3850757,3850763,3850805,3850738,3850759,3850770,3850773,3850803';
            $OrderIdList ='4474217';
            $Xml_data = $this->lazada->getshippingcode($token['api_host'], $token['Key'], $token['lazada_user_id'], $OrderIdList);
            print_r($Xml_data);exit;

        }
    }


}