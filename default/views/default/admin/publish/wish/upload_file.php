<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">请选择要上传的文件</h3>
    </div>
</div>
<div class="row">
   <form enctype="multipart/form-data" action="<?php echo admin_base_url('publish/wish/do_deal_upload_file') ?>" method="post">
        <div class="col-xs-3">
          <input type="file" name="readfile" />
        </div>
        <div class="col-xs-3">
         <input type="submit" value="导入" id="sub" style="width:70px;height:30px;" class="btn btn-sm btn-primary"/>
        </div>   
        <div class="col-xs-5">
          <a href="<?php echo site_url('attachments')?>/excel_template/wish_auto_publish_template.xls">模板文件下载</a>
          (<span style="color:red;">*注:只能导入excel文件</span>)
        </div>  
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
		<?php foreach($token_array as $token):?>
            <input type="hidden" name="token_id[]" value="<?php echo $token?>" />
        <?php endforeach;?>     
	</form>
              
</div>
<script type="text/javascript">
$("#sub").click(function(){
	var file=$('[name=file]').val();
	if(file==''){
		alert('请选择一个文件');
		return false;
	}
});
</script>