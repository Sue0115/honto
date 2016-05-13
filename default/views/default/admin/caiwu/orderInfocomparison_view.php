<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
      	 <div class="table-header">业会核对结果</div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="" id="export_form">
					     
					        <label>
                              ERP订单号:<input type="text"  name="search[orders_id]" value="<?php echo array_key_exists('orders_id', $search) ? $search['orders_id'] : '';?>" />                                      
                            </label>
                            <label>
                                                                 平台订单号:<input type="text"  name="search[platform_orders_id]" value="<?php echo array_key_exists('platform_orders_id', $search) ? $search['platform_orders_id'] : '';?>" />                                      
                            </label>						                                                             
                            <label>
                                                                         核对时间:<input type="text"  name="search[start_date]" value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_start_date"  />
                            </label>
                            <label>
					           ~<input type="text"  name="search[end_date]" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_end_date" />
					       </label>
					       <br/>
					       发货年月
					   <label>
					        <input type="text"  name="search[ship_date]" value="<?php echo array_key_exists('ship_date', $search) ? $search['ship_date'] : '';?>" datefmt="yyyy-MM" class="Wdate" id="ship_date" />
					   </label>
					       平台类型:
					        <label>
					          <select name="search[orders_type]" id="orders_type">
					            <option value="0">平台类型</option>
					            <?php foreach($orders_type as $ot):?>
					            <option value="<?php echo $ot['typeID']?>"><?php echo $ot['typeName']?></option>
					            <?php endforeach;?>
					          </select>
					        </label>
					               核对状态：
					        <label>
					          <select name="search[data_status]" id="data_status">
					            <option value="0">全部</option>
					            <option value="1">正常状态</option>
					            <option value="2">异常状态</option>
					          </select>
					        </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" id="export_data">
                                  	导出异常结果
                                </button>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" id="batch_update">
                                  	批量更新平台费
                                </button>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" id="updateAll">
                                  	全部更新平台费
                                </button>
                            </label>
                             <label>
							   <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('caiwu/order_receivables_import/orderInfocomparison');?>">清空</a>
						    </label>
                            <input type="hidden" name="select_type" value="1" id="select_type"/>
                        </form> 
					</div>
					&nbsp;&nbsp;&nbsp;<span style="color:green;font-weight:bold;">平台总费用=平台总佣金+平台总联盟佣金+其它</span><br/>
					&nbsp;&nbsp;&nbsp;<span style="color:green;font-weight:bold;">
										压在平台的总金额=平台总订单金额-平台总佣金-平台总联盟佣金-平台总放款金额-平台总退款金额
									  </span>
				</div>
			   
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <colgroup>
						<col width="1%">
						<col width="4%"/>
						<col width="4%"/>
						<col width="8%"/>
						<col width="3%">
						<col width="3%"/>
						<col width="3%"/>
						<col width="3%">
						<col width="3%">
						<col width="3%">
						<col width="3%">
						<col width="3%"/>
						<col width="3%"/>
						<col width="3%"/>
						<col width="5%">
						<col width="5%"/>
						<col width="8%"/>
						<col width="4%"/>
						<col width="4%"/>
						<col width="10%">
						<col width="15%">
						<col width="10%">
						
					</colgroup>
	           <thead>
    	           <tr>  
    	                <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" /></td> 	           
        	            <td style="font-size:13px;font-weight:bold;padding:5px;">ERP订单号</td>
        	            <td style="font-size:13px;font-weight:bold;padding:5px;">平台订单号</td>
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">平台交易号</td>
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">ERP订单总金额</td> 
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">ERP订单总平台佣金</td>
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">平台总订单金额</td>   
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">平台总费用</td>        	           	
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">平台总佣金</td>        	           	
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">平台总联盟佣金</td> 
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">其它</td>
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">平台放款总金额</td>
        	            <td style="font-size:13px;font-weight:bold;padding:5px;">平台退款总金额</td>
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">压在平台的总金额</td>
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">核对时间</td>   
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">订单类型</td>
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">账号</td>        	           	
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">订单发货月份</td> 
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">订单发货年份</td> 
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">导入备注</td>  
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">核对备注</td> 
        	           	<td style="font-size:13px;font-weight:bold;padding:5px;">操作</td>              	        		        
    		       </tr>
		       </thead>
		      <?php foreach($data as $d):?>
		        <tr>
		          <td <?php echo $d->data_status>1 ? 'style="background:red;"' : ''?>>
			          <label>
						 <input type="checkbox" class="ace" name="orders_data[]" value="<?php echo $d->id.'-'.$d->platform_total_fee.'-'.$d->platform_orders_id;?>" >
						 <span class="lbl"></span>
					  </label>
		          </td>
		          <td><?php echo $d->orders_id?></td>
		          <td><?php echo $d->platform_orders_id?></td>
		          <td><?php echo $d->trading_number?></td>
		          <td <?php echo $d->data_status==2 ? 'style="color:red;"' : ''?>><?php echo $d->erp_total?></td>
		          <td <?php echo $d->data_status==3 ? 'style="color:red;"' : ''?>><?php echo $d->erp_fee?></td>
		          <td <?php echo $d->data_status==2 ? 'style="color:red;"' : ''?>><?php echo $d->platform_total?></td>
		          <td <?php echo $d->data_status==3 ? 'style="color:red;"' : ''?>><?php echo $d->platform_total_fee?></td>
		          <td><?php echo $d->platform_fee?></td>
		          <td><?php echo $d->platform_lianmen_fee?></td>
		          <td><?php echo $d->plat_other?></td>
		          <td><?php echo $d->loan_amount?></td>
		          <td><?php echo $d->refund_amount?></td>
		          <td><?php echo $d->residual_amount?></td>
		          <td><?php echo $d->check_time?></td>
		          <td><?php echo $orders_type[$d->orders_type]['typeName']?></td>
		          <td><?php echo $d->sales_account?></td>
		          <td><?php echo $d->mouth_num?></td>
		          <td><?php echo $d->year_num?></td>
		          <td><?php echo $d->note?></td>
		          <td <?php echo $d->data_status>1 ? 'style="color:red;"' : ''?>>
		            <?php echo $resultArr[$d->data_status]?>
		          </td>
		          <td>
		            <a  title="查看详情"  class="Info" data-id="<?php echo $d->orders_id?>" data="<?php echo $d->platform_orders_id?>" style="cursor:pointer;">
                        <span class="green">
                            <i class="icon icon-info-sign"></i>
                        </span>
                    </a>
		          </td>
		        </tr>
		      <?php endforeach;?>
		        <tr>
		          <td colspan="4">总计：</td>
		          <td><?php echo $erp_orders_total?></td>
		          <td><?php echo $erp_plat_total?></td>
		          <td><?php echo $plat_total_order?></td>
		          <td><?php echo $plat_total_fee?></td>
		          <td><?php echo $plat_yong_fee?></td>
		          <td><?php echo $plat_lianmeng_yong_fee?></td>
		          <td><?php echo $other?></td>
		          <td><?php echo $plat_fk_fee?></td>
		          <td><?php echo $plat_tk_fee?></td>
		          <td><?php echo $ya_plat_fee?></td>
		          <td colspan="8"></td>
		        </tr>
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
$("#data_status").val(<?php echo $search['data_status'];?>);
$("#orders_type").val(<?php echo $search['orders_type'];?>);
//查看比对的订单信息和平台信息
$(".Info").click(function(){
	  var id=$(this).attr("data-id");//erp订单号
	  var pid = $(this).attr('data');//平台订单号

	  $.layer({
			type   : 2,
			shade  : [0.4 , '' , true],
			title  : ['查看详情',true],
			iframe : {src : '<?php echo admin_base_url("caiwu/order_receivables_import/orderInfocomparison_detail?id=");?>'+encodeURIComponent(id)+'&pid='+encodeURIComponent(pid)},
			area   : ['950px' , '600px'],
			success : function(){
				layer.shift('top', 400)
			}
		});
		return false;
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
//导出异常数据
$("#export_data").click(function(){
	$("#select_type").val('2');
	$("#export_form").submit();
});
//全部更新平台费
$("#updateAll").click(function(){
	if (confirm('确定要更新所有的平台费吗')){
		$("#select_type").val('3');
		$("#export_form").submit();
    }
	
});

//批量更新平台费
$("#batch_update").click(function(){

	var Ids = $('input[name="orders_data[]"]:checked').map(function() {
		return $(this).val();
	}).get().join(',');
	if (Ids == ''){
		alert('请先选择数据');
		return false;
	}

	if (confirm('确定要更新选中项的平台费吗')){
		$.ajax( {     
			  url:'<?php echo admin_base_url("caiwu/order_receivables_import/update_plat_fee")?>',
			  data:{"pID":Ids,"type":"1"},     
			  type:'post',     
			  async:false,
			  cache:false,     
			  dataType:'json',     
			  success:function(data) {
				  var mesg = '';
				  $.each(data,function(i,val){
					 mesg +=val+'\n';
				  });
				  alert(mesg);
			  }        
		});
	}
	return false;
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


</script>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 













