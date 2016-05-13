<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue"><a href='#' title='收货人或地址或邮箱或邮编相同，且提交退款订单个数超过5个的，初步判定为黑名单客户'>黑名单客户管理</a>  <span class="btn-lg btn btn-success">待确认黑名单客户总数：<?php echo $k;?>个</span><span class="btn-lg btn btn-success">待确认黑名单客户订单总数：<?php echo $j;?>个</span><span class="btn-lg btn btn-success">已确认黑名单客户总数：<?php echo $count_order;?>个</span><span class="btn-lg btn btn-success">已确认黑名单客户订单总数：<?php echo $count;?>个</span></h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">		
                    <form method="get" action="<?php echo admin_base_url('export_data/orders_blacklist/index');?>">			       
                            <label>
                                 平台:<select name="orders_type" id="orders_type">
								          <option value="">请选择</option>
										  <?php foreach($orders_type as $k => $v):?>
										  <?php if($k !==0 ){?>
								          <option value="<?php echo $k;?>" <?php if(isset($ret['orderstype']) && ($ret['orderstype']==$k)){echo "selected='selected'";}?>><?php echo $v;?></option>
										  <?php } endforeach;?>
										  </select>
										  
                            </label>
							<label>
                                 收货人ID:<input type="text" id="buyer_id"  name="buyer_id" value="<?php echo isset($ret['buyer_id'])?$ret['buyer_id']:'';?>" />
                                                                         
                            </label>
							<label>
                                 收货人:<input type="text" id="buyer_name"  name="buyer_name" value="<?php echo isset($ret['buyer_name'])?$ret['buyer_name']:'';?>" />
                                                                         
                            </label>
                            <label>
                                 邮箱:<input type="text" id="buyer_zip"  name="buyer_zip" value="<?php echo isset($ret['buyer_zip'])?$ret['buyer_zip']:'';?>" />
                            </label>
							<label>
                                 黑名单客户:<select name="status" id="status">
								          <option value="">     请选择</option>
								          <option value="3" <?php if(isset($ret['status']) && ($ret['status']==3)){echo "selected='selected'";}?>>待确认黑名单客户</option>
										  <option value="1" <?php if(isset($ret['status']) && ($ret['status']==1)){echo "selected='selected'";}?>>已确认黑名单客户</option>
										   <option value="2" <?php if(isset($ret['status']) && ($ret['status']==2)){echo "selected='selected'";}?>>导入待处理黑名单客户</option>
										  </select>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" id="house" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            &nbsp;</label>
							<label>
							<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('export_data/orders_blacklist/index');?>">清空</a>
							</label>
                        </form> 			        
					</div>
				</div>
				<div class="row">
				<label><?php if($key == 'manager' || $key == 'smt_sale' || $uid = 1028 || $uid = 395){?><a class="btn btn-primary btn-sm" id="odd" onclick="true_black()">确认为黑名单</a><?php }?></label>&nbsp;&nbsp;&nbsp;
			  <label><a class="btn btn-primary btn-sm" id="odd" onclick="odd()">导出勾选的内单号</a></label>&nbsp;&nbsp;&nbsp;
			  <label><a class="btn btn-primary btn-sm" id="odd" onclick="oddall()">导出所有内单号</a></label>
			  <label><a class="btn btn-primary btn-sm" id="deldata" onclick="deldata()">删除勾选数据</a></label>
			  <div class="col-sm-12" style="width:600px;">
            	 <form id='submitForm' method='post' enctype="multipart/form-data" action="<?php echo admin_base_url('export_data/orders_blacklist/import_data');?>">            	
            	 <label><input class="btn btn-primary btn-sm" name='excelFile' type='file' id='file' ></label>
            	 <label><input type='hidden' name='add' value='add'></label>
            	 <label><a class="btn btn-primary btn-sm" id="export" >导入黑名单订单数据</a></label>
            	 <label><a href="<?php echo base_url('attachments/template/orders_blacklist.xls');?>"><span class="w-40-h-20">导入黑名单订单数据模版格式</span></a></label>				
				</form>
				 				
				</div>
			 </div>
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <thead>
    	           <tr>  	           
				        <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="checkall()" /></td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">序号</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">平台</td>
						<td style="font-size:14px;font-weight:bold;padding:5px;">内单号</td>
						<td style="font-size:14px;font-weight:bold;padding:5px;">收货人ID</td>  
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">收货人</td>    
						<td style="font-size:14px;font-weight:bold;padding:5px;">邮箱</td> 
						<td style="font-size:14px;font-weight:bold;padding:5px;">邮编</td> 
						<td style="font-size:14px;font-weight:bold;padding:5px;">退款订单数</td> 
						<td style="font-size:14px;font-weight:bold;padding:5px;">客户总订单数</td> 
                        <td style="font-size:14px;font-weight:bold;padding:5px;">备注</td>  						
    		       </tr>
		       </thead>
		        <?php foreach($data as $key => $val):?>
		        <tr  id="<?php echo $val['erp_orders_id']; 
				$color = ''; 
				if($val['status'] == 1){
					$color = "PowderBlue";
				}else if($val['color_type'] == 1){
					$color = "Wheat";
				} else if($val['color_type'] == 2){
					$color = "PaleGreen";
				}else if($val['color_type'] == 3){
				    $color = "Yellow";
				}
				?>">
				<td <?php echo "style='background-color:".$color."'";?>><input type="checkbox" id="shipmentCost" name="shipmentCost" value="<?php echo $val['erp_orders_id'];?>" /></td>
		        <td <?php echo "style='background-color:".$color."'";?>><?php echo $per++;?></td>		        		               
		        <td <?php echo "style='background-color:".$color."'";?>><?php echo $orders_type[$val['orders_type']];?></td>
				<td <?php echo "style='background-color:".$color."'";?>><a href="http://erp.moonarstore.com/all_orders_manage.php?<?php if($val['buyer_email'] && $val['color_type'] == 2){ ?> buyer_id=<?php echo $val['buyer_email'];}else if($val['buyer_name'] && $val['color_type'] == 1){?>buyer_name=<?php echo $val['buyer_name'];}else if($val['buyer_id'] && $val['color_type'] == 3){?>buyer_id=<?php echo $val['buyer_id'];}else{?>buyer_id=<?php echo $val['buyer_id'];}?>&withNotes=is_blacklist&Submit6=筛选" target="_blank" title='点击链接到全部订单管理' style="font-size: 12px"><?php echo $val['erp_orders_id'];?></a></td>
				<td <?php echo "style='background-color:".$color."'";?>><?php echo $val['buyer_id'];?></td>
				<td <?php echo "style='background-color:".$color."'";?>><?php echo $val['buyer_name'];?></td>
		        <td <?php echo "style='background-color:".$color."'";?>><?php echo $val['buyer_email'];?></td>    
                <td <?php echo "style='background-color:".$color."'";?>><?php echo $val['buyer_zip'];?></td>
				<td <?php echo "style='background-color:".$color."'";?>><?php echo $val['times'];?></td>
				<td <?php echo "style='background-color:".$color."'";?>><?php echo $val['orders_count'];?></td>
		        <td <?php echo "style='background-color:".$color."'";?> style="width:80px;"><a href="<?php echo $val['remark'];?>" target="_blank"><?php echo $val['remark'];?></a></td> 			
		         </tr>
		        <?php endforeach;?>
		        </table>
				<?php  $this->load->view('admin/common/page_number');?> 
	        </div>
	        
	       
	        
	      </div>
	   </div>	      		       
    </div>
