<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//月排行榜
class MonthList extends Admin_Controller{
	
	
function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'sharepage','order/pick_model','order/pick_product_model',
								'slme_user_model','order/use_shipped_num_model','sangelfine_warehouse_model',
								'user_ship_statistical_model'
								)
							);

		$this->model = $this->pick_product_model;
		
	}
	
  public function index(){
  	
  	$keys = $this->user_info->key;//用户组key

  	//找出货找面单组的用户
  	$userGroup = $this->slme_user_model->getUserBykey('');
  	
  	//找出用户的所有信息
  	$userInfo = $this->slme_user_model->getAllLc();
  	$new_user_info = array();
  	foreach($userInfo as $u){
  	  $new_user_info[$u->id] = $u->warehouse_id;
  	}
  	
  	//找到所有仓库
    $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
	foreach($warehouse as $va){
		$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
	}

  	$time = $this->input->get_post('start_date');
  	
  	$scanTime = empty($time) ? date('Y-m') : $time;	
  	
	//获取下一个月的时间
	$timeArr = explode('-',$scanTime);
	
	$currentMonth = $timeArr[1];
	
	
	$nextMonth = $timeArr[1]+1;
	if($nextMonth<10){
	  $nextMonth = '0'.$nextMonth;
	}
	$nextTime = $timeArr[0].'-'.$nextMonth;
 
	$lastDayTime = date('Y-m-d',strtotime('-1 day'));//昨天时间戳
	
	$currentDayTime = strtotime(date('Y-m-d'));//今天时间戳
	
	$nextDayTime = strtotime(date('Y-m-d'))+86400;//明天时间戳

    $MonthData = $this->user_ship_statistical_model->getDataByMonth($scanTime.'-01',$nextTime.'-01');//当月的发货数,要累加的与昨天的不能同样的方法处理

    $dayData = $this->model->getProductCountByTime($currentDayTime,$nextDayTime);//当天发货数
    
    $lastData = $this->user_ship_statistical_model->getDataByDate($lastDayTime,date('Y-m-d',$currentDayTime));//昨天发货数

    $newMonthData = array();//存放详细发货信息的数组
    
    
    //将当日发货数重组进去
    foreach($dayData as $vs){
      $newMonthData[$vs['ship_uid']]['currentDay'] = $vs['all_num'];
      $newMonthData[$vs['ship_uid']]['currentDayProductNum'] = $vs['total_num'];
    }
    //将昨日发货数重组进去
    foreach($lastData as $vss){
      $newMonthData[$vss['ship_uid']]['lastDay'] = $vss['ship_orderCount'];
      $newMonthData[$vss['ship_uid']]['currentLastProductNum'] = $vss['ship_productCount'];
    }
    
  //重组当月发货数组，以scan_uid为键名,当月的还要加上今日发货数
    foreach($MonthData as $v){
     $curr_order_count = isset($newMonthData[$v['ship_uid']]['currentDay']) ? $newMonthData[$v['ship_uid']]['currentDay'] : 0;//今日发货订单数
     $curr_product_count = isset($newMonthData[$v['ship_uid']]['currentDayProductNum']) ? $newMonthData[$v['ship_uid']]['currentDayProductNum'] : 0;
     $newMonthData[$v['ship_uid']]['currentMonth'] = $v['orderCount']+$curr_order_count;
     $newMonthData[$v['ship_uid']]['currentMonthProductNum'] = $v['productCount']+$curr_product_count;
    }

    //从erp_use_shipped_num表中获取当月总工时和当月发错数,并整合进去$newMonthData数组中
    //从erp_pick_product获取某用户当月的扫描商品数
    foreach($newMonthData as $key => $va){
       $month_static_data = array();
       $month_order_product_num = array();//当月扫描的商品数
       $currDay_product_num = array();//今天扫描的商品数
       $lastDay_product_num = array();//昨天扫描的商品数
       
       //组装当月总工时和当月发错数
       $month_static_data = $this->use_shipped_num_model->getTimeAndCountByTime($key,$currentMonth);
       $newMonthData[$key]['monthTotalTime'] = isset($month_static_data['monthTotalTime']) ? $month_static_data['monthTotalTime'] : 0;
       $newMonthData[$key]['monthErrorNum'] = isset($month_static_data['monthErrorNum']) ? $month_static_data['monthErrorNum'] : 0;
       
       //组装当月扫描商品数
//       $month_order_product_num = $this->model->getOrderProductCountByTime($key,strtotime($scanTime),strtotime($nextTime));
//       $newMonthData[$key]['currentMonthProductNum'] = isset($month_order_product_num['total_num']) ? $month_order_product_num['total_num'] : 0;
//       
//       //组装当天扫描的商品数
//       $currDay_product_num = $this->model->getOrderProductCountByTime($key,$currentDayTime,$nextDayTime);
//       $newMonthData[$key]['currentDayProductNum'] = isset($currDay_product_num['total_num']) ? $currDay_product_num['total_num'] : 0;
//
//       //组装昨天扫描的商品数
//       $lastDay_product_num = $this->model->getOrderProductCountByTime($key,$lastDayTime,$currentDayTime);
//       $newMonthData[$key]['currentLastProductNum'] = isset($lastDay_product_num['total_num']) ? $lastDay_product_num['total_num'] : 0;
//       
    }

    $data = array(
      'userGroup'  =>  $userGroup,
      'result'     =>  $newMonthData,
      'scanTime'   =>  $scanTime,
      'currentMonth'=> $currentMonth,
      'keys'        => $keys,
      'warehouseArr'=> $warehouseArr,
      'new_user_info'=> $new_user_info
    );
    
    $this->_template('admin/order/monthList',$data);
  }
  
  /**
   * 修改工时和错误发货数
   * 显示界面
   */
  function UpdateTimeAndNum(){
     $month = $this->input->get_post('month');
     $new_month = $month > 10 ? $month : '0'.$month;
     $uid = $this->input->get_post('uid');
     $year = date('Y');
     //先获取总工时和错误的发货数
     $option = array();
     $where = array();
     $select  = array();
     $select = array('monthTotalTime','monthErrorNum');
     $where = array(
       'years' => $year,
       'months'=> $new_month,
       'uid'   => $uid
     );
     $option = array(
       'select'  =>  $select,
       'where'   =>  $where
     );
     $re = $this->use_shipped_num_model->getOne($option,true);
     $data = array(
       'uid'  =>  $uid,
       'month'=>  $month,
       'vdata'=>  $re
     );
     $this->template('admin/order/update_month_data',$data);
  }
  /**
   * 修改工时和错误发货数
   * 处理过程
   */
  function updateing(){
  	
  	$flag = false;//默认失败
  	
    $uid = $this->input->get_post('uid');
    $month = $this->input->get_post('month');
    $year = date('Y');
    $totalTime = $this->input->get_post('totalTime');
    $totalNum = $this->input->get_post('totalNum');
    $new_month = $month >= 10 ? $month : '0'.$month;
    
    //根据uid和month查找某人在某月的记录是否存在，存在就更新，不存在则插入
    $result = $this->use_shipped_num_model->getTimeAndCountByTime($uid,$new_month,$year);

    //要插入或者更新的数组
    $data = array();
    $data['years'] = $year;
    $data['months']= $new_month;
    $data['uid'] = $uid;
    $data['monthErrorNum'] = $totalNum;

    if(!empty($result)){//如果存在的话，更新该记录
      $data['monthTotalTime'] = $totalTime + $result['monthTotalTime'];
      $option = array();
      $where = array(
        'uid' => $uid,
        'years'=> $year,
        'months'=> $new_month
      );
      $option['where'] = $where;
      $ud = $this->use_shipped_num_model->update($data,$option);
      if($ud){
        $flag = true;
      }
    }else{//否则，新插入数据
      $data['monthTotalTime'] = $totalTime;
      $ad = $this->use_shipped_num_model->add($data);
      if($ad){
       $flag = true;
      }
    }

    echo json_encode($flag);
  }
}
