<?php
/**
 * wish监控
 * 查看已发货未标记的订单
 * 查看欠货未标记的订单
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>
<?php if($type==1):?>  
<div class="row">
  <div class="col-xs-12">
    <h3 class="header small lighter blue">wish-手动标记发货</h3>
    <div class="table-header">
	  <span style="color:#fff;font-weight:bold;">
	         输入要标记发货的订单号
	  </span>
	</div>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
       <div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
		
				     <label>
						内单号:
						<input type="text" name="erp_orders_id" placeholder="请输入要标记发货的内单号" value="" id="erp_orders_id" />
					 </label>
			
				    <label>
						<a class="btn btn-primary btn-sm" id="mark_ship">标记发货</a>
					</label>
				</div>
			</div>
	  </div>
  </div>
</div>

<?php endif;?>
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">wish-<?php echo $type==1 ? '已发未标记' : '超过五天未标记'?></h3>
	
		<div class="table-header">
		  <span style="color:#fff;font-weight:bold;">
		     <?php if($type==1):?> erp已发货但是平台未标记发货，发货时间超过一天的订单<?php else:?>
		          订单导入时间超过五天平台未标记的订单
		     <?php endif;?>
		  </span>
		</div>
	
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
				            <?php echo $a;?>  
				           </option>
				         <?php endforeach;?>
				       </select>
				     </label>
				     
				     <label>
						内单号:
						<input type="text" name="search[orderID]" placeholder="请输入内单号" value="" id="orderID" />
					 </label>
					 
				     <label>
						buyer_id:
						<input type="text" name="search[buyer_id]" placeholder="请输入买家id" value="" id="buyer_id" />
					 </label>
					 
					 <label>
					   发货时间:<input type="text"  value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="search[start_date]"/>
					</label>
					<label>
					  ~<input type="text" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="search[end_date]"/>
					</label>
					 
				     <label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					 
				    <label>
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('wish/wish_monitor/shipping_no_mark');?>">清空</a>
					</label>
					
				  </form>
				  
					
				</div>
				
			  <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content" style="WORD-BREAK:break-all">
					<colgroup>
						<col width="1%">
						<col width="6%"/>
						<col width="10%"/>
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<td>内单号</td>
							<th>buyer_id</th>
							<th>账号</th>
							<th>订单导入时间</th>
							<th>发货时间</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($data as $item):
							
						?>
						<tr>
							<td class="center">
								<label>
									<input type="checkbox" class="ace" name="orderID[]" value="<?php echo $item->erp_orders_id;?>" >
									<span class="lbl"></span>
								</label>
							</td>
							<td><?php echo $item->erp_orders_id;?></td>
							<td><?php echo $item->buyer_id;?></td>
							<td><?php echo $account[$item->sales_account];?></td>
							<td><?php echo $item->orders_export_time?></td>
							<td><?php echo $item->orders_shipping_time;?></td>
						</tr>
						<?php
						endforeach;
						?>
					</tbody>
				</table>

				<?php
				
					$this->load->view('admin/common/page_number');
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#account").val("<?php echo $search['account']?>");
$("#orderID").val("<?php echo $search['orderID']?>");
$("#buyer_id").val("<?php echo $search['buyer_id']?>");

//
$("#mark_ship").click(function(){
	var order_id = $("#erp_orders_id").val();
	if(order_id==''){
	    alert('请输入要标记的订单号');
	    return false;
	}
	if(confirm('确定要手动标记发货？')){
    	layer.load('正在标记，请耐心等候。。', 3);
    	$.ajax( {     
    		  url:'<?php echo admin_base_url('wish/wish_monitor/shipping_orders');?>',  
    		  type:'post',     
    		  async:false,
    		  cache:false,     
    		  dataType:'json',  
    		  data:{"orderID":order_id},   
    		  success:function(data) {
    			  result = eval(data);
  				  if(result['status']===true){
  					  layer.close();
  					  layer.alert(result['msg'], 9);
  					  layer.close();
  					  //window.location.href='/admin/wish/wish_product/productManage';
  				  }else{
  					  layer.alert(result['msg'], 8);
  					  layer.close();
  					  //window.location.href='/admin/wish/wish_product/productManage';
  				  }
    		  }        
      });
	}
    	
	return false;
});
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
				shade  : [0.4 , '' , true],
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
function synchronous(productID){
	alert(productID.attr('lang'));
}
</script>