<?php
/**
 * 运费模板管理-列表页
 */
?>
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">速卖通刊登-运费模板列表</h3>
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
								<button class="btn btn-primary btn-sm" id="freight_synchronization">同步</button>
							</label>
						</form>
					</div>
				</div>

				<table class="table table-bordered table-striped table-hover dataTable">
					<thead>
						<tr>
							<th>id</th>
							<th>账号</th>
							<th>运费模板ID</th>
							<th>运费模板名称</th>
							<th>是否默认</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($freight as $f):?>
							<tr>
								<td><?php echo $f['id'];?></td>
								<td><?php echo $token[$f['token_id']]['accountSuffix']?></td>
								<td><?php echo $f['templateId']?></td>
								<td><?php echo $f['templateName']?></td>
								<td>
									<?php echo $f['default'] ? '<font color="green">是</font>' : '<font color="#666">否</font>';?>
									&nbsp;&nbsp;
									<a href="javascript: void(0);" title="查看详情" class="blue freight-view"><i class="icon-eye-open bigger-130"></i></a>
								</td>
							</tr>
							<?php
							if($f['freightSettingList']):
							?>
							<tr class="hide freight-detail">
								<td colspan="5">
									<?php
										$freightSettingList = unserialize($f['freightSettingList']);
										foreach($freightSettingList as $row):
											echo '<table class="table table-bordered table-striped table-hover" style="word-break: break-all;">';
											echo '<col width="30%"><col width="70%">';
											if($row['template']): //物流公司运费设置信息
												echo '<tr><th colspan="2">物流公司运费设置信息:</th></tr>';
												echo '<tr><td>物流公司:</td><td>'.$row['template']['logisticsCompany'].'</td></tr>';
												echo (array_key_exists('allStandardDiscount', $row['template']) ? '<tr><td>标准运费减免率(%):</td><td>'.$row['template']['allStandardDiscount'].'</td></tr>' : '');
												echo '<tr><td>是否为全部免运费:</td><td>'.$row['template']['allFreeShipping'].'</td></tr>';
												echo (array_key_exists('freeShippingCountry', $row['template']) ? '<tr><td>自定义免运费国家:</td><td>'.$row['template']['freeShippingCountry'].'</td></tr>' : '');
												echo (array_key_exists('standardShippingCountry', $row['template']) ? '<tr><td>自定义标准运费的国家:</td><td>'.$row['template']['standardShippingCountry'].'</td></tr>' : '');
												echo (array_key_exists('standardShippingDiscount', $row['template']) ? '<tr><td>自定义标准运费减免率(%):</td><td>'.$row['template']['standardShippingDiscount'].'</td></tr>' : '');
											endif;
											if($row['selfdefine']):
												echo '<tr><th colspan="2">自定义运费:</th></tr>';
												echo (array_key_exists('startOrderNum', $row['selfdefine']) ? '<tr><td>自定义起始采购量:</td><td>'.$row['selfdefine']['startOrderNum'].'</td></tr>' : '');
												echo (array_key_exists('endOrderNum', $row['selfdefine']) ? '<tr><td>自定义截至采购量:</td><td>'.$row['selfdefine']['endOrderNum'].'</td></tr>' : '');
												echo (array_key_exists('minFreight', $row['selfdefine']) ? '<tr><td>截至采购量里的运费报价:</td><td>'.$row['selfdefine']['minFreight'].'</td></tr>' : '');
												echo (array_key_exists('perAddNum', $row['selfdefine']) ? '<tr><td>每增加定额产品采购量:</td><td>'.$row['selfdefine']['perAddNum'].'</td></tr>' : '');
												echo (array_key_exists('addFreight', $row['selfdefine']) ? '<tr><td>续增的运费:</td><td>'.$row['selfdefine']['addFreight'].'</td></tr>' : '');
												echo (array_key_exists('shippingCountry', $row['selfdefine']) ? '<tr><td>自定义运送国家:</td><td>'.$row['selfdefine']['shippingCountry'].'</td></tr>' : '');
											endif;
											if ($row['selfstandard']):
												echo '<tr><th colspan="2">自定义标准运费:</th></tr>';
												foreach($row['selfstandard'] as $k => $r):
													echo '<tr><td>'.($k+1).'-自定义标准运费国家:</td><td>'.$r['selfStandardCountry'].'</td></tr>';
													echo '<tr><td>'.($k+1).'-自定义标准运费减免率(%):</td><td>'.$r['selfStandardDiscount'].'</td></tr>';
												endforeach;
											endif;
											echo '</table>';
										endforeach;
									?>
								</td>
							</tr>
							<?php
							endif;
							?>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	//运费模板同步
	$(document).on('click', '#freight_synchronization', function(event) {
		event.preventDefault();
		/* Act on the event */
		if (confirm('确定要同步该账号吗?')) {
			var token_id = $('#token_id').val();
			$.ajax({
				url:'<?php echo admin_base_url("smt/smt_product/getFreightTemplateList");?>',
				type:'POST',
				dataType:'json',
				data:'token_id='+token_id,
				beforeSend:function(){
					$('#freight_synchronization').html('同步中...').addClass('disabled');
				},
				success:function(data){
					if (data.status) {
						showxbtips(data.info);
						window.location.href = '<?php echo admin_base_url("smt/smt_product/freightManage?token_id='+token_id+'")?>';
					}else {
						showxbtips(data.info, 'alert-warning');
					}
				},
				complete:function(){
					$('#freight_synchronization').html('同步').removeClass('disabled');
				}
			});
		}else
			return false;
	});
	//点击查看详情
	$(document).on('click', '.freight-view', function(event) {
		event.preventDefault();
		/* Act on the event */
		var my_detail = $(this).parents('tr').next('tr.freight-detail');
		if (my_detail.hasClass('hide')){
			my_detail.removeClass('hide');
			$(this).attr('title', '关闭详情').html('<i class="icon-eye-close bigger-130"></i>');
		}else{
			my_detail.addClass('hide');
			$(this).attr('title', '查看详情').html('<i class="icon-eye-open bigger-130"></i>');
		}
	});
})
</script>