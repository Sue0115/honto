<div class="row" style="height:10px;"></div>
<div class="row">
	<div class="col-sm-1">
	
	</div>
	<div class="col-sm-4">
		<select name="reason" id="reason">
		  <option value="">请选择退款原因</option>
		  <?php foreach($reason as $k => $r):?>
		   <option value="<?php echo $k?>"><?php echo $r?></option>
		  <?php endforeach;?>
		</select>
	</div>
    
</div>
<br/>
<div class="row">

	<div class="col-sm-1">
	  
	</div>
	<div class="col-sm-7" style="font-weight:bold;">
		Enter your ticket reply
	</div>
    
</div>
<div class="row">

	<div class="col-sm-1">
	 
	</div>
	<div class="col-sm-9">
		<textarea style="width:99%; resize: none;height:200px;" id="content"  name="content">
	    Dear customer, 

		Apologies for this inconvenience. We have refunded this transaction for you. Please allow 5-7 days for your refund to be processed back to your original payment method. 
		
		Thank you
	    </textarea> 
	</div>
    
</div>
<div class="row" style="height:8px;"></div>
<div class="row">
     <div class="col-xs-8 text-center">
	   <button class="btn btn btn-info" id="queren">
                <i class="bigger-110"></i>
                  	Refund Marked
       </button>
      
         </div>
</div>
<script type="text/javascript" src="<?php echo site_url('static/theme/common/layer/layer.min.js')?>"></script>
<script>

 $("#queren").click(function(){
  var content = $("#content").val();
  var reason_code = $("#reason").val();
  var wish_id = '<?php echo $wish_id?>';
  var account = '<?php echo $account?>';
  var mailID = '<?php echo $mailID?>';
  if(reason_code==''){
	  layer.alert('请选择退款原因',8);
	  return false;
  }

  $.ajax({
      url: '<?php echo admin_base_url('smt_message/wish_message_center/preplying_refurn_wish_order')?>',
      data:{'reason_code':reason_code,'content':content,'wishID':wish_id,'account':account,'mailID':mailID},
      type: 'POST',
      async:false,
	  cache:false,
      dataType: 'JSON',
      success: function(data){
          if(data.status==true){
        	  layer.alert(data.msg,9);
          }else{
                layer.alert(data.msg,8);
          }
      }
  });
  
 });
</script>