<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">amz上架模板详情</h3>
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
                             <span style="color:#858585;">title:</span><input type="text"  name="title_search" value="<?php echo isset($title_search)?$title_search:'';?>" />
                            </label>
							<label>
                             <span style="color:#858585;">excel位置:</span><input type="text"  name="rows_search" value="<?php echo isset($rows_search)?$rows_search:'';?>" />
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
		        <table border="1" style="border-color:#ccc;" class="table table-striped table-bordered table-hover dataTable">
		        <thead>
		        <tr><td style="font-size:14px;">站点</td>
		            <td style="font-size:14px;">类别</td>
		            <td >title</td>
		            <td >excel位置</td><td>显示栏目</td><td>parentSku是否显示数据</td><td >操作</td>
		        </tr><thead>
		        <?php foreach($data as $key => $v):?>	        
		        <tr id="<?php echo 'tr'.$v['id'];?>">
		            <td id="<?php echo 'site'.$v['id'];?>"><?php echo $v['site'];?></td>
		            <td id="<?php echo 'category'.$v['id'];?>"><?php echo $v['category'];?></td>
    		        <td style="width:90px;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;" id="<?php echo 'title'.$v['id'];?>"><?php echo $v['title'];?></td>
    		        <td id="<?php echo 'rows'.$v['id'];?>"><?php echo $v['rows'];?></td>
    		        <td id="<?php echo 'relation_field'.$v['id'];?>"><?php echo $v['relation_field'];?></td>
    		        <td id="<?php echo 'parent_show',$v['id'];?>"><?php echo $v['parent_show'];?></td>
    		        <td><span class="green" onclick="return edit(<?php echo $v['id'];?>)"><i class="icon-edit bigger-100" style="cursor:pointer;" ></i></span></td>
		        </tr>
		        <?php endforeach;?>
		        </table>
		        <?php  $this->load->view('admin/common/page_number');?>		           
		    </div>
		    <div class="row"><div class="col-xs-12"><span style="color:red;font-weight:bold;">*注(只有显示在最后一行的title才需要填写显示栏目)</span></div></div><br/>
		   <div class="table-header">  
		   <button type="button" class="btn btn-info"  id="other">添加</button>
		   </div>   
		  <form id="form_submit" class="form-horizontal registerform" ajaxpost="ajaxpost" role="form" accept-charset="utf-8" method="post" action="<?php echo admin_base_url('amz/amzListingTemplate/listingTemplateShowDetail');?>">			
    			<div id="insert">   	                	                	            
    	        </div>
    	        <span id='selectType' style="display:none;"><select name='site'><option value=''>站点</option><?php foreach ($site as $v):?><option value ='<?php echo $v;?>'><?php echo $v;?></option><?php endforeach;?></select>
    	        <select name='category'><option value=''>类别</option><?php foreach ($category as $v):?><option value ='<?php echo $v;?>'><?php echo $v;?></option><?php endforeach;?></select></span>
    	        <button class="btn btn-sm btn-primary" type="submit" style="display:none;" id="insertButton">
                          <i>保存</i>
                    </button>	        
		</form>
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


$("#other").click(function(){
	var input='';
	input +='<div class="row">';
	
	input +='<div class="col-xs-2">';
	input +='<input type="text" name="insertTitle[]" placeholder="title" value="" />';
	input +='</div>';
	input +='<div class="col-xs-2">';
	input +="<span style='color:#858585;'>excel位置</span><input type='text' name='insertRows[]'><option value=''>";		    	
	input +='</div>';
	input +='<div class="col-xs-2">';
	input +="<td><select name='insert_relation_field[]'><option value=''>显示栏目</option><?php foreach ($fields as $v):?><option value ='<?php echo $v;?>'><?php echo $v;?></option><?php endforeach;?></select></td>";		
	input +='</div>';
	input +='<div class="col-xs-2">';
	input +="<td><select name='insert_parent_show[]'><option value=''>parentSku是否显示数据</option><option value=''>请选择</option><option value ='不显示'>不显示</option></select></td>";		
	input +='</div>';
	input +='<div class="col-xs-2">';
	input +='<a class="btn btn-success btn-sm del_row">删除</a>';
	input +='</div>';
	input +='</div>';
	$("#insert").append(input);
    $("#insertButton").css("display","block");
    $("#selectType").css("display","block");	
    
});
$("#insertButton").click(function(){ 
	try{    	
     	jQuery("input[name='insertTitle[]']").each(function(){
     	 	if(jQuery(this).val()==''){ showxbtips('title不能为空', 'alert alert-warning');throw "wrong";}});
     	jQuery("input[name='insertRows[]']").each(function(){
     		if(jQuery(this).val()==''){showxbtips('excel位置不能为空', 'alert alert-warning');throw "wrong";}});
     	jQuery("select[name='site']").each(function(){
     		if(jQuery(this).val()==''){showxbtips('站点不能为空', 'alert alert-warning');throw "wrong";}});
     	jQuery("select[name='category']").each(function(){
     		if(jQuery(this).val()==''){showxbtips('类别不能为空', 'alert alert-warning');throw "wrong";}});
     	
	} catch (e) {	
		return false;
	}$("#form_submit").submit();
});

