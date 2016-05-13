<?php
header("content-type:text/html; charset=utf-8");
class stock_deal extends Admin_Controller{

	function __construct(){		
		parent::__construct();
		$this->load->model(array(
								'stock/stock_detail_model','sangelfine_warehouse_model','sharepage',
								'orders/orders_record_model','orders/stock_detail_operate_record_detail_model',
								'sku_stock_change_model','print/products_data_model'
								)
							);

		$this->model = $this->stock_detail_model;
	}
	
	function pandian(){
	   $warehouseArr = array();
	   $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
	   foreach($warehouse as $va){
		 $warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
	   }
	   
	   $userInfo=$this->user_info;//登录用户名信息

	   $data = array(
	   			'type' => 1,
	   			'warehouse' => $warehouseArr,
	   			'userInfo'  => $userInfo
	   );
	  
	   $this->_template('admin/caiwu/pandian_view',$data);
	}
	
	function uploading(){
		
		$this->load->library(array('phpexcel/PHPExcel'));//载入excel类
        
        $uploadFiles = $_FILES["excelFile"]["tmp_name"];	//临时存储目录

        $post = $this->input->post();
        
		$warehouseArr = array();
	   $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
	   foreach($warehouse as $va){
		 $warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
	   }

        //开始读取excel文件的数据
		$PHPExcel = new PHPExcel(); 
		/**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
		$PHPReader = new PHPExcel_Reader_Excel2007(); 
		if(!$PHPReader->canRead($uploadFiles)){ 
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($uploadFiles)){ 
				'no Excel'; 
				return ; 
			} 
		} 
		$PHPExcel = $PHPReader->load($uploadFiles); 
		
		/**读取excel文件中的第一个工作表*/ 
		$currentSheet = $PHPExcel->getSheet(0); 
		
		/**取得最大的列号*/ 
		$allColumn = $currentSheet->getHighestColumn(); 
		
		/**取得一共有多少行*/ 
		$allRow = $currentSheet->getHighestRow(); 

		$data = array();//存放读取的表格数据
		
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){ 
		/**从第A列开始输出*/ 
			for($currentColumn= 65;$currentColumn<= 74; $currentColumn++){ 
				
				$pronum= $currentSheet->getCellByColumnAndRow($currentColumn - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				if($pronum instanceof PHPExcel_RichText){     //富文本转换字符串  
            	  $pronum = $pronum->__toString();  
				}
				$data[$currentRow][] = $pronum;
			
			}
		}

		$return = array(
		    'data' => $data,
			'type' => 2,
			'post' => $post,
			'warehouse' => $warehouseArr
		);
		$this->_template('admin/caiwu/pandian_view',$return);

	}
	
