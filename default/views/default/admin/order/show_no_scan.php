<table class="table  table-bordered">
				    <colgroup>
				       <col width="30%">
				       <col width="30%">
				       <col width="40%">
				       
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>sku</th>
                            <th>数量</th>
                            <th>储位</th>
	                    </tr>
	                </thead>
	                <tbody id="tbody_content">
	                 <?php foreach($data as $k => $v):?>
		                  <tr>
		                    <td><?php echo $k?></td>
		                    <td><?php echo $v['amount']?></td>
		                    <td><?php echo $v['location']?></td>
		                  </tr>
	                  <?php endforeach;?>
	                </tbody>
</table>