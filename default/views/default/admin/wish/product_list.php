<?php
/**
 * listing模板管理-列表页
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">Wish-产品管理列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="get">
				     <label>
				            账号:
				       <select name="search[account]" id="account">
				         <option value="">请选择账号</option>
				         <?php foreach($account as $k => $a):?>
				           <option value="<?php echo $k?>">
				            <?php echo $a?>
				           </option>
				         <?php endforeach;?>
				         
				       </select>
				     </label>


					  <label>
						  销售代码:
						  <select name="search[sellerID]" id="sellerID">
							  <option value="">请选择销售代码</option>
							  <?php foreach($user_info as $k => $a):?>
								  <option value="<?php echo $k?>">
									  <?php echo $k?>
								  </option>
							  <?php endforeach;?>

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
					   刊登时间:<input type="text"  value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="search[start_date]"/>
					</label>
					<label>
					  ~<input type="text" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="search[end_date]"/>
					</label>
					<label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('wish/wish_product/productManage');?>">清空</a>
					</label>
				  </form>
				  <label>
						<a class="btn btn-primary btn-sm" id="syscProduct">同步广告</a>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" id="exportSomeProduct">导出选中的广告</a>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" id="exportAllProduct"">导出所有的广告</a>
					</label>
					<label>
						<a class="btn btn-sm btn-primary" target="_blank" href="<?php echo admin_base_url('publish/wish/productInfo')?>">
	                      <i class="icon-plus"></i>新增广告
	                    </a>
					</label>
					<label>
						<button class="btn btn-primary btn-sm" id="batch_copy">另存为草稿</button>
					</label>
					<label>
						<button class="btn btn-primary btn-sm" id="batch_edit_product">批量编辑在线广告</button>
					</label>
				</div>
			   <form action="<?php echo admin_base_url('wish/wish_product/exportData');?>" method="post" id="dataArea">
			   <input type="hidden" name="pageData" value="" />
			   <input type="hidden" name="account" value="<?php echo $search['account']?>"/>
				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="3%">
						<col width="6%"/>
						<col width="6%"/>
						<col width="10%">
						<col width="10%">
						<col width="40%">
						<col width="10%">
						<col width="7%">
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<td>主图</td>
							<th>账号</th>
							<th>产品ID</th>
							<th>SKU</th>
							<th>标题</th>
							<th>刊登人员</th>
							<th>刊登时间</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($data as $item):
							
						?>
						<tr>
							<td class="center">
								<label>
									<input type="checkbox" class="ace" name="productIds[]" value="<?php echo $item->productID;?>" >
									<span class="lbl"></span>
								</label>
							</td>
							<td><a href="<?php echo $mainImangeArr[$item->productID]?>" target="_blank"><img src="<?php echo $mainImangeArr[$item->productID]?>" style="width:50px;height:50px;" /></a></td>
							<td><?php echo $account[$item->account];?></td>
							<td><?php echo $item->productID;?></td>
							<td><?php echo replaceDotToShow($productSku[$item->productID]);?></td>
							<td><?php echo $item->product_name;?></td>
							<td><?php echo isset($user_info[$item->sellerID])?$user_info[$item->sellerID]:'' ;?></td>
							<td><?php echo $item->publishedTime;?></td>
							<td>
								<label>
            						<a class="btn btn-primary btn-sm editProduct" data-id="<?php echo $item->productID?>">编辑</a>
            					</label>
							</td>
						</tr>
						<?php
						endforeach;
						?>
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

$(function(){
	
 	$("#account").change(function(){
 	 	var account=$("#account").val();
 		$('[name="account"]').val(account);
 	});
	
	//导出一些广告
    $("#exportSomeProduct").click(function(){
        $('[name="pageData"]').val('some');
    	var productIds = $('input[name="productIds[]"]:checked').map(function() {
			return $(this).val();
		}).get().join(',');
		if (productIds == ''){
			alert('请先选择数据');
			return false;
		}
           $("#dataArea").submit();
        return false;
    });
    
//导出所有广告
    $("#exportAllProduct").click(function(){
        if(confirm('确定要导出该帐号下的所有数据')){
       	 $('[name="pageData"]').val('All');
         $("#dataArea").submit();
         return false;
        }
    });
    

    $("#syscProduct").click(function(){
        if(confirm('确定要同步广告数据？')){

        	layer.load('同步过程中请别刷新浏览器，请耐心等候。。', 3);
        	$.ajax( {     
        		  url:'<?php echo admin_base_url("wish/wish_product/wishGetProductData")?>',  
        		  type:'post',     
        		  async:false,
        		  cache:false,     
        		  dataType:'json',     
        		  success:function(data) {
        			  result = eval(data);
					  if(result['status']===true){
						  layer.close();
						  layer.alert('广告更新成功', 9);
						  layer.close();
						  window.location.href='/admin/wish/wish_product/productManage';
					  }else{
						  layer.alert('广告更新失败', 8);
						  layer.close();
						  window.location.href='/admin/wish/wish_product/productManage';
					  }
        		  }        
          });
        }
    	
    });

    $("[name='productID']").click(function(){
		var productID = $(this).attr('lang');
		alert(productID);
		$.ajax( {     
		  url:'<?php echo admin_base_url("wish/wish_product/getWishProductByProductID")?>',
		  data:{"pID":productID},     
		  type:'post',     
		  async:false,
		  cache:false,     
		  dataType:'json',     
		  success:function(data) {
			  result = eval(data);
			  alert(result);
		  }        
		});
    });

    /**
	 * 批量编辑广告
	 * @param  {[type]} event [description]
	 * @return {[type]}       [description]
	 */
	$(document).on('click', '#batch_edit_product', function(event) {
		event.preventDefault();
		/* Act on the event */
		var productIds = $('input[name="productIds[]"]:checked').map(function() {
			return $(this).val();
		}).get().join(',');
		if (productIds == ''){
			alert('请先选择数据');
			return false;
		}
		$.layer({
		    type   : 2,
		    shade  : [0.8 , '' , true],
		    title  : ['批量在线编辑广告',true],
		    iframe : {src : '/admin/wish/wish_product/edit_product?id='+productIds+'&type=2'},
		    area   : ['900px' , '600px'],
		    success : function(){
	            layer.shift('top', 200)  
	        },
	        yes    : function(index){

	            layer.close(index);
	            move_order();
	        }
		});
	});
		
    
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
				iframe : {src : '<?php echo admin_base_url("publish/wish/showAccountToCopyProduct");?>'},
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
							url: '<?php echo admin_base_url("publish/wish/copyListingToDraft");?>',
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

	$(document).on('click','.Wdate',function(){ 
		var o = $(this); 
		if(o.attr('dateFmt') != '') 
		WdatePicker({dateFmt:o.attr('dateFmt')}); 
		else if(o.hasClass('month')) 
		WdatePicker({dateFmt:'yyyy-MM'}); 
		else if(o.hasClass('year')) 
		WdatePicker({dateFmt:'yyyy'}); 
		else 
		WdatePicker({dateFmt:'yyyy-MM-dd'}); 
	}); 

})

//编辑在线广告
//type=1编辑单个广告，2是编辑多个
$(".editProduct").click(function(){
	var productID = $(this).attr('data-id');
	$.layer({
	    type   : 2,
	    shade  : [0.8 , '' , true],
	    title  : ['在线编辑广告',true],
	    iframe : {src : '/admin/wish/wish_product/edit_product?id='+productID+'&type=1'},
	    area   : ['900px' , '600px'],
	    success : function(){
            layer.shift('top', 200)  
        },
        yes    : function(index){

            layer.close(index);
            move_order();
        }
	});
});
</script>