	/**
	 * 处理上传的表格数据
	 * 第一：将数据存入新建的表erp_sku_stock_change
	 * 第二：更加sku插入信息erp_orders_record、erp_stock_detail_operate_record_detail
	 * 第三：更改库存表的数量
	 */
	public function do_action(){
		
	  $posts = $this->input->post();
	  
	  $userInfo=$this->user_info;//登录用户名信息
	  
	  $result = array();//存放结果数组

	  $c_date 		= $posts['c_date'];//出库日期的数组
	  $z_date 		= $posts['z_date'];//质检日期的数组
	  $shenqing 	= $posts['shenqing'];//供应商或者申请部门的数组
	  $caigouname   = $posts['caigouname'];//采购员的数组
	  $caigouid 	= $posts['caigouid'];//原采购单号的数组
	  $deal_sku 	= $posts['deal_sku'];//sku的数组
	  $c_reason 	= $posts['c_reason'];//出库原因的数组
	  $shenqing_num = $posts['shenqing_num'];//申请数量的数组
	  $c_num 		= $posts['c_num'];//出库数量的数组
	  $type			= trim($posts['type']);//出入库类型
	  $warehouse	= trim($posts['warehouse']);//仓库id
	  
	  
	  $this->db->trans_begin();//事务开始
	  
	  //循环插入数据
	  foreach($c_date as $key => $c){
	  	
	     $in_data = array();//插入erp_sku_stock_change的数据
	     $or_data = array();//插入erp_orders_record的数据
	     $rd_data = array();//插入erp_stock_detail_operate_record_detail的数据
	     $sku 	  = trim($deal_sku[$key]);//要做处理的sku
		 $skuInfo = array();//存放sku的产品数据信息
		 $nowTime = date('Y-m-d H:i:s');
		 $msg	  = '';//存放返回信息
	     
	     //根据sku获取sku的产品数据详情
	     $skuInfo = $this->products_data_model->getProductsInfoWithSku(strtoupper($sku),$warehouse);
	     if(empty($skuInfo)){
	        $result[$sku]['status'] = false;
	        $result[$sku]['msg']    = $sku.'不存在';
	        continue;
	     }
		 //根据sku获取库存
		 $stockInfo = $this->stock_detail_model->getStockBySku(strtoupper($sku),$warehouse);

	     $in_data['c_date']		  = trim($c);
	     $in_data['z_date']		  = trim($z_date[$key]);
	     $in_data['shen_qing']	  = trim($shenqing[$key]);
	     $in_data['cai_gou_name']	  = trim($caigouname[$key]);
	     $in_data['cai_gou_id'] 	  = trim($caigouid[$key]);
	     $in_data['deal_sku']	  = $sku;
	     $in_data['shenqing_num'] = trim($shenqing_num[$key]);
	     $in_data['c_num']		  = trim($c_num[$key]);
	     $in_data['c_reason'] 	  = trim($c_reason[$key]);
	     $in_data['operate_user'] = trim($userInfo->nickname);
	     $in_id = $this->sku_stock_change_model->add($in_data);
	  	 if($in_id>0){
	  	   $in_flag = true;
	     }else{
	       $in_flag = false;
	       $msg = $sku.'insert erp_sku_stock_change failed<br/>';
	     }
	     
	     //插入erp_stock_detail_operate_record_detail表的数据整理
	  	  $out_in = '';//存放出库还是入库的判断
	  	  $operate_count = 0;//操作数量
	      $stock_sku = 0;//更新后的sku库存
	      $orders_record_status = 0;//erp_orders_record 的字段,入库-1，出库-2
		  if($type==2 || $type==3){
		     $out_in = 'out';
		     $operate_count = trim($c_num[$key]);
	         $stock_sku = $stockInfo['actual_stock'] - $operate_count;
	         $orders_record_status = 2;
		  }else{
		  	 $out_in = trim($c_num[$key])>=0 ? 'in' : 'out'; 
			  if($out_in=='in'){
		       $operate_count = trim($c_num[$key]);//操作数量
		       $stock_sku = $stockInfo['actual_stock'] + $operate_count;
		       $orders_record_status = 1;
		     }else{
		       $operate_count = substr(trim($c_num[$key]),1);
		       $stock_sku = $stockInfo['actual_stock'] - $operate_count;
		       $orders_record_status = 2;
		     }
		  }
	     
	     $rd_data['operate_type']  = $out_in;
	     $rd_data['product_id']    = $skuInfo['products_id'];
	     $rd_data['operate_count'] = $operate_count;
	     $rd_data['stock'] 		   = $stock_sku;
	     $rd_data['operate_time']  = $nowTime;
	     $rd_id = $this->stock_detail_operate_record_detail_model->add($rd_data);
	  	 if($rd_id>0){
	  	   $rd_flag = true;
	     }else{
	       $rd_flag = false;
	       $msg = $sku.'insert erp_stock_detail_operate_record_detail failed';
	     }
	     
	     //插入erp_orders_record表的数据整理
	     $or_data['products_sku'] 	       = $sku;
	     $or_data['orders_record_from']    = ($out_in=='in' ? '盘盈（入）' : '报损（出）');
	     $or_data['orders_record_count']   = $operate_count;
	     $or_data['user_id'] 			   = $userInfo->id;
	     $or_data['orders_record_type']    = ($out_in=='in' ? 2 : 3);
	     $or_data['orders_record_status']  = $orders_record_status;
	     $or_data['orders_record_time']    = $nowTime;
	     $or_data['orders_record_reason']  = trim($c_reason[$key]);
	     $or_data['orders_record_year']    = date('Y');
	     $or_data['orders_record_month']   = date('m');
	     $or_data['recordType'] 		   = 1;
	     $or_data['procurement_id']		   = trim($caigouid[$key]);
		 $or_id = $this->orders_record_model->add($or_data);
	   	 if($or_id>0){
	  	   $or_flag = true;
	     }else{
	       $or_flag = false;
	       $msg = $sku.'insert erp_orders_record falied';
	     }
	     
		 
		 //更新库存表的库存数量erp_stock_detail
		 $up_data = array();
		 $option  = array();
		 $option['where'] = array('products_sku'=>strtoupper($sku),'stock_warehouse_id'=>$warehouse);
		 $up_data['actual_stock'] =  $stock_sku;
		 if($stock_sku<0){
		   $sd_flag = false;
		 }else{
		   $sd_flag = $this->stock_detail_model->update($up_data,$option);
		 }
		 
	  	 if(!$sd_flag){
	       $msg = $sku.'update failed,stock<0';
	     }
		 
		 //判断四个表都更新了才执行插入操作
	  	 if( $this->db->trans_status() === TRUE && $in_flag===true && $rd_flag===true && $or_flag===true && $sd_flag!==false){
          	$this->db->trans_commit();//事务结束
            $result[$sku]['status'] = true;
	        $result[$sku]['msg']    =  $sku.'data update success';
         }else{
          	$this->db->trans_rollback();
            $result[$sku]['status'] = false;
	        $result[$sku]['msg']    = $msg;
         }
		 
	  }
	  echo json_encode($result);
	  exit;
	}
}