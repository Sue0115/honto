<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
date_default_timezone_set('PRC');
set_time_limit ( 0 ); //页面不过时
ini_set('memory_limit', '2024M');
header('Content-Type: text/html; Charset=utf-8');
//全部订单管理
class return_export extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();
		$this->load->model(
							array(
								'print/orders_model','moneyback_model','sangelfine_warehouse_model',
								'orders_type_model','category_model','products/products_data_model',
								'moneyback_products_model'
							)
		);
		$this->load->library('phpexcel/PHPExcel');
		$this->model = $this->moneyback_model;
		
	}
	
	function index(){
		
		$orders_type_arr = $this->orders_type_model->getOrdersType();

		//找到所有仓库
	    $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
		foreach($warehouse as $va){
			$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
		}
		
		$data = array(
		  'warehouse'		 => $warehouseArr,
		  'orders_type_arr'  => $orders_type_arr
		);

	    $this->_template('admin/export_data/return_export',$data);
	}
	
	function deal_data(){
		
	  $posts = $this->input->post();

	  $start_date = !empty($posts['start_date']) ? $posts['start_date'] : '';//开始时间
	  
	  $end_date   = !empty($posts['end_date']) ? $posts['end_date'] : '';//结束时间
	  
	  $orders_type_arr = $this->orders_type_model->getOrdersType();
	  
	  $file_array = array();
	  
	    
	  $orders_type = !empty($posts['orders_type']) ? $posts['orders_type'] : '';//平台类型
	  
	  $warehouse = !empty($posts['warehouse']) ? $posts['warehouse'] : '';//所属仓库仓库
	  
	  $where = ' where';
	  
	  $sql = "
	  		select m.*,o.orders_warehouse_id,o.currency_value,mp.moneyBackProductsSKU,mp.moneyBackProductsQuantity
	  	    from erp_moneyback m left join erp_orders o on m.erp_orders_id = o.erp_orders_id 
	  	    left join erp_moneyback_products mp on mp.moneyBackID = m.moneyback_id where 1=1
	  		 ";
		  
		  
		  if(!empty($start_date)){
		  	$sql .=" and m.moneyback_createTime>='".$start_date."'";
		  }
		  
		  if(!empty($end_date)){
		  	$sql .=" and m.moneyback_createTime<'".$end_date."'";
		  }
		  
		  if(!empty($orders_type)){
		  	$sql .=" and m.ordersType='".$orders_type."'";
		  }
		  
		  if(!empty($warehouse)){
		  	$sql .=" and o.orders_warehouse_id='".$warehouse."'";
		  }

		  $returnArr = $this->model->result_array($sql); 
		  
		  //导出的表格中对应的列标题
		  $title=array(
		  		'A'=>'订单号','B'=>'买家ID',
		  		'C'=>'erp导入时间','D'=>'退款金额',
		  		'E'=>'SKU','F'=>'SKU数量',
		  		'G'=>'仓库','H'=>'平台',
		  		'I'=>'退款时间'
		  );
		  $oT = !empty($orders_type) ? '-'.$orders_type_arr[$orders_type]['typeName'] : '';
		  $w = !empty($warehouse) ? '-'.$warehouse : '';
		  $filename = 'moneyback'.$start_date.$oT.$w;
	
		  $phpExcel=new PHPExcel();
		 
		  $file = $this->export($phpExcel,$title,$returnArr,$filename,'2007');
		  
	}
	
	
	//导出数据的excel表格
    public function export($phpExcel,$title,$result_data,$filename,$ex='2003'){//默认导出excel2003
    	
    	//找到所有仓库
	    $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
		foreach($warehouse as $va){
			$warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
		}
		//平台
		$orders_type_arr = $this->orders_type_model->getOrdersType();
		
       $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
       
	   $cacheSettings = array ('memoryCacheSize' => '512MB' );
	   
	   PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	   
      	//设置单元格标题
      	foreach($title as $k => $v){
	      	$phpExcel->getActiveSheet()->setCellValue($k.'1', $v);
      	}
      	
      	$price_array = array();
		//把数据填入单元格
      	foreach($result_data as $key => $value){
			$price = '';
      		$i = $key+2;
      		if(!in_array($value['erp_orders_id'],$price_array)){
      		   $price = $value['moneyback_amount']/$value['currency_value'];
      		   $price_array[] = $value['erp_orders_id'];
      		}
      		
      		$phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $value['erp_orders_id'])
            ->setCellValue('B' . $i, $value['moneyback_buyer'])
            ->setCellValue('C' . $i, $value['moneyback_createTime'])
            ->setCellValue('D' . $i, $price)
            ->setCellValue('E' . $i, $value['moneyBackProductsSKU'])
            ->setCellValue('F' . $i, $value['moneyBackProductsQuantity'])
            ->setCellValue('G' . $i, $warehouseArr[$value['orders_warehouse_id']])
            ->setCellValue('H' . $i, $orders_type_arr[$value['ordersType']]['typeName'])
            ->setCellValue('I' . $i, $value['moneyback_submitTime']);
      	}

		$phpExcel->getActiveSheet ()->setTitle ( 'return_data' );
		
	
		if($ex == '2007') { //导出excel2007文档   
//		    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');  
//		    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');  
//		    header('Cache-Control: max-age=0');  
//		    $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');  
//		 	$objWriter->save('attachments/export_excel/'.$filename.'.xlsx'); 
		 	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		        header('Cache-Control: max-age=0');
		        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		        $objWriter->save('php://output');
		        die;
		 //	return 'attachments/export_excel/'.$filename.'.xlsx';
		} else {  //导出excel2003文档   
		    header('Content-Type: application/vnd.ms-excel');  
		    header('Content-Disposition: attachment;filename="links_out'.$filename.'.xls"');  
		    header('Cache-Control: max-age=0');  
		    $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');   
		    $objWriter->save('php://output'); 
		    die;
		} 
		
    }
	
	
	//获取所有深圳仓的sku分类名称，以sku为键名,链接erp_category和erp_products_data表
    function get_sku_category(){
       $option = array();
	  
	   $where = array();
	  
	   $join = array();
	   
	   $result = array();//装重组以后的数组
	  
	   $select = array("{$this->category_model->_table}.*",'pd.products_sku');
	  
	   $join[] = array('erp_products_data pd',"pd.products_sort={$this->category_model->_table}.category_id");
	   
	   $where['pd.product_warehouse_id'] = 1000;
	   
	  //获取订单表中数据10条
	  $option	= array(
				'select' => $select,
		  		'join'   => $join,
		  	    'where'  => $where
	  );
	  $categoryArr = $this->category_model->getAll2array($option);
	  foreach($categoryArr as $ca){
	     $result[$ca['products_sku']] = $ca['category_name'];
	  }
	  return $result;
    }
    
    //前端显示要下载的文件
    public function show_flie_list(){
      $flie_string = $this->input->get_post('data');
      $file_list = explode(',',$flie_string);
      $new_data = array();
      foreach($file_list as $kes => $f){
        $keyArr = explode('/',$f);
        $new_data[$kes]['name'] = $keyArr[2];
        $new_data[$kes]['url'] = $f;
      }
      $data = array('file_list'=>$new_data);
      $this->template('admin/export_data/file_list',$data);
    }
    
    
    //批量下载
    public function batch_download(){
        $file_urls = $this->input->post('file_urls');
        $file_arr = explode(',',$file_urls);
  	
		$filename='attachments/export_excel/'.time().'.zip'; //最终生成的文件名（含路径）
		
		if(file_exists($filename)){
		
		    unlink($filename);
		
		}
		
		//重新生成文件
		
		$zip=new ZipArchive();
		
		if($zip->open($filename,ZIPARCHIVE::CREATE)!==TRUE){
		
		    exit('无法打开文件，或者文件创建失败');
		
		}
		
		foreach($file_arr as $val){
		
		    if(file_exists('attachments/export_excel/'.$val)){
		
		        $zip->addFile('attachments/export_excel/'.$val);
		
		    }
		
		}
		
		$zip->close();//关闭
		
		if(!file_exists($filename)){
		
		    exit('无法找到文件'); //即使创建，仍有可能失败
		
		}
	   
	    header('Content-Description: File Transfer');    
		Header("content-type:application/x-zip-compressed");  
		header('Content-Disposition: attachment; filename='.basename($filename));     
		header('Content-Transfer-Encoding: binary');     
		header('Expires: 0');     
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');     
		header('Pragma: public');     
		header('Content-Length: ' . filesize($filename));     
		ob_clean();   //清空但不关闭输出缓存 
		flush();     
		@readfile($filename);   
		@unlink($filename);//删除打包的临时zip文件。文件会在用户下载完成后被删除   
	    
    }
 	
    
}
