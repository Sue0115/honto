<?php
/**
 * 产品草稿-列表页
 */
?>
<div class="modal fade" id="myModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" >批量生产草稿</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" onsubmit="return false;">
					<div class="form-group">
						<div class="col-sm-8">
							<span>SKU:</span> <input type="text" id="skuname" readonly/>
							<input type="text" id="draft_productid" class="hidden"/>
						</div>

						<div class="col-sm-4">
							<span>生成空标题草稿:</span><input type="checkbox" id="empty_biaoti" />
						</div>
						 </div>
					<div class="form-group">
						<div class="col-sm-6">
							<span class="red">必填词汇</span><input type="checkbox" id="mustword" /><br/>
							<div  id ='mustkeyword'>
							</div>
							业务新增必填词汇(多个词汇用,隔开):<input id="addmustword" type="text"/>
							</div>


						<div class="col-sm-6">
							<span class="red"> 选填填词汇</span><input type="checkbox" id="optionword"/><br/>
							<div  id ='optionkeyword'>
								</div>
							业务新增选填词汇(多个词汇用,隔开):<input id="addoptionword" type="text"/>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-9">
							<span class="red">前缀(不带*)</span> <input id="perfectnum" type="text"/>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12">
						全选:	<input type="checkbox" id="quanxuanzaccount"/>
							</br>
							<?php
							$newaccont = array();
							foreach($token as $t)
							{
								$newaccont[$t['accountSuffix']]= $t['token_id'];
							}
							ksort($newaccont);

							$i=1;
							foreach($newaccont as $key=>$new)
							{
								if($i==1)
								{
									echo '<td >'.$key.':'.'<input type="checkbox"  name="account"  value="'.$new.'" ></td>';
									$str1 = substr($key,0,1);
								}
								else
								{
									$str2= substr($key,0,1);
									if($str1==$str2)
									{
										echo '<td >'.$key.':'.'<input type="checkbox"  name="account" value="'.$new.'" ></td>';
									}
									else
									{
										$str1 = substr($key,0,1);
										echo '</br>';
										echo '<td >'.$key.':'.'<input type="checkbox"  name="account" value="'.$new.'" ></td>';
									}
								}
								$i++;
							}
							?>

						</div>
					</div>



					<div class="modal-footer">
						<a href="#"   class="btn btn-primary " id="accountcheck">确定</a>
						<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a>
						<!--<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>-->
						<!--<button  class="btn btn-primary " id="categoryselectsub"  >确定</button>
                         <button  class="btn btn-default" data-dismiss="modal">关闭</button>-->
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="myPriceModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" >修改SKU价格</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal"   action="<?php echo admin_base_url("publish/smt/changePirce"); ?> " method="post"  >
					<div class="form-group" id="changeprice">

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

