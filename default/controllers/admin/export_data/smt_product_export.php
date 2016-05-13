<?php
ini_set('memory_limit', '1024M');
set_time_limit(0);
header("content-type:text/html; charset=utf-8");
class smt_product_export extends Admin_Controller{

	function __construct(){		
		parent::__construct();
		$this->load->model(array(
								'smt/smt_user_tokens_model','smt/slme_smt_categorylist_model',
								'smt/smt_product_list_model','smt/smt_product_detail_model',
								'smt/smt_product_skus_model','sharepage'
								)
							);
		$this->load->library('phpexcel/PHPExcel');
		$this->model = $this->smt_user_tokens_model;
	}
	
	function export_view(){
	   $accountArr = $this->model->getSmtTokenList(array());
	   $data = array(
	     'accountArr' => $accountArr
	   );
	   $this->_template('admin/export_data/smt_export_view',$data);
	}
	function export_excel(){
	  
	  $post = $this->input->post();
	  
	  $token_id = $post['account'];
	  
	  $start_time = $post['import_start'];//刊登时间筛选的开始时间
	  
	  $end_time = $post['import_end'];//刊登时间筛选的结束时间
	  
	  $sql="select 
	      		pl.productId,pl.product_url,pl.categoryId,pl.productStatusType,ps.skuCode,pd.detail,pl.token_id,pd.imageURLs,
	      		pl.subject,pl.grossWeight,ps.ipmSkuStock,ps.skuPrice,ps.lowerPrice,ps.discountRate
	        from 
			smt_product_list pl inner join smt_product_detail pd on pl.productId=pd.productId
		    inner join smt_product_skus ps on pd.productId=ps.productId
			where pl.token_id={$token_id} and pl.isRemove=0 and ps.isRemove=0
		";
	 
	  if($start_time!=='' && $end_time!==''){
	    $sql .= "and pl.gmtCreate>='{$start_time}' and pl.gmtCreate<'{$end_time}'";
	  }

	  $resultArr = $this->smt_product_list_model->result_array($sql);

	  $phpExcel=new PHPExcel();
	  $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
	  $cacheSettings = array ('memoryCacheSize' => '1024MB' );
	  PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod, $cacheSettings );
	  $phpExcel->getActiveSheet()
        ->setCellValue('A1', 'sku')
        ->setCellValue('B1', 'body')
        ->setCellValue('C1', '分类')
        ->setCellValue('D1', '销售账号tokenID')
        ->setCellValue('E1', '产品标题')
        ->setCellValue('F1', '产品重量')
        ->setCellValue('G1', '实际库存')
        ->setCellValue('H1', 'sku单价')
        ->setCellValue('I1', '商品ID')
        ->setCellValue('L1', '图片url')
        ->setCellValue('K1', '产品链接')
        ->setCellValue('J1', '产品状态')
        ->setCellValue('M1', '计算出来的最低售价')
        ->setCellValue('N1', '按原价和最低售价计算的折扣率');
        
	  $i = 2;
	  //组装导出的数据
	  $result = array();
	  foreach($resultArr as $k => $rA){
	  	
	  	 $count = 0;
	  	
	  	 $imageArr = array();//存放图片链接的数组
	  	 $categroy = '';//存放广告的分类
	  	 $body = '';//存放body说明
	  	 $bodys = '';

	     //获取分类
	     $categroy = $this->slme_smt_categorylist_model->getCateroryAndParentName($rA['categoryId'],'en');

	     //获取body内容，只保留<br/>标签
	     $body = strip_tags(htmlspecialchars_decode($rA['detail']),'<br>');
	     $bodys = str_replace('&nbsp;','',$body);
	     
	     //处理图片链接，多个分多行
	     if(!empty($rA['imageURLs'])){
	       $imageArr = explode(';',$rA['imageURLs']);
	     }

	     $phpExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $rA['skuCode'])
            ->setCellValue('B' . $i, !empty($bodys) ? $bodys : '')
            ->setCellValue('C' . $i, !empty($categroy) ? $categroy : '')
            ->setCellValue('D' . $i, $token_id)
            ->setCellValue('E' . $i, !empty($rA['subject']) ? $rA['subject'] : '')
            ->setCellValue('F' . $i, !empty($rA['grossWeight']) ? $rA['grossWeight'] : '')
            ->setCellValue('G' . $i, !empty($rA['ipmSkuStock']) ? $rA['ipmSkuStock'] : '')
            ->setCellValue('H' . $i, !empty($rA['skuPrice']) ? $rA['skuPrice'] : '')
            ->setCellValue('I' . $i, !empty($rA['productId']) ? $rA['productId'] : '')
            ->setCellValue('K' . $i, !empty($rA['product_url']) ? $rA['product_url'] : '')
            ->setCellValue('J' . $i, !empty($rA['productStatusType']) ? $rA['productStatusType'] : '')
            ->setCellValue('M' . $i, !empty($rA['lowerPrice']) ? $rA['lowerPrice'] : '')
            ->setCellValue('N' . $i, !empty($rA['discountRate']) ? $rA['discountRate'] : '');
          //$phpExcel->getActiveSheet()->getCell('D'.$i)->getHyperlink()->setUrl($rA['product_url']);
          
	  	 if(empty($imageArr)){//图片链接为空
            $count = 1;
            continue;
	  	 }else{
	  	 	 $img_count = $i;
		  	 foreach($imageArr as $ke => $im){
		  	   $phpExcel->setActiveSheetIndex(0)->setCellValue('L' . $img_count, $im);
		  	   $img_count = $img_count+1;
		  	 }
		  	 $count = count($imageArr);
	  	 }
	  	 
	  	 $i+=$count;
	  	 
	  }

      $phpExcel->getActiveSheet ()->setTitle ( 'smt_product' );
	  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	  header('Content-Disposition: attachment;filename="'.time().'.xlsx"');
	  header('Cache-Control: max-age=0');
	  $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
	  $objWriter->save('php://output');
		
	  die;
	
	}
}