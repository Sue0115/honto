<?php
ini_set('memory_limit', '3888M');
set_time_limit(0);
header("Content-type:text/html;Charset=utf-8");
class export_order_data extends Admin_Controller{	
	private $infoArr = array('erp_orders'=>'订单信息','erp_orders_products'=>'订单商品信息');
	//订单状态
	private $orders_status=array(
	    '1'  =>  '新录入',
	    '2'  =>  '不通过',
	    '3'  =>  '已通过',
	    '4'  =>  '已打印',
	    '5'  =>  '已发货',
	    '6'  =>  '已撤单',
	    '7'  =>  '未付款',
	    '8'  =>  '已发货[FBA]',
	    '9'  =>  '预打印'
	);
	//订单平台
	private $orders_typeArr;	
	//是否带电
	private $is_battery = array(
	  '0' => '否',
	  '1' => '是'
	);	
	//模板是否禁用
	private $is_use = array(
	  '1' => '否',
	  '2' => '是'
	);	
	function __construct(){

		parent::__construct();
		$this->load->model(
							array(
								'print/orders_model','out_fileds_model','out_data_template_model',
								'orders_type_model','print/orders_products_model','products/products_data_model',
								'shipment/shipment_model','sangelfine_warehouse_model','country_model','slme_user_model',
								'category_model','order/currency_info_model','print_record_model','base_country_model',
								'caiwu/shipment_cost_model','manages_model','orders_type_model','smt/smt_user_tokens_model','kingdee_currency_model',
								'Kingdee_purchase_user_model','procurement/Procurement_products_model','hs_country_model','wish/wish_user_tokens_model'
							)
		);
		$this->load->library('phpexcel/PHPExcel');
		$this->model = $this->out_fileds_model;
		
		$this->orders_typeArr = $this->orders_type_model->getOrdersType(false);
	}
	
	//用户选择导出字段并生成模板的视图
	public function data_template(){
	  $id = $this->input->get_post('id');
	  $newTemplateInfo = array();
	  if(!empty($id)){//获取模板信息
	  	
	    $templateInfo = $this->out_data_template_model->getTemplateInfo($id);
	    
	    //重组序列化后的数组,以唯一值做键名
		  $newSerialize = array();
		  $otherArr = array();//存放其他的数组
		  $serializeArr = unserialize($templateInfo['serialize_data']);

		  foreach($serializeArr as $ke => $da){
		  	if($da[3]=='other'){
		  	  $otherArr[$ke] = $da;
		  	  continue;
		  	}
		    $newSerialize[$da[0]][] = $ke;
		    $newSerialize[$da[0]][] = $da[1];
		    $newSerialize[$da[0]][] = $da[2];
		    $newSerialize[$da[0]][] = $da[3];
		    $newSerialize[$da[0]][] = $da[4];
		  }
		  $newTemplateInfo[] = $templateInfo['title'];
		  $newTemplateInfo[] = $newSerialize; 
		  $newTemplateInfo[] = $otherArr;
	  }

	  //从导出的字段表中获取数据，并以订单表名为键名重组数据
	  $option = array();
	  $result = $this->model->getAll2array($option);

	  $dataArr = array();//存放重组后的数组
	  foreach($result as $v){
	  	$tableArr = explode('|',$v['table_name']);
	  	unset($v['table_name']);
	  	if($v['read_method']==1){//如果是读取表字段
	  	  $v['table_name'] = $tableArr[0];
	  	}else{
	  	  $v['table_name'] = $tableArr[1];
	  	}
	    $dataArr[$tableArr[0]][] = $v;
	  }
	  $data = array('data' => $dataArr,'infoArr'=>$this->infoArr,'templateInfo'=>$newTemplateInfo,'id'=>$id);
	  
	  $this->_template('admin/export_data/create_template',$data);
	}
	
	//用户选择导出字段并生成模板的逻辑处理
	public function create_template(){
		
	   $modify_id = $this->input->post('modify_id');//判断是修改还是新增

	   $uid = $this->user_info->id;//登录用户id
		
	   $posts = $this->input->post();

	   //重组列与字段的对应关系
	   $orders_data 		= $posts['datas_erp_orders'];//存放erp_orders表的内容
	   $erp_orders			= $posts['erp_orders'];//存放erp_orders列的内容
	   $erp_orders_products = !empty($posts['erp_orders_products']) ? $posts['erp_orders_products'] : '';//存放erp_orders_products列的内容
	   $orders_products 	= !empty($posts['datas_erp_orders_products']) ? $posts['datas_erp_orders_products'] : '';//存放erp_orders_products表的内容
	   $other_title  		= !empty($posts['filed_title']) ? $posts['filed_title'] : '';//其他的标题
	   $other_num  			= !empty($posts['filed_num']) ? $posts['filed_num'] : '';//其他的编号
	   $other_value  		= !empty($posts['filed_value']) ? $posts['filed_value'] : '';//其他的值
	   
	   $new_orders_data = array();
	   if($orders_data !=''){
		   foreach($orders_data as $k => $od){
		   	 $datas = explode('-',$od);
		     if($erp_orders['filed_num'][$k]==''){
		   	    echo '{"info":"模板创建失败，"'.$datas[0].'的列号未填写","status":"n"}';
		   	    exit;
		   	 }
		   	 $datas[] = $erp_orders['filed_name'][$k];
		     $new_orders_data[$erp_orders['filed_num'][$k]] = $datas;
		   }
	   }
	   $new_orders_products_data = array();
	   if($orders_products !=''){
		   foreach($orders_products as $ke => $op){
		   	 $datap = explode('-',$op);
		     if($erp_orders_products['filed_num'][$ke]==''){
		   	    echo '{"info":"模板创建失败，"'.$datap[0].'的列号未填写","status":"n"}';
		   	    exit;
		   	 }
		   	 $datap[] = $erp_orders_products['filed_name'][$ke];
		     $new_orders_products_data[$erp_orders_products['filed_num'][$ke]] = $datap;
		   }
	   }
	   $newOrther = array();
	   if(!empty($other_title)){
		   foreach($other_title as $kes=>$ot){
		     $newOrther[$other_num[$kes]][] = $ot;
		     $newOrther[$other_num[$kes]][] = $other_value[$kes];
		     $newOrther[$other_num[$kes]][] = 0;
		     $newOrther[$other_num[$kes]][] = 'other';
		     $newOrther[$other_num[$kes]][] = '';
		     if($other_num[$kes]==''){
		   	   echo '{"info":"模板创建失败，"'.$ot.'的列号未填写","status":"n"}';
		   	   exit;
		   	 }
		   }
	   }
	   
	   //存放要序列化的数组
	   $serialize_data = array_merge($new_orders_data,$new_orders_products_data,$newOrther);

	   //将数据整理后插入erp_out_data_template表
	   $insert = array();
	   $insert['title'] = $posts['template_name'];
	   $insert['serialize_data'] = serialize($serialize_data);
	   $insert['uid'] = $uid;
	   $insert['createTime'] = date('Y-m-d H:i:s');
	   
	   if($modify_id>0){//修改模板
	   	 $msg = '修改';
	   	 $option = array();
	   	 $where['id'] = $modify_id;
	   	 $option['where'] = $where;
	     $id = $this->out_data_template_model->update($insert,$option);
	   }else{//新增模板
	   	 $msg = '创建';
	     $id = $this->out_data_template_model->add($insert);
	   }
	   
	   if($id>0){
	     echo '{"info":"模板'.$msg.'成功","status":"y"}';
	   }else{
	     echo '{"info":"模板'.$msg.'失败","status":"n"}';
	   }
	   
	   die;
	}
	
	/**
	 * 筛选条件用于数据导出
	 * 
	 */
	public function order_data(){
		
	    $orders_type_arr = $this->orders_type_model->getOrdersType();
	    
	    //导出模板
	    $uid = $this->user_info->id;//登录用户id
	    $templateArr = $this->out_data_template_model->getAllTemplate();
	    $newTemplate = array();
	    foreach($templateArr as $temp){
	      $newTemplate[$temp['id']] = $temp['title'];
	    }

		//找到所有仓库
	    $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
		foreach($warehouse as $va){
			$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
		}
		
		//物流数组
		$shipArr = $this->shipment_model->getAllShipment();
		$shipmentArr = array();
		foreach($shipArr as $sh){
		  $shipmentArr[$sh['shipmentID']] = $sh['shipmentTitle'];
		}

	   $data = array(
	     'warehouse'		 => $warehouseArr,
		 'orders_type_arr'  => $orders_type_arr,
	  	 'templateArr'		=> $newTemplate,
	     'orders_status'    => $this->orders_status,
	     'shipmentArr'      => $shipmentArr    
	   );
	   $this->_template('admin/export_data/order_data',$data);
	}
	/**
	 * 采购订单导出视图
	 */
	public function exprot_purchase_order(){




		//找到所有仓库
		$warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
		foreach($warehouse as $va){
			$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
		}


		$data = array(
			'warehouse'		 => $warehouseArr,

		);
		$this->_template('admin/export_data/export_purchase_order',$data);
	}
	
	/**
	 * 数据模板列表
	 */
	public function template_list(){

	    $templateArr = $this->out_data_template_model->getAllTemplate();
	    
	    $userInfo = $this->slme_user_model->get_all_user_info();

	    $data = array(
	      'templateArr' => $templateArr,
	      'is_use'  => $this->is_use,
	      'userInfo'=> $userInfo
	    );
	    $this->_template('admin/export_data/template_list',$data);
	}
	
	/**
	 * 删除模板数据
	 */
	public function deleteTemplateByID(){
	   $id = $this->input->post('Ids');
	   $option = array();
	   $where = array();
	   $where['id'] = $id;
	   $option = array(
	     'where' => $where
	   );
	   $rows = $this->out_data_template_model->delete($option);
	   if($rows){
	     ajax_return('删除成功', true);
	   }else{
	     ajax_return('删除失败', false);
	   }
	}
	
	/**
	 * 数据导出处理
	 */
	public function deal_data(){
		
	  $posts 		= $this->input->post();
	  $gets = $this->input->get();
	  if(!$posts && $gets){
	  	$posts = $gets;
	  }
	  $template 	= $posts['template'];		//选用的模板id
	 


		//找到所有仓库
	    $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组

		foreach($warehouse as $va){
			$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
		}

	  //数组映射
	  $array = array(
	      'A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E','F'=>'F',
		  'G'=>'G','H'=>'H','I'=>'I','J'=>'J','K'=>'K','L'=>'L',
		  'M'=>'M','N'=>'N','O'=>'O','P'=>'P','Q'=>'Q','R'=>'R',
		  'S'=>'S','T'=>'T','U'=>'U','V'=>'V','W'=>'W','X'=>'X',
		  'Y'=>'Y','Z'=>'Z','AA'=>'AA','AB'=>'AB','AC'=>'AC','AD'=>'AD',
		  'AE'=>'AE','AF'=>'AF','AG'=>'AG','AH'=>'AH','AI'=>'AI','AJ'=>'AJ',
		  'AK'=>'AK','AL'=>'AL','AM'=>'AM','AN'=>'AN','AO'=>'AO','AP'=>'AP',
		  'AQ'=>'AQ','AR'=>'AR','AS'=>'AS','AT'=>'AT','AU'=>'AU','AV'=>'AV',
		  'AW'=>'AW','AX'=>'AX','AY'=>'AY','AZ'=>'AZ','BA'=>'BA','BB'=>'BB','BC'=>'BC','BD'=>'BD'
	  );
	  
	  $orders_type  = $posts['orders_type'];	//订单平台类型
	  
	  $order_status = $posts['orders_status'];	//订单状态
	  
	  $shipmentID   = $posts['shipmentID'];		//订单所属物流id
	  
	  $warehouse 	= $posts['warehouse'];		//订单所属仓库
	  
	  $import_start = $posts['import_start'];	//订单导入开始时间
	  
	  $import_end   = $posts['import_end'];		//订单导入结束时间
	  
	  $ship_start = $posts['ship_start'];		//订单发货开始时间
	  
	  $ship_end = $posts['ship_end'];			//订单导入开始时间
	  
	  $sale_account = !empty($posts['sale_account']) ? $posts['sale_account'] : '';   //销售账号
	  
	  $smt_account = !empty($posts['account']) ? $posts['account'] : '';//smt和wish销售账号
	  
	  $options = array();
	   
	  $where = array();
	  
	  $where['orders_is_join'] = 0;

	  if(!empty($orders_type)){
	    $where['orders_type'] = $orders_type;
	  }
	  if(!empty($order_status)){
	    $where['orders_status'] = $order_status;
	  }
	  if(!empty($shipmentID)){
	    $where['shipmentAutoMatched'] = $shipmentID;
	  }
	  if(!empty($warehouse)){
	    $where['orders_warehouse_id'] = $warehouse;
	  }
	  if(!empty($sale_account)){
	    $where['sales_account'] = $sale_account;
	  }
	  if($orders_type==6 && $smt_account != ''){
	     $where['sales_account'] = $smt_account;
	  }
	  if($orders_type==13 && $smt_account != ''){
	     $where['sales_account'] = $smt_account;
	  }
	  if(!empty($import_start)){
	    $where['orders_export_time >='] = $import_start;
	  }
	  if(!empty($import_end)){
	    $where['orders_export_time <'] = $import_end;
	  }
	  if(!empty($ship_start)){
	    $where['orders_shipping_time >='] = $ship_start;
	  }
	  if(!empty($ship_end)){
	    $where['orders_shipping_time <'] = $ship_end;
	  }


	  $options['where'] = $where;

		
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		
 		$cacheSettings = array ('memoryCacheSize' => '3888MB' );
		PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod,$cacheSettings );

		$phpExcel=new PHPExcel();
		$templateInfo = $this->out_data_template_model->getTemplateInfo($template);
		
		if($templateInfo['type']==2&&($templateInfo['serialize_data']=='exportSalesOrder')){

			$this->$templateInfo['serialize_data']($phpExcel);
			exit;
		}

		
		
		if(!empty($posts['clearance_start_time']) && !empty($posts['clearance_end_time'])){
		    $options['select'] = array("erp_orders.*","p.clearance_time");
		    $join[] = array('erp_order_memo p',"p.erp_orders_id=erp_orders.erp_orders_id");
		    $options['join'] = $join;
		    $options['where']['p.clearance_time >='] = $posts['clearance_start_time'];
		    $options['where']['p.clearance_time <'] = $posts['clearance_end_time'];
		}

		
	  $resultArr = $this->orders_model->getAll2array($options);
//            echo $this->db->last_query();echo "<hr/>";exit();
	  //根据模板id获取模板信息
	  $templateInfo = $this->out_data_template_model->getTemplateInfo($template);
	  if($templateInfo['type']==2){
	     //type=2,读取函数导表,type=1读取序列话的模板字段
	     $this->$templateInfo['serialize_data']($resultArr,$phpExcel);
	     exit;
	  }
	  
