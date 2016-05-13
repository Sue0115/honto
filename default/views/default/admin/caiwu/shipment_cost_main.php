<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流对账</h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="<?php echo admin_base_url('caiwu/shipmentCost/mainList');?>">
					        <label>
                                                                           批次号:<input type="text"  name="main_name_search" value="<?php echo isset($main_name_search)?$main_name_search:'';?>" />
                                                                         
                            </label>					                                                             
                            <label>
                                                                         导入时间:<input type="text"  name="import_start_date" value="<?php echo isset($import_start_date)?$import_start_date:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="import_end_date" value="<?php echo isset($import_end_date)?$import_end_date:'';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_end_date" />
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
            	<div class="col-sm-12" style="width:666px;">
            	 <form id='submitForm' method='post' enctype="multipart/form-data" action="<?php echo admin_base_url('caiwu/shipmentCost/mainList');?>">            	
            	 <label><input class="btn btn-primary btn-sm" name='excelFile' type='file' id='file' ></label>
            	 <label><input type='hidden' name='add' value='add'></label>
            	 <label><a class="btn btn-primary btn-sm" id="export" >导入数据</a></label>
            	 <label><a  target="_blank" href="<?php echo base_url('attachments/template/shipmentCostTemplate.xls');?>"><span class="w-40-h-20">导入物流费用模版格式</span></a></label>
            	 <label><a class="btn btn-primary btn-sm" id="deleteSelectedAll" >批量删除</a></label>				
				</form>
				 				
				</div>
				
			 </div>	
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <thead>
    	           <tr>  
    	                <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" disabled="disabled"/></td> 	           
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">批次号</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">计费总重量(kg)</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">理论总重量(kg)</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;"><a style="cursor:pointer;" title="通折:((不含挂号费+挂号费)*通折);非通折:((不含挂号费*非通折)+挂号费);">计费总运费(元)</a></td>       	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;"><a style="cursor:pointer;" title="在原理论运费上每单减掉0.35的处理费,然后求和">理论总运费(元)</a></td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">总条数</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;"><a style="cursor:pointer;" title="各渠道:((不含挂号费之和*折扣)/计费重量之和)">各渠道均价(元)</a></td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">导入人</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">导入时间</td>        	           	
        	           	  <td style="font-size:14px;font-weight:bold;padding:5px;">操作</td>            	        		        
    		       </tr>
		       </thead>
		        <?php foreach($data as $key => $val):?>
		        <tr id="<?php echo 'tr'.$val->id;?>">		        		       
		        <td><input type="checkbox" id="shipmentCostMain<?php echo  $val->id;?>" name="shipmentCost" value="<?php echo  $val->id;?>" /></td>		        
		        <td><?php echo $val->main_name;?></td>
		        <td><?php echo $val->total_weight;?></td>
		        <td><?php echo $val->total_packet_weight;?></td>
		        <td><?php echo $val->total_cost;?></td>		        
		        <td><?php echo $val->total_shipping_cost;?></td>
		        <td><?php echo $val->total_num;?></td>
		        <td><?php echo $val->avg_price_str;?></td>
		        <td><?php echo $val->import_users;?></td>
		        <td><?php echo $val->import_time;?></td>
                <td>   		                          
    		        <a class="tooltip-success" data-rel="tooltip" title="查看详情" href="<?php echo admin_base_url('caiwu/shipmentCost/listShow?main_id_search='.$val->id);?>">
                        <span class="green">
                            <i class="icon icon-info-sign"></i>
                        </span>
                    </a>
                    <a class="tooltip-success" data-rel="tooltip" title="查看异常数据详情" href="<?php echo admin_base_url('caiwu/shipmentCost/exceptionListShow?main_id_search='.$val->id);?>">
                        <span class="red">
                            <i class="icon icon-info-sign"></i>
                        </span>
                    </a>
                    
		        </td>
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













