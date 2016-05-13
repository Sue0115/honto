<?php
/**
 * 服务模板-列表页
 */
?>
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">速卖通刊登-服务模板列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
					<div class="col-xs-12">
						<form action="" method="get">
							<label>
								账号:
								<select name="token_id" id="token_id">
									<option value="">---全选---</option>
									<?php
									foreach($token as $t):
										echo '<option value="'.$t['token_id'].'" '.($token_id == $t['token_id'] ? 'selected="selected"': '').'>'.$t['token_id'].'-'.$t['accountSuffix'].'</option>';
									endforeach;
									?>
								</select>
							</label>
							<label>
								<button class="btn btn-primary btn-sm">筛选</button>
							</label>
							<label>
								<button class="btn btn-primary btn-sm" id="service_synchronization">同步</button>
							</label>
						</form>
					</div>
				</div>

				<table class="table table-bordered table-striped table-hover dataTable">
					<thead>
						<tr>
							<th>id</th>
							<th>账号</th>
							<th>服务模板ID</th>
							<th>服务模板名称</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($service as $s):?>
							<tr>
								<td><?php echo $s['id'];?></td>
								<td><?php echo $token[$s['token_id']]['accountSuffix']?></td>
								<td><?php echo $s['serviceID']?></td>
								<td><?php echo $s['serviceName']?></td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$(document).on('click', '#service_synchronization', function(event) {
		event.preventDefault();
		/* Act on the event */
		if (confirm('确定要同步该账号吗?')) {
			var token_id = $('#token_id').val();
			$.ajax({
				url:'<?php echo admin_base_url("smt/smt_product/getServiceTemplateList");?>',
				type:'POST',
				dataType:'json',
				data:'token_id='+token_id,
				beforeSend:function(){
					$('#service_synchronization').html('同步中...').addClass('disabled');
				},
				success:function(data){
					if (data.status) {
						showxbtips(data.info);
						window.location.href = '<?php echo admin_base_url("smt/smt_product/serviceManage?token_id='+token_id+'")?>';
					}else {
						showxbtips(data.info, 'alert-warning');
					}
				},
				complete:function(){
					$('#service_synchronization').html('同步').removeClass('disabled');
				}
			});
		}else
			return false;
	});
})
</script>