<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">供应商信息浏览:</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
            		<div class="row">
            			<h4 class="blue">基本信息</h4>
					</div>
					  <table class="table table-bordered">
						  <tr>
						    <th width="70">供应商名称:</th>
						    <td colspan='5'><?php echo $info['suppliers_company'];?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">供应商地址:</th>
						  	<td colspan='5'><?php echo $info['suppliers_address'];?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">联系人:</th>
						    <td><?php echo $info['suppliers_name']; ?></td>
						    
						    <th width="70">联系电话:</th>
						    <td><?php echo $info['suppliers_phone']; ?></td>
						    
						    <th width="70">手机号:</th>
						    <td><?php echo $info['suppliers_mobile']; ?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">传真号:</th>
						    <td><?php echo $info['suppliers_fax']; ?></td>
						    
						    <th width="70">网址:</th>
						    <td><?php echo $info['suppliers_website']; ?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">QQ号:</th>
						    <td><?php echo $info['suppliers_qq']; ?></td>
						    
						    <th width="70">旺旺号 	:</th>
						    <td><?php echo $info['suppliers_wangwang']; ?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">供应商地址退货地址:</th>
						  	<td colspan='5'><?php echo $info['suppliers_return_address'];?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70" colspan='6'></th>
						  </tr>
						  
						  <tr>
						  	<th width="70">开户行:</th>
						  	<td colspan='5'><?php echo $info['suppliers_bank'];?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">银行帐号:</th>
						    <td><?php echo $info['suppliers_card_number']; ?></td>
						    
						    <th width="70">收款人:</th>
						    <td><?php echo $info['suppliers_card_name']; ?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">付款方式:</th>
						  	<?php $pay_methods = payment_method();?>
						    <td>
						    	<?php echo $pay_methods[$info['pay_method']]['text']; ?>
						    </td>
						    
						    <th width="70">物流方式:</th>
						    <td><?php echo $info['ship_method']; ?></td>
						  </tr>
						  
						  <tr>
						  	<th width="70">供货周期:</th>
						  	<?php $arrivalMinDaysArray = defineSuppliersArrivalMinDays();?>
						  	<td colspan='5'>
						  		<?php echo $arrivalMinDaysArray[$info['supplierArrivalMinDays']]['text'];?>
						  		系数:<?php echo $arrivalMinDaysArray[$info['supplierArrivalMinDays']]['modulus'];?>
						  	</td>
						  </tr>
						  
						  <tr>
						  	<th width="70">收款信息:</th>
						  	<td colspan='5'><?php echo $info['gathering_information'];?></td>
						  </tr>
						  
					</table>
            		
            		<div class="row">
            			<h4 class="blue">物品信息</h4>
					</div>
					<table class="table table-bordered">
						<tr bgcolor="#FFFFFF">
							<th>NO.</th>
							<th>SKU</th>
							<th>名称</th>
							<th>售价</th>
							<th>中文申报名</th>
							<th>英文申报名</th>
							<th>申报价值(USD)</th>
							<th>电池</th>
							<th>转接头</th>
							<th>液体</th>
							<th>粉末</th>
							<th>图片源</th>
							<th>备注</th>
						</tr>
						
						<?php 
						if(sizeof($productsArray) > 0)
						{
							$inputCount = 0;
							foreach($productsArray as $rs){
								$inputCount ++;
						?>
						  <tr bgcolor="#FFFFFF">
						    <td align="center"><?php echo $inputCount;?></td>
							<td align="center"><?php echo $rs['products_sku'];?></td>
							<td align="center"><?php echo $rs['products_name_cn'];?></td>
							<td align="center"><?php echo $rs['products_value'];?></td>
							<td align="center"><?php echo $rs['products_declared_cn'];?></td>
							<td align="center"><?php echo $rs['products_declared_en'];?></td>
							<td align="center"><?php echo $rs['products_declared_value'];?></td>
							<td align="center"><?php echo ($rs['products_with_battery'] == 1) ? '是' : '' ;?></td>
							<td align="center"><?php echo ($rs['products_with_adapter'] == 1) ? '是' : '' ;?></td>
							<td align="center"><?php echo ($rs['products_with_fluid'] == 1) ? '是' : '' ;?></td>
							<td align="center"><?php echo ($rs['products_with_powder'] == 1) ? '是' : '' ;?></td>
							<td><?php echo $rs['products_more_img'];?></td>
							<td><?php echo $rs['products_remark_2'];?></td>
						  </tr>
					  <?php }}?>
		  
					</table>
            </div>
        </div>
    </div>
</div>