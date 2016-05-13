<style>
body,html{
  margin:0;
  padding:0;
  border:0;
}
#main{
	width:840px;
	background:#fff;
}
.paging{page-break-after :always}   
.info{
  width:740px;
  height:130px;
  margin:0 auto;
}
.bottom{
  margin-bottom:10px;
  padding-left:10px;
}
.title{
    font-size:16px;
    font-weight:bold;
    text-align:center;
	margin:0;
}
.info p span{
	border-bottom:1px solid #000;
	width:100px;
    display:inline-block;
	text-align:center;
}
.ware{
	text-align:left;
	font-size:14px;
    font-weight:bold;
	margin-bottom:0;
	height:40px;
	width:500px;
	display:inline-block;
}
.code{
	width:240px;
	height:40px;
 	display:inline-block;
 	float:right;
	font-size:14px;
    font-weight:bold;
}
.detail{
	font-size:10px;
	font-weight:bold;
	margin:0;
}
table{
	width:820px;
	margin:0 auto;
	margin-top:10px;
	text-align:center;
}
</style>

  <?php         //只有深圳仓单品单件和单品多件需要分仓打印拣货单
  $i=1;
  if($pickInfo['type'] !== '3' && $pickInfo['warehouseTitle'] !== '义乌仓'){     //多品多件和义乌仓不需要分仓
	  $data = array();    //初始化2号仓
	  foreach($productInfo as $k=>$v){
		  if($v['products_location']){   
			  $j = '';
			  $location = explode('-',$v['products_location']);   //字符串-截取
					   $num = strlen(ltrim($location['0'],'.'));  //第一个的字符串长度
					   $j = substr($location['0'],0,1);
					   if($num == 3 || $num == 2){                //长度为2或者3
					         if($num == 3){                       //当长度是3时
								 if($j !== 'A' || $j !== 'B' ){   //当长度是3时，首字母不为A或B
								     $v['type'] = 2;
						             $data[] = $v;
									 unset($productInfo[$k]);      //释放数组
						      }
						   }
					   }else{   //长度不为2或3  为2号仓单子
					       $v['type'] = 2;
						   $data[] = $v;
						   unset($productInfo[$k]);               //释放数组
					   }
		  }
	  }
	  $o = array('0');    //循环数组时 默认多循环一次
	  $data = array_merge($o,$data);
	  $productInfo = array_merge($productInfo,$data);     //合并1,2仓
	  } 
  $f = 1;
  $t = true;
  $all = count($productInfo);   //合并后即计算数量
  
  $productInfo[$all] = $o;
  $productInfo[$all+1] = $o;
  $l = '';
  
  foreach($productInfo as $k => $v){
	if($pickInfo['type'] !== '3'){    //多品多件不分仓
	 if(isset($v['type']) && $i !== 1 && $t){    //拼接 两个仓库分别打印不同面单
			$i = 0;
			$t = false;
			$productInfo[$all] = $v;
			$v = '';
		}
		if($f == $all){    //循环次数已经等于总数量时   进行数据交换
			$t = $v;
			$v = $productInfo[$all];
			$l = $f +1;
		}
		if($f == $l){
			$v = $t;
		}
		}
      if($i==1 || ($i%33) == 1 && isset($v['product_sku'])){   //有product_sku时  进行头部循环
      $totalnum = 0;
  ?>
  <div id="main">
  <div class="info">
    <p class="title">仓库发货拣货单（面单尺寸：<?php echo $pickInfo['template_size'];?>）</p>
    <div>
	    <p class="ware">
	    	<b style="margin-top:10px;display:inline-block;">仓库:<span><?php if($pickInfo['warehouseTitle'] == '义乌仓'){echo "义乌仓";}else if(isset($v['type']) && $pickInfo['type'] !== '3'){echo "深圳2仓";}else if($pickInfo['type'] !== '3'){echo "深圳1仓";}else{echo  "深圳仓";}?></span></b>
	    	<span style="border:none;"></span><span style="width:50px;border:none;"></span>
	    	<b style="margin-top:10px;display:inline-block;">类型:<span><?php echo $type_text[$pickInfo['type']]?></span></b>
	    	<span style="border:none;"></span><span style="width:50px;border:none;"></span>
	    </p>
	   
	    <div class="code">
	    	  <span style="display:inline-block;height:40px;float:left;padding-top:10px;">单号：</span>
	    	  <span style="display:inline-block;text-align:center;float:left;">
	    		  <img src="<?php echo site_url('default/third_party')?>/chanage_code/barcode/html/image.php?code=code128&o=2&t=13&r=2&text=<?php echo $pickInfo['id']?>&f1=-1&f2=8&a1=&a2=B&a3=" style="display:block;"/>
	    		  <b style="font-size:12px;display:block;"><?php echo $pickInfo['id'].'-'.ceil($f/33);?></b>
	    	  </span>
	     </div>
     </div>
     <p class="detail">
       	建单时间:<span><?php echo date('Y-m-d H:i',$pickInfo['create_time'])?></span><span style="width:50px;border:none;"></span>
       	建单人:<span><?php echo $create_username;?></span><span style="width:50px;border:none;"></span>
       	打印时间:<span><?php echo date('Y-m-d H:i',$print_time)?></span><span style="width:30px;border:none;"></span>
       	打单员:<span><?php echo $print_username?></span>
     </p>
     <p class="detail" style="margin-top:10px;font-size:12px;overflow:hidden;">
     	物流渠道:<?php echo $shipmentTitle['shipmentTitle']?>
     </p>
  </div>
  
  <table border="1">
    <tr style="font-weight:bold;">
      <td style="width:30px;"></td>
      <td style="width:100px;">储位</td>
      <td style="width:100px;">SKU</td>
      <td style="width:40px;">数量</td>
      <td style="width:140px;">注意事项</td>
      <td style="width:330px;">品名</td>
    </tr>
   <?php }if($i !== 0 && isset($v['product_sku'])){  //$i不等于0
   ?>            
    <tr>
      <td style="width:30px;"><?php echo $i;?></td>
      <td style="width:100px;"><?php echo $v['products_location']?></td>
      <td style="width:100px;"><?php echo $v['product_sku']?></td>
      <td style="width:40px;"><?php echo $v['product_num']?></td>
      <td style="width:140px;"><?php echo $v['products_warring_string']?></td>
      <td style="width:330px;"><?php echo $v['products_name_cn']?></td>
    </tr>
   <?php
   }
      $totalnum+=$v['product_num']; 
      if(($i%33) == 0 || $i==$all+1 || $f==$all+1){
   ?>
    <tr style="font-weight:bold;">
      <td colspan="6">总数：<?php echo $totalnum?></td>
    </tr>
  </table>
  
  <div style="width:840px;height:40px;margin-top:10px;text-align:center;">
    <p style="float:left;width:250px;line-height:40px;font-weight:bold;">面单尺寸：<?php echo $pickInfo['template_size'];?></p>
    <p style="float:left;width:200px;line-height:40px;font-weight:bold;">类型:<?php echo $type_text[$pickInfo['type']]?></p>
    <p class="code" style="float:left;">
	    	  <span style="display:inline-block;height:40px;float:left;padding-top:10px;">单号：</span>
	    	  <span style="display:inline-block;text-align:center;float:left;">
	    		  <img src="<?php echo site_url('default/third_party')?>/chanage_code/barcode/html/image.php?code=code128&o=2&t=13&r=2&text=<?php echo $pickInfo['id']?>&f1=-1&f2=8&a1=&a2=B&a3=" style="display:block;"/>
	    		  <b style="font-size:12px;display:block;"><?php echo $pickInfo['id'].'-'.ceil($f/33);?></b>
	    	  </span>
	</p>
  </div>
  
  </div>
  
  
  
  <div class="paging"></div>
   <?php
     }
     $i++;
	 $f++;
     }
    ?>
  