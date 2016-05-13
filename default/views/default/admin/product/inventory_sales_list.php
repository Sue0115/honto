<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">当前位置-库存销量</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
                            <label>
	                            <select id="products_unreal" name="products_unreal">
									<option value="">=库存=</option>
									<option value="x">负值(欠货)</option>
									<option value="y">无货</option>
									<option value="z">有货</option>
								</select>
							</label>
		
                           <label>
	                            <select name="products_sort" id="products_sort" style="width:120px;" id="productsType">
									<option value="">=分类=</option>
									<?php foreach($categoryArray[0] as $pT){?>
									<option value="<?php echo $pT['category_id'];?>" disabled="disabled" style="font-size:14px; font-weight:bold; color:#0000CC;">--<?php echo str_pad($pT['category_id'],8,'-');?><?php echo $pT['category_name'];?>--</option>
										<?php foreach($categoryArray[$pT['category_id']] as $p){?>
										<option value="<?php echo $p['category_id'];?>">&nbsp;&nbsp;<?php echo str_pad($p['category_id'],8,'-');?><?php echo $p['category_name'];?> (<?php echo isset($productSort[$p['category_id']]) ? $productSort[$p['category_id']] : 0;//$productsManage -> getProductsList(array('productsSort' => $p['category_id']), true);?>)</option>
										<?php }?>
									<?php }?>
								</select>
							</label>
							
							<label>
	                            <select id="products_status_2" name="products_status_2">
									<option value="">=状态=</option>
									<option value="selling">在售</option>
									<option value="sellWaiting">待售</option>
									<option value="stopping">停产</option>
									<option value="saleOutStopping">卖完下架</option>
									<option value="trySale">试销(卖多少采多少)</option>
								</select>
							</label>
							
							<label>
	                            <select id="product_warehouse_id" name="product_warehouse_id">
									<option value="">=所属仓库=</option>
									<option value="1000">深圳一仓</option>
									<option value="1024">广州仓</option>
									<option value="1025">义乌仓</option>
								</select>
							</label>
							<br>
							
							
							<label>
	                            <select id="search_from" name="search_from">
									<option value="products_sku">SKU</option>
									<option value="products_name_cn">中文名</option>
									<option value="products_name_en">英文名</option>
								</select>
							</label>
							<input name="keywd" type="text" id="keywd" value=""/>
                            
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                            
                            <label>
                            	<input type="reset" value="清空" class="btn btn-danger btn-sm" onclick="empty()">
                            </label>
                        </form>  
					</div>
				</div>
				
				<?php 
				if (!empty($list))
				{
				?>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="10%">
				       <col width="10%">
				       <col width="10%">
                       <col width="10%">
                       <col width="10%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th class="center">产品ID</th>
                            <th class="center">产品名称(中文)</th>
                            <th class="center">SKU号</th>
                            <th class="center">所在仓库</th>
                            <th class="center">实库存</th>
                            <th class="center">状态</th>
	                    </tr>
	                    
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                      <?php foreach($list as $k=>$v):?>
                      <tr>
	                    
                        <td><?php echo (empty($v['products_id'])) ? NULL : $v['products_id']; ?></td>
                        
                        <td><?php echo (empty($v['products_name_cn'])) ? NULL : $v['products_name_cn']; ?></td>
                        
                        <td><?php echo (empty($v['products_sku'])) ? NULL : $v['products_sku']; ?></td>
                        
                        <td><?php echo (empty($v['product_warehouse_id'])) ? NULL : $warehouse[$v['product_warehouse_id']]; ?></td>
                        
                        <td>
	                        <a class="see-bind-shipment" data_id="<?php echo $v['products_id']?>">
	                      	  <?php echo (empty($v['actual_stock']) && $v['actual_stock'] !== '0') ? 0 : $v['actual_stock']; ?>
	                        </a>
                        </td>
                        
                        
                        <td>
                        <?php 
                        if (!empty($v['products_status_2']))
                        {
	                        switch ($v['products_status_2'])
	                        {
	                        	case 'selling': echo '在售';break;
	                        	case 'sellWaiting': echo '待售';break;
	                        	case 'stopping': echo '停产';break;
	                        	case 'saleOutStopping': echo '卖完下架';break;
	                        	case 'trySale': echo '试销(卖多少采多少)';break;
	                        }
                        }
                        ?>
                        </td>
                        
                      </tr>
                      <?php endforeach;?>

	                </tbody>
	            </table>
	            <?php 
		            if($key == 'root' || $key == 'manager'){
		            	$this->load->view('admin/common/page_number');
		            
		            }else{
		            	$this->load->view('admin/common/page');
		            }
				}
	            ?>
                
            </div>
        </div>
    </div>
</div>

<script>
$(function(){

	<?php 
	foreach ($get as $k=>$v){
		if (!empty($v))
		{
			?>
			$("#<?php echo $k; ?>").val('<?php echo $v;?>');
			<?php 
		}
	}
	?>

});

$(".see-bind-shipment").click(function(){
	
	var id = $(this).attr('data_id');
	$.layer({
	    type   : 2,
	    shade  : [0.8 , '' , true],
	    title  : ['实库存出入库记录',true],
	    iframe : {src : '/admin/product/Inventory_sales/actual_stock_record?product_id='+id},
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

</script>