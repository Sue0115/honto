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
				       <col width="10%">
				       <col width="5%">                
				       <col width="8%">    
				       <col width="5%">                      
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>订单号</th>	                   
                            <th>SKU名称/规格</th>
                            <th>注意事项</th>  
                            <th>SKU</th>
                            <th>应发数量</th>
                            <th>已扫描数量</th>
                            <th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                    <?php if($data_list):?>
                      <?php foreach($data_list as $key => $item):?>
                      <tr>
                        <td><?php echo $item['orders_id']?></td>
                        <td>
                        	<?php if($item['img']) {?>
                        		<a href="<?php echo $item['img'] ?>"  target="_black"><?php echo $item['products_name_cn']?></a>
                        	<?php }else{?>
                        		<?php echo $item['products_name_cn']?>
                        	<?php }?>
                        </td>
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
                        <td> 
                          
                           <a data_id="<?php echo $item['orders_id']?>" class="printorder">
                              <button class="btn btn-success btn-xs" >打印</button>
	                       </a>
	                     
                        </td>
                      </tr>
                      <?php endforeach;?>
                    <?php endif;?>

	                </tbody>
	            </table>
	         
            </div>
        </div>
    </div>
</div>
 <script src="<?php echo $_SERVER['HTTP_HOST'] == '120.24.100.157:72'?  static_url('theme/lodop6194/LodopFuncs.js') : static_url('theme/lodop6194/LodopFuncs_v2.js');?>"></script>
<script>

$(function(){
	$(".printorder").click(function(){
       var orders_id=$(this).attr("data_id");
       ajaxchange_printed(orders_id);
	});
});
	


function PrintOneURL(url){
	
	var tof = false;

	LODOP=getLodop();  
	LODOP.PRINT_INIT();
	LODOP.SET_PRINT_PAGESIZE();
	LODOP.ADD_PRINT_URL(0,0,"100%","100%",url);
	//LODOP.SET_PRINT_STYLEA(0,"HOrient",3);
	//LODOP.SET_PRINT_STYLEA(0,"VOrient",3);
//	LODOP.SET_SHOW_MODE("MESSAGE_GETING_URL",""); //该语句隐藏进度条或修改提示信息
//	LODOP.SET_SHOW_MODE("MESSAGE_PARSING_URL","");//该语句隐藏进度条或修改提示信息
	tof = LODOP.PRINT();
  //tof = LODOP.PREVIEW();
	return tof;

};

function ajaxchange_printed(orders_id){
	var url = "<?php echo admin_base_url('print/order_print/orderPrint?id=')?>"+orders_id;
	var tof = PrintOneURL(url);
	if(!tof){
		alert("订单："+orders_id+"打印失败");
	}else{
		location.reload();
	}
}	

</script>