	  $filedsArr = unserialize($templateInfo['serialize_data']);//需要导出的字段和生成excel的列

	  $order_products_arr=array();//存储属于erp_orders_products的信息

	  $key_num = array();

      foreach($filedsArr as $k => $v){
      	if(isset($v[3]) && $v[3]!='erp_orders_products' || $v[2]==2){//不是sku信息的字段，设置标题
	      	$key_num[]=ord($k);
	      	$phpExcel->getActiveSheet()->setCellValue($k.'1', !empty($v[4]) ? $v[4] : $v[0]);
      	}else{//存放sku的数组
      	   $order_products_arr[] = $v;
      	}
      }
      $sku_start = max($key_num)+1;//sku信息开始列数，十进制
      
	  $allParamArr = array();
	  
	  $newFiledsArr = array();//存放最终生成的字段数组
	  
	  $sku_countArr = array();//存放每个订单sku的个数

	  foreach($resultArr as $orderInfo){//主要获取sku的最大个数
	     //订单信息
		$allParamArr['erp_orders']      = $orderInfo;	
		//订单中的产品信息
		$allParamArr['erp_orders_products']    = $this->orders_products_model->getAllProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
		$sku_count = count($allParamArr['erp_orders_products']);
		$sku_countArr[] = $sku_count;
	  }
	  $sku_max = max($sku_countArr);//最多sku的个数

	  //设置sku信息标题
	  for($is=1;$is<=$sku_max;$is++){
			$iss = $is-1;
			
		    foreach($order_products_arr as $va){
		    	
			     if($sku_start>116){//限制Az以后的列不再写入数据
				  break;
				 }
				 
				 if($sku_start>90){
				     $start_file = 'A'.chr($sku_start-26);
				 }else{
				     $start_file = chr($sku_start);
				 }
				 //echo $array[$start_file].'-'.$va[4].'<br/>';
				 $phpExcel->getActiveSheet()->setCellValue($array[$start_file].'1', !empty($va[4]) ? $va[4].$is : $va[0].$is);//设置标题
				 $sku_start++;
			}  
	  }

	  $i=2;
	  foreach($resultArr as $ks => $orderInfo){
	  	
	  	//订单数据=的处理
	  	$dealResult = $this->data_replace($orderInfo);
	  	
	     //订单信息
		$allParamArr['erp_orders']      = $dealResult;
			
		//订单中的产品信息
		$allParamArr['erp_orders_products']    = $this->orders_products_model->getAllProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	
		foreach($filedsArr as $key => $value){
			
		  if($value[3]=='erp_orders'){//订单信息的导出部分
		  	
		   if($value[1]=='orders_status'){
		  		$phpExcel->getActiveSheet()->setCellValue($key . $i, $this->orders_status[$allParamArr[$value[3]][$value[1]]]); 
		  		continue;
		  	}
		    $phpExcel->getActiveSheet()->setCellValue($key . $i, $allParamArr[$value[3]][$value[1]]);

		    
		  }elseif($value[3]=='other'){//其他导出部分
		     $phpExcel->getActiveSheet()->setCellValue($key . $i, $value[1]);
		     
		     
		  }elseif($value[2]==2){//如果是读取函数
		  	 if($value[3]=='get_sequenced'){
		  	   $readValue=$ks+1;
		  	 }else{
		  	   $readValue = $this->$value[3]($allParamArr);
		  	 }
		    
		     $phpExcel->getActiveSheet()->setCellValue($key . $i, $readValue);
		  }
		  
		}
		
		  $sku_start = max($key_num)+1;//sku信息开始的列，十进制

		  for($ii=1;$ii<=$sku_max;$ii++){//设置sku的信息
		       $iss = $ii-1;

		       foreach($order_products_arr as $val){//设置值
		       	
			       if($sku_start>116){//限制Az以后的列不再写入数据
					  break;
				   }
		       	
			       if($sku_start>90){
			          $start_file = 'A'.chr($sku_start-26);
			       }else{
			         $start_file = chr($sku_start);
			       }
			       $cellValue = !empty($allParamArr['erp_orders_products'][$iss][$val[1]]) ? $allParamArr['erp_orders_products'][$iss][$val[1]] : '';
			      
			       $phpExcel->getActiveSheet()->setCellValue($array[$start_file]. $i, $cellValue);//设置对应的数据
			      
			       $sku_start++;  
		     }
		  
		  }
		 
		  
		$i++;
	  }
	  
