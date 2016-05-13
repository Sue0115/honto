<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//物流方式管理
class ShipmentManage extends Admin_Controller{
  
	private $shipment_status = array('0'=>'停用','1'=>'启用');
	
	function __construct(){
		
		parent::__construct();
		
		$this->load->model(array(
							'sharepage',
							'shipment_model',
							'country_model',
							'operate_log_model',
							'shipment_category_model',
							'category_model',
							'orders_type_model',
							'shipment/shipping_method_default_model',
							'logistics_service_model',
							'shipment/amz_logistic_model',
							'shipment/shipment_trackurl_model',
							'shipment/ebay_logistic_model',
							'shipment_rule_model',
							'shipment/printing_template_model',
							'sangelfine_warehouse_model',
							'shipment/sf_user_tokens_model',
							'shipment/smt_user_tokens_model',
							'wish_logistics_service_model')
							);
		
		$this->model = $this->shipment_model;
		
	}
	
	function index(){
	  	
		$key = $this->user_info->key;//用户组key
		
		$per_page	= (int)$this->input->get_post('per_page');
		
		$cupage	= config_item('site_page_num'); //每页显示个数
		
		$return_arr = array ('total_rows' => true );
		
		$where = array();
		
		$like  = array();
		
		$string='';
		
		$search_data=$this->input->get_post('search');
		
		$shipmentID = '';
		$shipmentCategoryID = '';
		$shipmentEnable = '';
		$shipmentTitle = '';
		$shipmentWarehouse='';
		
		//根据物流编号获取数据
		if(isset($search_data['shipmentID']) && $shipmentID=trim($search_data['shipmentID'])){
		 $where['shipmentID']=$shipmentID;
		 $string.='&search[shipmentID]='.$shipmentID;
		}
		
		//根据物流分类获取数据
		if(isset($search_data['shipmentCategoryID']) && $shipmentCategoryID=trim($search_data['shipmentCategoryID'])){
		 $where['shipmentCategoryID']=$shipmentCategoryID;
		 $string.='&search[shipmentCategoryID]='.$shipmentCategoryID;
		}
		
		//根据物流是否启用获取数据
		if(isset($search_data['shipmentEnable'])&& $search_data['shipmentEnable']!= ''){
		 $shipmentEnable=trim($search_data['shipmentEnable']);	
		 $where['shipmentEnable']=$shipmentEnable;
		 $string.='&search[shipmentEnable]='.$shipmentEnable;
		}
		
		//根据物流名称获取数据
		if(isset($search_data['shipmentTitle']) && $shipmentTitle=trim($search_data['shipmentTitle'])){
		 $like['shipmentTitle']=$shipmentTitle;
		 $string.='&search[shipmentTitle]='.$shipmentTitle;
		}
		
		//根据物流所属仓库获取数据
		if(isset($search_data['warehouse']) && $shipmentWarehouse=trim($search_data['warehouse'])){
		 $where['shipment_warehouse_id']=$shipmentWarehouse;
		 $string.='&search[warehouse]='.$shipmentWarehouse;
		}
		
		$search_data['shipmentID'] = $shipmentID;
		$search_data['shipmentCategoryID'] = $shipmentCategoryID;
		$search_data['shipmentEnable'] = $shipmentEnable;
		$search_data['shipmentTitle'] = $shipmentTitle;
		$search_data['warehouse'] = $shipmentWarehouse;
		

		
		$options	= array(
			'page'		=> $cupage,
			'per_page'	=> $per_page,
			'where'		=> $where,
			'like'		=> $like,
			'order'		=> "shipmentID desc",
			
		);

		$data_list=$this->model->get_all_shipment($options,$return_arr);//查询所有物流信息

		$warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
		foreach($warehouse as $va){
		  $warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
		}
		
		$shipmentCArr=$this->shipment_category_model->get_all_category();//查询所有物流分类信息

		$url = admin_base_url('shipment/shipmentManage?').$string;
		
		$page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
		
		
		$data = array(
			'data_list'	         => $data_list,
			'page'		         => $page,	
			'totals'	         => $return_arr ['total_rows'],	 //数据总数
			'shipmentCArr'		 => $shipmentCArr,
			'search'			 => $search_data,
		    'shipment_status'    => $this->shipment_status,
			'warehouse'			 => $warehouseArr
		); 
		
	    $this->_template('admin/shipment/shipment_list',$data);
	}
	
