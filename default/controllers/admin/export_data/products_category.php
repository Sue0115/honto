<?php
date_default_timezone_set('PRC');
set_time_limit ( 0 ); //页面不过时
ini_set('memory_limit', '1024M');
header('Content-Type: text/html; Charset=utf-8');
//全部订单管理
class products_category extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();
		$this->load->model(
							array(
								'sangelfine_warehouse_model','category_model','products/products_data_model',
								'order/orders_products_model','stock/stock_detail_model'
							)
		);
		$this->load->library('phpexcel/PHPExcel');
		$this->model = $this->products_data_model;
	}
	
	function myview(){
	   $warehouseArr = array();//仓库
	   
	   $warehouse=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
	   
	   foreach($warehouse as $va){
		 $warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
	   }

	   $newData = array(
		  'warehouse'  =>  $warehouseArr
	   );

	   $this->_template('admin/export_data/products_category_view',$newData);
	   
	}
	
	public function deal_data(){
	  $posts = $this->input->post();
	  $cate_level = $posts['category'];//分类级别
	  $warehouse = $posts['warehouse'];//产品仓库
	  
	  $warehouseArr = array();//仓库
	   
	   $warehouses=$this->sangelfine_warehouse_model->get_all_warehouse();//查询所有的仓库信息并且组装仓库数组
	   
	   foreach($warehouses as $va){
		 $warehouseArr[$va['warehouseID']]=$va['warehouseTitle'];
	   }
	  
	   $option = array();
	 
	   $join = array();
	   
	   $where = array();
	   
	   $result = array();
	  
	   $select = array(
	   			"{$this->model->_table}.products_sku","{$this->model->_table}.products_sort",
	   			'c.category_parent_id',"{$this->model->_table}.products_name_cn","{$this->model->_table}.products_value","{$this->model->_table}.product_warehouse_id"
	   );
	  
	   $join[] = array('erp_category c',"c.category_id={$this->model->_table}.products_sort");
	   
	   if(!empty($warehouse)){
	      $where = array(
	        'product_warehouse_id' => $warehouse
	   	  );
	   }
	   

	  //获取订单表中数据10条
	  $option	= array(
				'select' => $select,
		  		'join'   => $join,
	  			'where'  => $where
	  );
	  
	  $result = $this->model->getAll2array($option);
	  
	  $new_category = array();
	  //获取所有的分类，并以分类id为键名，重组数组
	  $cate_arr = $this->category_model->getAllCategory();
	  foreach($cate_arr as $c){
	   $new_category[$c['category_id']]= $c;
	  }
	  
	  //获取sku的库存，并且以sku为键名重组数据
	  $stockArr = array();
	  $stockArr = $this->stock_detail_model->getSkuStockNumInfo(1,$warehouse);

	  $phpExcel=new PHPExcel();
	  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	  $cacheSettings = array ('memoryCacheSize' => '512MB' );
	  PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	  $phpExcel->getActiveSheet()
        ->setCellValue('A1', 'sku')
        ->setCellValue('B1', '分类中文名称')
        ->setCellValue('C1', 'sku中文名称')
        ->setCellValue('D1', 'sku价格')
        ->setCellValue('E1', 'sku库存')
        ->setCellValue('F1', '仓库');
      $i = 0;
	  foreach($result as $k => $v){
	  	
	  	$i = $k+2;
	  	if($cate_level==1){
	  	  $cate_sort = $v['category_parent_id'];
	  	}else{
	  	  $cate_sort = $v['products_sort'];
	  	}
	    $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $v['products_sku'])
            ->setCellValue('B' . $i, isset($new_category[$cate_sort]['category_name']) ? $new_category[$cate_sort]['category_name'] : '')
            ->setCellValue('C' . $i, !empty($v['products_name_cn']) ? $v['products_name_cn']: '')
            ->setCellValue('D' . $i, !empty($v['products_value']) ? $v['products_value']: '')
            ->setCellValue('E' . $i, !empty($stockArr[$v['products_sku']]) ? $stockArr[$v['products_sku']]: '')
            ->setCellValue('F' . $i, !empty($warehouseArr[$v['product_warehouse_id']]) ? $warehouseArr[$v['product_warehouse_id']] : '');
	  }
	  $phpExcel->getActiveSheet ()->setTitle ( 'ordersInfo' );
	  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	  header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
	  header('Cache-Control: max-age=0');
	  $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
	  $objWriter->save('php://output');
	  die;
	}
	
}