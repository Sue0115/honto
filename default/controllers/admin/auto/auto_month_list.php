<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class auto_month_list extends MY_Controller{
	
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array(
                'order/orders_model','track_number_model','operate_log_model',
        		'order/pick_product_model','user_ship_statistical_model'
          )
        );
       $this->model = $this->pick_product_model;

    }
    
    //获取前一天的发货数据(发货数、商品数)
    public function index(){
//      $dataTime = '2015-06-14';
//      $lastDayTime = strtotime($dataTime);
      $lastDayTime = strtotime('-1 day');//昨天时间戳
      $lastDayfrom = date('Y-m-d',$lastDayTime).' 00:00:00';//正常日期格式,开始时间
      $lastDayto   = date('Y-m-d',$lastDayTime).' 23:59:59';//正常日期格式,开始时间
      
	  $data = $this->model->getProductCountByTime(strtotime($lastDayfrom),strtotime($lastDayto));

	  $newMonthData = array();
      //将当日发货数重组进去
      foreach($data as $vs){
        $newMonthData[$vs['ship_uid']]['currentDay'] = $vs['all_num'];
        $newMonthData[$vs['ship_uid']]['currentDayProductNum'] = $vs['total_num'];
      }
      //将数据存表
      foreach($newMonthData as $k => $d){
        $insertData = array();
        $insertData['ship_uid'] = $k;
        $insertData['ship_orderCount'] = $d['currentDay'];
        $insertData['ship_productCount'] = $d['currentDayProductNum'];
        $insertData['ship_time'] = date('Y-m-d',strtotime('-1 day'));
        //$insertData['ship_time'] = $dataTime;
        
 		$id = $this->user_ship_statistical_model->add($insertData);
 		if($id>0){
 		  echo $lastDayfrom.'的数据成功插入，id为'.$id.'<br/>';
 		}
      }
	  
    }
}