</div> 

<div class="row" style="padding-left:10px;" >
	        <br><br><h5><b>黑名单客户条件如下：</b></h5><br>
1.邮编+收货人或收货人id或邮箱相同，且提交退款订单个数超过5个的，初步判定为黑名单客户；<br>
2.列表数据每星期日更新一次，确认为黑名单客户数据保留；<br>
3.淡橙色为收货人姓名+邮编搜索，淡绿色为邮箱搜索，黄色为收货人id搜索的黑名单客户；<br>
4.浅蓝色背景为已确认为黑名单客户信息,无色为导入待处理数据；<br>
5.导出所有内单号，选择平台则分平台导出 ；<br>


	        </div>
<script>
function checkall(){  //选中取消checkbox
	 var checklist = document.getElementsByName ("shipmentCost");
	   if(document.getElementById("checkAll").checked)
	   {
	   for(var i=0;i<checklist.length;i++)
	   {
	      checklist[i].checked = true;
	   }
	 }else{
	  for(var j=0;j<checklist.length;j++)
	  {
	     checklist[j].checked = 0;
	  }
	 }
	}
	//导出订单号
function odd(){  //获取checkbox的值,','拼接
	var obj=document.getElementsByName('shipmentCost'); 
	var s='';
	for(var i=0; i<obj.length; i++){
	if(obj[i].checked) s+=""+obj[i].value+""+',';   //将erp_orders_id拼接起来
	}
	if(s){   //当s有值的时候能执行导出操作
		if(confirm("确认导出勾选的黑名单客户数据？")){
		location.href="<?php echo admin_base_url('export_data/orders_blacklist/exportorder?erp_orders_id=');?>"+s+"";
	}
  }else{
	  alert("请选择要导出的黑名单客户数据！");
	  return false;
  }
}
	//确认为黑名单
