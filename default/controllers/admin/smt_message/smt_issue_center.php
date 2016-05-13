<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-09
 * Time: 13:19
 */
header("content-type:text/html; charset=utf-8");

class Smt_issue_center extends Admin_Controller
{
    public $user_info;
    private $issue_status = array(
        'wait_seller_confirm_refund' => '买家已提起纠纷',
        'seller_refuse_refund' => '卖家拒绝纠',
        'seller_accept_issue' => '卖家接受纠纷',
        'wait_buyer_send_goods' => '等待买家发货',
        'wait_seller_receive_goods' => '买家发货，等待卖家收货',
        'buyer_initiate_arbitration' => '仲裁中',
        'seller_response_issue_timeout' => '卖家响应纠纷超时',
    );

    function __construct()
    {
    	
        parent::__construct();
	
        $this->load->library('MySmt');
        
	$this->load->model(array(
            'smt/Smt_user_tokens_model',
            'smt_message/smt_issue_list_model',
            'order/orders_model',
            'order/orders_products_model',
            'products/products_data_model',
            'moneyback_products_model',
            'moneyback_model',
            'shipment_model',
            'slme_user_model',
            'sharepage'
        ));

        $this->smt = new MySmt();

    }

    public function issue_center()
    {

        $search =array();

        $where = array(); //查询条件
        $in = array(); //in查询条件
        $like = array(); //like查询条件
        $string = array(); //URL参数
        $curpage = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search = $this->input->get_post('search');
        //  var_dump($search);exit;
        if (isset($search['token_id']) && $search['token_id']) {
            $where['token_id'] = trim($search['token_id']);
            $string[] = 'search[token_id]=' . trim($search['token_id']);
        }


        if (isset($search['messageType']) && $search['messageType']) {
            $where['messageType'] = trim($search['messageType']);
            $string[] = 'search[messageType]=' . trim($search['messageType']);
        }


        if (isset($search['otherName']) && $search['otherName']) {
            $where['otherName'] = urldecode(trim($search['otherName']));
            $string[] = 'search[otherName]=' . trim($search['otherName']);
        }

        if (isset($search['order_id']) && $search['order_id']) {
            $where['order_id'] = trim($search['order_id']);
            $string[] = 'search[order_id]=' . trim($search['order_id']);
        }

        if (isset($search['issue_reason_cn']) && $search['issue_reason_cn']) {
            if($search['issue_reason_cn']=='其他原因'){
                $where['issue_reason_cn !='] = '货物仍然在运输途中';
            }else{
                $where['issue_reason_cn'] = trim($search['issue_reason_cn']);
            }

            $string[] = 'search[issue_reason_cn]=' . trim($search['issue_reason_cn']);
        }

        /* if (isset($search['import_end']) && $search['import_end']) {
             $where['messageTime <'] = trim($search['import_end']);
             $string[]           = 'search[import_end]=' . trim($search['import_end']);
         }


         if (isset($search['start_export']) && $search['start_export']) {
             $where['exporttime >'] = trim($search['start_export']);
             $string[]           = 'search[start_export]=' . trim($search['start_export']);
         }

         if (isset($search['end_export']) && $search['end_export']) {
             $where['exporttime <'] = trim($search['end_export']);
             $string[]           = 'search[end_export]=' . trim($search['end_export']);
         }*/


        if (isset($search['issue_status']) && $search['issue_status']) {
            $where['issue_status'] = trim($search['issue_status']);
            $string[] = 'search[issue_status]=' . trim($search['issue_status']);
        }


        if (isset($search['num'])) {
            if ($search['num'] == 1) {
                $where['num >'] = trim($search['num']);
            } else {
                $where['num'] = trim($search['num']);
            }

            $string[] = 'search[num]=' . trim($search['num']);
        }else{
            $where['num'] = 0;
            $string[] = 'search[num]=0';
        }

        if (isset($search['orderby'])) {
            if ($search['orderby'] == 'desc') {
                $orderBy = 'issue_update_time DESC';;
            }elseif($search['orderby']=='descstart'){
                $orderBy = 'issue_creat_time ASC';;
            }
            else{
                $orderBy = 'issue_update_time ASC';
            }
            $string[] = 'search[orderby]=' . trim($search['orderby']);
        }else{
            $orderBy = 'issue_update_time ASC';
            $string[] = 'search[orderby]=asc';
        }

        if (isset($search['curpage']) && $search['curpage']) {
            $string[] = 'search[curpage]=' . trim($search['curpage']);
        }else{
            $string[] = 'search[curpage]=50';

        }

        /*if (isset($search['reply_no'])) {
            $where['reply_no'] = trim($search['reply_no']);
            $string[]           = 'search[reply_no]=' . trim($search['reply_no']);
        }*/
        $where['is_new'] = 1;
        // var_dump($where);exit;

        $search = $search ? $search : array();
          $curpage = (isset($search['curpage']) && $search['curpage'])?$search['curpage']:50;

        //查询条件
        $options = array(
            'select' => "id,order_id,issue_id,issue_status,issue_reason_cn,issue_creat_time,token_id,order_currency,num,snapshotUrl,order_price,issue_update_time,issueProcessDTOs_detail",
            'where' => $where,
            'where_in' => $in,
            'page' => $curpage,
            'per_page' => $per_page,
            'order' => $orderBy
        );
        if (!empty($like)) {
            $options = array_merge($options, array('like' => $like));
        }


        $return_data = array('total_rows' => true);
        $data_array = $this->smt_issue_list_model->getAll($options, $return_data);
        $data_array = $this->objectToArray($data_array);

        if (!empty($data_array)) {
            foreach ($data_array as $key => $data) {
                $order_option = array();
                $order_option['select'] = 'shipmentAutoMatched,orders_shipping_time';
                $order_option['where']['orders_is_join'] =0;
                $order_option['where']['buyer_id'] = $data['order_id'];

                $orders_data = $this->orders_model->getAll2Array($order_option);
                $data_array[$key]['orders_data'] = $orders_data;

            }
        }
        $data_array = $this->arrayToObject($data_array);

//smt_message/smt_message_center/message_center
        $c_url = admin_base_url('smt_message/smt_issue_center/issue_center');
        $url = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);