	  $phpExcel->getActiveSheet ()->setTitle ( 'order_data' );
	  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	  header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
	  header('Cache-Control: max-age=0');
	  $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
	  $objWriter->save('php://output');
	  die;
	 
	  
	}
	
	//获取ladaza的itemid
	public function getItemID($allParamArr){
	  $item = '';
	  if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $item=$allParamArr['erp_orders_products'][0]['orderlineitemid'];
	  }
	  return $item;
	}
	
	//获取sku的组合信息
	public function getSkuInfo($allParamArr){
		$skuInfo = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
			foreach($allParamArr['erp_orders_products'] as $eop){
			  $sku = $eop['orders_sku'];
			  $skuCount = $eop['item_count'];
			  $skuInfo .= ','.$sku.'*'.$skuCount;
			}
			$skuInfo = substr($skuInfo,1);
		}
	
	    return $skuInfo;
	}
	
	//返回第一个sku名称
	public function getFirstSkuName($allParamArr){
		$skuFirst = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $skuFirst=$allParamArr['erp_orders_products'][0]['orders_sku'];
		}
		return $skuFirst;
	}
	
	//返回第一个sku数量
	public function getFirstSkuCount($allParamArr){
		$skuFirstCount = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $skuFirstCount=$allParamArr['erp_orders_products'][0]['item_count'];
		}
		return $skuFirstCount;
	}
	
	//返回第一个sku单价
	public function getFirstSkuPrice($allParamArr){
		$skuFirstPrice = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $skuFirstPrice=$allParamArr['erp_orders_products'][0]['item_price'];
		}
		return $skuFirstPrice;
	}
	
	//返回第一个sku英文申报名称
	public function getFirstSkuDeclaredEn($allParamArr){
		$SkuDeclaredEn = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $SkuDeclaredEn=$allParamArr['erp_orders_products'][0]['products_declared_en'];
		}
		return $SkuDeclaredEn;
	}
	
	//返回第一个sku英文申报名称
	public function getFirstSkuDeclaredCn($allParamArr){
		$SkuDeclaredCn = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $SkuDeclaredCn=$allParamArr['erp_orders_products'][0]['products_declared_cn'];
		}
		return $SkuDeclaredCn;
	}
	
	//返回第一个sku的海关编码
	public function getFirstSkuHscode($allParamArr){
		$skuFirstHscode = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $skuFirstHscode=$allParamArr['erp_orders_products'][0]['product_hscode'];
		}
		return $skuFirstHscode;
	}
	
	//返回第一个sku申报价值
	public function getFirstSkuDeclaredValue($allParamArr){
		$SkuDeclaredValue = '';
		if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
		  $SkuDeclaredValue=$allParamArr['erp_orders_products'][0]['products_declared_value'];
		}
		return $SkuDeclaredValue;
	}
	
	//根据国家英文名称获取国家中文名称
	public function getCountryCn($allParamArr){
	   $country_en = !empty($allParamArr['erp_orders']['buyer_country']) ? $allParamArr['erp_orders']['buyer_country'] : $allParamArr['erp_orders']['buyer_country_code'];
	   $countryInfo = $this->country_model->getCountryByEN($country_en);
	   $country_cn = !empty($countryInfo) ? $countryInfo['country_cn'] : '';
	  return $country_cn;
	}
	
	//国家英文简称
	public function getCountryCode($allParamArr){
	   $countryCode = '';
	   if(!empty($allParamArr['erp_orders']['buyer_country_code'])){
	     $countryCode = $allParamArr['erp_orders']['buyer_country_code'];
	   }
	   return $countryCode;
	}
	
	//数据=号替换
	public function data_replace($orderInfo){
		//处理邮箱
		if(!empty($orderInfo['buyer_email'])){
		  $buyer_mail = str_replace('=','',$orderInfo['buyer_email']);
		  $orderInfo['buyer_email'] = $buyer_mail;
		}
		//处理地址1
		if(!empty($orderInfo['buyer_address_1'])){
		  $buyer_addresss_1 = str_replace('=','',$orderInfo['buyer_address_1']);
		  $orderInfo['buyer_address_1'] = $buyer_addresss_1;
		}
		//处理地址2
		if(!empty($orderInfo['buyer_address_2'])){
		  $buyer_addresss_2 = str_replace('=','',$orderInfo['buyer_address_2']);
		  $orderInfo['buyer_address_2'] = $buyer_addresss_2;
		}
		return $orderInfo;	
	}
	
	//国家英文全称
	public function getCountryEn($allParamArr){
	  $country_en = '';
	  if(!empty($allParamArr['erp_orders']['buyer_country'])){
	     $country_en = $allParamArr['erp_orders']['buyer_country'];
	     //根据国家英文简称获取全称
	     $resultInfo = $this->base_country_model->getCountryInfoByCode($allParamArr['erp_orders']['buyer_country_code']);
	     if(!empty($resultInfo)){
	       $country_en = $resultInfo['country_en'];
	     }
	  }
	  return $country_en;
	}
	
	//国家中文或者英文拼接
	public function getCountryString($allParamArr){
	   $data = '';
	   $buyer_country = !empty($allParamArr['erp_orders']['buyer_country_code']) ? $allParamArr['erp_orders']['buyer_country_code'] : $allParamArr['erp_orders']['buyer_country'];
	   $data = $buyer_country.' country';
	   return $data;
	}
	
	//获取订单sku总数
	public function getSkuCount($allParamArr){
	   $total_count = 0;
	   foreach($allParamArr['erp_orders_products'] as $pd){
	     $total_count += $pd['item_count'];
	   }
	   return $total_count;
	}
	
	//收件人地址拼接
	public function getAddress($allParamArr){
	  $buyer_address = $allParamArr['erp_orders']['buyer_address_1'].' '.$allParamArr['erp_orders']['buyer_address_2'];
	  return $buyer_address;
	}
	
	//订单总重量
	public function getOrderTotalWeight($allParamArr){
	  $total_weight = 0;
	  foreach($allParamArr['erp_orders_products'] as $pT){
	     $total_weight += $pT['item_count']*$pT['products_weight'];
	  }
	  return $total_weight;
	}

	//订单实际运费，从erp_shipment_cost 表获取
	public function get_real_shippment_cost($allParamArr,$type=1){

		$cost = '';

		if($type==2){
		   $data = $this->shipment_cost_model->get_one_by_orders_id($allParamArr['erp_orders_id']);	
		}else{
		   $data = $this->shipment_cost_model->get_one_by_orders_id($allParamArr['erp_orders']['erp_orders_id']);	
		}

		if($data && $data['cost'] > 0){
			$cost = $data['cost'];
		}

		return $cost;
	}
	
	//根据用户id获取用户姓名
	public function getShippUserName($allParamArr){
		$name = '';
	    $uid = $allParamArr['erp_orders']['orders_shipping_user'];
	    $userInfo = $this->slme_user_model->getInfoByUid($uid);
	    $name = !empty($userInfo) ? $userInfo['nickname'] : '';
	    return $name;
	}
	
	//根据物流id获取物流中文名称
	public function getShipName($allParamArr){
	   $shipName = '';
	   $shipmentInfo = $this->shipment_model->getInfoById($allParamArr['erp_orders']['shipmentAutoMatched']);
	   $shipName = !empty($shipmentInfo)? $shipmentInfo['shipmentTitle'] : '';
	   return $shipName;
	}
	
	//判断是否带电
	public function getIsBattery($allParamArr){
	   $result = '';
	   if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
	     $result = $this->is_battery[$allParamArr['erp_orders_products'][0]['products_with_battery']];
	   }
	   return $result;
	}
	
	//第一个产品的中文分类名称
	public function getProductCnSort($allParamArr){
	  $category_cn = '';
	  if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
	    $categoryInfo = $this->category_model->getCateInfoById($allParamArr['erp_orders_products'][0]['products_sort']);
	    $category_cn = !empty($categoryInfo) ? $categoryInfo['category_name'] : '';
	  }
	  return $category_cn;
	}
	
	//第一个产品的英文分类名称
	public function getProductEnSort($allParamArr){
	  $category = '';
	  if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
	    $categoryInfo = $this->category_model->getCateInfoById($allParamArr['erp_orders_products'][0]['products_sort']);
	    $category = !empty($categoryInfo) ? $categoryInfo['category_name_en'] : '';
	  }
	  return $category;
	}
	
	//获取订单平台
	public function getOrderType($allParamArr){
	  $orderType = '';
	  $orderType = $this->orders_typeArr[$allParamArr['erp_orders']['orders_type']];
	  return $orderType;
	}
	
	
	
	//导出中英专线
	public function explodeCNToUKTables($resultArr,$phpExcel){
		
	   $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	   $cacheSettings = array ('memoryCacheSize' => '512MB' );
	   PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	   
	   $titleArr = array(
	     'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R',
		  'S','T','U','V','W','X','Y','Z'
	   );
		//设置excel标题
		$excelFieldNameArray = array(
				'ROWID',
				'HAWB NO.: ',
				'Consignee_Name',
				'Recipient\'s address1',
				'Recipient\'s address2',
				'Recipient\'s address3',
				'Postcode',
				'COUNTRY',
				'Item description',
				'Commercial value',
				'weight(KG)',
				'CARTON NO.',
				'Supplier\'s Name ',
				'Web Link',
				'HS Code'
		);
		foreach($excelFieldNameArray as $key => $excel){
		    $phpExcel->getActiveSheet()->setCellValue($titleArr[$key] . '1', $excel);
		}
		
		$linkList = array(
			array('price' => 4.69, 'url' => 'http://www.ebay.com/itm/380959253833?ru=http%3A%2F%2Fwww.ebay.com%2Fsch%2Fi.html%3F_sacat%3D0%26_from%3DR40%26_nkw%3D380959253833%26_rdc%3D1'),
			array('price' => 3.98, 'url' => 'http://www.ebay.com/itm/Candy-7-Colors-Women-Chiffon-Long-Sleeve-Shirt-Party-Top-Blouse-T-Shirt-S-4XL-/151330177762?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 1.59, 'url' => 'http://www.ebay.com/itm/Korean-Women-Chiffon-Floral-Vest-Tank-Top-Shirt-Loose-T-shirts-Blouse-8-Styles-/380959262208?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 2.15, 'url' => 'http://www.ebay.com/itm/Summer-New-Womens-Colorful-Casual-Soft-Chiffon-Batwing-Loose-Blouse-T-Shirt-Tops-/151304217345?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 2.59, 'url' => 'http://www.ebay.com/itm/Lovely-New-Womens-Floral-Blouse-Sheer-Top-Casual-Batwing-Short-Sleeve-T-Shirt-/151364198652?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 4.25, 'url' => 'http://www.ebay.com/itm/New-Women-Casual-V-Neck-Chiffon-T-Shirt-Minimalist-Half-Sleeve-Loose-Top-Blouse-/380913340926?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 4.45, 'url' => 'http://www.ebay.com/itm/New-Fashion-Summer-Womens-Chiffon-Flouncing-Sleeveless-Shirt-Blouse-Tops-T-shirt-/151303373839?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 5.69, 'url' => 'http://www.ebay.com/itm/2014-Summer-Womens-V-Neck-Short-Sleeve-Loose-T-shirt-Tops-Blouse-Casual-3-Color-/151300582416?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 1.99, 'url' => 'http://www.ebay.com/itm/FREE-Ladies-Casual-Chiffon-Top-Blouse-Sheer-Batwing-Short-Sleeve-Loose-T-Shirt-/380931438097?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined'),
			array('price' => 5.99, 'url' => 'http://www.ebay.com/itm/Women-Fashion-V-Neck-Batwing-Sleeve-Loose-T-Shirt-Top-False-Two-Piece-Blouse-/380921971729?tfrom=380959253833&tpos=left&ttype=price&talgo=undefined')
		);
		
		foreach($resultArr as $k => $re){
			
		
		  $i = $k+2;
	   	
	   	  $allParamArr = array();
	   	
	      //订单信息
		  $allParamArr['erp_orders']      = $re;
			
		  //订单中的产品信息
		  $allParamArr['erp_orders_products']    = $this->orders_products_model->getAllProdcutList($re['erp_orders_id'], $re['orders_warehouse_id']);

		  $products_declared_en = '';//英文申报名称
		  
		  $total_weight = 0;//订单总重量
		  
		  if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
			  foreach($allParamArr['erp_orders_products'] as $op){
			     if(empty($products_declared_en)){
			       $products_declared_en = $op['products_declared_en'];
			     }
			     $total_weight += $op['item_count']*$op['products_weight'];
			  }
		  }
		  
		  $randKey = array_rand($linkList);

		  $phpExcel->getActiveSheet()->setCellValue('A'.$i, $k+1);
		  $phpExcel->getActiveSheet()->setCellValue('B'.$i, 'S'.$allParamArr['erp_orders']['erp_orders_id']);
		  $phpExcel->getActiveSheet()->setCellValue('C'.$i, $allParamArr['erp_orders']['buyer_name']);
		  $phpExcel->getActiveSheet()->setCellValue('D'.$i, $allParamArr['erp_orders']['buyer_address_1']);
		  $phpExcel->getActiveSheet()->setCellValue('E'.$i, $allParamArr['erp_orders']['buyer_address_2']);
		  $phpExcel->getActiveSheet()->setCellValue('F'.$i, $allParamArr['erp_orders']['buyer_city'].' '.$allParamArr['erp_orders']['buyer_state']);
		  $phpExcel->getActiveSheet()->setCellValue('G'.$i, $allParamArr['erp_orders']['buyer_zip'],PHPExcel_Cell_DataType::TYPE_STRING);
		  $phpExcel->getActiveSheet()->setCellValue('H'.$i, 'UK');
		  $phpExcel->getActiveSheet()->setCellValue('I'.$i, $products_declared_en);
		  $phpExcel->getActiveSheet()->setCellValue('J'.$i, $linkList[$randKey]['price']);
		  $phpExcel->getActiveSheet()->setCellValue('K'.$i, $total_weight);
		  $phpExcel->getActiveSheet()->setCellValue('L'.$i, '');
		  $phpExcel->getActiveSheet()->setCellValue('M'.$i, '');
		  $phpExcel->getActiveSheet()->setCellValue('N'.$i, $linkList[$randKey]['url']);
		  $phpExcel->getActiveSheet()->setCellValue('O'.$i, '');
	    }
		$phpExcel->getActiveSheet ()->setTitle ( 'CNTOUKshipment' );
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
	   
	}
	
	//导出德国邮政发货清单
	public function explodeForGermanyPost($resultArr,$phpExcel){
	   $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	   $cacheSettings = array ('memoryCacheSize' => '512MB' );
	   PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	
	   //设置标题
	    $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
        $phpExcel->setActiveSheetIndex(0)->mergeCells('A1:L1')->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $phpExcel->getActiveSheet()->setCellValue('A1', 'LOW VALUE MANIFEST DESTINATION GERMANY')
        ->setCellValue('A2', 'Origin:')
        ->setCellValue('B2', 'HKG')
        ->setCellValue('C2', 'Date:')
        ->setCellValue('D2', date('Y-m-d'))
        ->setCellValue('H2', 'Destination:')
        ->setCellValue('I2', 'Germany')
        ->setCellValue('A3', 'Total items:')
        ->setCellValue('B3', count($resultArr))
        ->setCellValue('C3', 'Box Number:')
        ->setCellValue('D3', '1')
        ->setCellValue('H3', 'Total Weight:')
        ->setCellValue('A6', 'Item')
        ->setCellValue('B6', 'Shipper')
        ->setCellValue('C6', 'Receiver Name')
        ->setCellValue('D6', 'Street + Nr.')
        ->setCellValue('E6', 'Postcode')
        ->setCellValue('F6', 'City')
        ->setCellValue('G6', 'COO')
        ->setCellValue('H6', 'Pces')
        ->setCellValue('I6', 'Weight (g)')
        ->setCellValue('J6', 'Item value')
        ->setCellValue('K6', 'Shipping cost')
        ->setCellValue('L6', 'Total value')
        ->setCellValue('M6', 'Curr.')
        ->setCellValue('N6', 'Commodity')
        ->setCellValue('O6', 'Inv No.')
        ->setCellValue('P6', 'Selling ID')
        ->setCellValue('Q6', 'Tracking no.');
        
        $totalWeight = 0;//所有订单的总重量
        
        $rate = $this->currency_info_model->getValueByID(7);

        foreach($resultArr as $k=>$re){
        	
        	$i = $k+7;
        	
        	$allParamArr = array();
        	
	        //订单信息
		   $allParamArr['erp_orders']      = $re;
		   //订单中的产品信息
		   $allParamArr['erp_orders_products']    = $this->orders_products_model->getAllProdcutList($re['erp_orders_id'], $re['orders_warehouse_id']);
		   
		   $total_weight = 0;
		   $total_count = 0;//订单sku总数
		   $products_declared_en = '';//英文申报名称第一件产品的
		   $orders_item_number = '';//第一个产品的产品ID

        	if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
				foreach($allParamArr['erp_orders_products'] as $op){
				   $total_count += $op['item_count'];
				   $total_weight += $op['item_count']*$op['products_weight'];
				}
				$products_declared_en = $allParamArr['erp_orders_products'][0]['products_declared_en'];
				$orders_item_number = $allParamArr['erp_orders_products'][0]['orders_item_number'];
			}
		  $totalWeight += $total_weight;
		  
		  $perPrice = ($total_count==0) ? 0 : ($allParamArr['erp_orders']['orders_total'] - $allParamArr['erp_orders']['orders_ship_fee']) / $total_count;
          $perPrice = $allParamArr['erp_orders']['currency_type'] == 'EUR' ? number_format($perPrice, 2) : number_format($perPrice/$allParamArr['erp_orders']['currency_value'] * $rate, 2);
          //运费
          $ship_fee = $allParamArr['erp_orders']['currency_type'] == 'EUR' ? $allParamArr['erp_orders']['orders_ship_fee'] : ($allParamArr['erp_orders']['orders_ship_fee'] / $allParamArr['erp_orders']['currency_value'] * $rate);
          $orders_total = $perPrice * $total_count + $ship_fee;

            if($orders_total > 20){
                $orders_total = 20;
                $perPrice = round((($orders_total-$ship_fee)/$total_count),2);
            }

            $orders_total = $perPrice * $total_count + $ship_fee;
		  
		  
		  $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, ($k + 1))
            ->setCellValue('B' . $i, 'Shenzhen Salamoer Co.Ltd')
            ->setCellValue('C' . $i, $allParamArr['erp_orders']['buyer_name'])
            ->setCellValue('D' . $i, $allParamArr['erp_orders']['buyer_address_1'].' '.$allParamArr['erp_orders']['buyer_address_2'])
            ->setCellValueExplicit('E' . $i, $allParamArr['erp_orders']['buyer_zip'], PHPExcel_Cell_DataType::TYPE_STRING)
            ->setCellValue('F' . $i, $allParamArr['erp_orders']['buyer_city'])
            ->setCellValue('G' . $i, 'China')
            ->setCellValue('H' . $i, $total_count)
            ->setCellValue('I' . $i, $total_weight * 1000)
            ->setCellValue('J' . $i, $perPrice)
            ->setCellValue('K' . $i, $ship_fee)//运费
            ->setCellValue('L' . $i, $orders_total)
            ->setCellValue('M' . $i, 'EUR') //币种
            ->setCellValue('N' . $i, $products_declared_en)//产品名称,用第一个产品的申报名称
            ->setCellValue('O' . $i, $allParamArr['erp_orders']['erp_orders_id']) //发票号码
            ->setCellValueExplicit('P' . $i, $orders_item_number, PHPExcel_Cell_DataType::TYPE_STRING)//写第一个ItemNumber
            ->setCellValueExplicit('Q' . $i, $allParamArr['erp_orders']['orders_shipping_code'], PHPExcel_Cell_DataType::TYPE_STRING)
			;

        }
        
        $phpExcel->getActiveSheet()->setCellValue('I3', ($totalWeight * 1000).' g');
        $phpExcel->getActiveSheet ()->setTitle ( 'mainfest' );
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
		
	}
	
	//比利时邮政发货清单
	public function explodeShippedForBLS($resultArr,$phpExcel){
	   $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	   $cacheSettings = array ('memoryCacheSize' => '512MB' );
	   PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	
	   //设置标题
	    $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
	    $objActSheet = $phpExcel->getActiveSheet();
        $phpExcel->setActiveSheetIndex( 0 )->setCellValue( 'A1', 'SKU' )
        ->setCellValue( 'B1', 'Chinese content description' )
        ->setCellValue( 'C1', 'Order Number' )
        ->setCellValue( 'D1', 'Product barcode' )
        ->setCellValue( 'E1', 'Recipient name' )
        ->setCellValue( 'F1', 'Recipient street' )
        ->setCellValue( 'G1', 'Recipient housenumber' )
        ->setCellValue( 'H1', 'Recipient busnumber' )
        ->setCellValue( 'I1', 'Recipient zipcode' )
        ->setCellValue( 'J1', 'Recipient city' )
        ->setCellValue( 'K1', 'Recipient state' )
        ->setCellValue( 'L1', 'Recipient country' )
        ->setCellValue( 'M1', 'Item content' )
        ->setCellValue( 'N1', 'Item count' )
        ->setCellValue( 'O1', 'Value' )
        ->setCellValue( 'P1', 'Currency' )
        ->setCellValue( 'Q1', 'Weight' );
        
        $totalWeight = 0;//所有订单的总重量
        

        foreach($resultArr as $k=>$re){
        	
        	$i = $k+2;
        	
        	$allParamArr = array();
        	
	        //订单信息
		   $allParamArr['erp_orders']      = $re;
		   //订单中的产品信息
		   $allParamArr['erp_orders_products']    = $this->orders_products_model->getAllProdcutList($re['erp_orders_id'], $re['orders_warehouse_id']);
		   
		   //获得该订单的打印记录,erp_print_record
		   $print_record = $this->print_record_model->getInfoByID($allParamArr['erp_orders']['erp_orders_id']);
		   $value = '';
		   if(!empty($print_record)){
		     $dataArr = explode(',',$print_record['print_remark']);
		     $value = $dataArr[1];
		   }

		   $sku = '';//第一个sku
		   $products_declared_en = '';//第一个sku的英文申报名称
		   $products_declared_cn = '';//第一个sku的中文申报名称
		   $item_count = '';//第一个sku 的数量
		   $total_weight = 0;//订单总重量
        	if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
				foreach($allParamArr['erp_orders_products'] as $op){
				   $total_weight += $op['item_count']*$op['products_weight'];
				}
				$products_declared_en = $allParamArr['erp_orders_products'][0]['products_declared_en'];
				$products_declared_cn = $allParamArr['erp_orders_products'][0]['products_declared_cn'];
				$sku = $allParamArr['erp_orders_products'][0]['orders_sku'];
				$item_count = $allParamArr['erp_orders_products'][0]['item_count'];
			}
		
		   $phpExcel->setActiveSheetIndex( 0 )
		        ->setCellValue( 'A' . $i, $sku )
                ->setCellValue( 'B' . $i, $products_declared_cn.$products_declared_en) //中文标题 --改成申报名称 suwei20140919
                ->setCellValue( 'C' . $i, $allParamArr['erp_orders']['erp_orders_id'] )
                ->setCellValue( 'E' . $i, $allParamArr['erp_orders']['buyer_name'] )
                ->setCellValue( 'F' . $i, $allParamArr['erp_orders']['buyer_address_1'] . ' ' . $allParamArr['erp_orders']['buyer_address_2'] )
                ->setCellValue( 'G' . $i, '' ) 
                ->setCellValue( 'H' . $i, '' )
                ->setCellValue( 'J' . $i, $allParamArr['erp_orders']['buyer_city'] )
                ->setCellValue( 'K' . $i, $allParamArr['erp_orders']['buyer_state'] )
                ->setCellValue( 'L' . $i, $allParamArr['erp_orders']['buyer_country_code'] )
                ->setCellValue( 'M' . $i, $products_declared_en) //手动
                ->setCellValue( 'N' . $i, $item_count)
                ->setCellValue( 'O' . $i, $value ) //手动
                ->setCellValue( 'P' . $i, 'EUR')
                ->setCellValue( 'Q' . $i, $total_weight * 1000 ) //包裹重量
                    ;
                $shipCoce = $objActSheet->getCell( 'D' . $i );
                $zip      = $objActSheet->getCell( 'I' . $i );
                $shipCoce->setValueExplicit( $allParamArr['erp_orders']['orders_shipping_code'], PHPExcel_Cell_DataType::TYPE_STRING );
                $zip->setValueExplicit( $allParamArr['erp_orders']['buyer_zip'], PHPExcel_Cell_DataType::TYPE_STRING );
        }
        
        $phpExcel->getActiveSheet ()->setTitle ( 'OrdersList' );
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
		
	}
	
	//ajax获取smt账号
	public function getSmtAccount(){
	   $orders_type = $this->input->post('type');
	   if($orders_type==6){
	     $accountArr = $this->smt_user_tokens_model->formatSmtTokenList(array());
	   }else{
	     $accountArr = $this->wish_user_tokens_model->getWishTokenList(array());
	   }

	   echo json_encode($accountArr);
	   exit;
	  
	}
	
	//自主申报表格
	public function explodeOwnselfTable($resultArr,$phpExcel){
		
	  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	  $cacheSettings = array ('memoryCacheSize' => '512MB' );
	  PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	  $shipmentArr = $this->shipment_model->getAllShipment(2);

	   //设置标题
	   $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
       $phpExcel->getActiveSheet()
        ->setCellValue('A1', '原始订单编号')
        ->setCellValue('B1','物流企业代码')
        ->setCellValue('C1', '物流企业运单号')
        ->setCellValue('D1', '中文申报名称')
        ->setCellValue('E1', '计量单位')
        ->setCellValue('F1', '商品数量')
        ->setCellValue('G1', '币制代码')
        ->setCellValue('H1', '商品总价')
        ->setCellValue('I1', '不含税采购成本')
        ->setCellValue('J1', '订单商品货款')
        ->setCellValue('K1', '订单商品运费')
        ->setCellValue('L1', '收货人名称')
        ->setCellValue('M1', '收货地址')
        ->setCellValue('N1', '收货人电话')
        ->setCellValue('O1', '收货人所在国家')
        ->setCellValue('P1', '企业商品货号')
        ->setCellValue('Q1', '付款时间')
        ->setCellValue('R1', '打印时间')
        ->setCellValue('S1','发货时间')
        ->setCellValue('T1', '付款方式')
        ->setCellValue('U1', 'Email地址')
        ->setCellValue('V1', 'Erp运费')
        ->setCellValue('W1', '计费运费')
        ->setCellValue('X1', '结算运费')
        ->setCellValue('Y1', '通关审结')
        ->setCellValue('Z1', '物流名称')
        ->setCellValue('AA1', '订单重量');
	  
      $i=2;
      
	  //循环订单表获取订单产品
	  foreach($resultArr as $orderInfo){
	  	
	  	$productsInfo = array();
	  	
	    $productsInfo = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	    
		$skuCount = 0;
		
		$skuCount = count($productsInfo);
		
	    if(empty($productsInfo)){
	      continue;
	    }
	    
	    //去掉属于快悦挂号物流的数据
	    if($orderInfo['shipmentAutoMatched']==402){
	      continue;
	    }
	    
	    //获取国家中文
	    $country_en = !empty($orderInfo['buyer_country']) ? $orderInfo['buyer_country'] : $orderInfo['buyer_country_code'];
	    $countryInfo = $this->country_model->getCountryByEN($country_en);
	    $country_cn = !empty($countryInfo) ? $countryInfo['country_cn'] : '';

	    //计费运费
	    $ji_shippingCost = 0;
	    $ji_shippingCost = $this->get_real_shippment_cost($orderInfo,2);
	    
	    //结算运费
	    $jie_cost = $ji_shippingCost==0 ? $orderInfo['shippingCost'] : $ji_shippingCost;
	    
	    $erpOrdersId = '';
	    $total_weight = 0;
	    foreach($productsInfo as  $pd){
	      $total_weight += $pd['item_count']*$pd['products_weight'];
	    }
	    foreach($productsInfo as $k => $pf){
	    	
	       $skuPrice = $pf['item_count']*$pf['item_price'];
	       
	       //商品总价
//	       if($k<1){
//	         $itemTotalPrice = ($skuPrice-$orderInfo['orders_ship_fee']);
//	       }else{
//	         $itemTotalPrice = $skuPrice;
//	       }
		   $each_sku_ship_fee = 0;//每个商品的运费
	       
	       $each_sku_ship_fee = $orderInfo['orders_ship_fee']/$skuCount;
	       
	       //商品总价
	       $itemTotalPrice = ($skuPrice+$each_sku_ship_fee);
		   
	       if($pf['erp_orders_id']==$erpOrdersId){
	           $orderInfo['orders_total'] = 0;
	           $orderInfo['shippingCost'] = 0;
	           
	           $jie_cost = 0;
	           $total_weight = 0;
	       }
	       $erpOrdersId = $pf['erp_orders_id'];
	       
	       $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $orderInfo['erp_orders_id'].'-S')
            ->setCellValue('B' . $i, $orderInfo['shipmentAutoMatched'])
            ->setCellValue('C' . $i, $orderInfo['orders_shipping_code'])
            ->setCellValue('D' . $i, $pf['products_declared_cn'])
            ->setCellValue('E' . $i, $pf['products_unit1'])
            ->setCellValue('F' . $i, $pf['item_count'])
            ->setCellValue('G' . $i, $orderInfo['currency_type'])
            ->setCellValue('H' . $i, $itemTotalPrice)
            ->setCellValue('I' . $i, $pf['products_value'])
            ->setCellValue('J' . $i, $orderInfo['orders_total'])
            ->setCellValue('K' . $i, '0')
            ->setCellValue('L' . $i, $orderInfo['buyer_name'])
            ->setCellValue('M' . $i, $orderInfo['buyer_address_1'].' '.$orderInfo['buyer_address_2'])
            ->setCellValue('N' . $i, $orderInfo['buyer_phone'])
            ->setCellValue('O' . $i, $country_cn)
            ->setCellValue('P' . $i, $pf['orders_sku'])
            ->setCellValue('Q' . $i, $orderInfo['orders_paid_time'])
            ->setCellValue('R' . $i, $orderInfo['orders_print_time'])
            ->setCellValue('S' . $i, $orderInfo['orders_shipping_time'])
            ->setCellValue('T' . $i, $orderInfo['pay_method'])
            ->setCellValue('U' . $i, $orderInfo['buyer_email'])
            ->setCellValue('V' . $i, $orderInfo['shippingCost'])
            ->setCellValue('W' . $i, $ji_shippingCost)
            ->setCellValue('X' . $i, $jie_cost)
            ->setCellValue('Y' . $i, '否')
            ->setCellValue('Z' . $i, $shipmentArr[$orderInfo['shipmentAutoMatched']])
            ->setCellValue('AA' . $i, $total_weight);
            
            $i++;
	    }
	  }
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
	}
	
	//海关申报表格
	public function explodehaikwanTable($resultArr,$phpExcel){
	  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	  $cacheSettings = array ('memoryCacheSize' => '512MB' );
	  PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	
	   //设置标题
	   $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
       $phpExcel->getActiveSheet()
         ->setCellValue('A1', '原始订单编号')
        ->setCellValue('B1','物流企业代码')
        ->setCellValue('C1', '物流企业运单号')
        ->setCellValue('D1', '中文申报名称')
        ->setCellValue('E1', '计量单位')
        ->setCellValue('F1', '商品数量')
        ->setCellValue('G1', '币制代码')
        ->setCellValue('H1', '商品总价')
        ->setCellValue('I1', '参考采购单价')
        ->setCellValue('J1', '订单商品货款')
        ->setCellValue('K1', '订单商品运费')
        ->setCellValue('L1', '收货人名称')
        ->setCellValue('M1', '收货地址')
        ->setCellValue('N1', '收货人电话')
        ->setCellValue('O1', '收货人所在国家')
        ->setCellValue('P1', '企业商品货号')
        ->setCellValue('Q1', '付款时间')
        ->setCellValue('R1', '打印时间')
        ->setCellValue('S1','发货时间')
        ->setCellValue('T1', '付款方式')
        ->setCellValue('U1', 'Email地址')
        ->setCellValue('V1', '是否备案')
        ->setCellValue('W1', 'Erp运费')
        ->setCellValue('X1', '计费运费')
        ->setCellValue('Y1', '通关时间');
	  
      $i=2;
        
	  //循环订单表获取订单产品
	  foreach($resultArr as $orderInfo){
	  	
	  	$productsInfo = array();
	  	
	    $productsInfo = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	    
		$skuCount = 0;
		
		$skuCount = count($productsInfo);
		
	    if(empty($productsInfo)){
	      continue;
	    }
	    
	    //获取国家中文
	    $country_en = !empty($orderInfo['buyer_country']) ? $orderInfo['buyer_country'] : $orderInfo['buyer_country_code'];
	    $countryInfo = $this->country_model->getCountryByEN($country_en);
	    $country_cn = !empty($countryInfo) ? $countryInfo['country_cn'] : '';
	    
	     //计费运费
	    $ji_shippingCost = 0;
	    $ji_shippingCost = $this->get_real_shippment_cost($orderInfo,2);
	    
	    foreach($productsInfo as $k => $pf){
	    	
	       $skuPrice = $pf['item_count']*$pf['item_price'];
	       
	       $each_sku_ship_fee = 0;//每个商品的运费
	       
	       $each_sku_ship_fee = $orderInfo['orders_ship_fee']/$skuCount;
	       
	       //商品总价
	       $itemTotalPrice = ($skuPrice+$each_sku_ship_fee);
	       
	       if($pf['products_is_declare']==1){
	         break;
	       }
	       
	       $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $orderInfo['erp_orders_id'].'-S')
            ->setCellValue('B' . $i, $orderInfo['shipmentAutoMatched'])
            ->setCellValue('C' . $i, $orderInfo['orders_shipping_code'])
            ->setCellValue('D' . $i, $pf['products_declared_cn'])
            ->setCellValue('E' . $i, $pf['products_unit1'])
            ->setCellValue('F' . $i, $pf['item_count'])
            ->setCellValue('G' . $i, $orderInfo['currency_type'])
            ->setCellValue('H' . $i, $itemTotalPrice)
            ->setCellValue('I' . $i, $pf['products_value'])
            ->setCellValue('J' . $i, ($k>0) ? '0' : $orderInfo['orders_total'])
            ->setCellValue('K' . $i, '0')
            ->setCellValue('L' . $i, $orderInfo['buyer_name'])
            ->setCellValue('M' . $i, $orderInfo['buyer_address_1'].' '.$orderInfo['buyer_address_2'])
            ->setCellValue('N' . $i, $orderInfo['buyer_phone'])
            ->setCellValue('O' . $i, $country_cn)
            ->setCellValue('P' . $i, $pf['orders_sku'])
            ->setCellValue('Q' . $i, $orderInfo['orders_paid_tim'])
            ->setCellValue('R' . $i, $orderInfo['orders_print_time'])
            ->setCellValue('S' . $i, $orderInfo['orders_shipping_time'])
            ->setCellValue('T' . $i, $orderInfo['pay_method'])
            ->setCellValue('U' . $i, $orderInfo['buyer_email'])
            ->setCellValue('V' . $i, $pf['products_is_declare']==1 ? '未备案' : '已备案')
            ->setCellValue('W' . $i, $orderInfo['shippingCost'])
            ->setCellValue('X' . $i, $ji_shippingCost)
	        ->setCellValue('Y' . $i, $orderInfo['clearance_time']);
            $i++;
	    }
	  }
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
	}
	
	//快悦退税财务申报表格-挂号
	public function explodehaikwanTableGuaHao($resultArr,$phpExcel){
	  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	  $cacheSettings = array ('memoryCacheSize' => '512MB' );
	  PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	
	   //设置标题
	   $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
       $phpExcel->getActiveSheet()
         ->setCellValue('A1', '原始订单编号')
        ->setCellValue('B1','物流企业代码')
        ->setCellValue('C1', '物流企业运单号')
        ->setCellValue('D1', '中文申报名称')
        ->setCellValue('E1', '计量单位')
        ->setCellValue('F1', '商品数量')
        ->setCellValue('G1', '币制代码')
        ->setCellValue('H1', '商品总价')
        ->setCellValue('I1', '参考采购单价')
        ->setCellValue('J1', '订单商品货款')
        ->setCellValue('K1', '订单商品运费')
        ->setCellValue('L1', '收货人名称')
        ->setCellValue('M1', '收货地址')
        ->setCellValue('N1', '收货人电话')
        ->setCellValue('O1', '收货人所在国家')
        ->setCellValue('P1', '企业商品货号')
        ->setCellValue('Q1', '付款时间')
        ->setCellValue('R1', '打印时间')
        ->setCellValue('S1','发货时间')
        ->setCellValue('T1', '付款方式')
        ->setCellValue('U1', 'Email地址')
        ->setCellValue('V1', '是否备案')
        ->setCellValue('W1', 'Erp运费')
        ->setCellValue('X1', '计费运费')
        ->setCellValue('Y1', '通关时间');
	  
      $i=2;
        
	  //循环订单表获取订单产品
	  foreach($resultArr as $orderInfo){
	  	
	  	if($orderInfo['shipmentAutoMatched']!=402){
	  	  continue;
	  	}
	  	
	  	$productsInfo = array();
	  	
	    $productsInfo = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);
	    
		$skuCount = 0;
		
		$skuCount = count($productsInfo);
		
	    if(empty($productsInfo)){
	      continue;
	    }
	    
	    //获取国家中文
	    $country_en = !empty($orderInfo['buyer_country']) ? $orderInfo['buyer_country'] : $orderInfo['buyer_country_code'];
	    $countryInfo = $this->country_model->getCountryByEN($country_en);
	    $country_cn = !empty($countryInfo) ? $countryInfo['country_cn'] : '';
	    
	     //计费运费
	    $ji_shippingCost = 0;
	    $ji_shippingCost = $this->get_real_shippment_cost($orderInfo,2);
	    
	    foreach($productsInfo as $k => $pf){
	    	
	       $skuPrice = $pf['item_count']*$pf['item_price'];
	       
	       $each_sku_ship_fee = 0;//每个商品的运费
	       
	       $each_sku_ship_fee = $orderInfo['orders_ship_fee']/$skuCount;
	       
	       //商品总价
	       $itemTotalPrice = ($skuPrice+$each_sku_ship_fee);
	       
	       if($pf['products_is_declare']==1){
	         continue;
	       }
	       
	       $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $orderInfo['erp_orders_id'].'-S')
            ->setCellValue('B' . $i, $orderInfo['shipmentAutoMatched'])
            ->setCellValue('C' . $i, $orderInfo['orders_shipping_code'])
            ->setCellValue('D' . $i, $pf['products_declared_cn'])
            ->setCellValue('E' . $i, $pf['products_unit1'])
            ->setCellValue('F' . $i, $pf['item_count'])
            ->setCellValue('G' . $i, $orderInfo['currency_type'])
            ->setCellValue('H' . $i, $itemTotalPrice)
            ->setCellValue('I' . $i, $pf['products_value'])
            ->setCellValue('J' . $i, ($k>0) ? '0' : $orderInfo['orders_total'])
            ->setCellValue('K' . $i, '0')
            ->setCellValue('L' . $i, $orderInfo['buyer_name'])
            ->setCellValue('M' . $i, $orderInfo['buyer_address_1'].' '.$orderInfo['buyer_address_2'])
            ->setCellValue('N' . $i, $orderInfo['buyer_phone'])
            ->setCellValue('O' . $i, $country_cn)
            ->setCellValue('P' . $i, $pf['orders_sku'])
            ->setCellValue('Q' . $i, $orderInfo['orders_paid_tim'])
            ->setCellValue('R' . $i, $orderInfo['orders_print_time'])
            ->setCellValue('S' . $i, $orderInfo['orders_shipping_time'])
            ->setCellValue('T' . $i, $orderInfo['pay_method'])
            ->setCellValue('U' . $i, $orderInfo['buyer_email'])
            ->setCellValue('V' . $i, $pf['products_is_declare']==1 ? '未备案' : '已备案')
            ->setCellValue('W' . $i, $orderInfo['shippingCost'])
            ->setCellValue('X' . $i, $ji_shippingCost)
	        ->setCellValue('Y' . $i, $orderInfo['clearance_time']);
            $i++;
	    }
	  }
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
	}
	
	
	
	
	//快悦海关申报订单表格
	public function explodeKuaiYuehaikwanTable($resultArr,$phpExcel){
		
	  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	  $cacheSettings = array ('memoryCacheSize' => '512MB' );
	  PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	
	   //设置标题
	   $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
       $phpExcel->getActiveSheet()
        ->setCellValue('A1', '原始订单编号')
        ->setCellValue('B1', '进出口标志')
        ->setCellValue('C1','物流企业代码')
        ->setCellValue('D1', '物流企业运单号')
        ->setCellValue('E1', '订单商品货款')
        ->setCellValue('F1', '订单商品运费')
        ->setCellValue('G1', '订单税款总额')
		->setCellValue('H1', '收货人名称')
        ->setCellValue('I1', '收货地址')
        ->setCellValue('J1', '收货人电话')
        ->setCellValue('K1', '收货人所在国家')
        ->setCellValue('L1', '企业商品货号')
        ->setCellValue('M1', '商品数量')
        ->setCellValue('N1', '计量单位')
        ->setCellValue('O1', '币制代码')
        ->setCellValue('P1', '商品总价')
		->setCellValue('Q1', '是否备案')
        ->setCellValue('R1', '通关时间');
	  
      $i=2;
        
	  //循环订单表获取订单产品
	  foreach($resultArr as $orderInfo){

	  	$productsInfo = array();
	  	
	    $productsInfo = $this->orders_products_model->getProdcutList($orderInfo['erp_orders_id'], $orderInfo['orders_warehouse_id']);

		$skuCount = 0;
		
		$skuCount = count($productsInfo);
		
	    if(empty($productsInfo)){
	      continue;
	    }
	    
	    //获取国家中文
	    $country_en = !empty($orderInfo['buyer_country']) ? $orderInfo['buyer_country'] : $orderInfo['buyer_country_code'];
	    $countryInfo = $this->country_model->getCountryByEN($country_en);

	    $country_cn = !empty($countryInfo) ? $countryInfo['country_cn'] : '';
	    $hs_country_info = $this->hs_country_model->getInfoByCn($country_cn);

	    foreach($productsInfo as $k => $pf){
	    	
	       $skuPrice = $pf['item_count']*$pf['item_price'];
	       
	       $each_sku_ship_fee = 0;//每个商品的运费
	       
	       $each_sku_ship_fee = $orderInfo['orders_ship_fee']/$skuCount;
	       
	       //商品总价
	       $itemTotalPrice = $skuPrice+$each_sku_ship_fee;

	       $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $orderInfo['erp_orders_id'].'-S')
            ->setCellValue('B' . $i, 'E')
            ->setCellValue('C' . $i, $orderInfo['shipmentAutoMatched'])
            ->setCellValue('D' . $i, $orderInfo['orders_shipping_code'])
            ->setCellValue('E' . $i, $orderInfo['orders_total'])
            ->setCellValue('F' . $i, '0')
            ->setCellValue('G' . $i, '0')
			->setCellValue('H' . $i, $orderInfo['buyer_name'])
            ->setCellValue('I' . $i, $orderInfo['buyer_address_1'].' '.$orderInfo['buyer_address_2'])
            ->setCellValue('J' . $i, $orderInfo['buyer_phone'])
            ->setCellValue('K' . $i, !empty($hs_country_info) ? $hs_country_info['country_co'] : $country_cn)
            ->setCellValue('L' . $i, $pf['orders_sku'])
            ->setCellValue('M' . $i, $pf['item_count'])
            ->setCellValue('N' . $i, $pf['products_unit1'])
            ->setCellValue('O' . $i, '502')
            ->setCellValue('P' . $i, $itemTotalPrice)
			->setCellValue('Q' . $i, $pf['products_is_declare']==1 ? '未备案' : '已备案')
	        ->setCellValue('R' . $i, isset($orderInfo['clearance_time']) ? $orderInfo['clearance_time'] : '');
            $i++;
          
	    }
	  }
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
	}
	
	//金华邮政挂号数据
	public function explodeForJHGuahao($resultArr,$phpExcel){
	   $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	   $cacheSettings = array ('memoryCacheSize' => '512MB' );
	   PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	
	   //设置标题
	    $phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
        $phpExcel->getActiveSheet()
        ->setCellValue('A1', '货运单号')
        ->setCellValue('B1', '寄达国家(中文)')
        ->setCellValue('C1', '寄达国家(英文)')
        ->setCellValue('D1', '州名')
        ->setCellValue('E1', '城市名')
        ->setCellValue('F1', '收件人详细地址(不超过60个字母)')
        ->setCellValue('G1', '收货人姓名')
        ->setCellValue('H1', '收货人电话')
        ->setCellValue('I1', '寄件人详细地址(英文)')
        ->setCellValue('J1', '寄件人姓名 ')
        ->setCellValue('K1', '寄件人电话')
        ->setCellValue('L1', '内件类型代码')
        ->setCellValue('M1', '邮编');
        
        $totalWeight = 0;//所有订单的总重量
        

        foreach($resultArr as $k=>$re){
        	
        	$i = $k+2;
        	
        	$allParamArr = array();
        	
	        //订单信息
		   $allParamArr['erp_orders']      = $re;
		   //订单中的产品信息
		   $allParamArr['erp_orders_products']    = $this->orders_products_model->getAllProdcutList($re['erp_orders_id'], $re['orders_warehouse_id']);

	       $country_en = !empty($allParamArr['erp_orders']['buyer_country']) ? $allParamArr['erp_orders']['buyer_country'] : $allParamArr['erp_orders']['buyer_country_code'];
	   	   $countryInfo = $this->country_model->getCountryByEN($country_en);
	
	       if(empty($countryInfo)){
	          $buyer_country_en = $allParamArr['erp_orders']['buyer_country']=='' ? $allParamArr['erp_orders']['buyer_country_code'] : $allParamArr['erp_orders']['buyer_country'];
	          $buyer_country_cn = '';
	       }else{
	          $buyer_country_en = $countryInfo['display_name'];
	          $buyer_country_cn = $countryInfo['country_cn'];
	       }

		   $buyer_address = $allParamArr['erp_orders']['buyer_address_1'].' '.$allParamArr['erp_orders']['buyer_address_2'];
		   
		   $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $allParamArr['erp_orders']['orders_shipping_code'])
            ->setCellValue('B' . $i, $buyer_country_cn)
            ->setCellValue('C' . $i, $buyer_country_en)
            ->setCellValue('D' . $i, $allParamArr['erp_orders']['buyer_state'])
            ->setCellValue('E' . $i, $allParamArr['erp_orders']['buyer_city'])
            ->setCellValue('F' . $i, $buyer_address)
            ->setCellValue('G' . $i, $allParamArr['erp_orders']['buyer_name'])
            ->setCellValue('H' . $i, $allParamArr['erp_orders']['buyer_phone'])
            ->setCellValue('I' . $i, 'yiwu')
            ->setCellValue('J' . $i, 'salamoer')
            ->setCellValue('K' . $i, '17705794166')
            ->setCellValue('L' . $i, 'SLME ')
            ->setCellValue('M' . $i, $allParamArr['erp_orders']['buyer_zip']);
        }
        $phpExcel->getActiveSheet ()->setTitle ( 'ordersInfo' );
        
        //创建第二个工作簿
	    $msgWorkSheet = new PHPExcel_Worksheet($phpExcel, 'sheet2'); //创建一个工作表
	    $phpExcel->addSheet($msgWorkSheet); //插入工作表
	    $phpExcel->setActiveSheetIndex(1); //切换到新创建的工作表
	    $phpExcel->getActiveSheet()
	        ->setCellValue('A1', '货运单号')
	        ->setCellValue('B1', '物品中文名称')
	        ->setCellValue('C1', '物品英文名称')
	        ->setCellValue('D1', '数量')
	        ->setCellValue('E1', '单件重量')
	        ->setCellValue('F1', '单价')
	        ->setCellValue('G1', '原产地');
		foreach($resultArr as $ke=>$res){
        	
        	$i = $ke+2;
        	
        	$allParamArr = array();
        	
	        //订单信息
		   $allParamArr['erp_orders']      = $res;
		   //订单中的产品信息
		   $allParamArr['erp_orders_products']    = $this->orders_products_model->getAllProdcutList($res['erp_orders_id'], $res['orders_warehouse_id']);

	    
		   $sku = '';//第一个sku
		   $products_declared_en = '';//第一个sku的英文申报名称
		   $products_declared_cn = '';//第一个sku的中文申报名称
		   $item_count = 0;//第一个sku 的数量
		   $total_weight = 0;//订单中第一个sku的总重量
		   
		   
       	   
           if(isset($allParamArr['erp_orders_products']) && !empty($allParamArr['erp_orders_products'])){
				$total_weight += $allParamArr['erp_orders_products'][0]['item_count']*$allParamArr['erp_orders_products'][0]['products_weight'];
				$products_declared_en = $allParamArr['erp_orders_products'][0]['products_declared_en'];
				$products_declared_cn = $allParamArr['erp_orders_products'][0]['products_declared_cn'];
				$sku = $allParamArr['erp_orders_products'][0]['orders_sku'];
				$item_count = $allParamArr['erp_orders_products'][0]['item_count'];
		   }
		   
		   $declare_value = ($item_count==0) ? '' : $allParamArr['erp_orders']['orders_total']/$item_count;
       
       	   $declare_value = $declare_value > 22 ? 22 : $declare_value;
		   
		   $phpExcel->setActiveSheetIndex(1)
            ->setCellValue('A' . $i, $allParamArr['erp_orders']['orders_shipping_code'])
            ->setCellValue('B' . $i, $products_declared_cn)
            ->setCellValue('C' . $i, $products_declared_en)
            ->setCellValue('D' . $i, $item_count)
            ->setCellValue('E' . $i, $total_weight)
            ->setCellValue('F' . $i, $declare_value)
            ->setCellValue('G' . $i, 'CN');
        }
        
        
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;
	}

	public function exportSalesOrder($phpExcel)
	{	$_POST = $_GET;
		
		$where ='';
		if(!empty($_POST['orders_type'])){
		//	$where['orders_type'] = $_POST['orders_type'];

			$where =$where.' and o.orders_type='.$_POST['orders_type'];
		}
		if(!empty($_POST['order_status'])){
		//	$where['orders_status'] = $_POST['order_status'];

			$where =$where.' and o.orders_status='.$_POST['order_status'];
		}
		else
		{
			$where =$where.' and o.orders_status=5';
		}
		if(!empty($_POST['shipmentID'])){
			//$where['shipmentAutoMatched'] = $_POST['shipmentID'];
			$where =$where.' and o.shipmentAutoMatched='.$_POST['shipmentID'];

		}
		if(!empty($_POST['warehouse'])){
			//$where['orders_warehouse_id'] = $_POST['warehouse'];
			$where =$where.' and o.orders_warehouse_id='.$_POST['warehouse'];

		}
		/*if(!empty($_POST['sale_account'])){
			//$where['sales_account'] = $_POST['sale_account'];

			$where =$where.' and o.sales_account='."'".$_POST['sale_account']."'";
		}*/
		if(!empty($_POST['import_start'])){
			//$where['orders_export_time >='] = $_POST['import_start'];

			$where =$where.' and o.orders_export_time>='."'".$_POST['import_start']."'";
		}
		if(!empty($_POST['import_end'])){
			//$where['orders_export_time <'] = $_POST['import_end'];

			$where =$where.' and o.orders_export_time<'."'".$_POST['import_end']."'";
		}
		if(!empty($_POST['ship_start'])){
			//$where['orders_shipping_time >='] = $_POST['ship_start'];

			$where =$where.' and o.orders_shipping_time>='."'".$_POST['ship_start']."'";
		}
		if(!empty($_POST['ship_end'])){
			//$where['orders_shipping_time <'] = $_POST['ship_end'];

			$where =$where.' and o.orders_shipping_time<'."'".$_POST['ship_end']."'";
		}


	//	$where =$where.' and o.erp_orders_id=8733878';
		

		$re =$this->kingdee_currency_model->exportOrder($where);



		$currencyoptin=array();

		$crrencyresult= $this->kingdee_currency_model->getAll2Array($currencyoptin);

		//新函数内容开始  ldb
		$file=time().'.csv';//测试资源
		$exurl = base_url().'Excel/'.$file;

		if(empty($re)){
			echo "<strong style='color:#F00'>数据为空！请关闭窗口！</strong><br>";
			exit;
		}
		$totalNum = count($re);//总数
		$width 	    = 500; //显示的进度条长度，单位 px
		$pix 	    = $width / $totalNum; //每条记录的操作所占的进度条单位长度
		$progress 	= 0; //当前进度条长度


		$htmlstr = '';
		$htmlstr .='
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/transitional.dtd">
			<html>
			<head>
			<title>动态显示服务器运行程序的进度条</title>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="Generator" content="JEdit">
			<meta name="Author" content="Krazy Nio">
			<style>
			body, div input { font-family: Tahoma; font-size: 9pt }
			</style>
			
			<script type="text/javascript" src="'.static_url('theme/common/jquery/jquery-1.8.2.js').'"></script>
			<script language="JavaScript">
			function updateProgress(sMsg, iWidth)
			{
				document.getElementById("status").innerHTML = sMsg;
				document.getElementById("progress").style.width = iWidth + "px";
				document.getElementById("percent").innerHTML = parseInt(iWidth / '.$width.' * 100) + "%";
			}
			function is_jixu(){
				var sqlStr = document.getElementById("sqlStr").value;
				var sql = encodeURI(sqlStr);
				if(confirm("你确定要覆盖导出吗？")){
					alert("呼叫管理员");
					location.href = "queHuoBaoGao_download.php?sql="+sql+"&is_jixu=ok";
				}else{
					return false;
				}
			}
			function downloadLink(strMsg){
				document.getElementById("downloadList").innerHTML = strMsg;
			}
			</script>
			</head>

			<body>
			<table width="600px;" bgcolor="#EAEAEA">
				<tr>
			    	<td align="center">
			        	<table>
			            	<td>
			                    <div style="margin: 4px; padding: 8px; border: 1px solid gray; background: #EAEAEA; width: <?php echo $width+8; ?>px">
			                    <div><font color="gray">如下进度条的动态效果由服务器端 PHP 程序结合客户端 JavaScript 程序生成。</font></div>
			                    <div style="padding: 0; background-color: white; border: 1px solid navy; width: <?php echo $width; ?>px">
			                    <div id="progress" style="padding: 0; background-color: #FFCC66; border: 0; width: 0px; text-align: center; height: 16px"></div>
			                    </div>
			                    <div id="status">&nbsp;</div><br/>
			                    <div id="downloadList">
			                    请等待完成才点击下载:<a href="'.$exurl.'">'.$file.'_下载</a><br>
			</div>
                    <div id="percent" style="position: relative; top: -30px; text-align: center; font-weight: bold; font-size: 8pt">0%</div>
                    </div>
				</td>
            </table>
    	</td>
    </tr>
</table>
		';
		$htmlstr .='
			</body></html><br>
		';
		echo $htmlstr;
// header('Content-Type: application/vnd.ms-excel');
// header('Content-Disposition: attachment;filename="BackOrder.xls"');
// header('Cache-Control: max-age=0');
		
                  	


		$crrencylastinfo = array();
		foreach($crrencyresult as $crrency)
		{
			$crrencylastinfo[$crrency['currency_code']]['currency_name'] = $crrency['currency_name'];
			$crrencylastinfo[$crrency['currency_code']]['currency_value'] = $crrency['currency_value'];
		}
		unset($crrencyresult);

		$order_type_option = array();
		$ordertypeinfo = $this->orders_type_model->getAll2Array($order_type_option);
		$ordertypelastinfo =array();
		foreach($ordertypeinfo as $type)
		{
			$ordertypelastinfo[$type['typeID']] = $type['typeName'];
		}
		unset($ordertypeinfo);


		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array ('memoryCacheSize' => '3888MB' );
		PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );

		//设置标题
		$phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
		$phpExcel->setActiveSheetIndex(0); //切换到新创建的工作表
		$one_sheet = $phpExcel->getActiveSheet(0);
		/*$one_sheet
			->setCellValue('A1', " 审核日期")
			->setCellValue('B1', " 日期")
			->setCellValue('C1', " 制单人_FName")
			->setCellValue('D1', " 编    号")
			->setCellValue('E1', " 审核人_FName")
			->setCellValue('F1', " 制单机构_FNumber")
			->setCellValue('G1', " 制单机构_FName")
			->setCellValue('H1', " 事务类型")
			->setCellValue('I1', " 单据号")
			->setCellValue('J1', " 客户地点_FNumber")
			->setCellValue('K1', " 客户地点_FName")
			->setCellValue('L1', " 销售方式_FID")
			->setCellValue('M1', " 销售方式_FName")
			->setCellValue('N1', " 销售方式_FTypeID")
			->setCellValue('O1', " 交货方式_FID")
			->setCellValue('P1', " 交货方式_FName")
			->setCellValue('Q1', " 交货方式_FTypeID")
			->setCellValue('R1', " 币    别_FNumber")
			->setCellValue('S1', " 币    别_FName")
			->setCellValue('T1', " 购货单位_FNumber")
			->setCellValue('U1', " 购货单位_FName")
			->setCellValue('V1', " 交货地点")
			->setCellValue('W1', " 主管_FNumber")
			->setCellValue('X1', " 主管_FName")
			->setCellValue('Y1', " 部门_FNumber")
			->setCellValue('Z1', " 部门_FName")
			->setCellValue('AA1'," 业务员_FNumber")
			->setCellValue('AB1', " 业务员_FName")
			->setCellValue('AC1', " 结算方式_FNumber")
			->setCellValue('AD1', " 结算方式_FName")
			->setCellValue('AE1', " 汇率类型_FNumber")
			->setCellValue('AF1', " 汇率类型_FName")
			->setCellValue('AG1', " 汇    率")
			->setCellValue('AH1', " 分销订单号")
			->setCellValue('AI1', " 订货机构_FNumber")
			->setCellValue('AJ1', " 订货机构_FName")
			->setCellValue('AK1', " 运输提前期")
			->setCellValue('AL1', " 引入标志")
			->setCellValue('AM1', " 源单类型")
			->setCellValue('AN1', " 结算日期")
			->setCellValue('AO1', " 摘要")
			->setCellValue('AP1', " 销售范围_FID")
			->setCellValue('AQ1', " 销售范围_FName")
			->setCellValue('AR1', " 销售范围_FTypeID")
			->setCellValue('AS1', " 保税监管类型_FNumber")
			->setCellValue('AT1', " 保税监管类型_FName")
			->setCellValue('AU1', " 系统设置")
			->setCellValue('AV1', " 确认人")
			->setCellValue('AW1', " 收 货 方_FNumber")
			->setCellValue('AX1', " 收 货 方_FName")
			->setCellValue('AY1', " 打印次数")
			->setCellValue('AZ1', " 计划类别_FNumber")
			->setCellValue('BA1', " 计划类别_FName");*/

		$one_sheet->setTitle ( 'Page1' );



		//创建第二个工作簿
		$msgWorkSheet = new PHPExcel_Worksheet($phpExcel, 'sheet2'); //创建一个工作表
		$phpExcel->addSheet($msgWorkSheet); //插入工作表
		$phpExcel->setActiveSheetIndex(1); //切换到新创建的工作表
		$two_sheet = $phpExcel->getActiveSheet(1);
		/*$two_sheet
			->setCellValue('A1', " 行号")
			->setCellValue('B1', " 单据号_FBillno")
			->setCellValue('C1', " 单据号_FTrantype")
			->setCellValue('D1', " 单据号_FPOOrdBillNo")
			->setCellValue('E1', " 产品代码_FNumber")
			->setCellValue('F1', " 产品代码_FName")
			->setCellValue('G1', " 产品代码_FModel")
			->setCellValue('H1', " 辅助属性_FNumber")
			->setCellValue('I1', " 辅助属性_FName")
			->setCellValue('J1', " 辅助属性_FClassName")
			->setCellValue('K1', " 单位_FNumber")
			->setCellValue('L1', " 单位_FName")
			->setCellValue('M1', " 数量")
			->setCellValue('N1', " 单价")
			->setCellValue('O1', " 含税单价")
			->setCellValue('P1', " 折扣率(%)")
			->setCellValue('Q1', " 单位折扣额")
			->setCellValue('R1', " 折扣额")
			->setCellValue('S1', " 运输提前期")
			->setCellValue('T1', " 是否预测内_FID")
			->setCellValue('U1', " 是否预测内_FName")
			->setCellValue('V1', " 是否预测内_FTypeID")
			->setCellValue('W1', " 建议交货日期")
			->setCellValue('X1', " 备注")
			->setCellValue('Y1', " 金额")
			->setCellValue('Z1', " 实际含税单价")
			->setCellValue('AA1'," 销项税额")
			->setCellValue('AB1'," 价税合计")
			->setCellValue('AC1'," 基本单位数量")
			->setCellValue('AD1'," 税率(%)")
			->setCellValue('AE1'," 对应代码")
			->setCellValue('AF1'," 对应名称")
			->setCellValue('AG1'," 计划模式_FID")
			->setCellValue('AH1'," 计划模式_FName")
			->setCellValue('AI1'," 计划模式_FTypeID")
			->setCellValue('AJ1'," 计划跟踪号")
			->setCellValue('AK1'," 客户BOM")
			->setCellValue('AL1'," 成本对象_FNumber")
			->setCellValue('AM1'," 成本对象_FName")
			->setCellValue('AN1'," 成本对象_FItemClassID")
			->setCellValue('AO1'," 交货日期")
			->setCellValue('AP1'," 是否冲减_FID")
			->setCellValue('AQ1'," 是否冲减_FName")
			->setCellValue('AR1'," 是否冲减_FTypeID")
			->setCellValue('AS1'," 锁库标志")
			->setCellValue('AT1'," 换算率")
			->setCellValue('AU1'," 辅助数量")
			->setCellValue('AV1'," 源单单号")
			->setCellValue('AW1'," 源单类型")
			->setCellValue('AX1'," 源单内码")
			->setCellValue('AY1'," 源单分录")
			->setCellValue('AZ1'," 合同单号")
			->setCellValue('BA1'," 合同内码")
			->setCellValue('BB1'," 合同分录")
			->setCellValue('BC1'," 基本单位组装数量")
			->setCellValue('BD1'," 辅助单位组装数量")
			->setCellValue('BE1'," 组装数量")
			->setCellValue('BF1'," 价税合计(本位币)")
			->setCellValue('BG1'," MRP计算标记")
			->setCellValue('BH1'," MRP是否计算标记")
			->setCellValue('BI1'," 收款关联金额")
			->setCellValue('BJ1'," BOM类别_FID")
			->setCellValue('BK1'," BOM类别_FName")
			->setCellValue('BL1'," BOM类别_FTypeID")
			->setCellValue('BM1'," 客户订单号")
			->setCellValue('BN1'," 订单BOM状态_FID")
			->setCellValue('BO1'," 订单BOM状态_FName")
			->setCellValue('BP1'," 订单BOM状态_FTypeID")
			->setCellValue('BQ1'," 客户订单行号")
			->setCellValue('BR1'," 订单BOM内码");*/
		$two_sheet->setTitle ( 'Page2' );

		//设置表头结束

		$i=2;
		$j=2;
		$k=1;
		//PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;  
   		PHPExcel_Settings::setCacheStorageMethod($cacheMethod);  
		$check_order =array();
		$resultinfo = array();
		foreach($re as $key=>$v)
		{
			//ldb
			
			echo '<script language="JavaScript">
			 	updateProgress("一共'.$totalNum.'条数据,正在操作第'.($key+1).'条 ....",'.min($width, intval($progress)) .');
			</script>';
			ob_flush();//将输出发送给客户端浏览器，使其可以立即执行服务器端输出的 JavaScript 程序。
			flush(); //将输出发送给客户端浏览器，使其可以立即执行服务器端输出的 JavaScript 程序。
			
			
			$progress += $pix;
			
			$order_shipping_type = '赊销';
			$order_shipping_FID='FXF02';
			$order_shipping_FTypeID='101';
			$addzero ="00";


			if($v['orders_type'] >9)
			{
				$addzero="0";
			}
			$days = date('d', strtotime($v['orders_shipping_time']));
			if($days >15)
			{
				$order_shipping_type='分期收款销售';
				$order_shipping_FID='FXF03';
				$order_shipping_FTypeID='102';
			}
			$shipping_time = date('Y-m-d',strtotime($v['orders_shipping_time']));





			if(!isset($check_order[$v['erp_orders_id']]) ) // 不存在先插入page1 再插入page2
			{


				$resultinfo['Page1'][$i] = array(
					$shipping_time,
					$shipping_time,
					"Administrator",
					$v['erp_orders_id'],
					"Administrator",
					"",
					"",
					"81",
					"1128",
					"",
					"",
					$order_shipping_FID,
					$order_shipping_type,
					$order_shipping_FTypeID,
					"",
					"",
					"",
					$v['currency_type'],
					$crrencylastinfo[$v['currency_type']]['currency_name'],
					$addzero.$v['orders_type'],
					$ordertypelastinfo[$v['orders_type']],
					"",
					"",
					"",
					$addzero.$v['orders_type'],
					$ordertypelastinfo[$v['orders_type']]."运营部",
					$addzero.$v['orders_type'],
					$ordertypelastinfo[$v['orders_type']]."客服",
					"*",
					"*",
					"01",
					"公司汇率",
					$crrencylastinfo[$v['currency_type']]['currency_value'],
					"",
					"",
					"",
					"0",
					"0",
					"0",
					$shipping_time,
					"",
					"1",
					"购销",
					"997",
					"",
					"",
					"2",
					"",
					"",
					"",
					"0",
					"STD",
					"标准"
				);

				$i++;


			$check_order[$v['erp_orders_id']] = $v['erp_orders_id'];


				$resultinfo['Page2'][$j]=array(
					$k,
					$v['erp_orders_id'],
					"81",
					"",
					"",
					"运费",
					"YF",
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['orders_ship_fee'],
					"1",
					"1",
					"0",
					"0",
					"0",
					"0",
					"",
					"",
					"",
					$shipping_time,
					"",
					$v['orders_ship_fee'],
					"1",
					"0",
					$v['orders_ship_fee'],
					"1",
					"0",
					"",
					"",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"0",
					"*",
					"*",
					"0",
					$shipping_time,
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"0",
					"0",
					$v['orders_ship_fee'],
					"0",
					"0",
					"0",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"0",
					"0"
				);


				$j++;
				$k++;

				$resultinfo['Page2'][$j]=array(
					$k,
					$v['erp_orders_id'],
					"81",
					"",
					"",
					$v['products_name_cn'],
					$v['orders_sku'],
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['item_count'],
					$v['item_price'],
					$v['item_price'],
					"0",
					"0",
					"0",
					"0",
					"",
					"",
					"",
					$shipping_time,
					"",
					$v['item_count']*$v['item_price'],
					$v['item_price'],
					"0",
					$v['item_count']*$v['item_price'],

					$v['item_count'],
					"0",
					"",
					"",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"0",
					"*",
					"*",
					"0",
					$shipping_time,
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"0",
					"0",
					$v['item_count']*$v['item_price'],
					"0",
					"0",
					"0",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"0",
					"0"
				);

				$j++;
				$k++;


				//再把信息插入page2
			}
			else  //存在了。。。 只用在page2插入sku信息
			{


				$resultinfo['Page2'][$j]=array(
					$k,
					$v['erp_orders_id'],
					"81",
					"",
					"",
					$v['products_name_cn'],
					$v['orders_sku'],
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['item_count'],
					$v['item_price'],
					$v['item_price'],
					"0",
					"0",
					"0",
					"0",
					"",
					"",
					"",
					$shipping_time,
					"",
					$v['item_count']*$v['item_price'],
					$v['item_price'],
					"0",
					$v['item_count']*$v['item_price'],

					$v['item_count'],
					"0",
					"",
					"",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"0",
					"*",
					"*",
					"0",
					$shipping_time,
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"0",
					"0",
					$v['item_count']*$v['item_price'],
					"0",
					"0",
					"0",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"0",
					"0"
				);
				$j++;
				$k++;
			}
		}
		// ldb
		$downlink = "<a href='".$exurl."'>".$file."_下载</a>";
		echo '
		<script language="JavaScript">
			updateProgress("'.count($re).'条数据，导出完成！", '.$width.');
			downloadLink("'.$downlink.'");
		</script>
		';
		ob_flush();
		flush();


