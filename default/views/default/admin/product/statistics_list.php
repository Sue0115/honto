<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script> 
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">产品管理-入库及订单统计</h3>
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
								日期：
                            	<input type="text" value="<?php echo $start_date;?>" class="Wdate" id="end_date" name="start_date" size="11" readonly/>
								到
								<input type="text" value="<?php echo $end_date;?>" class="Wdate" id="end_date" name="end_date" size="11" readonly/>
                            </label>
                            
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                        </form>  
					</div>
				</div>
				
				<?php
				$statistics_type = array(
					'采购入库', '销售出库'
				);
				?>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
						<col/>
						<!--<col width="30%"/>-->
						<col/>
				    </colgroup>
	                <thead>
	                    <tr>
							<th>类型</th>
							<!--<th>时间</th>-->
							<th>小计</th>
	                    </tr>
	                    
	                </thead>
	
	                <tbody id="tbody_content">
					<?php
					foreach ($statistics_type as $type):
						$date = $end_date;
						$recordCount = 0;
						if (!empty($recordList)):
							foreach ($recordList as $row):
								if (isset($row['type']) && $row['type'] == $type && !empty($row['recordCount'])):
									//$date = $row['recordTime'];
									$recordCount = $row['recordCount'];
									break;
								endif;
							endforeach;
						endif;
					?>
					<tr>
						<td><?php echo $type;?></td>
						<!--<td>--><?php //echo $end_date;?><!--</td>-->
						<td><?php echo $recordCount;?></td>
					</tr>
					<?php
					endforeach;
					?>
	                </tbody>
	            </table>
	            <?php
					//$this->load->view('admin/common/page_number');
	            ?>
                <div>
					备注：(截至日期会统计在内)
					<p>1.采购入库：查询时间当天采购入库的所有SKU数量累加之和.</p>
					<p>2.销售出库：查询时间当天发货发货包裹数之和.</p>
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