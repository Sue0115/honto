<style type="text/css">
td{text-align:center;border-color:#ccc;}
.moneytable td{text-align:right;color:black;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">Hontozer-账单管理</h3>
      	 <div class="table-header" style="font-size:18px;background: #7A8B8B">
      	 <table class="moneytable">
      	 	<tr>
      	 		<td>剩余(RMB):</td>
      	 		<td><?php echo $money['symoney'];?></td>
      	 	</tr>
      	 	<tr>
      	 		<td>已使用(RMB):</td>
      	 		<td><?php echo $money['zcmoney'];?></td>
      	 	</tr>
      	 	<tr>
      	 		<td>总共注入金额(RMB):</td>
      	 		<td><?php echo $money['zrmoney'];?></td>
      	 	</tr>
      	 </table>
      	 </div>
      	   <div class="table-responsive">
             <div class="dataTables_wrapper">
                <div class="row">
					<div class="col-sm-12">		
							
					    <form method="get" action="">
						<label>
							<a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('honto/caiwu/info')?>">
								<i class="icon-plus"></i>
								添加
							</a>
						</label>	
                            <label>
								种类:
								<select name="type" id="token_id">
									<option value="">---全选---</option>
									<option value="1" <?php if(isset($tjdata['type'])){echo($tjdata['type'] =='1')?'selected="selected"': '';}?>>支出</option>
									<option value="2" <?php if(isset($tjdata['type'])){echo($tjdata['type'] =='2')?'selected="selected"': '';}?>>注入</option>
								</select>                                      
                            </label>   
                            <label>
                            	使用时间:<input type="text" name="activetime1" value="<?php echo isset($tjdata['activetime1'])?$tjdata['activetime1']:'';?>" datefmt="yyyy-MM-dd" class="Wdate"/>~<input type="text" name="activetime2" value="<?php echo isset($tjdata['activetime2'])?$tjdata['activetime2']:'';?>" datefmt="yyyy-MM-dd" class="Wdate"/>
                            </label> 
                            <label>
                            	录入时间:<input type="text" name="inputtime1" value="<?php echo isset($tjdata['inputtime1'])?$tjdata['inputtime1']:'';?>" datefmt="yyyy-MM-dd" class="Wdate"/>~<input type="text" name="inputtime2" value="<?php echo isset($tjdata['inputtime2'])?$tjdata['inputtime2']:'';?>" datefmt="yyyy-MM-dd" class="Wdate"/>
                            </label>                                                        
					      
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                               
                                  
                            </label>
                        </form> 
                        
					</div>
				</div>
			<form action="<?php echo admin_base_url('')?>" enctype="multipart/form-data" method="post" id="submitForm">
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

        	            <td style="font-size:14px;font-weight:bold;padding:5px;">用途/备注信息</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">金额</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">种类</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">使用时间</td>
        	            <td style="font-size:14px;font-weight:bold;padding:5px;">操作账号</td> 
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">录入时间</td>
        	           	<td style="font-size:14px;font-weight:bold;padding:5px;">操作</td>         	        		        
    		       </tr>
		       </thead>
		       <?php foreach($data as $key=>$item):?>
		       <tr >
		        <!--  <td>
		           <label>
					 <input type="checkbox" class="ace" name="orders_data[]" value="<?php echo $item->id;?>" >
					 <span class="lbl"></span>
				  </label>
		         </td> -->
		         <td><?php echo $key+1;?></td>
		         <td><?php echo $item->remarks;?></td>
		         <td><?php echo $item->money;?></td>
		         <td style="background:<?php if($item->type == 1){echo '';}elseif($item->type == 2){echo "#5CACEE";} ?>;"><?php if($item->type == 1){echo '支出';}elseif($item->type == 2){echo "注入";} ?></td>
		         <td><?php echo $item->active_time;?></td>
		         <td><?php echo $item->user_name;?></td>
		         <td><?php echo $item->input_time?></td>		         
		         <td>
		         <!-- 	<a href="javascript:void(0)" title="查看详情" data-rel="tooltip"  copyright-id="<?php echo $item->id;?>" class="tooltip-success copyright-detail">
                        <span class="green">
                            <i class="icon icon-info-sign"></i>
                        </span>
                    </a>
                   <a href="javascript:void(0)" title="删除" copyright-id="<?php echo $item->id;?>" data-rel="tooltip" class="tooltip-success copyright-del">
                        <span class="red">
                            <i class="icon icon-trash"></i>
                        </span>
                    </a>  -->
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













