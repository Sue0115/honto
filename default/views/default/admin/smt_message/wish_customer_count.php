<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		
		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="get">
				     
					<label>
					   回复时间:<input type="text"  value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate"  name="search[start_date]"/>
					</label>
					--
					<label>
					   <input type="text"  value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate"  name="search[end_date]"/>
					</label>
					<label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
				
				  </form>
				    
				</div>
				<div class="row">
				    <div class="col-sm-12">
					    <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
							<colgroup>
								<col width="15%">
								<col width="11%"/>
								<col width="11%"/>
								<col width="11%"/>
								<col width="11%"/>
							</colgroup>
							<thead>
								<tr>
									<th class="center">用户名</th>
									<th class="center">已回复</th>
									<th class="center">不必回</th>
									<th class="center">wish support</th>
									<th class="center">已关闭</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($data as $k => $d):?>
								 <tr>
								   <td><?php echo $user[$k]?></td>
								   <td><?php echo isset($d[1])? $d[1] : '0'?></td>
								   <td><?php echo isset($d[2])? $d[2] : '0'?></td>
								   <td><?php echo isset($d[3])? $d[3] : '0'?></td>
								   <td><?php echo isset($d[4])? $d[4] : '0'?></td>
								 </tr>
								<?php endforeach;?>
							</tbody>
						</table>
				    </div>
				</div>
	
				
			
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo site_url('static/theme/common/layer/layer.min.js')?>"></script>
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
	
})

</script>