//删除自定义属性
$(document).on('click', '.del_row', function () {
	$(this).closest('.row').remove();	
});

//修改
function edit(id){
	var editSite          = jQuery("#site"+id).text();
	var editCategory      = jQuery("#category"+id).text();
	var editTitle         = jQuery("#title"+id).text();
	var editRows          = jQuery("#rows"+id).text();
	var editRelationField = jQuery("#relation_field"+id).text();
	var parent_show = jQuery("#parent_show"+id).text();	
	var input='';
	input +="<td><select id='editSite"+id+"'><option value="+editSite+" selected='selected'>"+editSite+"</option><?php foreach ($site as $v):?><option value ='<?php echo $v;?>' ><?php echo $v;?></option><?php endforeach;?></select></td>";		    
	input +="<td><select id='editCategory"+id+"'><option value="+editCategory+" selected='selected'>"+editCategory+"</option><?php foreach ($category as $v):?><option value ='<?php echo $v;?>' ><?php echo $v;?></option><?php endforeach;?></select></td></td>"
	input +='<td><input type="text" id="editTitle'+id+'" placeholder="" value='+editTitle+'></td>';
	input +="<td><input type='text' id='editRows"+id+"' value="+editRows+"></td>";		    	
	input +="<td><select id='edit_relation_field"+id+"'><option value="+editRelationField+">"+editRelationField+"</option><option value=''>请选择</option><?php foreach ($fields as $v):?><option value ='<?php echo $v;?>'><?php echo $v;?></option><?php endforeach;?></select></td>";
	input +="<td><select id='edit_parent_show"+id+"'><option value="+parent_show+">"+parent_show+"</option><option value=''>请选择</option><option value ='不显示'>不显示</option></select></td>";
	input+='<td><button class="btn btn-sm btn-primary"  id="editButton['+id+']" onclick="editSubmit('+id+')"><i>保存</i></button></td>';
 	$("#tr"+id).children("td").remove();
 	$("#tr"+id).append(input); 	


// 	alert(id);return false;
}

function editSubmit(id){
	var editSite            = jQuery("#editSite"+id+"").val();
	var editCategory        = jQuery("#editCategory"+id+"").val();
	var editTitle           = jQuery("#editTitle"+id+"").val();
	var editRows            = jQuery("#editRows"+id+"").val();	
	var edit_relation_field = jQuery("#edit_relation_field"+id+"").val(); 
	var edit_parent_show    = jQuery("#edit_parent_show"+id+"").val();
	var input="<td id='site"+id+"'>"+editSite+"</td><td id='category"+id+"'>"+editCategory+"</td><td id='title"+id+"'>"+editTitle+"</td><td id='rows"+id+"'>"+editRows+"</td><td id='relation_field"+id+"'>"+edit_relation_field+"</td><td id='parent_show"+id+"'>"+edit_parent_show+"</td><td><span class='green' onclick='return edit("+id+")'><i class='icon-edit bigger-100' style='cursor:pointer;' ></i></span></td>";
    $.ajax({
        url: "<?php echo admin_base_url('amz/amzListingTemplate/ajaxEdit');?>",  
        type: "POST",
        data:{editSite: editSite,editCategory:editCategory,editTitle:editTitle,editRows:editRows,edit_relation_field:edit_relation_field,id:id,edit_parent_show:edit_parent_show },
        error: function(){ 
        	$("#tr"+id).children("td").remove();
         	$("#tr"+id).append(input); 
        	showxbtips('失败', 'alert-warning');   
        },  
        success: function(data){ 
        	$("#tr"+id).children("td").remove();
         	$("#tr"+id).append(input);  
        	showxbtips(data, 'alert-warning');
        	 
        }
    });
}

//重置搜索条件
function resetSearch(){
	$("#site").val("");
	$("#category").val("");
}
</script>



