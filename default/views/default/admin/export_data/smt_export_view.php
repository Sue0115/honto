<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<style>
.red{
	color:red;
}
.green{
	color:green;
}
</style>

<div class="row">
    <div class="col-xs-12">
        <h3 class="table-header">smt广告数据导出</h3>
    </div>
</div>

<div class="row">&nbsp;</div>

<form action="<?php echo admin_base_url('export_data/smt_product_export/export_excel')?>" method="post" id="submitForm">

<div class="row">
	<div class="col-xs-4">
	 	账号选择：
		  <select name="account" id="account">
		    <?php foreach($accountArr as $k => $w):?>
		    <option value="<?php echo $k?>"><?php echo $k;?></option>
		    <?php endforeach;?>
		  </select>
		  刊登时间选择:
		  <input type="text"  value="" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="import_start" size="15"/>
	        ~
	      <input type="text" value="" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="import_end" size="15"/>
	                        
	</div>
	<div class="col-xs-2">
	   <label>
         <input type="submit" value="导出数据" id="sub"  class="btn btn-sm btn-primary"/>
       </label>
	</div>
</div>
</form>
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
	
</script>