function true_black(){  
	var obj=document.getElementsByName('shipmentCost'); 
	var s='';
	for(var i=0; i<obj.length; i++){
	if(obj[i].checked) s+=""+obj[i].value+""+',';   //将erp_orders_id拼接起来
	}
	if(s){   //当s有值的时候能执行导出操作
		if(confirm("确认将勾选的客户改为黑名单客户？")){
		location.href="<?php echo admin_base_url('export_data/orders_blacklist/true_black?erp_orders_id=');?>"+s+"";
	}
  }else{
	  alert("请选择要确认更改的数据！");
	  return false;
  }
}

	//删除勾选数据
function deldata(){  
	var obj=document.getElementsByName('shipmentCost'); 
	var s='';
	for(var i=0; i<obj.length; i++){
	if(obj[i].checked) s+=""+obj[i].value+""+',';   //将erp_orders_id拼接起来
	}
	if(s){   //当s有值的时候能执行删除操作
		if(confirm("确认将勾选的黑名单客户删除？")){
		location.href="<?php echo admin_base_url('export_data/orders_blacklist/deldata?orders_id=');?>"+s+"";
	}
  }else{
	  alert("请选择要确认删除的数据！");
	  return false;
  }
}

//导出订单号
function oddall(){
	
	var orders_type = $("#orders_type").val();
	var status = $("#status").val();
	if(orders_type == 1){
		type = 'eBay';
	}
	else if(orders_type == 2){
		type = 'B2C商城';
	}
	else if(orders_type == 3){
		type = '线下交易';
	}
	else if(orders_type == 5){
		type = '补货';
	}
	else if(orders_type == 6){
		type = '速卖通';
	}
	else if(orders_type == 7){
		type = 'AMZ亚马逊';
	}
	else if(orders_type == 8){
		type = 'FBA头程';
	}
	else if(orders_type == 9){
		type = '淘宝仓订单';
	}
	else if(orders_type == 10){
		type = '海外仓头程';
	}
	else if(orders_type == 11){
		type = '新蛋网';
	}
	else if(orders_type == 12){
		type = '德国共享仓';
	}
	else if(orders_type == 13){
		type = 'wish';
	}
	else if(orders_type == 14){
		type = 'AllBuy';
	}
	else if(orders_type == ''){
		type = '全部';
		orders_type = "all";
	}
	data = '';
	if(status == 1){
		data = '已确认黑名单客户';
	}else if(status == 2){
		data = '导入待处理的黑名单客户';
	}else if(status == 3){
		data = '待确认黑名单客户';
	}else if(status == ''){
		data = '';
	}
    if(confirm("确认导出"+type+"平台"+data+"的订单？")){
		
			location.href="<?php echo admin_base_url('export_data/orders_blacklist/exportorder?orders_type=');?>"+orders_type+"&status="+status+"";
		
	}else{
		
		return false;
	}	
} 
//判断输入的是否为空
$("#house").click(function(){
	var buyer_name = $("#buyer_name").val();
	var buyer_id = $("#buyer_id").val();
	var buyer_zip = $("#buyer_zip").val();
	var orders_type = $("#orders_type").val();
	var status = $("#status").val();
	if(!status&&!orders_type&&!buyer_zip&&!buyer_id&&!buyer_name){
	    	alert("请输入搜索条件！");
			return false;
	}
});
//导入excel
$("#export").click(function(){
	var file=$("#file").val();
	if(file==""){ showxbtips('请选择需要导入的excel', 'alert-warning');return false;}
$("#submitForm").submit();
});
</script>