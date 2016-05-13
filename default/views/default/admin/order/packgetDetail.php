<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">查看拣货单-包裹详情-<?php echo $product_status[$status]?></h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
              <div class="row">
              
              </div>
				<table class="table table-striped table-bordered table-hover dataTable">
				   <tr>
				     <td></td>
				     <td>订单号</td>
				     <td>产品名称</td>
				     <td>sku</td>
				     <td>应发货数量</td>
				     <td>已扫描数量</td>
				     <?php if ($status == 4):?>
					     <td>发货时间</td>
					 <?php endif;?>
				     <?php if ($status == 9):?>
					     <td>备注</td>
					 <?php endif;?>
					 <td>页码</td>
				   </tr>
				   <?php
				   		$i=1;
				   		if($status==2)://包裹状态是已扫描的状态
				        foreach($prodcut as  $v):
				    ?>
				    <tr>
				    	 <td><?php echo $i;?></td>
					     <td><?php echo $v['orders_id']?></td>
					     <td><?php echo $v['product_name_cn']?></td>
					     <td><?php echo $v['product_sku']?></td>
					     <td><?php echo $v['product_num']?></td>
					     <td><?php echo $v['scan_num']?></td>
					     <?php if(!empty($v['note'])):?>
					     <td><?php echo $v['note']?></td>
					     <?php endif;?>
				    </tr>
				   <?php 
				   		$i++;
				        endforeach;
				        else:		//包裹状态不是已扫描的状态
				        foreach($prodcut as $key=>$v):
				   ?>
				   <tr>
				   		 <td><?php echo $i;?></td>
					     <td><?php echo $key;?></td>
					     <td>
					       <?php foreach($v as $va):?>
					         <p><?php echo isset($va['product_name_cn']) ? $va['product_name_cn'] : '';?></p>
					       <?php endforeach;?>
					     </td>
					     <td>
					       <?php foreach($v as $va):?>
					         <p><?php echo $va['product_sku']?></p>
					       <?php endforeach;?>
					     </td>
					     <td>
					       <?php foreach($v as $va):?>
					         <p><?php echo $va['product_num']?></p>
					       <?php endforeach;?>
					     </td>
					     <td>
					       <?php foreach($v as $va):?>
					         <p><?php echo $va['scan_num']?></p>
					       <?php endforeach;?>
					     </td>
					     
					     <?php if($status==4):?>
					      <td><?php echo date('Y-m-d H:i',$ship_time);?></td>
					     <?php endif;?>
					     
					     <?php if ($status == 9):?>
					     <td>
					       <?php foreach($v as $va):?>
					         <p><?php echo $va['note']?></p>
					       <?php endforeach;?>
					     </td>
					     <?php endif;?>

					     <td><?php echo $v[0]['page_num'];?></td>
					     
				    </tr>
				    <?php
				    	 $i++;
				    	 endforeach;
				    	 endif;
				    ?>
				   
	            </table>
	    
            </div>
        </div>
    </div>
</div>