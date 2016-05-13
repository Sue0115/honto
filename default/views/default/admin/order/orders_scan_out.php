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
</style>
<!-- 第一次扫描挂号码界面 -->
<div id="mainContent">
      <div class="table-header">扫描挂号码</div>
	  <form id="ordersIDSubmit" method="post" action="<?php echo admin_base_url('order/scan_out/sku_info')?>">
	    <input type="text" id="inputCode" name="trackNumber"/>
	    <input type="hidden" id="orderID" name="orderID" value="" />
	  </form>
	  <div class="error"></div>
</div>

<script type="text/javascript">
$(function(){
	$("#inputCode").val('');
	$("#inputCode").focus();
});
$("form").submit(function(e){  
   var trackNumber = $("[name='trackNumber']").val();
   if(trackNumber==''){
	  alert('请输入挂号码');
	  return false;
   }

	$.ajax( {     
		  url:"<?php echo admin_base_url('order/scan_out/check_do_scan')?>",// 跳转到 action     
		  data:{"TrackNumber":trackNumber},     
		  type:'post',     
		  async:false,
		  cache:false,     
		  dataType:'json',     
		  success:function(data) {
			  result = eval(data);
			 if(result['status'] === true){
				//跳到复检界面
				$("#orderID").val(result['erp_orders_id']);
			 }else{
				 $(".error").text(result['msg']);
				 $("#inputCode").val("");
		     }
		  }        
	});
	if($(".error").text() != '' && $("#inputCode").val() == ''){
		   return false; 
	}
});
	  
</script>