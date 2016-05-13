

<?php $is_just_print_order = (int)$_GET['is_just_print_order'];//订单状态是已发货，但是实际上没有出面单的情况。?>

<div class="row">
    <div class="col-xs-12">
    
    <?php if($is_just_print_order){?>
    	<h1 style="background:red;font-size:60px;">警告：当前页面仅使用于漏面单订单扫描,扫描完成后及时关闭</h1>
    	<h3 class="header smaller lighter blue">当前包装作业拣货单：<?php echo $pick['id'].' '.$type_text[$pick['type']]; ?> <span class="btn-lg btn btn-success">今日已包装：<?php echo $total?></span></h3>
    <?php }else{?>
        <h3 class="header smaller lighter blue">当前包装作业拣货单：<?php echo $pick['id'].' '.$type_text[$pick['type']]; ?> <span class="btn-lg btn btn-success">今日已包装：<?php echo $total?></span></h3>
        <a  target="_blank" style="position:absolute;right:20px;top:20px;color:#000;float:right;" href="<?php echo admin_base_url('order/pick_manage/scan_list?pick_id='.$pick['id'].'&is_just_print_order=1')?>" >漏面单订单SKU扫描</a>
    <?php }?>
        
        <div class="table-header">
          
	        <span>扫描/录入SKU：</span>
	        <input type="hidden" id="pick_id"  name="pick_id" value="<?php echo $pick['id'];?>" />
	        <input type="hidden" id="type"  name="type" value="<?php echo $pick['type'];?>" />
	        <input type="hidden" id="warehouse"  name="warehouse" value="<?php echo $pick['warehouse'];?>" />
	        <input type="text" id="sku"  name="sku" value="" />
	      	<?php if($no_scan_num <= 99 && $key == 'manager'){?>
		        <a class="sendgood" id-data="<?php echo $pick['id'];?>" warehouse-data="<?php echo $pick['warehouse']; ?>">
	               <button class="btn btn-success btn-xs">结束本次包装作业并出库扣库存</button>
		        </a>
            <?php }?>
        </div>
		<div class="table-header">
        	 已包装且已出库扣库存的订单
        </div>
        <iframe width="100%" height="200px" src="<?php echo admin_base_url('order/pick_manage/show_has_printed_list?pick_id='.$pick['id'].'&warehouse='.$pick['warehouse'])?>"></iframe>
        
        <div class="table-header">
       		  已扫描的订单
        </div>
        <iframe width="100%" height="100px" src="<?php echo admin_base_url('order/pick_manage/show_has_scan_list?pick_id='.$pick['id'].'&warehouse='.$pick['warehouse'])?>"></iframe>

        
    </div>
</div>

 <script src="<?php echo ($_SERVER['HTTP_HOST'] == '120.24.100.157:72')?  static_url('theme/lodop6194/LodopFuncs.js') : static_url('theme/lodop6194/LodopFuncs_v2.js');?>"></script>

<script>

	$(function(){

		$.ajaxSetup({ 
    		async : false //同步请求
		}); 

		$("#sku").focus();
		$("#sku").attr('disabled',false);
		var LODOP; //声明为全局变量

		$("#sku").change(function(){
			var pick_id = $("#pick_id").val();
			var type = $("#type").val();
			var sku = $("#sku").val();
			var warehouse = $("#warehouse").val();
			
			if(pick_id && sku){
				 $("#sku").attr('disabled',true);



					
					<?php if($is_just_print_order == 1){?>

					$.post("<?php echo admin_base_url('order/pick_manage/checkOrderStatusIsSent')?>",
							{   pick_id: pick_id,
		      					type : type,
				            	sku:   sku
				             }	,
							function(data){
								result = eval(data);

								if(result['status'] == 2){

									var url = "<?php echo admin_base_url('print/order_print/orderPrint?id=')?>"+result['orders_id'];
									var tof = PrintOneURL(url);
							    	if(!tof){
							    		alert("订单："+orders_id+"打印失败");
							    	}else{
							    		location.reload();
							    	}
							    	
								}else{
				                	alert(result['info']);
				                	location.reload();
				                }
				                
						    	
							},"json"
			        );
						
				    	return true;
					<?php }?>
					


				 $.post("<?php echo admin_base_url('order/pick_manage/do_scan')?>",
		            {   pick_id: pick_id,
      					type : type,
		            	sku:   sku
		             }	,
		            function(data){
		            	$("#sku").attr('disabled',false);
		                result = eval(data);
		                if(result['status'] == 1){
			                if(result['can_print']){
			                	ajaxchange_printed(result['orders_id'],pick_id,sku,warehouse);
					        }else{
					        	location.reload();
						    }
		                }else{
		                	alert(result.info);
		                	location.reload();
		                }
		                    
		            },"json"
		            );
			}
			$("#sku").val('');
			$("#sku").focus();
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
//		LODOP.SET_SHOW_MODE("MESSAGE_GETING_URL",""); //该语句隐藏进度条或修改提示信息
//		LODOP.SET_SHOW_MODE("MESSAGE_PARSING_URL","");//该语句隐藏进度条或修改提示信息
		tof = LODOP.PRINT();
		return tof;

	};

	function ajaxchange_printed(orders_id,pick_id,sku,warehouse){


		$.post("<?php echo admin_base_url('order/pick_manage/ajax_shipping_order')?>",
		            { orders_id: orders_id,pick_id: pick_id,sku:sku,warehouse:warehouse}	,
		            function(data){
		                result = eval(data);
		                if(result['status'] == 2){
		                	var url = "<?php echo admin_base_url('print/order_print/orderPrint?id=')?>"+orders_id;
		                	var tof = PrintOneURL(url);
		                	if(!tof){
		                		alert("订单："+orders_id+"打印失败");
		                	}else{
		                		location.reload();
		                	}
		                	
		                }else{
		                	alert(result['info']);
		                	location.reload();
		                }

		            },"json"
		            );
	}	
	
	$(".sendgood").click(function(){
		if(confirm('确定要结束本次包装作业并出库扣库存吗 ？')){
			var pick_id=$(this).attr('id-data');
			var warehouse=$(this).attr('warehouse-data');
			$.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['标记发货',true],
			    iframe : {src : "/admin/order/pick_manage/endPicking?pick_id="+pick_id+"&warehouse="+warehouse},
			    area   : ['800px' , '700px'],
			    success : function(){
	                layer.shift('top', 400)  
	            },
	            close: function(){
					window.location.href='/admin/order/pick_manage/picking';
	            },
	            yes    : function(index){
	                layer.close(index);
	                move_order();
	            }
			});
		}
	})
</script>