<table class="table  table-bordered">
				    <colgroup>
				       <col width="30%">
				       <col width="30%">
				       <col width="40%">
				       
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>sku</th>
                            <th>应发</th>
                            <th>已扫描</th>
	                    </tr>
	                </thead>
	                <tbody id="tbody_content">
	                 <?php foreach($basketInfo as $v):?>
		                  <tr <?php if($v['product_num']==$v['scan_num']):echo 'style="background:#82AF6F;"';endif;?>>
		                    <td><?php echo $v['product_sku']?></td>
		                    <td><?php echo $v['product_num']?></td>
		                    <td><?php echo $v['scan_num']?></td>
		                  </tr>
	                  <?php endforeach;?>
	                </tbody>
</table>