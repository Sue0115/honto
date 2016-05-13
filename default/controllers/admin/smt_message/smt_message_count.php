<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-13
 * Time: 17:27
 */
class Smt_message_count extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('MySmt');
        $this->load->model(array(
            'smt/Smt_user_tokens_model',
            'smt_message/smt_msg_detail_model',
            'smt_message/smt_msg_list_model',
            'smt_message/smt_msg_reply_model',
            'smt_message/email_mod_model',
            'smt_message/email_mod_class_model',
            'order/orders_model',
            'order/orders_products_model',
            'products/products_data_model',
            'shipment_model',
            'slme_user_model',
            'sharepage'
        ));
        $this->smt = new MySmt();
    }

    public function message_count()
    {

        $data=array();
        $this->_template('admin/smt_message/smt_message_count',$data);
    }




    public function get_message_count()
    {
        $date1=$_POST['date1'];
        $date2=$_POST['date2'];
        $messageType = array('message_center','order_msg');

        $option = array();
        $option['where']['token_status'] = 0;
        $account_arr =array();
        $account  = $this->Smt_user_tokens_model->getAll2Array($option);

        foreach($account as $ac)
        {
            $account_arr[$ac['token_id']] = $ac['accountSuffix'];
        }




        $last_result=array();

        foreach($messageType as $type)
        {



            $sql = "exporttime >'".$date1."' and exporttime <'".$date2."' and messageType='".$type."'";
            $result = $this->smt_msg_list_model->messageCount($sql);

            foreach($result as $re)
            {
                $re['accountSuffix'] = $account_arr[$re['token_id']];
                $re['un_Read'] = $re['num'] - $re['isRead'];
                $re['un_Return'] = $re['num'] - $re['isReturn'];
                $last_result[$re['token_id']][$type] = $re;
            }



            $empty_arr =array(
                'un_Return'=> 0,
                'un_Read' => 0,
                'num' =>0,
                'isRead' => 0,
                'isReturn'=> 0,
                'reply_no' =>0,
            );
            foreach($account as $ac)
            {
                if(!isset($last_result[$ac['token_id']][$type]))
                {

                    $last_result[$ac['token_id']][$type]=$empty_arr;
                    $last_result[$ac['token_id']][$type]['accountSuffix'] = $ac['accountSuffix'];
                }
            }
        }
        $last_result_new = array();
        $i = 0;

        ksort($last_result);

        foreach($last_result as $key=> $v)
        {
            $last_result_new[$i]['message_center'] = isset($last_result[$key]['message_center'])?$last_result[$key]['message_center']:'';
            $last_result_new[$i]['order_msg'] = isset($last_result[$key]['order_msg'])?$last_result[$key]['order_msg']:'';
            $i++;
        }
        unset($last_result);
        ajax_return('','1',$last_result_new);
    }

    public function get_message_user()
    {
        $last_result =array();
        $date1=$_POST['date1'];
        $date2=$_POST['date2'];
        $sql ="reply_time >'".$date1."' and reply_time <'".$date2."'";
        $result = $this->smt_msg_list_model->messageCountByUser($sql);

        if(!empty($result))
        {
            $option =array();
            $option['where']['status'] = 1;

            $user_info = array();

            $user_arr = $this->slme_user_model->getAll2Array($option);

            foreach($user_arr as $arr)
            {
                $user_info[$arr['id']]= $arr['nickname'];
            }
            unset($user_arr);

            foreach($result as $re)
            {
                $re['name'] = isset($user_info[$re['user_id']])?$user_info[$re['user_id']]:'错误';
                $last_result[] = $re;
            }
            unset($result);
            unset($user_info);
        }

        ajax_return('',1,$last_result);

    }

}