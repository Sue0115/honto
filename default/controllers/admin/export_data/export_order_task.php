<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-09-07
 * Time: 16:10
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('PRC');
set_time_limit ( 0 ); //页面不过时
ini_set('memory_limit', '2024M');
header('Content-Type: text/html; Charset=utf-8');


class Export_order_task extends Admin_Controller{
    function __construct()
    {
        parent::__construct();
        $this->load->model(
            array(
                'print/orders_model','shipment_model','sangelfine_warehouse_model',
                'orders_type_model','products/products_data_model','export_order_task_model','kingdee_currency_model','orders_type_model'
            )
        );
        $this->load->library('phpexcel/PHPExcel');
        $this->model = $this->orders_model;
    }





    public function export_task_view()
    {

        $where    = array(); //查询条件
        $in       = array(); //in查询条件
        $like     = array(); //like查询条件
        $string   = array(); //URL参数
        $curpage  = (int)$this->config->item('site_page_num');
        $per_page = (int)$this->input->get_post('per_page');
        $search   = $this->input->get_post('search');

        if (isset($search['warehouse_id']) && $search['warehouse_id']) {
            $where['warehouse_id'] = trim($search['warehouse_id']);
            $string[]           = 'search[warehouse_id]=' . trim($search['warehouse_id']);
        }

        if (isset($search['order_type']) && $search['order_type']) {
            $where['order_type'] = trim($search['order_type']);
            $string[]           = 'search[order_type]=' . trim($search['order_type']);
        }

        $search = $search ? $search : array();

        $orderBy = 'id DESC';
        //查询条件
        $options     = array(
            //'select'   => "{$this->model->_table}.*, s.*",
            'where'    => $where,
            'where_in' => $in,
            'page'     => $curpage,
            'per_page' => $per_page,
            'order'              => $orderBy
        );
        if (!empty($like)){
            $options = array_merge($options, array('like' => $like));
        }


        $return_data = array('total_rows' => true);
        $data_array   = $this->export_order_task_model->getAll($options, $return_data);

        $c_url = admin_base_url('export_data/export_order_task/export_task_view');
        $url   = $c_url . '?' . implode('&', $string);

        $page = $this->sharepage->showPage($url, $return_data['total_rows'], $curpage);


        $data = array(
            'data'           =>$data_array,
            'search'             => $search,

            'page'               => $page,
            'totals'             => $return_data['total_rows'],
            'c_url'              => $c_url,


        );


        $order_type_option = array();
        $order_type_result = $this->orders_type_model->getAll2Array($order_type_option);
        $order_type_result_new =array();
        foreach($order_type_result as $v)
        {
            $order_type_result_new[$v['typeID']] = $v['typeName'];
        }
        unset($order_type_result);

        $data['order_type'] = $order_type_result_new;

        $this->_template('admin/export_data/export_task_list_view',$data);


    }







    public function downloadExecl()
    {
        $id = $_POST['id'];
        $option_array= array();
        $option_array['where']['id'] = $id;

        $result =  $this->export_order_task_model->getOne($option_array,true);


    }
}