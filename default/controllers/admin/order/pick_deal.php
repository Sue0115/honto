<?php
class pick_deal extends Admin_Controller{
	
	private $type_text = array(//拣货单类型
		'1' => '单品单件',
		'2' => '单品多件',
		'3' => '多品多件'
	);
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'sharepage','order/pick_model','products/pack_method_model',
								'order/pick_product_model','order/orders_model',
								'order/orders_products_model','shipment/shipment_model',
								'order/order_type_model','sangelfine_warehouse_model',
								'order/pick_print_model','print/products_data_model',
								'slme_user_model','stock/stock_detail_model','country_model','order/old_sku_new_sku_model'

								)
							);
		
		$this->model = $this->pick_model;
		
	}
	//获取未包装的产品
	public function no_packget(){
		
	  $userInfo = $this->user_info;
	  
	  $username=$this->user_info->nickname;//登录用户名,打单员
	  
	  $warehouseArr = $this->sangelfine_warehouse_model->get_all_warehouse(true);
	  
	  //获取登录用户所属仓库的所有拣货单
	  $option = array();
	  $option['where'] = array('warehouse'=>$userInfo->warehouse_id,'status'=>3,'type <'=>3);
	   $option['where']['create_time <'] = strtotime(date('Y-m-d'));
	  $pickArr = $this->model->getAll2array($option);

	  $pickIDArr = array();
	  foreach($pickArr as $pick){
	   $pickIDArr[]=$pick['id'];
	  }

	  //组装拣货单产品的数据
	  $optio['select'] = array($this->pick_product_model->_table.".*",'p.products_location','p.products_warring_string','p.products_name_cn');
	  $joins[] = array('erp_products_data p',"p.products_sku={$this->pick_product_model->_table}.product_sku");
	  $optio['join']=$joins;
	  $optio['where']=array($this->pick_product_model->_table.'.status'=>1,'p.product_warehouse_id'=>$userInfo->warehouse_id);
	  $optio['where_in']['pick_id'] = $pickIDArr;
	  $optio['order']='p.products_location asc';
	  $productInfo=$this->pick_product_model->getAll2array($optio);

	  //以pick——id重组数组
	  $newProductInfo = array();
	  foreach($productInfo as $pI){
	    $newProductInfo[$pI['pick_id']][] = $pI;
	  }

	  $pick_other_info = array();//存放拣货单的其他信息,根据拣货id为键名
	  
	  foreach($newProductInfo as $key=>$datas){
	  	  $options = array();
	  	  $join = array();
	  	  $pickInfo = array();
	     //获取该拣货单的详细信息
		  $options['select'] = array("{$this->model->_table}.*",'us.nickname');
		  $join[] = array('erp_slme_user us',"us.id={$this->model->_table}.uid");
		  $options['join']=$join;
		  $options['where']=array("{$this->model->_table}.id" => $key);
		  $pickInfo=$this->model->getOne($options,true);

		  if(strpos($pickInfo['shipment_id'],',')){
		  	$shipmentTitle = "混合渠道(".$pickInfo['shipment_id'].")";
		  }else{
			  //根据拣货单中的物流id获取物流渠道名
			  $o=array(
			    'where'  => array('shipmentID' => $pickInfo['shipment_id']),
			  );
			  $shipmentTitleArr=$this->shipment_model->getOne($o,true);
			  $shipmentTitle = $shipmentTitleArr['shipmentTitle'];
		  }
		  $shipment_id_array = explode(',', $pickInfo['shipment_id']);
		  $print_template = $this->shipment_model->get_one_get_template($shipment_id_array[0]);
		  $pickInfo['template_size'] = $print_template['page_size'];
		  
		  $pick_other_info[$key]['pickInfo'] = $pickInfo;
		  $pick_other_info[$key]['shipment'] = $shipmentTitle;
	  }

	  //映射SKU处理
	  $old_sku_new_sku = $this->old_sku_new_sku_model->get_all_key_old_sku();
	  
	  $finall_result_arr = array();
	  //去掉重复的sku并数量加一组成新的数组
	  foreach($newProductInfo as $kes => $pds){
	    $newProduct=array();
	    $total = 0;
	    foreach($pds as $product){
	    	
	    	$total += $product['product_num'];
	    	
	    	//先判断是否需要替换sku
	   		if(isset($old_sku_new_sku[$product['product_sku']])){
		  		  $product_sku = $old_sku_new_sku[$product['product_sku']];
		  	}else{
		  	      $product_sku = $product['product_sku'];
		  	} 

		  	//相同sku的信息覆盖，件数相加
		    if(isset($newProduct[$product_sku])){
			  $newProduct[$product_sku]['product_num']+=$product['product_num'];
			}else{
				$product['product_sku'] = $product_sku;
		  		$newProduct[$product_sku]=$product;
			}
			
	    }
	    $finall_result_arr[$kes] = $newProduct;
	    $pick_other_info[$kes]['total'] = $total;
	  }

	  $new_data = array();
	  //更新页码数
	  foreach($finall_result_arr as $k => $d){
	  	 $i = 1;
	  	 $page_num = 1;
	     foreach($d as $ke => $da){
	      //更新pick_product中的页数
	    	if($i/(33*$page_num) == 1){
	  			$page_num++;
	  		}

	  		$data_page = $k.'-'.$page_num;
	  		
	  		$da['page_num'] =$data_page;
	  		$new_data[$k][$ke] = $da;
	  		$i++;
	     }
	  }

	 $sort = array();
	  foreach($new_data as $keys => $nedata){
	      $sort[$keys] = count($nedata);
	  }
	 arsort($sort);
	 $finall_data = array();
	 foreach($sort as $kk => $dd){
	   $finall_data[$kk] = $new_data[$kk];
	 }

	  $resultData=array(
		'pick_other_info' => $pick_other_info,//拣货的其他信息，物流标题，sku总数
	  	'type_text'=>$this->type_text,//拣货单类型
	  	'print_time'=>date('Y-m-d H:i:s'),//打印时间
	  	'print_username'=>$username,//打单员
	  	'new_pick_info' => $finall_data,
	  	'warehouseArr'  => $warehouseArr
	  );
	 $this->template('admin/order/create_no_packget',$resultData);
	  
	}
}