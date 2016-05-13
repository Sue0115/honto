<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//全部订单管理
class orderManage extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'sharepage','order/orders_model','sangelfine_warehouse_model',
								'order/order_type_model','shipment/shipment_model','slme_user_model',
								'country_model','order/orders_products_model','operate_log_model',
								'send_orders_message_log_model',
								)
							);
		$this->model = $this->orders_model;
		
	}
	
	function index(){
		
	  $string = '';
		
	  $per_page	= (int)$this->input->get_post('per_page');
		
	  $cupage	= 20; //每页显示个数
	 
	  $like		= array();
	 
	  $return_arr = array ('total_rows' => true );	
		
	  //搜索
	  $search_data = $this->input->get_post('search');
	  
	  //初始化
	  $orderID='';
	  $buyer_id='';
	  $seller_account='';
	  $buyer_name='';
	  $orders_status='';
	  $warehouse='';
	  $isBackOrder='';
	  $pay_id='';
	  $orders_type='';
	  $buyer_address='';
	  $showType='';
	  //根据内单号查找数据
	  if(isset($search_data['erp_orders_id']) && $orderID = trim($search_data['erp_orders_id'])){
			$where['erp_orders_id'] = $orderID;
			$string .= '&search[erp_orders_id]='.$orderID;
	  }
	  //根据买家ID查找数据
	  if(isset($search_data['buyer_id']) && $buyer_id = trim($search_data['buyer_id'])){
			$where['buyer_id'] = $buyer_id;
			$string .= '&search[buyer_id]='.$buyer_id;
	  }
	  //根据销售账号查找数据
	 if(isset($search_data['seller_account']) && $seller_account = trim($search_data['seller_account'])){
			$where['sales_account'] = $seller_account;
			$string .= '&search[seller_account]='.$seller_account;
	  }
	  //根据收货人查找数据
	  if(isset($search_data['buyer_name']) && $buyer_name = trim($search_data['buyer_name'])){
	 		$where['buyer_name'] = $buyer_name;
			$string .= '&search[buyer_name]='.$buyer_name;
	  }
	  //根据订单状态查找数据
	  if(isset($search_data['orders_status']) && $orders_status = trim($search_data['orders_status'])){
	 		$where['orders_status'] = $orders_status;
			$string .= '&search[orders_status]='.$orders_status;
	  }
	  //根据订单所属仓库查找数据
	  if(isset($search_data['warehouse']) && $warehouse = trim($search_data['warehouse'])){
	 		$where['orders_warehouse_id'] = $warehouse;
			$string .= '&search[warehouse]='.$warehouse;
	  }
	  //根据订单是否欠货查找数据
	  if(isset($search_data['isBackOrder']) && $isBackOrder = trim($search_data['isBackOrder'])){
	 		$where['orders_is_backorder'] = $isBackOrder;
			$string .= '&search[isBackOrder]='.$isBackOrder;
	  }
	  //根据订单交易号查找数据
	  if(isset($search_data['pay_id']) && $pay_id = trim($search_data['pay_id'])){
	 		$where['pay_id'] = $pay_id;
			$string .= '&search[pay_id]='.$pay_id;
	  }
	  //根据订单的平台类型查找数据
	  if(isset($search_data['orders_type']) && $orders_type = trim($search_data['orders_type'])){
	  		$where['orders_type'] = $orders_type;
			$string .= '&search[orders_type]='.$orders_type;
	  }
	 //根据显示类型查找数据
	  if(isset($search_data['showType']) && $showType = trim($search_data['showType'])){
			$string .= '&search[showType]='.$showType;
	  }
	  //根据收货地址查找数据
	  if(isset($search_data['buyer_address']) && $buyer_address = trim($search_data['buyer_address'])){
	 		$like['buyer_address_1'] = $buyer_address;
			$string .= '&search[buyer_address]='.$buyer_address;
	  }
	  
	  //根据订单状态查找数据
	  
	  $search_data['erp_orders_id'] = $orderID;
	  $search_data['buyer_id'] = $buyer_id;
	  $search_data['seller_account'] = $seller_account;
	  $search_data['buyer_name'] = $buyer_name;
	  $search_data['orders_status'] = $orders_status;
	  $search_data['warehouse'] = $warehouse;
	  $search_data['isBackOrder'] = $isBackOrder;
	  $search_data['pay_id'] = $pay_id;
	  $search_data['buyer_address'] = $buyer_address;
	  $search_data['orders_type'] = $orders_type;
	  $search_data['showType'] = $showType;
      //订单状态关联数组
      $order_status_array=array(
          '1' => '新录入',
	      '2' => '不通过',
	      '3' => '已通过',
	      '4' => '已打印',
	      '5' => '已发货',
	      '6' => '已撤单',
	      '7' => '未付款',
	      '8' => '已发货[FBA]',
	      '9' => '预打印',
      );
      //订单类型,并处理数组
      $order_type=$this->order_type_model->get_all_used_order_type(array(),true);
      $newOrderType=array();
	  foreach($order_type as $v){
	    $newOrderType[$v['id']]=$v['name'];
	  }

      
     //查询所有的仓库信息并且组装仓库数组
	 $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();
	 foreach($warehouse as $va){
		$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
	 }
	 
	 //查询物流信息,并重新组装数据
	 $shipment=$this->shipment_model->getAllShipment();
	 $shipmentArr=array();
	 foreach($shipment as $val){
	   $shipmentArr[$val['shipmentID']]=$val['shipmentTitle'];
	 }
	
		
	 $where['orders_is_join']=0;
	 if(!isset($where['orders_is_backorder'])){
	   $where['orders_is_backorder']=0;
	 }
	 
	 $option=array(
	   'page'		=> $cupage,
	   'per_page'	=> $per_page,
	   'where' 		=> $where,
	   'order'		=> 'orders_export_time desc',
	   'like'		=> $like,
	 );
	 
	 $data_list=$this->model->getAll($option,$return_arr,true);
	 
	  //查找对应订单的客服名
	   $op=array();
	   $op['select']=array('nickname','id');
	   $name=$this->slme_user_model->getAll2array($op);
	   $nameArr=array();
	   foreach($name as $value){
	     $nameArr[$value['id']]=$value['nickname'];
	   }
	   
	   //查找订单中对应国家英文名的中文名
	   $opt=array();
	   $opt['select']=array('display_name','country_cn','country_en');
	   $displayName=$this->country_model->getAll2array($opt);
	   $newDisplayName=array();
	   foreach($displayName as $valuee){
	     $newDisplayName[$valuee['country_en']]=$valuee;
	   }
	   
	 $newDataList=array();
	 //组装order表中的数据
	 foreach($data_list as $ke => $valu){
	 	$skuArr=array();
	 	$totalWeight=0;
	 		 	
	 	$newDataList[$ke]=$valu;
	    
	 	//处理销售账号
	    $sales_account=$valu['token_id'] ? accounterFormat($valu['sales_account']) . '(' . $valu['token_id'] . ')' : $valu['sales_account'];
	    $newDataList[$ke]['seller_name']=$sales_account;
	    
	    //处理运费
	    $ship_fee=$valu['orders_ship_fee']? $valu['orders_total'].'('.$valu['orders_ship_fee'].')' : $valu['orders_total'];
	    $newDataList[$ke]['orderTotalFee']=$ship_fee;
	    
	    //处理订单sku数据
	    $productInfo=$this->orders_products_model->getProductSkuByOrderId($valu['erp_orders_id'],$valu['orders_warehouse_id']);
	    foreach($productInfo as $k => $p){
	      $tr='';
	      //所有sku的总重量
	      $totalWeight+=$p['products_weight'];
	      
	      //判断产品sku状态defineProdcutsDataStatus();
	     $statusArr=defineProdcutsDataStatus();
	     $productInfo[$k]['status']=$statusArr[$p['products_status_2']]['text'];
	      
	      //sku详情表格中的第一列数据
	      if($valu['orders_type']==1){
	        $productInfo[$k]['skuFrist']='ebayID:'.$valu['ebay_orders_id'].'<br/>';
	      }elseif($valu['orders_type']==6){
	      	$productInfo[$k]['skuFrist']='Buyer ID:'.$valu['buyer_id'].'<br/>';
	      }
	      $productInfo[$k]['skuFrist'].=($p['orders_item_number'] != 0 ? $p['orders_item_number'] : '');
	      $productInfo[$k]['skuFrist'].=((trim($p['transactionID']) != '0') ? '<br/><span style="font-size:10px; color:grey;">'.$p['transactionID'].'</span>' : '' );
	      $productInfo[$k]['skuFrist'].=((trim($p['itemSite']) != '0') ? '<br/><span style="font-size:10px; color:#333;">站点:'.$p['itemSite'].'</span>' : '' );
	    
	      //sku详情表中第三列数据显示产品中文名称，如果产品中文名称==sku名，显示sku名
	      if($p['orders_sku']==$p['products_name_cn']){
	      	$productInfo[$k]['skuThird']=$p['orders_item'];
	      }else{
	        $productInfo[$k]['skuThird']=$p['products_name_cn'];
	      }
	      
	      //sku详情表中第四列数据，如果sku 的价值小于建议的价值，则在sku价值后面显示建议的价值
	      if(((int)($p['item_price']*1000)) < ((int)($p['price_suggest']*1000))){
	        $productInfo[$k]['skuFour']=$valu['currency_type'].' '.$p['item_price'].'('.$p['price_suggest'].')';
	      }else{
	        $productInfo[$k]['skuFour']=$valu['currency_type'].' '.$p['item_price'];
	      }
	      
	      //sku详情表中第五列数据，如果订单产品表中token_id>0,可以取消订单
	      if($p['token_id']>0){
	        $productInfo[$k]['skuFive']='消';
	      }else{
	        $productInfo[$k]['skuFive']='';
	      }
	      
	      //判断是否有产品属性（客户选择）
	     if ($p['item_attribute'] != '') {									
            $unseItem_attribute = unserialize ( $p['item_attribute'] );
            $attributearray = '';
            foreach ( $unseItem_attribute as $k => $att ) {
             $attributearray .= '<font color="blue">'.$k.'</font>' . ':' . $att . ' ';
            }
            $tr.='<tr><td colspan="6">客户选择:'.$attributearray.'</td></tr>';
	     }else{
	        $tr='';
	     }
	    }
		$newDataList[$ke]['customer_select']=$tr;
	    $newDataList[$ke]['productInfo']=$productInfo;
	    $newDataList[$ke]['totalWeight']=$totalWeight;
	    $newDataList[$ke]['skuNum']=count($productInfo);

	   //根据订单中英文国家的名称获取订单所需的国家信息
	   $orderCoun=$newDisplayName[$valu['buyer_country']];
	   if($orderCoun['display_name']==$orderCoun['country_en']){
	       $addressCountry=$orderCoun['country_en'].' '.$orderCoun['country_cn'];
	   }else{
	       $addressCountry=$orderCoun['country_en'].' '.$orderCoun['display_name'].' '.$orderCoun['country_cn'];
	   }
	   $newDataList[$ke]['addressCountry']=$addressCountry;
	   
	   //处理ebay订单的buyer_id
	   if($valu['orders_type']==1){
	   	 $options['where']=array('orders_id'=>$valu['erp_orders_id']);
	   	 $messageArr=$this->send_orders_message_log_model->getAll2array($options);
		 $messageCount=count($messageArr);
	     $newDataList[$ke]['buyer_id']=$messageCount==0 ? $valu['buyer_id'] : $valu['buyer_id'].'<font color="#CC0000">('.$messageCount.')</font>';
	   }
	 }

	 $url = admin_base_url('order/orderManage?').$string;
		
	 $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );

      $returnData=array(
        'order_status' => $order_status_array,//订单的状态
      	'order_type'   => $newOrderType,//组合后的订单类型数组
        'warehouse'    => $warehouseArr,//仓库
        'data_list'	   => $newDataList,//重组后的订单数组
      	'nameArr'	   => $nameArr,//对应的客服数组
		'page'		   => $page,	
      	'shipemntInfo' => $shipmentArr,
      	'search'	   => $search_data,
      );
	  $this->_template('admin/order/allOrderManage',$returnData);
	}
	
	//订单管理操作日志
	function operate_log(){
		
	  $id	  =	intval($this->input->get_post('id'));
	  
	  $uid    =$this->user_info->id;
	  
	  $mod	  =$this->input->get_post('operateMod');
	  
	  
	  $per_page	= (int)$this->input->get_post('per_page');
		
	  $cupage	= config_item('site_page_num'); //每页显示个数
		
	  $return_arr = array ('total_rows' => true );
	  
	  $where  =array(
	  	'operateDisable'=>0,
	  	'operateMod'=>$mod,
	  	'operateKey'=>$id,
	    'operateID >'=>0,
	  );
	  
	  $string='/operate_log?operateMod=ordersManage&id='.$id;
	  

	  $options=array(
	   'page'		=> $cupage,
	   'per_page'	=> $per_page,
	   'where'		=>$where,
	   'order'      => 'operateTime desc',
	  );
	  
	  $logList=$this->operate_log_model->getAll($options,$return_arr);
	  
	  //获取用户数组
	  $userList=$this->db->query('select id,user_name from erp_slme_user');
	  $userResultList=$userList->result_array();

	  foreach($userResultList as $val){
	   $userArray[$val['id']]=$val['user_name'];
	  }

	  $url = admin_base_url('shipment/orderManage').$string;
	
	  $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
	  
	  $data=array(
	    'logList'=>$logList,
	    'page'   =>$page,
	    'userArray'=>$userArray,
	  );
	  $this->template('admin/order/order_operate_log',$data);
	}
	
	public function autoCanceledOrders(){
		
		$this->load->model(array('orders_auto_canceled'));
		
		$per_page	= (int)$this->input->get_post('per_page');
	  	$cupage	= config_item('site_page_num'); //每页显示个数
	  	
	  	$search	= $this->input->get_post('search');
	 
		 //订单类型,并处理数组
	    $order_type=$this->order_type_model->get_all_used_order_type(array(),true);
	    $newOrderType=array();
		foreach($order_type as $v){
		  $newOrderType[$v['id']]=$v['name'];
		}
	  
		$where = array();
		
		$query_string    = array();
		
		if($search['sales_account']){
			$where['sales_account'] = $search['sales_account'];
			$query_string['search[sales_account]'] = $search['sales_account'];
		}
		
		if($search['orders_type']){
			$where['orders_type'] = $search['orders_type'];
			$query_string['search[orders_type]'] = $search['orders_type'];
		}
		
		if($search['orderID']){
			$where['erp_orders_id'] = $search['orderID'];
			$query_string['search[erp_orders_id]'] = $search['orderID'];
		}
		
		if($search['start_date']){
			$where['add_time >='] = $search['start_date'];
			$query_string['search[start_date]'] = $search['start_date'];
		}
		
		if($search['end_date']){
			$where['add_time <='] = $search['end_date'];
			$query_string['search[end_date]'] = $search['end_date'];
		} else {
			$where['add_time <='] = date('Y-m-d H:i:s');
			$query_string['search[end_date]'] = date('Y-m-d H:i:s');
		}
		
	  	$return_arr = array ('total_rows' => true );
	  	$options	= array(
				'page'		=> $cupage,
				'per_page'	=> $per_page,
				'where'		=> $where,
				'order'		=> 'add_time desc',
			);
			
		$lc_list = $this->orders_auto_canceled->getAll($options, $return_arr); //查询所有信息
		
		$url = admin_base_url('order/orderManage/autoCanceledOrders?').http_build_query($query_string);
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		$data['result'] = $lc_list;
		$data['page'] = $page;
		$data['search'] = $search;
		$data['orders_type'] = $newOrderType;
		
		$this->_template('admin/order/autoCanceledOrders',$data);
		
	}

}