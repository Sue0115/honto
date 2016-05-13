<?php
/**
 * 当前月排行榜
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue"><?php echo $currentMonth;?>月排行榜</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="post">
				 	选择月份:<input type="text"  value="<?php echo $scanTime;?>" datefmt="yyyy-MM" class="Wdate" id="start_date" name="start_date"/>
					<label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('order/MonthList');?>">清空</a>
					</label>
				  </form>
				  
				</div>

				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="6%">
						<col width="10%"/>
						<col width="10%"/>
						<col width="10%">
						<col width="10%"/>
						<col width="10%">
						<col width="10%"/>
						<col width="10%">
						<col width="10%"/>
					</colgroup>
					<thead>
						<tr>
							<th>姓名</th>
							<th>所属仓库</th>
							<th>昨天发货数</th>
							<th>今天发货数</th>
							<th><?php echo $currentMonth;?>月发货数</th>
							<th><?php echo $currentMonth;?>月总工时</th>
							<th><?php echo $currentMonth;?>月平均发货数</th>
							<th><?php echo $currentMonth;?>月包装错误</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($result as $k => $r):
						?>
						<tr>
							<td><?php echo !empty($userGroup[$k]) ? $userGroup[$k] : ''?></td>
							<td><?php echo !empty($new_user_info[$k]) ? $warehouseArr[$new_user_info[$k]] : ''?></td>
							<td>
							   <?php echo isset($r['lastDay']) ? $r['lastDay'] : 0 ?>
							   <?php echo !empty($r['currentLastProductNum']) ? '<span style="font-weight:bold;">(商品数:'.$r['currentLastProductNum'].')</span>' : ''?>
							</td>
							<td>
							   <?php echo isset($r['currentDay']) ? $r['currentDay'] : 0 ?>
							   <?php echo !empty($r['currentDayProductNum']) ? '<span style="font-weight:bold;">(商品数:'.$r['currentDayProductNum'].')</span>' : ''?>
							</td>
							<td>
							   <?php echo !empty($r['currentMonth']) ? $r['currentMonth'] : 0 ?>
							   <?php echo !empty($r['currentMonthProductNum']) ? '<span style="font-weight:bold;">(商品数:'.$r['currentMonthProductNum'].')</span>' : ''?>
							</td>
							<td><?php echo isset($r['monthTotalTime']) ? $r['monthTotalTime'] : 0 ?></td>
							<td>
							  <?php echo (!empty($r['currentMonth'])&& isset($r['monthTotalTime']) && $r['monthTotalTime']>0) ? '<b style="color:red;">'.ceil($r['currentMonth']/$r['monthTotalTime']).'</b>' : '<b style="color:red;">0</b>' ?>
							  <?php echo (!empty($r['currentMonthProductNum']) && isset($r['monthTotalTime']) && $r['monthTotalTime']>0) ? '<span style="font-weight:bold;">(平均商品数:<b style="color:red;">'.ceil($r['currentMonthProductNum']/$r['monthTotalTime']).'</b>)</span>' : ''?>
							</td>
							<td><?php echo isset($r['monthErrorNum']) ? '<b style="color:red;">'.$r['monthErrorNum'].'</b>' : 0 ?></td>
							<td>
							   <?php if($keys == 'manager'):?>
									<a class="green" data-id = "<?php echo $k;?>" style="cursor:pointer;">
	                                    <i class="icon-pencil bigger-130"></i>
	                                </a>
							   <?php endif;?>
							</td>
						</tr>
						<?php
						endforeach;
						?>
					</tbody>
				</table>
			 
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

		$(".green").click(function(){
			var month = <?php echo $currentMonth;?>;
			var uid = $(this).attr('data-id');
			$.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['更改工时和发货数',true],
			    iframe : {src : '/admin/order/MonthList/UpdateTimeAndNum?uid='+uid+'&month='+month},
			    area   : ['800px' , '500px'],
			    success : function(){
                    layer.shift('top', 200)  
                },
                yes    : function(index){

                    layer.close(index);
                    move_order();
                }
			});
		})
		
	}); 
</script>