<?php
/**
 * 产品信息模板-列表页
 */
?>
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">速卖通刊登-产品信息模块列表</h3>
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
										echo '<option value="'.$t['token_id'].'" '.($token_id == $t['token_id'] ? 'selected="selected"': '').'>'.$t['token_id'].'-'.$t['seller_account'].'</option>';
									endforeach;
									?>
								</select>
							</label>
							<label>
								<button class="btn btn-primary btn-sm">筛选</button>
							</label>
							<label>
								<button class="btn btn-primary btn-sm" id="module_synchronization">同步</button>
							</label>
						</form>
					</div>
				</div>

				<table class="table table-bordered table-striped table-hover dataTable">
					<thead>
						<tr>
							<th>id</th>
							<th>账号</th>
							<th>信息模板ID</th>
							<th>信息模板名称</th>
							<th>模板类型</th>
							<th>信息模板显示</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($module as $m):?>
							<tr>
								<td><?php echo $m['id'];?></td>
								<td><?php echo $token[$m['token_id']]['seller_account'];?></td>
								<td><?php echo $m['module_id'];?></td>
								<td><?php echo $m['module_name'];?></td>
								<td><?php echo $m['module_type'];?></td>
								<td style="word-break: break-all;"><?php echo htmlspecialchars($m['displayContent']);?></td>
							</tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<?php $this->load->view('admin/common/page_number'); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$(document).on('click', '#module_synchronization', function(event) {
		event.preventDefault();
		/* Act on the event */
		if (confirm('确定要同步该账号吗?')) {
			var token_id = $('#token_id').val();
			$.ajax({
				url:'<?php echo admin_base_url("smt/smt_product/getProductModuleList");?>',
				type:'POST',
				dataType:'json',
				data:'token_id='+token_id,
				beforeSend:function(){
					$('#module_synchronization').html('同步中...').addClass('disabled');
				},
				success:function(data){
					if (data.status) {
						showxbtips(data.info);
						window.location.href = '<?php echo admin_base_url("smt/smt_product/moduleManage?token_id='+token_id+'")?>';
					}else {
						showxbtips(data.info, 'alert-warning');
					}
				},
				complete:function(){
					$('#module_synchronization').html('同步').removeClass('disabled');
				}
			});
		}else
			return false;
	});
})
</script>