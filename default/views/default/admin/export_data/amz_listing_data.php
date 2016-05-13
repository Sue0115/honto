
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">上架模版显示栏目数据</h3>		 
        <div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
                            <label>
                             item_sku：<input type="text"  name="item_sku_search" value="<?php  echo isset($item_sku_search)?$item_sku_search:'';?>" />
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                        </form>  
					</div>
				</div>           				 				
		     </div>
				
			 </div>	
				<div class="row"> 
            	<div class="col-sm-12">          	
					 		导出excel:	                        
	                      <select name="site_type" id="site_type" >
						 	  <option value="">站点</option>
		                      <?php foreach($site as $k => $v):?>
		                       <option value="<?php echo $v?>"><?php echo $v;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  
	                 	  <select name="category_type" id="category_type" >
						 	  <option value="">类别</option>
		                      <?php foreach($category as $k => $v):?>
		                       <option value="<?php echo $v;?>"><?php echo $v;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  
	                 	  <select name="img_url" id="img_url" >
						 	  <option value="">帐号</option>
		                      <?php foreach($imgUrl as $k => $v):?>
		                       <option value="<?php echo $k;?>"><?php echo $v;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                      
	                 &nbsp;&nbsp;&nbsp;&nbsp;
		             <label>
						<a class="btn btn-primary btn-sm" id="export">导出上架模版数据</a>
					</label>
					
					<label>
					    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('amz/amzListingTemplate/addEditData')?>">
                            <i class="icon-plus"></i>添加
                        </a>
					</label>
				 
				</div>
			 </div>
	        <div class="row" >
	           <table  class="table table-striped table-bordered table-hover dataTable">
	           <thead>
	           <tr>
	                 <td>		              
					    <input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" />		            
					 </td>		           
    	           <?php foreach ($fields as $key=>$val):?>
    	           <td><?php echo $val;?></td>
    	           <?php endforeach;?>	
    	           <td>操作</td>	        		        
		       </tr>
		       </thead>
		        <?php foreach($data as $key => $val):?>	        
		        <tr id="<?php echo 'tr'.$val['id'];?>">
		        <td><input type="checkbox" name="exportTemplate" value="<?php echo $val['same_product_id'];?>" /></td>
		        <?php foreach ($val as $k=> $v):?>
		        <?php            if(!in_array($k,array('item_sku','item_name','brand_name','standard_price','currency','sale_price','sale_from_date','create_time')))continue;
		         ?>
		        <td id="<?php echo $k.$val['id'];?>"><?php echo $v;?></td>
		        <?php endforeach;?>
		        <td>   
                    <a class="tooltip-success" data-rel="tooltip" title="修改" href="<?php echo admin_base_url('amz/amzListingTemplate/addEditData?ids='.$val['same_product_id']);?>">
                        <span class="green">
                            <i class="icon-edit bigger-110"></i>
                        </span>
                    </a>		                          
		        		        
    		        <a href="javascript:" onclick="return deleteData(<?php echo $val['id'];?>)" class="tooltip-error" data-rel="tooltip" title="删除">
                        <span class="red">
                            <i class="icon-trash bigger-110"></i>
                        </span>
                    </a>
                    
		        </td>
		         </tr>
		        <?php endforeach;?>
		        </table>
		         <?php  $this->load->view('admin/common/page_number');?>
	        </div>
	        <div class="row"><div class="col-xs-10"><span style="color:red;font-weight:bold;"></span></div></div><br/>
		   		       
    </div>
</div>
<script type="text/javascript">
//删除
function deleteData(id){
	if(confirm("确认删除？")){
		$.ajax({
	        url: "<?php echo admin_base_url('amz/amzListingTemplate/ajaxDeleteData');?>",  
	        type: "POST",
	        data:{id:id},
	        error: function(){  
	        	showxbtips('失败', 'alert-warning');   
	        },  
	        success: function(data){   
	        	showxbtips(data, 'alert-warning');
	        	window.location.href=window.location.href;
// 	        	location.reload(); //重新加载页面 
	        }
	    });
	}
	
}

//复制
function copyData(id){
	var length = prompt("请输入需要复制的条数");
	
		$.ajax({
	        url: "<?php echo admin_base_url('amz/amzListingTemplate/ajaxCopyData');?>",  
	        type: "POST",
	        data:{id:id,length:length},
	        error: function(){  
	        	showxbtips('失败', 'alert-warning');   
	        },  
	        success: function(data){   
	        	showxbtips(data, 'alert-warning');
	        	window.location.href=window.location.href;
//  	        	location.reload(); //重新加载页面
	        }
	    });
	
}

//全选 反选
function selectAll(){
	 var checklist = document.getElementsByName ("exportTemplate");
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

//导出excel
$("#export").click(function(){
	var site_type     = $('#site_type').val();//站点
	var category_type = $('#category_type').val();//类目
	var img_url = $('#img_url').val();
	var id='';
// 	var id ='{id:"';
	if(site_type=='' || category_type=='' || img_url==''){
		showxbtips('站点类目和帐号不能为空', 'alert-warning'); return false;
	}

	var selected  = $("input[type='checkbox']").is(':checked');
	if(selected==false){
		showxbtips('请先选中数据', 'alert-warning');return false;
	}
	try{
		var i=0;
     	$('input[name="exportTemplate"]:checked').each(function(){
         	if(i!=0){
             	id +=',';
         	}
     	id += ''+jQuery(this).val()+'';
     	i++;
     		});     	    	
	} catch (e) {	
		return false;
	}

	var url = "<?php echo admin_base_url('amz/amzListingTemplate/exportTemplate');?>";
	    url +="?site_type="+site_type+"&category_type="+category_type+"&img_url="+img_url+"&ids="+id; 
	    location.href=url;

  
	
});
</script>














