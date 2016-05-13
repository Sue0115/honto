<div class="table-header">
  <span>扫描/录入SKU：</span>
  <input type="text" id="sku"  name="sku" value="" />
  <input type="hidden" id="has_scan_num" value="" />
</div>

<table class="table  table-bordered">
				    <colgroup>
				       <col width="30%">
				       <col width="30%">
				       <col width="40%">
				       
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>sku名称</th>
                            <th>应发数</th>
                            <th>已扫描数</th>
	                    </tr>
	                </thead>
	                <tbody id="tbody_content" >
	                 <?php 
	                 		$sku_num = 0;
	                 		$orderID = !empty($productData) ? $productData[0]['erp_orders_id'] : 0;
	                 		foreach($productData as $v):
	                 	  
	                       $sku_num += $v['item_count'];
	                 ?>
		                  <tr id="<?php echo $v['orders_sku'].'_row'?>" style="display:none;">
		                    <td><span id="<?php echo $v['orders_sku']?>"><?php echo $v['orders_sku']?></span></td>
		                    <td><span id="<?php echo $v['orders_sku'].'_itemCount'?>"><?php echo $v['item_count']?></span></td>
		                    <td><span id="<?php echo $v['orders_sku'].'_num'?>">0</span></td>
		                  </tr>
	                  <?php endforeach;?>
	                </tbody>
</table>

<script type="text/javascript">
$(function(){
	$("#sku").focus();
	$("#sku").change(function(){

		//扫描的sku
		var sku = $("#sku").val();
	    if(sku==''){
		  alert('请扫入sku');
		  return false;
		}
		var orderID = <?php echo $orderID?>;

		//获取已经扫描的次数
		var has_scan_num = $("#has_scan_num").val();
		has_scan_num++;

		//所有sku的总数
		var sku_count = <?php echo $sku_num?>;

	    //获取扫描sku原本的数量
	    var item_count=$("#"+sku+"_itemCount").text();
	    if(item_count==''){
			alert(sku+'不存在，请重新扫描或者刷新页面');
			return false;
	    }

	    //获取扫描的sku的数量
	    var sku_num = $("#"+sku+"_num").text();

	    //给扫描sku的扫描数量+1
	    sku_num++

	    //判断扫描数量和sku原本的数量是否一致
	    if(sku_num>item_count){
	     alert('扫描数量已经超出该sku的数量');
	     $("#sku").val('');
	     $("#sku").focus();
	     return false;
	    }else{
	      $("#"+sku+"_row").show();
	      $("#"+sku+"_num").text(sku_num);
	      $("#has_scan_num").val(has_scan_num);
	      $("#sku").val('');
	      $("#sku").focus();
	    }

	    //如果已扫描sku的总数==sku总数，执行出库扣库存
	    if(has_scan_num==sku_count){
	    	$.ajax( {     
	    		  url:"<?php echo admin_base_url('order/scan_out/dealStock')?>",// 跳转到 action     
	    		  data:{"orderID":orderID},     
	    		  type:'post',     
	    		  async:false,
	    		  cache:false,     
	    		  dataType:'json',     
	    		  success:function(data) {
	    			  result = eval(data);
	    			  if(result['status']==1){
						window.location.href="/admin/order/scan_out/";
	        		  }else{
						alert(result['msg']);
	            	  }
	    		  }        
	    	});
	    }
	    $("#sku").val('');
	    $("#sku").focus();
	});

});

</script>