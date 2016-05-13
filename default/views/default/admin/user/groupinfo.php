<?php 
echo ace_header('用户组',$item->gid);

echo ace_form_open('','',array('gid'=>$item->gid));
	$options = array(
					'label_text'=>'组名称',
					'datatype'=>'*', 
					'nullmsg'=>"请输入用户组名称！",
					'errormsg'=>"请输入用户组名称", 
					'help'=>'用户组名称'
					);
	echo ace_input_m($options,'title',$item->title);
?>
            <div class="col-sm-7">
                <div class="widget-box">
                    <div class="widget-header header-color-blue2">
                        <h4 class="lighter smaller">选择权限栏目</h4>
                    </div>

                    <div class="widget-body">
                        <div class="widget-main padding-8">
                            <div id="tree1" class="tree"></div>
                        </div>
                    </div>
                </div>
                <div style="display: none;">
		              <?php foreach($colum_list as $colum):?>
		               <input type="checkbox" value="<?php echo $colum['cid']?>" id="colum_<?php echo $colum['cid']?>" class="colum_item" name="items[]" <?php if(in_array($colum['cid'],$item->array_group)) echo 'checked';?> />
		              <?php endforeach;?>
                </div>
            </div>
		      
		    <div class="clearfix"></div>
		    
<?php 
	echo ace_srbtn('user/group');
echo ace_form_close();
?>

<script src="<?php echo static_url('theme/ace/js/fuelux/data/fuelux.tree-sampledata.js')?>"></script>
<script src="<?php echo static_url('theme/ace/js/fuelux/fuelux.tree.min.js')?>?<?php echo random(3)?>"></script>
<script type="text/javascript">
    jQuery(function($){
    	//$('select').chosen();
    	var colum_items = [];
    	$('input.colum_item').each(function(i){
    	    colum_items[this.value] = this.checked;
    	})
   		function initdata(data){
   			var tree = {};
        	$.each(data,function(index,value){
        		var type = 'folder';
        		if(value.child == null){
       			   type = 'item';
        		}
      			tree[value.title] = {cid:value.cid,name:value.title,checked:colum_items[value.cid],type:type};
      			if(type == 'folder'){
      				tree[value.title]['additionalParameters'] = {};
      				tree[value.title]['additionalParameters']['children'] = initdata(value.child);
      			}
        	})
        	
        	return tree;
   		}
   		var tree_data = initdata(<?php echo json_encode($this->tree->tree)?>);
   		
    	var treeDataSource = new DataSourceTree({data: tree_data});
                    	
        $('#tree1').ace_tree({
            dataSource: treeDataSource ,
            openAll:true,
            loadingHTML:'<div class="tree-loading"><i class="icon-refresh icon-spin blue"></i></div>',
            'open-icon' : 'icon-minus',
            'close-icon' : 'icon-plus',
            'selectable' : true,
            'selected-icon' : 'icon-ok',
            'unselected-icon' : 'icon-remove'
        });
        
        $('#tree1').on('selected', function (evt, data) {
        	$('input.colum_item').prop('checked',false)
            $.each(data.info,function(index,value){
            	$("#colum_"+value.cid).prop('checked',true);
            })
        });
    	
        
	});
</script>