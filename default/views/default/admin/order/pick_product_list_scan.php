<style>
html { overflow-x:hidden; }
</style>
<div class="row">
<div class="row">
    <div class="col-xs-12">
       
        <div class="table-responsive" style="margin-left:10px;">
            <div class="dataTables_wrapper">   
				
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
	                        <th>订单号</th>	                   
                            <th>SKU名称/规格</th>
                            <th>注意事项</th>  
                            <th>SKU</th>
                            <th>应发数量</th>
                            <th>已扫描数量</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                    <?php if($data_list):?>
                      <?php foreach($data_list as $item):?>
                      <tr>
                        <td><?php echo $item['orders_id']?></td>
                        <td><?php echo $item['products_name_cn']?></td>
                         <td style="font-size:22px;">
                          
                           <?php if(!empty($item['remark'])) {?> 
                           <span style="color:blue;">[订单备注：<?php echo $item['remark']?>]</span><br/>
                           <?php }?>
                           <span style="color:red;"><?php echo $item['products_warring_string']?><br/>
                           <?php echo isset($arrAdapter[$item['adapter']]) ? $arrAdapter[$item['adapter']] : ' ' ?>
                           </span>
                           <span style="color:blue;">[包装方式：<?php echo $item['pack_name']?>]</span>
                         
                         </td>
                        <td><?php echo $item['product_sku']?></td>
                        <td><?php echo $item['product_num']?></td>
                        <td><?php echo $item['scan_num']?></td>     
                      </tr>
                      <?php endforeach;?>
                    <?php endif;?>

	                </tbody>
	            </table>
	         
            </div>
        </div>
    </div>
</div>
