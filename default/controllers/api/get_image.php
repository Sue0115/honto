<?php
  		$dirName  = $_GET['dirName'];//传过来的sku名称
        $opt      = isset($_GET['dir']) ? $_GET['dir'] : '';//是否实拍

        //本程序的上级目录
        //$topDir = str_replace('\\', '/', dirname(ROOTPATH));

        //图片库中位置
        $ebayPicDir = 'upload';

        $skuDir = $ebayPicDir . '/' . $dirName;
        
        $img_url = "http://imgurl.moonarstore.com";

        if (!empty($opt)){
        	if($opt=='SP'){
	        	$spArray = array('SP', 'sp', 'Sp', 'sP');
	            $hasFlag = false;
	            foreach ($spArray as $sp){
	                $skuDir = $ebayPicDir . '/' . $dirName.'/'.$sp;
	                if (file_exists($skuDir)){ //文件夹还是存在的
	                    $hasFlag = true;
	                    break;
	                }
	            }
        	}else{
        	 	$skuDir = $ebayPicDir . '/' . $dirName.'/'.$opt;
	                if (file_exists($skuDir)){ //文件夹还是存在的
	                    $hasFlag = true;
	            }
        	}
            
            if (!$hasFlag){
                echo json_encode('SKU对应的文件夹不存在(若发现该SKU目录的名称含有小写，请让修改)');
                die;
            }
        }else {
            if (!file_exists($skuDir)) { //SKU对应的文件夹不存在
                echo json_encode('SKU对应的文件夹不存在(若发现该SKU目录的名称含有小写，请让修改)');
                die;
            }
        }

        if (!is_dir($skuDir)) {
            echo json_encode('SKU对应的信息不是文件夹，请检查路径');
            die;
        }

        $dh = opendir($skuDir);

        $success = array();
  
        while ($file = readdir($dh)) {
            if ($file != '.' && $file != '..' && !is_dir($skuDir . '/' . $file)) {
                     $success[] = $img_url.'/'.$skuDir . '/' . $file;

            }
        }
        closedir($dh);
        echo json_encode($success);
        die;
