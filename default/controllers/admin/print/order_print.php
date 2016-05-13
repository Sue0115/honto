<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class order_print extends MY_Controller{
	
	function __construct(){
		
		parent::__construct();
		$this->load->model(
							array(
								'print/orders_model','shipment_model','order/currency_info_model',
								'print/printing_template_model','print/postpacket_config_model',
								'shipment/orders_type_model','print/orders_products_model','country_model',
								'category_model','print/products_data_model','print/system_model',
								'mdd_country_model','eub_back_address_model','youzheng_country_model',
								'bei_jing_post_code_model','guangzhou_gekou_model','guangzhou_address_model',
							    'print/china_post_zone_model','smt_area_code_model','base_country_model',
								'russia_ping_code_model','eub_fenjian_model','shunyou_area_model','lazada_pagenumber_model','gz_gekou_model','gz_address_model','bilishi_area_model'
							)
		);
		$this->model = $this->orders_model;

	}
	
	/**
	 * 获取打印前订单、物流、模板的信息
	 * 通用方法
	 */
	public function orderPrint(){
	   $id=$this->input->get_post('id');
	   $uid=(int)$this->input->get_post('uid');
	   
	   //根据订单号或者追踪码获取订单的详细信息
	   $where['erp_orders_id']=intval($id);
	   $orwhere['orders_shipping_code']=$id;
	   $opt=array(
	     'where' => $where,
	     'or_where'=>$orwhere,
	   );
	   $orderInfo=$this->model->getOne($opt,true);
	   if(empty($orderInfo)){
	     echo '<span style="color:red;font-weight:bold;">'.$id.'该订单号不存在</span>';
	     die();
	   }
	   
	   //根据订单信息中的物流id号获取物流信息
	   $shipWhere=array(
	     'shipmentID' => $orderInfo['shipmentAutoMatched'],
	   );
	   $option=array(
	     'where' => $shipWhere,
	   );
	
	   $shipmentInfo=$this->shipment_model->getOne($option,true);

		 
	   if(empty($shipmentInfo)){
	     echo '<span style="color:red;font-weight:bold;">物流方式不存在</span>';
	     die();
	   }
	   
	   //根据物流信息中的模板id号获取模板信息,根据模板名称调用相应的面单
	    $tempWhere=array(
	       'id' => $shipmentInfo['shipment_template'],
	       'isOpen' => 1,
	    );
	    $options=array(
	       'where' => $tempWhere,
	    );
	    $templateInfo=$this->printing_template_model->getOne($options,true);
		
		if(empty($templateInfo)){
	     echo '<span style="color:red;font-weight:bold;">面单打印模板不存在</span>';
	     die();
	    }else{
	     $templateName=$templateInfo['template_url'];
	     $templateCname=$templateInfo['template_name'];
	    }

	    
		$this->load->model('order/pick_product_model');
	    $tof_pick = $this->pick_product_model->change_status_is_shiped($id);
			//更新打印次数
			if ($id){
				$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) WHERE erp_orders_id=".$id."";
				$times = $this->db->query($sql);
				if($times){
					$user = $uid;
					if(!$user){
						$user = 30;
					}
						$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$user."','update','ordersManage','". $id ."','打印".$templateInfo['template_name']."')");
			  }
			}
		if($templateName=='yanWenPostTemplateApi'){
		 $printInfo=$this->$templateName($templateCname,$shipmentInfo,$orderInfo);
		}else{
			 $printInfo='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
							<title></title>
							</head><body>';
		 $printInfo.=$this->$templateName($id,$shipmentInfo,$orderInfo);
		 $printInfo.='</body></html>';
		 header('Content-type:text/html;charset=utf8');
		}
	    
		
	    echo($printInfo);
	}
	
	/**
	 * 燕文API标签打印
	 */
	public function yanWenPostTemplateApi($templateCname,$shipmentInfo,$orderInfo){
		$this->load->library('YW56');
		$yw    = new YW56;
		//$uid = $this->user_info->id;//登录用户id
		$uid="";
		$label_size = array();
		$label_size = explode("-",$templateCname);
			
		//从erp_system系统里获取常量
		$option['select']=array('system_value_name','system_value');
		$const=$this->system_model->getAll2array($option);
		foreach($const as $v){
			$newConst[$v['system_value_name']]=$v['system_value'];
		}

		$act     = "";
		if ( $shipmentInfo['shipment_warehouse_id'] == 1000 ) {//深圳仓库
			$yw->token = $newConst['YANWEN_API_TOKEN'];
			$yw->userID = $newConst['YANWEN_API_USERID'];    
			$act = "ywlabel";   
		} elseif($shipmentInfo['shipment_warehouse_id'] == 1025) {//义乌仓库
			$yw->token = $newConst['YANWEN_API_TOKEN_YW'];
			$yw->userID = $newConst['YANWEN_API_USERID_YW'];     
			$act = "ywlabel_yw";    
		}  
		
	    $jpg_array = $yw->add_label_to_jpg( $orderInfo, $act, $label_size,$uid );

	    $str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title></title>
				<style>
				*{margin:0; padding:0;}
				body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;margin:0; padding:0;}
										td{ white-space:nowrap;}
										.PageNext{page-break-after:always; clear:both; min-height:1px; height:auto; overflow:auto; width:100%;}
										.float_box{ position: relative; width:370px; height:364px; overflow:hidden; margin:11px 3px 1px; border:1px solid black;}
				</style>
				</head>

				<body>';

	    if(!empty($jpg_array)){
	    	foreach ($jpg_array as $k => $v) {
	    		$str .='<div>
						<img src="'.site_url($v).'" style="width:370px;height:370px">
						</div>';
	    	}
	    }

		$str.='</body>
			   </html>';

		return $str;	   	
	}
	
	/**
	 * 打印lwe小包面单
	 */
	public function printLWE($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	 	
			$skuArr = array();//存放sku和数量
			$skuString='';//存放sku和数量的组合字符串
			foreach($allParamArr['productsInfo'] as $va){
	
				if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
				}else{
					$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
					$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
					$skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}
			}
	
			//把sku和数组组合成字符串
			foreach($skuArr as $v){
				$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
			}
			$skuString=substr($skuString,1);
			$allParamArr['productsInfo']['sku']=$skuString;
			
			//通过国家简码找 有可能国家简码未填写
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
			
			if (empty($query))	//如果通过国家简码找不到
			{
				//通过国家全名找 
				$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
			}
	
			if (empty($query))	//如果通过国家全名找不到
			{
				//通过国家全名找全名
				$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
			}
			
			if (empty($query))	//如果都找不到
			{
				$query = array(
						'display_name' => $allParamArr['ordersInfo']['buyer_country'],
						'country_cn'	=> ''
				);
			}
			$allParamArr['countryInfo'] = $query;
			
			$totalPrice = $allParamArr['ordersInfo']['orders_total'];
			$totalPrice = $totalPrice > 20 ? 20 : $totalPrice;
			$allParamArr['ordersInfo']['orders_total'] = $totalPrice;

			$reMsg= $orderprint->printLWETemplate($allParamArr);
		
			/*
			//更新打印次数,批量
			if ($id){
				$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
						WHERE erp_orders_id=".$id;
				$this->db->query($sql);
				$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
			}
			*/
			
			return $reMsg;
	}
	
	
	/**
	 * 打印4x俄罗斯联邮通平邮面单逻辑处理
	 */
	public function print4px($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
		
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找 
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}

		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
		
		if (empty($query))	//如果都找不到
		{
			$query = array(
					'display_name' => $allParamArr['ordersInfo']['buyer_country'],
					'country_cn'	=> ''
			);
		}
		$allParamArr['countryInfo'] = $query;
		
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
				
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
			
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;

		$reMsg= $orderprint->print4pxTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印顺友标签，逻辑处理
	 * 平邮
	 */
	 public function printshunYou($id,$shipmentInfo,$orderInfo){
		
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	 
		$total_weight = 0;
		$total_price = 0;
		$first_declarevalue = 0;//第一个产品的申报总价值
		
			$skuArr = array();//存放sku和数量
			$skuString='';//存放sku和数量的组合字符串
			foreach($allParamArr['productsInfo'] as $va){
	
				if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
				}else{
					$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
					$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
					$skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}
				$total_weight += $va['item_count']*$va['products_weight'];
				$total_price  += $va['item_count']*$va['products_declared_value'];
			}
			$allParamArr['total_weight'] = $total_weight;
			$total_price = $total_price>20 ? 20 : $total_price;
			$allParamArr['total_price'] = $total_price;
			
			$first_declarevalue = $allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_declared_value'];
			$first_declarevalue = $first_declarevalue>20 ? 20 : $first_declarevalue;
			$allParamArr['first_declarevalue'] = $first_declarevalue;
			
			//把sku和数组组合成字符串
			foreach($skuArr as $v){
				$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
			}
			$skuString=substr($skuString,1);
			$allParamArr['productsInfo']['sku']=$skuString;
			
			//通过国家简码找 有可能国家简码未填写
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
			
			if (empty($query))	//如果通过国家简码找不到
			{
				//通过国家全名找 
				$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
			}
	
			if (empty($query))	//如果通过国家全名找不到
			{
				//通过国家全名找全名
				$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
			}
			
			if (empty($query))	//如果都找不到
			{
				$query = array(
						'display_name' => $allParamArr['ordersInfo']['buyer_country'],
						'country_cn'	=> ''
				);
			}
			$allParamArr['countryInfo'] = $query;
			
			$totalPrice = $allParamArr['ordersInfo']['orders_total'];
			$totalPrice = $totalPrice > 20 ? 20 : $totalPrice;
			$allParamArr['ordersInfo']['orders_total'] = $totalPrice;
			$allParamArr['is_flag'] = ' ';
			
			$sign = '';
		    //添加签名
		    if($allParamArr['ordersInfo']['orders_warehouse_id']==1000){
		      $sign = 'szslm';
		    }elseif($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
		      $sign = 'ywslm';
		    }
		    $allParamArr['sign'] = $sign;
			
			$reMsg= $orderprint->printshunYouTemplate($allParamArr);
		
			/*
			//更新打印次数,批量
			if ($id){
				$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
						WHERE erp_orders_id=".$id;
				$this->db->query($sql);
				$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
			}
			*/
			
			return $reMsg;
		
	}
	
	/**
	 * 顺友平邮100*100，暂时针对义务仓的发货
	 */
	public function printshunYou100($id,$shipmentInfo,$orderInfo){
		
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	 
		$total_weight = 0;
		$total_price = 0;
		$first_declarevalue = 0;//第一个产品的申报总价值
		
			$skuArr = array();//存放sku和数量
			$skuString='';//存放sku和数量的组合字符串
			foreach($allParamArr['productsInfo'] as $va){
	
				if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
				}else{
					$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
					$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
					$skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}
				$total_weight += $va['item_count']*$va['products_weight'];
				$total_price  += $va['item_count']*$va['products_declared_value'];
			}
			$allParamArr['total_weight'] = $total_weight;
			$total_price = $total_price>20 ? 20 : $total_price;
			$allParamArr['total_price'] = $total_price;
			
			$first_declarevalue = $allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_declared_value'];
			$first_declarevalue = $first_declarevalue>20 ? 20 : $first_declarevalue;
			$allParamArr['first_declarevalue'] = $first_declarevalue;
			
			//把sku和数组组合成字符串
			foreach($skuArr as $v){
				$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
			}
			$skuString=substr($skuString,1);
			$allParamArr['productsInfo']['sku']=$skuString;
			
			//通过国家简码找 有可能国家简码未填写
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
			
			if (empty($query))	//如果通过国家简码找不到
			{
				//通过国家全名找 
				$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
			}
	
			if (empty($query))	//如果通过国家全名找不到
			{
				//通过国家全名找全名
				$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
			}
			
			if (empty($query))	//如果都找不到
			{
				$query = array(
						'display_name' => $allParamArr['ordersInfo']['buyer_country'],
						'country_cn'	=> '',
						'country_en'   => $allParamArr['ordersInfo']['buyer_country_code']
				);
			}
			$allParamArr['countryInfo'] = $query;
			
			//根据国家简码获取国家分区
			$areaInfo = array();
			$areaCode = '';
			if($allParamArr['countryInfo']['country_en']=='UK'){
			  $allParamArr['countryInfo']['country_en']='GB';
			}
			$areaInfo = $this->shunyou_area_model->getInfoByCode($allParamArr['countryInfo']['country_en']);
			if(!empty($areaInfo)){
			  $areaCode = $areaInfo['area_code'];
			}
			$allParamArr['areaCode'] = $areaCode;
			
			$totalPrice = $allParamArr['ordersInfo']['orders_total'];
			$totalPrice = $totalPrice > 20 ? 20 : $totalPrice;
			$allParamArr['ordersInfo']['orders_total'] = $totalPrice;
			$allParamArr['is_flag'] = ' ';
			
			$sign = '';
		    //添加签名
		    if($allParamArr['ordersInfo']['orders_warehouse_id']==1000){
		      $sign = 'szslm';
		    }elseif($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
		      $sign = 'ywslm';
		    }
		    $allParamArr['sign'] = $sign;
			
			$reMsg= $orderprint->printshunYouPingYouTemplate($allParamArr);
		
			/*
			//更新打印次数,批量
			if ($id){
				$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
						WHERE erp_orders_id=".$id;
				$this->db->query($sql);
				$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
			}
			*/
			
			return $reMsg;
	}
	
	/**
	 * 打印顺友标签，逻辑处理
	 * 挂号
	 */
	 public function printshunYouGuaHao($id,$shipmentInfo,$orderInfo){
		
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	 	
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		$total_weight = 0;
		$total_price = 0;
		$first_declarevalue = 0;//第一个产品的申报总价值
		foreach($allParamArr['productsInfo'] as $va){

			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
			$total_weight += $va['item_count']*$va['products_weight'];
			$total_price  += $va['item_count']*$va['products_declared_value'];
		}

		$allParamArr['total_weight'] = $total_weight;
		$total_price = $total_price>20 ? 20 : $total_price;
		$allParamArr['total_price'] = $total_price;
		
		$first_declarevalue = $allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_declared_value'];
		$first_declarevalue = $first_declarevalue>20 ? 20 : $first_declarevalue;
		$allParamArr['first_declarevalue'] = $first_declarevalue;
		
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
		
		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
		
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找 
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}

		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
		
		if (empty($query))	//如果都找不到
		{
			$query = array(
					'display_name' => $allParamArr['ordersInfo']['buyer_country'],
					'country_cn'	=> ''
			);
		}
		$allParamArr['countryInfo'] = $query;
		
		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		$totalPrice = $totalPrice > 20 ? 20 : $totalPrice;
		$allParamArr['ordersInfo']['orders_total'] = $totalPrice;
		$allParamArr['is_flag'] = 'R';
		
		$sign = '';
	    //添加签名
	    if($allParamArr['ordersInfo']['orders_warehouse_id']==1000){
	      $sign = 'szslm';
	    }elseif($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
	      $sign = 'ywslm';
	    }
	    $allParamArr['sign'] = $sign;

		$reMsg= $orderprint->printshunYouTemplate($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;
		
	}
	
	
	
	/**
	 * 打印顺友挂号标签，逻辑处理
	 * 100*100，暂时针对义务使用
	 */
	 public function printshunYouGuaHao100($id,$shipmentInfo,$orderInfo){
		
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	 	
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		$total_weight = 0;
		$total_price = 0;
		$first_declarevalue = 0;//第一个产品的申报总价值
		foreach($allParamArr['productsInfo'] as $va){

			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
			$total_weight += $va['item_count']*$va['products_weight'];
			$total_price  += $va['item_count']*$va['products_declared_value'];
		}

		$allParamArr['total_weight'] = $total_weight;
		$total_price = $total_price>20 ? 20 : $total_price;
		$allParamArr['total_price'] = $total_price;
		
		$first_declarevalue = $allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_declared_value'];
		$first_declarevalue = $first_declarevalue>20 ? 20 : $first_declarevalue;
		$allParamArr['first_declarevalue'] = $first_declarevalue;
		
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
		
		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
		
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找 
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}

		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
		
		if (empty($query))	//如果都找不到
		{
			$query = array(
					'display_name' => $allParamArr['ordersInfo']['buyer_country'],
					'country_cn'	=> '',
			 	    'country_en'   => $allParamArr['ordersInfo']['buyer_country_code']
			);
		}
		$allParamArr['countryInfo'] = $query;
		
		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		$totalPrice = $totalPrice > 20 ? 20 : $totalPrice;
		$allParamArr['ordersInfo']['orders_total'] = $totalPrice;
		$allParamArr['is_flag'] = 'R';
		
		//根据国家简码获取国家分区
		$areaInfo = array();
		$areaCode = '';
		if($allParamArr['countryInfo']['country_en']=='UK'){
		  $allParamArr['countryInfo']['country_en']='GB';
		}
		$areaInfo = $this->shunyou_area_model->getInfoByCode($allParamArr['countryInfo']['country_en']);
		if(!empty($areaInfo)){
		  $areaCode = $areaInfo['area_code'];
		}
		$allParamArr['areaCode'] = $areaCode;

		$sign = '';
	    //添加签名
	    if($allParamArr['ordersInfo']['orders_warehouse_id']==1000){
	      $sign = 'szslm';
	    }elseif($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
	      $sign = 'ywslm';
	    }
	    $allParamArr['sign'] = $sign;


		$reMsg= $orderprint->printshunYouGuaHaoTemplate($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;
		
	}
	
	/**
	 * 【平邮】燕文燕邮宝(俄)
	 * 逻辑处理
	 */
	public function PrintPingyouYWRussia($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
	    
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据邮编获取分区
		$AreaCodeArr = array(
		  '100000' => array(199999,1),
		  '200000' => array(299999,2),
		  '300000' => array(399999,3),
		  '400000' => array(499999,4),
		  '600000' => array(629999,4),
		  '640000' => array(641999,4),
		  '500000' => array(599999,5),
		  '630000' => array(639999,6),
		  '642000' => array(699999,6)
		);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		//根据邮编获取分区
		$areaCode = '';//分区
		foreach($AreaCodeArr as $k => $v){
			if($allParamArr['ordersInfo']['buyer_zip']>$k && $allParamArr['ordersInfo']['buyer_zip']<$v[0]){
				$areaCode = $v[1];
				break;
			}
		}
		$allParamArr['ordersInfo']['areaCode'] = $areaCode;
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}

		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuString=substr($skuString,1);
		$skuString='<span style="font-weight:bold;font-size:11px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>&nbsp;'.$skuString;
		$allParamArr['skufiles'] = $skuString;
		
		$reMsg= $orderprint->PrintPingyouYWRussiaTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印【平邮】燕文燕邮宝(俄)面单')");
		}
		*/
		
		return $reMsg;
		
	}
	
	/**
	 * 打印广州挂号小包面单逻辑处理
	 */
	public function printForNewPostXiaobao($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		foreach($allParamArr['productsInfo'] as $va){
				if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
				}else{
					$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
					$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
					$skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}
		}
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuString=substr($skuString,1);
		$skuString='<span style="font-weight:bold;font-size:11px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>&nbsp;'.$skuString;
		//根据国家中文名获取格口号
		$geKouInfo = $this->guangzhou_gekou_model->getInfoByCountryCn($allParamArr['country_cn']);
		$geKou = $geKouInfo[0]['geID'];
		//判断格口的个数，如果大于1，则需要根据邮编筛选格口
		if(count($geKouInfo)>1){
			//获取国家邮编的第一个数，如514467，获得5
			$zip = substr($allParamArr['ordersInfo']['buyer_zip'],0,1);
			if($allParamArr['country_cn']=='美国'){
				$countryGeArr['16'] = array(4,5,6,7,8,9);
				$countryGeArr['17'] = array(0,1,2,3);
			}elseif($allParamArr['country_cn']=='澳大利亚'){
				$countryGeArr['13'] = array(3,5,6,7,8);
				$countryGeArr['14'] = array(0,1,2,4,9);
			}elseif($allParamArr['country_cn']=='俄罗斯'){
				$countryGeArr['11'] = array(6);
				$countryGeArr['2'] = array(1,2,3,4,5);
			}
			//遍历该数组，找到对应邮编的格口
			foreach($countryGeArr as $gk => $val){
				//如果该邮编的第一位数字在该数组下，则格口号等于该数组的键名$gk
				if(in_array($zip,$val)){
					$geKou=$gk;
					break;
				}
			}
		}
		$allParamArr['gekou'] = $geKou;

		//先更新地址表信息
		$this->guangzhou_address_model->updateSenderInfo();
		
		//获取寄件人信息，只能获取使用次数小于50次，只限于当天
		$senderInfo =$this->guangzhou_address_model->getSenderInfo();
		if(empty($senderInfo)){
			die('<span style="font-weight:bold;color:red;">今日寄件人地址已用完，不允许打印</span>');
		}
		$allParamArr['senderInfo'] = $senderInfo;

		$totalPrice=0;//第一个产品sku的总申报价值
		$totalWeight=0;//第一个产品sku的总重量

		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		$totalPrice = $totalPrice > 30 ? 30 : $totalPrice; 
		$totalWeight = $allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		
		$allParamArr['productsInfo']['namefiles']=$allParamArr['productsInfo'][0]['products_declared_en'];
		
		$allParamArr['productsInfo']['skufiles']=$skuString;

		$allParamArr['productsInfo']['totalPrice']=$totalPrice;
		
		$allParamArr['productsInfo']['time']=date('Y-m-d');
		
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;

		$reMsg= $orderprint->printForNewPostXiaobaotTemplate($allParamArr);
		


		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;
		
	}
	
	/**
	 * 打印广州平邮小包面单逻辑处理
	 */
	public function printGuangZhouPost($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		foreach($allParamArr['productsInfo'] as $va){
				if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
				}else{
					$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
					$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
					$skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}
		}
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuString=substr($skuString,1);
		$skuString='<span style="font-weight:bold;font-size:13px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>&nbsp;'.$skuString;
		//根据国家中文名获取格口号
		$geKouInfo = $this->guangzhou_gekou_model->getInfoByCountryCn($allParamArr['country_cn']);
		$geKou = $geKouInfo[0]['geID'];
		//判断格口的个数，如果大于1，则需要根据邮编筛选格口
		if(count($geKouInfo)>1){
			//获取国家邮编的第一个数，如514467，获得5
			$zip = substr($allParamArr['ordersInfo']['buyer_zip'],0,1);
			if($allParamArr['country_cn']=='美国'){
				$countryGeArr['16'] = array(4,5,6,7,8,9);
				$countryGeArr['17'] = array(0,1,2,3);
			}elseif($allParamArr['country_cn']=='澳大利亚'){
				$countryGeArr['13'] = array(3,5,6,7,8);
				$countryGeArr['14'] = array(0,1,2,4,9);
			}elseif($allParamArr['country_cn']=='俄罗斯'){
				$countryGeArr['11'] = array(6);
				$countryGeArr['2'] = array(1,2,3,4,5);
			}
			//遍历该数组，找到对应邮编的格口
			foreach($countryGeArr as $gk => $val){
				//如果该邮编的第一位数字在该数组下，则格口号等于该数组的键名$gk
				if(in_array($zip,$val)){
					$geKou=$gk;
					break;
				}
			}
		}
		$allParamArr['gekou'] = $geKou;

		//先更新地址表信息
		$this->guangzhou_address_model->updateSenderInfo();
		
		//获取寄件人信息，只能获取使用次数小于50次，只限于当天
		$senderInfo =$this->guangzhou_address_model->getSenderInfo();
		if(empty($senderInfo)){
			die('<span style="font-weight:bold;color:red;">今日寄件人地址已用完，不允许打印</span>');
		}
		$allParamArr['senderInfo'] = $senderInfo;

		$totalPrice=0;//第一个产品sku的总申报价值
		$totalWeight=0;//第一个产品sku的总重量

		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		$totalPrice = $totalPrice > 25 ? 25 : $totalPrice; 
		$totalWeight = $allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		
		$allParamArr['productsInfo']['namefiles']=$allParamArr['productsInfo'][0]['products_declared_en'];
		
		$allParamArr['productsInfo']['skufiles']=$skuString;

		$allParamArr['productsInfo']['totalPrice']=$totalPrice;
		
		$allParamArr['productsInfo']['time']=date('Y-m-d');
		
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
		
		$reMsg= $orderprint->printGuangZhouPostTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	
	
	/**
	 * 打印DHL-GM(意大利+其他)面单
	 */
	public function printDhlGmpost($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
		
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		$totalPrice=0;//总价值
		$totalWeight=0;//总重量
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
			//$totalPrice += $va['products_declared_value']*$va['item_count'];
			$totalWeight+=$va['products_weight']*$va['item_count'];
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
		$totalPrice = $allParamArr['ordersInfo']['orders_total'];	
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].' ['.$v['location'].']';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
		$allParamArr['productsInfo']['totalPrice']=$totalPrice>20 ? 20: $totalPrice;
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
			
		//根据国家英文名称获取国家信息
		$countryInfo=$this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		
		$it_other_html = '
						<tr>
			       			<td rowspan="4" style="text-align:left; padding-left:10px;">
								<p>En cas de non remise</p>
								<p>prière de retourner à</p>
								<p><b>Postfach 2007</b></p>
								<p><b>36243 Niederaula</b></p>
								<p><b>ALLEMAGNE</b></p>
							</td>
			       		</tr>
			       		<tr>
			       			<td>
								<p><b>Deutsche Post</b></p>
							</td>
			       		</tr>
			       		<tr>
			       			<td>
								<p><b>Port payé</b></p>
								<p>60544 Frankfurt</p>
								<p>Allemagne</p>
							</td>
			       		</tr>
			       		<tr>
			       			<td>
								<p>Luftpost/Prioritaire</p>
							</td>
			       		</tr>';
		
		if($countryInfo['country_cn']=='意大利'){
		
			$it_html = '
						<tr>
			       			<td colspan="2">&nbsp</td>
			       		</tr>';
		
			$allParamArr['country_img']= $it_html . $it_other_html;
		
		}else if ($countryInfo['country_cn']=='德国')
		{
			$allParamArr['country_img']='
						<tr>
			       			<td rowspan="3" style="text-align:left; padding-left:10px;">
								<p>Wenn unzustellbar,</p>
								<p>zurück an</p>
								<p><b>Postfach 2007</b></p>
								<p><b>36243 Niederaula</b></p>
							</td>
			       		</tr>
			       		<tr>
			       			<td>
								<p><b>Deutsche Post</b></p>
							</td>
			       		</tr>
			       		<tr>
			       			<td>
								<p><b>Entgelt bezahlt</b></p>
								<p>60544 Frankfurt</p>
								<p>(2378)</p>
							</td>
			       		</tr>
						';
		}else{
		
			$other_html = '
						<tr>
			       			<td colspan="2"><p><b>PRIORITAIRE</b></p></td>
			       		</tr>';
		
			$allParamArr['country_img'] = $other_html . $it_other_html;
		}
		
		$reMsg= $orderprint->DhlGmpostTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 比利时邮政面单-100×100热敏标签
	 */
	public function printForBlsThermal($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr['ordersInfo']      = $orderInfo;//订单信息
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		//如果收件人国家为空，显示国家代码
		$cname = $allParamArr['ordersInfo']['buyer_country'] == '' ? $allParamArr['ordersInfo']['buyer_country_code'] : $allParamArr['ordersInfo']['buyer_country'];
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($cname);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//物流分类ID是8,10,20的物流并且订单状态要>=3，才能打印该比利时邮政标签
		//if($allParamArr['ordersInfo']['orders_status']>=3 && in_array($shipmentInfo['shipmentCategoryID'], array(8,10,20)))
		
		//比利时挂号码处理
		if(empty($allParamArr['ordersInfo']['orders_shipping_code'])){
			//如果该物流的分类ID是8和10，进行如下处理
		   if(in_array($shipmentInfo['shipmentCategoryID'], array(8,10))){
		      $allParamArr['ordersInfo']['orders_shipping_code']=generate_shipping_code( $allParamArr['ordersInfo']['erp_orders_id'] );
		   }else{
		     echo '<span style="color:red;font-weight:bold;">【'.$allParamArr['ordersInfo']['erp_orders_id'].'】请先执行【上传bPost获取追踪号】操作后才能打印操作!</span>';
	         die();
		   }
		}
		
		//显示订单的sku详情
		$tr = '';//存储订单详情
		$totalValue = round($allParamArr['ordersInfo']['orders_total'],1);//订单总价值
		$totalValue = $totalValue >25 ? 25 : $totalValue;
		$order_num = count($allParamArr['productsInfo']);
		$one_price = round($totalValue/$order_num,1);
		$totalValue= 0;
		$totalCount = 0;//订单内sku的总数
		foreach($allParamArr['productsInfo'] as $k => $v){
		  $skuAndLocation = '';
		  $subTotla = 0;
		  //第一列显示英文申报名称和储位
		  $skuAndLocation = $v['products_location'] ? $v['products_declared_en'].'【'.$v['products_location'].'】' : $v['products_declared_en'];
			
		  $subTotla=round($one_price/$v['item_count'],1);
		  
		   //产品总数量
		   $totalCount +=$v['item_count']; 

		      $tr.='<tr bgcolor="#FFFFFF" class="fontSize10" style="line-height:12px;">
			     <td>'.$skuAndLocation.'</td>
			     <td>'.$v['orders_sku'].'</td>
			     <td>'.$v['item_count'].'</td>
			     <td>'. $subTotla.'</td>
			     <td>'.$one_price.'</td>
			     </tr>
			  ';
	
		  

		  $totalValue += $one_price;
		  
		}
		$tr .=' 
		<tr bgcolor="#FFFFFF" class="fontSize10">
		    <td colspan="4" align="right">Total:</td>
		    <td align="center">EUR '.$totalValue.'</td>
	    </tr>
	   ';
		$allParamArr['productsInfo']['skuDetail'] = $tr;
		$allParamArr['productsInfo']['totalCount'] = $totalCount;
		$reMsg= $orderprint->printForBlsThermal($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印平邮小包')");
		}
		*/		
		return $reMsg;
		
	}
	
	/**
	 * 比利时Mini Scan-100×100热敏标签
	 */
	public function printMiniThermalScan($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr['ordersInfo']      = $orderInfo;//订单信息
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		//如果收件人国家为空，显示国家代码
		$cname = $allParamArr['ordersInfo']['buyer_country'] == '' ? $allParamArr['ordersInfo']['buyer_country_code'] : $allParamArr['ordersInfo']['buyer_country'];
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($cname);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//物流分类ID是8,10,20的物流并且订单状态要>=3，才能打印该比利时邮政标签
		//if($allParamArr['ordersInfo']['orders_status']>=3 && in_array($shipmentInfo['shipmentCategoryID'], array(8,10,20)))
		
		//比利时挂号码处理
		if(empty($allParamArr['ordersInfo']['orders_shipping_code'])){
			//如果该物流的分类ID是8和10，进行如下处理
		   if(in_array($shipmentInfo['shipmentCategoryID'], array(8,10))){
		      $allParamArr['ordersInfo']['orders_shipping_code']=generate_shipping_code( $allParamArr['ordersInfo']['erp_orders_id'] );
		   }else{
		     echo '<span style="color:red;font-weight:bold;">【'.$allParamArr['ordersInfo']['erp_orders_id'].'】请先执行【上传bPost获取追踪号】操作后才能打印操作!</span>';
	         die();
		   }
		}
		
		//显示订单的sku详情
		$tr = '';//存储订单详情
		$tr3 ='';//存储重量详情
		
		$totalValue = round($allParamArr['ordersInfo']['orders_total'],1);//订单总价值
		$totalValue = $totalValue >25 ? 25 : $totalValue;
		$order_num = count($allParamArr['productsInfo']);
		$one_price = round($totalValue/$order_num,1);
		$totalValue= 0;

		$totalCount = 0;//订单内sku的总数
		$totalWeight = 0;//订单总重量
		foreach($allParamArr['productsInfo'] as $k => $v){
		  $skuAndLocation = '';
		  $subTotla = 0;
		  $skuTotalWeight = 0;//单个sku的总重量
		  //第一列显示英文申报名称和储位
		  $skuAndLocation = $v['products_location'] ? $v['products_declared_en'].'【'.$v['products_location'].'】' : $v['products_declared_en'];
		  //单个sku的总价值
		  $subTotla=round($one_price/$v['item_count'],1);
		  //单个sku的总重量
		  $skuTotalWeight=$v['item_count']*$v['products_weight'];
		  
		   //产品总数量
		   $totalCount +=$v['item_count']; 
		   $tr.='<tr bgcolor="#FFFFFF" class="fontSize10" style="line-height:12px;">
		     <td>'.$skuAndLocation.'</td>
		     <td>'.$v['orders_sku'].'</td>
		     <td>'.$v['item_count'].'</td>
		     <td>'.$subTotla.'</td>
		     <td>'.$one_price.'</td>
		     </tr>
		  ';
		   
		   //第三张面单重量详情
		   if($k<1){
			   	$tr3.='
			    <tr height="10" class="fontSize10" style="line-height:10px;">
				    <td style="border-top:1px dashed #000;border-right:1px solid #000;">'.$skuAndLocation.'</td>
					<td style="border-top:1px dashed #000;border-right:1px solid #000;">'.$skuTotalWeight.'</td>
					<td style="border-top:1px dashed #000;">'.$v['products_declared_value'].'</td>
				</tr>
			   ';
		   }else{
			   $tr3.='
			    <tr height="10" class="fontSize10" style="line-height:10px;">
				    <td style="border-right:1px solid #000;">'.$skuAndLocation.'</td>
					<td style="border-right:1px solid #000;">'.$skuTotalWeight.'</td>
					<td style="">'.$v['products_declared_value'].'</td>
				</tr>
			   ';
		   }
		   
		   
		   $totalValue += $one_price;
		   $totalWeight +=$skuTotalWeight;
		}
		$tr .=' 
		<tr bgcolor="#FFFFFF" class="fontSize10">
		    <td colspan="4" align="right">Total:</td>
		    <td align="center">EUR '.$totalValue.'</td>
	    </tr>
	   ';
		$tr3.='
		   <tr class="fontSize10">
				<td style="border-right:1px solid #000;border-top:1px solid #000;">For commercial items only<br/><span class="fontSize10">If known, HS tariff number (4) and country of origian of goods (5)<br/>N°tarifaire du SH et pays d\'origine des marchandises (si connus)</span></td>
				<td style="border-right:1px solid #000;border-top:1px solid #000;">Total Weight Poids total(in kg) (6)</td>
				<td style="border-top:1px solid #000;">Total Value (7)Valeur totale</td>
		  </tr>
		  <tr height="10" class="fontSize10">
				<td style="border-right:1px solid #000;">&nbsp;</td>
				<td style="border-right:1px solid #000;">'.$totalWeight.'</td>
				<td>EUR '.$totalValue.'</td>
		  </tr>
		';
		$allParamArr['productsInfo']['skuDetail'] = $tr;
		$allParamArr['productsInfo']['weightDetail'] = $tr3;
		$allParamArr['productsInfo']['totalCount'] = $totalCount;

		$reMsg= $orderprint->printMiniThermalScan($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印平邮小包')");
		}
		*/		
		return $reMsg;
		
	}
		
	/**
	* 顺丰俄罗斯平邮面单模板(新)逻辑处理
	*/
	public function newShunFengRussiaTemplate($id,$shipmentInfo,$orderInfo){
		
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	   		 //数据重组
			$flag=false;//是否带电
			$allParamArr  = array();
			$allParamArr['ordersInfo']      = $orderInfo;//订单信息
			
			//订单中的产品信息
			$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
			
			$totalPrice=0;//第一个产品sku的申报价值
			$totalWeight=0;//总重量
			$skuArr=array();//存放sku和数量
			$skuString='';//存放sku和数量的组合字符串
			foreach($allParamArr['productsInfo'] as $va){
				$totalWeight+=$va['products_weight']*$va['item_count'];
				if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
				}else{
					$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
					$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
					$skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}
				//是否带电
				if($va['products_with_battery']>0){
					$flag=true;
				}
			}
			
			$totalPrice=$allParamArr['productsInfo'][0]['products_declared_value'];
			//把sku和数组组合成字符串	
			foreach($skuArr as $v){
				$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuString=substr($skuString,1);
			$skucount=1;
			$allParamArr['productsInfo']['totalPrice']=$totalPrice;
			$allParamArr['productsInfo']['currency']='USD';
			$allParamArr['productsInfo']['time']=date('Y-m-d');
			$allParamArr['productsInfo']['totalWeight']=$totalWeight;
			$allParamArr['productsInfo']['namefiles']=$allParamArr['productsInfo'][0]['products_declared_en'].'*'.$skucount;
			$allParamArr['productsInfo']['skufiles']=$skuString;

			if($flag==true){
				$allParamArr['productsInfo']['battery']='Y';
			}else{
				$allParamArr['productsInfo']['battery']='N';
			}

	    $reMsg= $orderprint->newShunFengRussiaTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印平邮小包')");
		}
		*/		
		return $reMsg;
	}
	
	
	/**
	* 顺丰俄罗斯平邮面单（新2015-04-22）
	* add in 2015-07-22
	* 
	*/
	public function printShunFengRussia($id,$shipmentInfo,$orderInfo){
		
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	   		 //数据重组
			$flag=false;//是否带电
			$allParamArr  = array();
			$allParamArr['ordersInfo']      = $orderInfo;			
			$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
			$totalPrice=0;//第一个产品sku的申报价值
			$totalWeight=0;//总重量
			$skuArr=array();//存放sku和数量
			$skuString='';//存放sku和数量的组合字符串
			foreach($allParamArr['productsInfo'] as $va){
				
				if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
					$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
				}else{
					$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
					$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
					$skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}
				//是否带电
				if($va['products_with_battery']>0){
					$flag=true;
				}
			}
			$totalWeight =$allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
			$totalPrice=$allParamArr['productsInfo'][0]['products_declared_value'];
			//把sku和数组组合成字符串	
			foreach($skuArr as $v){
				$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuString=substr($skuString,1);
			$skucount=1;
			$allParamArr['productsInfo']['totalPrice']=$totalPrice;
			$allParamArr['productsInfo']['currency']='USD';
			$allParamArr['productsInfo']['time']=date('Y-m-d');
			$allParamArr['productsInfo']['totalWeight']=$totalWeight;
			$allParamArr['productsInfo']['namefiles']=$allParamArr['productsInfo'][0]['products_declared_en'].'*'.$skucount;
			$allParamArr['productsInfo']['skufiles']=$skuString;

			if($flag==true){
				$allParamArr['productsInfo']['battery']='Y';
			}else{
				$allParamArr['productsInfo']['battery']='N';
			}
			$allParamArr['express_code'] = '';
			//根据国家简码获取流向代码信息
			$express_code = $this->russia_ping_code_model->getInfoByCountryCode($allParamArr['ordersInfo']['buyer_country_code']);
			if(!empty($express_code)){
			  $allParamArr['express_code'] = $express_code['express_code'];
			}

	    $reMsg= $orderprint->shunFengRussiaTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印平邮小包')");
		}
		*/		
		return $reMsg;
	}
	
	/**
	 * printPingyouPacketTemplateList
	 * 平邮小包面单逻辑处理
	 */
 	public function printPingyouPacketTemplateList($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
		
		//平台信息
		$ordersTypeList = $this->orders_type_model->getAll2array();
		
		$typeArray      = array();
		foreach ($ordersTypeList as $row){
			$typeArray[$row['typeID']] = $row['typeName'];
		}
		

		$allParamArr  = array();

		$productsList = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		$country=$this->country_model->getCountryByEN($orderInfo['buyer_country']);//获取国家中文名称
		
		$weightTotla=$this->orders_products_model->getOrdersTotalWeight($orderInfo['erp_orders_id']);//获取该订单下sku 的总重量

		$totalCount   = 0;
			
		//数据重组
		$allParamArr = array(		'orderId'=>$orderInfo['erp_orders_id'],//订单编号
									'shipmentInfo'=>$shipmentInfo,//物流信息数组
									'totalCount'=>$totalCount,//总数量
									'orderInfo'=>$orderInfo,
									'typeArray'=>$typeArray,
									'productsList'=>$productsList,
									'countryCn'=>$country['country_cn'],
									'weightTotal'=>$weightTotla,
							);
								
		$reMsg= $orderprint->printPingyouPacketTemplate($allParamArr);

		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印平邮小包')");
		}
		*/		
		return $reMsg;
	}
	
	/**
	 * 根据模板名称调用相应的面单
	 * 中国邮政逻辑处理（组装函数）
	 */
	public function chinaPostTemplateList($id,$shipmentInfo,$orderInfo){
	   
	   $this->load->library('OrderBuffetPrint');
	   $orderprint=new orderBuffetPrint();
	   //$uid = $this->user_info->id;//登录用户id
	   $reMsg = "";	
	   //退件地址信息
	   $backInfo       = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);	

		//打印一体化面单(按申报名称) 与 按分类  合并
		if(in_array($shipmentInfo['showPostOrder'],array(1,2))){
			
				$categoryList   = $this->category_model->getAllCategory();//分类列表
				foreach($categoryList as $va){
				  $newCategoryList[$va['category_id']]=$va;
				}
				$allParamArr   = array();
				$rs            = $this->orders_model->getOne($p['where']=array('erp_orders_id'=>$id),true);
				$rp            = $this->orders_products_model->getAll2array($p['where']=array('erp_orders_id'=>$id));
				$data          = array();
				$products_data = array();//重量申报价值等信息
				$flag          = false;  //是否换单价和总价值
				$total_weight  = 0;
				$total_value   = 0;
				$totalCount    = 0;
				$perCount	   = 0;
				$perWeight	   = 0;
				$perPrice	   = 0;
				
				foreach ($rp as $row){
					$tmp             = array();
					$tmp             = $this->products_data_model->getProductsInfoWithSku(trim($row['orders_sku']), $rs['orders_warehouse_id']);
					$data[]          = trim($row['orders_sku']).'【'.$row['item_count'].'】'.($tmp['products_location'] ? '【'.$tmp['products_location'].'】' : '');
					$totalCount     += $row['item_count'];
					$products_data[] = $tmp;
					$total_weight   += $tmp['products_weight'] * $row['item_count'];
					$total_value    += $tmp['products_declared_value'] * $row['item_count'];
				}

				//打印一体化面单(按申报名称)
				if($shipmentInfo['showPostOrder'] == 1){
					if (($total_value > 20 || count($rp) > 2)) {
						$flag            = true;
						$total_value     = $total_value > 20 ? 20 : $total_value;
						$perCount        = count($rp) > 2 ? 2 : count($rp);			
					}
					
				//按分类来
				}elseif($shipmentInfo['showPostOrder'] == 2){
					if ($total_value > 10 || count($rp) > 1) {
						$flag            = true;
						$total_value     = $total_value > 10 ? 10 : $total_value;
						$perCount        = count($rp) > 1 ? 1 : count($rp);					
					}
				}
				if($perCount>0){
				 $perPrice        = number_format($total_value/$perCount, 2);
				 $total_value     = $perPrice * $perCount;
				 $perWeight       = number_format($total_weight/$perCount, 3);
				 $total_weight    = $perWeight * $perCount;
				}
				
				
				if($products_data[0]){
					$categoryListStr = $newCategoryList[$products_data[0]['products_sort']]['category_name'].'<br/>'.$newCategoryList[$products_data[0]['products_sort']]['category_name_en'];
				}	
					
				$country=$this->country_model->getCountryByEN($orderInfo['buyer_country']);

				$countryCode=$this->country_model->getCountryCodeByEN($country['country_cn']);

				//数据重组
				$allParamArr = array(	'orderId'=>$orderInfo['erp_orders_id'],//订单编号
										'shipmentInfo'=>$shipmentInfo,//物流信息数组
										'rs'=>$rs,//订单信息数组
										'rp'=>$rp,//订单详情数组
										'backInfo'=>$backInfo,//退件地址信息数组
										'consumer_name'=>$backInfo[0]['consumer_name'],
										'consumer_from'=>$backInfo[0]['consumer_from'],
										'consumer_zip'=>$backInfo[0]['consumer_zip'],
										'consumer_phone'=>$backInfo[0]['consumer_phone'],
										'consumer_back'=>$backInfo[0]['consumer_back'],
										'sender_signature'=>$backInfo[0]['sender_signature'],
										'data'=>$data,//SKU信息
										'totalCount'=>$totalCount,//总数量
										'products_data'=>$products_data,//SKU数组
										'perWeight'=>$perWeight,//有用到
										'perPrice'=>$perPrice,//有用到
										'total_weight'=>$total_weight,//总重量
										'total_value'=>$total_value,//总价值
										'shipment_template'=>$shipmentInfo['shipment_template'],
										'categoryListStr'=>$categoryListStr,
										'country'	=> $country['country_cn'],
				 						'countryCode'=>$countryCode[0]['country_en'],
										'displayname'=>$country['display_name'],
										'flag'=>$flag,
									 );
				$reMsg = $orderprint->chinaPostTemplateOne($allParamArr);
		
		}else{
			//平台信息
			$ordersTypeList = $this->orders_type_model->getAll2array();

			$typeArray      = array();
			foreach ($ordersTypeList as $row){
				$typeArray[$row['typeID']] = $row['typeName'];
			}
			
				$allParamArr  = array();
				$productsList = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
				
				$weightTotla=$this->orders_products_model->getOrdersTotalWeight($orderInfo['erp_orders_id']);
				
				$country=$this->country_model->getCountryByEN($orderInfo['buyer_country']);
				
				
				$totalCount   = 0;
				
				//数据重组
				$allParamArr = array(	'orderId'=>$orderInfo['erp_orders_id'],//订单编号
										'shipmentInfo'=>$shipmentInfo,//物流信息数组
										'totalCount'=>$totalCount,//总数量
										'orderInfo'=>$orderInfo,
										'typeArray'=>$typeArray,
										'productsList'=>$productsList,
										'weightTotal' => $weightTotla,
										'country_cn'  => $country['country_cn'],
									 );
				
				$reMsg = $orderprint->chinaPostTemplateTow($allParamArr);

		}
	/*	//更新打印次数,批量
	 if (isset($id)&&!empty($id)){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','订单打印（热敏）')");
		}
	*/	
		
		return $reMsg;
	 }
	
	  /**
	   *  
	   	MDD香港平邮面单 逻辑处理
	   */
	  public function mddHongKongPostTemplateList($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
		
		$allParamArr  = array();
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		$country						=$this->country_model->getCountryByEN($orderInfo['buyer_country']);//获取国家中文名称
		$allParamArr['country']			=$country['country_cn'];
		
		//根据国家中文名称获取国家所在的分区
        $country_cns=$this->mdd_country_model->getRegionByCountryCn($allParamArr['country']);
		$allParamArr['region']=$country_cns['region'];
		
		$reMsg = $orderprint->mddHongKongPostTemplate($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','mdd香港平邮面单打印')");
		}
			*/	
		return $reMsg;
	}
	
	/**
	 * 贝邮宝面单逻辑处理
	 */
	public function PpbybTemplateList($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
		
		$allParamArr  = array();
		$totalCount   = 0;
		$totalWeight = 0;
		$totalValue  = 0;
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		
		//第一个产品的sku名称要组合申报英文名称
		$allParamArr['productsInfo'][0]['orders_sku']=$allParamArr['productsInfo'][0]['orders_sku'].'('.$allParamArr['productsInfo'][0]['products_declared_en'].')';
		
		//重新组合产品sku的数据
		foreach($allParamArr['productsInfo'] as $va){
			$totalCount+=$va['item_count'];
			$totalWeight+=$va['products_weight']*$va['item_count'];
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
		$totalValue = $allParamArr['ordersInfo']['orders_total'];
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuString=substr($skuString,1);
		$skuString  = strlen($skuString) > 120 ? mb_strcut($skuString, 0, 120, 'utf-8') : $skuString;
		
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
		$allParamArr['productsInfo']['totalCount']=$totalCount;
		$allParamArr['productsInfo']['totalValue']=$totalValue;
		$allParamArr['productsInfo']['sku']=$skuString;
		
		$country						=$this->country_model->getCountryByEN($orderInfo['buyer_country']);
		
		$allParamArr['country']			=$country['country_cn'];//获取国家中文名称
		$allParamArr['display_name']	=$country['display_name'] ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		$allParamArr['createTime']=date('Y-m-d');
		$reMsg = $orderprint->PpbybTemplate($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印贝邮宝面单')");
		}
			*/	
		return $reMsg;

	}
	
	/**
	 * 燕文北京平邮面单逻辑处理
	 */
	public function printYWBejiing($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
		
		$allParamArr  = array();
		$totalCount   = 0;
		$totalWeight = 0;
		$totalValue  = 0;
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//说去所有的退件地址
		$backList = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
	
		$allParamArr['backList'] = $backList[0];
		
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		
		//第一个产品的sku名称要组合申报中英文名称
		$allParamArr['productsInfo'][0]['orders_sku']=$allParamArr['productsInfo'][0]['products_declared_en'].' '.$allParamArr['productsInfo'][0]['products_declared_cn'].' '.$allParamArr['productsInfo'][0]['orders_sku'];
		
		//第一个产品的申报价值
		$totalValue = $allParamArr['productsInfo'][0]['products_declared_value'];
		
		//第一个产品的总重量
	    $totalWeight =$allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		
		//重新组合产品sku的数据
		foreach($allParamArr['productsInfo'] as $ke => $va){
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
		
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuString=substr($skuString,1);
		//$skuString  = strlen($skuString) > 120 ? mb_strcut($skuString, 0, 120, 'utf-8') : $skuString;
		
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
		$allParamArr['productsInfo']['totalValue']=$totalValue;
		$allParamArr['productsInfo']['sku']=$skuString;
		
		$country						=$this->country_model->getCountryByEN($orderInfo['buyer_country']);

		$allParamArr['country']			=$country['country_cn'];//获取国家中文名称
		
	    //根据国家中文名获取序号
      	$postInfo=$this->bei_jing_post_code_model->getInfoByCn($allParamArr['country']);
      	
      	if(empty($postInfo)){
	      		$postInfo['postArea2'] = '平1';
	      		$postInfo['enCode'] = 'B';
	      		$postInfo['postArea1'] = '序41';
	    }

      	$allParamArr['postInfo']=$postInfo;
		
		$allParamArr['display_name']	=$country['display_name'] ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		$allParamArr['createTime']=date('Y-m-d');

		//公司在燕文的客户编号
		$allParamArr['yanwen_code'] = '302035';
		if($allParamArr['ordersInfo']['orders_warehouse_id'] == '1025'){
				$allParamArr['yanwen_code'] = '209052';
		}

		$reMsg = $orderprint->printYWBejiingTemplate($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印贝邮宝面单')");
		}
			*/	
		return $reMsg;

	}
	
	/**
	 * 顺丰荷兰面单逻辑处理
	 */
	public function ShunfengPostToNLD($id,$shipmentInfo,$orderInfo){


		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
		
		$allParamArr  = array();
		$totalCount   = 0;
		$totalWeight = 0;
		$totalValue = 0;
		$count = 0;
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		
		//重新组合产品sku的数据
		foreach($allParamArr['productsInfo'] as $va){
			$totalCount+=$va['item_count'];
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				
				//如果储位为空，不显示[]符号
				if(empty($va['products_location'])){
				  $skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}else{
				  $skuArr[$va['orders_sku']]['location']='['.$va['products_location'].']';
				}
				
			}
		}
		
		//显示第一个产品的总价值
		//$totalValue =$allParamArr['productsInfo'][0]['products_declared_value']*$allParamArr['productsInfo'][0]['item_count'];
		//订单总值，不超过20美金orders_total
		$totalValue =$allParamArr['ordersInfo']['orders_total'];

		$totalValue = $totalValue > 20 ? 20 : $totalValue;
		
		//显示第一个产品的总重量
		$totalWeight =$allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			if($count>5){
			  $skuString.=',***';
			}else{
		      $skuString.=','.$v['sku'].'*'.$v['item_count'].''.$v['location'];
			}
			$count++;
		}
		$skuString=substr($skuString,1);
		
		//第一个产品的英文申报名称
		$allParamArr['productsInfo']['product_sort'] = $allParamArr['productsInfo'][0]['products_declared_en'];
		$allParamArr['productsInfo']['totalWeight'] = $totalWeight;
		$allParamArr['productsInfo']['totalValue'] = $totalValue;
		$allParamArr['productsInfo']['totalCount'] = $totalCount;
		$allParamArr['productsInfo']['sku'] = $skuString;
		$allParamArr['createTime'] = date('Y-m-d H:i:s');
		
		//如果国家简码为空，则显示国家英文名
		$allParamArr['ordersInfo']['buyer_country_code'] = $allParamArr['ordersInfo']['buyer_country_code'] ? $allParamArr['ordersInfo']['buyer_country_code'] : $allParamArr['ordersInfo']['buyer_country'];
		
		//如果国家信息栏无信息，则显示国家简码
		$allParamArr['ordersInfo']['buyer_country'] = $allParamArr['ordersInfo']['buyer_country'] ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		$mid = $allParamArr['ordersInfo']['buyer_country'];
		$sql = "SELECT display_name FROM erp_country WHERE country_en ='$mid'";
		$display_name  = $this->db->query($sql)->result_array();
		$allParamArr['ordersInfo']['buyer_country'] = isset($display_name[0]['display_name'])?$display_name[0]['display_name']:$allParamArr['ordersInfo']['buyer_country'];
		
		$reMsg = $orderprint->ShunfengPostToNLDTemplate($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印贝邮宝面单')");
		}
			*/	
		return $reMsg;

	}
	
	
	/**
	 * 顺丰欧洲小包平邮面单逻辑处理
	 */				
	public function ShunfengPostToPingYou($id,$shipmentInfo,$orderInfo){

		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
		
		$allParamArr  = array();
		$totalCount   = 0;
		$totalWeight = 0;
		$totalValue = 0;
		$count = 0;
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		
		//重新组合产品sku的数据
		foreach($allParamArr['productsInfo'] as $va){
			$totalCount+=$va['item_count'];
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				
				//如果储位为空，不显示[]符号
				if(empty($va['products_location'])){
				  $skuArr[$va['orders_sku']]['location']=$va['products_location'];
				}else{
				  $skuArr[$va['orders_sku']]['location']='['.$va['products_location'].']';
				}
				
			}
		}
		
		//显示第一个产品的总价值
		//$totalValue =$allParamArr['productsInfo'][0]['products_declared_value']*$allParamArr['productsInfo'][0]['item_count'];
		//订单总值，不超过20美金orders_total
		$totalValue =$allParamArr['ordersInfo']['orders_total'];

		$totalValue = $totalValue > 20 ? 20 : $totalValue;
		
		//显示第一个产品的总重量
		$totalWeight =$allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			if($count>5){
			  $skuString.=',***';
			}else{
		      $skuString.=','.$v['sku'].'*'.$v['item_count'].''.$v['location'];
			}
			$count++;
		}
		$skuString=substr($skuString,1);
		
		//第一个产品的英文申报名称
		$allParamArr['productsInfo']['product_sort'] = $allParamArr['productsInfo'][0]['products_declared_en'];
		$allParamArr['productsInfo']['totalWeight'] = $totalWeight;
		$allParamArr['productsInfo']['totalValue'] = $totalValue;
		$allParamArr['productsInfo']['totalCount'] = $totalCount;
		$allParamArr['productsInfo']['sku'] = $skuString;
		$allParamArr['createTime'] = date('Y-m-d H:i:s');
		
		//如果国家信息栏无信息，则显示国家简码
		$allParamArr['ordersInfo']['buyer_country'] = $allParamArr['ordersInfo']['buyer_country'] ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		$mid = $allParamArr['ordersInfo']['buyer_country'];
		$sql = "SELECT display_name FROM erp_country WHERE country_en ='$mid'";
		$display_name  = $this->db->query($sql)->result_array();
		$allParamArr['buyer_country'] = isset($display_name[0]['display_name'])?$display_name[0]['display_name']:$allParamArr['ordersInfo']['buyer_country'];
		//如果国家简码为空，则显示国家英文名
		$allParamArr['ordersInfo']['buyer_country_code'] = $allParamArr['ordersInfo']['buyer_country_code'] ? $allParamArr['ordersInfo']['buyer_country_code'] : $allParamArr['ordersInfo']['buyer_country'];
		$reMsg = $orderprint->ShunfengPostToPingYouTemplate($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印贝邮宝面单')");
		}
			*/	
		return $reMsg;

	}
	
	/**
	 * EUB-100×100热敏标签逻辑处理
	 */
	public function printForPostEubThermal($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    //$uid = $this->user_info->id;//登录用户id
	    
	    $reMsg = "";
	    
	    $allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		$totalWeight = 0;
		$totalValue = 0;
		$totalCount = 0;
		$skuArr = array();//存放sku 和数量
		$docStr  = "";//产品详细显示
		$weightStr = "";//重量显示
		$key = '';
		$itemCount = '';
		$timeVal = date("Y-m-d",time($allParamArr['ordersInfo']['orders_print_time']));//订单打印时间
		$products_declared_value_str = "";//单价
		
		foreach($allParamArr['productsInfo'] as $k => $va){
		  $totalCount += $va['item_count'];//该订单下所有sku的总数
		  //如果该订单下的产品超过3个，订单详情显示不一样
		  if($k>=1){
		    if($k>1){
		    	    $docStr  .=	"";
					$weightStr .= "";
					$products_declared_value_str .= "";
					$key .="";
					$itemCount .="";
					
			}else{
					$docStr  .=	$va['orders_sku']."_".$va['item_count']."<br>";
					$weightStr .= $va['products_weight']."<br>";
					$products_declared_value_str .= $va['products_declared_value']."<br>";
					$key .= ($k+1).'<br/>';
		 			$itemCount .= $va['item_count'].'<br/>';
		 			$totalWeight += $va['products_weight']*$va['item_count'];//该订单下所有sku的总重量
		  			$totalValue += $va['products_declared_value']*$va['item_count'];//该订单下所有sku的总价值
			}
		  }else{
		  	$docStr  .=	$va['products_declared_en']." ".$va['products_declared_cn']." ".$va['orders_sku']."_".$va['item_count']."<br>";
			$weightStr .= $va['products_weight']."<br>";
			$products_declared_value_str .= $va['products_declared_value']."<br>";
			$key .= ($k+1).'<br/>';
		    $itemCount .= $va['item_count'].'<br/>';
		    $totalWeight += $va['products_weight']*$va['item_count'];//该订单下所有sku的总重量
		  	$totalValue += $va['products_declared_value']*$va['item_count'];//该订单下所有sku的总价值
		  } 
		}
		
		//如果该订单的总价值超过30元并且该订单下有3种产品(sku)
		if ($totalValue > 30 || count($allParamArr['productsInfo']) > 3) {
			$flag = true;
			$totalValue = $totalValue > 30 ? 30 : $totalValue;
			$perCount = count($allParamArr['productsInfo']) > 3 ? 3 : count($allParamArr['productsInfo']);
			$perPrice = number_format($totalValue/$perCount, 2);
			$totalValue = $perPrice * $perCount;
			$perWeight = number_format($totalWeight/$perCount, 3);
			$totalWeight = $perWeight * $perCount;
		}
		//订单显示详情
		$allParamArr['productsInfo']['key'] = $key;
		$allParamArr['productsInfo']['itemCount'] = $itemCount;
		$allParamArr['productsInfo']['skuDetail'] = $docStr.$timeVal;
		$allParamArr['productsInfo']['weight'] = $weightStr;
		$allParamArr['productsInfo']['declared_value'] = $products_declared_value_str;
		
		//提取回邮地址信息
		$addressArr = explode('|',$shipmentInfo['backAddress']);
		if($addressArr[1]==1){//如果回邮地址类型是1，提取erp_eub_back_address表中的数据,EUB
		  $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
		  $allParamArr['senderInfo']['sender']=$backAddress['sender'];
		  $allParamArr['senderInfo']['street']=$backAddress['senderStreet'];
		  $allParamArr['senderInfo']['provinces']=$backAddress['senderCity'].' '.$backAddress['senderState'];
		  $allParamArr['senderInfo']['country']=$backAddress['senderCountry'];
		  $allParamArr['senderInfo']['countryAndPostcode']=$allParamArr['senderInfo']['country'].' '.$backAddress['senderZip'];
		  $allParamArr['senderInfo']['mobilePhone']=$backAddress['senderMobile'];
		}
	
		//地区编码和邮编的处理
		$allParamArr['buyer_zip'] = explode("-",$allParamArr['ordersInfo']['buyer_zip']);//拆分邮编
		$AreaID					= intval(substr($allParamArr['buyer_zip'][0],0,3));
		
		
		//根据邮编的前三位从erp_eub_fenjian表中获取分拣码
		$fenjianInfo = $this->eub_fenjian_model->getInfoByCode($AreaID);

		$fenjian_code = !empty($fenjianInfo) ? $fenjianInfo['code'] : '';
	
		//根据国家简码获取国家英文全称

		$countryInfo = $this->base_country_model->getCountryInfoByCode($allParamArr['ordersInfo']['buyer_country_code']);
		$allParamArr['countryAll'] = !empty($countryInfo) ?  $countryInfo['country_en'] : $allParamArr['ordersInfo']['buyer_country'];
		
//		
//		if($AreaID>=0&&$AreaID<=34){
//			$AreaID		= '1';
//		}else if ($AreaID>=35&&$AreaID<=74){
//			$AreaID		= '3';	
//		}else if ($AreaID>=75&&$AreaID<=93){
//			$AreaID		= '2';	
//		}else if ($AreaID>=94&&$AreaID<=99){
//			$AreaID		= '4';	
//		}else {
//			$AreaID		= '1';
//		}
		$allParamArr['AreaID'] = $fenjian_code;//地区编码

		
		//以下收件人地址全部大写
		$allParamArr['ordersInfo']['buyer_name']=strtoupper($allParamArr['ordersInfo']['buyer_name']);
		$allParamArr['ordersInfo']['buyer_address_1']=strtoupper($allParamArr['ordersInfo']['buyer_address_1']);
		$allParamArr['ordersInfo']['buyer_address_2']=strtoupper($allParamArr['ordersInfo']['buyer_address_2']);
		$allParamArr['ordersInfo']['buyer_city']=strtoupper($allParamArr['ordersInfo']['buyer_city']);
		$allParamArr['ordersInfo']['buyer_state']=strtoupper($allParamArr['ordersInfo']['buyer_state']);
		$allParamArr['ordersInfo']['buyer_country']=strtoupper($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['ordersInfo']['buyer_zip']=strtoupper($allParamArr['ordersInfo']['buyer_zip']);
		
		$allParamArr['productsInfo']['totalWeight'] = $totalWeight;
		$allParamArr['productsInfo']['totalValue'] = $totalValue;
		$allParamArr['productsInfo']['totalCount'] = $totalCount;

		$reMsg = $orderprint->printForPostEubThermal($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印EUB-100×100热敏标签面单')");
		}
			*/	
		return $reMsg;

	    
	}

	/**
	 * 德国邮政面单
	 */
	public function printForGermanyPost($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    //$uid = $this->user_info->id;//登录用户id
	    
	    $reMsg = "";
	    //获取该订单的信息
	    $allParamArr['ordersInfo']      = $orderInfo;			
		//获取该订单下的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		//美元对欧元的汇率
		$currency_value = $this->currency_info_model->getValueByID(7);
		
	    $totalCount = 0;//sku的总数
	    $perPrice = 0 ;//sku单价（处理后）
	    $tr = '';//存放sku详情
	    foreach($allParamArr['productsInfo'] as $va){
	    	$totalCount += $va['item_count'];
	    }
	    $perPrice = ($allParamArr['ordersInfo']['orders_total'] - $allParamArr['ordersInfo']['orders_ship_fee']) / $totalCount;
		$perPrice = $allParamArr['ordersInfo']['currency_type'] == 'EUR' ? number_format($perPrice, 2) : number_format($perPrice/$allParamArr['ordersInfo']['currency_value'] * $currency_value, 2);
		
	    foreach($allParamArr['productsInfo'] as $k => $v){
	      if($k<4){
		      $skuAndLocation = '';//存放sku和储位信息
		      $location = '';//储位
		      $location = $v['products_location'] ? '('.$v['products_location'].')' : '';
		      $skuAndLocation = $v['products_declared_en'].'('.$v['orders_sku'].')'.$location;
		      $tr .='
		        <tr align="center"><td style="white-space:normal;">'.$skuAndLocation.'</td><td>'.$v['item_count'].'</td><td>'.$perPrice.' EUR</td></tr>
		      ';
	      }
	    }
	    $allParamArr['tr'] = $tr;//sku详情
	    
	    $allParamArr['current_data'] = date('d-M-Y');//当前打印时间
	    
	    $allParamArr['totalCount'] = $totalCount;//在面单右下方显示sku总数

	    $reMsg = $orderprint->printForGermanyPost($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印德国邮政面单')");
		}
			*/	
		return $reMsg;
	}
	
	/**
	 * 打印中英专线面单
	 */
	public function printForCNToUK($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    //$uid = $this->user_info->id;//登录用户id
	    
	    $reMsg = "";
	    //获取该订单的信息
	    $allParamArr['ordersInfo']      = $orderInfo;			
		//获取该订单下的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	    
	    $total_count = 0;
	    foreach($allParamArr['productsInfo'] as $v){
	        $proTmp[] = $v['orders_sku'].'('.$v['item_count'].')'.($v['products_location'] ? '('.$v['products_location'].')' : '');
	        $total_count += $v['item_count'];
	    }
	    $allParamArr['productsInfo']['total_count'] = $total_count;
	    $allParamArr['productsInfo']['skuInfo'] = implode('<br/>', $proTmp);
		//提取回邮地址信息
		$addressArr = explode('|',$shipmentInfo['backAddress']);
		
		if($addressArr[1]==1){//如果回邮地址类型是1，提取erp_eub_back_address表中的数据
		  $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
		  $allParamArr['senderInfo']['sender']	 	= $backAddress['sender'];
		  $allParamArr['senderInfo']['street']	 	= $backAddress['senderStreet'];
		  $allParamArr['senderInfo']['provinces']	= $backAddress['senderCity'].' '.$backAddress['senderState'];
		  $allParamArr['senderInfo']['country']	 	= $backAddress['senderCountry'];
		  $allParamArr['senderInfo']['zip']		 	= $backAddress['senderZip'];
		  $allParamArr['senderInfo']['mobilePhone'] = $backAddress['senderMobile'];
		}else{//如果回邮地址类型是2，提取erp_postpacket_config表中的数据   邮政小包
		   
		  $backAddress = $this->postpacket_config_model->getInfoByID($addressArr[0]);
//		  $allParamArr['senderInfo']['sender']	 	= $backAddress['consumer_name'];
		  $allParamArr['senderInfo']['street']	 	= $backAddress['consumer_from'];
//		  $allParamArr['senderInfo']['provinces']	= '';
//		  $allParamArr['senderInfo']['country']	 	= '';
//		  $allParamArr['senderInfo']['zip']		 	= $backAddress['consumer_zip'];
//		  $allParamArr['senderInfo']['mobilePhone'] = $backAddress['consumer_phone'];
		}
		$reMsg = $orderprint->printForCNToUKTemplate($allParamArr);
		return $reMsg;
	}
	
	/**
	 * JH邮局平邮面单逻辑处理
	 */
	public function printJHPingYou($id,$shipmentInfo,$orderInfo){
		
		$this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    //$uid = $this->user_info->id;//登录用户id
	    
	    $country_fenjian  = array(
		    'RU' => 21,'US' => 22,'GB' => 23,'BR' => 24,
			'AU' => 25,'FR' => 26,'ES' => 27,'CA' => 28,
			'IL' => 29,'IT' => 30,'DE' => 31,'CL' => 32,
			'SE' => 33,'BY' => 34,'NO' => 35,'NL' => 36,
			'UA' => 37,'CH' => 38,'MX' => 39,'PL' => 40,
		);
	    
	    $reMsg = "";
	    //获取该订单的信息
	    $allParamArr['ordersInfo']      = $orderInfo;			
		//获取该订单下的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

	    $total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	      
	      if($k<1){//只显示第一个产品的sku信息
		      $trInfo .='<tr style="font-size:12px;">
		      				   <td width="70%" style="border-right:none;">'.$p['products_declared_en'].'</td>
			 		           <td width="15%" style="border-right:none;">'.$p['item_count']*$p['products_weight'].'</td>
			 		           <td width="15%">'.$p['item_count']*$p['item_price'].'</td>
			 		      </tr>';
		      $total_weight += $p['item_count']*$p['products_weight'];
	      	  $total_value  += $p['item_count']*$p['item_price'];
	      }
	      
	      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
				$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
		  }else{
				$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
				$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
				$skuArr[$p['orders_sku']]['location']=$p['products_location'];
		  }
	    }
	    
	    //把sku和数组组合成字符串	
		foreach($skuArr as $v){
		  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuInfo=substr($skuInfo,1);
		$allParamArr['skuInfo'] = $skuInfo;
		
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['total_value'] = $total_value;
	    //提取回邮地址信息
		$addressArr = explode('|',$shipmentInfo['backAddress']);
		$backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
		$allParamArr['senderInfo']['street']	 	= !empty($backAddress[0]['consumer_from']) ? $backAddress[0]['consumer_from'] : '';
		$allParamArr['senderInfo']['mobilePhone'] 	= !empty($backAddress[0]['consumer_phone']) ? $backAddress[0]['consumer_phone'] : '';

		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		$allParamArr['ordersInfo']['area'] = '';
		//根据国家中文名获取分区
		if(!empty($allParamArr['country_cn'])){
		   $country = $this->china_post_zone_model->getAreaByCn($allParamArr['country_cn']);
		   $allParamArr['ordersInfo']['area'] = $country['zone'];
		}

		$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		//获取国家分拣号
		$allParamArr['country_fenjian'] = !empty($country_fenjian[$allParamArr['country_code']]) ? $country_fenjian[$allParamArr['country_code']] : '';
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;

		$reMsg = $orderprint->printJHPingYouTemplate($allParamArr);
		
		return $reMsg;
	}
	
	
	
	/**
	 * 打印wish邮面单
	 */
	public function printwishYou($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	    
	    $us_area = array(
		    '0' => '① (USJFKA)',
			'1' => '① (USJFKA)',
			'2' => '① (USJFKA)',
			'3' => '① (USJFKA)',
			'4' => '① (USJFKA)',
			'5' => '① (USJFKA)',
			'6' => '① (USJFKA)',
			'7' => '② (USSFOA)',
			'8' => '② (USSFOA)',
			'9' => '③ (USLAXA)'
		);
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .='<tr style="font-size:12px;">
			      				   <td width="70%" style="border-right:none;">'.$p['products_declared_en'].'</td>
				 		           <td width="15%" style="border-right:none;">'.$p['item_count']*$p['products_weight'].'</td>
				 		           <td width="15%">'.$p['item_count']*$p['item_price'].'</td>
				 		      </tr>';
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
		     

			//提取回邮地址信息
			$addressArr = explode('|',$shipmentInfo['backAddress']);
			/**
			 * $addressArr[1] 1-eub 2-邮政小包回邮地址
			 * $addressArr[0] 回邮地址id
			 */
			if($addressArr[1]== '1'){
			   $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
			   $allParamArr['senderInfo']['street']	 		= $backAddress['senderStreet'];
			   $allParamArr['senderInfo']['mobilePhone'] 	= $backAddress['senderMobile'];
			   $allParamArr['senderInfo']['back_street'] 	= $backAddress['back_company'];
			}else{
			   $backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
			   $allParamArr['senderInfo']['street']	 		= !empty($backAddress[0]['consumer_from']) ? $backAddress[0]['consumer_from'] : '';
			   $allParamArr['senderInfo']['mobilePhone'] 	= !empty($backAddress[0]['consumer_phone']) ? $backAddress[0]['consumer_phone'] : '';
			   $allParamArr['senderInfo']['back_street'] 	= !empty($backAddress[0]['consumer_back']) ? $backAddress[0]['consumer_back'] : '';
			}
		  

			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
			
			
			$allParamArr['ordersInfo']['area'] = '';
			
    		//根据国家中文名获取分区
			if(!empty($allParamArr['country_cn'])){
				
				if($allParamArr['country_cn']=='俄罗斯'){
					$f_n = substr($allParamArr['ordersInfo']['buyer_zip'],0,1);
					if($f_n<5){
						$allParamArr['ordersInfo']['area'] = 'EKA小';
					}else{
						$s_n = substr($allParamArr['ordersInfo']['buyer_zip'],0,2);
					   $area_array = array(
					     'EKA大' => array('60','61','62'),
					     'OVB' => array('63','64','65','66','67'),
					   	 'VVO' => array('68','69')
					   );
					   foreach($area_array as $k => $v){
					      if(in_array($s_n,$v)){
					        $allParamArr['ordersInfo']['area'] = $k;
					        break;
					      }
					      if($k=='VVO'){
					        $allParamArr['ordersInfo']['area'] = 'MOW';
					      }
					   }
					}
				}elseif($allParamArr['country_cn']=='美国'){
				  $f_n = substr($allParamArr['ordersInfo']['buyer_zip'],0,1);
				  
				  $allParamArr['ordersInfo']['area'] = $us_area[$f_n];
				  
				}else{
				   $allParamArr['ordersInfo']['area'] = '';
				}

    	   }

		$reMsg= $orderprint->wishTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	
	/**
	 * 打印lazada印尼发货面单
	 */
	public function printLazada($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .='<tr style="font-size:12px;">
			      				   <td>'.$p['products_declared_en'].'</td>
				 		           <td>'.$p['item_count']*$p['products_weight'].'</td>
				 		           <td>'.$p['item_count']*$p['item_price'].'</td>
				 		      </tr>';
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
		     
			//根据不同的账号获取不同的名称
		    $allParamArr['shipName'] = '';
		    if(trim($allParamArr['ordersInfo']['sales_account'])=='99706454@qq.com_ID'){
		      $allParamArr['shipName'] = 'Moonastore';
		    }elseif(trim($allParamArr['ordersInfo']['sales_account'])=='lixuanpengwu@126.com_ID'){
		      $allParamArr['shipName'] = 'Makiyo';
		    }else{
		      $reMsg = "该订单不符合打印条件";
		      return $reMsg;
		    }
		    
		    //获取包裹号，pagenumber
		    $pageArr = $this->lazada_pagenumber_model->getInfoByOrderID(array('ordersID'=>$allParamArr['ordersInfo']['erp_orders_id']));
			if(empty($pageArr)){
			  $reMsg = "该订单的pagenumber不存在";
		      return $reMsg;
			}
			$allParamArr['pagenumber'] = $pageArr[0]['pagenumber'];
			
			//提取回邮地址信息
			$addressArr = explode('|',$shipmentInfo['backAddress']);
			/**
			 * $addressArr[1] 1-eub 2-邮政小包回邮地址
			 * $addressArr[0] 回邮地址id
			 */
			if($addressArr[1]== '1'){
			   $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
			   $allParamArr['senderInfo']['street']	 		= $backAddress['senderStreet'];
			   $allParamArr['senderInfo']['mobilePhone'] 	= $backAddress['senderMobile'];
			   $allParamArr['senderInfo']['sender']		 	= $backAddress['sender'];
			}else{
			   $backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
			   $allParamArr['senderInfo']['street']	 		= !empty($backAddress[0]['consumer_from']) ? $backAddress[0]['consumer_from'] : '';
			   $allParamArr['senderInfo']['mobilePhone'] 	= !empty($backAddress[0]['consumer_phone']) ? $backAddress[0]['consumer_phone'] : '';
			   $allParamArr['senderInfo']['sender'] 		= !empty($backAddress[0]['consumer_name']) ? $backAddress[0]['consumer_name'] : '';
			}
		  
			if(in_array($orderInfo['shipmentAutoMatched'],array(382,385))){//382 【专线】LWE印尼-深圳，385 【专线】LWE印尼-金华这两个渠道的面单要去掉申报价值
			   $allParamArr['declare_value'] = '';
			}else{
			  $allParamArr['declare_value'] = '<div style="font-size:11px;margin-top:0px;"><span><span><strong>Declared value:&nbsp;</strong></span></span><span>IDR&nbsp;'.$allParamArr['ordersInfo']['orders_total'].'</span></div>';
			} 

			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->lazadaTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印万邑邮面单
	 */
	public function printWanYi($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $allParamArr  = array();
	    
	    $winitProductCodeName =array( 
			'WP-HKP001'=>'万邑邮选-香港渠道（平邮）', 
			'WP-HKP002'=>'万邑邮选-香港渠道（挂号）', 
			'WP-MYP001'=>'万邑邮选-马来西亚渠道（平邮）', 
			'WP-EEP002'=>'万邑邮选-爱沙尼亚渠道（平邮）', 
			'WP-EEP001'=>'万邑邮选-爱沙尼亚渠道（挂号）', 
			'WP-SGP003'=>'万邑邮选-新加坡渠道（挂号）', 
			'WP-SGP004'=>'万邑邮选-新加坡渠道（平邮）', 
			'WP-NLP001'=>'万邑邮选-荷兰渠道（挂号）-含电', 
			'WP-NLP011'=>'万邑邮选-荷兰渠道（挂号）-不含电', 
			
			'WP-NLP002'=>'万邑邮选-荷兰渠道（平邮）-含电', 
			'WP-NLP012'=>'万邑邮选-荷兰渠道（平邮）-不含电', 
			'WP-CNP007'=>'万邑邮选-普通渠道（挂号）-北京', 
			'WP-CNP004'=>'万邑邮选-普通渠道（平邮）-北京', 
			
			'WP-SRP001'=>'万邑邮选-俄罗斯SPSR渠道（挂号）', 
			'WP-FIP001'=>'万邑邮选-芬兰渠道（挂号）', 
			'WP-DEP001'=>'万邑邮选-德国渠道（挂号）', 
			'WP-DEP002'=>'万邑邮选-德国渠道（平邮）', 
			
			'WP-CNP005'=>'万邑邮选-普通渠道（挂号）-上海', 
			'WP-CNP006'=>'万邑邮选-普通渠道（平邮）-上海', 
			'WP-HKP101'=>'万邑邮选-香港渠道（平邮）-eBay IDSE', 
			'WP-MYP101'=>'万邑邮选-马来西亚渠道（平邮）-ebay IDSE', 
			
			'WP-NLP101'=>'万邑邮选-荷兰渠道（平邮）-ebay IDSE-含电', 
			'WP-NLP102'=>'万邑邮选-荷兰渠道（平邮） -eBay IDSE-不含电', 
			'WP-DEP102'=>'万邑邮选-德国渠道（平邮香港）-ebay IDSE', 
			'WP-DEP103'=>'万邑邮选-德国渠道（平邮上海）-ebay IDSE'
		);
		
		$allParamArr['customer_code'] = array(
		  1000 => '10004110',
		  1025 => '暂无设置'
		);
	    
	    $reMsg = "";
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		if($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
		  $reMsg = "该渠道暂不支持义务仓打印";
		  return $reMsg;
		}
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		
		$allParamArr['total_decalre_value'] = '';

		$total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	    	
	    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
	      
	      if($k<1){//只显示第一个产品的sku信息
		      $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
		      $total_weight += $p['item_count']*$p['products_weight'];
	      	  $total_value  += $p['item_count']*$p['item_price'];
	      }
	      
	      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
				$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
		  }else{
				$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
				$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
				$skuArr[$p['orders_sku']]['location']=$p['products_location'];
		  }
	    }
	    
	    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
	    
	    
	    //把sku和数组组合成字符串	
		foreach($skuArr as $v){
		  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuInfo=substr($skuInfo,1);
		$allParamArr['skuInfo'] = $skuInfo;
		
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    
		$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		
	    //提取回邮地址信息
		$addressArr = explode('|',$shipmentInfo['backAddress']);
		/**
		 * $addressArr[1] 1-eub 2-邮政小包回邮地址
		 * $addressArr[0] 回邮地址id
		 */
		if($addressArr[1]== '1'){
		   $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
		   $allParamArr['senderInfo']['from']	 		= $backAddress['sender'];
		   $allParamArr['senderInfo']['street']	 		= $backAddress['senderStreet'];
		   $allParamArr['senderInfo']['mobilePhone'] 	= $backAddress['senderMobile'];
		   $allParamArr['senderInfo']['back_street'] 	= $backAddress['back_company'];
		}else{
		   $backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
		   $allParamArr['senderInfo']['from']	 		= !empty($backAddress[0]['consumer_name']) ? $backAddress[0]['consumer_name'] : '';
		   $allParamArr['senderInfo']['street']	 		= !empty($backAddress[0]['consumer_from']) ? $backAddress[0]['consumer_from'] : '';
		   $allParamArr['senderInfo']['mobilePhone'] 	= !empty($backAddress[0]['consumer_phone']) ? $backAddress[0]['consumer_phone'] : '';
		   $allParamArr['senderInfo']['back_street'] 	= !empty($backAddress[0]['consumer_back']) ? $backAddress[0]['consumer_back'] : '';
		}
		
		$TitleArr = explode(',',$shipmentInfo['yw_channel']);
		//渠道标题
		$allParamArr['shipmentTitles'] = $winitProductCodeName[$TitleArr[1]];

		$reMsg= $orderprint->printWanYiTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印新秀驿CEO平邮面单
	 */
	public function printNewCOEPY($id,$shipmentInfo,$orderInfo){
	
	    $this->load->library('OrderBuffetPrint');
	
	    $orderprint=new orderBuffetPrint();
	     
	    $reMsg = "";
	     
	    $allParamArr  = array();
	
	     
	    //订单信息
	    $allParamArr['ordersInfo']      = $orderInfo;
	    	
	    //订单中的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
	    //根据国家英文名称获取国家中文名称
	    $country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
	    $allParamArr['country_cn'] = $country_cn['country_cn'];
	
	    //如果国家为空，取国家简码
	    if(empty($allParamArr['ordersInfo']['buyer_country'])){
	        $country=$allParamArr['ordersInfo']['buyer_country_code'];
	    }else{
	        $country=$allParamArr['ordersInfo']['buyer_country'];
	    }
	    $allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
	
	
	    $allParamArr['total_decalre_value'] = '';
	   
	    $signal_weight=0;//第一个sku的总重量
	    $signal_value =0;//第一个sku的总价值
	    $total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	         
	        $allParamArr['total_decalre_value']+=$p['products_declared_value'];
	
	        if($k<1){//只显示第一个产品的sku信息
	            $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
	            $signal_weight += $p['item_count']*$p['products_weight'];
	            $signal_value  += $p['item_count']*$p['item_price'];
	        }
	        $total_weight += $p['item_count']*$p['products_weight'];
	        $total_value  += $p['item_count']*$p['item_price'];
	        
	        if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
	            $skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
	        }else{
	            $skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
	            $skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
	            $skuArr[$p['orders_sku']]['location']=$p['products_location'];
	        }
	    }
	
	    $s_value = $signal_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['signal_value'] = $s_value > 20 ? 20 : $s_value;
	    
	    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
	
	
	    //把sku和数组组合成字符串
	    foreach($skuArr as $v){
	        $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
	    }
	    $skuInfo=substr($skuInfo,1);
	    $allParamArr['skuInfo'] = $skuInfo;
	    	
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['signal_weight'] = $signal_weight;
	
	    $allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

	    $reMsg= $orderprint->printNewCOEPYTemplates($allParamArr);
	
	    /*
	     //更新打印次数,批量
	     if ($id){
	     $sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time)
	     WHERE erp_orders_id=".$id;
	     $this->db->query($sql);
	     $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
	     }
	    */
	
	    return $reMsg;
	
	}
	
	
	
	/**
	 * 打印递欧德国面单
	 */
	public function printDiOuDE($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
		    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
		    
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    
	
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->printDiOuDETemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印递欧意大利面单
	 */
	public function printDiOuIT($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
			 $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
		    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->printDiOuITTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印递欧日本面单
	 */
	public function printDiOuJP($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		     $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
		    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
	
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->printDiOuJPTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印递欧其他面单
	 */
	public function printDiOuOther($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
		    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
	
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->printDiOuOtherTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	
	/**
	 * 打印lazada泰国的面单
	 * 100*130
	 */
	public function printLazadaTh($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .='<tr style="font-size:12px;">
			      				   <td>'.$p['products_declared_en'].'</td>
				 		           <td>'.$p['item_count']*$p['products_weight'].'</td>
				 		           <td>'.$p['item_count']*$p['item_price'].'</td>
				 		      </tr>';
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
		     
			//根据不同的账号获取不同的名称
		    $allParamArr['shipName'] = '';
		    if(trim($allParamArr['ordersInfo']['sales_account'])=='99706454@qq.com_TH'){
		      $allParamArr['shipName'] = 'Moonastore';
		    }elseif(trim($allParamArr['ordersInfo']['sales_account'])=='lixuanpengwu@126.com_TH'){
				$allParamArr['shipName'] = 'Makiyo';
			}else{
		      $reMsg = "该订单不符合打印条件";
		      return $reMsg;
		    }
		    
		    //获取包裹号，pagenumber
		    $pageArr = $this->lazada_pagenumber_model->getInfoByOrderID(array('ordersID'=>$allParamArr['ordersInfo']['erp_orders_id']));
			if(empty($pageArr)){
			  $reMsg = "该订单的pagenumber不存在";
		      return $reMsg;
			}
			$allParamArr['pagenumber'] = $pageArr[0]['pagenumber'];
			
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->lazadaThTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印lazada新加坡的面单
	 * 100*100
	 */
	public function printLazadaSg($id,$shipmentInfo,$orderInfo){

	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .='<tr style="line-height: 12px; height: 44px;">
			      				   <td colspan="4" class="border_r_b_l" valign="top" width="60%">'.$p['products_declared_en'].'</td>
				 		           <td class="border_r_b" valign="top">'.$p['item_count']*$p['products_weight'].'</td>
				 		           <td class="border_r_b" valign="top">'.$p['item_count']*$p['item_price'].'('.$allParamArr['ordersInfo']['currency_type'].')</td>
				 		      </tr>';
			      
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      $total_weight += $p['item_count']*$p['products_weight'];
		      $total_value  += $p['item_count']*$p['item_price'];
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
		     
			//根据不同的账号获取不同的名称
		    $allParamArr['shipName'] = '';
		    if(trim($allParamArr['ordersInfo']['sales_account'])=='99706454@qq.com_SG'){
		      $allParamArr['shipName'] = 'Moonastore';
		    }elseif(trim($allParamArr['ordersInfo']['sales_account'])=='lixuanpengwu@126.com_SG'){
				$allParamArr['shipName'] = 'Makiyo';
			}else{
		      $reMsg = "该订单不符合打印条件";
		     // return $reMsg;
		    }
		    
		    //获取包裹号，pagenumber
		    $pageArr = $this->lazada_pagenumber_model->getInfoByOrderID(array('ordersID'=>$allParamArr['ordersInfo']['erp_orders_id']));
			if(empty($pageArr)){
			  $reMsg = "该订单的pagenumber不存在";
		      return $reMsg;
			}
			$allParamArr['pagenumber'] = $pageArr[0]['pagenumber'];
			
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->lazadaSgTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印lazada菲律宾的发货面单
	 */
	public function printLBC($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//根据不同的账号获取不同的名称
	    $allParamArr['shipName'] = '';
	    if(trim($allParamArr['ordersInfo']['sales_account'])=='99706454@qq.com_PH'){
	      $allParamArr['shipName'] = 'Moonastore';
	    }elseif(trim($allParamArr['ordersInfo']['sales_account'])=='lixuanpengwu@126.com_PH'){
	      $allParamArr['shipName'] = 'Makiyo';
	    }else{
	      $reMsg = "该订单不符合打印条件";
	      return $reMsg;
	    }
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		   
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
	
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

			//获取包裹号，pagenumber
		    $pageArr = $this->lazada_pagenumber_model->getInfoByOrderID(array('ordersID'=>$allParamArr['ordersInfo']['erp_orders_id']));
			if(empty($pageArr)){
			  $reMsg = "该订单的pagenumber不存在";
		      return $reMsg;
			}
			$allParamArr['pagenumber'] = $pageArr[0]['pagenumber'];
			
		$reMsg= $orderprint->LBCTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	
	
	/**
	 * 打印中国邮政平常小包+面单（SMT线上发货）
	 */
	public function printSmtLineShipping($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	 
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//根据国家中文名获取分区信息
		$allParamArr['country_Info'] = $this->china_post_zone_model->getAreaByCn($allParamArr['country_cn']);
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .='<tr style="font-size:12px;">
			      				   <td width="70%" style="border-right:none;">'.$p['products_declared_en'].'</td>
				 		           <td width="15%" style="border-right:none;">'.$p['item_count']*$p['products_weight'].'</td>
				 		           <td width="15%">'.$p['item_count']*$p['item_price'].'</td>
				 		      </tr>';
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
		     

			//提取回邮地址信息
			$addressArr = explode('|',$shipmentInfo['backAddress']);
			/**
			 * $addressArr[1] 1-eub 2-邮政小包回邮地址
			 * $addressArr[0] 回邮地址id
			 */
			if($addressArr[1]== '1'){
			   $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
			   $allParamArr['senderInfo']['street']	 		= $backAddress['senderStreet'];
			   $allParamArr['senderInfo']['mobilePhone'] 	= $backAddress['senderMobile'];
			   $allParamArr['senderInfo']['back_street'] 	= $backAddress['back_company'];
			}else{
			   $backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
			   $allParamArr['senderInfo']['street']	 		= !empty($backAddress[0]['consumer_from']) ? $backAddress[0]['consumer_from'] : '';
			   $allParamArr['senderInfo']['mobilePhone'] 	= !empty($backAddress[0]['consumer_phone']) ? $backAddress[0]['consumer_phone'] : '';
			   $allParamArr['senderInfo']['back_street'] 	= !empty($backAddress[0]['consumer_back']) ? $backAddress[0]['consumer_back'] : '';
			}
		  

			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
			
			$allParamArr['warehouse_flag'] = '中邮深圳仓';
			if($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
			  $allParamArr['warehouse_flag'] = '中邮金华仓';
			}
			
			//根据国家简码获取国家分区
//			$countryInfo = $this->smt_area_code_model->getArea($allParamArr['country_code']);
//		    $allParamArr['countryAreaCode'] = $countryInfo['areaCode'];

		$reMsg= $orderprint->smtLineShippingTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	/**
	 * 打印云途通用面单100*100
	 */
	public function printZhongMei($id,$shipmentInfo,$orderInfo){
		
		 $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	 
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
	
		$total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	      
	      if($k<1){//只显示第一个产品的sku信息
		      $trInfo .='<tr style="font-size:12px;">
		      				   <td width="70%" style="border-right:none;">'.$p['products_declared_en'].'</td>
			 		           <td width="15%" style="border-right:none;">'.$p['item_count']*$p['products_weight'].'</td>
			 		           <td width="15%">'.$p['item_count']*$p['item_price'].'</td>
			 		      </tr>';
		      $total_weight += $p['item_count']*$p['products_weight'];
	      	  $total_value  += $p['item_count']*$p['item_price'];
	      }
	      
	      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
				$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
		  }else{
				$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
				$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
				$skuArr[$p['orders_sku']]['location']=$p['products_location'];
		  }
	    }
	    
	    //把sku和数组组合成字符串	
		foreach($skuArr as $v){
		  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuInfo=substr($skuInfo,1);
		$allParamArr['skuInfo'] = $skuInfo;
		
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['total_value'] = $total_value;

		$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		//物流标题数组
		$shipmentTitleArr = array(
		  346 => 'EUDDP',
		  397 => '中美独轮车专线',
		  417 => 'EUZXSW',
		  418 => '中美专线',
		  425 => '中美专线',
		  427 => 'EUDDP'
		);
		
		//获取物流API标题
//		$shipmentsInfo = array();
//		$shipmentsInfo = explode(',',$shipmentInfo['yw_channel']);
		$allParamArr['title'] = $shipmentTitleArr[$shipmentInfo['shipmentID']];
		$reMsg= $orderprint->zhongMeiTemplate($allParamArr);
		
		return $reMsg;
	}
	
	
	/**
	 * 打印云途广州平邮小包面单100*130
	 */
	public function printYunTuGuangZhou($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	 
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//根据国家中文名获取分区信息
		$allParamArr['country_Info'] = $this->china_post_zone_model->getAreaByCn($allParamArr['country_cn']);
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .='<tr style="font-size:12px;">
			      				   <td width="70%" style="border-right:none;">'.$p['products_declared_en'].'</td>
				 		           <td width="15%" style="border-right:none;">'.$p['item_count']*$p['products_weight'].'</td>
				 		           <td width="15%">'.$p['item_count']*$p['item_price'].'</td>
				 		      </tr>';
			      $total_weight += $p['item_count']*$p['products_weight'];
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
		     

			//提取回邮地址信息
			$addressArr = explode('|',$shipmentInfo['backAddress']);
			/**
			 * $addressArr[1] 1-eub 2-邮政小包回邮地址
			 * $addressArr[0] 回邮地址id
			 */
			if($addressArr[1]== '1'){
			   $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
			   $allParamArr['senderInfo']['name']	 		= $backAddress['contacter'];
			   $allParamArr['senderInfo']['street']	 		= $backAddress['senderStreet'];
			   $allParamArr['senderInfo']['mobilePhone'] 	= $backAddress['senderMobile'];
			   $allParamArr['senderInfo']['back_street'] 	= $backAddress['back_company'];
			}else{
			   $backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
			   $allParamArr['senderInfo']['name']	 		= !empty($backAddress[0]['consumer_name']) ? $backAddress[0]['consumer_name'] : '';
			   $allParamArr['senderInfo']['street']	 		= !empty($backAddress[0]['consumer_from']) ? $backAddress[0]['consumer_from'] : '';
			   $allParamArr['senderInfo']['mobilePhone'] 	= !empty($backAddress[0]['consumer_phone']) ? $backAddress[0]['consumer_phone'] : '';
			   $allParamArr['senderInfo']['back_street'] 	= !empty($backAddress[0]['consumer_back']) ? $backAddress[0]['consumer_back'] : '';
			}
		  

			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->YunTuGuangZhouTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	

	/**
	 * 打印lazda马来西亚面单100*130
	 */
	public function printLazadaMy($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	 
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//根据国家中文名获取分区信息
		$allParamArr['country_Info'] = $this->china_post_zone_model->getAreaByCn($allParamArr['country_cn']);
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		$total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	      
		  $total_weight += $p['item_count']*$p['products_weight'];
	      $total_value  += $p['item_count']*$p['item_price'];
	      
	    }
	    
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['total_value'] = $total_value;

	    //根据不同的账号获取不同的名称
	    $allParamArr['shipName'] = '';
	    if(trim($allParamArr['ordersInfo']['sales_account'])=='99706454@qq.com'){
	      $allParamArr['shipName'] = 'Moonastore';
	    }elseif(trim($allParamArr['ordersInfo']['sales_account'])=='lixuanpengwu@126.com'){
	      $allParamArr['shipName'] = 'Makiyo';
	    }else{
	      $reMsg = "该订单不符合打印条件";
	      return $reMsg;
	    }
	     
		//提取回邮地址信息
		$addressArr = explode('|',$shipmentInfo['backAddress']);
		/**
		 * $addressArr[1] 1-eub 2-邮政小包回邮地址
		 * $addressArr[0] 回邮地址id
		 */
		if($addressArr[1]== '1'){
		   $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
		   $allParamArr['senderInfo']['name']	 		= $backAddress['contacter'];
		   $allParamArr['senderInfo']['street']	 		= $backAddress['senderStreet'];
		   $allParamArr['senderInfo']['mobilePhone'] 	= $backAddress['senderMobile'];
		   $allParamArr['senderInfo']['back_street'] 	= $backAddress['back_company'];
		}else{
		   $backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
		   $allParamArr['senderInfo']['name']	 		= !empty($backAddress[0]['consumer_name']) ? $backAddress[0]['consumer_name'] : '';
		   $allParamArr['senderInfo']['street']	 		= !empty($backAddress[0]['consumer_from']) ? $backAddress[0]['consumer_from'] : '';
		   $allParamArr['senderInfo']['mobilePhone'] 	= !empty($backAddress[0]['consumer_phone']) ? $backAddress[0]['consumer_phone'] : '';
		   $allParamArr['senderInfo']['back_street'] 	= !empty($backAddress[0]['consumer_back']) ? $backAddress[0]['consumer_back'] : '';
		}
	  
		$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->LazadaMyTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	
	
	/**
	 * 中国邮政一体化面单
	 */
	public function printForPostXiaobao($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    //$uid = $this->user_info->id;//登录用户id
	    
	    $country_fenjian  = array(
		    'RU' => 21,'US' => 22,'GB' => 23,'BR' => 24,
			'AU' => 25,'FR' => 26,'ES' => 27,'CA' => 28,
			'IL' => 29,'IT' => 30,'DE' => 31,'CL' => 32,
			'SE' => 33,'BY' => 34,'NO' => 35,'NL' => 36,
			'UA' => 37,'CH' => 38,'MX' => 39,'PL' => 40,
		);
	    
	    $reMsg = "";
	    //获取该订单的信息
	    $allParamArr['ordersInfo']      = $orderInfo;			
		//获取该订单下的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		//获取所有的品类管理
		$allParamArr['productsType']    = $this->category_model->defineProductsType();
		
		//说去所有的退件地址
		$backList = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
		$allParamArr['backList'] = $backList[0];
		
		$totalCount = 0;//该订单下sku的总数
		$totalWeight = 0;//该订单下sku的总重量
		$totalValue = 0;//该订单下sku的总价值
		$skuInfo = '';//存放sku和仓储信息
		$flag = false;//是否要转换重量
		$perWeight = 0;
		$perPrice = 0;
		foreach($allParamArr['productsInfo'] as $ke => $va){
		  $location = '';
		  $location = $va['products_location'] ? '【'.$va['products_location'].'】' : '';
		  $totalCount +=$va['item_count'];
		  $totalWeight += $va['products_weight'] * $va['item_count'];
		  $totalValue  += $va['products_declared_value'] * $va['item_count'];
		  if($ke <=3){
		  	$skuInfo .= ' '.$va['orders_sku'].'【'.$va['item_count'].'】'.$location;
		  }
		}
		//订单总价值处理
		if ($totalValue > 10 || count($allParamArr['productsInfo']) > 1) {
			$flag = true;
			$totalValue = $totalValue > 10 ? 10 : $totalValue;
			$perCount = count($allParamArr['productsInfo']) > 1 ? 1 : count($allParamArr['productsInfo']);
			$perPrice = number_format($totalValue/$perCount, 2);
			$totalValue = number_format($perPrice * $perCount, 2);
			$perWeight = number_format($totalWeight/$perCount, 3);
			$totalWeight = $perWeight * $perCount;
		}

		$allParamArr['skuWeight'] = $flag ? $perWeight : $allParamArr['productsInfo'][0]['products_weight'];
		
		$allParamArr['skuValue'] = $flag ? $perPrice : $allParamArr['productsInfo'][0]['products_declared_value'];
		
		//获取国家中文名
		$countryInfo = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country'] = $countryInfo['country_cn'];
		
		$allParamArr['displayname'] = $countryInfo['display_name'];
		
		//根据国家中文名获取国家简码
		$countryCode = $this->youzheng_country_model->getCountryCodeByChineseName($allParamArr['country']);
		$allParamArr['countryCode'] = $countryCode['CountryCode'];
		//获取分拣码
		$allParamArr['country_fenjian'] = !empty($country_fenjian[$allParamArr['countryCode']]) ? $country_fenjian[$allParamArr['countryCode']] : '';
		
		$allParamArr['productsInfo']['totalWeight'] = $totalWeight;
		$allParamArr['productsInfo']['totalCount'] = $totalCount;
		$allParamArr['productsInfo']['totalValue'] = $totalValue;
		$allParamArr['productsInfo']['skuInfo']  = $skuInfo;

		$reMsg = $orderprint->printForPostXiaobao($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印中国邮政一体化面单')");
		}
			*/	
		return $reMsg;

	}
	
	/**
	 * DHL-中德专线面单
	 */
	public function de_print_orders($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    //$uid = $this->user_info->id;//登录用户id
	    
	    $reMsg = "";
	    //获取该订单的信息
	    $allParamArr['ordersInfo']      = $orderInfo;			
		//获取该订单下的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
	    $skuString = '';//存放sku信息
	    foreach($allParamArr['productsInfo'] as $v){
	      $inpacket = 1;
	      $sku = $v['orders_sku'];
	      if ( stripos( $sku, 'HM' ) > 0 ) {
             $skus     = $v[ 'orders_sku' ];
             $skuSplit = explode( 'HM', $skus );
             $inpacket = (int) $skuSplit[ 0 ];
             $sku      = 'HM' . $skuSplit[ 1 ];
          }
	      $skuString .= $sku . '*' . ( $inpacket * $v[ 'item_count' ] ) . ' ';
	    }
	    
	    $allParamArr['productsInfo']['skuString'] = $skuString;
	    
	    $reMsg = $orderprint->de_print_orders_template($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印中国邮政一体化面单')");
		}
			*/	
		return $reMsg;

	}

	function ajax_temp_is_pdf(){

		$order_id = $this->input->get_post('orders_id');

		$result = array();

		$result['status'] = 0;

		$result['is_pdf'] = 1;

		$result['info'] = '';

		$order = $this->orders_model->getOne(array('erp_orders_id' => $order_id),true);

		if(empty($order)){
			$result['info'] = '订单不存在';
			echo json_encode($result);die;
		}

		if(empty($order['shipmentAutoMatched'])){
			$result['info'] = '订单物流为空';
			echo json_encode($result);die;
		}

		//物流方式
		$option = array();

		$where = array();

		$option['select'] = array('tmp.*');

		$where['shipmentID'] = $order['shipmentAutoMatched'];

		$options['where'] = $where;

		$join[] = array("{$this->printing_template_model->_table} as tmp","tmp.id={$this->shipment_model->_table}.shipment_template");

		$options['join'] = $join;

		$shipment = $this->shipment_model->getOne($options,true);

		if(empty($shipment)){
			$result['info'] = '没有绑定物流模板';
			echo json_encode($result);die;
		}

		if(!empty($shipment['is_pdf'])){
			
			$result['status'] = 1;

		    $result['is_pdf'] = $shipment['is_pdf'];// 1-不是pdf，2-是pdf
			
		}

		echo json_encode($result);
	}

	//打印燕文瑞士平邮面单
	public function yanWenCH ($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
		
		$orderprint=new orderBuffetPrint();
		 
		$reMsg = '';
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//订单总价 超过30取30
		$allParamArr['total_price'] = ($allParamArr['ordersInfo']['orders_total'] > 30) ? 30 : $allParamArr['ordersInfo']['orders_total'];
		
		//如果国家全名不存在 显示国家简码
		$allParamArr['country'] = (!empty($allParamArr['ordersInfo']['buyer_country'])) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		
		//[物流id]sku*数量(仓位)
		$sku_string = '【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】';
		foreach ($allParamArr['productsInfo'] as $p_v) {
			$sku_string .= ' ' . $p_v['orders_sku'].'*'.$p_v['item_count'].'('.$p_v['products_location'].')';
		}
		$allParamArr['sku_string'] = $sku_string;
		
		
		/**
		 * 燕文国家分区 start
		 */
		$this->load->model(array('yanwen_zone_model'));
		
		//通过国家简码找分区
		$partition = $this->yanwen_zone_model->get_yw_partition($allParamArr['ordersInfo']['buyer_country_code']);
		if (empty($partition))	//如果国家简码找不到
		{
			//通过国家全名找分区
			$partition = $this->yanwen_zone_model->get_yw_partition($allParamArr['ordersInfo']['buyer_country']);
		}
		
		$allParamArr['partition'] = (empty($partition)) ? 'A' : $partition['partition'];
		/**
		 * 燕文国家分区 end
		 */
		
		$reMsg .= $orderprint->yanWenCH_template($allParamArr);
		
		return $reMsg;
	}
	
	//DHL挂号面单
	public function printDhlGuaHao ($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
		
		$orderprint=new orderBuffetPrint();
			
		$reMsg = '';
		
		$allParamArr  = array();
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		$totalPrice=0;//总价值
		$totalWeight=0;//总重量
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
			//$totalPrice+=$va['products_declared_value']*$va['item_count'];
			$totalWeight+=$va['products_weight']*$va['item_count'];
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].' ['.$v['location'].']';
		}
		
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
		$allParamArr['productsInfo']['totalPrice']=$totalPrice>20 ? 20: $totalPrice;
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
		
		$allParamArr['mail_num'] = '
						<div style="margin-left: 50px;">
							EINSCHREIBEN
						</div>
						';

		//根据国家英文名称获取国家信息
		$countryInfo=$this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		if ($countryInfo['country_cn']=='德国')
		{	
			$allParamArr['mail_num'] = '
						<div style="margin-left: 50px;">
							EINSCHREIBEN EINWURF
						</div>
						';

			$allParamArr['country_img']='
						<tr>
							<td rowspan="2" style="text-align: left; padding-left: 10px;">
								<p>Wenn unzustellbar,</p>
								<p>zurück an</p>
								<br>
								<p><b>Postfach 2007</b></p>
								<p>36243 Niederaula</p>
							</td>
							<td>
								<p><b>Deutsche Post</b></p>
							</td>
						</tr>
						<tr>
							<td>
								<br>
								<p><b>Entgelt bezahlt</b></p>
								<p>60544 Frankfurt</p>
								<p>(2378)</p>
							</td>
						</tr>
					';
		}else{
			$allParamArr['country_img']='
						<tr>
							<td colspan="2">
								<p><b>PRIORITAIRE</b></p>
							</td>
						</tr>
						
						<tr>
							<td rowspan="3" style="text-align: left; padding-left: 10px;font-size: 10px;">
								<p>En cas de non remise</p>
								<p>prière de retourner à</p>
								<br>
								<p><b>Postfach 2007</b></p>
								<p><b>36243 Niederaula</b></p>
								<p><b>ALLEMAGNE</b></p>
							</td>
							<td>
								<p><b>Deutsche Post</b></p>
							</td>
						</tr>
						
						<tr>
							<td>
								<p><b>Port payé</b></p>
								<p>60544 Frankfurt</p>
								<p>Allemagne</p>
							</td>
						</tr>
						
						<tr>
							<td>
								<p>Luftpost/Prioritaire</p>
							</td>
						</tr>
					';
		}
			
		
		
		$reMsg .= $orderprint->printDhlGuaHao_template($allParamArr);
		
		return $reMsg;
	}
	
	//中邮平邮面单逻辑处理
	Public function printForZhongYouPingYou($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
		
		$orderprint=new orderBuffetPrint();
			
		$reMsg = '';
		
		$allParamArr  = array();
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		//获取国家中文名
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//分区
		$this->load->model(array('print/china_post_zone_model'));
		$options = array(
			'where' => array(
				'country_cn' => $allParamArr['country_cn']
			)
		);
		$z_query = $this->china_post_zone_model->getOne($options, TRUE);
		$allParamArr['zone'] = (empty($z_query['zone'])) ? '' : $z_query['zone'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
			$country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
			$country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		$totalPrice=0;//第一个产品sku的总申报价值
		$totalWeight=0;//第一个产品sku的总重量
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
		
		//$totalPrice = $allParamArr['productsInfo'][0]['products_declared_value']*$allParamArr['productsInfo'][0]['item_count'];
		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		$totalPrice = $totalPrice > 25 ? 25 : $totalPrice;
		$totalWeight = $allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		$allParamArr['productsInfo']['namefiles']=$allParamArr['productsInfo'][0]['products_declared_en'];
		
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuString=substr($skuString,1);
		$skuString='<span style="font-weight:bold;font-size:13px;">【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】</span>&nbsp;'.$skuString;
		$allParamArr['productsInfo']['totalPrice']=$totalPrice;
		$allParamArr['productsInfo']['currency']='USD';
		$allParamArr['productsInfo']['time']=date('Y-m-d');
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
		$allParamArr['productsInfo']['skufiles']=$skuString;
		
		$reMsg .= $orderprint->ZhongYouPingYou_template($allParamArr);
		
		return $reMsg;
	}
	
	/**
	 * 打印4px新加坡平邮小包面单逻辑处理
	 */
	public function print4pxXinJiaPo($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
	
		$orderprint=new orderBuffetPrint();
		 
		$reMsg = "";
		 
		$allParamArr  = array();
		 
		//订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
	
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}
	
		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
	
		if (empty($query))	//如果都找不到
		{
			$query = array(
					'display_name' => $allParamArr['ordersInfo']['buyer_country'],
					'country_cn'	=> ''
			);
		}
		$allParamArr['countryInfo'] = $query;
		
		/** 通过国家中文名找分区 **/
		$this->load->model(array('erp_4px_country_code_model'));
		$where = array(
			'country_name' => $query['country_cn']
		);
		$quer_fq = $this->erp_4px_country_code_model->get_one_data($where);
		$allParamArr['fq'] = (empty($quer_fq['partition'])) ? " " : $quer_fq['partition'];
		
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
	
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
			
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
	
		$reMsg= $orderprint->print4pxXinJiaPoTemplate($allParamArr);
	
		/*
			//更新打印次数,批量
			if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time)
			WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
			}
		*/
	
		return $reMsg;
	
	}
	
	//燕文YW荷兰面单
	public function printYanWenNL ($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
	
		$orderprint=new orderBuffetPrint();
			
		$reMsg = '';
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
		//订单总价 超过30取30
		$declvar_value = 0;
		
		$declvar_value=$allParamArr['ordersInfo']['orders_total']/$allParamArr['ordersInfo']['currency_value'];
		
		$allParamArr['total_price'] = ($declvar_value > 22) ? 22 : $declvar_value;

		$total_weight = 0;
		
		//[物流id]sku*数量(仓位)
		$sku_string = '【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】';
		foreach ($allParamArr['productsInfo'] as $p_v) {
			$sku_string .= ' ' . $p_v['orders_sku'].'*'.$p_v['item_count'].'('.$p_v['products_location'].')';
			$total_weight += $p_v['item_count']*$p_v['products_weight'];
		}
		$allParamArr['sku_string'] = $sku_string;
	
		$allParamArr['total_weight'] = $total_weight;
		/**
		 * 燕文国家分区 start
		 */
		
		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
		
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}
		
		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
		
		/** 通过国家中文名找分区 **/
		$this->load->model(array('yanwen_helan_country_code_model'));
		
		$where = array(
				'country_name' => $query['country_cn']
		);
		$options = array(
			'where' => $where
		);
		
		$quer_fq = $this->yanwen_helan_country_code_model->getOne($options, TRUE);
		$allParamArr['country_int'] = (empty($quer_fq['YW_code'])) ? ' ' : $quer_fq['YW_code'];
		$allParamArr['country_eu'] = (empty($quer_fq['is_EU'])) ? ' ' : 'EU';
		/**
		 * 燕文国家分区 end
		 */
		
		//得到国家全名
		$allParamArr['country'] = (!empty($query['display_name'])) ? $query['display_name'] : $allParamArr['ordersInfo']['buyer_country'];
		
		//公司在燕文的客户编号
		$allParamArr['yanwen_code'] = '302035';
		if($allParamArr['ordersInfo']['orders_warehouse_id'] == '1025'){
				$allParamArr['yanwen_code'] = '209052';
		}

		$reMsg .= $orderprint->yanWenNL_template($allParamArr);
	
		return $reMsg;
	}
	
	//云途马来西亚平邮面单逻辑处理
	public function printMalaysiaPingYou ($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
	
		$orderprint=new orderBuffetPrint();
		
		$reMsg = '';
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
		//订单总价 超过20取20
		$allParamArr['total_price'] = ($allParamArr['ordersInfo']['orders_total'] > 20) ? 20 : $allParamArr['ordersInfo']['orders_total'];
	
		
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
		
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
		
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
	

		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
	
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}
	
		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
		
		if (empty($query))	//如果都找不到
		{
			$query = array(
					'display_name' => $allParamArr['ordersInfo']['buyer_country'],
					'country_cn'	=> ''
			);
		}
		$allParamArr['query'] = $query;
		
		$reMsg .= $orderprint->printMalaysiaPingYou_template($allParamArr);
	
		return $reMsg;
	}
	
	//云途U+平邮电面单逻辑处理
	public function printYunTuUPingYou ($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
	
		$orderprint=new orderBuffetPrint();
			
		$reMsg = '';
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
		//订单总价 超过30取30
		$allParamArr['total_price'] = ($allParamArr['ordersInfo']['orders_total'] > 30) ? 30 : $allParamArr['ordersInfo']['orders_total'];
	
	
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
	
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
	
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
	
	
		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
	
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}
	
		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
	
		$allParamArr['query'] = $query;
	
		$reMsg .= $orderprint->printYunTuUPingYou_template($allParamArr);
	
		return $reMsg;
	}
	
	/**
	 *
	 SHS香港平邮面单 逻辑处理
	 */
	public function SHSHongKongPintYou($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
		$orderprint=new orderBuffetPrint();
		
		$reMsg = "";
	
		$allParamArr  = array();
		$allParamArr['ordersInfo']      = $orderInfo;
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
		$country						=$this->country_model->getCountryByEN($orderInfo['buyer_country']);//获取国家中文名称
		$allParamArr['country']			=$country['country_cn'];
		
		$zip_arr = str_split(trim($allParamArr['ordersInfo']['buyer_zip']));	//分割邮编
		$zip_one = strtoupper($zip_arr[0]);
			
		$partition = '';
			
		$US_arr = array(1, 34, 35);	//美国根据邮编首位不同 会有3个分区
		$AU_arr = array(5, 31, 32, 33);	//澳大利亚
		$CA_arr = array(7, 30);	//加拿大
		$CA_ptt = array();
		$CA_a_z = range('A', 'Z');
		$n = FALSE;
			
		foreach ($CA_a_z as $v){
		
			if ($n){
				$CA_ptt[$v] = 30;
			}else {
				$CA_ptt[$v] = 7;
			}
				
			if ($v == 'P')
			{
				$n = TRUE;
			}
				
		}
			
		$partition_arr = array(	//国家分区数组 key为邮编首位 value为分区
				'美国' => array(
						0 => $US_arr[0],
						1 => $US_arr[0],
						2 => $US_arr[0],
						3 => $US_arr[0],
						4 => $US_arr[1],
						5 => $US_arr[1],
						6 => $US_arr[1],
						7 => $US_arr[1],
						8 => $US_arr[2],
						9 => $US_arr[2],
				),
					
				'澳大利亚' => array(
						0 => $AU_arr[0],
						1 => $AU_arr[0],
						2 => $AU_arr[0],
						3 => $AU_arr[1],
						4 => $AU_arr[2],
						5 => $AU_arr[1],
						6 => $AU_arr[3],
						7 => $AU_arr[1],
						8 => $AU_arr[1],
						9 => $AU_arr[2],
				),
					
				'加拿大' => $CA_ptt,
		);
		$partition = (empty($partition_arr[$country['country_cn']][$zip_one])) ? '' : $partition_arr[$country['country_cn']][$zip_one];
		
		if (empty($partition))
		{
			$this->load->model(array('shs_hk_zone_model'));
			$where = array(
				'country_cn' => $country['country_cn'],	//通过国家中文名 搜索分区
			);
			
			$options = array(
					'where' => $where
			);
			$reCountry = $this->shs_hk_zone_model->getOne($options, TRUE);
			
			$partition = $reCountry['partition'];
		}

		$partition = (empty($partition)) ? 50 : $partition;	//如果表里找不到这个国家 分区取50
		$allParamArr['partition'] = $partition;
	
		$reMsg = $orderprint->SHSHongKongPintYou_template($allParamArr);
	
		return $reMsg;
	}
	
	//杭州小包ZJ挂号面单逻辑处理
	public function printForHangZhouXiaoBao ($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
	
		$orderprint=new orderBuffetPrint();
			
		$reMsg = '';
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		$totalWeight = 0;
		$totalValue  = 0;
		
		//获取对应物流的退件地址
		$allParamArr['backList'] = $this->postpacket_config_model->getAllBackInfo($allParamArr['ordersInfo']['shipmentAutoMatched']);
		
		//订单总价 超过25取25
		$totalValue = ($allParamArr['ordersInfo']['orders_total'] > 25) ? 25 : $allParamArr['ordersInfo']['orders_total'];

		//第一个产品的总重量
		$totalWeight =$allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		
		//第一个产品的sku名称要组合申报中英文名称
		$allParamArr['productsInfo'][0]['orders_sku']=$allParamArr['productsInfo'][0]['products_declared_en'].' '.$allParamArr['productsInfo'][0]['products_declared_cn'].' '.$allParamArr['productsInfo'][0]['orders_sku'];
		
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
	
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
	
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		
		$allParamArr['productsInfo']['sku']=$skuString;
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
		$allParamArr['productsInfo']['totalValue']=$totalValue;
		
		//通过国家全名找
		$countryInfo = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		
		$allParamArr['country'] = $countryInfo['country_cn'];//获取国家中文名称
		
		//根据国家中文名获取序号
      	$postInfo=$this->bei_jing_post_code_model->getInfoByCn($allParamArr['country']);
      	$allParamArr['postInfo']=$postInfo;
      	
      	$allParamArr['display_name'] = $countryInfo['display_name'] ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
      	
		$reMsg .= $orderprint->printForHangZhouXiaoBao_template($allParamArr);
	
		return $reMsg;
	}
	
	//打印爱莎尼亚小包平邮
	public function PrintPingyouEstonia ($id,$shipmentInfo,$orderInfo)
	{
		$this->load->library('OrderBuffetPrint');
	
		$orderprint=new orderBuffetPrint();
			
		$reMsg = '';
		//获取该订单的信息
		$allParamArr['ordersInfo']      = $orderInfo;
		//获取该订单下的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
		//订单总价 超过20取20
		$allParamArr['total_price'] = ($allParamArr['ordersInfo']['orders_total'] > 20) ? 20 : $allParamArr['ordersInfo']['orders_total'];
	
	
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){
	
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
	
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
	
		$reMsg .= $orderprint->PrintPingyouEstonia_template($allParamArr);
	
		return $reMsg;
	}
	
	/**
	 * 燕文北京挂号面单
	 * @param unknown $id
	 * @param unknown $shipmentInfo
	 * @param unknown $orderInfo
	 * @return string
	 */
	public function printYWBjGh($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
		
		$allParamArr  = array();
		$totalCount   = 0;
		$totalWeight = 0;
		$totalValue  = 0;
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//说去所有的退件地址
		$backList = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
	
		$allParamArr['backList'] = $backList[0];
		
		$skuArr=array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		
		//第一个产品的sku名称要组合申报中英文名称
// 		$allParamArr['productsInfo'][0]['orders_sku']=$allParamArr['productsInfo'][0]['products_declared_en'].' '.$allParamArr['productsInfo'][0]['products_declared_cn'].' '.$allParamArr['productsInfo'][0]['orders_sku'];
		
		//第一个产品的申报价值
		$totalValue = $allParamArr['productsInfo'][0]['products_declared_value'];
		
		//第一个产品的总重量
	    $totalWeight =$allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
		
		//重新组合产品sku的数据
		foreach($allParamArr['productsInfo'] as $ke => $va){
			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
		}
		
		//把sku和数组组合成字符串	
		foreach($skuArr as $v){
			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
		}
		$skuString=substr($skuString,1);
		//$skuString  = strlen($skuString) > 120 ? mb_strcut($skuString, 0, 120, 'utf-8') : $skuString;
		
		$allParamArr['productsInfo']['totalWeight']=$totalWeight;
		$allParamArr['productsInfo']['totalValue']=$totalValue;
		$allParamArr['productsInfo']['sku']=$skuString;		
		$country						=$this->base_country_model->getCountryInfoByCode($orderInfo['buyer_country_code']);
		$allParamArr['country']			=$country['country_cn'];//获取国家中文名称
		$allParamArr['country_enname']  =$country['country_en'];
	    //根据国家中文名获取序号
      	$postInfo=$this->bei_jing_post_code_model->getInfoByCn($allParamArr['country']);
      	
      	if(empty($postInfo)){
	      		$postInfo['postAreaGH'] = '挂1';
	      		$postInfo['enCode'] = 'B';
	      		$postInfo['postArea1'] = '序41';
	    }

      	$allParamArr['postInfo']=$postInfo;
		$allParamArr['createTime']=date('Y-m-d');

		//公司在燕文的客户编号
		$allParamArr['yanwen_code'] = '302035';
		if($allParamArr['ordersInfo']['orders_warehouse_id'] == '1025'){
				$allParamArr['yanwen_code'] = '209052';
		}

		$reMsg = $orderprint->printYWBejiingGH($allParamArr);
	
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印贝邮宝面单')");
		}
			*/	
		return $reMsg;
	}
	
//	/**
//	 * 共用获取订单数据和产品数据方法
//	 */
//	public function get_orders_and_products_info($orderInfo){
//		
//		$allParamArr  = array();
//		
//	    //订单信息
//		$allParamArr['ordersInfo']      = $orderInfo;
//			
//		//订单中的产品信息
//		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
//		
//		//根据国家英文简称获取国家信息
//		$country_info = $this->base_country_model->getCountryInfoByCode($orderInfo['buyer_country_code']);
//		
//		if(empty($country_info)){
//		  $allParamArr['countrysInfo']['country_en'] = $orderInfo['buyer_country'];
//		  $allParamArr['countrysInfo']['country_cn'] = '';
//		  $allParamArr['countrysInfo']['country_code'] = $orderInfo['buyer_country_code'];
//		}else{
//		  $allParamArr['countrysInfo'] = $country_info;
//		}
//		
//		$skuArr=array();//存放sku和数量
//		
//		$skuString='';//存放sku和数量的组合字符串
//		
//		$sku_info = '';
//	
//		//重新组合产品sku的数据
//		foreach($allParamArr['productsInfo'] as $ke => $va){
//			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
//				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
//			}else{
//				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
//				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
//				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
//			}
//		}
//		
//		//把sku和数组组合成字符串	
//		foreach($skuArr as $v){
//			$skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
//		}
//		$skuString=substr($skuString,1);
//		
//		$sku_info = '【'.$allParamArr['ordersInfo']['shipmentAutoMatched'].'】'.' '.$skuString;
//		
//		$allParamArr['skusInfo'] = $sku_info;	
//		
//		return $allParamArr;
//		
//	}
	
	
	/**
	 * 秀驿COE平邮面单
	 * @param $id =>订单ID
	 * @param $shipmentInfo => 物流信息
	 * @param $orderInfo => 订单信息
	 * @return string
	 */
	  public function printCOEPY($id,$shipmentInfo,$orderInfo){
	    $this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint(); 
	    //$uid = $this->user_info->id;//登录用户id
	    $reMsg = "";	
	    $allParamArr = array();
	    $totalCount  = 0;
	    $totalWeight = 0;
	    $totalValue  = 0;
	    $allParamArr['ordersInfo']      = $orderInfo;
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	    
	    //说去所有的退件地址
	    $backList = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
	
	    $allParamArr['backList'] = $backList[0];
	
	    $skuArr=array();//存放sku和数量
	    $skuString='';//存放sku和数量的组合字符串
	
	    //第一个产品的sku名称要组合申报中英文名称
	    // 		$allParamArr['productsInfo'][0]['orders_sku']=$allParamArr['productsInfo'][0]['products_declared_en'].' '.$allParamArr['productsInfo'][0]['products_declared_cn'].' '.$allParamArr['productsInfo'][0]['orders_sku'];
	
	    //第一个产品的申报价值
	    $totalValue = $allParamArr['productsInfo'][0]['products_declared_value'];
	
	    //第一个产品的总重量
	    $totalWeight =$allParamArr['productsInfo'][0]['products_weight']*$allParamArr['productsInfo'][0]['item_count'];
	
	    //重新组合产品sku的数据
	    foreach($allParamArr['productsInfo'] as $ke => $va){
	        if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
	            $skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
	        }else{
	            $skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
	            $skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
	            $skuArr[$va['orders_sku']]['location']=$va['products_location'];
	        }
	    }
	
	    //把sku和数组组合成字符串
	    foreach($skuArr as $v){
	        $skuString.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
	    }
	    $skuString=substr($skuString,1);
	    //$skuString  = strlen($skuString) > 120 ? mb_strcut($skuString, 0, 120, 'utf-8') : $skuString;
	
	    $allParamArr['productsInfo']['totalWeight']=$totalWeight;
	    $allParamArr['productsInfo']['totalValue']=$totalValue;
	    $allParamArr['productsInfo']['sku']=$skuString;
	    $country						=$this->base_country_model->getCountryInfoByCode($orderInfo['buyer_country_code']);
	    $allParamArr['country']			=$country['country_cn'];//获取国家中文名称
	    $allParamArr['country_enname']  =$country['country_en'];
	    //根据国家中文名获取序号
	    $postInfo=$this->bei_jing_post_code_model->getInfoByCn($allParamArr['country']);
	     
	    if(empty($postInfo)){
	        $postInfo['postAreaGH'] = '挂1';
	        $postInfo['enCode'] = 'B';
	        $postInfo['postArea1'] = '序41';
	    }
	
	    $allParamArr['postInfo']=$postInfo;
	    $allParamArr['createTime']=date('Y-m-d');
	
	    //公司在燕文的客户编号
	    $allParamArr['yanwen_code'] = '302035';
	    if($allParamArr['ordersInfo']['orders_warehouse_id'] == '1025'){
	        $allParamArr['yanwen_code'] = '209052';
	    }
	
	    $reMsg = $orderprint->printCOEPY($allParamArr);
	
	    /*
	     //更新打印次数,批量
	     if ($id){
	     $sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time)
	     WHERE erp_orders_id=".$id;
	     $this->db->query($sql);
	     $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印贝邮宝面单')");
	     }
	    */
	    return $reMsg;
	}
	
	public function printTestyz($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
		//提取回邮地址信息
		$addressArr = explode('|',$shipmentInfo['backAddress']);
		if($addressArr[1]==1){//如果回邮地址类型是1，提取erp_eub_back_address表中的数据,EUB
		  $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
		  $backAddress =$backAddress['back_address'];
		}elseif($addressArr[1]==2){
			$backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
			$backAddress =$backAddress[0]['consumer_back'];
		}

	    $reMsg = "";	
		
		$allParamArr  = array();
		$totalCount   = 0;
		$totalWeight = 0;
		$totalValue  = 0;
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		$allParamArr['backAddress'] = $backAddress;
		
		//获取寄件FORM数据
		$senderInfo = $this->gz_address_model->getSenderInfo();
		if(!$senderInfo){
			die('<span style="font-weight:bold;color:red;">今日寄件人地址已用完，不允许打印</span>');
		}
		$allParamArr['senderInfo'] = $senderInfo;

		$total_weight = 0;
		$total_price = 0;
		$first_declarevalue = 0;//第一个产品的申报总价值
		
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){

			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
			$total_weight += $va['item_count']*$va['products_weight'];
			$total_price  += $va['item_count']*$va['products_declared_value'];
		}
		$allParamArr['total_weight'] = $total_weight;
		$total_price = $total_price>20 ? 20 : $total_price;
		$allParamArr['total_price'] = $total_price;
		
		$first_declarevalue = $allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_declared_value'];
		$first_declarevalue = $first_declarevalue>20 ? 20 : $first_declarevalue;
		$allParamArr['first_declarevalue'] = $first_declarevalue;
		
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
		
		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
		
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找 
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}

		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
		
		if (empty($query))	//如果都找不到
		{
			$query = array(
					'display_name' => $allParamArr['ordersInfo']['buyer_country'],
					'country_cn'	=> '',
					'country_en'   => $allParamArr['ordersInfo']['buyer_country_code']
			);
		}
		
		//根据国家中文名获取格口号
		$geKouInfo = $this->gz_gekou_model->getInfoByCountryCn($query['country_cn']);
		$geKou = $geKouInfo[0]['geID'];
		$allParamArr['geKou'] = $geKou;

		$allParamArr['countryInfo'] = $query;

		//根据国家简码获取国家分区
		$areaInfo = array();
		$areaCode = '';
		if($allParamArr['countryInfo']['country_en']=='UK'){
		  $allParamArr['countryInfo']['country_en']='GB';
		}
		$areaInfo = $this->shunyou_area_model->getInfoByCode($allParamArr['countryInfo']['country_en']);
		if(!empty($areaInfo)){
		  $areaCode = $areaInfo['area_code'];
		}
		$allParamArr['areaCode'] = $areaCode;
		
		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		$totalPrice = $totalPrice > 20 ? 20 : $totalPrice;
		$allParamArr['ordersInfo']['orders_total'] = $totalPrice;
		$allParamArr['is_flag'] = ' ';
		
		$sign = '';
	    //添加签名
	    if($allParamArr['ordersInfo']['orders_warehouse_id']==1000){
	      $sign = 'szslm';
	    }elseif($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
	      $sign = 'ywslm';
	    }
	    $allParamArr['sign'] = $sign;

		$reMsg = $orderprint->printTestyz_tlp($allParamArr);
		return $reMsg;
	}
	public function printLdbYZ($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	    $orderprint=new orderBuffetPrint();
	    //$uid = $this->user_info->id;//登录用户id
		//提取回邮地址信息
		$addressArr = explode('|',$shipmentInfo['backAddress']);
		if($addressArr[1]==1){//如果回邮地址类型是1，提取erp_eub_back_address表中的数据,EUB
		  $backAddress = $this->eub_back_address_model->getInfoByID($addressArr[0]);
		  $backAddress =$backAddress['back_address'];
		}elseif($addressArr[1]==2){
			$backAddress = $this->postpacket_config_model->getAllBackInfo($shipmentInfo['shipmentID']);
			$backAddress =$backAddress[0]['consumer_back'];
		}

	    $reMsg = "";	
		
		$allParamArr  = array();
		$totalCount   = 0;
		$totalWeight = 0;
		$totalValue  = 0;
		$allParamArr['ordersInfo']      = $orderInfo;			
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		$allParamArr['backAddress'] = $backAddress;
		
		//获取寄件FORM数据
		$senderInfo = $this->gz_address_model->getSenderInfo();
		if(!$senderInfo){
			die('<span style="font-weight:bold;color:red;">今日寄件人地址已用完，不允许打印</span>');
		}
		$allParamArr['senderInfo'] = $senderInfo;

		$total_weight = 0;
		$total_price = 0;
		$first_declarevalue = 0;//第一个产品的申报总价值
		
		$skuArr = array();//存放sku和数量
		$skuString='';//存放sku和数量的组合字符串
		foreach($allParamArr['productsInfo'] as $va){

			if(isset($skuArr[$va['orders_sku']])){//组合sku和sku的数量
				$skuArr[$va['orders_sku']]['item_count']+=$va['item_count'];
			}else{
				$skuArr[$va['orders_sku']]['sku']=$va['orders_sku'];
				$skuArr[$va['orders_sku']]['item_count']=$va['item_count'];
				$skuArr[$va['orders_sku']]['location']=$va['products_location'];
			}
			$total_weight += $va['item_count']*$va['products_weight'];
			$total_price  += $va['item_count']*$va['products_declared_value'];
		}
		$allParamArr['total_weight'] = $total_weight;
		$total_price = $total_price>20 ? 20 : $total_price;
		$allParamArr['total_price'] = $total_price;
		
		$first_declarevalue = $allParamArr['productsInfo'][0]['item_count']*$allParamArr['productsInfo'][0]['products_declared_value'];
		$first_declarevalue = $first_declarevalue>20 ? 20 : $first_declarevalue;
		$allParamArr['first_declarevalue'] = $first_declarevalue;
		
		//把sku和数组组合成字符串
		foreach($skuArr as $v){
			$skuString.=', '.$v['sku'].'*'.$v['item_count'] . '【' . $v['location'] . '】';
		}
		$skuString=substr($skuString,1);
		$allParamArr['productsInfo']['sku']=$skuString;
		
		//通过国家简码找 有可能国家简码未填写
		$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country_code']);
		
		if (empty($query))	//如果通过国家简码找不到
		{
			//通过国家全名找 
			$query = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		}

		if (empty($query))	//如果通过国家全名找不到
		{
			//通过国家全名找全名
			$query = $this->country_model->getCountryByDisplayName($allParamArr['ordersInfo']['buyer_country']);
		}
		
		if (empty($query))	//如果都找不到
		{
			$query = array(
					'display_name' => $allParamArr['ordersInfo']['buyer_country'],
					'country_cn'	=> '',
					'country_en'   => $allParamArr['ordersInfo']['buyer_country_code']
			);
		}
		
		//根据国家中文名获取格口号
		$geKouInfo = $this->gz_gekou_model->getInfoByCountryCn($query['country_cn']);
		$geKou = $geKouInfo[0]['geID'];
		$allParamArr['geKou'] = $geKou;

		$allParamArr['countryInfo'] = $query;

		//根据国家简码获取国家分区
		$areaInfo = array();
		$areaCode = '';
		if($allParamArr['countryInfo']['country_en']=='UK'){
		  $allParamArr['countryInfo']['country_en']='GB';
		}
		$areaInfo = $this->shunyou_area_model->getInfoByCode($allParamArr['countryInfo']['country_en']);
		if(!empty($areaInfo)){
		  $areaCode = $areaInfo['area_code'];
		}
		$allParamArr['areaCode'] = $areaCode;
		
		$totalPrice = $allParamArr['ordersInfo']['orders_total'];
		$totalPrice = $totalPrice > 20 ? 20 : $totalPrice;
		$allParamArr['ordersInfo']['orders_total'] = $totalPrice;
		$allParamArr['is_flag'] = ' ';
		
		$sign = '';
	    //添加签名
	    if($allParamArr['ordersInfo']['orders_warehouse_id']==1000){
	      $sign = 'szslm';
	    }elseif($allParamArr['ordersInfo']['orders_warehouse_id']==1025){
	      $sign = 'ywslm';
	    }
	    $allParamArr['sign'] = $sign;

		$reMsg = $orderprint->printLdbYZ_tlp($allParamArr);
		return $reMsg;
	}
	/**
	 * COE平邮面单100*100
	 */
	public function printCOEpy_ldb($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	
	    $orderprint=new orderBuffetPrint();
	     
	    $reMsg = "";
	     
	    $allParamArr  = array();
	
	     
	    //订单信息
	    $allParamArr['ordersInfo']      = $orderInfo;
	    	
	    //订单中的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
	    //根据国家英文名称获取国家中文名称
	    $country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
	    $allParamArr['country_cn'] = $country_cn['country_cn'];
	
	    //如果国家为空，取国家简码
	    if(empty($allParamArr['ordersInfo']['buyer_country'])){
	        $country=$allParamArr['ordersInfo']['buyer_country_code'];
	    }else{
	        $country=$allParamArr['ordersInfo']['buyer_country'];
	    }
	    $allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
	
	
	    $allParamArr['total_decalre_value'] = '';
	   
	    $signal_weight=0;//第一个sku的总重量
	    $signal_value =0;//第一个sku的总价值
	    $total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	         
	        $allParamArr['total_decalre_value']+=$p['products_declared_value'];
	
	        if($k<1){//只显示第一个产品的sku信息
	            $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
	            $signal_weight += $p['item_count']*$p['products_weight'];
	            $signal_value  += $p['item_count']*$p['item_price'];
	        }
	        $total_weight += $p['item_count']*$p['products_weight'];
	        $total_value  += $p['item_count']*$p['item_price'];
	        
	        if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
	            $skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
	        }else{
	            $skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
	            $skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
	            $skuArr[$p['orders_sku']]['location']=$p['products_location'];
	        }
	    }
	
	    $s_value = $signal_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['signal_value'] = $s_value > 20 ? 20 : $s_value;
	    
	    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;

	    //把sku和数组组合成字符串
	    foreach($skuArr as $v){
	        $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
	    }
	    $skuInfo=substr($skuInfo,1);
	    $allParamArr['skuInfo'] = $skuInfo;
	    	
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['signal_weight'] = $signal_weight;
	
	    $allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		//区号
		$qnumb = $allParamArr['ordersInfo']['buyer_zip'];
		$qnfirst = substr($qnumb,0,1);
		$qnfs = substr($qnumb,0,2);
		if(in_array($qnfirst,array(3,4,6))){
			//区号为SIB
			$allParamArr['qnumb'] = 'SIB';
		}elseif(in_array($qnfs,array(16,17,18,19))){
			//区号为STP；
			$allParamArr['qnumb'] = 'STP';
		}else{
			//其他区号为MSC；
			$allParamArr['qnumb'] = 'MSC';
		}
		
	    $reMsg= $orderprint->printCOEpy_ldb_tlp($allParamArr);
		
	    /*
	     //更新打印次数,批量
	     if ($id){
	     $sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time)
	     WHERE erp_orders_id=".$id;
	     $this->db->query($sql);
	     $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
	     }
	    */
	
	    return $reMsg;
	}
	/**
	 * 迪欧比利时邮政渠道面单100*130
	 *2016-4-11
	 */
	public function printDiouPY($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	
	    $orderprint=new orderBuffetPrint();
	     
	    $reMsg = "";
	     
	    $allParamArr  = array();
	
	     
	    //订单信息
	    $allParamArr['ordersInfo']      = $orderInfo;
	    	
	    //订单中的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
	    //根据国家英文名称获取国家中文名称
	    $country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
	    $allParamArr['country_cn'] = $country_cn['country_cn'];
	
	    //获取比利时的国家分区
	    $blishiArea = $this->bilishi_area_model->getAll2array();
	    $allParamArr['bilishiArea'] = '';//比利时国家分区号；
	    foreach($blishiArea as $v){
	    	if($allParamArr['ordersInfo']['buyer_country_code'] == $v['country_code']){
	    		$allParamArr['bilishiArea'] = $v['area'];break;
	    	}elseif(strtolower($allParamArr['ordersInfo']['buyer_country']) == strtolower($v['country'])){
	    		$allParamArr['bilishiArea'] = $v['area'];break;
	    	}
	    }
	    if($allParamArr['bilishiArea'] == ''){
	    	$allParamArr['bilishiArea']=0;
	    }

	    //如果国家为空，取国家简码
	    if(empty($allParamArr['ordersInfo']['buyer_country'])){
	        $country=$allParamArr['ordersInfo']['buyer_country_code'];
	    }else{
	        $country=$allParamArr['ordersInfo']['buyer_country'];
	    }
	    $allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
	
		



	    $allParamArr['total_decalre_value'] = '';
	   
	    $signal_weight=0;//第一个sku的总重量
	    $signal_value =0;//第一个sku的总价值
	    $total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	         
	        $allParamArr['total_decalre_value']+=$p['products_declared_value'];
	
	        if($k<1){//只显示第一个产品的sku信息
	            $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
	            $signal_weight += $p['item_count']*$p['products_weight'];
	            $signal_value  += $p['item_count']*$p['item_price'];
	        }
	        $total_weight += $p['item_count']*$p['products_weight'];
	        $total_value  += $p['item_count']*$p['item_price'];
	        
	        if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
	            $skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
	        }else{
	            $skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
	            $skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
	            $skuArr[$p['orders_sku']]['location']=$p['products_location'];
	        }
	    }

	    $s_value = $signal_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['signal_value'] = $s_value > 20 ? 20 : $s_value;
	    
	    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
	
	
	    //把sku和数组组合成字符串
	    foreach($skuArr as $v){
	        $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
	    }
	    $skuInfo=substr($skuInfo,1);
	    $allParamArr['skuInfo'] = $skuInfo;
	    	
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['signal_weight'] = $signal_weight;
	
	    $allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		//区号
		$qnumb = $allParamArr['ordersInfo']['buyer_zip'];
		$qnfirst = substr($qnumb,0,1);
		$qnfs = substr($qnumb,0,2);
		if(in_array($qnfirst,array(3,4,6))){
			//区号为SIB
			$allParamArr['qnumb'] = 'SIB';
		}elseif(in_array($qnfs,array(16,17,18,19))){
			//区号为STP；
			$allParamArr['qnumb'] = 'STP';
		}else{
			//其他区号为MSC；
			$allParamArr['qnumb'] = 'MSC';
		}
		
	    $reMsg= $orderprint->printDiouPY_ldb_tlp($allParamArr);
		
	    /*
	     //更新打印次数,批量
	     if ($id){
	     $sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time)
	     WHERE erp_orders_id=".$id;
	     $this->db->query($sql);
	     $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
	     }
	    */
	
	    return $reMsg;
	}
	/**
	 * 打印lazada新加坡的面单 add by lidabiao 2016-5-5
	 * 100*100
	 */
	public function printNewlazada($id,$shipmentInfo,$orderInfo){
		
	    $this->load->library('OrderBuffetPrint');
		
	    $orderprint=new orderBuffetPrint();
	    
	    $reMsg = "";
	    
	    $allParamArr  = array();
	   
	    
	    //订单信息
		$allParamArr['ordersInfo']      = $orderInfo;
			
		//订单中的产品信息
		$allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		
		//根据国家英文名称获取国家中文名称
		$country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
		$allParamArr['country_cn'] = $country_cn['country_cn'];
		
		//如果国家为空，取国家简码
		if(empty($allParamArr['ordersInfo']['buyer_country'])){
		       $country=$allParamArr['ordersInfo']['buyer_country_code'];
		}else{
		       $country=$allParamArr['ordersInfo']['buyer_country'];
		}
		$allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
		
		$allParamArr['total_decalre_value'] = '';

			$total_weight = 0;//总重量
		    $total_value = 0;//总价值
		    $skuInfo = '';//sku信息
		    $skuArr = array();//存放sku信息数组
		    $trInfo = '';//存放表格行
		    //获取产品信息
		    foreach($allParamArr['productsInfo'] as $k => $p){
		    	
		    	$allParamArr['total_decalre_value']+=$p['products_declared_value'];
		      
		      if($k<1){//只显示第一个产品的sku信息
			      $trInfo .='<tr style="line-height: 12px; height: 44px;">
			      				   <td colspan="4" class="border_r_b_l" valign="top" width="60%">'.$p['products_declared_en'].'</td>
				 		           <td class="border_r_b" valign="top">'.$p['item_count']*$p['products_weight'].'</td>
				 		           <td class="border_r_b" valign="top">'.$p['item_count']*$p['item_price'].'('.$allParamArr['ordersInfo']['currency_type'].')</td>
				 		      </tr>';
			      
		      	  $total_value  += $p['item_count']*$p['item_price'];
		      }
		      $total_weight += $p['item_count']*$p['products_weight'];
		      $total_value  += $p['item_count']*$p['item_price'];
		      if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
					$skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
			  }else{
					$skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
					$skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
					$skuArr[$p['orders_sku']]['location']=$p['products_location'];
			  }
		    }
		    
		    //把sku和数组组合成字符串	
			foreach($skuArr as $v){
			  $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
			}
			$skuInfo=substr($skuInfo,1);
			$allParamArr['skuInfo'] = $skuInfo;
			
		    $allParamArr['trInfo'] = $trInfo;
		    $allParamArr['productsInfo']['total_weight'] = $total_weight;
		    $allParamArr['productsInfo']['total_value'] = $total_value;
		     
			//根据不同的账号获取不同的名称
		    $allParamArr['shipName'] = '';
		    if(trim($allParamArr['ordersInfo']['sales_account'])=='99706454@qq.com_SG'){
		      $allParamArr['shipName'] = 'Moonastore';
		    }elseif(trim($allParamArr['ordersInfo']['sales_account'])=='lixuanpengwu@126.com_SG'){
				$allParamArr['shipName'] = 'Makiyo';
			}else{
		      $reMsg = "该订单不符合打印条件";
		      return $reMsg;
		    }
		    
		    //获取包裹号，pagenumber
		    $pageArr = $this->lazada_pagenumber_model->getInfoByOrderID(array('ordersID'=>$allParamArr['ordersInfo']['erp_orders_id']));
			if(empty($pageArr)){
			  $reMsg = "该订单的pagenumber不存在";
		      return $reMsg;
			}
			$allParamArr['pagenumber'] = $pageArr[0]['pagenumber'];
			
			$allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];

		$reMsg= $orderprint->printNewlazadaTemplate($allParamArr);
		
		/*
		//更新打印次数,批量
		if ($id){
			$sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time) 
					WHERE erp_orders_id=".$id;
			$this->db->query($sql);
			$this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
		}
		*/
		
		return $reMsg;

	}
	/**
	 * 云途中华小包面单
	 */
	public function printCOEyuntoxb($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	
	    $orderprint=new orderBuffetPrint();
	     
	    $reMsg = "";
	     
	    $allParamArr  = array();
	
	     
	    //订单信息
	    $allParamArr['ordersInfo']      = $orderInfo;
	    	
	    //订单中的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
	    //根据国家英文名称获取国家中文名称
	    $country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
	    $allParamArr['country_cn'] = $country_cn['country_cn'];
	
	    //如果国家为空，取国家简码
	    if(empty($allParamArr['ordersInfo']['buyer_country'])){
	        $country=$allParamArr['ordersInfo']['buyer_country_code'];
	    }else{
	        $country=$allParamArr['ordersInfo']['buyer_country'];
	    }
	    $allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
	
	
	    $allParamArr['total_decalre_value'] = '';
	   
	    $signal_weight=0;//第一个sku的总重量
	    $signal_value =0;//第一个sku的总价值
	    $total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	         
	        $allParamArr['total_decalre_value']+=$p['products_declared_value'];
	
	        if($k<1){//只显示第一个产品的sku信息
	            $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;x'.$p['item_count'];
	            $signal_weight += $p['item_count']*$p['products_weight'];
	            $signal_value  += $p['item_count']*$p['item_price'];
	        }
	        $total_weight += $p['item_count']*$p['products_weight'];
	        $total_value  += $p['item_count']*$p['item_price'];
	        
	        if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
	            $skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
	        }else{
	            $skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
	            $skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
	            $skuArr[$p['orders_sku']]['location']=$p['products_location'];
	        }
	    }
	
	    $s_value = $signal_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['signal_value'] = $s_value > 20 ? 20 : $s_value;
	    
	    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['total_value'] = $z_value > 20 ? 20 : $z_value;
	
	
	    //把sku和数组组合成字符串
	    foreach($skuArr as $v){
	        $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
	    }
	    $skuInfo=substr($skuInfo,1);
	    $allParamArr['skuInfo'] = $skuInfo;
	    	
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['signal_weight'] = $signal_weight;
	
	    $allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		//区号
		$qnumb = $allParamArr['ordersInfo']['buyer_zip'];
		$qnfirst = substr($qnumb,0,1);
		$qnfs = substr($qnumb,0,2);
		if(in_array($qnfirst,array(0,1,2,3))){
			
			$allParamArr['qnumb'] = 'JFK';
		}elseif(in_array($qnfirst,array(4,5,6))){
			$allParamArr['qnumb'] = 'ORD';
		}elseif(in_array($qnfirst,array(7,8,9))){
			$allParamArr['qnumb'] = 'LAX';
		}
		$ship_code = $allParamArr['ordersInfo']['orders_shipping_code'];
		$yuntuaddress=$this->getYuntusender($ship_code);
		$allParamArr['newAddress'] = $yuntuaddress['Item']['SenderAddress'];
	    $reMsg= $orderprint->printCOEyuntoxb_tlp($allParamArr);
		
	    return $reMsg;
	}
	//云途获取寄件人
	public function getYuntusender($shipcode){
			$credentials = "C10262&8EE7CxtoZ2c=";  //帐号：密码	
			$url = "http://api.yunexpress.com/LMS.API/api/WayBill/GetSendMessage?number=".$shipcode;
		
			$headers = array(
					"Authorization: Basic ".base64_encode($credentials),
					"Content-type: application/json;charset=UTF-8"		
			);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, 0);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $request_json);	
			$data = curl_exec($ch);	
			$reArr = array();
			if (curl_errno($ch)) {
				$reArr['Error'] = curl_error($ch);
				return $reArr;
			}else{
				curl_close($ch);		
				$data = json_decode($data,true);
				return $data;
			}	
	}
	//土耳其挂号面单ETRR add by lidabiao 2016-5-11
	public function printTTPgh_ldb($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	
	    $orderprint=new orderBuffetPrint();
	     
	    $reMsg = "";
	     
	    $allParamArr  = array();
	
	     
	    //订单信息
	    $allParamArr['ordersInfo']      = $orderInfo;
	    	
	    //订单中的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
	    //根据国家英文名称获取国家中文名称
	    $country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
	    $allParamArr['country_cn'] = $country_cn['country_cn'];
	
	    //如果国家为空，取国家简码
	    if(empty($allParamArr['ordersInfo']['buyer_country'])){
	        $country=$allParamArr['ordersInfo']['buyer_country_code'];
	    }else{
	        $country=$allParamArr['ordersInfo']['buyer_country'];
	    }
	    $allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
	
	
	    $allParamArr['total_decalre_value'] = '';
	   
	    $signal_weight=0;//第一个sku的总重量
	    $signal_value =0;//第一个sku的总价值
	    $total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	         
	        $allParamArr['total_decalre_value']+=$p['products_declared_value'];
	
	        if($k<1){//只显示第一个产品的sku信息
	            $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;x'.'1';
	            $signal_weight += $p['item_count']*$p['products_weight'];
	            $signal_value  += $p['item_count']*$p['item_price'];
	        }
	        $total_weight += $p['item_count']*$p['products_weight'];
	        $total_value  += $p['item_count']*$p['item_price'];
	        
	        if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
	            $skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
	        }else{
	            $skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
	            $skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
	            $skuArr[$p['orders_sku']]['location']=$p['products_location'];
	        }
	    }
	
	    $s_value = $signal_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['signal_value'] = $s_value > 20 ? 20 : $s_value;
	    
	    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['total_value'] = $z_value > 22 ? 22 : $z_value;

	    //把sku和数组组合成字符串
	    foreach($skuArr as $v){
	        $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
	    }
	    $skuInfo=substr($skuInfo,1);
	    $allParamArr['skuInfo'] = $skuInfo;
	    	
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['signal_weight'] = $signal_weight;
	
	    $allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		//区号
		$qnumb = $allParamArr['ordersInfo']['buyer_zip'];
		$qnfirst = substr($qnumb,0,1);
		$qnfs = substr($qnumb,0,2);
		if(in_array($qnfirst,array(3,4,6))){
			//区号为SIB
			$allParamArr['qnumb'] = 'SIB';
		}elseif(in_array($qnfs,array(16,17,18,19))){
			//区号为STP；
			$allParamArr['qnumb'] = 'STP';
		}else{
			//其他区号为MSC；
			$allParamArr['qnumb'] = 'MSC';
		}
		
	    $reMsg= $orderprint->printTTPgh_ldb_tlp($allParamArr);
		
	    /*
	     //更新打印次数,批量
	     if ($id){
	     $sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time)
	     WHERE erp_orders_id=".$id;
	     $this->db->query($sql);
	     $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
	     }
	    */
	
	    return $reMsg;
	}
	//土耳其挂号面单ETRR add by lidabiao 2016-5-11
	public function printTTPpy_ldb($id,$shipmentInfo,$orderInfo){
		$this->load->library('OrderBuffetPrint');
	
	    $orderprint=new orderBuffetPrint();
	     
	    $reMsg = "";
	     
	    $allParamArr  = array();
	
	     
	    //订单信息
	    $allParamArr['ordersInfo']      = $orderInfo;
	    	
	    //订单中的产品信息
	    $allParamArr['productsInfo']    = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
	    //根据国家英文名称获取国家中文名称
	    $country_cn = $this->country_model->getCountryByEN($allParamArr['ordersInfo']['buyer_country']);
	    $allParamArr['country_cn'] = $country_cn['country_cn'];
	
	    //如果国家为空，取国家简码
	    if(empty($allParamArr['ordersInfo']['buyer_country'])){
	        $country=$allParamArr['ordersInfo']['buyer_country_code'];
	    }else{
	        $country=$allParamArr['ordersInfo']['buyer_country'];
	    }
	    $allParamArr['buyerCountry'] = $country_cn['display_name'] ? $country_cn['display_name'] : $country;
	
	
	    $allParamArr['total_decalre_value'] = '';
	   
	    $signal_weight=0;//第一个sku的总重量
	    $signal_value =0;//第一个sku的总价值
	    $total_weight = 0;//总重量
	    $total_value = 0;//总价值
	    $skuInfo = '';//sku信息
	    $skuArr = array();//存放sku信息数组
	    $trInfo = '';//存放表格行
	    //获取产品信息
	    foreach($allParamArr['productsInfo'] as $k => $p){
	         
	        $allParamArr['total_decalre_value']+=$p['products_declared_value'];
	
	        if($k<1){//只显示第一个产品的sku信息
	            $trInfo .= $p['products_declared_en'].'&nbsp;&nbsp;&nbsp;&nbsp;x1';
	            $signal_weight += $p['item_count']*$p['products_weight'];
	            $signal_value  += $p['item_count']*$p['item_price'];
	        }
	        $total_weight += $p['item_count']*$p['products_weight'];
	        $total_value  += $p['item_count']*$p['item_price'];
	        
	        if(isset($skuArr[$p['orders_sku']])){//组合sku和sku的数量
	            $skuArr[$p['orders_sku']]['item_count']+=$p['item_count'];
	        }else{
	            $skuArr[$p['orders_sku']]['sku']=$p['orders_sku'];
	            $skuArr[$p['orders_sku']]['item_count']=$p['item_count'];
	            $skuArr[$p['orders_sku']]['location']=$p['products_location'];
	        }
	    }
	
	    $s_value = $signal_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['signal_value'] = $s_value > 20 ? 20 : $s_value;
	    
	    $z_value = $total_value/$allParamArr['ordersInfo']['currency_value'];
	    $allParamArr['productsInfo']['total_value'] = $z_value > 22 ? 22 : $z_value;

	    //把sku和数组组合成字符串
	    foreach($skuArr as $v){
	        $skuInfo.=','.$v['sku'].'*'.$v['item_count'].'【'.$v['location'].'】';
	    }
	    $skuInfo=substr($skuInfo,1);
	    $allParamArr['skuInfo'] = $skuInfo;
	    	
	    $allParamArr['trInfo'] = $trInfo;
	    $allParamArr['productsInfo']['total_weight'] = $total_weight;
	    $allParamArr['productsInfo']['signal_weight'] = $signal_weight;
	
	    $allParamArr['country_code'] = empty($allParamArr['ordersInfo']['buyer_country_code']) ? $allParamArr['ordersInfo']['buyer_country'] : $allParamArr['ordersInfo']['buyer_country_code'];
		//区号
		$qnumb = $allParamArr['ordersInfo']['buyer_zip'];
		$qnfirst = substr($qnumb,0,1);
		$qnfs = substr($qnumb,0,2);
		if(in_array($qnfirst,array(3,4,6))){
			//区号为SIB
			$allParamArr['qnumb'] = 'SIB';
		}elseif(in_array($qnfs,array(16,17,18,19))){
			//区号为STP；
			$allParamArr['qnumb'] = 'STP';
		}else{
			//其他区号为MSC；
			$allParamArr['qnumb'] = 'MSC';
		}
		
	    $reMsg= $orderprint->printTTPpy_ldb_tlp($allParamArr);
		
	    /*
	     //更新打印次数,批量
	     if ($id){
	     $sql = "UPDATE erp_orders SET printTimes = printTimes + 1, orders_status = IF(orders_status = 3, 4, orders_status), orders_print_time = IF(orders_print_time IS NULL, NOW(), orders_print_time)
	     WHERE erp_orders_id=".$id;
	     $this->db->query($sql);
	     $this->db->query("INSERT INTO erp_operate_log(operateUser,operateType,operateMod,operateKey,operateText) VALUES('".$uid."','update','ordersManage','". $id ."','打印DHL-GM(意大利+其他)面单')");
	     }
	    */
	
	    return $reMsg;
	}
}
