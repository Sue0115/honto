<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class up_pick_orders extends MY_Controller{
	
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array(
                'order/orders_model','order/pick_model',
				'order/pick_product_model'
          )
        );
       $this->model = $this->pick_model;
	   $this->load->model(array('operate_log_model'));

    }
	//未发货的 订单表未改变的数据恢复
	function index(){
			$sql = "SELECT  `orders_id` ,  `pick_id` 
					FROM erp_pick_product
					WHERE  `status` =8
					AND  `create_time` > UNIX_TIMESTAMP( NOW( ) - INTERVAL 10
					DAY ) 
					AND result IS NULL 
					ORDER BY create_time"; //之前数据status改为了8  但是result是没改的  而且是这10天内的数据 线上3531条数据未修改
			$result = $this->model->return_array($sql);  //未发货的 订单表未改变的数据
			$orders_id = '';
			$pick_id = '';
			$uid = 'erp';
			$i = 0;
			foreach($result as $k=>$v){
				$orders_id .= $v['orders_id'].',';
				$pick_id .= $v['pick_id'].',';
			}
			$orders_id = rtrim($orders_id,',');   //将未改变的订单id拼接
			if($orders_id){
				$sql = "update erp_orders set `orders_status`='3',`orders_is_backorder`='1',`orders_print_time`=null where `erp_orders_id` IN (".$orders_id.") AND `orders_status`=4";//修改状态为已通过
				$ret = $this->model->return_query($sql);
				if($ret){
						foreach($result as $k=>$v){
					        $logData= array( //加入日志
						      				'operateUser' =>$uid,
						      				'operateTime '=>date('Y-m-d H:i:s',time()),
					                        'operateType' => 'update',
					                        'operateMod' => 'ordersManage',
					                        'operateKey' => $v['orders_id'],
					                        'operateText' => '货找面单退回,订单状态变为已通过,拣货单号'.$v['pick_id'], 
					                    	);
					        $insertLog=$this->operate_log_model->add($logData); //写入日志
							$i++;
						}
						echo "订单表未修改的数据状态修改成功！本次共修改".$i."条订单数据，订单id为".$orders_id.",拣货单id为".$pick_id."";
				}
			}else{
				echo "没有要修改的数据或数据出错，请检查！";
			}
  }
}