<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-19
 * Time: 14:06
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class Auto_smt_exportIssue extends MY_Controller
{

    private $issue = array(
        'WAIT_SELLER_CONFIRM_REFUND',  //买家提起纠纷
        'SELLER_REFUSE_REFUND', //卖家拒绝纠
        // 'ACCEPTISSUE', //卖家接受纠纷     相当于完成了的纠纷
        // 'WAIT_BUYER_SEND_GOODS', //等待买家发货
        //  'WAIT_SELLER_RECEIVE_GOODS', // 买家发货，等待卖家收货
           'ARBITRATING', // 仲裁中
        //   'SELLER_RESPONSE_ISSUE_TIMEOUT' // 卖家响应纠纷超时  对应相关超时的不需要获取
    );
    private $reason_type_buyer = array(
        //1 非卖家原因
       1=>array( '买家原因',
                    '七天无理由退货',
                    '未按约定的物流方式发货'
       ),
        //2 物流原因
        2=>array(
            '运单号无法查询',
            '限时达超时',
            '物流退回了包裹',
            '运输途中',
            '发错地址',
            '海关扣关'
        )
        // 3 货不对版
    );


    public function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
            'smt_message/smt_issue_list_model',
        ));
        $this->smt = new MySmt();


    }


    public function autoExportIssue()
    {
        $is_ajax=false;
        $option = array();
        $option['where']['token_id'] = 55;
        if (isset($_POST['type']) && $_POST['type'] == 'export') {
            $option['where']['token_id'] = $_POST['token_id'];
            $is_ajax = true;
        }
        $token_arr = $this->Smt_user_tokens_model->getAll2Array($option);
        foreach ($token_arr as $token) {
            foreach ($this->issue as $sue) {
                $set_is_new = array();
                $set_is_new['where']['token_id'] = $token['token_id'];
                $set_is_new['where']['issue_status'] = strtolower($sue);
                   $is_new_data =array();
                   $is_new_data['is_new'] =0;
                   $this->smt_issue_list_model->update($is_new_data,$set_is_new);

                $this->smt->setToken($token);
                $pageSize = 10;

                $limit = 3;
                $i = 0;
                for ($page = 1; $page < $limit; $page++) {
                    $i++;
                    $method = 'api.queryIssueList';
                    $para = "currentPage=$page&pageSize=$pageSize&issueStatus=".$sue;
                    $listArrJson = $this->smt->getJsonData($method, $para);
                    $listArr = json_decode($listArrJson, true);
                    //  var_dump($listArr);
                    if (isset($listArr['success']) && $listArr['success']) {
                        if (!empty($listArr['dataList'])) {
                            if ($i == 1) {
                                $limit = ceil($listArr['totalItem'] / $pageSize);
                            }
                            $method = "api.queryIssueDetail";
                            foreach ($listArr['dataList'] as $list) {
                                $para = "issueId=" . $list['id'];
                                $detailJson = $this->smt->getJsonData($method, $para);
                                $detail = json_decode($detailJson, true);
                                //   var_dump($detail);exit;

                                if (isset($detail['success']) && $detail['success'] && !empty($detail['data'])) {


                                    $list_info = array();
                                    $list_info['order_id'] = $detail['data']['issueAPIIssueDTO']['parentOrderId'];
                                    $list_info['issue_id'] = $list['id'];
                                    $list_info['order_id_child'] = $list['orderId'];
                                    $list_info['issue_status'] = $list['issueStatus'];
                                    $list_info['issue_reason_cn'] = $list['reasonChinese'];
                                    $list_info['issue_reason_type'] = 3;
                                    foreach($this->reason_type_buyer as $key =>$reason){
                                      foreach($reason as $re){
                                          if(strpos($list_info['issue_reason_cn'],$re) !== false) {
                                              $list_info['issue_reason_type'] = $key;
                                              break;
                                          }
                                      }
                                    }


                                    $is_many = false;
                                    if($list['issueStatus']=='seller_refuse_refund'){


                                        foreach($detail['data']['issueAPIIssueDTO']['issueProcessDTOs'] as $key=> $de){
                                            if($de['actionType']=='cancel'){
                                                    $is_many=true;
                                                    $str = mb_substr($detail['data']['issueAPIIssueDTO']['issueProcessDTOs'][$key-1]['gmtModified'], 0, 14);
                                                    break;
                                            }
                                        }
                                    }
                                    if($is_many){
                                        $list_info['issue_creat_time'] = date("Y-m-d H:i:s",$str);
                                    }else{
                                        $list_info['issue_creat_time'] = $this->getTime($list['gmtCreate']);
                                        }
                                    $list_info['issue_update_time'] = $this->getTime($list['gmtModified']);
                                    $list_info['issueProcessDTOs'] = serialize($list['issueProcessDTOs']);
                                    $list_info['update_time'] = date('Y-m-d H:i:s');
                                    $list_info['token_id'] = $token['token_id'];
                                    $list_info['order_price'] = $detail['data']['issueAPIIssueDTO']['limitRefundAmount']['amount'];
                                    $list_info['order_currency'] = $detail['data']['issueAPIIssueDTO']['limitRefundAmount']['currencyCode'];
                                    $list_info['snapshotUrl'] = $detail['data']['snapshotUrl'];
                                    $list_info['is_new'] = 1;

                                    $list_info['issueProcessDTOs_detail'] = serialize($detail['data']['issueAPIIssueDTO']['issueProcessDTOs']);
                                    $list_info['limitRefundAmount'] = json_encode($detail['data']['issueAPIIssueDTO']['limitRefundAmount']);

                                    $check_list = $this->smt_issue_list_model->getOneList($list['orderId']);
                                    if (empty($check_list)) {
                                        $this->smt_issue_list_model->add($list_info);
                                        if (!isset($_POST['type'])) {
                                            echo $list_info['order_id'] . '新增纠纷订单<br/>';
                                        }
                                    } else {
                                        $upadte_list = array();
                                        $upadte_list['where']['order_id_child'] = (string)$list['orderId'];
                                        if (!isset($_POST['type'])) {
                                            echo $list_info['order_id'] . '更新纠纷订单1<br/>';
                                        }

                                        $this->smt_issue_list_model->update($list_info, $upadte_list);
                                    }
                                    //  exit;

                                    /*    $detail_info =array();
                                        $detail_info['snapshotUrl'] =$detail['data']['snapshotUrl'];
                                        $detail_info['orderId'] =$detail['data']['issueAPIIssueDTO']['parentOrderId'];
                                        $detail_info['issueProcessDTOs'] =serialize($detail['data']['issueAPIIssueDTO']['issueProcessDTOs']);
                                        $detail_info['limitRefundAmount'] =json_encode($detail['data']['issueAPIIssueDTO']['limitRefundAmount']);
                                        $detail_info['reasonChinese'] =$detail['data']['issueAPIIssueDTO']['reasonChinese'];
                                        $detail_info['token_id'] =$token['token_id'];
                                        $detail_info['gmtCreate'] =$this->getTime($detail['data']['issueAPIIssueDTO']['gmtCreate']);
                                        $detail_info['updatetime'] =date('Y-m-d H:i:s');

                                        $check_detail = $this->smt_issue_detail_model->getOneDetail($list['orderId']);
                                        if(empty($check_detail)){
                                            $this->smt_issue_detail_model->add($detail_info);
                                        }else{
                                            $upadte_detail =array();
                                            $upadte_detail['where']['orderId'] = $list['orderId'];
                                            $this->smt_issue_detail_model->update($detail_info,$upadte_detail);
                                        }*/


                                } else {
                                    if (!isset($_POST['type'])) {
                                        var_dump($detail);
                                        echo '<br/>';
                                    }
                                    continue;
                                }
                            }
                        } else {
                            if (!isset($_POST['type'])) {
                                echo "没有数据了<br/>";
                            }
                            continue;
                        }
                    } else {
                        if (!isset($_POST['type'])) {
                            var_dump($listArr);
                            echo '<br/>';
                        }

                        continue;
                    }
                }
            }
        }

        $num_result = $this->smt_issue_list_model->getNum();

        if (!empty($num_result) && $num_result) {
            foreach ($num_result as $re) {
                $num_option = array();
                $num_option['order_id'] = $re['order_id'];

                $num_data = array();
                $num_data['num'] = $re['num'];

                $this->smt_issue_list_model->update($num_data, $num_option);
            }
        }

        if ($is_ajax) {
            ajax_return('导入完成', 2);
        }
    }

    function getTime($time)
    {
        $str = mb_substr($time, 0, 14);
        return date('Y-m-d H:i:s', strtotime($str));
    }
}