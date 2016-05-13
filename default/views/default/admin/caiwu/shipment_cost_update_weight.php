<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">SKU重量更新</h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="<?php echo admin_base_url('caiwu/shipmentCost/updateWeightList');?>">					       
                            
                            <label>
                             SKU:<input type="text"  name="sku_search" value="<?php echo isset($searchArr['sku_search'])?$searchArr['sku_search']:'';?>" />
                            </label>
                            
                            <label>
                                                                        状态:<select name="status_search" id="status_search" >
    						 	 <option value="3" <?php if(isset($searchArr['status_search']) && ($searchArr['status_search']==2)){echo "selected='selected'";}?>>请选择</option>		                     
    		                     <option value="1" <?php if(isset($searchArr['status_search']) && ($searchArr['status_search']==1)){echo "selected='selected'";} if(empty($searchArr['status_search'])){echo "selected='selected'";}?>>未更新</option>
    		                     <option value="2" <?php if(isset($searchArr['status_search']) && ($searchArr['status_search']==2)){echo "selected='selected'";}?>>已更新</option>
	                 	    </select>
	                 	  </label>                            
                            <label>
                                                                        重量差异百分比:<input type="text"  name="percent_search_start" value="<?php echo isset($searchArr['percent_search_start'])?$searchArr['percent_search_start']:'';?>" />
                            </label>
                            <label>
					         ~<input type="text"  name="percent_search_end" value="<?php echo isset($searchArr['percent_search_end'])?$searchArr['percent_search_end']:'';?>" />
					       </label>
                           
                                                                                 
                            <label>
                                                                         更新时间:<input type="text"  name="update_start_date" value="<?php echo isset($searchArr['update_start_date'])?$searchArr['update_start_date']:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="update_end_date" value="<?php echo isset($searchArr['update_end_date'])?$searchArr['update_end_date']:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_end_date" />
					       </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                        </form> 
                        
					</div>
				</div>
				<div class="row"> 
            	<div class="col-sm-12">            	
            	 <label><a class="btn btn-primary btn-sm" id="deleteSelectedAll" ><i class="icon-trash bigger-110"></i>批量删除</a></label>           	 
            	 <label><a class="btn btn-primary btn-sm" id="batchUpdate" ><i class="icon-edit bigger-110"></i>批量更新</a></label>	
            	 <label><a class="btn btn-primary btn-sm" id="allUpdate" ><i class="icon-edit bigger-110"></i>全部更新</a></label>				
				 <label><a class="btn btn-primary btn-sm" id="countWeight"  ><i class="icon-plus"></i>计算重量</a></label>
				 <label><a class="btn btn-primary btn-sm" id="export_out" onclick="return export_out()">导出所有未更新数据</a></label>				
				</div>
				
			 </div>				
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <thead>
    	           <tr>  
    	                <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" /></td> 	           
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">SKU</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">erp重量</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">更新后重量</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">状态</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">计算重量时间</td>       	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">更新重量时间</td>
        	           	<!--<td style="font-size:14px;font-weight:bold;padding:5px;">更新人</td>-->        	           	        	           	
        	           	<!--<td style="font-size:14px;font-weight:bold;padding:5px;">操作</td> -->	           	        		        
    		       </tr>
		       </thead>	
		       <?php foreach($data as $key=>$val):?>	        
		        <tr id="">		        		       
		        <td><input type="checkbox" id="shipmentCost<?php echo $val->id;?>" name="shipmentCost" value="<?php echo $val->id;?>" /></td>		        
		        <td><?php echo $val->sku;?></td>
		        <td><?php echo $val->erp_weight;?></td>
		        <td><?php echo $val->update_weight;?></td>
		        <td><?php echo $statusArr[$val->status];?></td>		        
		        <td><?php echo $val->modify_time;?></td>
		        <td><?php echo $val->update_time;?></td>
		        <!--<td><?php ?></td>
                  <td>   		                          
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
//导出excel
function export_out(id){
	location.href="<?php echo admin_base_url('caiwu/shipmentCost/exceptionWeightExportOut');?>";
}

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


	//计算重量
	$("#countWeight").click(function(){
		if(confirm("确认计算重量？")){
			location.href='<?php echo admin_base_url('caiwu/shipmentCost/countWeight');?>';
		}
	});
	
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
	        url: "<?php echo admin_base_url('caiwu/shipmentCost/ajaxDeleteSkuWeight');?>",  
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


//批量更新
$("#batchUpdate").click(function(){
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

	if(confirm("确认更新？")){
		$.ajax({
	        url: "<?php echo admin_base_url('caiwu/shipmentCost/batchUpdateWeight');?>",  
	        type: "POST",
	        data:{id:id},
	        error: function(){  
	        	showxbtips('失败', 'alert-warning');   
	        },  
	        success: function(data){ 
		        if(data=='更新成功'){  
	        	showxbtips(data, 'alert-warning');
 	        	window.location.href=window.location.href;
		        }else{
			        alert(data);
			        window.location.href=window.location.href;
		        }
	        }
	    });
	}
});


//全部更新
$("#allUpdate").click(function(){
	if(confirm("确认全部更新？")){
		$.ajax({
	        url: "<?php echo admin_base_url('caiwu/shipmentCost/allUpdateWeight');?>",  
	        type: "POST",
	        data:{},
	        error: function(){  
	        	showxbtips('失败', 'alert-warning');   
	        },  
	        success: function(data){   
	        	if(data=='更新成功'){  
		        	showxbtips(data, 'alert-warning');
	 	        	window.location.href=window.location.href;
			    }else{
				    alert(data);
				    window.location.href=window.location.href;
			    }
	        }
	    });
	}
});
</script>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 













