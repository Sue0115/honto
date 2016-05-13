<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue"><a href='#' title='条件:1.过去7天在不同的拣货单中出现了3次以上（无论何种状态）或出现了2次，但当前状态是已通过（说明又被退回了）;2.SKU的库存数小于10'>拣货异常订单查询</a>  <span class="btn-lg btn btn-success">异常SKU总数：<?php echo $count;?>个</span><span class="btn-lg btn btn-success">异常订单总数：<?php echo $count_order;?></span></h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">		
                    <form method="get" action="<?php echo admin_base_url('order/exceptionOrder/orderList');?>">			       
                            <label>
                                 订单SKU:<input type="text" id="ordersku"  name="ordersku" value="<?php echo isset($ret['search']['ordersku'])?$ret['search']['ordersku']:'';?>" />
                                                                         
                            </label>
                            <label>
                                 库位:<input type="text" id="hou"  name="house" value="<?php echo isset($ret['search']['house'])?$ret['search']['house']:'';?>" />
                            </label>
							<label>
                                 所属仓库:<select name="location" id="location">
								          <option value="">请选择</option>
								          <option value="1000" <?php if(isset($ret['search']['location']) && ($ret['search']['location']==1000)){echo "selected='selected'";}?>>深圳一仓</option>
										  <option value="1025" <?php if(isset($ret['search']['location']) && ($ret['search']['location']==1025)){echo "selected='selected'";}?>>义乌仓</option>
								          </select>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" id="house" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            &nbsp;</label>
							<label>
							<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('order/exceptionOrder/orderList');?>">清空</a>
							</label>
                        </form> 			        
					</div>
				</div>
				<div class="row">
			  <label><a class="btn btn-primary btn-sm" id="odd" onclick="odd()">导出勾选的异常订单</a></label>&nbsp;&nbsp;&nbsp;
			  <label><a class="btn btn-primary btn-sm" id="odd" onclick="oddall()">导出所有异常订单</a></label>
			 </div>
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <thead>
    	           <tr>  	           
				        <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="checkall()" /></td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">序号</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">SKU</td>
						<td style="font-size:14px;font-weight:bold;padding:5px;">SKU下的异常订单</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">库位</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">实库存</td>      
						<td style="font-size:14px;font-weight:bold;padding:5px;">所属仓库</td>  
    		       </tr>
		       </thead>
		        <?php foreach($data as $key => $val):?>
		        <tr id="<?php echo $val['product_sku'];?>">
				<td ><input type="checkbox" id="shipmentCost" name="shipmentCost" value="<?php echo $val['product_sku'];?>" /></td>
		        <td><?php echo $per++;?></td>		        		               
		        <td><?php echo $val['product_sku'];?></td>
				<td><?php echo $val['orders_warehouse_id'];?></td>
				<td><?php echo $val['products_location'];?></td>
		        <td><?php echo $val['actual_stock'];?></td>    
                <td><?php if($val['warehouse']==1000){
					echo "深圳一仓";
				}else if($val['warehouse']==1025){
					echo "义乌仓";
				}?></td>				
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
	        <br><br><h5><b>拣货异常订单条件如下：</b></h5><br>
1.拣货单时间是在15天内的；<br>
2.拣货单状态是 恢复为已通过 或者 异常；<br>
3.拣货单异常次数大于等于1次；<br>
4.相关订单状态是以下之一：新录入，不通过，已打印；<br>
5.订单中sku的实际库存数在1到10之间。
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
	//导出异常订单
function odd(){  //获取checkbox的值,','拼接
	var obj=document.getElementsByName('shipmentCost'); 
	var s='';
	for(var i=0; i<obj.length; i++){
	if(obj[i].checked) s+="'"+obj[i].value+"'"+',';   //将sku拼接起来
	}
	if(s){   //当s有值的时候能执行导出操作
		if(confirm("确认导出勾选的异常订单？")){
		location.href="<?php echo admin_base_url('order/exceptionOrder/exportorder?order_id=');?>"+s+"";
	}
  }else{
	  alert("请选择要导出的数据！");
	  return false;
  }
}
//导出所有异常订单
function oddall(){
    if(confirm("确认导出所有异常订单？")){
		location.href="<?php echo admin_base_url('order/exceptionOrder/exportorder?oddall=');?>"+1+"";
	}else{
		return false;
	}	
}
$("#house").click(function(){
	var house = $("#hou").val();
	var ordersku = $("#ordersku").val();
	var location = $("#location").val();
	if(!house&&!ordersku&&!location){
	    	alert("请输入搜索条件！");
			return false;
	}
});
</script>