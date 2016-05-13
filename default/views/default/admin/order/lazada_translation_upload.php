<div class="row">
   <form enctype="multipart/form-data" action="<?php echo admin_base_url('order/lazada_translation/deal_data') ?>" method="post" id="from1">
        <div class="col-xs-4">
          <input type="file" name="readfile" />
        </div>
        <div class="col-xs-3">
         <input type="submit" value="翻译" id="sub" style="width:70px;height:30px;" class="btn btn-sm btn-primary"/>
        </div>   
        <div class="col-xs-5">
          <a href="<?php echo site_url('attachments')?>/excel_template/lazada_translation_template.xlsx">模板文件下载</a>
          (<span style="color:red;">*注:只能支持excel2003和2007</span>)
        </div>  
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		
	</form>
              
</div>

