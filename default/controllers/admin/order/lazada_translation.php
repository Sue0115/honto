<?php

class lazada_translation extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'sharepage','order/orders_model'
								)
							);

		$this->model = $this->orders_model;
		
	}
	
	//导入界面
	function index(){
	  $this->_template('admin/order/lazada_translation');
	}
	
	//上传界面
	function lazada_translation_upload(){
	   $this->template('admin/order/lazada_translation_upload');
	}
	
	//处理导入的表格将泰文替换中文
	function deal_data(){

		$this->load->library(array('phpexcel/PHPExcel'));//载入excel类
        
        $uploadFiles = $_FILES["readfile"]["tmp_name"];	//临时存储目录

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
		
		/**从第二行开始输出，因为excel表中第一行为列名*/ 
		for($currentRow = 2;$currentRow <= $allRow;$currentRow++){ 
		/**从第A列开始输出*/ 
			for($currentColumn= 65;$currentColumn<= 87; $currentColumn++){ 
				if($currentColumn==66 || $currentColumn==71 || $currentColumn==73 || $currentColumn==74){
				  $pronum= $currentSheet->getCellByColumnAndRow($currentColumn - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
				   
				  $data[$currentRow][] = $pronum;
				}
				continue;
			}
		}
		$newData = array();
		foreach($data as $d){
		  if(isset($newData[$d[0]])){
		    continue;
		  }
		  $newData[$d[0]] = $d;
		}

		$result = array();//存放结果数据
		
		//批量更新订单地址内容，以buyer_id为条件
		foreach($newData as $nd){
		  $option = array();
		  $up = array();
		  $up['buyer_name'] = $nd[1];
		  $up['buyer_address_1'] = $nd[2];
		  $up['buyer_address_2'] = '';
		  $up['buyer_city'] = $nd[3];
		  $option['where'] = array(
		    'buyer_id' => $nd[0],
		  	'orders_status <=' => 3,
		    'orders_is_join' => 0
		  );
		  $tof = $this->model->update($up,$option);
		  
		  if($tof){
		    echo '<span style="color:green">买家ID为'.$nd[0].'的姓名和地址更新成功</span><br/>';
		  }else{
		    echo '<span style="color:red">买家ID为'.$nd[0].'的姓名和地址更新失败</span><br/>';
		  }
		  
		}
		
		exit;
	}
}