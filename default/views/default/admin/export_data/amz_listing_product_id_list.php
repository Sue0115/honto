<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">amz上架 Product ID</h3>
        <div class="row">
					<div class="col-sm-13">
					    <form method="get" action="">
                            <label>
                             SKU:<input type='text' name='sku' id='sku' value="<?php echo (isset($sku)&& !empty($sku))?$sku:''?>">
                            </label>
                            <label>
                            Product ID:<input type='text' name='product_id' id='product_id' value="<?php echo (isset($productId)&& !empty($productId))?$productId:''?>">                             
                            </label>
                             
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="">搜索</i>
                                </button>                               
                            </label>
                            <label>
                                <span class="btn btn-primary btn-sm" >
                                       <i class="" onclick='resetSearch()'>重置</i>
                                </span>
                            </label>
                        </form>  
					</div>
				</div>        
		    <div class="row"> 
            	<div class="col-sm-12">
            	 <form id='submitForm' method='post' enctype="multipart/form-data" action="<?php echo admin_base_url('amz/amzListingTemplate/productIdList');?>">            	
            	 <label><input class="btn btn-primary btn-sm" name='excelFile' type='file'  id='file' ></label>
            	 <label><input type='hidden' name='act' value='exportAdd'></label>
            	 <label><a class="btn btn-primary btn-sm" id="export" >导入数据</a></label>      	 				
				</form>
				 				
				</div>
				
			 </div>
		        <div class="row">
		        <table border="1" style="border-color:#ccc;" class="table table-striped table-bordered table-hover dataTable">
		        <thead>
		        <tr><td style="font-size:14px;">SKU</td>
		            <td style="font-size:14px;">product ID</td>
		            <td >使用状态</td>
		            <td >创建人</td><td>创建时间</td><td >修改人</td><td>修改时间</td>
		        </tr><thead>
		        <?php foreach($data as $key => $v):?>	        
		        <tr id="<?php echo 'tr'.$v->id;?>">
		            <td id="<?php echo 'sku'.$v->id;?>"><?php echo $v->sku;?></td>
		            <td id="<?php echo 'productId'.$v->id;?>"><?php echo $v->product_id;?></td>
    		        <td id="<?php echo 'status'.$v->id;?>"><?php echo $status[$v->status];?></td>
    		        <td id="<?php echo 'createUser'.$v->id;?>"><?php echo $v->create_user;?></td>
    		        <td id="<?php echo 'createTime'.$v->id;?>"><?php echo $v->create_time;?></td>
    		        <td id="<?php echo 'modifyUser'.$v->id;?>"><?php echo $v->use_user;?></td>
    		        <td id="<?php echo 'modifyTime'.$v->id;?>"><?php echo $v->use_time;?></td>
		        </tr>
		        <?php endforeach;?>
		        </table>
		        <?php  $this->load->view('admin/common/page_number');?>		           
		    </div>
		  
		     
		  
    </div>
</div>
<script type="text/javascript">
//导入excel
$("#export").click(function(){	
	try{    	     	
    	if($("#file").val()==''){ showxbtips('请选择需要导入的excel', 'alert-warning');throw "wrong";}    	
	} catch (e) {	
		return false;
	}
    $("#submitForm").submit();	
});

//重置搜索条件
function resetSearch(){
	$("#sku").val("");
	$("#product_id").val("");
}
</script>



