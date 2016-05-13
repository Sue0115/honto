<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流对账详情</h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="<?php echo admin_base_url('caiwu/shipmentCost/listShow');?>">	
					    	<label>
                                   <input type="hidden"  name="main_id_search" value="<?php echo isset($main_id_search)?$main_id_search:'';?>" />
                                                                         
                            </label>			       
                            <label>
                                                                         挂号码:<input type="text"  name="shipping_code_search" value="<?php echo isset($data['search']['shipping_code_search'])?$data['search']['shipping_code_search']:'';?>" />
                                                                         
                            </label>
                            <label>
                                                                         内单号:<input type="text"  name="orders_id_search" value="<?php echo isset($data['search']['orders_id_search'])?$data['search']['orders_id_search']:'';?>" />
                            </label>
                            <label>
                                                                        订单SKU:<input type="text"  name="orders_sku_search" value="<?php echo isset($data['search']['orders_sku_search'])?$data['search']['orders_sku_search']:'';?>" />
                            </label>
                            
                            <label>
                                                                         计费重量:<input type="text"  name="weight_search_start" value="<?php echo isset($data['search']['weight_search_start'])?$data['search']['weight_search_start']:'';?>"   />
                            </label>
                            <label>
					           ~<input type="text"  name="weight_search_end" value="<?php echo isset($data['search']['weight_search_end'])?$data['search']['weight_search_end']:'';?>"  />
					       </label>
                            
                            <label>
                                                                         订单发货时间:<input type="text"  name="shipping_start_date" value="<?php echo isset($data['search']['shipping_start_date'])?$data['search']['shipping_start_date']:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="shipping_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="shipping_end_date" value="<?php echo isset($data['search']['shipping_end_date'])?$data['search']['shipping_end_date']:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="shipping_end_date" />
					       </label>
                            <label>
                                                                         订单匹配物流:<input type="text"  name="shipmentAutoMatched_search" value="<?php echo isset($data['search']['shipmentAutoMatched_search'])?$data['search']['shipmentAutoMatched_search']:'';?>" />
                            </label>
                            <label>
                                                                         国家简称:<input type="text"  name="buyer_country_code_search" value="<?php echo isset($data['search']['buyer_country_code_search'])?$data['search']['buyer_country_code_search']:'';?>" />
                            </label>
                            <label>
                                                                         渠道名称:<input type="text"  name="shipment_name_search" value="<?php echo isset($data['search']['shipment_name_search'])?$data['search']['shipment_name_search']:'';?>" />
                            </label>
                            <label>
                                                                         重量差异百分比:<input type="text"  name="weight_differential_search_start" value="<?php echo isset($data['search']['weight_differential_search_start'])?$data['search']['weight_differential_search_start']:'';?>" />
                            </label>
                            <label>
                               ~<input type="text"  name="weight_differential_search_end" value="<?php echo isset($data['search']['weight_differential_search_end'])?$data['search']['weight_differential_search_end']:'';?>" />
                            </label>                                                      
                            <label>
                                                                         导入时间:<input type="text"  name="import_start_date" value="<?php echo isset($data['search']['import_start_date'])?$data['search']['import_start_date']:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="import_end_date" value="<?php echo isset($data['search']['import_end_date'])?$data['search']['import_end_date']:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_end_date" />
					       </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                            
                        </form> 
                        
					</div>
				</div>
			<!--<div class="row"> 
            	<div class="col-sm-12" style="width:666px;">
            	 <form id='submitForm' method='post' enctype="multipart/form-data" action="<?php //echo admin_base_url('caiwu/shipmentCost/listShow');?>">            	
            	 <label><input class="btn btn-primary btn-sm" name='excelFile' type='file' id='file' ></label>
            	 <label><input type='hidden' name='add' value='add'></label>
            	
            	 <label><a  target="_blank" href="<?php //echo base_url('attachments/template/shipmentCostTemplate.xls');?>"><span class="w-40-h-20">导入物流费用模版格式</span></a></label>
            	  <label><a class="btn btn-primary btn-sm" id="deleteSelectedAll" >批量删除</a></label>	 			
				</form>
				 				
				</div>
				
			 </div>	-->
			 <div class="row">
			  <label><a class="btn btn-primary btn-sm" id="deleteSelectedAll" ><i class="icon-trash bigger-110"></i>批量删除</a></label>
			  <label><a class="btn btn-primary btn-sm" id="export_out" onclick="return export_out(<?php echo $main_id_search;?>)">导出批次异常数据</a></label>
			 </div>
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <thead>
    	           <tr>  
    	                <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" /></td> 
    	                <td style="font-size:14px;font-weight:bold;padding:5px;">批次号</td>	           
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">挂号码</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">内单号</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">订单类型</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">订单SKU</td>
        	           	
        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">订单发货时间</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">订单匹配物流</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">国家简称</td>
        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">目的地</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">计费重量(kg)</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">理论重量(kg)</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;"><a style="cursor:pointer;" title="(计费重量-理论重量)/理论重量,红色是大于百分之15,蓝色是小于百分之15">重量差异百分比</a></td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;"><a style="cursor:pointer;" title="通折:((不含挂号费+挂号费)*通折);非通折:((不含挂号费*非通折)+挂号费);">计费运费(元)</a></td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;"><a style="cursor:pointer;" title="在原理论运费上每单减掉0.35的处理费">理论运费(元)</a></td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">渠道名称</td> 
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">导入时间</td>
        	           	
        	           	<!--  <td style="font-size:14px;font-weight:bold;padding:5px;">操作</td> -->	           	        		        
    		       </tr>
		       </thead>
		        <?php foreach($data['info'] as $key => $val):?>
		        <tr id="<?php echo 'tr'.$val->id;?>">		        
		        <?php if(!empty($val->packetWeight) && ((($val->weight-$val->packetWeight)/$val->packetWeight)>=0.15)):?>
		        <td style="background-color:red;"><input type="checkbox" id="shipmentCost<?php echo $val->id;?>" name="shipmentCost" value="<?php echo $val->id;?>" /></td>
		        <?php elseif (!empty($val->packetWeight) && ((($val->weight-$val->packetWeight)/$val->packetWeight)<=-0.15)):?>
		        <td style="background-color:blue;"><input type="checkbox" id="shipmentCost<?php echo $val->id;?>" name="shipmentCost" value="<?php echo $val->id;?>" /></td>
		        <?php else:?>
		        <td><input type="checkbox" id="shipmentCost<?php echo $val->id;?>" name="shipmentCost" value="<?php echo $val->id;?>" /></td>
		        <?php endif;?>
		        <td><?php echo $val->main_name;?></td>
		        <td><?php echo $val->shipping_code;?></td>
		        <td><?php echo $val->orders_id;?></td>
		        <td><?php echo $val->orders_type;?></td>
		        <td><?php echo $val->orders_sku;?></td>		        
		        <td><?php echo $val->orders_shipping_time;?></td>
		        <td><?php echo $val->shipmentAutoMatched;?></td>
		        <td><?php echo $val->buyer_country_code;?></td>
		        <td><?php echo $val->country;?></td>
		        <td><?php echo $val->weight;?></td>
		        <td><?php echo $val->packetWeight;?></td>
	            <?php if(!empty($val->packetWeight) && ((($val->weight-$val->packetWeight)/$val->packetWeight)>=0.15)):?>
	            <td style="color:red;"><?php echo number_format((($val->weight-$val->packetWeight)/$val->packetWeight)*100,2)."%";?></td>
	            <?php elseif (!empty($val->packetWeight) && ((($val->weight-$val->packetWeight)/$val->packetWeight)<=-0.15)):?>
		         <td style="color:blue;"><?php echo number_format((($val->weight-$val->packetWeight)/$val->packetWeight)*100,2)."%";?></td>  
	            <?php else:?>
	            <td><?php echo number_format((($val->weight-$val->packetWeight)/$val->packetWeight)*100,2)."%";?></td>
	            <?php endif;?>
		        
		        <td><?php echo $val->cost;?></td>
		        <td><?php echo $val->erp_shippingCost;?></td>
		        <td><?php echo $val->shipment_name;?></td>
		        <td><?php echo $val->import_time;?></td>
               <!--   <td>   		                          
    		        <a href="javascript:" onclick="return deleteData(<?php //echo $val->id;?>)" class="tooltip-error" data-rel="tooltip" title="删除">
                        <span class="red">
                            <i class="icon-trash bigger-110"></i>
                        </span>
                    </a>
                    
		        </td>-->
		         </tr>
		        <?php endforeach;?>
		        </table>
		        <?php  $this->load->view('admin/common/page_number');?> 
	        </div>
	      </div>
	   </div>	      		       
    </div>
</div>
<script type="text/javascript">
//导入excel
$("#export").click(function(){
	var file=$("#file").val();
	if(file==""){ showxbtips('请选择需要导入的excel', 'alert-warning');return false;}
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


//删除
// function deleteData(id){
// 	if(confirm("确认删除？")){
// 		$.ajax({
	         
// 	        type: "POST",
// 	        data:{id:id},
// 	        error: function(){  
// 	        	showxbtips('失败', 'alert-warning');   
// 	        },  
// 	        success: function(data){   
// 	        	showxbtips(data, 'alert-warning');
// 	        	window.location.href=window.location.href;
// 	        }
// 	    });
// 	}
	
// }

//全选 反选
function selectAll(){
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
	        url: "<?php echo admin_base_url('caiwu/shipmentCost/ajaxDeleteDataDetail');?>",  
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

//导出本批次重量差异超过百分之15的数据
function export_out(id){
	if(confirm("导出本批次重量差异超过百分之15的数据？")){
		location.href="<?php echo admin_base_url('caiwu/shipmentCost/exportOut?main_id=');?>"+id+"";
	}
	
}
</script>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 













