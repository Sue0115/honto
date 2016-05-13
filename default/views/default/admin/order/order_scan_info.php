<table class="table  table-bordered">
				    <colgroup>
				       <col width="30%">
				       <col width="30%">
				       <col width="40%">
				       
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>sku名称</th>
                            <th>应发数</th>
                            <th>已扫描数</th>
	                    </tr>
	                </thead>
	                <tbody id="tbody_content">
	                 <?php foreach($ordersInfo as $v):?>
		                  <tr>
		                    <td><?php echo $v['product_sku']?></td>
		                    <td><?php echo $v['product_num']?></td>
		                    <td><?php echo $v['scan_num']?></td>
		                  </tr>
	                  <?php endforeach;?>
	                </tbody>
</table>