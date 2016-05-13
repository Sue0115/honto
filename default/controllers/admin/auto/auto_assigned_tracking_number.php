<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/23
 * Time: 14:44
 * 自动分配挂号码
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//
class Auto_assigned_tracking_number extends MY_Controller{
    function __construct()
    {
        parent::__construct();
        //$this->load->model("orders_model");
        $this->load->model(array(
                'order/orders_model','track_number_model','operate_log_model')
        );

    }

    //分配追踪号
    public function index($id = '')
    {   
        $shipping_id = $id;

        if(empty($id)){
            $shipping_id = $this->input->get('shipping_id');//获取物流方式编号ID
        }

        if ($shipping_id == 0 || empty($shipping_id))//如果物流号为0或为空终止脚本
        {
            die('物流ID为空');
        }

        $orders = $this->orders_model->info($shipping_id);//根据该物流方式查找状态为3，而且物流追踪号为空的订单


        if(!(is_array($orders))||empty($orders))//如果订单信息不是数组或者订单数组为空终止脚本
        {
            die('没有找到符合条件的订单');
        }

        foreach ($orders as $rd)
        {
            $track_number = $this->track_number_model->get_track_number($rd['shipmentAutoMatched']);//获取可用物流追踪号

            if (empty($track_number))
            {
                continue;
            }

            $data=array();

            $data['orders_shipping_code']=$track_number->track_number;

            $options=array();

            $options['where']['erp_orders_id']=$rd['erp_orders_id'];

            $distribute = $this->orders_model->update($data, $options);//给该订单分配一个可用物流追踪号

            if ($distribute == TRUE)
            {   
                $data=array();

                $data['order_id']=$rd['erp_orders_id'];

                $options=array();

                $options['where']['track_number']=$track_number->track_number;

                $this->track_number_model->update($data, $options);//更新该追踪号为已用

                //加入订单日志
                $log_data = array();
                $log_data['operateUser'] = 30;
                $log_data['operateKey'] =  $rd['erp_orders_id'];
                $log_data['operateText'] = "自动分配追踪号：".$track_number->track_number;
                $log_tof = $this->operate_log_model->add_order_operate_log($log_data);
                echo $rd['erp_orders_id'].',';
            }
        }
    }
}