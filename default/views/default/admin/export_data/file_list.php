<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">下载的文件</h3>
    </div>
</div>
<div class="row">

  			<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="3%">
				       <col width="3%">
				       <col width="6%">
				       <col width="15%">
				    </colgroup>
	                <thead>
	                    <tr>
	                    	<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
	                        <th class="center">序号</th>
                            <th class="center">文件名</th>
                            <th class="center">操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    <?php foreach($file_list as $k => $va):?>
                      <tr>
                        <td class="center">
								<label>
									<input type="checkbox" class="ace" name="file_url[]" value="<?php echo site_url($va['url'])?>" >
									<span class="lbl"></span>
								</label>
						</td>
                        <td class="center"><?php echo ($k+1);?></td>
                        <td class="center"><?php echo $va['name'] ?></td>
                        <td class="center"><a href="<?php echo site_url($va['url'])?>" class="files">下载</a></td>
                        
                      </tr>
                    <?php endforeach;?>

	                </tbody>
	            </table>
	             <label>
					<a class="btn btn-primary btn-sm" id="download">批量下载</a>
				</label>
        
</div>


<script type="text/javascript">
$("#sub").click(function(){
	var file=$('[name=file]').val();
	if(file==''){
		alert('请选择一个文件');
		return false;
	}
});
$(document).on('click', '#download', function(event) {
	event.preventDefault();
	/* Act on the event */
	var file_urls = $('input[name="file_url[]"]:checked').map(function() {
		return $(this).val();
	}).get().join(',');
	if (file_urls == ''){
		alert('请先选择数据');
		return false;
	}
	var arr = new Array();
	arr = file_urls.split(',');
	$.each(arr, function(i,val){        
	      window.open(val);
    });
	
})

</script>