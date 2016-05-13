<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">仓库操作-异常包裹列表</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">  
             
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup> 
				       <col width="8%">
				       <col width="8%">
                       <col width="8%">
                       <col width="10%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>编号</th>
                            <th>拣货单ID</th>
                            <th>订单ID</th>
                            <th>SKU</th>
                          	<th>异常备注</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                     <?php foreach($data as $va):?>
                       <tr>
                         <td><?php echo $va->id?></td>
                         <td><?php echo $va->pick_id?></td>
                         <td><?php echo $va->orders_id?></td>
                         <td><?php echo $va->product_sku?></td>
                         <td><?php echo $va->note?></td>
                       </tr>
					 <?php endforeach;?>
	                </tbody>
	            </table>
	            
			    <?php 
				 $this->load->view('admin/common/page'); 
				?>
            </div>
        </div>
    </div>
</div>