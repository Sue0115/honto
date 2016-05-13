<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">订单收款导入详情<?php echo $id==0 ? '' : '--'.$import_name_arr[$id]?></h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="">
						     
					        <label>
                                                                           平台订单号:<input type="text"  name="search[erp_buyer_id]" value="<?php echo array_key_exists('erp_buyer_id', $search) ? $search['erp_buyer_id'] : '';?>" />                                   
                            </label>
                            
                            <?php if($id!=0):?>
                            <input type="hidden" name="id" value="<?php echo $id?>" />
                            <?php endif;?>	
                            	                                                             
                            <label>
                                                                         发生时间:<input type="text"  name="search[f_start_date]" value="<?php echo array_key_exists('f_start_date', $search) ? $search['f_start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="f_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="search[f_end_date]" value="<?php echo array_key_exists('f_end_date', $search) ? $search['f_end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="f_end_date" />
					       </label>
					       
					       <label>
                                                                         支付时间:<input type="text"  name="search[z_start_date]" value="<?php echo array_key_exists('z_start_date', $search) ? $search['z_start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="z_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="search[z_end_date]" value="<?php echo array_key_exists('z_end_date', $search) ? $search['z_end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="z_end_date" />
					        </label>
					        
					        <label>
                                                                           账号:<input type="text"  name="search[account]" value="<?php echo array_key_exists('account', $search) ? $search['account'] : '';?>" />
                                                                         
                            </label>
					       
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                             <label>
							   <a class="btn btn-primary btn-sm" href="<?php echo $c_url;?>">清空</a>
						    </label>
                        </form> 
                        
					</div>
				</div>
			
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <colgroup>
						<col width="1%">
						<col width="5%"/>
						<col width="5%"/>
						<col width="6%"/>
						<col width="6%"/>
						<col width="6%"/>
						<col width="8%"/>
						<col width="17%">
						<col width="4%">
						<col width="4%">
						<col width="4%"/>
						<col width="4%"/>
						<col width="4%"/>
						<col width="3%"/>
						<col width="5%">
						<col width="3%">
						<col width="10%">
					</colgroup>
	           <thead>
    	           <tr>  
    	                <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" /></td> 
    	                <td style="font-size:14px;font-weight:bold;padding:5px;">批次号</td>	 
    	                <td style="font-size:14px;font-weight:bold;padding:5px;">账号</td>	           
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">发生时间</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">平台订单号</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">订单支付时间</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">商品ID</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">商品名称</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">订单金额</td>      
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">包含退款金额</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">扣除平台佣金</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">扣除联盟佣金</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">本次放款金额</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">放款币种</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">是否特别放款</td>  
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">保证金冻结比例</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">导入备注</td>       	        		        
    		       </tr>
		       </thead>
		       <?php foreach($data as $item):?>
		         <tr>
		           <td>
		             <input type="checkbox" class="ace" name="orders_data[]" value="<?php echo $item->id;?>" >
					 <span class="lbl"></span>
		           </td>
		           <td><?php echo $import_name_arr[$item->pid]?></td>
		           <td><?php echo $item->account?></td>
		           <td><?php echo $item->times?></td>
		           <td><?php echo $item->erp_buyer_id?></td>
		           <td><?php echo $item->order_paid_time?></td>
		           <td><?php echo $item->products_id?></td>
		           <td><?php echo $item->products_title?></td>
		           <td><?php echo $item->orders_total?></td>
		           <td><?php echo $item->return_amount?></td>
		           <td><?php echo $item->plat_amount?></td>
		           <td><?php echo $item->union_amount?></td>
		           <td><?php echo $item->loan_amount?></td>
		           <td><?php echo $item->currency_type?></td>
		           <td><?php echo $item->is_special_order?></td>
		           <td><?php echo $item->baozheng_rate?></td>
		           <td><?php echo $item->remark?></td>
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













