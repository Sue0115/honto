<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
/**
 * Created by PhpStorm.
 * User: Administrator
 * DateTime: 2015-06-12
 */
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sku_date_stock extends MY_Controller{

    public $api_time;
    const  API_START          = 1;
    const  API_END            = 2;
    function __construct(){
        parent::__construct();   
        $this->load->model(array(
            'system_model','stock/stock_detail_model','stock/sku_date_stock_model','order/order_api_log_model')
        );
    }
    
    /**
     * 获取sku实际库存(入口)
     */
    public function getSkuStockNum(){
        //添加数据主程序开始
        $this->api_time =  date('Y-m-d  H:i:s');
        $apiInsertData = array(
            'api_time'  => date('Y-m-d'),
            'platform'  => 'SLME',//敦煌,
            'api_name'  => 'insertStockNum',
            'start_time'=> $this->api_time,
            'status'    => self::API_START ,
        );
        
        $this->order_api_log_model->dhgateOrderApiLog($apiInsertData);
        $SkuStockNumInfo = $this->stock_detail_model->getSkuStockNumInfo();//获取sku实际库存信息
        $this->insertSkuStockNum($SkuStockNumInfo);
    }
    
    /**
     * 添加当天sku实际库存记录
     */
    public function insertSkuStockNum($SkuStockNumInfo){
        foreach($SkuStockNumInfo as $key=>$val){
            $data = array(
                'sku'           =>$val->products_sku,
                'stock_num'     =>$val->actual_stock,
                'warehouse_id'  =>$val->stock_warehouse_id,
                'create_time'   =>date('Y-m-d H:i:s')
            );
            $this->sku_date_stock_model->add($data);
        }
        //添加数据主程序完成
        $apiUpdateData = array(
            'end_time' => date("Y-m-d H:i:s"),
            'status'   =>self::API_END ,
        );
        $this->order_api_log_model->updatedhgateOrderApiLog($this->api_time,'SLME',$apiUpdateData);
    }
}