<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
header("Content-type: text/html; charset=utf-8");
class print_no_pack extends Admin_Controller{
	
	function __construct(){
		
		parent::__construct();

		$this->load->model(array(
								'order/pick_model','shipment/shipment_model',
								'order/pick_product_model','order/orders_model',
								'print/orders_products_model','print/products_data_model',
								)
							);
	}
	
	/**
	 * type='' 多品多件复检扫描界面的打印未包装订单
	 * type=1    多品多件扫描界面里的未包装订单按钮
	 */
	function index(){
		
	   $pick_id = $this->input->get_post('pick_id');
	   
	   //根据拣货单id获取拣货单所属物流
	   $pickInfo = $this->pick_model->get_info_by_pick_id($pick_id);
	   
	   $warehouse = $this->input->get_post('warehouse');
	   
	   $type = $this->input->get_post('type');
	   
	   //获取该拣货单下，status=3的订单号
	   $option = array();
	   
	   $select = array('orders_id','product_sku','product_num','basket_num');
	  
	   $where = array();
	   if($type==1){
		    $where = array(
		     'status <' => 3,
		     'pick_id'=> $pick_id
		   );
	   }else{
		    $where = array(
		     'status' => 3,
		     'pick_id'=> $pick_id
		   );
	   }
	  
	   $option = array(
	     'where' => $where,
	     'select'=> $select,
	     'order'  => 'scan_time'
	   );
	   $dataArr = $this->pick_product_model->getAll2array($option);

	   //根据拣货单所属物流获取打印的面单尺寸
	   $shipment_id_array = explode(',', $pickInfo['shipment_id']);
	   $print_template = $this->shipment_model->get_one_get_template($shipment_id_array[0]);
	   $pickInfo['template_size'] = $print_template['page_size'];
	   
	   //新组合数组，存放相同订单号的数组数据,并获取储位信息
	   $newData = array();
	   $sku_location = array();
	   foreach($dataArr as $da){
	   	 $productInfo = array();
	   	 $skuArr=array();//存放sku和数量
		 $skuString='';//存放sku和数量的组合字符串
	   	 //根据sku和仓库id获取产品信息
	   	 $productInfo = $this->products_data_model->getProductsInfoWithSku($da['product_sku'],$warehouse);
	   	 $da['location'] = $productInfo['products_location'];
	     $newData[$da['orders_id']][] = $da;
	     $sku_location[$da['product_sku']] = $da['location'];
	   }
	  
	   //获取未扫描的sku和数量
	    if($type==1){
	      $s = array('product_sku','product_num','scan_num','orders_id');
	      $w = array(
	      	'status <' => 3,
	        'product_num > scan_num' => null,
	        'pick_id' => $pick_id
	      );
	      $op = array('select'=>$s,'where'=>$w);
	      $no_scan_arr = $this->pick_product_model->getAll2array($op,true);
	      //重组数据
	      $new_no_scan_arr = array();
	      foreach($no_scan_arr as $no){
	         //根据sku和仓库id获取产品信息
		   	 $productInfos = $this->products_data_model->getProductsInfoWithSku($no['product_sku'],$warehouse);
		   	 $no['location'] = $productInfos['products_location'];
	         $new_no_scan_arr[$no['orders_id']][] = $no;
	      }
	    }

	  if($type==1){
	    echo $this->print_no_pack_template($newData,$new_no_scan_arr,$pick_id,$pickInfo);
	  }else{
	    echo $this->order_template($newData,$pick_id,$pickInfo);
	  }
      
	}
	
	//多品多件复检扫描界面的打印未包装订单模板
	public function order_template($newData,$pick_id,$pickInfo){
		  $reStr ='
		    <html>
		      <head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>打印</title>
			  </head>
			  <body>
		  ';
		  $reStr .= '
		    <style>
			     *{margin:0;padding:0;}
				 body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
				 #main{
					width:100mm;
				    margin:0 auto;
				  }
				.order{
				  width:100mm;
				  height:100mm;
				}
		    </style>
		  ';
		  $reStr .='<div id="main">';
		  foreach($newData as $ke=>$d){
			  	$reStr .='
			  	 <div class="order">
				     <p style="text-align:center;padding-top:5px;">
				       <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=28&r=2&text='.$pick_id.'_'.$d[0]['basket_num'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br/>
				       '.$ke.'
				     </p>
				     <p>
				  ';
			  	foreach($d as $info){
			  		$reStr .='<span style="display:inline-block;padding-left:10px;">'.$info['product_sku'].'*'.$info['product_num'].'【'.$info['location'].'】</span><br/>';
			  	}
			  	$reStr .='</p>';
			  	
			  	$reStr.='<br/>
			  	   		 面单尺寸:'.$pickInfo['template_size'].'
					  	 <hr style="height:1px;border:none;border-top:1px solid #555555;"/>
					  	 </div>
					  	 ';
		  }
		  $reStr .='</div></body></html>';
		  return $reStr;	
			
	  }
	
	//多品多件扫描界面的打印未包装订单按钮的界面
	public function print_no_pack_template($newData,$new_no_scan_arr,$pick_id,$pickInfo){
		  $reStr ='
		    <html>
		      <head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>打印</title>
			  </head>
			  <body>
		  ';
		  $reStr .= '
		    <style>
			     *{margin:0;padding:0;}
				 body{ font-family:Arial, Helvetica, sans-serif,"宋体",Verdana; font-size:14px;}
				 #main{
					width:100mm;
				    margin:0 auto;
				  }
				.order{
				  width:100mm;
				  height:100mm;
				}
		    </style>
		  ';
		  $reStr .='<div id="main">';
		  foreach($newData as $ke=>$d){
			  	$reStr .='
			  	 <div class="order">
			  	     <p style="width:20%;float:left;height:78px;line-height:78px;text-align:center;font-weight:bold;font-size:18px;">
			  	       '.$d[0]['basket_num'].'
			  	     </p>
				     <p style="text-align:center;padding-top:5px;width:80%;float:right;">
				       <img src="'.site_url('default/third_party').'/chanage_code/barcode/html/image.php?code=code128&o=2&t=28&r=2&text='.$pick_id.'_'.$d[0]['basket_num'].'&f1=-1&f2=8&a1=&a2=B&a3=" /><br/>
				       '.$ke.'
				     </p>
				     <p style="clear:both;">
				  ';
			  	foreach($d as $info){
			  		$reStr .='<span style="display:inline-block;padding-left:10px;">'.$info['product_sku'].'*'.$info['product_num'].'【'.$info['location'].'】</span><br/>';
			  	}
			  	$reStr .='</p>';
			  	
			  	 $reStr.='<br/>
			  	   		 面单尺寸:'.$pickInfo['template_size'].'
					  	 <hr style="height:1px;border:none;border-top:1px solid #555555;"/>
					  	 <p style="clear:both;">
					  	 ';
			  	if(!empty($new_no_scan_arr[$ke])){
			  		 $reStr .='未扫描<br/>';
				  	 foreach($new_no_scan_arr[$ke] as $infos){
				  		$reStr .='<span style="display:inline-block;padding-left:10px;">'.$infos['product_sku'].'*'.($infos['product_num']-$infos['scan_num']).'【'.$infos['location'].'】</span><br/>';
				  	 } 
			  	}
		  		
				$reStr.='</p></div>';
		  }
		  $reStr .='</div></body></html>';
		  return $reStr;	
			
	  }
	
}