unset($rs);

		$one_sheet->fromArray($resultinfo['Page1']);
		$two_sheet->fromArray($resultinfo['Page2']);
unset($resultinfo);
unset($check_order);

$filesName ='./Excel/'.$file;
		// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		// header('Content-Disposition: attachment;filename="'.time().'.csv"');
  // 		header('Cache-Control: max-age=0');	
	
		// $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		// $objWriter->save($filesName);
		// fclose($objWriter);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="BackOrder-'.time().'.csv"');
	header('Cache-Control: max-age=0');

	$phpExcel->setActiveSheetIndex(0);
	$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel7');
	$objWriter->save($filesName);
	
		die;

	}
////////////////OLD
	public function exportSalesOrder_old($phpExcel)
	{	
		$where ='';
		if(!empty($_POST['orders_type'])){
		//	$where['orders_type'] = $_POST['orders_type'];

			$where =$where.' and o.orders_type='.$_POST['orders_type'];
		}
		if(!empty($_POST['order_status'])){
		//	$where['orders_status'] = $_POST['order_status'];

			$where =$where.' and o.orders_status='.$_POST['order_status'];
		}
		else
		{
			$where =$where.' and o.orders_status=5';
		}
		if(!empty($_POST['shipmentID'])){
			//$where['shipmentAutoMatched'] = $_POST['shipmentID'];
			$where =$where.' and o.shipmentAutoMatched='.$_POST['shipmentID'];

		}
		if(!empty($_POST['warehouse'])){
			//$where['orders_warehouse_id'] = $_POST['warehouse'];
			$where =$where.' and o.orders_warehouse_id='.$_POST['warehouse'];

		}
		/*if(!empty($_POST['sale_account'])){
			//$where['sales_account'] = $_POST['sale_account'];

			$where =$where.' and o.sales_account='."'".$_POST['sale_account']."'";
		}*/
		if(!empty($_POST['import_start'])){
			//$where['orders_export_time >='] = $_POST['import_start'];

			$where =$where.' and o.orders_export_time>='."'".$_POST['import_start']."'";
		}
		if(!empty($_POST['import_end'])){
			//$where['orders_export_time <'] = $_POST['import_end'];

			$where =$where.' and o.orders_export_time<'."'".$_POST['import_end']."'";
		}
		if(!empty($_POST['ship_start'])){
			//$where['orders_shipping_time >='] = $_POST['ship_start'];

			$where =$where.' and o.orders_shipping_time>='."'".$_POST['ship_start']."'";
		}
		if(!empty($_POST['ship_end'])){
			//$where['orders_shipping_time <'] = $_POST['ship_end'];

			$where =$where.' and o.orders_shipping_time<'."'".$_POST['ship_end']."'";
		}


	//	$where =$where.' and o.erp_orders_id=8733878';
		

		$re =$this->kingdee_currency_model->exportOrder($where);



		$currencyoptin=array();

		$crrencyresult= $this->kingdee_currency_model->getAll2Array($currencyoptin);


		$crrencylastinfo = array();
		foreach($crrencyresult as $crrency)
		{
			$crrencylastinfo[$crrency['currency_code']]['currency_name'] = $crrency['currency_name'];
			$crrencylastinfo[$crrency['currency_code']]['currency_value'] = $crrency['currency_value'];
		}
		unset($crrencyresult);

		$order_type_option = array();
		$ordertypeinfo = $this->orders_type_model->getAll2Array($order_type_option);
		$ordertypelastinfo =array();
		foreach($ordertypeinfo as $type)
		{
			$ordertypelastinfo[$type['typeID']] = $type['typeName'];
		}
		unset($ordertypeinfo);


		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array ('memoryCacheSize' => '3888MB' );
		PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );

		//设置标题
		$phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
		$phpExcel->setActiveSheetIndex(0); //切换到新创建的工作表
		$one_sheet = $phpExcel->getActiveSheet(0);
		/*$one_sheet
			->setCellValue('A1', " 审核日期")
			->setCellValue('B1', " 日期")
			->setCellValue('C1', " 制单人_FName")
			->setCellValue('D1', " 编    号")
			->setCellValue('E1', " 审核人_FName")
			->setCellValue('F1', " 制单机构_FNumber")
			->setCellValue('G1', " 制单机构_FName")
			->setCellValue('H1', " 事务类型")
			->setCellValue('I1', " 单据号")
			->setCellValue('J1', " 客户地点_FNumber")
			->setCellValue('K1', " 客户地点_FName")
			->setCellValue('L1', " 销售方式_FID")
			->setCellValue('M1', " 销售方式_FName")
			->setCellValue('N1', " 销售方式_FTypeID")
			->setCellValue('O1', " 交货方式_FID")
			->setCellValue('P1', " 交货方式_FName")
			->setCellValue('Q1', " 交货方式_FTypeID")
			->setCellValue('R1', " 币    别_FNumber")
			->setCellValue('S1', " 币    别_FName")
			->setCellValue('T1', " 购货单位_FNumber")
			->setCellValue('U1', " 购货单位_FName")
			->setCellValue('V1', " 交货地点")
			->setCellValue('W1', " 主管_FNumber")
			->setCellValue('X1', " 主管_FName")
			->setCellValue('Y1', " 部门_FNumber")
			->setCellValue('Z1', " 部门_FName")
			->setCellValue('AA1'," 业务员_FNumber")
			->setCellValue('AB1', " 业务员_FName")
			->setCellValue('AC1', " 结算方式_FNumber")
			->setCellValue('AD1', " 结算方式_FName")
			->setCellValue('AE1', " 汇率类型_FNumber")
			->setCellValue('AF1', " 汇率类型_FName")
			->setCellValue('AG1', " 汇    率")
			->setCellValue('AH1', " 分销订单号")
			->setCellValue('AI1', " 订货机构_FNumber")
			->setCellValue('AJ1', " 订货机构_FName")
			->setCellValue('AK1', " 运输提前期")
			->setCellValue('AL1', " 引入标志")
			->setCellValue('AM1', " 源单类型")
			->setCellValue('AN1', " 结算日期")
			->setCellValue('AO1', " 摘要")
			->setCellValue('AP1', " 销售范围_FID")
			->setCellValue('AQ1', " 销售范围_FName")
			->setCellValue('AR1', " 销售范围_FTypeID")
			->setCellValue('AS1', " 保税监管类型_FNumber")
			->setCellValue('AT1', " 保税监管类型_FName")
			->setCellValue('AU1', " 系统设置")
			->setCellValue('AV1', " 确认人")
			->setCellValue('AW1', " 收 货 方_FNumber")
			->setCellValue('AX1', " 收 货 方_FName")
			->setCellValue('AY1', " 打印次数")
			->setCellValue('AZ1', " 计划类别_FNumber")
			->setCellValue('BA1', " 计划类别_FName");*/

		$one_sheet->setTitle ( 'Page1' );



		//创建第二个工作簿
		$msgWorkSheet = new PHPExcel_Worksheet($phpExcel, 'sheet2'); //创建一个工作表
		$phpExcel->addSheet($msgWorkSheet); //插入工作表
		$phpExcel->setActiveSheetIndex(1); //切换到新创建的工作表
		$two_sheet = $phpExcel->getActiveSheet(1);
		/*$two_sheet
			->setCellValue('A1', " 行号")
			->setCellValue('B1', " 单据号_FBillno")
			->setCellValue('C1', " 单据号_FTrantype")
			->setCellValue('D1', " 单据号_FPOOrdBillNo")
			->setCellValue('E1', " 产品代码_FNumber")
			->setCellValue('F1', " 产品代码_FName")
			->setCellValue('G1', " 产品代码_FModel")
			->setCellValue('H1', " 辅助属性_FNumber")
			->setCellValue('I1', " 辅助属性_FName")
			->setCellValue('J1', " 辅助属性_FClassName")
			->setCellValue('K1', " 单位_FNumber")
			->setCellValue('L1', " 单位_FName")
			->setCellValue('M1', " 数量")
			->setCellValue('N1', " 单价")
			->setCellValue('O1', " 含税单价")
			->setCellValue('P1', " 折扣率(%)")
			->setCellValue('Q1', " 单位折扣额")
			->setCellValue('R1', " 折扣额")
			->setCellValue('S1', " 运输提前期")
			->setCellValue('T1', " 是否预测内_FID")
			->setCellValue('U1', " 是否预测内_FName")
			->setCellValue('V1', " 是否预测内_FTypeID")
			->setCellValue('W1', " 建议交货日期")
			->setCellValue('X1', " 备注")
			->setCellValue('Y1', " 金额")
			->setCellValue('Z1', " 实际含税单价")
			->setCellValue('AA1'," 销项税额")
			->setCellValue('AB1'," 价税合计")
			->setCellValue('AC1'," 基本单位数量")
			->setCellValue('AD1'," 税率(%)")
			->setCellValue('AE1'," 对应代码")
			->setCellValue('AF1'," 对应名称")
			->setCellValue('AG1'," 计划模式_FID")
			->setCellValue('AH1'," 计划模式_FName")
			->setCellValue('AI1'," 计划模式_FTypeID")
			->setCellValue('AJ1'," 计划跟踪号")
			->setCellValue('AK1'," 客户BOM")
			->setCellValue('AL1'," 成本对象_FNumber")
			->setCellValue('AM1'," 成本对象_FName")
			->setCellValue('AN1'," 成本对象_FItemClassID")
			->setCellValue('AO1'," 交货日期")
			->setCellValue('AP1'," 是否冲减_FID")
			->setCellValue('AQ1'," 是否冲减_FName")
			->setCellValue('AR1'," 是否冲减_FTypeID")
			->setCellValue('AS1'," 锁库标志")
			->setCellValue('AT1'," 换算率")
			->setCellValue('AU1'," 辅助数量")
			->setCellValue('AV1'," 源单单号")
			->setCellValue('AW1'," 源单类型")
			->setCellValue('AX1'," 源单内码")
			->setCellValue('AY1'," 源单分录")
			->setCellValue('AZ1'," 合同单号")
			->setCellValue('BA1'," 合同内码")
			->setCellValue('BB1'," 合同分录")
			->setCellValue('BC1'," 基本单位组装数量")
			->setCellValue('BD1'," 辅助单位组装数量")
			->setCellValue('BE1'," 组装数量")
			->setCellValue('BF1'," 价税合计(本位币)")
			->setCellValue('BG1'," MRP计算标记")
			->setCellValue('BH1'," MRP是否计算标记")
			->setCellValue('BI1'," 收款关联金额")
			->setCellValue('BJ1'," BOM类别_FID")
			->setCellValue('BK1'," BOM类别_FName")
			->setCellValue('BL1'," BOM类别_FTypeID")
			->setCellValue('BM1'," 客户订单号")
			->setCellValue('BN1'," 订单BOM状态_FID")
			->setCellValue('BO1'," 订单BOM状态_FName")
			->setCellValue('BP1'," 订单BOM状态_FTypeID")
			->setCellValue('BQ1'," 客户订单行号")
			->setCellValue('BR1'," 订单BOM内码");*/
		$two_sheet->setTitle ( 'Page2' );

		//设置表头结束

		$i=2;
		$j=2;
		$k=1;


		$check_order =array();
		$resultinfo = array();
		foreach($re as $key=>$v)
		{


			$order_shipping_type = '赊销';
			$order_shipping_FID='FXF02';
			$order_shipping_FTypeID='101';
			$addzero ="00";


			if($v['orders_type'] >9)
			{
				$addzero="0";
			}
			$days = date('d', strtotime($v['orders_shipping_time']));
			if($days >15)
			{
				$order_shipping_type='分期收款销售';
				$order_shipping_FID='FXF03';
				$order_shipping_FTypeID='102';
			}
			$shipping_time = date('Y-m-d',strtotime($v['orders_shipping_time']));





			if(!isset($check_order[$v['erp_orders_id']]) ) // 不存在先插入page1 再插入page2
			{


				$resultinfo['Page1'][$i] = array(
					$shipping_time,
					$shipping_time,
					"Administrator",
					$v['erp_orders_id'],
					"Administrator",
					"",
					"",
					"81",
					"1128",
					"",
					"",
					$order_shipping_FID,
					$order_shipping_type,
					$order_shipping_FTypeID,
					"",
					"",
					"",
					$v['currency_type'],
					$crrencylastinfo[$v['currency_type']]['currency_name'],
					$addzero.$v['orders_type'],
					$ordertypelastinfo[$v['orders_type']],
					"",
					"",
					"",
					$addzero.$v['orders_type'],
					$ordertypelastinfo[$v['orders_type']]."运营部",
					$addzero.$v['orders_type'],
					$ordertypelastinfo[$v['orders_type']]."客服",
					"*",
					"*",
					"01",
					"公司汇率",
					$crrencylastinfo[$v['currency_type']]['currency_value'],
					"",
					"",
					"",
					"0",
					"0",
					"0",
					$shipping_time,
					"",
					"1",
					"购销",
					"997",
					"",
					"",
					"2",
					"",
					"",
					"",
					"0",
					"STD",
					"标准"
				);

				$i++;


			$check_order[$v['erp_orders_id']] = $v['erp_orders_id'];


				$resultinfo['Page2'][$j]=array(
					$k,
					$v['erp_orders_id'],
					"81",
					"",
					"",
					"运费",
					"YF",
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['orders_ship_fee'],
					"1",
					"1",
					"0",
					"0",
					"0",
					"0",
					"",
					"",
					"",
					$shipping_time,
					"",
					$v['orders_ship_fee'],
					"1",
					"0",
					$v['orders_ship_fee'],
					"1",
					"0",
					"",
					"",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"0",
					"*",
					"*",
					"0",
					$shipping_time,
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"0",
					"0",
					$v['orders_ship_fee'],
					"0",
					"0",
					"0",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"0",
					"0"
				);


				$j++;
				$k++;

				$resultinfo['Page2'][$j]=array(
					$k,
					$v['erp_orders_id'],
					"81",
					"",
					"",
					$v['products_name_cn'],
					$v['orders_sku'],
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['item_count'],
					$v['item_price'],
					$v['item_price'],
					"0",
					"0",
					"0",
					"0",
					"",
					"",
					"",
					$shipping_time,
					"",
					$v['item_count']*$v['item_price'],
					$v['item_price'],
					"0",
					$v['item_count']*$v['item_price'],

					$v['item_count'],
					"0",
					"",
					"",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"0",
					"*",
					"*",
					"0",
					$shipping_time,
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"0",
					"0",
					$v['item_count']*$v['item_price'],
					"0",
					"0",
					"0",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"0",
					"0"
				);

				$j++;
				$k++;


				//再把信息插入page2
			}
			else  //存在了。。。 只用在page2插入sku信息
			{


				$resultinfo['Page2'][$j]=array(
					$k,
					$v['erp_orders_id'],
					"81",
					"",
					"",
					$v['products_name_cn'],
					$v['orders_sku'],
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['item_count'],
					$v['item_price'],
					$v['item_price'],
					"0",
					"0",
					"0",
					"0",
					"",
					"",
					"",
					$shipping_time,
					"",
					$v['item_count']*$v['item_price'],
					$v['item_price'],
					"0",
					$v['item_count']*$v['item_price'],

					$v['item_count'],
					"0",
					"",
					"",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"0",
					"*",
					"*",
					"0",
					$shipping_time,
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"0",
					"0",
					$v['item_count']*$v['item_price'],
					"0",
					"0",
					"0",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"0",
					"0"
				);
				$j++;
				$k++;
			}
		}


