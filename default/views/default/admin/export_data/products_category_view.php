<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">数据导出—产品分类导出</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">  
             <div class="row"> 
            	<div class="col-sm-12">
            	   <form action="<?php echo admin_base_url('export_data/products_category/deal_data')?>" method="post" id="form">
	                 	  <select name="warehouse" id="warehouse" >
						 	  <option value="">选择仓库</option>
		                      <?php foreach($warehouse as $key => $w):?>
		                       <option value="<?php echo $key?>"><?php echo $w;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  <select name="category" id="category" >
						 	  <option value="">选择分类</option>
		                      <option value="1">一级分类</option>
		                      <option value="2">二级分类</option>
	                 	  </select>
		             <label>
						<a class="btn btn-primary btn-sm" id="export">导出数据</a>
					</label>
					
				 </form>
				</div>
			 </div>
				
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$("#export").click(function(){
	  var category = $("#category").val();
	  var warehouse = $("#warehouse").val();
	  
	  if(warehouse==''){
			layer.alert('仓库为必选项',8);
			return false;
	  }

	  if(category==''){
			layer.alert('请选择分类级别',8);
			return false;
	  }
	  
	  $("#form").submit();
	});
</script>