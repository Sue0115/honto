<?php
/**
 * 当前月排行榜
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue"><?php echo $currentMonth;?>Sku盘点查询</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="?" method="post">
				  
				  <label>&nbsp;&nbsp;</label>
				  <label>
						Sku:
						<input type="text" name="search[products_sku]" placeholder="请输入内单号" value="<?php echo array_key_exists('products_sku', $search) ? $search['products_sku'] : '';?>"  />
					 </label>
					 
					 <label>
					   仓库:
					   <select id="warehouse" name="search[product_warehouse_id]">
                               <option value="">类型</option>
							   <option <?php echo $search[product_warehouse_id]=='1000'? 'selected':'';?> value="1000">深圳仓</option>
							   <option  <?php echo $search[product_warehouse_id]=='1025'? 'selected':'';?> value="1025">义乌仓</option>
						</select>
					   
					</label>
					
					<label>
					  仓位:<input type="text" value="<?php echo array_key_exists('products_location', $search) ? $search['products_location'] : '';?>"  name="search[products_location]"/>
					</label>
					
					<label>
					  产品状态:
					  <select  name="search[productsStauts]">
                					<option   value="">状态</option>
                    				<option  <?php echo $search[productsStauts]=='selling'? 'selected':'';?> value="selling">在售</option>
                                    <option  <?php echo $search[productsStauts]=='sellWaiting'? 'selected':'';?> value="sellWaiting">待售</option>
                                    <option  <?php echo $search[productsStauts]=='stopping'? 'selected':'';?> value="stopping">停产</option>
                                    <option  <?php echo $search[productsStauts]=='saleOutStopping'? 'selected':'';?> value="saleOutStopping">卖完下架</option>
                                    <option  <?php echo $search[productsStauts]=='unSellTemp'? 'selected':'';?> value="unSellTemp">货源待定</option>
                                    <option  <?php echo $search[productsStauts]=='trySale'? 'selected':'';?> value="trySale">试销(卖多少采多少)</option>
                            </select>
                            
					</label>
					<input type="hidden" name="search[is_export]" id="is_export" value="0" />
					<label>
						<button class="btn btn-primary btn-sm" id="btn_query" type="submit">查询</button>
					</label>
					
					<label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button  id="btn_exprot" class="btn btn-primary btn-sm" type="submit">导出</button>
					</label>
					
				  </form>
				  
				</div>

				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="15%">
						<col width="30%"/>
						<col width="00%"/>
						<col width="15%"/>
						<col width="10%"/>
						<col width="10%"/>
						<col width="10%"/>
				
					</colgroup>
					<thead>
						<tr>
							<th>Sku</th>
							<th>中文名</th>
							<th>仓库</th>
							<th>仓位</th>
							<th>实际库存</th>
							<th>销售状态</th>
							<th>重量</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($result as $k => $r):
						?>
						<tr>
							<td><?php echo $r->sku?></td>
							<td><?php echo $r->products_name_cn?></td>
							
							<?php 
							$warehouse_id = $r->stock_warehouse_id;
							$warehouse = '';
							
							if($warehouse_id=='1000'){
								$warehouse = '深圳仓';
							}elseif($warehouse_id=='1025'){
								$warehouse = '义乌仓';
							}
							
							?>   
							<td><?php echo $warehouse;?></td>
							<td><?php echo $r->products_location?></td>
							<td><?php echo $r->actual_stock?></td>
							<td><?php echo $r->sku_status?></td>
							<td><?php echo $r->products_weight?></td>
						</tr>
						<?php
						endforeach;
						?>
					</tbody>
				</table>
			 
			 
			 <div class="row">
	            
	            <div class="col-sm-12">
	                <div class="dataTables_paginate paging_bootstrap">
	                    <ul class="pagination">
	                    <?php echo $page?>
	                    </ul>
	                </div>
	            </div>
	        </div>
        
        
        
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){ 
		$('#btn_query').click(function(){
			$('#is_export').val('0');
		});
		$('#btn_exprot').click(function(){
			$('#is_export').val('1');
		});
		
	}); 
</script>