<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
/**
 * 以下是业务核对功能
 * time 2015-08-26
 */
class auto_check_orders extends MY_Controller{

	
    function __construct()
    {
        parent::__construct();
        
       $this->load->model(
							array(
								'order/orders_model','shipment_model','sangelfine_warehouse_model',
								'orders_type_model','category_model','products/products_data_model',
								'orders_receivable_model','order/currency_info_model','orders_receivable_detail_model',
								'sharepage','slme_user_model','order/order_check_status_model','order/order_check_result_model'
							)
		);
       $this->model = $this->order_check_status_model;

    }
    
 	/**
     *
     * 抓取7天前的那天（比如今天7号，那应该抓取1号那天已发货的订单数据），已发货的数据，存入表（erp_order_check_status），is_check=0
     */
	public function get_order_data(){
		
	   $option = array();
	   
	   $time = date('Y-m-d',strtotime('-7 day'));
	   
	   $where = array();
	   
	   $select = array('erp_orders_id','orders_status','buyer_id');
	   
//	   $where['orders_shipping_time >='] = $time.' 00:00:00';
//
//	   $where['orders_shipping_time <='] = $time.' 23:59:59';
 	   $where['orders_export_time >='] = '2015-07-21';
 	   $where['orders_export_time <'] = '2015-07-31';
	   
	   $where['orders_status'] = 5;//已经发货的
	   
	   $where['orders_type'] = 6;
	   
	   $where['orders_is_join'] = 0;
	   
	   $option = array(
	     'select' => $select,
	   	 'where'  => $where,
	   );
	   
	   $data = $this->orders_model->getAll2array($option);

	   foreach($data as $d){
	     //存入表（erp_order_check_status）
	     $in = array();
	     $in['erp_orders_id'] = $d['erp_orders_id']; 
	     $in['buyer_id'] 	  = $d['buyer_id']; 
	     $in['create_time']   = date('Y-m-d H:i:s');  
	   	 $id = $this->order_check_status_model->add($in);
	   	 if($id>0){
	   	   echo '<span style="color:green;">'.$id.'数据插入成功</span><br/>';
	   	 }else{
	   	   echo '<span style="color:red;">'.$id.'数据插入失败</span><br/>';
	   	 }
	   	 
	   }

	}
	
