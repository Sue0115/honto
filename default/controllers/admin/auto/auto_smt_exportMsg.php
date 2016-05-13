<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-05
 * Time: 10:07
 */
set_time_limit(0); //页面不过时
header("Content-type:text/html;charset=utf-8");

class  Auto_smt_exportMsg extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('MySmt');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
            'smt_message/smt_msg_detail_model',
            'smt_message/smt_msg_list_model'
        ));
        $this->smt = new MySmt();


    }


    public function autoExportMsg($type=2,$token='')
    {

        $option = array();
        $option['where']['token_status'] = 0;

        if($type ==1) // 多线程获取信件
        {
            $off = 10; //系数
            if($token!='')
            {
                $option['where']['token_id  >='] = ($token-1)*$off;
                $option['where']['token_id  <'] = ($token)*$off;

            }else
            {
                echo '多线程获取信件  请指定第二参数 例如 /1/2  表示取token 10-19 的账号';
                exit;
            }

        }else if($type == 2) {
            if($token!='') // 指定账号
            {
                $option['where']['token_id'] = $token;
            }
            else
            {
                echo '请指定账号';
                exit;
            }
        }else if($type==100){
            // $type=100  循环 账号获取

        }else{
            echo '请指定类型';
            exit;
        }
        $result = $this->Smt_user_tokens_model->getAll2Array($option);
        foreach ($result as $account) {
            $this->smt->setToken($account);
            $msgSourcesArr =array('message_center','order_msg');

            foreach($msgSourcesArr as $Sources)
            {
                echo $Sources.'  开始获取数据';
                echo '<br/>';
                $method = 'api.queryMsgRelationList';
                $msgSources = $Sources;
               // $msgSources = 'message_center'; // 站内信
                // $msgSources='order_msg'; //订单留言
                $filter = 'readStat';
                $pageSize = 100;
                for ($page = 1; $page < 50; $page++) {
                    $para = "currentPage=$page&pageSize=$pageSize&msgSources=$msgSources&filter=$filter";
                    $listArrJson = $this->smt->getJsonData($method, $para);
                    $listArr = json_decode($listArrJson, true);
                    if (isset($listArr['result'])) {
                        if (!empty($listArr['result'])) {
                            foreach ($listArr['result'] as $v) {//198540864
                                $msg_listarr = array();
                                $msg_listarr['otherName'] = addslashes($v['otherName']);
                                $msg_listarr['rank'] = $v['rank'];
                                $msg_listarr['childId'] = $v['childId'];
                                $msg_listarr['dealStat'] = $v['dealStat'];
                                $msg_listarr['messageTime'] = $this->changetime($v['messageTime']); //
                                $msg_listarr['channelId'] = $v['channelId'];
                                $msg_listarr['unreadCount'] = $v['unreadCount'];
                                $msg_listarr['lastMessageIsOwn'] = $v['lastMessageIsOwn'];
                                if($v['lastMessageIsOwn']) // 如果这条信息是自己的没有获取的意义
                                {
                                    continue;
                                }

                                $msg_listarr['readStat'] = $v['readStat'];
                                $msg_listarr['otherLoginId'] = $v['otherLoginId'];
                                $msg_listarr['lastMessageId'] = $v['lastMessageId'];
                                $msg_listarr['lastMessageContent'] = addslashes($v['lastMessageContent']);
                                if(empty($v['otherLoginId'])) // 如果这个为空。就不能回信息了 所以为空就跳过
                                {
                                    continue;
                                }
                                $msg_listarr['otherLoginId'] = addslashes($v['otherLoginId']);
                                $msg_listarr['token_id'] = $account['token_id'];
                                $msg_listarr['erp_user_id'] = "";
                                $msg_listarr['reply_no'] = 0;
                                $msg_listarr['isRead'] = 0;
                                $msg_listarr['messageType'] = $msgSources;
                                $msg_listarr['exporttime'] =date('Y-m-d H:i:s',time());
                                $check_option = array();
                                $check_option['where']['lastMessageId'] = (string)$v['lastMessageId'];
                                $check_option['where']['token_id'] = intval($account['token_id']);


                                $one_list = $this->smt_msg_list_model->getOne($check_option, true);

                               
                                if (empty($one_list)) {
                                    $this->db->trans_begin(); // 开启一下事物

                                    $update_option = array();
                                    $update_option['where']['channelId'] =$msg_listarr['channelId'];
                                    $update_data =array();
                                    $update_data['isRead'] = 1;
                                    $this->smt_msg_list_model->update($update_data,$update_option); // 把以前导进来 然后没有阅读 属于同一批channelId 更新为已读 wtf

                                    $this->smt_msg_list_model->add($msg_listarr);
                                    echo '<span style="color: #d12723">' . $v['lastMessageId'] . '</span>';
                                    echo '<br/>';
                                } else {

                                    echo $v['lastMessageId'] . " 信息存在 跳过";
                                    echo '<br/>';
                                    continue;
                                }


                                $page1 = 1; // 暂时设置1 。;
                                $methodNew = 'api.queryMsgDetailList';
                                $channelId = $v['channelId'];
                                $para = "currentPage=$page1&pageSize=$pageSize&msgSources=$msgSources&channelId=$channelId";
                                $detailArrJson = $this->smt->getJsonData($methodNew, $para);
                                $detailArr = json_decode($detailArrJson, true);
                                if (isset($detailArr['result'])) // 不为空
                                {
                                    foreach ($detailArr['result'] as $detail) {
                                        $msg_detailarr = array();
                                        $msg_detailarr['summary'] = json_encode($detail['summary']);
                                        $msg_detailarr['content'] = addslashes($detail['content']);
                                        $msg_detailarr['datail_id'] = $detail['id'];
                                        $msg_detailarr['senderName'] = $detail['senderName'];
                                        $msg_detailarr['gmtCreate'] = $this->changetime($detail['gmtCreate']);
                                        $msg_detailarr['filePath'] = json_encode($detail['filePath']);
                                        $msg_detailarr['messageType'] = $detail['messageType'];
                                        $msg_detailarr['typeId'] = $detail['typeId'];
                                        $msg_detailarr['channelId'] = $channelId;
                                        $msg_detailarr['token_id'] = $account['token_id'];
                                        $check_option = array();
                                        $check_option['where']['datail_id'] = (string)$detail['id'];
                                        $check_option['where']['token_id'] = $account['token_id'];
                                        $check_option['where']['typeId'] = $detail['typeId'];
                                        $one_detail = $this->smt_msg_detail_model->getOne($check_option, true);
                                        if (empty($one_detail)) {
                                            $this->smt_msg_detail_model->add($msg_detailarr);
                                        }
                                    }
                                    if ($this->db->trans_status() === TRUE) {
                                        $this->db->trans_commit();//事务结束
                                    }
                                }
                                else // 调用API 失败  或者数据为空
                                {

                                    $this->db->trans_rollback(); // 如果没detail信息 则list表 也不插入信息
                                    var_dump($detailArr);
                                    echo '<br/>';
                                }
                            }
                        } else {
                            echo '没有数据了';
                            echo '<br/>';
                            break;
                        }
                    } else {
                        echo "调用API失败";
                        print_r($listArr);
                    }
                }
                echo $Sources.'  获取数据完成';
                echo '<br/>';
            }

        }
    }


    public function changetime($time)
    {

        $time = date('Y-m-d H:i:s', substr($time, 0, 10));

        return $time;
    }

}