<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">仓库操作-<?php echo $options_text['title'] ?></h3>
        <div class="table-header">
            <?php if($options_text['type'] == 3){?>
            <form id="scan_form" name="scan_form" method="get" action="<?php echo admin_base_url($c_url.'/scan_list')?>">
	            <span>扫描/录入拣货单单号开始包装作业：</span>
	            <input type="text" id="pick_id"  name="pick_id" value="" />
            </form>	
            <?php }?>
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					<?php if ($key == 'manager' || $key == 'root'){?>	
					   
                    <?php }?>

                    <?php if($options_text['type'] == 3){?>	
						<label>
							正在进行的包装作业	   
						</label>
					<?php }?> 

					</div>
				</div>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="10%">
				       <col width="10%">
				       <col width="15%">
				       <col width="15%">
				       <col width="5%">                
				       <col width="8%">                        
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>拣货单单号</th>	                   
                            <th>类型</th>
                            <th>作业开始时间</th>
                            <th>时长</th>
                            <th>订单进度</th>
                            <th>商品进度</th>    
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    <?php if($data_list):?>
                      <?php foreach($data_list as $item):?>
                      <tr>
                        <td><?php echo $item['id']?></td>
                        <td><?php echo $type_text[$item['type']]?></td>
                        <td><?php echo datetime($item['pick_start_time'])?></td>
                        <td><?php echo $item['time']?></td>
                        <td><?php echo $item['picked_order'].'/'.$item['order_num']?></td>     
                        <td><?php echo $item['picked_product'].'/'.$item['num']?></td>
                      </tr>
                      <?php endforeach;?>
                    <?php endif;?>

	                </tbody>
	            </table>
                
            </div>
        </div>
        <div class="table-header">
          	已完成包装的拣货单
        </div>
        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
				 	   可进行的出库扣库存的拣货单
				 	</div>
				</div>
				<table class="table table-striped table-bordered table-hover dataTable">
				  <colgroup>
				       <col width="10%">
				       <col width="10%">
				       <col width="15%">
				       <col width="15%">
				       <col width="5%">                
				       <col width="8%">                        
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>拣货单单号</th>	                   
                            <th>类型</th>
                            <th>作业开始时间</th>
                            <th>作业结束时间</th>
                            <th>时长</th>
                            <th>操作</th>    
	                    </tr>
	                </thead>
	                <tbody id="tbody_content">
                    <?php if($result):?>
                      <?php foreach($result as $items):?>
                      <tr>
                        <td><?php echo $items['id']?></td>
                        <td><?php echo $type_text[$items['type']]?></td>
                        <td><?php echo datetime($items['pick_start_time'])?></td>
                        <td><?php echo datetime($items['pick_end_time'])?></td> 
                        <td><?php echo $items['time']?></td> 
                        <td>
                          <label>
							    <a class="sendgood" id-data="<?php echo $items['id'];?>" warehouse-data="<?php echo $items['warehouse']; ?>">
                                    	<button class="btn btn-success btn-xs">出库扣库存</button>
	                            </a>
						    </label>
                        </td>
                      </tr>
                      <?php endforeach;?>
                    <?php endif;?>
	                </tbody>
				</table>
			</div>
		</div>
    </div>
</div>
<script>
	$(function(){
		
		$("#pick_id").change(function(){
			var pick_id = $("#pick_id").val();
			if(pick_id){
				document.scan_form.submit();
			}
		});
		
		$(".sendgood").click(function(){
			var pick_id=$(this).attr('id-data');
			var warehouse=$(this).attr('warehouse-data');
			$.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['标记发货',true],
			    iframe : {src : "/admin/order/pick_manage/shipping_order?pick_id="+pick_id+"&warehouse="+warehouse},
			    area   : ['800px' , '700px'],
			    success : function(){
                    layer.shift('top', 400)  
                },
                yes    : function(index){
                    layer.close(index);
                    move_order();
                }
			});
		})

	});
	
</script>