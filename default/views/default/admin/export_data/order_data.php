<?php header("Content-type:text/html;Charset=utf-8");?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">数据导出—订单数据</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">  
             <div class="row"> 
            	<div class="col-sm-12">
            	   <form action="<?php echo admin_base_url('export_data/export_order_data/deal_data')?>" method="post" id="form">
            	  			
            	   		  <select name="template" id="template" >
						 	  <option value="">请选择导出模板</option>
		                      <?php foreach($templateArr as $k => $temp):?>
		                       <option value="<?php echo $k?>"><?php echo $temp;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  			 		
	                      <select name="orders_type" id="orders_type" >
						 	  <option value="">订单平台</option>
		                      <?php foreach($orders_type_arr as $ks => $ot):?>
		                       <option value="<?php echo $ks?>"><?php echo $ot['typeName'];?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  
	                 	  <select name="orders_status" id="orders_status" >
						 	  <option value="">订单状态</option>
		                      <?php foreach($orders_status as $ke => $os):?>
		                       <option value="<?php echo $ke?>"><?php echo $os;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  
	                 	  <select name="shipmentID" id="shipmentID" >
						 	  <option value="">选择物流</option>
		                      <?php foreach($shipmentArr as $s => $ship):?>
		                       <option value="<?php echo $s?>"><?php echo $s.'-'.$ship;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  
	                 	  <select name="warehouse" id="warehouse" >
						 	  <option value="">仓库</option>
		                      <?php foreach($warehouse as $key => $w):?>
		                       <option value="<?php echo $key?>"><?php echo $w;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  
	                 	  销售账号：<input type="text" name="sale_account" value="" id="sale_account"/>
	                 	  
	                 	  <br/>
	                       ERP导入时间
	                     	<input type="text"  value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="start_date" name="import_start" size="15"/>
	                         ~
	                        <input type="text" value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="end_date" name="import_end" size="15"/>
	                        
	                                                   发货时间
	                     	<input type="text"  value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="start_date" name="ship_start" size="15"/>
	                         ~
	                        <input type="text" value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="end_date" name="ship_end" size="15"/>
	                        
	                                                   通关时间
	                     	<input type="text"  value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="clearance_start_time" name="clearance_start_time" size="15"/>
	                         ~
	                        <input type="text" value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="clearance_end_time" name="clearance_end_time" size="15"/>
	        			  
	                 &nbsp;&nbsp;&nbsp;&nbsp;
		             <label>
						<a class="btn btn-primary btn-sm" id="export">导出数据</a>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" href="<?php admin_base_url('export_data/export_order_data/order_data')?>">清空</a>
					</label>
					<br/><span style="color:green;font-weight:bold;">时间筛选：例如（导出7月1日数据，时间段应选7月1日至7月2日）</span>
				 </form>
				</div>
			 </div>
				
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(function(){ 
		$(document).on('click','.Wdate',function(){ 
			var o = $(this); 
			if(o.attr('dateFmt') != '') 
			WdatePicker({dateFmt:o.attr('dateFmt')}); 
			else if(o.hasClass('month')) 
			WdatePicker({dateFmt:'yyyy-MM'}); 
			else if(o.hasClass('year')) 
			WdatePicker({dateFmt:'yyyy'}); 
			else 
			WdatePicker({dateFmt:'yyyy-MM-dd'}); 
		}); 
	}); 
	$("#export").click(function(){
	  $("#form").submit();
	});
	$("#orders_type").change(function(){
		var order_type=$("#orders_type").val();
		var smt_account='';
		var wish_account='';
		$("#account").remove();
		if(order_type==6 || order_type==13){
			$.ajax( {     
		  		  url:'<?php echo admin_base_url('export_data/export_order_data/getSmtAccount')?>',  
		  		  dataType:'json',
			      data:{"type":order_type},
			  	  type: 'POST',
			      async:false,
				  cache:false,       
		  		  success:function(data) {
			  		 if(order_type==6){
			  			smt_account = '<select name="account" id="account" >';
			 		   	 $.each(data,function(n,value) {
				 		   		smt_account+='<option value="'+value+'">'+value+'</option>';
				 	     })
					 	 smt_account+='</select>'; 
					 	 $("#orders_type").after(smt_account);   
				  	 }else{
				  		 wish_account = '<select name="account" id="account" >';
			 		   	 $.each(data,function(n,value) {
				 		   		wish_account+='<option value="'+value.account_name+'">'+value.account_name+'</option>';
				 	     })
					 	 wish_account+='</select>'; 
					 	 $("#orders_type").after(wish_account); 
					 }
			    	 
				 	 
				 	 $("#sale_account").attr('disabled',true);
		 		  }

			});
		}
		
	});
</script>