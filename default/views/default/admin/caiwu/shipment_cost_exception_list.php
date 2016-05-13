<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">异常物流对账详情</h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="<?php echo admin_base_url('caiwu/shipmentCost/exceptionListShow');?>">	
					    	<label>
                                   <input type="hidden"  name="main_id_search" value="<?php echo isset($main_id_search)?$main_id_search:'';?>" />                                                                        
                            </label>			       
                            <label>
                                                                         挂号码:<input type="text"  name="shipping_code_search" value="<?php echo isset($data['search']['shipping_code_search'])?$data['search']['shipping_code_search']:'';?>" />                                                                         
                            </label>
                            <label>
                            <label>
                                                                         渠道名称:<input type="text"  name="shipment_name_search" value="<?php echo isset($data['search']['shipment_name_search'])?$data['search']['shipment_name_search']:'';?>" />
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
			<div class="row"> 
            	<div class="col-sm-12" style="width:666px;">           	
	 			<label><a class="btn btn-primary btn-sm" id="export_out" onclick="return export_out(<?php echo $main_id_search;?>)">导出重量异常数据</a></label>							 				
				</div>
			 </div>
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <thead>
    	           <tr>  
    	                <!--  <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" /></td>--> 
    	                <td style="font-size:14px;font-weight:bold;padding:5px;">批次号</td>	           
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">挂号码</td>     	    
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">渠道名称</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">异常描述</td> 
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">导入时间</td>       	           	        	           		           	        		        
    		       </tr>
		       </thead>
		        <?php foreach($data['info'] as $key => $val):?>
		        <tr id="<?php echo 'tr'.$val->id;?>">		        		       		       
		        <td><?php echo $val->main_name;?></td>
		        <td><?php echo $val->shipping_code;?></td>		        
		        <td><?php echo $val->shipment_name;?></td>
		        <td><?php echo $val->description;?></td>
		        <td><?php echo $val->import_time;?></td>               
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
	location.href="<?php echo admin_base_url('caiwu/shipmentCost/exceptionExportOut?main_id=');?>"+id+"";
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



// //全选 反选
// function selectAll(){
// 	 var checklist = document.getElementsByName ("exceptionShipmentCost");
// 	   if(document.getElementById("checkAll").checked)
// 	   {
// 	   for(var i=0;i<checklist.length;i++)
// 	   {
// 	      checklist[i].checked = true;
// 	   }
// 	 }else{
// 	  for(var j=0;j<checklist.length;j++)
// 	  {
// 	     checklist[j].checked = 0;
// 	  }
// 	 }
// 	}


</script>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 