	/**
	 *每次抓取（erp_order_check_status和erp_orders 链表后的数据），条件is_check=0，每次100条。
	 *把拿到的数据，去和财务导入收款信息表的，数据做整理，然后把整理后的，数据存入表erp_order_check_result
	 */
	public function deal_check_order_data(){
		
		//获取币种英文和中文数据
       $curr_info = $this->currency_info_model->getAllInfo('currency_value');
		
	   $option = array();
	 
	   $join = array();
	   
	   $where = array();
	   
	   $select = array(
	   			"c.*","{$this->order_check_status_model->_table}.is_check","{$this->order_check_status_model->_table}.id"
	    );
	  
	   $join[] = array('erp_orders c',"c.erp_orders_id={$this->order_check_status_model->_table}.erp_orders_id");
	   
	   $where = array("{$this->order_check_status_model->_table}.is_check"=>0);
	  
	  //获取订单表中数据10条
	  $option	= array(
				'select' => $select,
		  		'join'   => $join,
	  			'where'  => $where,
	  			'limit'  => 10
	  );
	  
	  $result = $this->model->getAll2array($option);

	  if(empty($result)){
	      echo '<span style="color:red;">没有要处理的订单</span><br/>';
	      die;
	  }

	  //开始比对数据
	  foreach($result as $r){
	  	
	  	 //重新查询 erp_order_check_status的订单号的is_check状态，预防拆单的重新校对
	  	 $Info = $this->order_check_status_model->getInfoByID($r['id']);
	  	 if($Info['is_check']==1){
	  	   continue;
	  	 }
	  	
	  	 $this->db->trans_begin();//事务开始
	  	
	  	 $timeArr = array();
	  	
	  	 $timeArr = explode('-',$r['orders_shipping_time']);
	  
	  	 $in_data = array();//插入校对结果表的数据
	  	
	     //根据订单信息中的buyer_id获取订单表的数据
	     $orderArr = $this->getInfoByfiled('buyer_id','orders_model',$r['buyer_id']);//用买家ID重新获取一下订单信息，是避免拆单的问题
	     $count_erp = count($orderArr);
	     $erp_order_id_string = '';
	     $erp_orders_total = 0;//erp订单总金额
	     $erp_plat_total = 0;//erp平台总金额
	     foreach($orderArr as $o){
	       $erp_order_id_string .= '+'.$o['erp_orders_id'];
	       $erp_orders_total += $o['orders_total'];
	       $erp_plat_total += $o['platFeeTotal '];
	     }
	     
	     $erp_order_id_string =substr($erp_order_id_string,1);

	     //根据erp订单中的buyer_id获取订单导入详情的数据
	     $order_receiveableArr = $this->getInfoByfiled('erp_buyer_id','orders_receivable_detail_model',$r['buyer_id']);

		  //更新erp_orders_receivable_detail表的status=2，防止两个相同买家ID的订单再次核对，主要针对拆单的情况
		  if(!empty($order_receiveableArr)){
		    $op = array();
		    $w = array();
		    $updata = array();
		    $w['erp_buyer_id'] = $r['buyer_id'];
		    $updata['status'] = 2;
		    $this->orders_receivable_detail_model->update($updata,$w);
		  }

	     if(empty($order_receiveableArr)){//如果找不到该订单的收款导入信息
	     	
	       $up_data = array();
		   $options = array();
		   $up_data['is_check'] = 1;//是否已经核对过，0-否，1是
		   $up_data['check_status'] = 2;//核对状态，1-正常，2-异常
		   $options['where'] = array('erp_orders_id'=>$r['erp_orders_id']);
		   $this->order_check_status_model->update($up_data,$options);
		   
		   $in_data['orders_id'] 			= $r['erp_orders_id'];
		   $in_data['platform_orders_id']  	= '';//平台订单号，多个加号隔开
		   $in_data['trading_number'] 	  	= $r['pay_id'];//平台交易号
		   $in_data['platform_total'] 	  	= '';//平台总订单金额
		   $in_data['platform_fee'] 		= '';//平台总佣金
		   $in_data['platform_lianmen_fee']	= '';//平台总联盟佣金
		   $in_data['plat_other']			= '';//其它
		   //平台总费用；平台总费用=平台总佣金+平台总联盟佣金+其它（用于更新erp平台费用）
		   $in_data['platform_total_fee'] 	= '';
		   $in_data['loan_amount'] 		  	= '';//平台总放款金额
		   $in_data['refund_amount'] 		= '';//平台总退款金额
		   $in_data['residual_amount'] 	  	= '';//压在平台的总金额
		   $in_data['check_time'] 	 	  	= date('Y-m-d H:i:s');//核对时间，即记录创建时间
		   $in_data['is_update_fee']		= 0;//是否已经更新erp_orders表佣金，0-未更新，1-已更新
		   $in_data['is_update_shipment_cost']= 0;//是否已更新erp_orders表物流成本，0-未更新，1-已更新
		   $in_data['erp_total']			= $r['orders_total'];//ERP订单总金额
		   $in_data['erp_fee']				= $r['platFeeTotal'];//ERP订单总平台佣金
		   $in_data['orders_type']			= $r['orders_type'];//订单类型
		   $in_data['sales_account']		= $r['sales_account'];//账号
		   $in_data['type']					= '';//核对类型
		   $in_data['mouth_num']			= $timeArr[1];//订单发货月份
		   $in_data['year_num']				= $timeArr[0];//订单发货年份
		   $in_data['data_status'] 			= 4;
		   $this->order_check_result_model->add($in_data);
		   
		   $this->db->trans_commit();//事务结束
		   echo '<span style="color:red;">订单号为'.$r['erp_orders_id'].'的buyer_id为'.$r['buyer_id'].'的订单导入详情数据不存在</span><br/>';
	       continue;
	     }

	     $count_orderType = count($order_receiveableArr);
	     $orderType_string = '';//存放平台订单号
	     $platform_fee = 0;//平台总佣金
	     $platform_lianmen_fee = 0;//平台总联盟佣金
	     $loan_amount = 0;//平台放款总金额
	     $refund_amount = 0;//平台总退款金额
	     $residual_amount = 0;//压在平台的总金额
	     
	     //获取币率
	     $currency_value = 1;//默认1
	     if($order_receiveableArr[0]['currency_type']=='CNY'){
	       $currency_value = $curr_info['RMB'];
	     }else{
	       $currency_value = $curr_info[$order_receiveableArr[0]['currency_type']];
	     }

	     foreach($order_receiveableArr as $or){
	       $orderType_string 	 =  $or['erp_buyer_id'];//平台订单号
	       $platform_fee 		 += $or['plat_amount'];//扣除平台佣金
	       $platform_lianmen_fee += $or['union_amount'];//扣除联盟佣金
	       $loan_amount 		 += $or['loan_amount'];//放款金额
	       $refund_amount 		 += $or['return_amount'];//退款金额
	     }

	     $residual_amount = $order_receiveableArr[0]['orders_total']-($platform_fee+$platform_lianmen_fee+$loan_amount+$refund_amount);
	     
	     $type = 0;//记录类型，1-ERP 1对平台1,2-ERP 1对平台多，3-ERP多对平台1,4-ERP多对平台多
	     if($count_erp==1 && $count_orderType==1){
	        $type = 1;
	     }
	  	 if($count_erp==1 && $count_orderType>1){
	        $type = 2;
	     }
	  	 if($count_erp>1 && $count_orderType==1){
	        $type = 3;
	     }
	  	 if($count_erp>1 && $count_orderType>1){
	        $type = 4;
	     }
	     $in_data['orders_id'] 			  	= $erp_order_id_string;//erp内单号，多个加号隔开
		 $in_data['platform_orders_id']  	= $orderType_string;//平台订单号，多个加号隔开
		 $in_data['trading_number'] 	  	= $r['pay_id'];//平台交易号
		 $in_data['platform_total'] 	  	= $order_receiveableArr[0]['orders_total'];//平台总订单金额
		 
		 $in_data['platform_fee'] 		 	= $platform_fee;//平台总佣金
		 $in_data['platform_lianmen_fee']	= $platform_lianmen_fee;//平台总联盟佣金
		 $in_data['plat_other']				= '';//其它
		 //平台总费用；平台总费用=平台总佣金+平台总联盟佣金+其它（用于更新erp平台费用）
		 $in_data['platform_total_fee'] 	= $in_data['platform_fee']+$in_data['platform_lianmen_fee']+$in_data['plat_other'];
		 
		 $in_data['loan_amount'] 		  	= $loan_amount;//平台总放款金额
		 $in_data['refund_amount'] 		  	= $refund_amount;//平台总退款金额
		 $in_data['residual_amount'] 	  	= $residual_amount<0 ? 0 : $residual_amount;//压在平台的总金额,小于0取0
		 $in_data['check_time'] 	 	  	= date('Y-m-d H:i:s');//核对时间，即记录创建时间
		 $in_data['is_update_fee']		  	= 0;//是否已经更新erp_orders表佣金，0-未更新，1-已更新
		 $in_data['is_update_shipment_cost']= 0;//是否已更新erp_orders表物流成本，0-未更新，1-已更新
		 $in_data['erp_total']				= $erp_orders_total;//ERP订单总金额
		 $in_data['erp_fee']				= $erp_plat_total;//ERP订单总平台佣金
		 $in_data['orders_type']			= $r['orders_type'];//订单类型
		 $in_data['sales_account']			= $r['sales_account'];//账号
		 $in_data['type']					= $type;//核对类型
		 $in_data['mouth_num']				= $timeArr[1];//订单发货月份
		 $in_data['year_num']				= $timeArr[0];//订单发货年份
		 
		 //1是正常，2是平台总订单金额不等于erp订单总金额，3是平台总费用高出erp平台费20%，4是找不到该订单的收款信息
		 $in_data['data_status'] 			= 1;//核对结果状态
		 
		 //如果平台总订单金额不等于erp订单总金额
		 if($order_receiveableArr[0]['orders_total'] != $erp_orders_total){//平台总订单金额和erp订单总金额不一致
		   $in_data['data_status']  = 2;
		 }
		 
		 //平台总费用比erp平台费高出20%，视为异常
		 $rate = 0;//平台总费用高出erp平台费的比率
		 $rate = ($in_data['platform_total_fee']-$r['platFeeTotal'])/$r['platFeeTotal'];
		 
		 if($rate>0.2){
		   $in_data['data_status']  = 3;
		 }

		 $in_id = $this->order_check_result_model->add($in_data);

		 //更新erp_order_check_status表的is_check和check_status字段
		 foreach($orderArr as $os){
		     $up_data = array();
			 $options = array();
			 $up_data['is_check'] = 1;//是否已经核对过，0-否，1是
			 if($in_data['data_status']>1){
			   $up_data['check_status'] = 2;//核对状态，1-正常，2-异常
			 }else{
			 	$up_data['check_status'] = 1;//核对状态，1-正常，2-异常
			 }
			 $options['where'] = array('erp_orders_id'=>$os['erp_orders_id']);
			 $up_id = $this->order_check_status_model->update($up_data,$options);
		 	
		 }
		 
	     //更新erp_orders表中的平台费
//		 $u = array();
//		 $opt = array();
//		 $u['platFeeTotal'] = $in_data['platform_total_fee'];
//		 $opt['where'] = array('buyer_id'=>$r['buyer_id']);
//		 $u_id = $this->orders_model->update($u,$opt);//不应该加入事务判断，如果平台费一致的话更新条数为0
		 
		 if($in_id>0 && $up_id>0 && $this->db->trans_status() === TRUE){
		 	 echo '<span style="color:green;">'.$r['erp_orders_id'].'的订单号核对成功</span><br/>';
		     $this->db->trans_commit();//事务结束
		 }else{
		 	 echo '<span style="color:red;">'.$r['erp_orders_id'].'的订单号核对失败</span><br/>';
		     $this->db->trans_rollback();
		 }

	  }
	  
	}
	
	
	/**
	 * 根据某个字段获取某个表下的记录
	 * 第一个参数是字段名
	 * 第二个参数是表的模型名
	 * 第三个参数是字段值
	 * 如果是orders_receivable_detail_model模型直接更新该表status=2
	 */
	
	function getInfoByfiled($filed,$model,$value){
	  $option = array();
	  $where[$filed] = $value;
	  if($model=='orders_receivable_detail_model'){//只查找status=1的数据，1-未核对，2-已核对
	    $where['status'] = 1;
	  }
	  if($model=='orders_model'){
	   $where['orders_is_join'] = 0;
	  }
	  $option['where'] = $where;
	  $da = $this->$model->getAll2array($option);
	  
	  return $da;
	}
	
}