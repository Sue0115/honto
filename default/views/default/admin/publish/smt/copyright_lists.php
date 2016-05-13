<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">SMT侵权列表</h3>	
      	 <div class="table-header"> &nbsp;  </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">					
					    <form method="get" action="">
					     
					        <label>
								账号:
								<select name="account" id="token_id">
									<option value="">---全选---</option>
									<?php
									foreach($smtuser as $t):
										echo '<option value="'.$t['accountSuffix'].'" '.($account == $t['accountSuffix'] ? 'selected="selected"': '').'>'.$t['accountSuffix'].'</option>';
									endforeach;
									?>
								</select>                                      
                            </label>	
                            <label>
                            	SKU:<input type="text" name="sku" value="<?php echo isset($tjdata['sku'])?$tjdata['sku']:'';?>"/>
                            </label>
                            <label>
                            	产品ID:<input type="text" name="pro_id" value="<?php echo isset($tjdata['pro_id'])?$tjdata['pro_id']:'';?>"/>
                            </label>	
                            <label>
                            	投诉人:<input type="text" name="complainant" value="<?php echo isset($tjdata['complainant'])?$tjdata['complainant']:'';?>"/>
                            </label>	
                            <label>
                            	商标名:<input type="text" name="trademark" value="<?php echo isset($tjdata['trademark'])?$tjdata['trademark']:'';?>"/>
                            </label>
                            <label>
                            	知识产权编号:<input type="text" name="ip_number" value="<?php echo isset($tjdata['ip_number'])?$tjdata['ip_number']:'';?>"/>
                            </label>	
                            <label>
                            	严重程度:
								<select name="degree" id="token_id">
									<option value="">---全选---</option>
									<option value="是" <?php if(isset($tjdata['degree'])){echo($tjdata['degree'] =='是')?'selected="selected"': '';}?>>是</option>
									<option value="否" <?php if(isset($tjdata['degree'])){echo($tjdata['degree'] =='否')?'selected="selected"': '';}?>>否</option>
								</select> 
                            </label>
                            <label>
                            	违规编号:<input type="text" name="violatos_number" value="<?php echo isset($tjdata['violatos_number'])?$tjdata['violatos_number']:'';?>"/>
                            </label>
                            <label>
                            	违规大类:<input type="text" name="violatos_big_type" value="<?php echo isset($tjdata['violatos_big_type'])?$tjdata['violatos_big_type']:'';?>"/>
                            </label>
                            <label>
                            	违规小类:<input type="text" name="violatos_small_type" value="<?php echo isset($tjdata['violatos_small_type'])?$tjdata['violatos_small_type']:'';?>"/>
                            </label>	
                            <label>
								是否有效:
								<select name="status" id="token_id">
									<option value="">---全选---</option>
									<option value="1" <?php if(isset($tjdata['status'])){echo($tjdata['status'] =='1')?'selected="selected"': '';}?>>有效</option>
									<option value="0" <?php if(isset($tjdata['status'])){echo($tjdata['status'] =='0')?'selected="selected"': '';}?>>无效</option>
								</select>                                      
                            </label>   
                            <label>
                            	分值:<input type="text" name="score1" value="<?php echo isset($tjdata['score1'])?$tjdata['score1']:'';?>"/>~<input type="text" name="score2" value="<?php echo isset($tjdata['score2'])?$tjdata['score2']:'';?>"/>
                            </label>  
                            <label>
                            	销售:<input type="text" name="seller" values="<?php echo isset($tjdata['seller'])?$tjdata['seller']:'';?>"/>
                            </label>                                                        
                            <label>
                                                                       <!--   导入时间:<input type="text"  name="search[start_date]" value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_start_date"  /> -->
                            </label>
                            <label>
					           <!-- <input type="text"  name="search[end_date]" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="import_end_date" /> -->
					       </label>
					      
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                               
                                  
                            </label>
                        </form> 
                        
					</div>
				</div>
			<form action="<?php echo admin_base_url('publish/copyright/deal_data')?>" enctype="multipart/form-data" method="post" id="submitForm">
				<div class="row"> 
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

					</colgroup>
	           <thead>
    	           <tr>  
    	              <!--   <td><input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" /></td>  -->
    	                <td style="font-size:14px;font-weight:bold;padding:5px;">序号</td>	           
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">账号</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">SKU</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">产品广告ID</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">投诉人</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">侵权原因</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">商标名</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">知识产权编号</td>        	           	
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">严重程度</td>  
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">违规编号</td>  
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">违规大类</td> 
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">违规小类</td> 
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">是否有效</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">分值</td>  
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">销售</td> 
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">备注信息</td>           	        		        
    		       </tr>
		       </thead>
		       <?php foreach($data as $key=>$item):?>
		       <tr>
		        <!--  <td>
		           <label>
					 <input type="checkbox" class="ace" name="orders_data[]" value="<?php echo $item->id;?>" >
					 <span class="lbl"></span>
				  </label>
		         </td> -->
		         <td><?php echo $key+1;?></td>
		         <td><?php echo $item->account;?></td>
		         <td><?php echo $item->sku;?></td>
		         <td><?php echo $item->pro_id;?></td>
		         <td><?php echo $item->complainant;?></td>
		         <td><?php echo $item->reason?></td>
		         <td><?php echo $item->trademark;?></td>
		         <td><?php echo $item->ip_number;?></td>
		         <td><?php echo $item->degree;?></td>
		         <td><?php echo $item->violatos_number;?></td>
		         <td><?php echo $item->violatos_big_type ;?></td>
		         <td><?php echo $item->violatos_small_type ;?></td>
		         <td><?php echo $item->status?'有效':'无效';?></td>
		         <td><?php echo $item->score ;?></td>
		         <td><?php echo $item->seller ;?></td>
		         <td><?php echo $item->remarks  ;?></td>
		         <td>
		         	<a href="javascript:void(0)" title="查看详情" data-rel="tooltip"  copyright-id="<?php echo $item->id;?>" class="tooltip-success copyright-detail">
                        <span class="green">
                            <i class="icon icon-info-sign"></i>
                        </span>
                    </a>
                   <a href="javascript:void(0)" title="删除" copyright-id="<?php echo $item->id;?>" data-rel="tooltip" class="tooltip-success copyright-del">
                        <span class="red">
                            <i class="icon icon-trash"></i>
                        </span>
                    </a> 
		         </td>
		       </tr>

		       <?php endforeach;?>
		        </table>

		        <?php if($account){echo '账号有效总分值:'.$addaccount;}?>
		        <?php
					$this->load->view('admin/common/page_number');
				?>
	        </div>
	      </div>
	   </div>	      		       
    </div>
</div>
<script type="text/javascript" src="<?php echo static_url('theme/common/createFullWindowTag-copyright.js');?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo static_url('theme/common/css/style2.css');?>">
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

//查看详情
$(document).on('click', '.copyright-detail', function () {
	var id = $(this).attr('copyright-id');
    $url = '<?php echo admin_base_url("publish/copyright/detail");?>?id='+id;
    createFullWindowTag($url,600,600);
});
//删除侵权信息
$(document).on('click', '.copyright-del', function () {
	var id = $(this).attr('copyright-id');
	if(confirm("确认删除？")){
		$.ajax({
	        url: "<?php echo admin_base_url('publish/copyright/del');?>",  
	        type: "POST",
	        data:{id:id},
	        error: function(){  
	        	showxbtips('失败', 'alert-warning');   
	        },  
	        success: function(data){   
	        	var msg = eval("("+data+")");
	        	alert(msg['info']);
	        	if(msg['status']==1){
	        		//删除成功
	        		location.reload();
	        	}
	        }
	    });
	}
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