<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">速卖通刊登-<?php if ($productStatusType == 'newData'){echo '产品草稿';}elseif ($productStatusType == 'waitPost'){echo '待发布产品';}?>列表</h3>
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

							<label class="<?php echo empty($token_id) ? 'hide ': '';?>p-group">
								产品分组:
								<select name="groupId" id="groupId" style="width: 120px;">
									<option value="">=所有分组=</option>
									<option value="none" <?php echo $groupId == 'none' ? 'selected="selected"': '';?>>未分组</option>
									<?php
									if (!empty($group_list)):
										foreach($group_list as $id => $item){
											echo '<option value="'.$item['group_id'].'" '.($groupId == $item['group_id'] ? 'selected="selected"': '').'>'.$item['group_name'].'</option>';
											if (!empty($item['child'])){

												foreach ($item['child'] as $pid => $row){
													echo '<option value="'.$row['group_id'].'" '.($groupId == $row['group_id'] ? 'selected="selected"': '').'>&nbsp;&nbsp;&nbsp;&nbsp;--'.$row['group_name'].'</option>';
												}
											}
										}
									endif;
									?>
								</select>
							</label>

							<label>
								ID:
								<input type="text" name="productId" value="<?php echo $productId;?>"/>
							</label>
							<label>
								SKU:
								<input type="text"  size="8"  name="sku" placeholder="不要输入前后缀" value="<?php echo $sku;?>"/>
							</label>
							<label>
								标题:
								<input type="text" name="subject" value="<?php echo $subject;?>"/>
							</label>
							<label>
								<button class="btn btn-primary btn-sm" type="submit">筛选</button>
							</label>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label>
								<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/smt/add');?>">添加</a>
							</label>

							<?php if ($productStatusType == 'newData'):?>
								<br/>
								<label>
									<a href="javascript: void(0);" class="btn btn-sm btn-primary" id="batch_wait">批量保存为待发布</a>
								</label>
								<label>
									<a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('publish/smt/waitPost');?>">查看待发布产品列表</a>
								</label>
								<label>
									<a class="btn btn-sm btn-primary" href="javascript: void(0);" id="batch_del">批量删除</a>
								</label>
							<?php elseif ($productStatusType == 'waitPost'): ?>
								<br/>

								<label>
									<a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('publish/smt/index');?>">查看草稿列表</a>
								</label>
								<label>
									<a class="btn btn-sm btn-primary" href="javascript: void(0);" id="batch_del">批量删除</a>
								</label>
							<?php endif;?>

							<label>
								<a class="btn btn-sm btn-primary" href="javascript: void(0);" id="batch_modify">批量修改</a>
							</label>

							<label>
								<a class="btn btn-sm btn-primary" href="javascript: void(0);" id="production_praft">批量生产草稿</a>
							</label>


							<label>
								<a href="javascript: void(0);" class="btn btn-sm btn-primary" id="batch_post">批量发布</a>
							</label>


						</form>
					</div>
				</div>

				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="5%">
						<col width="10%"/>
						<col width="7%">
						<col width="10%">
						<col width="16%">
						<col width="6%">
						<col width="6%"/>
						<col>
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
							<th>单价</th>
							<th>状态</th>
							<th>标题</th>
							<th>关键词1</th>
							<th>关键词2</th>
							<th>关键词3</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i =0;
						foreach($data_list as $item):
							$imageURLs = array_key_exists($item->productId, $detail_list) ? $detail_list[$item->productId]['imageURLs'] : '';
							$first_image = array_shift(explode(';', $imageURLs));


							$keyword = array_key_exists($item->productId, $detail_list) ? $detail_list[$item->productId]['keyword'] : '';
							$productMoreKeywords1 = array_key_exists($item->productId, $detail_list) ? $detail_list[$item->productId]['productMoreKeywords1'] : '';
							$productMoreKeywords2 = array_key_exists($item->productId, $detail_list) ? $detail_list[$item->productId]['productMoreKeywords2'] : '';
						?>
						<tr>
							<td class="center">
								<label>
									<input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->productId;?>">
									<span class="lbl"></span>
								</label>
							</td>
							<td><?php echo $item->productId;?></td>
							<td>
								<img width="50" height="50" src="<?php echo $first_image;?>" alt="<?php echo $item->productId;?>" title="<?php echo $item->productId;?>"/>
								<a href="http://www.aliexpress.com/item/-/<?php echo $item->old_productId; ?>.html" class="btn btn-success btn-xs" target="_blank">原网址</a>
							</td>
							<td id="<?php echo 'account'.$i; ?>" ondblclick="modifyAccount(this.id)"><?php echo $token[$item->token_id]['accountSuffix'];?></td>
							<td><?php echo array_key_exists($item->productId, $draft_skus) ? replaceDotToShow($draft_skus[$item->productId]) : '';?></td>
							<td id="<?php echo 'price'.$i; ?>" ondblclick="modifyPrice(this.id)"> <?php echo $item->productPrice;?></td>
							<td><?php echo $statusTypeList[$item->productStatusType];?></td>
							<td id="<?php echo 'biaoti'.$i; ?>" ondblclick="modifySubject(this.id)"><?php echo $item->subject;?></td>
							<td class="modifyinfo"><?php echo $keyword;?> </td>
							<td class="modifyinfo"><?php echo $productMoreKeywords1;?> </td>
							<td class="modifyinfo"><?php echo $productMoreKeywords2;?> </td>
							<td>
								<a title="编辑" href="<?php echo admin_base_url('publish/smt/edit?id='.$item->productId);?>">
									<i class="icon-pencil bigger-130"></i>
								</a>
								&nbsp;
								<a href="javascript: void(0);" title="删除" onclick="msgdelete('<?php echo $item->productId;?>', '<?php echo admin_base_url('publish/smt/delete');?>');">
									<i class="icon-trash bigger-130 red"></i>
								</a>
							</td>
						</tr>
						<?php
						$i++;
						endforeach;
						?>
					</tbody>
				</table>

				<?php
					$this->load->view('admin/common/page_number');
				?>

				<form name="batchModify" action="<?php echo admin_base_url("smt/smt_product/batchModifyProducts");?>" method="post" target="newWindow" onsubmit="openNewSpecifiedWindow('newWindow2')">
					<input type="hidden" name="operateProductIds" value="" id="operateProductIds"/>
					<input type="hidden" name="from" value="draft"/>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	function modifySubject(id)
	{
		var productid = $('#'+id).parent().children().eq(1).text();
		if($('#modifybiaoti').length>0)
		{
			alert('请先完成前一个编辑框');
			return false;
		}
		var textval = $('#'+id).text();
		$('#'+id).text('');
		$('#'+id).append('<textarea id="modifybiaoti"  class="reporttextarea" />')

		var _width = $('.reporttextarea').parent().width();
		var _height = $('.reporttextarea').parent().height();

		$('.reporttextarea').css({'width':_width,"height":_height});
		$('#modifybiaoti').val(textval);

		$('#modifybiaoti').blur(function() {
			if (confirm('确认保存吗？（该标题会影响详情标题，请确保详情内不含标题）')) {
				var textnewval = $('#modifybiaoti').val();

				$.ajax({
					url: '<?php echo admin_base_url("publish/smt/updateListSubject");?>',
					data: 'productid='+productid+'&subject='+textnewval,
					type: 'POST',
					dataType: 'JSON',
					success: function(data){
						if(data.status==1)
						{
							$('#modifybiaoti').remove();
							$('#'+id).text(textnewval);
						}

						if(data.status==2)
						{
							alert(data.info);
						}

					}
				})
			}
			else
			{
				$('#modifybiaoti').remove();
				$('#'+id).text(textval);

			}
		})


	}

	function modifyAccount(id)
	{

		var productid = $('#'+id).parent().children().eq(1).text();

		if($('#modifybiaoti').length>0)
		{
			alert('请先完成前一个编辑框');
			return false;
		}
		var textval = $('#'+id).text();
		$('#'+id).text('');
		$('#'+id).append('<select id="modifyaccount"></select>');

		$.ajax({
			url: '<?php echo admin_base_url("publish/smt/getStmAccount");?>',
			data: 'account='+textval,
			type: 'POST',
			dataType: 'JSON',
			success: function(data){
				$('#modifyaccount').append(data.info);
			}
		})

		$('#modifyaccount').blur(function(){
				if(confirm('确认保存吗？'))
				{
					var textnewval = $('#modifyaccount').val();
					var textnewvaltext = $('#modifyaccount').find("option:selected").text();
					$.ajax({
						url: '<?php echo admin_base_url("publish/smt/updateStmAccount");?>',
						data: 'productid='+productid+'&token_id='+textnewval,
						type: 'POST',
						dataType: 'JSON',
						success: function(data){
							if(data.status==1)
							{
								$('#modifyaccount').remove();
								textnewvaltext=textnewvaltext.split("-");
								$('#'+id).text(textnewvaltext[1]);
							}

							if(data.status==2)
							{
								alert(data.info);
							}

						}
					})
				}
			else
				{
					$('#modifyaccount').remove();
					$('#'+id).text(textval);
				}
		})
	}
	function modifyPrice(id)
	{

		var productid =  $('#'+id).parent().children().eq(1).text();
		$.ajax({
			url:'<?php echo admin_base_url("publish/smt/getSkuPirceByProductId"); ?>',
			data:'productid='+productid,
			type:'POST',
			dataType: 'JSON',
			success: function(data){

				$('#changeprice').empty();
				for(var i=0;i<data.data.length;i++)
				{

					$('#changeprice').append('<span class="col-sm-5">'+data.data[i]['skuCode']+'</span> <input type="text" name="sku'+i+'" class="hidden" value="'+data.data[i]['skuCode']+'"/>  <input type="text" name="price'+i+'" value="'+data.data[i]['skuPrice']+'"/></br>')
					//alert(data.data[i]['skuCode']);
					//alert(data.data[i]['skuPrice']);
				}
			}

			})
		$('#myPriceModalSelect').modal('toggle');
	}

	$('#pricecheck').click(function(){



	})

	$(".modifyinfo").dblclick(function(){


		if($('#modifyKeyWord').length>0)
		{
			alert('请先完成前一个编辑框');
			return false;
		}

		var textval = $(this).text();

		var lie = $(this).prevAll().length;
		lie++;
		$(this).text('');
		$(this).append('<textarea id="modifyKeyWord"  class="reporttextarea" />')

		var _width = $('.reporttextarea').parent().width();
		var _height = $('.reporttextarea').parent().height();

		$('.reporttextarea').css({'width':_width,"height":_height});
		$('#modifyKeyWord').val(textval);

		var  productid = $(this).parent().children().eq(1).text();

		if(lie==9)
		{
			var type = 'keyword';
 		}

		if(lie==10)
		{
			var type = 'productMoreKeywords1';
		}

		if(lie==11)
		{
			var type = 'productMoreKeywords2';
		}
		$('#modifyKeyWord').blur(function() {
			if (confirm('确认保存吗？')) {
				var textnewval = $('#modifyKeyWord').val();

				$.ajax({
					url: '<?php echo admin_base_url("publish/smt/updateListSkuWordInfo");?>',
					data: 'productid='+productid+'&wordinfo='+textnewval+'&type='+type,
					type: 'POST',
					dataType: 'JSON',
					success: function(data){
						if(data.status==1)
						{
							$('#modifyKeyWord').parent().text(textnewval);
							$('#modifyKeyWord').remove();

						}

						if(data.status==2)
						{
							alert(data.info);
						}


					}
				})
			}
			else
			{
				$('#modifyKeyWord').remove();
				$(this).text(textval);

			}
		})

		//alert(lie);
	})