        $data = array(
            'data' => $data_array,
            'search' => $search,

            'page' => $page,
            'totals' => $return_data['total_rows'],
            'c_url' => $c_url,


        );

        $reason = $this->smt_issue_list_model->getReason();
        $reason_arr =array(
            '运单号无法查询到物流信息','货物仍然在运输途中','海关扣关','发错地址','物流退回了包裹','其他原因'
        );
        foreach($reason as $rea){
            $reason_arr[] = $rea['issue_reason_cn'];
        }
        $reason =$reason_arr;
        $reason =array_unique($reason);
        $shipment_option = array();
       // $shipment_option['where']['shipmentEnable'] = 1;
        $shipment_data = $this->shipment_model->getAll2Array($shipment_option);
        $shipment_data_last = array();
        foreach ($shipment_data as $shipment) {
            $shipment_data_last[$shipment['shipmentID']] = $shipment['shipmentTitle'];
        }
        unset($shipment_data);

        $token_option = array();
        $token_option['where']['token_status'] = 0;
        $token_arr = $this->Smt_user_tokens_model->getAll2Array($token_option);


        $smt_user_options = array(
            'select' => 'token_id, seller_account,accountSuffix',
            'where' => array('token_status' => 0),
        );
        //速卖通账号
        $token_array = $this->Smt_user_tokens_model->getSmtTokenList($smt_user_options);

        $data['token_account'] = $token_array;
        $data['token'] = $token_arr;
        $data['issue_status'] = $this->issue_status;
        $data['shipment'] = $shipment_data_last;
        $data['reason'] = $reason;
        $this->_template('admin/smt_message/smt_issue_center', $data);
    }


    public function issue_refuse()
    {
        $data = array();
        $this->template('admin/smt_message/smt_issue_refuse', $data);
    }



