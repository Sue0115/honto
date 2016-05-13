<style type="text/css">
td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">SMT帐号信息</h3>		 
        <div class="row">
					<div class="col-sm-12">
					    <form method="post" action="">
                            <label>
                             seller_account：<input type="text"  name="seller_account_search" value="<?php echo isset($_REQUEST['seller_account_search'])?$_REQUEST['seller_account_search']:'';?>" />
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                        </form>  
					</div>
				</div>
				
	        <div class="row" >
	           <table border="1" style="border-color:#ccc;margin-left:10px;">
	           <tr>
	                 <td style="width:30px;padding:5px;">		              
					    <input type="checkbox" id="checkAll" name="checkAll" value="" onclick ="return selectAll()" />		            
					 </td>		           
    	           <?php foreach ($fields as $key=>$val):?>
    	           <td style="width:60px;font-size:14px;font-weight:bold;padding:5px;"><?php echo $val;?></td>
    	           <?php endforeach;?>	
    	           <td style="width:60px;font-size:14px;font-weight:bold;">操作</td>	        		        
		       </tr>
		        <?php foreach($data as $key => $val):?>	        
		        <tr id="<?php echo 'tr'.$val['token_id'];?>">
		        <td><input type="checkbox" name="exportTemplate" value="<?php echo $val['token_id'];?>" /></td>
		        <?php foreach ($val as $k=> $v):?>
		        <?php if($k=='token_id')continue; ?>
		        <td id="<?php echo $k.$val['token_id'];?>"><?php echo $v;?></td>
		        <?php endforeach;?>
		        <td style="width:70px;padding:3px;">
    		        <a href="javascript:" onclick="return editData(<?php echo $val['token_id'];?>)" class="tooltip-success" data-rel="tooltip" title="修改">
                        <span class="green">
                            <i class="icon-edit bigger-110"></i>
                        </span>
                    </a>                   
		        </td>
		         </tr>
		        <?php endforeach;?>
		        </table>
	        </div>
	        <div class="row"><div class="col-xs-12"><span style="color:red;font-weight:bold;"></span></div></div><br/>
		   <div class="table-header">  
		   <button type="button" class="btn btn-info"  id="insert">添加</button>
		   </div> 
		   <div class="row">   			  
    		  <form id="form_submit" class="form-horizontal registerform" ajaxpost="ajaxpost" role="form" accept-charset="utf-8" method="post" action="<?php echo admin_base_url('smt/smt_user_tokens/listShow');?>">			
        			<div id="insertData">
        			</div>
        			<div class="col-xs-2">       			
        	        <button class="btn btn-sm btn-primary" type="submit" style="display:none;" id="insertButton">
                              <i>保存</i>
                    </button><input type='hidden' name='add' value='add' />
                    </div>	        
    		</form>
		</div>		       
    </div>
</div>
<script type="text/javascript">

$("#insert").click(function(){
	var input='';
	input +='<div class="row">';
	input +='<?php foreach ($fields as $key=>$val):?><div class="col-xs-2">';
	input +='<input type="text" name="<?php echo $val;?>[]" placeholder="<?php echo $val;?>" value="" />';
	input +='</div><?php endforeach;?>';
	input +='<div class="col-xs-2">';
	input +='<a class="btn btn-success btn-sm del_row">删除</a>';
	input +='</div>';
	input +='</div>';
	$("#insertData").append(input);
    $("#insertButton").css("display","block");	
});
$("#insertButton").click(function(){ 
	try{
     	jQuery("input[name='seller_account[]']").each(function(){
     		if(jQuery(this).val()==''){showxbtips('seller_account不能为空', 'alert alert-warning');throw "wrong";}});
     	jQuery("input[name='member_id[]']").each(function(){
     		if(jQuery(this).val()==''){showxbtips('member_id不能为空', 'alert alert-warning');throw "wrong";}});     	    	
	} catch (e) {	
		return false;
	}$("#form_submit").submit();
});

//删除自定义属性
$(document).on('click', '.del_row', function () {
	$(this).closest('.row').remove();	
});

//修改
function editData(id){
	var fields = <?php echo json_encode($fields);?>;
	var input='';
	input +='<td style="width:30px;padding:5px;"><input type="checkbox"  name="exportTemplate" value="'+id+'" /></td>';
	for(var k in fields ){
		var value = jQuery("#"+fields[k]+id).text();		
		input +='<td><input type="text" id="'+fields[k]+id+'" placeholder="" value="'+value+'" / ></td>';		
	}
		input+='<td><button class="btn btn-sm btn-primary"  id="editButton['+id+']" onclick="editSubmit('+id+')"><i>保存</i></button></td>';
 	$("#tr"+id).children("td").remove();
 	$("#tr"+id).append(input); 	
}

//修改
function editSubmit(id){
	var fields = <?php echo json_encode($fields);?>;
	var str = '{'	; 
	for(var k in fields ){
		var value = jQuery("#"+fields[k]+id).val();
		str += ''+fields[k]+':"'+value+'"';		
		str += ',';		
	}
	str += '"id":"'+id+'"';	
	str += '}';

var input ="";
    var fields = <?php echo json_encode($fields);?>;
    var input='';
    input +='<td style="width:30px;padding:5px;"><input type="checkbox"  name="exportTemplate" value="'+id+'" /></td>';
    for(var k in fields ){
    	var value = jQuery("#"+fields[k]+id).val();		
    	input +='<td id='+fields[k]+id+'>'+value+'</td>';		
    }
    input +='<td style="width:70px;padding:3px;"><a href="javascript:" onclick="return editData('+id+')" class="tooltip-success" data-rel="tooltip" title="修改"><span class="green"><i class="icon-edit bigger-110"></i></span></a>';            	
	$.ajax({
        url: "<?php echo admin_base_url('smt/smt_user_tokens/ajaxEditData');?>",  
        type: "POST",
        data:eval('(' + str + ')'),
        error: function(){  
        	showxbtips('失败', 'alert-warning');   
        },  
        success: function(data){
            	$("#tr"+id).children("td").remove();
             	$("#tr"+id).append(input);   
            	showxbtips(data, 'alert-warning');            
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


</script>














