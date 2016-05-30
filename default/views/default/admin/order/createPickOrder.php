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

  <?php 
  $i=1;
  $all = count($productInfo); 
  foreach($productInfo as $k => $v){
    if($i==1 || ($i%33) == 1){
      $totalnum = 0;
  ?>
  <div id="main">
  <div class="info">
    <p class="title">仓库发货拣货单（面单尺寸：<?php echo $pickInfo['template_size'];?>）</p>
    <div>
	    <p class="ware">
	    	<b style="margin-top:10px;display:inline-block;">仓库:<span><?php echo $pickInfo['warehouseTitle'];?></span></b>
	    	<span style="border:none;"></span><span style="width:50px;border:none;"></span>
	    	<b style="margin-top:10px;display:inline-block;">类型:<span><?php echo $type_text[$pickInfo['type']]?></span></b>
	    	<span style="border:none;"></span><span style="width:50px;border:none;"></span>
	    </p>
	   
	    <div class="code">
	    	  <span style="display:inline-block;height:40px;float:left;padding-top:10px;">单号：</span>
	    	  <span style="display:inline-block;text-align:center;float:left;">
	    		  <img src="<?php echo site_url('default/third_party')?>/chanage_code/barcode/html/image.php?code=code128&o=2&t=13&r=2&text=<?php echo $pickInfo['id']?>&f1=-1&f2=8&a1=&a2=B&a3=" style="display:block;"/>
	    		  <b style="font-size:12px;display:block;"><?php echo $pickInfo['id'].'-'.ceil($i/33);?></b>
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
   <?php }?>
    <tr>
      <td style="width:30px;"><?php echo $i;?></td>
      <td style="width:100px;"><?php echo $v['products_location']?></td>
      <td style="width:100px;"><?php echo $v['product_sku']?></td>
      <td style="width:40px;"><?php echo $v['product_num']?></td>
      <td style="width:140px;"><?php echo $v['products_warring_string']?></td>
      <td style="width:330px;"><?php echo $v['products_name_cn']?></td>
    </tr>
   <?php
      $totalnum+=$v['product_num']; 
      if(($i%33) == 0 || $i==$all){
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
	    		  <b style="font-size:12px;display:block;"><?php echo $pickInfo['id'].'-'.ceil($i/33);?></b>
	    	  </span>
	</p>
  </div>
  
  </div>
  
  
  
  <div class="paging"></div>
   <?php
     }
     $i++;
     }
    ?>
  