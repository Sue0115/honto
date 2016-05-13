<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">amz上架模板</h3>
        <div class="row">
					<div class="col-sm-13">
					    <form method="get" action="">
                            <label>
                             <select name='site' id='site'><option value=''>站点</option><?php foreach ($site as $v):?><option value ='<?php echo $v;?>' <?php echo (isset($siteSearch)&& $siteSearch==$v)?'selected="selected"':''?>><?php echo $v;?></option><?php endforeach;?></select>
                            </label>
                            <label>
                            <select name='category' id='category'><option value=''>类别</option><?php foreach ($category as $v):?><option value ='<?php echo $v;?>' <?php echo (isset($categorySearch)&& $categorySearch==$v)?'selected="selected"':''?>><?php echo $v;?></option><?php endforeach;?></select>                             
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
            	 <form id='submitForm' method='post' enctype="multipart/form-data" action="<?php echo admin_base_url('amz/amzListingTemplate/listingTemplateShow');?>">            	
            	 <label><input class="btn btn-primary btn-sm" name='excelFile' type='file' value='' ></label>
            	 <label><input type='hidden' name='exportSite' id='exportSite' value=''></label>
            	 <label><input type='hidden' name='exportCatagory' id='exportCatagory' value=''></label>
            	 <label><input type='hidden' name='act' value='exportAdd'></label>
          	 
            	 <label><a class="btn btn-primary btn-sm" id="export" >导入数据</a></label> 
            	 <label><a  target="_blank" href="<?php echo base_url('attachments/template/amzListingTemplate.xls');?>"><span class="w-40-h-20">导入上架模版模版excel</span></a></label>     	 				
				</form>
				 				
				</div>
				
			 </div>
		        <div class="row">
		        <table border="1" style="border-color:#ccc;" class="table table-striped table-bordered table-hover dataTable">
		        <thead>
		        <tr><td style="font-size:14px;">站点</td>
		            <td style="font-size:14px;">类别</td>		            
		            <td >操作</td>
		        </tr><thead>
		        <?php foreach($data as $key => $v):?>	        
		        <tr id="<?php echo 'tr'.$v['id'];?>">
		            <td id="<?php echo 'site'.$v['id'];?>"><?php echo $v['site'];?></td>
		            <td id="<?php echo 'category'.$v['id'];?>"><?php echo $v['category'];?></td>
    		        <td><a class="tooltip-success" data-rel="tooltip" title="查看详情" href="<?php echo admin_base_url('amz/amzListingTemplate/listingTemplateShowDetail?site='.$v['site'].'&category='.$v['category']);?>">
                        <span class="green">
                            <i class="icon icon-info-sign"></i>
                        </span>
                    </a></td>
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
     	jQuery("select[name='site']").each(function(){
     		if(jQuery(this).val()==''){showxbtips('站点不能为空', 'alert alert-warning');throw "wrong";}});
     	jQuery("select[name='category']").each(function(){
     		if(jQuery(this).val()==''){showxbtips('类别不能为空', 'alert alert-warning');throw "wrong";}}); 
    	if($("#file").val()==''){ showxbtips('请选择需要导入的excel', 'alert-warning');throw "wrong";}    	
	} catch (e) {	
		return false;
	}
	var site     = $("#site").val();
	var category = $("#category").val();
	 $("#exportSite").val(site);
	 $("#exportCatagory").val(category);
    $("#submitForm").submit();	
});

//重置搜索条件
function resetSearch(){
	$("#site").val("");
	$("#category").val("");
}
</script>



