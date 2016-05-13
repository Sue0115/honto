<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">产品管理-出入库统计</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
						    <label>
						    	选择筛选条件
						    </label>
                            
                            <label>
                            	SKU号:
                            	<input id="sku" name="sku" type="text" id="sku" size="15"/>
                            </label>
                            
                            <label>
	                            <select id="matchMethod" name="matchMethod">
									<option value="like" selected="selected">模糊匹配</option>
									<option value="equal">完全匹配</option>
								</select>
							</label>
		
                            <label>
                            	日期从:
                            	 <input type="text" value="" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="start_date" size="11" readonly/>
                            </label>
                            
                            <label>
                            	到:
                            	 <input type="text" value="" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="end_date" size="11" readonly/>
                            </label>
                            <br>
                            
                            <label>
                            	采购(入)
                            	<input name="method[]" type="checkbox" id="method_1" value="1"/>
                            </label>
                            <span>&nbsp;&nbsp;</span>
                            
                            <label>
                            	不发(入)
                            	<input name="method[]" type="checkbox" id="method_2" value="2"/>
                            </label>
                            <span>&nbsp;&nbsp;</span>
                            
                            <label>
                            	 报损(出)
                            	 <input name="method[]" type="checkbox" id="method_3" value="3"/>
                            </label>
                            <span>&nbsp;&nbsp;</span>
                            
                            <label>
                            	 销售(出)
                            	<input name="method[]" type="checkbox" id="method_4" value="4">
                            </label>
                            <span>&nbsp;&nbsp;</span>
                            
                            <label>
                            	调拨(出)
                            	<input name="method[]" type="checkbox" id="method_5" value="5"/>
                            </label>
                            <span>&nbsp;&nbsp;</span>
                            
                            <label>
                            	到货未入
                            	<input name="method[]" type="checkbox" id="method_6" value="6" onClick="change_status(this)"/>
                            </label>
                            <span>&nbsp;&nbsp;</span>
                            
                            <label>
                            	撤单入库
                            	<input name="method[]" type="checkbox" id="method_7" value="7"/>
                            </label>
                            <span>&nbsp;&nbsp;</span>
                            
                            <label>
                            	扫入缺货单（出）
                            	<input name="method[]" type="checkbox" id="method_8" value="8" />
                            </label>
                            
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                            
                            <label>
                            	<input type="reset" value="清空" class="btn btn-danger btn-sm" onclick="empty()">
                            </label>
                        </form>  
					</div>
				</div>
				
				<?php 
				if (!empty($recordList))
				{
				?>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="2%">
				       <col width="10%">
				       <col width="10%">
				       <col width="10%">
				       <col width="5%">
				       <col width="8%">
                       <col width="8%">
                       <col width="10%">
                       <col width="10%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th class="center" >
	                            <label>
	                                <input type="checkbox" class="ace" />
	                                <span class="lbl"></span>
	                            </label>
	                        </th>
	                        <th class="center">单据号</th>
                            <th class="center">产品名称</th>
                            <th class="center">SKU号</th>
                            <th class="center">数量</th>
                            <th class="center">采购单价</th>
                            <th class="center">出/入库人</th>
                            <th class="center">供应商编号</th>
                            <th class="center">时间</th>
                            <th class="center">出入库原因</th>
                            <th class="center">操作方式</th>
	                    </tr>
	                    
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                      <?php foreach($recordList as $k=>$v):?>
                      <tr>
                        <td class="center">
	                            <label>
	                                <input type="checkbox" class="ace" name="ids[]" value="<?php echo $k;?>" />
	                                <span class="lbl"></span>
	                            </label>
	                    </td>
	                    
                        <td><?php echo (empty($v['id'])) ? NULL : $v['id']; ?></td>
                        
                        <td><?php echo (empty($v['cn'])) ? NULL : $v['cn']; ?></td>
                        
                        <td><?php echo (empty($v['sku'])) ? NULL : $v['sku']; ?></td>
                        
                        <td><?php echo (empty($v['count'])) ? NULL : $v['count']; ?></td>
                        
                        <td><?php echo (empty($v['value'])) ? NULL : $v['value']; ?></td>
                        
                        <td><?php echo (empty($v['user_id'])) ? NULL : $v['user_id']; ?></td>
                        
                        <td><?php echo (empty($v['supplierID'])) ? NULL : $v['supplierID']; ?></td>
                        
                        <td><?php echo (empty($v['time'])) ? NULL : $v['time']; ?></td>
                        
                        <td><?php echo (empty($v['reason'])) ? NULL : $v['reason']; ?></td>
                        
                        <td>
                        <?php 
                        if (!empty($v['type']))
                        {
	                        switch ($v['type'])
	                        {
	                        	case 1: echo '<font color="#006600">采购入库</font>';break;
	                        	case 2: echo '<font color="#FF6600">不发入库</font>';break;
	                        	case 3: echo '<font color="#CC0000">报损出库</font>';break;
	                        	case 4: echo '<font color="#CC00FF">销售出库</font>';break;
	                        	case 5: echo '<font color="#CC00FF">调拨出库</font>';break;
	                        	case 6: echo '<font color="#000000">到货未入</font>';break;
	                        	case 7: echo '<font color="#F00">撤单入库</font>';break;
	                        	case 8: echo '<font color="#000000">扫入缺货出</font>';break;
	                        }
                        }
                        ?>
                        </td>
                        
                      </tr>
                      <?php endforeach;?>

	                </tbody>
	            </table>
	            <?php 
		            if($key == 'root' || $key == 'manager'){
		            	$this->load->view('admin/common/page_number');
		            
		            }else{
		            	$this->load->view('admin/common/page');
		            }
				}
	            ?>
                
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
	$("#sku").val("<?php echo $get['sku']; ?>");
	$("#matchMethod").val("<?php echo (empty($get['matchMethod'])) ? 'like' : $get['matchMethod']; ?>");
	$("#start_date").val("<?php echo $get['start_date']; ?>");
	$("#end_date").val("<?php echo $get['end_date']; ?>");

	<?php 
	if (isset($_GET['method']))
	{
		foreach ($_GET['method'] as $v){
			if (!empty($v))
			{
				?>
				$("#method_<?php echo $v; ?>").attr('checked', 'checked');
				<?php 
			}
		}
	}
	?>

	if_checked();
});

function change_status() {
	var is_select = $('#method_6').attr('checked');
	
	if(!is_select){
		for(var i = 1; i<= 8; i++){
			if(i == 6) continue;
			$("#method_"+i).removeAttr('checked');
			$("#method_"+i).attr('disabled', '');
		}

		$('#method_6').attr('checked', '');
		
	}else {
		for(var i = 1; i<= 8; i++){
			if(i == 6) continue;
			$("#method_"+i).removeAttr('disabled');
		}
		
		$('#method_6').removeAttr('checked');
	}
}

function if_checked (){
	var is_select = $('#method_6').attr('checked');
	if(is_select){
		for(var i = 1; i<= 8; i++){
			if(i == 6) continue;
			$("#method_"+i).removeAttr('checked');
			$("#method_"+i).attr('disabled', '');
		}

		$('#method_6').attr('checked', '');
		
	}
}

function empty() {
	for(var i = 1; i<= 8; i++){
		$("#method_"+i).removeAttr('checked');
	}
}
</script>

<script type="text/javascript"> 
	$(function(){ 
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
	}); 
</script>