$(function(){

	//批量保存为待发布
	$(document).on('click', '#batch_wait', function(){
		var productIds = $('input[name="ids[]"]:checked').map(function(){
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
			url: '<?php echo admin_base_url("publish/smt/changeToWaitPost");?>',
			data: 'ids='+productIds,
			type: 'POST',
			dataType: 'JSON',
			success: function(data){
				if (data.status){
					data.info ? showtips('部分成功,error:'+data.info, 'alert-warning') : showtips('操作成功', 'alert-success');
				}else {
					showtips('操作失败'+ data.info, 'alert-warning');
				}
				window.location.reload();
			}
		});
	});

	//批量删除
	$(document).on('click', '#batch_del', function(){
		var productIds = $('input[name="ids[]"]:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		if (!productIds){
			showtips('请先选择行', 'alert-warning');
			return false;
		}

		if (!confirm('确定要删除吗？')){
			return false;
		}

		msgdelete(productIds, '<?php echo admin_base_url('publish/smt/batchDel');?>')
	});

	//批量发布产品
	$(document).on('click', '#batch_post', function(){
		var productIds = $('input[name="ids[]"]:checked').map(function(){
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
			title: 'SMT产品批量发布',
			closeBtn: [0, true],
			shade: [0.8, '', true],
			border: [0],
			offset: ['',''],
			area: ['450px', '300px'],
			iframe: {src: '<?php echo admin_base_url("publish/smt/batchPost?productIds='+productIds+'")?>'}
		});
	});

	//批量修改
	$('#batch_modify').on('click', function(e){
		var productIds = $('input[name="ids[]"]:checked').map(function() {
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

	//批量生成标题
	$('#production_praft').on('click', function(e){
		var i=0;
		var productIds ='';
		$('#skuname').val('');
		$('#addmustword').val('');
		$('#addoptionword').val('');
		$('#perfectnum').val('');

		$('input[name="ids[]"]:checked').map(function() {
			 if(i>0)
			 {
				 return false;
			 }
			productIds =  $(this).val();
			var SKU = $(this).parent().parent().parent().children().eq(4).text();
			var draft_productid  = $(this).parent().parent().parent().children().eq(1).text();
			 if (productIds == ''){
				 layer.msg('请先选择产品');
				 return false;
			 }

			$.ajax({
				url: '<?php echo admin_base_url("publish/smt/getSkuKeyWord");?>',
				data: 'SKU='+SKU,
				type: 'POST',
				dataType: 'JSON',
				success: function(data){

					$('#mustkeyword').empty().append(data.data[0]);
					$('#optionkeyword').empty().append(data.data[1]);
					$('#skuname').val(data.data[2])
					$('#draft_productid').val(draft_productid);
					//alert(data.data[0]);
					//alert(data.data[1]);

				}
			});

			$("[name='account']").each(function(){

				if($(this).is(':checked')) {
					$(this).removeProp('checked');
					$(this).prop('checked',false);
				}
			})

			if(	$('#mustword').is(':checked'))
			{
				$('#mustword').removeProp('checked');
				$('#mustword').prop('checked',false);
			}
			if($('#optionword').is(':checked'))
			{
				$('#optionword').removeProp('checked');
				$('#optionword').prop('checked',false);
			}
			if($('#accountcheck').is(':checked'))
			{
				$('#accountcheck').removeProp('checked');
				$('#accountcheck').prop('checked',false);
			}
		//	$('#myModalSelect').modal({backdrop: 'static', keyboard: false});
			$('#myModalSelect').modal({backdrop: 'static', keyboard: false,toggle:true});
			 i++;
		})
	});


	$('#mustword').click(function(){
		$("[name='mustword']").each(function(){

			if($(this).is(':checked')) {
				$(this).removeProp('checked');
				$(this).prop('checked',false);
			}
			else
			{
				$(this).prop("checked",true);//全选
			}
		})
	})
	$('#quanxuanzaccount').click(function(){
		$("[name='account']").each(function(){

			if($(this).is(':checked')) {
				$(this).removeProp('checked');
				$(this).prop('checked',false);
			}
			else
			{
				$(this).prop("checked",true);//全选
			}
		})
	})

	$('#optionword').click(function(){
		$("[name='optionword']").each(function(){

			if($(this).is(':checked')) {
				$(this).removeProp('checked');
				$(this).prop('checked',false);
			}
			else
			{
				$(this).prop("checked",true);//全选
			}
		})
	})



	$('#accountcheck').click(function(){

		var accounttext="";
		$('input[name="account"]:checked').each(function() {

			accounttext += ","+$(this).val();
		});
		if(accounttext=='')
		{
			alert('请选择账号');
			return false;
		}


		var sku = $('#skuname').val();
		var addmustword = $('#addmustword').val();
		var addoptionword = $('#addoptionword').val();
		var productid = $('#draft_productid').val();
		var perfectnum = $('#perfectnum').val();
		var mustword="";
		$('input[name="mustword"]:checked').each(function(){
			 mustword= mustword+','+$(this).val();

		});

		var optionword="";
		$('input[name="optionword"]:checked').each(function(){
			 optionword= optionword+','+$(this).val();
		});


		if($('#empty_biaoti').is(':checked')) {
			var empty_biaoti = 'yes';
		}else{
			var empty_biaoti = 'no';
		}


		$.ajax({
			url: '<?php echo admin_base_url("publish/smt/auto_draft_list");?>',
			data: 'sku='+sku+'&addmustword='+addmustword+'&addoptionword='+addoptionword+'&productid='+productid+'&perfectnum='+perfectnum+'&mustword='+mustword+'&optionword='+optionword+'&empty_biaoti='+empty_biaoti+'&accounttext='+accounttext,
			type: 'POST',
			dataType: 'JSON',
			success: function(data){

			alert(data.info);
			$('#myModalSelect').modal('toggle');
				window.location.reload();
			}
		})
	/*	alert(sku);
		alert(addmustword);
		alert(addoptionword);
		alert(mustword);
		alert(optionword);
		alert(perfectnum);
		alert(productid);
*/
	})

	$("#product_post").click(function(){
		var productIds = $('input[name="ids[]"]:checked').map(function(){
			return $(this).val();
		}).get().join(',');

		if (!productIds){
			showtips('请先选择行', 'alert-warning');
			return false;
		}
		var ii = layer.load('执行中')
		$.ajax({
			url: '<?php echo admin_base_url("publish/smt/draft_postProduct");?>',
			data: 'productIds='+productIds,
			type: 'POST',
			dataType: 'JSON',
			success: function(data){
				layer.close(ii);

				if (data.status) {
					showxbtips(data.info);
				}
				else
				{
					alert(data.info);
				}
			}
		})
	})
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
})
</script>