	/**
	 * 复制物流方式
	 */
	public function copyShipment(){
		
	  $shipmentID=$this->input->get_post('id');
	  
	  $where['shipmentID']=$shipmentID;
	  
	  $options=array(
	   'where'  => $where,
	  );
	  
	  //获取指定的物流信息，并且重新组装物流信息
	  $dataList=$this->model->getOne($options,true);
	  
	  unset($dataList['shipmentID']);
	  
	  $dataList['shipmentTitle']=$dataList['shipmentTitle'].'-copy';
	  
	  $result=$this->model->add($dataList);
	  
	  //信息返回操作
		if($result){
			echo '{"info":"物流复制成功","status":"y","id":"'.$result.'"}';
		}else{
			echo '{"info":"物流复制失败","status":"n"}';
		}
		die;
	}
	
	/**
	 * 新建物流方式
	 */
	public function info(){
		
	   if($this->input->is_post()){
	        $this->save();
	    }
	    
	    
	    //获取物流的具体信息并且修改物流信息	开始
		$id	  =	intval($this->input->get_post('id'));
		$opti = array();
		$shipmentInfo='';
		if($id>0){
			$opti['where'] = array('shipmentID'=>$id);
		}
		$shipmentInfo=$this->model->getOne($opti);
		$shipmentInfo->shipmentCalculateElementArray=unserialize($shipmentInfo->shipmentCalculateElementArray);
		$shipmentInfo->shipmentRuleMatchArray=unserialize($shipmentInfo->shipmentRuleMatchArray);
		$shipmentInfo->shipmentCarrierInfo=unserialize($shipmentInfo->shipmentCarrierInfo);
		$shipmentInfo->shipmentSangeCalculateElementArray=unserialize($shipmentInfo->shipmentSangeCalculateElementArray);
		$newarray=explode(',',$shipmentInfo->yw_channel);
		foreach($newarray as $key => $vvv){
		  $k=$key+1;
		  $arry['yw_channel'.$k]=$vvv;
		}
		if(!empty($arry)){
			$shipmentInfo->yw_channel1=$arry['yw_channel1'];
			if(!isset($arry['yw_channel2'])){
				$arry['yw_channel2']='';
			}
			$shipmentInfo->yw_channel2=$arry['yw_channel2'];
		}
		//获取物流的具体信息并且修改物流信息      结束

	  $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//获取所有的仓库信息
	  foreach($warehouse as $va){
	    $newWarehouse[$va['warehouseID']]=$va['warehouseTitle'];
	  }

	  $shipmentCategroy=$this->shipment_category_model->get_all_category();//获取所有的物流分类

	  $smtList=$this->logistics_service_model->getAll();//获取速卖通供应商
	 
	  $wishList=$this->wish_logistics_service_model->getAll();//获取wish供应商
	  
	  $ebayList=$this->ebay_logistic_model->getAll();//获取ebay供应商
	  
	  $amzList=$this->amz_logistic_model->getAll();//获取amz供应商

	  $allShipmentRule=$this->shipment_rule_model->getAllRuleList();//获取所有的匹配规则
	  
	  $allTemplate=$this->printing_template_model->getAllTemplate();//获取所有的模板
	  
	  $allTrackUrl=$this->shipment_trackurl_model->getAllUrl();//获取所有物流查询方式

	  $data=array(
	    'warehouse'=>$newWarehouse,
	    'allShipmentCategory'=>$shipmentCategroy,
	  	'smtList'=>$smtList,
	  	'wishList'=>$wishList,
	  	'ebayList'=>$ebayList,
	    'amzList'=>$amzList,
	    'ruleList'=>$allShipmentRule,
	  	'allTemplate'=>$allTemplate,
	  	'allTrackUrl'=>$allTrackUrl,
	    'shipmentInfo'=>$shipmentInfo,
	  );
	    
	  $this->_template('admin/shipment/shipment_info',$data);
	}
	
