<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class auto_pick_complete extends MY_Controller{
	
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
	/*操作
	*<=30个的拣货单
	*/
	function index(){
		$result = $this->model->pick_complete();
		$uid  = 'erp';
		$pick_id = '';
		foreach($result as $k=>$v){
			if($v['type'] == 1 || $v['type'] == 2){  //当类型为1，2 是单品单件  单品多件
				if($v['num'] < 30){    //则未包装件数小于30个  拼接pick_id
					$pick_id .= $v['pick_id'].',';
				}
			}else if($v['type'] == 3){    //当类型为3 是多品
				$total = $v['sku_num'] * 0.2;
				if($v['num'] < $total){      //未包装数小于总件数的20%  拼接pick_id
					$pick_id .= $v['pick_id'].',';
				}
			}
		}
			$pick_id = rtrim($pick_id,',');
			if($pick_id){
				//事务开始
				  $this->db->trans_begin();
				$ret=$this->model->change_pick_id($pick_id);//将id下的拣货单改为已标记发货
			if($ret){
				$pick = $this->model->change_pick_status($pick_id);//拣货单详情表未拣货订单改为已通过
				$ordersid = $this->model->getorders_id($pick_id);
				if($ordersid){
					$orders_id = '';
					foreach($ordersid as $k=>$v){
						$orders_id .= $v['erp_orders_id'].',';
					}
				}
				if($orders_id){
					$orders_id = rtrim($orders_id,',');
					$pick = $this->model->check_pick($orders_id);//订单表erp_orders未拣货订单改为已通过
					if($pick){
						$this->db->trans_commit();//事务结束
						foreach($ordersid as $k=>$v){
					        $logData= array( //加入日志
						      				'operateUser' =>$uid,
						      				'operateTime '=>date('Y-m-d H:i:s',time()),
					                        'operateType' => 'update',
					                        'operateMod' => 'ordersManage',
					                        'operateKey' => $v['erp_orders_id'],
					                        'operateText' => '货找面单退回,订单状态变为已通过,拣货单号'.$v['pick_id'], 
					                    	);
					        $insertLog=$this->operate_log_model->add($logData);
						}
						echo "正在包装的未拣单数据修改状态成功！本次修改的拣货单id为".$pick_id.",订单id为".$orders_id."";
					}else{
						$this->db->trans_rollback();
						
					}
				}
			}
		}else{
			echo "本次没有修改的数据或数据出错！";
		}
  }
}