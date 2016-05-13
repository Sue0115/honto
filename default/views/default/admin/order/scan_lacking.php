<style>
#scan_id{
 width:100%;
 height:50px;
 font-size:20px;
}
</style>
<div class="table-header">
   扫入异常订单条码     
</div>
<div>
  <input type="text" name="scan_id" id="scan_id" value="" />
</div>
<script>
$(function(){
	$("#scan_id").focus();
});
$("#scan_id").change(function(){
	
	var data = $("#scan_id").val();
	
	$.ajax( {     
		  url:"<?php echo admin_base_url('order/scan_lacking/ajax_deal_order')?>",// 跳转到 action     
		  data:{"data":data},     
		  type:'post',     
		  async:false,
		  cache:false,     
		  dataType:'json',     
		  success:function(data) {
			 layer.alert(data.msg,'');
			 $("#scan_id").focus();
		  }        
	});
});
</script>

