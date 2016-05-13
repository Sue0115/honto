<div class="row">
    <div class="col-xs-12">
        <h3 class="table-header">请选择要导入的文件</h3>
        
    </div>
</div>
<div class="row">
    <div class="col-xs-12 text-center">
        <label>
			<a class="btn btn-sm btn-primary" target="_blank" id="import_data">
	           	点击导入翻译表格
	         </a>
		</label> 
    </div>
</div>

<script type="text/javascript">
$(function(){
	$("#import_data").click(function(){
		//弹出上传文件层
		$.layer({
			type   : 2,
			shade  : [0.4 , '' , true],
			title  : ['上传文件',true],
			iframe : {src : '<?php echo admin_base_url("order/lazada_translation/lazada_translation_upload");?>'},
			area   : ['700px' , '350px'],
			success : function(){
				layer.shift('top', 400)
			},	
			no: function(index){
				
				layer.close(index);
			}
		});
		return false;
	});
	
});
</script>