	public function save(){
	  $id = (int)$this->input->get_post('shipmentID');
	  $user_id = $this->user_info->id;
	  $data=array(
	        'shipmentTitle'                     => $this->input->get_post('shipmentTitle'),
	        'shipmentDescription'               => $this->input->get_post('shipmentDescription'),
	    	'shipmentCategoryID'                => $this->input->get_post('shipmentCategoryID'),	
	        'shipmentCalculateMethod'           => $this->input->get_post('shipmentCalculateMethod'),
	        'shipmentElementMin'                => $this->input->get_post('shipmentElementMin'),
	        'shipmentElementMax'                => $this->input->get_post('shipmentElementMax'),
	        'shipmentRate'                      => $this->input->get_post('shipmentRate'),
	        'shipmentNeedTrackingCode'          => $this->input->get_post('shipmentNeedTrackingCode'),
	        'shipmentElementMax'                => $this->input->get_post('shipmentElementMax'),
	        'shipmentCustomLabel'               => $this->input->get_post('shipmentCustomLabel'),
	        'shipmentScanMethod'                => $this->input->get_post('shipmentScanMethod'),
	        'shipmentScanLocal'                 => $this->input->get_post('shipmentScanLocal'),
	    	'shipmentWishCodeID'                => $this->input->get_post('shipmentWishCodeID'),
	        'shipmentSmtCodeID'                 => $this->input->get_post('shipmentSmtCodeID'),
	        'shipmentIsIntercept'               => $this->input->get_post('shipmentIsIntercept'),
			'updateTrackingNumber'              => $this->input->get_post('updateTrackingNumber'),
	    	'shipment_template'					=> $this->input->get_post('shipment_template'),
			'sales_view'                        => $this->input->get_post('sales_view'),
	    	'shipmentAMZCode'                   => $this->input->get_post('shipmentAMZCode'),
	    	'for_delivered'                     => $this->input->get_post('for_delivered'),//预计送达时间
			'shipmentEnTitle'                   => $this->input->get_post('shipmentEnTitle'),
	    	'ebayRemark'						=> $this->input->get_post('ebayRemark'),
	    	'ebaySearchUrl'						=> $this->input->get_post('ebaySearchUrl'),
	   	    'wishRemark'						=> $this->input->get_post('wishRemark'),
	    	'wishSearchUrl'						=> $this->input->get_post('wishSearchUrl'),
	        'smtRemark'							=> $this->input->get_post('smtRemark'),
	    	'smtSearchUrl'						=> $this->input->get_post('smtSearchUrl'),
	    	'amzRemark'							=> $this->input->get_post('amzRemark'),
	    	'amzSearchUrl'						=> $this->input->get_post('amzSearchUrl'),
			'yw_channel'                        => $this->input->get_post('yw_channel_1') . "," . $this->input->get_post('yw_channel_2'),
			'buffet_print'                      => $this->input->get_post('buffet_print'),
			'equal_order_id'                    => $this->input->get_post('equal_order_id'),
			'showPostOrder'                     => $this->input->get_post('showPostOrder'),
	        'shipment_warehouse_id'             => $this->input->get_post('shipment_warehouse_id') ,
	  		'shipmentCalculateElementArray'	 	=> serialize($this->input->get_post('shipmentCalculateElementArray')),
	  		'shipmentSangeCalculateElementArray'=> serialize($this->input->get_post('shipmentSangeCalculateElementArray')),
	 		'shipmentRuleMatchArray'	 		=> serialize($this->input->get_post('shipmentRuleMatchArray')),
	 		'shipmentCarrierInfo'	 			=> serialize($this->input->get_post('shipmentCarrierInfo')),
	  		'shipmentTrackingUrl'				=> $this->input->get_post('shipmentTrackingUrl'),
	    );
	    if($id>0){
	     //修改
	      $data['shipmentID']=$id;
	      $result=$this->model->update($data);
	      if($result){//如果修改物流成功，添加修改日志
		      $logData= array(
		      				'operateUser' =>$user_id,
		      				'operateTime '=>date('Y-m-d H:i:s',time()),
	                        'operateType' => 'update',
	                        'operateMod' => 'shipmentManage',
	                        'operateKey' => $id,
	                        'operateText' => '修改物流方式' 
	                    	);
	            $insertLog=$this->operate_log_model->add($logData);
	      }
	    }else{
	     //添加物流
	     $result=$this->model->add($data);
	    }
		$info = $id ? '修改' : '添加';
		//信息返回操作
		if($result){
			$val = $id ? $id : $result;
			echo '{"info":"物流'.$info.'成功","status":"y","id":"'.$val.'"}';
		}else{
			echo '{"info":"物流'.$info.'失败","status":"n"}';
		}
	  die;
	}
	
	
	/**
	 * 物流日志
	 */
	public function operate_log(){
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
	  
	  $string='/operate_log?operateMod=shipmentManage&id='.$id;
	  

	  $options=array(
	   'page'		=> $cupage,
	   'per_page'	=> $per_page,
	   'where'		=> $where,
	   'order'      => 'operateTime desc'
	  );
	  
	  $logList=$this->operate_log_model->getAll($options,$return_arr);
	  
	  //获取用户数组
	  $userList=$this->db->query('select id,user_name from erp_slme_user');
	  $userResultList=$userList->result_array();
	  foreach($userResultList as $val){
	   $userArray[$val['id']]=$val['user_name'];
	  }

	  $url = admin_base_url('shipment/shipmentManage').$string;
	
	  $page = $this->sharepage->showPage ($url, $return_arr ['total_rows'], $cupage );
	  
	  $data=array(
	    'logList'=>$logList,
	    'page'   =>$page,
	    'userArray'=>$userArray,
	  );

	  $this->template('admin/shipment/shipment_operate_log',$data);
	  
	}
}
