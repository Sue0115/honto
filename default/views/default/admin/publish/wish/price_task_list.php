<?php
/**
 * wish调价任务列表
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">Wish-调价任务列表</h3>
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
				      </label>
				      <label>
				       <select name="search[price_status]" id="price_status">
				         <option value="">请选择调价状态</option>
				         <option value="1">未调价</option>
				         <option value="2">已调价</option>
				         <option value="3">异常</option>
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
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/wish/price_task_list');?>">清空</a>
					</label>
					<label>
						<a class="btn btn-sm btn-primary batch_operate">
	                    	  批量删除
	                    </a>
					</label>
				  </form>
				</div>
			   <form action="" method="post" >
				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="3%">
						<col width="12%">
						<col width="12%">
						<col width="14%"/>
						<col width="6%"/>
						<col width="6%"/>
						<col width="6%"/>
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="6%">
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<th>账号</th>
							<th>产品ID</th>
							<th>调价sku</th>
							<th>调价类型</th>
							<th>调价幅度</th>
							<th>调价后价格</th>
							<th>调价状态</th>
							<th>创建时间</th>
							<th>API请求时间</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($data_list as $da):?>
						  <tr>
						    <td class="center">
						      <input type="checkbox" name="ids[]" value="<?php echo $da->id?>" <?php echo ($da->status==2) ? 'disabled' : ''?>/>
						    </td>
						    <td class="center"><?php echo $userInfo[$da->account]?></td>
						    <td class="center"><?php echo $da->productID?></td>
						    <td class="center">
							    <?php echo $da->original_sku?>
						    </td>
						    <td class="center">
							    <?php echo $price_type[$da->price_type]?>
						    </td>
						    <td class="center"><?php echo $da->price_amount?></td>
						    <td class="center"><?php echo $da->price?></td>
						    <td class="center"><?php echo $price_status[$da->status]?></td>
						    <td class="center"><?php echo $da->create_time?></td>
						    <td class="center"><?php echo !empty($da->API_time) ? $da->API_time : '未请求'?></td>
						    <td class="center">
						     
						      <a title="删除" class="a_delete" data-id="<?php echo $da->id;?>" style="cursor:pointer;">
									<i class="icon-trash bigger-130 red"></i>
							  </a>
							  
						    </td>
						  </tr>
						<?php endforeach;?>
					</tbody>
				</table>
			   </form>
				<?php
					$this->load->view('admin/common/page_number');
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#account").val("<?php echo $search['account']?>");
$("#price_status").val("<?php echo $search['price_status']?>");
$("#productID").val("<?php echo $search['productID']?>");

$("#parent_sku").val("<?php echo $search['parent_sku']?>");

$(function(){
    

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
	//单个删除
	$(".a_delete").click(function(){
		if (!confirm('确定要删除吗？')){
			return false;
		}
	    	var Ids = $(this).attr('data-id');
			$.ajax({
				url: '<?php echo admin_base_url("publish/wish/batch_delete_price");?>',
				data: 'Ids='+Ids,
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
	
	//批量审核通过
	$(".batch_operate").click(function(){
		if (confirm('确定要批量删除数据吗？')){
	    	var Ids = $('input[name="ids[]"]:checked').map(function() {
				return $(this).val();
			}).get().join(',');
			if (Ids == ''){
				alert('请勾选需要的数据');
				return false;
			}
	
			$.ajax({
				url: '<?php echo admin_base_url("publish/wish/batch_delete_price");?>',
				data: 'Ids='+Ids,
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
		}
    });
	
	
})

</script>