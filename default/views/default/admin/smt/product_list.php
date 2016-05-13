<?php
/**
 * listing模板管理-列表页
 */
?>


<div class="modal fade" id="myPriceModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" >同步指定账号广告</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal"  >

					<div class="form-group">
						<label class="col-sm-2">账号:</label>
						<div class="col-sm-6">
							<select  id="selectaccount">
								<option value="">---请选择---</option>
								<?php
								foreach($token as $t):
									echo '<option value="'.$t['token_id'].'">'.$t['token_id'].'-'.$t['accountSuffix'].'</option>';
								endforeach;
								?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2">产品分组</label>
						<div class="col-sm-6">
							<select  id="groupId3" >
								<option value="">=所有分组=</option>
							</select>

						</div>
					</div>


					<div class="modal-footer">
						<a href="#"   class="btn btn-primary " id="pricecheck">确定</a>
						<!--<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a>-->
					</div>
					<!--<button type="submit" class="btn btn-primary">提交</button>-->
				</form>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="coypaccountModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" >复制信息</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal"  >

					<div class="form-group">
						<label class="col-sm-2">(从)账号</label>
						<div class="col-sm-6">
							<select  id="selectaccountfrom">
								<option value="">---请选择---</option>
								<?php
								foreach($token as $t):
									echo '<option value="'.$t['token_id'].'">'.$t['token_id'].'-'.$t['accountSuffix'].'</option>';
								endforeach;
								?>
							</select>
						</div>
					</div>

				<div class="form-group">
						<label class="col-sm-2">产品分组</label>
						<div class="col-sm-6">
							<select  id="groupId1" >
								<option value="">=所有分组=</option>
							</select>

						</div>
					</div>

				<div class="form-group">
					<label class="col-sm-2">选择分类</label>

					<div class="col-sm-6">
						<input type="checkbox" id="needcategoryone"  name="needcategoryone">

					</div>
				</div>

				<div  class="needcategory hidden">
					<div class="form-group">
						<label class="col-sm-2">搜索类目</label>
						<div class="col-sm-4">
							<input type="text"  size="15" id="searchcategoryinfo">
						</div>
						<div class="col-sm-2">
							<a href="#" class="btn btn-primary btn-xs" id="searchcategory"> 搜索</a>
						</div>

					</div>

						<div class="form-group">

							<label class="col-sm-2">确认类目</label>
							<div class="col-sm-6">
								<select id="checkecategory">

								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2">(到)账号</label>
						<div class="col-sm-6">
							<select  id="selectaccountto">
								<option value="">---请选择---</option>
								<?php
								foreach($token as $t):
									echo '<option value="'.$t['token_id'].'">'.$t['token_id'].'-'.$t['accountSuffix'].'</option>';
								endforeach;
								?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2">产品分组</label>
						<div class="col-sm-10">
							<select  id="groupId2" >
								<option value="">=所有分组=</option>
							</select>

							<span class="red">请指定产品分组，否则新草稿分组为空</span>

						</div>
					</div>


					<div class="modal-footer">
						<a href="#"   class="btn btn-primary " id="copycheck">确定</a>
						<!--<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a>-->
					</div>
					<!--<button type="submit" class="btn btn-primary">提交</button>-->
				</form>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">速卖通刊登-产品管理列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
					<div class="col-xs-12">
						<form action="" method="get">
							<label>
								账号:
								<select name="search[token_id]" id="token_id">
									<option value="">---全选---</option>
									<?php
									foreach($token as $t):
										echo '<option value="'.$t['token_id'].'" '.($search['token_id'] == $t['token_id'] ? 'selected="selected"': '').'>'.$t['token_id'].'-'.$t['accountSuffix'].'</option>';
									endforeach;
									?>
								</select>
							</label>
							<label class="<?php echo empty($search['token_id']) ? 'hide ': '';?>p-group">
								产品分组:
								<select name="search[groupId]" id="groupId" style="width: 120px;">
									<option value="">=所有分组=</option>
									<option value="none" <?php echo $search['groupId'] == 'none' ? 'selected="selected"': '';?>>未分组</option>
									<?php
									if (!empty($group_list)):
										foreach($group_list as $id => $item){
											echo '<option value="'.$item['group_id'].'" '.($search['groupId'] == $item['group_id'] ? 'selected="selected"': '').'>'.$item['group_name'].'</option>';
											if (!empty($item['child'])){

												foreach ($item['child'] as $pid => $row){
													echo '<option value="'.$row['group_id'].'" '.($search['groupId'] == $row['group_id'] ? 'selected="selected"': '').'>&nbsp;&nbsp;&nbsp;&nbsp;--'.$row['group_name'].'</option>';
												}
											}
										}
									endif;
									?>
								</select>
							</label>
							<label>
								产品ID:
								<input type="text" name="search[productId]" placeholder="请输入产品ID" value="<?php echo array_key_exists('productId', $search) ? $search['productId'] : '';?>">
							</label>
							<label>
								SKU:
								<input type="text" name="search[sku]" placeholder="不要输入前后缀" value="<?php echo array_key_exists('sku', $search) ? $search['sku'] : '';?>">
							</label>
							<label>
								标题:
								<input type="text" name="search[subject]" value="<?php echo !empty($search['subject']) ? $search['subject'] : '';?>"/>
							</label>
							<label>
								广告状态:
								<select name="search[productStatusType]" id="productStatusType">
									<option value="">--所有状态--</option>
									<?php
									foreach ($smt_product_status as $s):
										echo '<option value="'.$s.'" '.($search['productStatusType'] == $s ? 'selected="selected"' : '').'>'.$s.'</option>';
									endforeach;
									echo '<option value="other" '.($search['productStatusType'] == 'other' ? 'selected="selected"' : '').'>其他</option>';
									?>
								</select>
							</label>
							<label>
								<button class="btn btn-primary btn-sm" type="submit">筛选</button>
							</label>
							<!--<label>
								<botton class="btn btn-primary btn-sm" id="product_synchronization">同步</botton>
							</label>-->
							<br>
							<label>
								<button class="btn btn-primary btn-sm" id="batch_copy">另存为草稿</button>
							</label>
							&nbsp;&nbsp;
							<label>
								<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/smt/index');?>">查看草稿列表</a>
							</label>
							&nbsp;
							<label>
								<a class="btn btn-primary btn-sm" href="javascript: void(0);" id="batch_mod">批量修改</a>
							</label>

							<label>
								<a class="btn btn-sm btn-primary "  href="javascript: void(0);" id="new_task">
									同步指定账号广告
								</a>
							</label>

							<label>
								<a class="btn btn-sm btn-primary "  href="javascript: void(0);" id="coyp_account">
									COPY账号在线广告
								</a>
							</label>
						</form>
					</div>
				</div>

				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="5%">
						<col width="6%"/>
						<col width="10%">
						<col width="10%">
						<col width="20%">
						<col>
						<col width="5%">
						<col width="10%"/>
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<th>图片</th>
							<th>账号</th>
							<th>产品ID</th>
							<th>SKU</th>
							<th>标题</th>
							<th>用户</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($data_list as $item):
							$imageURLs = array_key_exists($item->productId, $detail_list) ? $detail_list[$item->productId]['imageURLs'] : '';
							$first_image = $imageURLs ? array_shift(explode(';', $imageURLs)) : '';
						?>
						<tr>
							<td class="center">
								<label>
									<input type="checkbox" class="ace" name="productIds[]" value="<?php echo $item->productId;?>">
									<span class="lbl"></span>
								</label>
							</td>
							<td><a href="<?php echo $item->product_url;?>" target="_blank"><?php echo $first_image ? '<img src="'.$first_image.'" width="50" height="50" />' : '无图片'?></a></td>
							<td><?php echo $token[$item->token_id]['accountSuffix'];?></td>
							<td><a href="<?php echo $item->product_url;?>" target="_blank"><?php echo $item->productId;?></a></td>
							<td><?php echo replaceDotToShow($product_skus[$item->productId]);?></td>
							<td><?php echo $item->subject;?></td>
							<td><?php echo $item->user_id;?></td>
							<td>
								<a href="javascript: void(0);" title="编辑在线广告" class="item-info" lang="<?php echo $item->productId;?>">
									<i class="icon-pencil bigger-130"></i>
								</a>
								<a href="<?php echo admin_base_url('publish/smt/edit?id=' . $item->productId);?>" target="_blank"><span class="item-info-hide"></span></a>
							</td>
						</tr>
						<?php
						endforeach;
						?>
					</tbody>
				</table>

				<?php
				 if($key == 'root' || $key == 'manager'){
					 $this->load->view('admin/common/page_number');
				 }else{
					$this->load->view('admin/common/page');
				}
				?>

				<form name="batchModify" action="<?php echo admin_base_url("smt/smt_product/batchModifyProducts");?>" method="post" target="newWindow" onsubmit="openNewSpecifiedWindow('newWindow')">
					<input type="hidden" name="operateProductIds" value="" id="operateProductIds"/>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	/**
	 * 另存为草稿功能
	 * @param  {[type]} event [description]
	 * @return {[type]}       [description]
	 */
	$(document).on('click', '#batch_copy', function(event) {
		event.preventDefault();
		/* Act on the event */
		var productIds = $('input[name="productIds[]"]:checked').map(function() {
			return $(this).val();
		}).get().join(',');
		if (productIds == ''){
			alert('请先选择数据');
			return false;
		}

		if (confirm('确定要将选中的广告另存为草稿吗？')) {
			//弹出层选择账号
			$.layer({
				type   : 2,
				shade  : [0.8 , '' , true],
				title  : ['选择账号',true],
				iframe : {src : '<?php echo admin_base_url("smt/smt_product/showAccountToCopyProduct");?>'},
				area   : ['900px' , '550px'],
				success : function(){
					layer.shift('top', 400)
				},
				btns : 2,
				btn : ['确定', '取消'],
				yes : function(index){ //确定按钮的操作
					var account_list = layer.getChildFrame('.account_list :checked', index).map(function(){
						return $(this).val();
					}).get().join(',');
					if (account_list != ''){

						$.ajax({
							url: '<?php echo admin_base_url("smt/smt_product/copyListingToDraft");?>',
							data: 'productIds='+productIds+'&tokenIds='+account_list,
							type: 'POST',
							dataType: 'json',
							beforeSend: function(){
								$('#batch_copy').addClass('disabled');
							},
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
							},
							complete: function(){
								$('#batch_copy').removeClass('disabled');
							}
						});
					}else {
						showtips('请先选择账号', 'alert-warning');
					}
					layer.close(index);
				},
				no: function(index){
					layer.close(index);
				}
			});
		}else{
			return false;
		}
	});

	/**
	 * 同步并打开
	 */
	$(document).on('click', '.item-info', function(){
		var productId = $(this).attr('lang');

		if (!productId){
			showtips('产品不存在');
			window.location.reload();
			return false;
		}

		var targetBtn = $(this).closest('td').find('.item-info-hide');
		if (confirm('是否要先同步广告,本次同步不会计算利润率')){ //不同步的话，就直接打开
			//开始同步
			$.ajax({
				url: '<?php echo admin_base_url("smt/smt_product/synchronizationProduct");?>',
				data: 'productId='+productId,
				type: 'POST',
				dataType: 'JSON',
				beforeSend: function(){
					showtips('产品:'+productId+'同步中...', 'alert-success');
				},
				success: function(data){
					if (data.status){
						showtips('同步成功', 'alert-success');
						targetBtn.trigger('click');
					}else {
						showtips('同步失败'+data.info);
						targetBtn.trigger('click');
					}
				}
			});
		}else {
			targetBtn.trigger('click');
		}
	});

	//异步显示账号的分组信息
	$(document).on('change', '#token_id', function(){
		var token_id = $(this).val();
		$('#groupId').empty();
		if (token_id == ''){ //账号为空，隐藏分组信息
			$('.p-group').addClass('hide');
		}else {
			//异步获取账号信息
			$.ajax({
				url: '<?php echo admin_base_url("smt/smt_product/showAccountProductGroup");?>',
				data: 'token_id='+token_id,
				type: 'POST',
				dataType: 'JSON',
				success: function(data){
					if (data.status){
						//插入分组的选项
						if ($('.p-group').hasClass('hide')){
							$('.p-group').removeClass('hide');
						}
						$('#groupId').append(data.data);
					}
				}
			});
		}
	});

	//批量修改
	$(document).on('click', '#batch_mod', function(e){
		var productIds = $('input[name="productIds[]"]:checked').map(function() {
			return $(this).val();
		}).get().join(',');
		if (productIds == ''){
			showtips('请先选择产品', 'alert-warning');
			return false;
		}

		//赋值下 --选择的产品就是需要批量修改的
		$('#operateProductIds').val(productIds);

		if (confirm("是否要先同步产品？")){
			//先同步广告
			//开始同步
			$.ajax({
				url: '<?php echo admin_base_url("smt/smt_product/batchSynchronizationProduct");?>',
				data: 'productIds='+productIds,
				type: 'POST',
				dataType: 'JSON',
				beforeSend: function(){
					showtips('产品:'+productIds+'同步中...', 'alert-success');
				},
				success: function(data){
					if (data.status){
						showtips(data.info, 'alert-success');
						//同步成功后提交下边的窗口
						document.forms.batchModify.submit();
					}else {
						showtips('同步失败'+data.info);
						//同步成功后提交下边的窗口
						document.forms.batchModify.submit();
					}
				}
			});
		}else {
			document.forms.batchModify.submit();
		}
	});

	$('#coyp_account').click(function(){
		$('#coypaccountModalSelect').modal('toggle');
		$("#searchcategoryinfo").val("");

		$("#needcategoryone").removeProp('checked');
		$("#needcategoryone").prop('checked',false);
	})


	$(document).on('change', '#selectaccountfrom', function(){
		var token_id = $(this).val();
		$('#groupId1').empty();
		if (token_id == ''){ //账号为空，隐藏分组信息
			return false;
		}else {
			//异步获取账号信息
			$.ajax({
				url: '<?php echo admin_base_url("smt/smt_product/showAccountProductGroup");?>',
				data: 'token_id='+token_id,
				type: 'POST',
				dataType: 'JSON',
				success: function(data){
					if (data.status){

						$('#groupId1').append(data.data);
					}
				}
			});
		}
	});


	$(document).on('change', '#selectaccountto', function(){
		var token_id = $(this).val();
		$('#groupId2').empty();
		if (token_id == ''){ //账号为空，隐藏分组信息
			return false;
		}else {
			//异步获取账号信息
			$.ajax({
				url: '<?php echo admin_base_url("smt/smt_product/showAccountProductGroup");?>',
				data: 'token_id='+token_id,
				type: 'POST',
				dataType: 'JSON',
				success: function(data){
					if (data.status){

						$('#groupId2').append(data.data);
					}
				}
			});
		}
	});

	$(document).on('change', '#selectaccount', function(){
		var token_id = $(this).val();
		$('#groupId3').empty();
		if (token_id == ''){ //账号为空，隐藏分组信息
			return false;
		}else {
			//异步获取账号信息
			$.ajax({
				url: '<?php echo admin_base_url("smt/smt_product/showAccountProductGroup");?>',
				data: 'token_id='+token_id,
				type: 'POST',
				dataType: 'JSON',
				success: function(data){
					if (data.status){

						$('#groupId3').append(data.data);
					}
				}
			});
		}
	});

	$('#copycheck').click(function(){
		var token_id_from = $('#selectaccountfrom').val();
		var token_id_to = $('#selectaccountto').val();
		var groupId1 = $('#groupId1').val();
		var groupId2 = $('#groupId2').val();
		var	 checkecategory='';
		if(groupId1=='')
		{
			alert('请选择主账号分组');
			return false;
		}
		if($('#needcategoryone').is(":checked"))
		{
				var	 checkecategory = $("#checkecategory").val();
		}



		if(token_id_from==''||token_id_to==''||(token_id_from==token_id_to))
		{
			alert("请选择账号,两个账号不能相同");

			return false;
		}

		$.ajax({
			url: '<?php echo admin_base_url("smt/smt_product/copyAllAccountNew");?>',
			data: 'token_id_from='+token_id_from+'&token_id_to='+token_id_to+'&groupId1='+groupId1+'&groupId2='+groupId2+'&checkecategory='+checkecategory,
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function(){
				ii = layer.load('更新中。。。');
			},
			success: function(data){

				layer.close(ii);

				if(data.status)
				{
					alert("复制完成");
				}
				else
				{
					alert(data.data);
				}
				$('#coypaccountModalSelect').modal('toggle');
			}
		});


	})


	$('#new_task').click(function(){
		$('#myPriceModalSelect').modal('toggle');

	})



	$('#pricecheck').click(function(){
		var token_id =$("#selectaccount").val();
		var groupId3 = $("#groupId3").val();
		if(groupId3=='none')
		{
			alert('无法同步该分组');
			return false;
		}
		$.ajax({
			url: '<?php echo admin_base_url("smt/smt_product/getListInfoByaccount");?>',
			data: 'token_id='+token_id+'&groupId3='+groupId3,
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function(){
				 ii = layer.load('更新中。。。');
			},
			success: function(data){
				layer.close(ii);
				$('#myPriceModalSelect').modal('toggle');
				alert(data.info)
			}
		});
	})


	$('#needcategoryone').click(function(){
		if($('#needcategoryone').is(":checked"))
		{
			$(".needcategory").removeClass("hidden");
		}
		else
		{
			$(".needcategory").addClass("hidden");
		}
	})

	$("#searchcategory").click(function(){
		var searchcategoryinfo = $("#searchcategoryinfo").val();
		if (searchcategoryinfo == "")
		{
			alert("请填写分类信息");
			return false;
		}
		$.ajax({
			url: '<?php echo admin_base_url("smt/smt_product/getCategoryInfo");?>',
			data: 'searchcategoryinfo='+searchcategoryinfo,
			type: 'POST',
			dataType: 'JSON',

			success:function(data){
				$('#checkecategory').empty();
				$('#checkecategory').append(data.info);
			}
		})
	})
})
</script>