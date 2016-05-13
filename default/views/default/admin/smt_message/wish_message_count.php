<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">WISH-信件统计</h3>
		<div class="table-header">
		   <label>
				<button class="btn btn-success btn-sm" id="custrom_count">客服回信统计</button>
			</label>
		</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="get">
				     
					<label>
					   导入时间:<input type="text"  value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate"  name="search[start_date]"/>
					</label>
					--
					<label>
					   <input type="text"  value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate"  name="search[end_date]"/>
					</label>
					<label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('smt_message/wish_message_center/show_message_count');?>">清空</a>
					</label>
					
				  </form>
				    
				</div>
				<div class="row">
				    <div class="col-sm-5">
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
									
									<th class="center">账号</th>
									<th class="center">未回复</th>
									<th class="center">已回复</th>
									<th class="center">不必回</th>
									<th class="center">已关闭</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($account as $a):?>
								  <tr>
								    <td><?php echo $a['account_name']?></td>
								    <td><?php echo !empty($data[1][$a['account_name']]['num']) ? $data[1][$a['account_name']]['num'] : 0?></td>
								    <td><?php echo !empty($data[2][$a['account_name']]['num']) ? $data[2][$a['account_name']]['num'] : 0?></td>
								    <td><?php echo !empty($data[3][$a['account_name']]['num']) ? $data[3][$a['account_name']]['num'] : 0?></td>
								    <td><?php echo !empty($data[4][$a['account_name']]['num']) ? $data[4][$a['account_name']]['num'] : 0?></td>
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
$("#custrom_count").click(function(){
	$.layer({
	    type   : 2,
	    shade  : [0.8 , '' , true],
	    title  : ['客服回信统计',true],
	    iframe : {src : '/admin/smt_message/wish_message_center/customerReply'},
	    area   : ['800px' , '500px'],
	    success : function(){
            layer.shift('top', 200)  
        },
        yes    : function(index){

            layer.close(index);
            move_order();
        }
	});
});
</script>