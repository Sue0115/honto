<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">订单收款导入</h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="">
					     
					        <label>
                                                                           批次号:<input type="text"  name="search[pch]" value="<?php echo array_key_exists('pch', $search) ? $search['pch'] : '';?>" />
                                                                         
                            </label>					                                                             
                            <label>
                                                                         导入时间:<input type="text"  name="search[start_date]" value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="search[end_date]" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_end_date" />
					       </label>
					      
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                             <label>
							   <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('caiwu/order_receivables_import/order_view');?>">清空</a>
						    </label>
                        </form> 
                        
					</div>
				</div>
			<form action="<?php echo admin_base_url('caiwu/order_receivables_import/deal_data')?>" enctype="multipart/form-data" method="post" id="submitForm">
				<div class="row"> 
					<div class="col-sm-2">
		            	<label>
			            	 订单平台：
							  <select name="orders_type" id="orders_type">
							    <option value="">请选择导入数据的平台</option>
							    <?php foreach($orderTypeArr as $k => $w):?>
							    <option value="<?php echo $k?>"><?php echo $w['typeName'];?></option>
							    <?php endforeach;?>
							  </select>
						 </label>
					 </div>
					 <div class="col-sm-2">
		            	<label>
			            	 导入类型：
							  <select name="import_type" id="import_type">
							    <option value="1">订单收款导入</option>
							    <option value="2">订单退款导入</option>
							  </select>
						 </label>
					 </div>
	            	<div class="col-sm-8">
		            	 <label>
		            	    <input type="file" id="file" name="excelFile" class="btn btn-primary btn-sm">
		            	 </label>
		            	 <label>
		            	   <input type="submit" value="导入数据" id="sub"  class="btn btn-sm btn-primary"/>
		            	 </label>			
					</div>
					
				 </div>	
			 </form>
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <colgroup>
						<col width="3%">
						<col width="8%"/>
						<col width="8%"/>
						<col width="8%"/>
						<col width="8%"/>
						<col width="8%"/>
						<col width="8%"/>
						<col width="10%">
						<col width="10%">
					</colgroup>
	           <thead>
    	           <tr>  
    	                <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" /></td> 	           
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">批次号</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">订单平台</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">平台总金额</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">平台总费用</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">总条数</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">导入人</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">导入时间</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">操作</td>            	        		        
    		       </tr>
		       </thead>
		       <?php foreach($data as $item):?>
		       <tr>
		         <td>
		           <label>
					 <input type="checkbox" class="ace" name="orders_data[]" value="<?php echo $item->id;?>" >
					 <span class="lbl"></span>
				  </label>
		         </td>
		         <td><?php echo $item->import_name?></td>
		         <td><?php echo $orderTypeArr[$item->orders_type]['typeName']?></td>
		         <td><?php echo round($fee[$item->id]['orders_total'],2)?></td>
		         <td><?php echo round($fee[$item->id]['plat_amount']+$fee[$item->id]['union_amount'],2)?></td>
		         <td><?php echo $item->import_count?></td>
		         <td><?php echo $userInfo[$item->import_uid]?></td>
		         <td><?php echo $item->import_time?></td>
		         <td>
		         	<a href="<?php echo admin_base_url('caiwu/order_receivables_import/import_detail?id=')?><?php echo $item->id?>" title="查看详情" data-rel="tooltip" class="tooltip-success">
                        <span class="green">
                            <i class="icon icon-info-sign"></i>
                        </span>
                    </a>
                    <a href="<?php echo admin_base_url('caiwu/order_receivables_import/delete_import_detail?id=')?><?php echo $item->id?>" title="查看详情" data-rel="tooltip" class="tooltip-success">
                        <span class="red">
                            <i class="icon icon-trash"></i>
                        </span>
                    </a>
		         </td>
		       </tr>
		       <?php endforeach;?>
		        </table>
		        <?php
					$this->load->view('admin/common/page_number');
				?>
	        </div>
	      </div>
	   </div>	      		       
    </div>
</div>
<script type="text/javascript">
//导入excel
$("#sub").click(function(){
	
	var file=$("#file").val();
	if(file==""){ 
		showxbtips('请选择需要导入的excel', 'alert-warning');
		return false;
	}
	
	var order_type = $("#orders_type").val();
	if(order_type==""){
		 showxbtips('请选择需要导入数据的平台类型', 'alert-warning');
		 return false;
	}
	layer.load('正在导入数据,请耐心等候。。。', 3);
	$("#submitForm").submit();

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




//全选 反选
function selectAll(){
	 var checklist = document.getElementsByName ("orders_data[]");
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

//批量删除
$("#deleteSelectedAll").click(function(){
	var selected  = $("input[type='checkbox']").is(':checked');
	if(selected==false){
		showxbtips('请先选中数据', 'alert-warning');return false;
	}
	try{
		var i=0;
		var id='';
     	$('input[name="shipmentCost"]:checked').each(function(){
         	if(i!=0){
             	id +=',';
         	}
     	id += ''+jQuery(this).val()+'';
     	i++;
     		});     	    	
	} catch (e) {	
		return false;
	}
	if(confirm("确认删除？")){
		$.ajax({
	        url: "<?php echo admin_base_url('caiwu/shipmentCost/ajaxDeleteData');?>",  
	        type: "POST",
	        data:{id:id},
	        error: function(){  
	        	showxbtips('失败', 'alert-warning');   
	        },  
	        success: function(data){   
	        	showxbtips(data, 'alert-warning');
 	        	window.location.href=window.location.href;
	        }
	    });
	}
});

</script>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 













