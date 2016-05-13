<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">Wish-自动撤单列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="get">
				     <label>
				            账号:
				       <select name="search[account]" id="account">
				         <option value="">请选择账号</option>
				         <?php foreach($userInfo as $k => $a):?>
				           <option value="<?php echo $k?>">
				            <?php echo $a?>  
				           </option>
				         <?php endforeach;?>
				         
				       </select>
				     </label>
				 	 <label>
						交易ID:
						<input type="text" name="search[transactionId]" placeholder="请输入交易ID" id="transactionId" value="" />
					 </label>
					<label>
						wishID:
						<input type="text" name="search[wishID]" placeholder="请输入wish的id号" id="wishID" value="" />
					</label>
					<label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('order/wish_cancle_order/index');?>">清空</a>
					</label>
				  </form>
				</div>
				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="9%"/>
						<col width="10%"/>
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th>账号</th>
							<th>交易号</th>
							<th>wish订单ID</th>
							<th>内单号</th>
							<th>erp撤单时间</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($data_list as $item):
							
						?>
						<tr>
							<td><?php echo $item->account;?></td>
							<td><?php echo $item->transaction_id;?></td>
							<td><?php echo $item->wish_order_id;?></td>
							<td><?php echo $item->erp_orders_id;?></td>
							<td><?php echo $item->updateTime;?></td>
						</tr>
						<?php
						endforeach;
						?>
					</tbody>
				</table>
			   </form>
				<?php
				
					$this->load->view('admin/common/page');
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#account").val("<?php echo $search['account']?>");
$("#transactionId").val("<?php echo $search['transactionId']?>");
$("#wishID").val("<?php echo $search['wishID']?>");
</script>