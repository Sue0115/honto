<?php
/**
 * wish数据导入列表
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<style>
	.datashow{display:block;}
	.datahide{display:none}
</style>
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">Wish-数据导入列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="get">
				     <label>
				            账号:
				       <select name="search[account]" id="account">
				         <option value="">请选择账号</option>
				         <?php foreach ($userInfo as $k => $u):?>
				          <option value="<?php echo $k?>"><?php echo $u?></option>
				         <?php endforeach;?>
				       </select>
				       
				        <select name="search[auditing]" id="auditing">
				         <option value="">请选择审核状态</option>
				         <?php foreach ($products_auditing as $k => $pa):?>
				          <option value="<?php echo $k?>"><?php echo $pa ?></option>
				         <?php endforeach;?>
				       </select>
				       
				       <select name="search[upload]" id="upload">
				         <option value="">请选择刊登状态</option>
				         <?php foreach ($products_upload as $ke => $pu):?>
				          <option value="<?php echo $ke?>"><?php echo $pu ?></option>
				         <?php endforeach;?>
				       </select>
				       
				     </label>
				     <label>
						产品ID:
						<input type="text" name="search[productID]" placeholder="请输入产品ID" value="" id="productID"/>
					 </label>
					 <label>
						sku:
						<input type="text" name="search[parent_sku]" placeholder="请输入sku" value="" id="parent_sku"/>
					 </label>
				     <label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/wish/wish_data_import');?>">清空</a>
					</label>
				 	<label>
						<a class="btn btn-sm btn-primary" target="_blank" id="import_data">
	                      <i class="icon-plus"></i>导入数据
	                    </a>
					</label> 
					<label>
						<a class="btn btn-sm btn-primary batch_operate" data-id="2">
	                    	  批量审核通过
	                    </a>
					</label>
					<label>
						<a class="btn btn-sm btn-primary batch_operate" data-id="3">
	                      	批量审核未通过
	                    </a>
					</label>  
				  </form>
				    
					
				</div>
			   <form action="" method="post" >
				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="3%">
						<col width="4%">
						<col width="8%">
						<col width="14%"/>
						<col width="6%"/>
						<col width="23%">
						<col width="22%">
						<col width="4%">
						<col width="4%">
						<col width="5%">
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
							<th>主图</th>
							<th>账号</th>
							<th>产品ID</th>
							<th>父sku</th>
							<th>产品标题</th>
							<th>Tags</th>
							<th>价格</th>
							<th>运费</th>
							<th>审核状态</th>
							<th>刊登状态</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($data_list as $da):?>
						  <tr>
						    <td class="center">
						      <input type="checkbox" name="ids[]" value="<?php echo $da->id?>" <?php echo ($da->is_upload==2) ? 'disabled' : ''?>/>
						    </td>
						    <td class="center">
						    	<a href="<?php echo $da->original_main_image ?>" target="_blank">
						    	  <img src="<?php echo $da->original_main_image ?>" style="width:80px;height:80px;" />
						    	</a>
						    </td>
						    <td class="center"><?php echo $userInfo[$da->account]?></td>
						    <td class="center"><?php echo $da->productID?></td>
						    <td class="center" atype="parent_sku">
							    <a class="skuInfo" data-id="<?php echo $da->id?>" style="cursor:pointer;">
							       <?php echo $da->parent_sku?>
								</a><input type="text" class="datahide" value="<?php echo $da->parent_sku?>"/>
						    </td>
						    <td class="center" atype="product_name"><?php echo $da->product_name?><input type="text" class="datahide" value="<?php echo $da->product_name?>"/></td>
						    <td class="center" atype="Tags"><span><?php echo $da->Tags?></span><input type="text" class="datahide" value="<?php echo $da->Tags?>"/></td>
						    <td class="center" atype="price"><?php echo $da->price?><input type="text" class="datahide" value="<?php echo $da->price?>"/></td>
						    <td class="center" atype="shipping"><?php echo $da->shipping?><input type="text" class="datahide" value="<?php echo $da->shipping?>"/></td>
						    <td class="center"><?php echo $products_auditing[$da->auditing_status]?></td>
						    <td class="center"><?php echo $products_upload[$da->is_upload]?></td>
						  </tr>
						<?php endforeach;?>
					</tbody>
				</table>
			   </form>
				<?php
				
					$this->load->view('admin/common/page');
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#account").val("<?php echo $search['account']?>");
$("#auditing").val("<?php echo $search['auditing']?>");
$("#productID").val("<?php echo $search['productID']?>");
$("#upload").val("<?php echo $search['upload']?>");
$("#parent_sku").val("<?php echo $search['parent_sku']?>");
$(function(){
	//实现双击修改
	$(".dataTable tr td").dblclick(function(){

		var url ="<?php echo admin_base_url('publish/wish/ajaxmodifyorder');?>";
		var type = $(this).attr('atype');
		if(type != 'parent_sku' && type != 'product_name' && type != 'Tags' && type != 'price' && type != 'shipping'){
			return false;
		}
		var oBid = $(this).parents('tr').find('td').find('input[type=checkbox]');
		var orderid = $(oBid).val();
		var oBinput = $('input',this);
		var oBthis = $(this);
		var width = $(this).width();
		var height = $(this).height();
		var ipval = $(oBinput).val();
		if(typeof(ipval) == 'undefined'){return false;}
		var html = "<textarea>"+ipval+"</textarea>";
		$(this).html(html);
		$('textarea',this).width(width);
		$('textarea',this).height(height);
		$('textarea').focus();
		//textarea失去焦点事件
		$('textarea').blur(function(){
			var txtval = $(this).val();
			var newval = ipval;
			if(txtval !== ipval){
				if(confirm("此数据值已经改变,确定修改吗?")){
					var newval = txtval;
					var data ='id='+orderid+'&'+type+'='+newval;
					//修改数据
					$.ajax({
						url:url,
						data:data,
						type:'post',
						dataType:'text',
						success:function(msg){
							var mes = eval("(" + msg + ")");
							if(mes['status'] =='1' ){
								//修改成功
								alert('修改成功');
							};
							if(mes['status'] =='2'){
								//修改2失败
							
								alert('修改失败');
							}
						}
					});
					
				}
			}
			var newhtml = '';
			if(type == 'parent_sku'){
				newhtml = "<a class='skuInfo' data-id="+orderid+" style='cursor:pointer;'>"+newval+"</a>"+"<input type='text' class='datahide' value='"+newval+"'/>";
			}else{
				newhtml = "<span>"+newval+"</span>"+'<input type="text" class="datahide" value="'+newval+'"/>';
			}
			
			$(oBthis).html(newhtml);
			//给SKU绑定事件
			if(type == 'parent_sku'){
				$(".skuInfo").click(function(){
				  var id=$(this).attr("data-id");
				  $.layer({
						type   : 2,
						shade  : [0.4 , '' , true],
						title  : ['记录详情',true],
						iframe : {src : '<?php echo admin_base_url("publish/wish/getInfoByID?id=");?>'+id},
						area   : ['800px' , '600px'],
						success : function(){
							layer.shift('top', 400)
						}
					});
					return false;
				});
			}
		});
    })
	
    /**
	 * 导入数据功能
	 * @param  {[type]} event [description]
	 * @return {[type]}       [description]
	 */
	$(document).on('click', '#import_data', function(event) {
		event.preventDefault();
		/* Act on the event */
		if (confirm('确定要导入数据吗？')) {
			//弹出层选择账号
			$.layer({
				type   : 2,
				shade  : [0.4 , '' , true],
				title  : ['选择账号',true],
				iframe : {src : '<?php echo admin_base_url("publish/wish/showAccountToCopyProduct?action=import");?>'},
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
						upload_file_view(account_list);
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

	$(".skuInfo").click(function(){
	  var id=$(this).attr("data-id");
	  $.layer({
			type   : 2,
			shade  : [0.4 , '' , true],
			title  : ['记录详情',true],
			iframe : {src : '<?php echo admin_base_url("publish/wish/getInfoByID?id=");?>'+id},
			area   : ['800px' , '600px'],
			success : function(){
				layer.shift('top', 400)
			}
		});
		return false;
	});

	//批量审核通过
	$(".batch_operate").click(function(){
		var action = $(this).attr('data-id');
    	var Ids = $('input[name="ids[]"]:checked').map(function() {
			return $(this).val();
		}).get().join(',');
		if (Ids == ''){
			alert('请勾选需要的数据');
			return false;
		}
		$.ajax({
			url: '<?php echo admin_base_url("publish/wish/batch_operate");?>',
			data: 'Ids='+Ids+'&action='+action,
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
        return false;
    });
	
	//选择账号以后，弹出选择上传文件的界面
	function upload_file_view(account){
		//弹出上传文件层
		$.layer({
			type   : 2,
			shade  : [0.4 , '' , true],
			title  : ['上传文件',true],
			iframe : {src : '<?php echo admin_base_url("publish/wish/upload_file?account=");?>'+account},
			area   : ['700px' , '350px'],
			success : function(){
				layer.shift('top', 400)
			},	
			no: function(index){
				window.location.href='admin/publish/wish/wish_data_import';
				layer.close(index);
			}
		});
		return false;
	}
})

</script>