<?php
date_default_timezone_set('PRC');
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class auto_wish_split_order extends MY_Controller{

	
    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array(
                'wishreturn_error_record_model','order/orders_model','operate_log_model',
        		'order/orders_products_model','shipment/shipment_model'
          )
        );
       $this->model = $this->wishreturn_error_record_model;

    }
    
    /**
     * 从wish退款错误表中获取type=2和未拆单的is_split=1 的订单用来拆单
     * @param $per_page
     * @param $account
     */
    public function wish_split_order_data(){

      $option = array();
      $where = array();
      $where['type'] = 2;
      $where['is_split'] = 1;

      $option = array(
        'where'     => $where
      );
      $data = $this->model->getAll2array($option);

      $orderArr = array();
      //以订单号为键名重组数组，判断数组的个数与erp订单的sku是否一致，相同的话是直接更改订单状态，不用拆单
      //不一致才要拆单，只有orders_status!=5和orders_status!=6才能拆
      foreach($data as $d){
        $orderArr[$d['erp_orders_id']][] = $d;
      }

      //开始拆单
      foreach($orderArr as $key => $o){

      	$uid = 30;
      	
      	$wishIDArr = array();//存放原始订单wishID
      	
      	//根据订单号获取订单信息
      	$ordersInfo = $this->orders_model->get_orders_info($key);
      	
      	$wishIDArr = explode('+',$ordersInfo['buyer_id']);
      	
        //根据订单号获取订单产品信息
        $order_products_info = $this->orders_products_model->get_product_by_order_id($key);

      	
        //根据物流id获取物流信息
        $shipmentInfo = $this->shipment_model->getInfoById($ordersInfo['shipmentAutoMatched']);
        
        $shipmentArr = array(192,8,1);//不清空挂号码的物流id
        
        $clean_shipment = true; 
        
        //以sku为键名重组数组
        $productArr = array();
        foreach($order_products_info as $pI){
          $productArr[$pI['orders_sku']] = $pI;
        }

        //判断订单是否可以拆分，不通过的，已发货，已撤单的不拆分
      	if($ordersInfo['orders_status']==5 || $ordersInfo['orders_status']==6 || $ordersInfo['orders_status']==2){
      	  echo '<span style="color:red;">'.$key.'的订单状态不可拆分</span><br/>';
      	  $sql_q = "update erp_wishreturn_error_record set is_split=2,updateTime='".date('Y-m-d H:i:s')."',remark='订单状态不可拆分' where erp_orders_id={$key}";
	      $this->db->query($sql_q);
      	  continue;
      	}
      	
       //判断订单是否已经拆分
      	if($ordersInfo['orders_is_join']==8){
      	  echo '<span style="color:red;">'.$key.'的订单已经拆分过了</span><br/>';
      	  $sql_q = "update erp_wishreturn_error_record set is_split=2,updateTime='".date('Y-m-d H:i:s')."',remark='订单已经拆分过了' where erp_orders_id={$key}";
	      $this->db->query($sql_q);
      	  continue;
      	}
      	
      	if(in_array($ordersInfo['shipmentAutoMatched'],$shipmentArr)){
      	  $clean_shipment = false;
      	}
      	
      	//判断退款的sku个数与erp订单的sku个数是否一致，一致的话直接改成不通过，并且添加日志
      	if(count($o)==count($order_products_info)){
      		
      	     //1、更改订单状态为不通过，并添加备注
			 $sqls = "update erp_orders set orders_status=2 where erp_orders_id='".$ordersInfo['erp_orders_id']."'";
			 $this->db->query($sqls);
			 
			 //2、添加订单日志
			 $sql_in = "insert into erp_operate_log (operateUser,operateType,operateMod,operateKey,operateText,usetype)
			 			 values ('".$uid."','insert','ordersManage','".$key."','".$key."订单的状态改为不通过并添加备注','')";
			 $this->db->query($sql_in);
			 
			 //3、更改状态为不用再拆单
			 $sql_q = "update erp_wishreturn_error_record set is_split=2,updateTime='".date('Y-m-d H:i:s')."' where erp_orders_id={$key}";
	         $this->db->query($sql_q);
			 
			 continue;
			 
      	}
      	
      	$this->db->trans_begin();
      	
      	//判断退款的sku个数与erp订单的sku个数是否一致，不一致，执行拆单操作
      	//获取订单表的字段数，除了订单号，其它字段保留
      	$gettable  = mysql_query( "SELECT * FROM erp_orders limit 1" );
		$numfields = mysql_num_fields( $gettable );
		$myfields = '';
		for ( $xx = 1; $xx < $numfields; $xx++ ) {
			 $myfields .= ','.mysql_field_name( $gettable, $xx );
		}
		$myfields = substr($myfields,1);

		//新增两个订单，一个订单是不退款，一个订单是退款的,
		mysql_query( "insert into erp_orders (" . $myfields . ") select " . $myfields . " from erp_orders where erp_orders_id=" .$key );
		$tID = mysql_insert_id();//退款订单的订单号
		
		mysql_query( "insert into erp_orders (" . $myfields . ") select " . $myfields . " from erp_orders where erp_orders_id=" .$key );
		$nID = mysql_insert_id();//正常要发货订单的订单号
		
		$newIDArr = array($tID,$nID);

      	//根据新的订单号往erp_orders_products表中插入订单产品数据
      	//退款的订单产品
      	$t_flag = false;//退款数据是否能插入orders_products的标志
      	$t_count = 0;
      	$t_ordersTotal = 0;//退款总金额
      	$t_isMixed = 0;//是否混合单
      	$t_shipping_cost = 0;//退款的运费
      	$t_wish_orderID = '';//退款的wishID
      	if(count($o)>1){
      	  $t_isMixed=1;
      	}
      	
      	$opS_flag = true;

      	foreach($o as $op){
      	  //会出现退款订单号相同但是退款sku不一致的情况，出现这这情况
      	  if(!isset($productArr[$op['sku']])){
      	     $opS_flag = false;
      	     break;
      	  }
      	  $op_data = array();
          $op_data['erp_orders_id'] 	 = $tID;
          $op_data['ebay_orders_id'] 	 = $productArr[$op['sku']]['ebay_orders_id'];
          $op_data['orderlineitemid'] 	 = ($productArr[$op['sku']]['orderlineitemid']==''? 0 : $productArr[$op['sku']]['orderlineitemid']);
          $op_data['orders_item_number'] = ($productArr[$op['sku']]['orders_item_number']==''? 0 : $productArr[$op['sku']]['orders_item_number']);
          $op_data['transactionID'] 	 = $productArr[$op['sku']]['transactionID'];
          $op_data['token_id'] 			 = $productArr[$op['sku']]['token_id'];
          $op_data['item_cost'] 		 = $productArr[$op['sku']]['item_cost'];
          $op_data['orders_sku'] 		 = $productArr[$op['sku']]['orders_sku'];
          $op_data['orders_item'] 		 = $productArr[$op['sku']]['orders_item'];
          $op_data['item_price']		 = $productArr[$op['sku']]['item_price'];
          $op_data['item_count']		 = $productArr[$op['sku']]['item_count'];
          $op_data['comment_text']		 = $productArr[$op['sku']]['comment_text'];
          $t_id = $this->orders_products_model->add($op_data);
          if($t_id>0){
            $t_count+=1;
          }
          $t_ordersTotal+=$productArr[$op['sku']]['item_price']*$productArr[$op['sku']]['item_count'];
          $t_shipping_cost+=$op['shipping_cost'];
          $t_wish_orderID.='+'.$op['wish_orderID'];
          $array_key = array_search($op['wish_orderID'], $wishIDArr);//获取该wishID的键名
          unset($productArr[$op['sku']]);
          unset($wishIDArr[$array_key]);
     	}

        //判断订单是否已经拆分
      	if($opS_flag===false){
      	  echo '<span style="color:red;">'.$key.'的订单拆分的sku对应不上</span><br/>';
      	  $sql_q = "update erp_wishreturn_error_record set is_split=2,updateTime='".date('Y-m-d H:i:s')."',remark='订单拆分的sku对不上' where erp_orders_id={$key}";
	      $this->db->query($sql_q);
      	  continue;
      	}
     	
      	if($t_count==count($o)){
      	  $t_flag=true;
      	}
      	
      	//插入正常发货的订单产品数据，去掉退款的产品，剩下的原订单产品即是正常的订单产品
      	$n_flag = false;
      	$n_count = 0;
      	$n_ordersTotal = 0;//正常的产品订单总金额
      	$n_isMixed=0;//正常订单是否是混合订单
      	$n_shipping_cost = 0;//正常订单的运费
      	$n_shipping_cost = $ordersInfo['orders_ship_fee']-($t_shipping_cost/0.85);
      	$n_wish_orderID='';
      	$n_wish_orderID=implode('+',$wishIDArr);
      	if(count($productArr)>1){
      	  $n_isMixed=1;
      	}
      	foreach($productArr as $k=>$v){
      	  $p_data = array();
      	  $p_data['erp_orders_id'] 	 	 = $nID;
          $p_data['ebay_orders_id'] 	 = $v['ebay_orders_id'];
          $p_data['orderlineitemid'] 	 = $v['orderlineitemid'];
          $p_data['orders_item_number']  = $v['orders_item_number'];
          $p_data['transactionID'] 	 	 = $v['transactionID'];
          $p_data['token_id'] 			 = $v['token_id'];
          $p_data['item_cost'] 		  	 = $v['item_cost'];
          $p_data['orders_sku'] 		 = $v['orders_sku'];
          $p_data['orders_item'] 		 = $v['orders_item'];
          $p_data['item_price']			 = $v['item_price'];
          $p_data['item_count']		 	 = $v['item_count'];
          $p_data['comment_text']		 = $v['comment_text'];
          $n_id = $this->orders_products_model->add($p_data);
      	  if($n_id>0){
            $n_count+=1;
          }
          $n_ordersTotal+=$v['item_price']*$v['item_count'];
      	}
        if($n_count==count($productArr)){
      	  $n_flag=true;
      	}

      	//存放更新子单的信息，订单总金额，是否混合单
      	$updateArr = array(
      	   'orders_total' =>array($tID=>$t_ordersTotal,$nID=>$n_ordersTotal),
      	   'isMixed'      =>array($tID=>$t_isMixed,$nID=>$n_isMixed),
      	   'orders_status'=>array($tID=>6,$nID=>1),
      	   'shipping_cost'=>array($tID=>$t_shipping_cost/0.85,$nID=>$n_shipping_cost),
      	   'wishID'		  =>array($tID=>substr($t_wish_orderID,1),$nID=>$n_wish_orderID)
      	);

      	//循环更新两个子单的信息
      	$up_son_flag = true;//更新订单表的标志
      	$up_count = 0;
      	foreach($newIDArr as $id){
      	    $update = array();
      	    $where = array();
      	    $option = array();
      	    $option['where'] = array('erp_orders_id'=>$id);
      	    
	      	$update['orders_is_split'] 			= 1;
	      	if($clean_shipment==true){//要清除挂号码
	      	  $update['orders_shipping_code']   = '';
	      	}
	      	$update['orderlineitemid']  		= $updateArr['wishID'][$id];
	      	$update['buyer_id']  				= $updateArr['wishID'][$id];
	      	$update['transactionIDNew']  		= $updateArr['wishID'][$id];
	      	$update['orders_total'] 			= $updateArr['orders_total'][$id]+$updateArr['shipping_cost'][$id];
	      	$update['currency_value'] 			= $ordersInfo['currency_value'];
	      	$update['currency_type']	    	= $ordersInfo['currency_type'];
	      	$update['orders_ship_fee'] 			= $updateArr['shipping_cost'][$id];
	      	$update['shipmentAutoMatched']  	= '0';
	      	$update['orders_warehouse_id']  	= '0';
	      	$update['matching_warehouse_time']  = '0';
	      	$update['orders_status'] 			= $updateArr['orders_status'][$id];
	      	$update['old_erp_orders_id'] 		= $key;
	      	$update['isMixed'] 					= $updateArr['isMixed'][$id];
	      	$up_id = $this->orders_model->update($update,$option);
	      	if($up_id>0){
	      	   $up_count+=1;
	      	}

	      	//插入订单日志
       		mysql_query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) 
        			 VALUES('".$uid."','update','ordersManage','$id','拆分订单，母单号：" .$key ."')");
        
      	}
      	if($up_count!=2){
      	   $up_son_flag=false;
      	}
      	
      	//更新母单的信息
      	$up_mu_flag = false;
	    if (in_array($shipmentInfo['shipmentCategoryID'], array(8,10)) && $ordersInfo['orders_shipping_code']){//会用比利时邮政打印，同时母单有挂号码
	    	$muOrder = mysql_query( "update erp_orders set orders_is_join = 8,orders_shipping_code = '' where erp_orders_id=" .$key );
	    }else {
	    	$muOrder = mysql_query( "update erp_orders set orders_is_join = 8 where erp_orders_id=" .$key);
	    }
	    if($muOrder>0){
	      $up_mu_flag = true;
	    }

	    if($this->db->trans_status() === TRUE && $tID>0 && $nID>0 && $t_flag==true && $n_flag==true && $up_son_flag==true && $up_mu_flag==true){
	       echo '<span style="color:green;">'.$key."的订单拆分成功，子单号分别为{$tID},{$nID}</span><br/>";
	       $sql_q = "update erp_wishreturn_error_record set is_split=2,updateTime='".date('Y-m-d H:i:s')."' where erp_orders_id={$key}";
	       $this->db->query($sql_q);
	       $this->db->trans_commit();//事务结束
	    }else{
	       echo '<span style="color:red;">'.$key."的订单拆分失败</span><br/>";
	       $this->db->trans_rollback();
	    }

      }
		
    }
    
    
}