<div class="page-header">
    <h1>
        仓库操作
        <small>
            <i class="icon-double-angle-right"></i>
		        生成拣货单
        </small>
    </h1>
</div>
<link href="<?php echo static_url('theme/icheck/skins/all.css')?>" rel="stylesheet">
<script src="<?php echo static_url('theme/icheck/icheck.min.js')?>"></script>
<?php
    echo ace_form_open('','',array('id'=>$item->id));
    
?>

	    <div class="row">
	       <div class="col-xs-12">
			
			      <input type="hidden" name="id" value="<?php echo $item->id?>" />
				  
				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right"> <span class="red">*</span>仓库</label>
				    <div class="col-xs-12 col-sm-5">
				        <select name="warehouse" id="warehouse">
                            <?php foreach($warehouse as $key => $v){?>
                            <option value="<?php echo $v['warehouseID']?>"><?php echo $v['warehouseTitle']?></option>
                            <?php }?>
                        </select>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>

				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right"> <span class="red">*</span>拣货单类型</label>
				    <div class="col-xs-12 col-sm-5">
				        <select name="type" id="type">
                            <?php foreach($type_text as $key => $v){?>
                            <option value="<?php echo $key?>"><?php echo $v?></option>
                            <?php }?>
                        </select>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>

				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right"> <span class="red">*</span>订单类型</label>
				    <div class="col-xs-12 col-sm-5">
				        <select name="order_type" id="order_type">
				        	<option value="0">所有</option>
                            <?php foreach($order_type as $v){?>
                            <option value="<?php echo $v->id?>"><?php echo $v->name?></option>
                            <?php }?>
                        </select>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>

                  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right"> <span class="red">*</span>物流</label>
				    <div class="col-xs-12 col-sm-5">
				    	<?php foreach($shipment as $v){?>
				    		<div class="col-sm-4" style="margin-bottom:5px;"><input type="checkbox" page-data="<?php echo $v['is_check']?>" class="shipment-show" name="shipment_id[]" value="<?php echo $v['shipmentID']?>" <?php echo $v['is_check']?>><?php echo $v['shipmentTitle']?></div>
				    	<?php }?>

				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>

				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right"></label>
				    <div class="col-xs-12 col-sm-5">
				    	 <span class="btn btn-success" onclick="checkbox_do('select_other')">反选</span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>
                  
				 <div class="form-group">
				    <label for="channel_name" class="col-xs-12 col-sm-2 control-label no-padding-right">
			            <span class="red">*</span>订单数
			        </label>
				    <div class="col-xs-12 col-sm-5">
				        <span class="input-icon block input-icon-right">
                        	<input type="text" value="<?php echo $item->order_num ? $item->order_num : 2100 ?>" class="width-100" name="order_num"  datatype="n" errormsg="请输入订单数量" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
			        <div class="help-block col-xs-12 col-sm-reset inline">
	                                                      
			        </div>
				  </div>
				  
				  
				  <div class="form-group">
				    <label for="channel_name" class="col-xs-12 col-sm-2 control-label no-padding-right">
			            <span class="red">*</span>拣货单单号
			        </label>
				    <div class="col-xs-12 col-sm-5">
				        <span class="input-icon block input-icon-right">
                        	<?php echo $item->id ?>
                        </span>
				    </div>
			        
				  </div>
				  

	       </div>
	    </div>
<?php 
        echo ace_srbtn('order/pick_manage');      
      
        echo ace_form_close();
?>
    
	<script>
	    $(function(){
			
			var order_type = '0';
			$("#order_type").val(order_type);

			$('input').iCheck({
			   checkboxClass: 'icheckbox_square-green',
			   increaseArea: '20%' // optional
			});

	    })

	    //复选框操作函数
	   function checkbox_do(page_size){

		   	var all_cleck = $(".shipment-show");

		   	if(page_size == "1"){

		   	}

		   	//反选
		   	if(page_size == "select_other"){
		   		
		   		var is_check = "";
		   		
		   		all_cleck.each(function(){
		   			is_check = $(this).prop("checked");
		   			
		   			if(is_check){
		   				$(this).prop("checked",false);
		   			}else{
		   				$(this).prop("checked",true);
		   			}
		   		});

		   		 location.reload();

		   	}

	   }

	</script>
  