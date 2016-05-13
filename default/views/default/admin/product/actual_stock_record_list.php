<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">当前位置-实库存出入库记录</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
					    
					   		 <label>
                            	日期从:
                            	 <input type="hidden" value="<?php echo $product_id;?>" id="product_id" name="product_id"/>
                            </label>
					    
                            <label>
                            	动作
	                            <select id="operate_type" name="operate_type">
									<option value="">=全部=</option>
									<option value="in">入库</option>
									<option value="out">出库</option>
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
                            
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                            
                            <label>
                            	<input type="reset" value="清空" class="btn btn-danger btn-sm">
                            </label>
                        </form>  
					</div>
				</div>
				
				<?php 
				if (!empty($list))
				{
				?>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="10%">
				       <col width="10%">
				       <col width="10%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th class="center">时间</th>
                            <th class="center">动作</th>
                            <th class="center">数量</th>
                            <th class="center">库存</th>
	                    </tr>
	                    
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                      <?php foreach($list as $k=>$v):?>
                      <tr>
	                    
                        <?php foreach ($v as $key => $value) {	?>
                        	<td>
                        		<?php 
                        			if ($key == 'operate_type') {
                        				echo $text[$value];
                        			}else {
                        				echo $value;
                        			}
                        		?>
                        	</td>
                        <?php } ?>
                        
                     </tr>
                      <?php endforeach;?>

	                </tbody>
	            </table>
	            <?php 
				}
	            ?>
                
            </div>
        </div>
    </div>
</div>

<script>
$(function(){

	<?php 
	foreach ($get as $k=>$v){
		if (!empty($v))
		{
			?>
			$("#<?php echo $k; ?>").val('<?php echo $v;?>');
			<?php 
		}
	}
	?>

});
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