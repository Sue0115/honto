<?php
/**
 * listing模板管理-列表页
 */
?>
<div class="row">
	<div class="col-xs-12">

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
					<div class="col-xs-12">
						<form action="" method="get">
							<label class="p-group">
								产品分组:

								<select name="groupId" id="groupId" style="width: 120px;">
									<option value="">=所有分组=</option>
									<option value="none" <?php echo (isset($groupId) && $groupId == 'none') ? 'selected="selected"': '';?>>未分组</option>
									<?php
									if (!empty($group_list)):
										foreach($group_list as $id => $item){
											echo '<option value="'.$item['group_id'].'" '.(isset($groupId) && $groupId == $item['group_id'] ? 'selected="selected"': '').'>'.$item['group_name'].'</option>';
											if (!empty($item['child'])){

												foreach ($item['child'] as $pid => $row){
													echo '<option value="'.$row['group_id'].'" '.(isset($groupId) && $groupId == $row['group_id'] ? 'selected="selected"': '').'>&nbsp;&nbsp;&nbsp;&nbsp;--'.$row['group_name'].'</option>';
												}
											}
										}
									endif;
									?>
								</select>
							</label>

							<label>
								标题:
								<input type="text" name="subject" placeholder="标题" value="<?php echo isset($subject) ? $subject : '';?>" style="width: 110px;">
							</label>

							<label>
								产品ID:
								<input type="text" name="productId" placeholder="请输入产品ID" value="<?php echo isset($productId) ? $productId : '';?>" style="width: 110px;">
							</label>
							<label>
								SKU:
								<input type="text" name="sku" placeholder="不要输入前后缀" value="<?php echo isset($sku) ? $sku : '';?>" style="width: 110px;">
							</label>

							<label>
								广告状态:
								<select name="productStatusType" id="productStatusType">
									<option value="">--所有状态--</option>
									<?php
									foreach ($smt_product_status as $s):
										echo '<option value="'.$s.'" '.((isset($productStatusType) && $productStatusType == $s) ? 'selected="selected"' : '').'>'.$s.'</option>';
									endforeach;
									echo '<option value="other" '.((isset($productStatusType) && $productStatusType == 'other') ? 'selected="selected"' : '').'>其他</option>';
									?>
								</select>
							</label>
							<label>
								<button class="btn btn-primary btn-sm" type="submit">筛选</button>
								<input type="hidden" name="token_id" value="<?php echo $token_id;?>"/>
							</label>
						</form>
					</div>
				</div>

				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="5%">
						<col width="6%"/>
						<col width="10%">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<th>图片</th>
							<th>产品ID</th>
							<th>标题</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($data_list as $item):
							$imageURLs = array_key_exists($item->productId, $detail_list) ? $detail_list[$item->productId]['imageURLs'] : '';
							$first_image = $imageURLs ? array_shift(explode(';', $imageURLs)) : '';
						?>
						<tr>
							<td class="center">
								<label>
									<input type="checkbox" class="ace product-list" name="productIds[]" value="<?php echo $item->productId.','.$first_image;?>">
									<span class="lbl"></span>
								</label>
							</td>
							<td><a href="http://www.aliexpress.com/item/XXX/<?php echo $item->productId;?>.html" target="_blank"><?php echo $first_image ? '<img src="'.$first_image.'" width="50" height="50" />' : '无图片'?></a></td>
							<td><a href="http://www.aliexpress.com/item/XXX/<?php echo $item->productId;?>.html" target="_blank"><?php echo $item->productId;?></a></td>
							<td><?php echo $item->subject;?></td>
						</tr>
						<?php
						endforeach;
						?>
					</tbody>
				</table>

				<?php
					$this->load->view('admin/common/page_number');
				?>
			</div>
		</div>
	</div>
</div>