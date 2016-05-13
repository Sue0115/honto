<style>
 #inputCode{
	width:1690px;
	height:50px;
    font-size:26px;
    font-weight:bold;
 }
 #mainContent{
  height:auto;
 }
 .error{
	font-size:16px;
    font-weight:bold;
    color:red;
 }
 .orderInfo{
	width:1673px;
    height:100px;
    background:#eee;
 }
.order_product_Info{
	width:1673px;
    height:auto;
    background:#eee;
 }
 .content{
	width:1650px;
    height:80px;
    margin:0 0 0 20px;
    padding-top:15px;
    font-weight:bold;
 }
 #productsInfo{
    width:1000px;
 }
 #erp_orders_id{
  width: 100%;
  font-size: 55px;
  border: #CC0000 2px solid;
  height: 60px;
  line-height: 60px;
  font-weight: bold;
 }
 #shipping_code{
  width: 100%;
  font-size: 30px;
  border: #0000CC 2px solid;
  height: 30px;
  line-height: 30px;
  font-weight: bold;
 }
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">
        	当前位置：查看包装方式
        	<a href="<?php echo admin_base_url('order/shippingAndRecheck')?>">
	           <button class="btn btn-sm btn-primary" style="float:right;margin-right:30px;width:80px;color:#fff;">
	               	回退
	           </button>
            </a>
        </h3>
        
    </div>
</div>
<div id="mainContent">
	<form name="formScan" id="formScan" method="post" action="<?php echo admin_base_url('order/shippingAndRecheck/orderShippedStock')?>">
	     <input name="oID" value="<?php echo $ordersInfo['erp_orders_id'];?>" type="hidden" />

	     <input name="TrackNumber" value="<?php echo $ordersInfo['orders_shipping_code'];?>" type="hidden" />
	     <div class="table-header">订单基本信息</div>
	     <div class="orderInfo">
	       <div class="content">
	         	收件人：<?php echo $ordersInfo['buyer_name']?>&nbsp;&nbsp;&nbsp;&nbsp;
	         	电话：<?php echo $ordersInfo['buyer_phone']?>&nbsp;&nbsp;&nbsp;&nbsp;
	         	国家：<?php echo $ordersInfo['buyer_country']?>&nbsp;&nbsp;&nbsp;&nbsp;
	         	邮编：<?php echo $ordersInfo['buyer_zip']?>&nbsp;&nbsp;&nbsp;&nbsp;<br/>
	         	地址：<?php echo $ordersInfo['buyer_address_1'].' '.$ordersInfo['buyer_address_2']?>&nbsp;&nbsp;&nbsp;&nbsp;
	         	城市：<?php echo $ordersInfo['buyer_city']?>&nbsp;&nbsp;&nbsp;&nbsp;
	         	州/省：<?php echo $ordersInfo['buyer_state']?>&nbsp;&nbsp;&nbsp;&nbsp;<br/>
	         	发货方式：<?php echo $ordersInfo['shipping_method']?>&nbsp;&nbsp;&nbsp;&nbsp;
	         	运费：<?php echo $ordersInfo['orders_ship_fee']?>
	       </div>
	     </div>
	     
	     <div class="table-header" style="margin-top:20px;">订单物品清单</div>
	     <div class="order_product_Info">
		      <table id="productsInfo" class="table table-striped table-bordered table-hover dataTable">
				<colgroup>
					<col width="30%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
				</colgroup>
			    <thead>
			        <tr>
			           <th>图片预览</th>	                   
		               <th>sku</th>
		               <th>物品名称</th>
		               <th>应发数</th>
		            </tr>
			   </thead>
			   <?php foreach($productsInfo as $v):?>
			   <tr align="center">
			     <td rowspan="2" ><img src="<?php echo $v['showImg']?>" style="width:200px;height:200px;"/></td>
			     <td><?php echo $v['orders_sku']?></td>
			     <td><?php echo $v['products_name_cn']?></td>
			     <td><input type="text" id="<?php echo strtoupper($v['orders_sku'])?>" value="<?php echo $v['item_count']?>" name="skucount" size="10" style="text-align:center;" readonly/></td>
			   </tr>
			   <tr align="center">
			     <td colspan="4">
			        <?php echo $v['products_warring_string'] == '' ? '' : '<span style="color:red;font-size:22px;">【注意事项：'.$v['products_warring_string'].'】</span><br/>';?>
			         <span style="color:red;font-size:30px;"><?php echo $ordersInfo['orders_remark'].' '.$v['adapter'];?></span><br/>
			         <span style="color:blue;font-size:22px;"> <?php echo !empty($v['packArr']) ? '【包装方式：' . $v['packArr']['code'] .'-' . $v['packArr']['title'] . '】' : '【包装方式：无】';  ?></span>
			     </td>
			   </tr>
			   <?php endforeach;?>
			 </table>
	     </div>
	   </form>
   
</div>
