<?php
/**
 * 当前月排行榜
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue"><?php echo $currentMonth;?>自动撤销订单查询</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="post">
				  
				  <label>&nbsp;&nbsp;</label>
				  
				  <label>
				  订单类型：
				  <select name="search[orders_type]" style="width: 99px; white-space: nowrap;">
                                <option value="">=全部=</option>
                                <?php
                                foreach ($orders_type as $k => $txt) {
                                    ?>
                                    <option value="<?php echo $k; ?>" <?php if ($search['orders_type'] == $k) {
                                echo 'selected';
                            } ?>><?php echo $txt; ?></option>
                                    <?php
                                }
                                ?>
                            </select>    
                   </label>
                           
                   <label>
						账号:
						<input type="text" name="search[sales_account]" placeholder="请输入账号" value="<?php echo array_key_exists('sales_account', $search) ? $search['sales_account'] : '';?>" id="sales_account" />
					 </label>
					 
					           
				  <label>
						内单号:
						<input type="text" name="search[orderID]" placeholder="请输入内单号" value="<?php echo array_key_exists('orderID', $search) ? $search['orderID'] : '';?>" id="orderID" />
					 </label>
					 
					 <label>
					   撤单时间:<input type="text"  value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="search[start_date]"/>
					</label>
					<label>
					  ~<input type="text" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="search[end_date]"/>
					</label>
					
					<label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					
				  </form>
				  
				</div>

				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="6%">
						<col width="10%"/>
						<col width="10%"/>
						<col width="10%"/>
						<col width="10%"/>
				
					</colgroup>
					<thead>
						<tr>
							<th>订单号</th>
							<th>下单时间</th>
							<th>撤单时间</th>
							<th>订单类型</th>
							<th>账号</th>
						
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($result as $k => $r):
						?>
						<tr>
							<td><?php echo $r->erp_orders_id?></td>
							<td><?php echo $r->orders_export_time?></td>
							<td>
							  <?php echo $r->add_time?>
							</td>
							<td><?php echo isset($orders_type[$r->orders_type])? $orders_type[$r->orders_type] : '';?></td>
							<td><?php 
							if(strpos($r->sales_account, '@') !== false){
								list($a, $b) = explode('@', $r->sales_account);
								echo substr($a, 0, 3).'***@'.$b;
							}else{
								echo $r->sales_account;
							}
							?></td>
						
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