<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">数据导出-数据模板列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				
			   <form action="" method="post" >
				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="3%">
						<col width="24%">
						<col width="8%">
						<col width="14%"/>
						<col width="6%"/>
						<col width="4%">
						
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<th>模板名</th>
							<th>创建人</th>
							<th>创建时间</th>
							<th>是否启用</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($templateArr as $da):?>
						  <tr>
						    <td class="center">
						      <input type="checkbox" name="ids[]" value="<?php echo $da['id']?>" />
						    </td>
						    <td class="center">
						    	<?php echo $da['title']?>
						    </td>
						    <td class="center"><?php echo !empty($userInfo[$da['uid']])? $userInfo[$da['uid']] : ''; ?></td>
						    <td class="center"><?php echo $da['createTime']?></td>
						    <td class="center">
							   <?php echo $is_use[$da['status']]?>
						    </td>
						    <td class="center">
						      <a class="green" href="<?php echo admin_base_url('export_data/export_order_data/data_template?id=')?><?php echo $da['id'];?>" title="修改">
						        <i class="icon-pencil bigger-130"></i>
						      </a>
							  <a class="red delete"  data-id="<?php echo $da['id']?>" title="删除" style="cursor:pointer;">
							    <i class="icon-trash bigger-130"></i>
							  </a>
						    </td>
						    
						  </tr>
						<?php endforeach;?>
					</tbody>
				</table>
			   </form>
				
			</div>
		</div>
	</div>
</div>
<script>
$(function(){
 $('.delete').click(function(){
	var id=$(this).attr('data-id');
	if (confirm('确定删除该模板吗？')){
		$.ajax({
			url: '<?php echo admin_base_url("export_data/export_order_data/deleteTemplateByID");?>',
			data: 'Ids='+id,
			type: 'POST',
			dataType: 'json',
			
			success: function(data){
				var str='';
				if (data.data){
					$.each(data.data, function(index, el){
						str += el+';';
					});
				}
				if (data.status) { //成功
					showxbtips(data.info+str);
				}else {
					showxbtips(data.info+str, 'alert-warning');
				}
			}
			
		});
	}else{
	  return false;
    }
 });
});
</script>