//string(16) "{"success":true}"
//string(56) "{"code":"06100001","msg":"参数错误","success":false}"

    public function  refuse_issue_smt()
    {

        if (isset($_POST['type']) && $_POST['type'] == 3) {
            $content = $_POST['content'];
            $idsArr = explode(',', $_POST['ids']);
            $string = '';
            $automsg = $_POST['automsg'];
            foreach ($idsArr as $id) {
                $list_option = array();
                $list_option['where']['id'] = $id;
                $list_one = $this->smt_issue_list_model->getOne($list_option, true);
                $token_arr = $this->Smt_user_tokens_model->getOneTokenInfo($list_one['token_id']);
                $this->smt->setToken($token_arr);
                $refuse_result = $this->sellerRefuseIssue($list_one['issue_id'], 3, 2, 0.00, $content);
                if (isset($refuse_result['success']) && $refuse_result['success']) {
                    $update_option = array();
                    $update_option['where']['id'] = $id;
                    $update_data = array();
                    $update_data['issue_status'] = 'seller_refuse_refund';
                    $this->smt_issue_list_model->update($update_data, $update_option);

                    $string .= $list_one['order_id'] . ' 拒绝成功 <br/>';

                    // 调用拒绝接口成功，并且需要发送订单留言
                    if ($automsg == 2) {
                        $leave_result = $this->leaveOrderMessage($list_one['order_id'], $content);
                        if (isset($leave_result['success']) && $leave_result['success']) {
                            $string .= $list_one['order_id'] . ' 留言成功 <br/>';
                        } else {
                            $message = isset($leave_result['code']) ? $leave_result['code'] : '';
                            $string .= $list_one['order_id'] . ' 留言失败:' . $message . ' <br/>';

                        }
                    }
                } else {
                    $message = isset($refuse_result['code']) ? $refuse_result['code'] : '';
                    $string .= $list_one['order_id'] . ' 拒绝失败:' . $message . ' <br/>';
                }

            }
            ajax_return($string, 2);

        } else {
            ajax_return('未知错误', 2);
        }


    }

    public function batch_leave_message(){
        $content = $_POST['content'];
        $idsArr = explode(',', $_POST['ids']);
        $string = '';
        foreach($idsArr as $id){
            $list_option = array();
            $list_option['where']['id'] = $id;
            $list_one = $this->smt_issue_list_model->getOne($list_option, true);
            $token_arr = $this->Smt_user_tokens_model->getOneTokenInfo($list_one['token_id']);
            $this->smt->setToken($token_arr);
            $leave_result = $this->leaveOrderMessage($list_one['order_id'], $content);
            if (isset($leave_result['success']) && $leave_result['success']) {
                $string .= $list_one['order_id'] . ' 留言成功 <br/>';
            } else {
                $message = isset($leave_result['code']) ? $leave_result['code'] : '';
                $string .= $list_one['order_id'] . ' 留言失败:' . $message . ' <br/>';
            }
        }
        ajax_return($string, 2);
    }

    public function agree_issue_smt(){
       // $_POST['ids'] =557;

        $reason_arr =array(
            '运单号无法查询到物流信息','货物仍然在运输途中','海关扣关','发错地址','物流退回了包裹','其他原因','限时达超时'
        );
        $idsArr = explode(',', $_POST['ids']);
        $is_batch = false;

        if(isset($_POST['action'])&&($_POST['action']=='batch')){
            $is_batch = true;
        }
        $batch_result = array();

        $retrun_result = array();
        foreach($idsArr as $id){
            $string = '';
            $is_success = 1;
            $list_one = $this->smt_issue_list_model->get_one_list($id);
            $detail  = unserialize($list_one['issueProcessDTOs_detail']);
            $isReturnGoods='';
            $refundAmount='';
            $refundCurrency='';
            $checkAmount='';
            foreach($detail as $de)
            {
                if($de['submitMemberType']=='buyer')
                {
                    $isReturnGoods =$de['isReceivedGoods'];
                    $refundAmount =$de['issueRefundSuggestionList'][0]['issueMoney']['cent'];
                    $refundCurrency = $de['issueRefundSuggestionList'][0]['issueMoney']['currencyCode'];

                    $checkAmount = $de['issueRefundSuggestionList'][0]['issueMoneyPost']['amount'];


                    break;
                }else{
                    continue;
                }
            }
            $issue_id = $list_one['issue_id'];
            $token_arr = $this->Smt_user_tokens_model->getOneTokenInfo($list_one['token_id']);
            $this->smt->setToken($token_arr);
            $result = $this->sellerAgreeIssue($issue_id,$isReturnGoods,$refundAmount,$refundCurrency);
        /*    $result = array(
                'success' =>true
            );*/
            if(isset($result['success'])&&$result['success']){ //成功
                $option_one =array();
                $option_one['where']['id'] = $id;
                //先修改这个纠纷的状态 seller_accept_issue
                $update_data =array();
                $update_data['issue_status'] = 'seller_accept_issue';
                $this->smt_issue_list_model->update($update_data,$option_one);

                $string .=$list_one['order_id'].'同意成功 ';

                $erp_reason = 1;
                if(!in_array($list_one['issue_reason_cn'],$reason_arr)){
                    $erp_reason = 2;
                }




                $money_result =   $this->createMoneyBack($list_one['order_id'],$erp_reason,$checkAmount);
             /*   $money_result=array(
                    'status' => 2,
                    'note' =>'登记成功',
                );*/
                if($money_result['status']==2){
                    $string .='  '.$money_result['note'].'</br>';
                }
                if($money_result['status']==1){
                    $string .='  登记退款失败:'.$money_result['note'].'</br>';
                }

                $content='Dear,
I had already confirm the dispute for you , as usual , you will get the refund within 7 working days from the aliexpress, we hope that we can solve any problem well not under bad effect from dispute, next time maybe we can solving the problem before open dispute, you can contact us to solve it well first .
Thank you for understanding us.
Best regards ';
                $this->leaveOrderMessage($list_one['order_id'],$content); // 同意纠纷 也发个留言
            }else{ // api失败
                $is_success =2;
                $string .=$list_one['order_id'].'同意失败<br/>';
            }
            if($is_batch){
                $batch_result[] = $string;
            }
        }

        if($is_batch){
            ajax_return('',1,$batch_result);
        }else{
            $retrun_result['is_success'] = $is_success;
            $retrun_result['string']  = $string;
            ajax_return('',1,$retrun_result);
        }

    }







    public function leaveOrderMessage($orderId, $content)
    {
        $method = 'api.leaveOrderMessage';
        $orderId = rawurlencode($orderId);
        $content = rawurlencode($content);
        $para = 'orderId=' . $orderId . '&content=' . $content;
         $listArrJson = $this->smt->getJsonData($method,$para);
        //$listArrJson = '{"code":"06100001","msg":"参数错误","success":false}';
        $listArr = json_decode($listArrJson, true);
        return $listArr;

    }

    /**
     * @param $issueId 纠纷id
     * @param $refund 退款方式
     * @param $isReturn 退货方式
     * @param $refundAmount 退款金额
     * @param $content 拒绝原因
     * @return mixed
     */

    public function sellerRefuseIssue($issueId, $refund, $isReturn, $refundAmount, $content)
    {
        $isReturnGoods_arr = array(
            1 => 'Y', //退货
            2 => 'N', //不退货
        );

        $refundType_arr = array(
            1 => 'full_amount_refund', //全额退款
            2 => 'part_amount_refund', //部分退款
            3 => 'no_amount_refund', //不退款
        );

        $method = 'api.sellerRefuseIssue';
        $para = 'issueId=' . rawurlencode($issueId) . '&refundType=' . rawurlencode($refundType_arr[$refund]) . '&isReturnGoods=' . rawurlencode($isReturnGoods_arr[$isReturn]) . '&refundAmount=' . number_format($refundAmount, 2) . '&content=' . rawurlencode($content);
        //return $para;
           $listArrJson = $this->smt->getJsonData($method,$para);
       // $listArrJson = '{"code":"06100001","msg":"参数错误","success":false}';

        $listArr = json_decode($listArrJson, true);
        return $listArr;
    }

    /**
     * @param $issueId  纠纷ID
     * @param $isReturnGoods 是否退货
     * @param $refundAmount 退款金额
     * @param $refundCurrency 退款币种
     */
    public function sellerAgreeIssue($issueId,$isReturnGoods,$refundAmount,$refundCurrency){
        $method = 'api.sellerAgreeIssue';
        $para = 'issueId='.rawurldecode($issueId).'&isReturnGoods='.rawurldecode($isReturnGoods).'&refundAmount='.($refundAmount).'&refundCurrency='.rawurldecode($refundCurrency);
        $listArrJson = $this->smt->getJsonData($method,$para);
        $listArr = json_decode($listArrJson, true);
        return $listArr;

    }


    public function createMoneyBack($buyer_id,$reason=1,$checkAmount='')
    {
       // $buyer_id =69358596681302;
        $return_array =array();
        $order_optin = array();
        $order_optin['where']['buyer_id'] = $buyer_id;
        $order_optin['where']['orders_is_join'] = 0;
      //  $order_optin['select'] = 'erp_orders_id, buyer_id, orders_is_split, orders_status, orders_is_join';

        $order_data = $this->orders_model->getAll2Array($order_optin);
        if(!empty($order_data)){
            foreach($order_data as $order){
                if($order['orders_is_split'] != 0){
                    $return_array['status'] = 1;
                    $return_array['note'] = '订单拆分或者合并过';

                    return $return_array;
                }elseif($order['orders_status'] !=5){
                    $return_array['status'] = 1;
                    $return_array['note'] = '订单不是已发货状态';
                    return $return_array;
                }
            }
            $orderInfo = end($order_data);
            $orderId = $orderInfo['erp_orders_id']; // 内单号

            $moneyback_option = array();
            $moneyback_option['where']['erp_orders_id'] = $orderId;
            $moneyback_data = $this->moneyback_model->getOne($moneyback_option,true);
            if(empty($moneyback_data)){
                if ($orderInfo['orders_is_resend'] == '0' || ($orderInfo['orders_is_resend'] != 1 && $orderInfo['orders_status'] == 6)){

                    $moneybackInfo = array();
                    $moneybackInfo['moneyback_buyer'] = $orderInfo['buyer_id'];	//买家ID
                    $moneybackInfo['checkAmount'] = !empty($checkAmount)?$checkAmount:$orderInfo['orders_total'];	//全部退款金额
                    $moneybackInfo['moneyback_amount'] = $orderInfo['orders_total'];	//手填退款金额
                    $moneybackInfo['moneyback_currency'] = $orderInfo['currency_type'];	//货币类型
                    $moneybackInfo['moneyback_currency_value'] = $orderInfo['currency_value'];
                    //交易ID
                    $moneybackInfo['moneyback_transactionID'] = (trim($orderInfo['transactionIDNew']) == '') ? trim($orderInfo['pay_id']).' Paid Time: '.$orderInfo['orders_paid_time'] : '';
                    $moneybackInfo['transactionIDNew'] = $orderInfo['transactionIDNew'];
                    $moneybackInfo['moneyback_paypal_id'] = '';
                    $moneybackInfo['moneyback_type'] = 'Full';	//退款类型 全部退款
                    $moneybackInfo['moneyback_memo'] = '';	//Memo 发给客户看的 只能填写英文
                    $moneybackInfo['refundMethod'] = 'platForm';	//退款方式 销售平台
                    $moneybackInfo['paypalAccount'] = '';	//客户帐号
                    $moneybackInfo['transactionID'] = '';
                    $moneybackInfo['ordersType'] = $orderInfo['orders_type'];
                    if($reason==1){
                        $moneybackInfo['moneyback_reason'] = '【中国发】物流问题';	//退款原因
                        $moneybackInfo['moneyback_description'] = '物流在途没收到';	//详细原因
                    }else{
                        $moneybackInfo['moneyback_reason'] = '质量问题(尺码色差不能用不满意)';	//退款原因
                        $moneybackInfo['moneyback_description'] = '产品质量存在缺陷，纠纷退款';	//详细原因
                    }

                    $moneybackInfo['moneyback_img'] = '';	//上传图片
                    $moneybackInfo['erp_orders_id'] = $orderInfo['erp_orders_id'];	//单号

                    $moneybackInfo['moneyback_status']  = 'newData';
                    $moneybackInfo['moneyback_submitTime'] = date('Y-m-d H:i:s',time());
                    $moneybackInfo['user_id'] =  $this->user_info->old_id;
                    $user_id= $this->user_info->old_id;
                    $refundAmount              = ( $moneybackInfo['moneyback_type'] == 'Full' ) ? $moneybackInfo['checkAmount'] : $moneybackInfo['moneyback_amount'];
                    $moneybackInfo['moneyback_amount'] = $refundAmount;

                    $productsArray = $this ->smt_issue_list_model->getMoneyBackAmountArray($orderId);	//单号里面的SKU信息
                    if(!empty($productsArray)) {
                        $moneyback_id = $this->moneyback_model->add($moneybackInfo);
                        foreach ($productsArray as $pro) {
                            $erp_moneyback_products = array();
                            $erp_moneyback_products['moneyBackProductsSKU'] = $pro['sku'];
                            $erp_moneyback_products['moneyBackProductsAmount'] = $pro['amount'];
                            $erp_moneyback_products['moneyBackProductsQuantity'] = $pro['quantity'];
                            $erp_moneyback_products['moneyBackID'] = $moneyback_id;

                            $this->moneyback_products_model->add($erp_moneyback_products);
                        }

                        if ($moneybackInfo['moneyback_type'] == 'Full'){
                            $update_order =array();
                            $update_option =array();
                            $update_option_new['where']['erp_orders_id'] = $orderId;
                            $update_option_new['where']['orders_status <'] = 5;
                            $update_order['orders_status'] = 6;
                            $update_order['orders_is_backorder'] = 0;
                            $update_order['orders_remark'] = '已退款撤单';
                            $this->moneyback_model->update($update_order,$update_option_new);
                            unset($update_option);
                        }
                        mysql_query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText)
VALUES($user_id,'update','ordersManage','".$orderId."','申请退款')");
                        $return_array['status'] = 2;
                        $return_array['note'] = '退款登记成功';
                        return $return_array;
                    }else{
                        $return_array['status'] = 1;
                        $return_array['note'] = '此单里面没有SKU';
                        return $return_array;
                    }


                }else{
                    $return_array['status'] = 1;
                    $return_array['note'] = '此单是重发且状态不为已撤单';
                    return $return_array;
                }


            }else{
                $return_array['status'] = 1;
                $return_array['note'] = '此单已存在退款记录';
                return $return_array;
            }

        }else{
            $return_array['status'] = 1;
            $return_array['note'] = '未找到该订单';
            return $return_array;
        }


    }


    public function setIssueNotNew()
    {
        $ids = $_POST['ids'];
        $ids_arr = explode(',', $ids);
        $update_optin = array();
        $update_optin['where_in']['id'] = $ids_arr;
        $update_data = array();
        $update_data['is_new'] = 0;
        $this->smt_issue_list_model->update($update_data, $update_optin);
        ajax_return('设置完成', 2);
    }

    function arrayToObject($e)
    {
        if (gettype($e) != 'array') return;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object')
                $e[$k] = (object)$this->arrayToObject($v);
        }
        return (object)$e;
    }

    function objectToArray($e)
    {
        $e = (array)$e;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'resource') return;
            if (gettype($v) == 'object' || gettype($v) == 'array')
                $e[$k] = (array)$this->objectToArray($v);
        }
        return $e;
    }
}