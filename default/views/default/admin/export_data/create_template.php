<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">数据导出—数据模板</h3>
        <form id="form_submit" class="form-horizontal registerform" ajaxpost="ajaxpost" role="form" accept-charset="utf-8" method="post" action="<?php echo admin_base_url('export_data/export_order_data/create_template')?>">
	        <?php foreach($data as $k => $d):?>
	        <div class="table-header">
	            &nbsp;&nbsp;&nbsp;
	            <label>
	                <input type="checkbox" id="ace_<?php echo $k?>" class="ace" />
	                <span class="lbl"></span>
	            </label>
	            <strong><?php echo $infoArr[$k];?></strong>
	        </div>
		        <?php foreach($d as $key => $v):?>
		        <div class="row">
		           <div class="col-xs-1" style="width:30px;"></div>
		           <div class="col-xs-2">
		             <label>
		               <?php if(!empty($templateInfo[1][$v['filed_cn']])){?>
						<input type="checkbox" class="<?php echo $k?>" name="datas_<?php echo $k?>[<?php echo $key?>]" value="<?php echo $v['filed_cn'].'-'.$v['filed_en'].'-'.$v['read_method'].'-'.$v['table_name'];?>" checked/>
					    <?php }else{?>
					    <input type="checkbox" class="<?php echo $k?>" name="datas_<?php echo $k?>[<?php echo $key?>]" value="<?php echo $v['filed_cn'].'-'.$v['filed_en'].'-'.$v['read_method'].'-'.$v['table_name'];?>" />
		               <?php }?>
						<span class="lbl"></span>
					 </label>
		             <?php echo $v['filed_cn']?>
		           </div>
		           
		           <div class="col-xs-2">
		           <?php if(!empty($templateInfo[1][$v['filed_cn']])){?>
		              <input type="text" name="<?php echo $k?>[filed_num][]" placeholder="excel的列号" value="<?php echo $templateInfo[1][$v['filed_cn']][0]?>" />
		           <?php }else{?> 
		              <input type="text" name="<?php echo $k?>[filed_num][]" placeholder="excel的列号" value="" />
		           <?php }?>   
		           </div>
		           <div class="col-xs-3">
		              <?php if(!empty($templateInfo[1][$v['filed_cn']])){?>
		              <input type="text" name="<?php echo $k?>[filed_name][]" placeholder="列别名，不填默认" value="<?php echo $templateInfo[1][$v['filed_cn']][4]?>" />
		              <?php }else{?> 
		              <input type="text" name="<?php echo $k?>[filed_name][]" placeholder="列别名，不填默认" value="" />
		              <?php }?>
		           </div>
		           
		        </div>
		        <?php endforeach;?>
			<?php endforeach;?>
			<div class="row"><div class="col-xs-12"><span style="color:red;font-weight:bold;">*注(订单商品信息的列号应最后填写)</span></div></div><br/>
			<div class="table-header" id="other">
	            &nbsp;&nbsp;&nbsp;
	            <strong>其他</strong>
	            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            <button type="button" class="btn btn-info">添加</button>
	        </div>
	        <?php if(!empty($templateInfo[2])){?>
	          <?php foreach($templateInfo[2] as $ks => $va):?>
				<div class="row">
					<div class="col-xs-2">
					  <input type="text" name="filed_title[]" placeholder="excel列标题" value="<?php echo $va[0]?>" />
					</div>
					<div class="col-xs-2">
					  <input type="text" name="filed_num[]" placeholder="excel的列号" value="<?php echo $ks;?>" />
					</div>
					<div class="col-xs-2">
					  <input type="text" name="filed_value[]" placeholder="默认值" value="<?php echo $va[1]?>" />
					</div>
					<div class="col-xs-2">
					  <a class="btn btn-success btn-sm del_row">删除</a>
					</div>
				</div>
			  <?php endforeach;?>
			<?php }?>
			<br/>
			<div class="row">
			  <div class="col-xs-1 text-right">
			      模板名称：
			  </div>
			  <div class="col-xs-6">
			    <?php if(!empty($templateInfo[0]) && $templateInfo[0]!=''){?>
			    <input type="text" name="template_name" placeholder="输入你要创建的模板名称" value="<?php echo $templateInfo[0]?>"/>
			    <?php }else{?> 
			    <input type="text" name="template_name" placeholder="输入你要创建的模板名称" />
			    <?php }?>
			  </div>
			</div>
			<br/>
			<div class="row">
			  <div class="col-xs-3 center">
			    <label>
					<button class="btn btn-primary btn-sm" id="create_submit">
					  <?php echo !empty($templateInfo) ? '修改模板' : '创建模板';?>
					</button>
				</label>
			  </div>
			</div>
			<input type="hidden" name="modify_id" value="<?php echo !empty($id) ? $id : 0?>" />
		</form>
    </div>
</div>
<script type="text/javascript">
$(function(){
  $('#ace_erp_orders').click(function(){
	  var checked_status = this.checked;
	  $(".erp_orders").each(function () {
		 this.checked = checked_status;
	  });
  });
  $('#ace_erp_orders_products').click(function(){
	  var checked_status = this.checked;
	  $(".erp_orders_products").each(function () {
		 this.checked = checked_status;
	  });
  });
  
  
});
$("#other").click(function(){
	var input='';
	input +='<div class="row">';
	input +='<div class="col-xs-2">';
	input +='<input type="text" name="filed_title[]" placeholder="excel列标题" value="" />';
	input +='</div>';
	input +='<div class="col-xs-2">';
	input +='<input type="text" name="filed_num[]" placeholder="excel的列号" value="" />';
	input +='</div>';
	input +='<div class="col-xs-2">';
	input +='<input type="text" name="filed_value[]" placeholder="默认值" value="" />';
	input +='</div>';
	input +='<div class="col-xs-2">';
	input +='<a class="btn btn-success btn-sm del_row">删除</a>';
	input +='</div>';
	input +='</div>';
	$(this).after(input);
});
$("#create_submit").click(function(){
	$("#form_submit").submit();
});

//删除自定义属性
$(document).on('click', '.del_row', function () {
	$(this).closest('.row').remove();
});
</script>