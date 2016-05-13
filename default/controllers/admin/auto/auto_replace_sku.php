<?php

/**
 * 自动替换SKU
 * Ebay的订单
 * 
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Auto_replace_sku extends MY_Controller{
    
    function __construct(){
        parent::__construct();

        $this->load->model(array(
                'order/orders_model',
                'order/orders_products_model',
                'order/old_sku_new_sku_model' ,
                'operate_log_model'
                )
        );

        $this->model = $this->orders_model;
       
    }

    public function replace_sku($orders_type = 1){

        header("Content-type: text/html; charset=utf-8");
        //查找所有映射的SKU
        $new_sku = $this->old_sku_new_sku_model->getAll2Array();

        if(empty($new_sku)){
            die('没有映射的SKU');
        }

        $sku = array();

        $sku_key = array();

        foreach ($new_sku as $s) {
            $sku[] = trim($s['new_sku']);
            $sku_key[trim($s['new_sku'])] = trim($s['old_sku']);
        }

        //查找ebay订单
        $options = array();

        $where = array();

        $where_in = array();

        $where_in['p.orders_sku'] = $sku;

        $where['orders_type'] = $orders_type;

        $where['orders_status <='] = 3;

        $where['orders_is_join'] = 0;

        $options['where'] = $where;

        $options['where_in'] = $where_in;

        $options['select'] = array('p.*');

        $join[] = array($this->orders_products_model->_table.' p',"p.erp_orders_id={$this->model->_table}.erp_orders_id");

        $options['join'] = $join;

        $options['limit'] = '50';

        $order = $this->model->getAll2Array($options);
        
        if(empty($order)){
            die('没有需要替换SKU的订单');
        }

        //如果有符合条件的订单
        foreach ($order as $key => $v) {

            $sku = $sku_key[$v['orders_sku']];

            if(empty($sku)){
                continue;
            }
            
            $options = array();

            $options['where']['orders_products_id'] = $v['orders_products_id'];

            $data = array();

            $data['orders_sku'] = $sku;

            $tof = $this->orders_products_model->update($data,$options);

            //写入操作日志
            if($tof){
                $data = array();
                $data['operateUser'] = 30;
                $data['operateKey'] = $v['erp_orders_id'];
                $data['operateText'] = '系统自动替换订单SKU，原SKU：'.$v['orders_sku'].',新SKU：'.$sku;
                $log_tof = $this->operate_log_model->add_order_operate_log($data);
                echo '订单：'.$v['erp_orders_id'].'替换SKU成功，原SKU：'.$v['orders_sku'].'，新SKU：'.$sku.'<br/>';
            }
        }

    }
    

}