<?php
/**
 * 产品草稿-列表页
 */
?>
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">wish刊登-<?php echo $product_type;?>列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
					<div class="col-xs-12">
						<form action="" method="get">
							<label>
								账号:
								<select name="search[account]" id="token_id">
									<option value="">---全选---</option>
									<?php
									foreach($userInfo as $k => $t):
										echo '<option value="'.$k.'" '.($search['account'] == $t ? 'selected="selected"': '').'>'.$t.'</option>';
									endforeach;
									?>
								</select>
							</label>
							<label>
								ID:
								<input type="text" name="search[productId]" value="<?php echo $search['productId']?>" size="30"/>
							</label>
							<label>
								SKU:
								<input type="text" name="search[sku]" placeholder="不要输入前后缀" value="<?php echo $search['sku']?>"/>
							</label>
							<label>
								标题:
								<input type="text" name="search[subject]" value="<?php echo $search['subject'];?>"/>
							</label>
							<label>
								<button class="btn btn-primary btn-sm" type="submit">筛选</button>
							</label>
							
							
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label>
								<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/wish/get_draft_list');?>">清空</a>
							</label>	
							<br/>
							<label>
								<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/smt/add');?>">添加</a>
							</label>	
							<?php if ($product_type == '草稿'):?>
								<label>
									<a href="javascript: void(0);" class="btn btn-sm btn-primary" id="batch_wait">批量保存为待发布</a>
								</label>
								<label>
									<a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('publish/wish/wait_post');?>">查看待发布产品列表</a>
								</label>
								<label>
									<a class="btn btn-sm btn-primary" href="javascript: void(0);" id="batch_del">批量删除</a>
								</label>
							<?php elseif ($product_type == '待发布'): ?>
								<label>
									<a href="javascript: void(0);" class="btn btn-sm btn-primary" id="batch_post">批量发布</a>
								</label>
								<label>
									<a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('publish/wish/get_draft_list');?>">查看草稿列表</a>
								</label>
							<?php endif;?>

							<label>
								<a class="btn btn-sm btn-primary" href="javascript: void(0);" id="batch_modify">批量修改</a>
							</label>
						</form>
					</div>
				</div>

				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="3%"/>
						<col width="5%"/>
						<col width="8%"/>
						<col width="10%"/>
						<col width="9%"/>
						<col width="6%"/>
						<col width="20%"/>
						<col width="7%"/>
						<col width="8%"/>
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<th>ID</th>
							<th>图片</th>
							<th>账号</th>
							<th>SKU</th>
							<th>状态</th>
							<th>标题</th>
							<th>模板生成时间</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($data_list as $da):?>
						  <tr>
						    <td class="center">
								<label>
									<input type="checkbox" class="ace" name="productIds[]" value="<?php echo $da->productID;?>" >
									<span class="lbl"></span>
								</label>
							</td>
							<td>
							  <?php echo $da->productID;?>
							</td>
							<td>
							<?php if(!empty($mainImageArr[$da->productID])):?>
								 <a href="<?php echo !empty($mainImageArr[$da->productID]) ? $mainImageArr[$da->productID] : ''?>" target="_blank">
								    <img src="<?php echo !empty($mainImageArr[$da->productID]) ? $mainImageArr[$da->productID] : ''?>" style="width:50px;height:50px;" />
								 </a>
							 <?php endif;?>
							</td>
							<td>
							  <?php echo $userInfo[$da->account];?>
							</td>
							<td style="word-break:break-all;">
							  <?php echo !empty($productSkuArr[$da->productID]) ? $productSkuArr[$da->productID] : '';?>
							</td>
							<td>
							  <?php echo $product_type;?>
							</td>
							<td>
							  <?php echo $da->product_name;?>
							</td>
							<td>
							  <?php echo $da->publishedTime;?>
							</td>
							<td>
							  <a class="green" href="<?php echo admin_base_url('publish/wish/productInfo?id=')?><?php echo $da->productID?>">
	                                    <i class="icon-pencil bigger-130"></i>
	                          </a>
	                          <a title="删除" class="a_delete" data-id="<?php echo $da->productID;?>" style="cursor:pointer;">
									<i class="icon-trash bigger-130 red"></i>
							  </a>
							</td>
						  </tr>
						<?php endforeach;?>
					</tbody>
				</table>
				<?php
					$this->load->view('admin/common/page_number');
				?>
			    <form name="batchModify" action="<?php echo admin_base_url("publish/wish/batchModifyProducts");?>" method="post" target="newWindow" onsubmit="openNewSpecifiedWindow('newWindow2')">
					<input type="hidden" name="operateProductIds" value="" id="operateProductIds"/>
					<input type="hidden" name="from" value="draft"/>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	//批量保存为待发布
	$(document).on('click', '#batch_wait', function(){
		var productIds = $('input[name="productIds[]"]:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		if (!productIds){
			showtips('请先选择行', 'alert-warning');
			return false;
		}

		if (!confirm('确定要批量保存为待发布吗?')){
			return false;
		}
		$.ajax({
			url: '<?php echo admin_base_url("publish/wish/changeToWaitPost");?>',
			data: 'ids='+productIds,
			type: 'POST',
			dataType: 'JSON',
			success: function(data){
			
			    showtips(data.info , 'alert-success');
			    
				window.location.reload();
			}
		});
	});

	//批量删除
	$(document).on('click', '#batch_del', function(){
		var productIds = $('input[name="productIds[]"]:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		if (!productIds){
			showtips('请先选择行', 'alert-warning');
			return false;
		}

		if (!confirm('确定要删除吗？')){
			return false;
		}

		$.ajax({
			url: '<?php echo admin_base_url("publish/wish/batchDel");?>',
			data: 'ids='+productIds+'&action=batch',
			type: 'POST',
			dataType: 'JSON',
			success: function(data){
			    if(data.status){
			    	showtips(data.info , 'alert-success');
				}else{
					showtips(data.info , 'alert-warning');
			    }
				window.location.reload();
			}
		});	
	
	});

	//单个删除
	$(".a_delete").click(function(){
		if (!confirm('确定要删除吗？')){
			return false;
		}
		var productIds = $(this).attr('data-id');

		$.ajax({
			url: '<?php echo admin_base_url("publish/wish/batchDel");?>',
			data: 'ids='+productIds+'&action=adelete',
			type: 'POST',
			dataType: 'JSON',
			success: function(data){
			    if(data.status){
			    	showtips(data.info , 'alert-success');
				}else{
					showtips(data.info , 'alert-warning');
			    }
				window.location.reload();
			}
		});	
	});

	//批量发布产品
	$(document).on('click', '#batch_post', function(){
		var productIds = $('input[name="productIds[]"]:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		if (!productIds){
			showtips('请先选择行', 'alert-warning');
			return false;
		}

		if (!confirm('确定要批量发布吗，发布过程中不能操作?')){
			return false;
		}

		var url = '';
		$.layer({
			type: 2,
			shadeClose: false,
			title: 'wish产品批量发布',
			closeBtn: [0, true],
			shade: [0.8, '', true],
			border: [0],
			offset: ['',''],
			area: ['450px', '300px'],
			iframe: {src: '<?php echo admin_base_url("publish/wish/batchPost?productIds='+productIds+'")?>'}
		});
	});

	//批量修改
	$('#batch_modify').on('click', function(e){
		var productIds = $('input[name="productIds[]"]:checked').map(function() {
			return $(this).val();
		}).get().join(',');
		if (productIds == ''){
			layer.msg('请先选择产品');
			return false;
		}

		//赋值下 --选择的产品就是需要批量修改的
		$('#operateProductIds').val(productIds);

		document.forms.batchModify.submit();
	});
})
</script>