unset($rs);

		$one_sheet->fromArray($resultinfo['Page1']);
		$two_sheet->fromArray($resultinfo['Page2']);
unset($resultinfo);
unset($check_order);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.csv"');
  		header('Cache-Control: max-age=0');	
// 		header("Pragma: no-cache");
// 		header("Expires: 0");
		
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		fclose($objWriter);
		die;

	}

	public function exportPurchaseOrder()
	{


		$where ='';

		if(!empty($_POST['orders_type'])){
			//	$where['orders_status'] = $_POST['order_status'];

			$where =$where.' and p.purchaseType='.$_POST['orders_type'];
		}


		if(!empty($_POST['warehouse'])){
			//$where['orders_warehouse_id'] = $_POST['warehouse'];
			$where =$where.' and p.procurement_warehouse_id='.$_POST['warehouse'];

		}


	/*	if(!empty($_POST['po_status']))
		{
			$where = $where.' and p.po_status = '.$_POST['po_status'];
		}*/

		if(!empty($_POST['import_start'])){
			//$where['orders_export_time >='] = $_POST['import_start'];

			$where =$where.' and a.arrival_chk_time>='."'".$_POST['import_start']."'";
		}
		if(!empty($_POST['import_end'])){
			//$where['orders_export_time <'] = $_POST['import_end'];

			$where =$where.' and a.arrival_chk_time<'."'".$_POST['import_end']."'";
		}



		$re =$this->kingdee_currency_model->exportPurchaseOrder($where);

	/*	$skuinfo = $this->kingdee_currency_model->getPurchaseSkuPirce();
		$skupricelist=array();
		if(!empty($skuinfo))
		{
			foreach($skuinfo as $skuprice)
			{
				$skupricelist[$skuprice['po_id']][$skuprice['op_pro_sku']] = $skuprice['op_pro_cost'];
			}
		}*/



		$purchase_user_option=array();
		$purchase_user_result = $this->Kingdee_purchase_user_model->getAll2Array($purchase_user_option);
		$userresultinfo = array();
		foreach($purchase_user_result as $user)
		{
			if($user['user_info'] !="")
			{
				$userresultinfo[$user['user_info']]['kingdee_num'] = $user['kingdee_num'];
				$userresultinfo[$user['user_info']]['kingdee_name'] = $user['kingdee_name'];
			}
		}



		//	var_dump($skupricelist);
		$phpExcel=new PHPExcel();
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array ('memoryCacheSize' => '512MB' );
		PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );


		//设置标题
		$phpExcel->getProperties()->setCreator("Moonar")->setLastModifiedBy("Moonar");
		$phpExcel->setActiveSheetIndex(0); //切换到新创建的工作表
		$one_sheet = $phpExcel->getActiveSheet(0);

		$one_sheet->setTitle ( 'Page1' );



		//创建第二个工作簿
		$msgWorkSheet = new PHPExcel_Worksheet($phpExcel, 'sheet2'); //创建一个工作表
		$phpExcel->addSheet($msgWorkSheet); //插入工作表
		$phpExcel->setActiveSheetIndex(1); //切换到新创建的工作表
		$two_sheet = $phpExcel->getActiveSheet(1);

		$two_sheet->setTitle ( 'Page2' );
		$resultinfo=array();

		$resultinfo['Page1'][0]=array(
			" 审核日期",
			" 日期",
			" 制单人_FName",
			" 编    号",
			" 审核人_FName",
			" 制单机构_FNumber",
			" 制单机构_FName",
			" 红蓝字",
			" 事务类型",
			" 单据号",
			" 供应商_FNumber",
			" 供应商_FName",
			" 验收_FNumber",
			" 验收_FName",
			" 保管_FNumber",
			" 保管_FName",
			" 采购方式_FID",
			" 采购方式_FName",
			" 采购方式_FTypeID",
			" 供货机构_FNumber",
			" 供货机构_FName",
			" 对方单据号",
			" 源单内码",
			" 源单类型",
			" 摘要",
			" 部门_FNumber",
			" 部门_FName",
			" 负责人_FNumber",
			" 负责人_FName",
			" 业务员_FNumber",
			" 业务员_FName",
			" 往来科目_FNumber",
			" 往来科目_FName",
			" 保税监管类型_FNumber",
			" 保税监管类型_FName",
			" 采购模式_FID",
			" 采购模式_FName",
			" 采购模式_FTypeID",
			" 付款日期",
			" 打印次数",
			" 付款条件_FNumber",
			" 付款条件_FName"
		);


		$resultinfo['Page2'][0] =array(
			" 序列号内码",
			" 订单号",
			" 订单行号",
			" 订单内码_FBillno",
			" 行号",
			" 单据号_FBillno",
			" 单据号_FTrantype",
			" 物料编码_FNumber",
			" 物料编码_FName",
			" 物料编码_FModel",
			" 辅助属性_FNumber",
			" 辅助属性_FName",
			" 辅助属性_FClassName",
			" 单位_FNumber",
			" 单位_FName",
			" 实收数量",
			" 单价",
			" 金额",
			" 批号",
			" 备注",
			" 基本单位实收数量",
			"计划单价",
			" 计划价金额",
			" 生产/采购日期",
			" 保质期(天)",
			" 有效期至",
			" 收料仓库_FNumber",
			" 收料仓库_FName",
			" 仓位_FName",
			" 仓位_FGroupName",
			" 对应代码",
			" 对应名称",
			" 拆单源单行号",
			" 换算率",
			" 辅助数量",
			" 源单单号",
			" 源单类型",
			" 源单内码",
			" 源单分录",
			" 计划模式_FID",
			" 计划模式_FName",
			" 计划模式_FTypeID",
			" 计划跟踪号",
			" 检验是否良品_FID",
			" 检验是否良品_FName",
			" 检验是否良品_FTypeID",
			" 交货通知单内码",
			" 交货通知单分录"
		);


		$i=1;
		$j=1;
		$k=1;

		$check_info =array();

		foreach($re as $v) {

			//Procurement_products_model
			$procurement_id = $v['po_id'];

			$procurement_products_result  = $this->Procurement_products_model->getAll2Array(array('where'=>array('po_id'=>$procurement_id)));
			if(!empty($procurement_products_result))
			{
				$count = 0;
				$shipping_fee = 0;

				$products_info =array();
				foreach($procurement_products_result as $products)
				{

					$products_info[$products['op_pro_sku']]['cost'] = $products['op_pro_cost']; //获取一下价格
					$products_info[$products['op_pro_sku']]['num'] = $products['op_pro_count_op']; //获取应该采购的数量


					$count =$count + $products['op_pro_count_op'];
				}

				if($count !=0)
				{
					$shipping_fee  =  $v['po_shipping_fee']/$count;
				}

			}
			else
			{
				continue;
			}



			if (!isset($check_info[$v['po_id'].'-'.$v['arrival_id']]))
			{
				$purchaser = isset($userresultinfo[$v['po_user']]['kingdee_name'])?$userresultinfo[$v['po_user']]['kingdee_name']:"";
				$purchasernum = isset($userresultinfo[$v['po_user']]['kingdee_num'])?$userresultinfo[$v['po_user']]['kingdee_num']:"";

				if($v['procurement_warehouse_id'] == 1000)
				{
					$po_sp_company_num = '002';
					$po_sp_company ="NETSHARP TECHNOLOGY (H.K.) CO.,LTD"; //
					$warehouse_name ='深圳仓';
				//	$purchaser = isset($userresultinfo[$v['po_user']])?$userresultinfo[$v['po_user']]:"";

				}



				if($v['procurement_warehouse_id'] == 1025) //义乌仓 暂时这么设置
				{
					$po_sp_company_num='001';
					$po_sp_company ='QILONG GROUP HK LIMITED';
					$warehouse_name ='义乌仓';
				//	$purchaser ='苏金玉';
				}

				/*if(strstr($v['po_sp_company'],"深圳"))
				{
					$po_sp_company='兴达';
				}
				else
				{
					$po_sp_company='麒龙';
				}*/
				//先插入page1

				$shipping_time = date('Y-m-d',strtotime($v['arrival_chk_time']));

				$resultinfo['Page1'][$i]=array(
					$shipping_time,
					$shipping_time,
					"Administrator",
					$v['po_id'].'-'.$v['arrival_id'],
					"Administrator",
					"",
					"",
					"1",
					"1",
					"1805",
					$po_sp_company_num,
					$po_sp_company,
					$purchasernum,
					$purchaser,
					$purchasernum,
					$purchaser,
					"P002",
					"赊销",
					"162",
					"",
					"",
					"",
					"0",
					"0",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"",
					"PTCG",
					"普通采购",
					"668",
					$shipping_time,
					"0",
					"",
					""
				);
				$i++;
				$check_info[$v['po_id'].$v['arrival_id']] = $v['po_id'].'-'.$v['arrival_id'];






				//page2 SKU信息


			//	$cost = isset($skupricelist[$v['po_id']][$v['arrival_sku']])?$skupricelist[$v['po_id']][$v['arrival_sku']]:"";

				$cost = isset($products_info[$v['arrival_sku']]['cost'])?$products_info[$v['arrival_sku']]['cost']:"";
				$num = isset($products_info[$v['arrival_sku']]['num'])?$products_info[$v['arrival_sku']]['num']:"";
				$resultinfo['Page2'][$j] =array(
					"0",
					"",
					"0",
					"",
					"".$k,
					"".$v['po_id'].'-'.$v['arrival_id'],
					"1",
					"",
					$v['products_name_cn'],
					$v['arrival_sku'],
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['arrival_count_real'],
					$cost,
					$cost*$v['arrival_count_real']+$shipping_fee*$v['arrival_count_real'],  // 金额等于 单价*数量 + 数量* 单个运费
					"",
					"",
					"".$v['arrival_count_real'],
					"0",
					"0",
					"",
					"0",
					$v['procurement_warehouse_id'],
					$warehouse_name,
					"",
					"",
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"Y",
					"是",
					"244",
					"0",
					"0"
				);
				$j++;
				$k++;

			}
			else
			{

				//page2 sku信息

				//$cost = isset($skupricelist[$v['po_id']][$v['arrival_sku']])?$skupricelist[$v['po_id']][$v['arrival_sku']]:"";

				$cost = isset($products_info[$v['arrival_sku']]['cost'])?$products_info[$v['arrival_sku']]['cost']:"";
				$num = isset($products_info[$v['arrival_sku']]['num'])?$products_info[$v['arrival_sku']]['num']:"";
				$resultinfo['Page2'][$j] =array(
					"0",
					"",
					"0",
					"",
					$k,
					$v['po_id'].'-'.$v['arrival_id'],
					"1",
					"",
					$v['products_name_cn'],
					$v['arrival_sku'],
					"",
					"",
					"*",
					"pcs",
					"pcs",
					$v['arrival_count_real'],
					"".$cost,
					"".$cost*$v['arrival_count_real']+$shipping_fee*$v['arrival_count_real'],  // 金额等于 单价*数量 + 数量* 单个运费
					"",
					"",
					"".$v['arrival_count_real'],
					"0",
					"0",
					"",
					"0",
					"".$v['procurement_warehouse_id'],
					"".$warehouse_name,
					"",
					"",
					"",
					"",
					"",
					"0",
					"0",
					"0",
					"",
					"0",
					"0",
					"0",
					"MTS",
					"MTS计划模式",
					"606",
					"",
					"Y",
					"是",
					"244",
					"0",
					"0"
				);
				$j++;
				$k++;


			}

		}




		$one_sheet->fromArray($resultinfo['Page1']);
		$two_sheet->fromArray($resultinfo['Page2']);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$objWriter->save('php://output');
		die;

	}

}