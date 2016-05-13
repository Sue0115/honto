<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">包装作业-标记发货</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
              <div class="row">
              
              </div>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup> 
				       <col width="8%">
                       <col width="10%">
                       <col width="15%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>订单号</th>
                            <th>sku</th>
                          	<th>备注</th>
                          	<th>状态</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                      <?php foreach($ordersInfo as $v):?>
                      <?php if($v['status']==9):?>
                       <tr style="color:red;">
                         <td><?php echo $v['orders_id']?></td>
                         <td><?php echo $v['product_sku']?></td>
                         <td><?php echo $v['note']?></td>
                         <td><?php echo $product_status[$v['status']]?></td>
                       </tr>
                       <?php else:?>
                         <tr>
	                         <td><?php echo $v['orders_id']?></td>
	                         <td><?php echo $v['product_sku']?></td>
	                         <td><?php echo $v['note']?></td>
	                         <td><?php echo $product_status[$v['status']]?></td>
                        </tr>
                       <?php endif;?>
                      <?php endforeach;?>
	                </tbody>
	            </table>
				
            </div>
        </div>
    </div>
</div>