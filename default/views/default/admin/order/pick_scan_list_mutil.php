

<?php $is_just_print_order = false;//订单状态是已发货，但是实际上没有出面单的情况。?>


<div class="row">
    <div class="col-xs-12">
    
    <?php if($is_just_print_order){?>
    	<h1 style="background:red;font-size:60px;">警告：当前页面仅使用于漏面单订单扫描,扫描完成后及时关闭</h1>
    	<h3 class="header smaller lighter blue">当前包装作业拣货单：<?php echo $pick['id'].' '.$type_text[$pick['type']]; ?> <span class="btn-lg btn btn-success">今日已包装：<?php echo $total?></span></h3>
    <?php }else{?>
        <h3 class="header smaller lighter blue">当前包装作业拣货单：<?php echo $pick['id'].' '.$type_text[$pick['type']]; ?> <span class="btn-lg btn btn-success">今日已包装：<?php echo $total?></span></h3>
        <a   target="_blank"  style="position:absolute;right:20px;top:20px;color:#000;float:right;" href="<?php echo admin_base_url('order/pick_manage/scan_list?pick_id='.$pick['id'].'&is_just_print_order=1')?>" >漏面单订单SKU扫描</a>
    <?php }?>
    
     
        <div class="table-header" id="topInfo">
            <div id="form_submit">
		        <span>扫描/录入SKU：</span>
		        <input type="hidden" id="pick_id"  name="pick_id" value="<?php echo $pick['id'];?>" />
		        <input type="hidden" id="type"  name="type" value="<?php echo $pick['type'];?>" />
		        <input type="hidden" id="warehouse"  name="warehouse" value="<?php echo $pick['warehouse'];?>" />
		        <input type="text" id="sku"  name="sku" value="" />
		        <input type="hidden" id="last_sku"  name="last_sku" value="" />
		        <input type="hidden" id="basket_num"  name="basket_num" value="<?php echo $basket_num;?>" />
		        
		      	<?php if($no_scan_num <= 50){?>
			        <a class="sendgood" id-data="<?php echo $pick['id'];?>" warehouse-data="<?php echo $pick['warehouse']; ?>">
		               <button class="btn btn-success btn-xs">结束本次包装作业并出库扣库存</button>
			        </a>
	            <?php }?>
	            <a href="/admin/order/print_no_pack/index?pick_id=<?php echo $pick['id'];?>&warehouse=<?php echo $pick['warehouse']; ?>" target="_blank">
		            <button class="btn btn-success btn-xs">打印异常</button>
			    </a>
           </div>
        </div>
		<div class="table-header">
        	sku
        </div>
        <div class="row">
		    <div class="col-xs-12">
		      <table class="table table-striped table-bordered table-hover dataTable">
						    <colgroup>
						       <col width="10%">
						       <col width="30%">
						       <col width="10%"> 
							   <col width="10%">						                       
						    </colgroup>
			                <thead>
			                    <tr>
			                        <th>sku</th>	 
			                        <th>包装方式</th>                  
		                            <th id="scan_num_title">已扫描数</th>
		                            <th>操作</th>
			                    </tr>
			                </thead>
			
			                <tbody id="tbody_content_sku">
		                     
			                </tbody>
			   </table>
		    </div>
		</div>
        <!-- iframe width="100%" height="200px" src="<?php echo admin_base_url('order/pick_manage/show_has_printed_list?pick_id='.$pick['id'].'&warehouse='.$pick['warehouse'])?>"></iframe> -->
        <!-- 显示已包装开始 -->
        <div class="table-header">
       		 已包装且已出库扣库存的订单
        </div>
        
        <div class="row">
		    <div class="col-xs-12">
		       
		        <div class="table-responsive">
		            <div class="dataTables_wrapper">   
						<table class="table table-striped table-bordered table-hover dataTable">
						    <colgroup>
						       <col width="10%">
						       <col width="10%">                 
						    </colgroup>
			                <thead>
			                    <tr>
			                        <th>订单号</th>	                   
		                            <th>操作</th>
			                    </tr>
			                </thead>
			
			                <tbody id="tbody_content">
		                     
			                </tbody>
			            </table>
			         
		            </div>
		        </div>
		    </div>
		</div>
		<!-- 显示已包装结束 -->
        
    </div>
</div>

 <script src="<?php echo $_SERVER['HTTP_HOST'] == '120.24.100.157:72'?  static_url('theme/lodop6194/LodopFuncs.js') : static_url('theme/lodop6194/LodopFuncs_v2.js');?>"></script>

<script>
	$(function(){

		
		get_orders_by_ajax();
		$.ajaxSetup({ 
    		async : false //同步请求
		}); 
		$("#last_sku").val('');//清除上一个sku
		$("#sku").focus();
		$("#sku").attr('disabled',false);
		var LODOP; //声明为全局变量

		$("#sku").change(function(){
			
			var pick_id = $("#pick_id").val();
			var type = $("#type").val();
			var sku = $("#sku").val();
			var last_sku = $("#last_sku").val();
			var warehouse = $("#warehouse").val();
			var sku_pack = $("#"+sku+"_pack").text();//sku 的包装方式
			if(sku==''){
   			  alert('请扫入sku');
   			  return false;
			}

			//判读扫描的sku与上一个sku是否一样，如果一样的话，不请求服务器,否则请求服务器获取包装方式
			if(sku!=last_sku){
				$.ajax( {     
					  url:"<?php echo admin_base_url('order/pick_manage/ajax_get_pack_method')?>",// 跳转到 action     
					  data:{"sku":sku,"warehouse":warehouse},     
					  type:'post',     
					  async:false,
					  cache:false,     
					  dataType:'json',     
					  success:function(data) {
						  sku_pack=data;
					  }        
				});
			}
			//判断该sku是否存在，存在数量加一，不存在则插入该sku
			if($("#"+sku+"_scan_num").text()<1){//不存在
			  var sku_num=1;
			  var item='<tr id="'+sku+'" style="font-size:30px;"><td>'+sku+'</td><td id="'+sku+'_pack">'+sku_pack+'</td><td id="'+sku+'_scan_num">'+sku_num+'</td><td><a class="sku_delete" id-data='+sku+'><button class="btn btn-success">删除</button></a></td></tr>';
			  var skus = '<input type="hidden" class="scan_sku" id="skus_'+sku+'"  value="'+sku+'@1" name="'+sku+'"/>';
			  $("#tbody_content_sku").append(item);
			  $("#form_submit").append(skus);
		    }else{
		      var sku_num = $("#"+sku+"_scan_num").text();
		      sku_num++;
		      $("#"+sku+"_scan_num").text(sku_num);
		      $("#skus_"+sku).val(sku+'@'+sku_num);
			}
			$("#last_sku").val(sku);//插入上一个sku进入隐藏域
			

			$("#sku").val('');
			$("#sku").focus();
		});

	 });

	//页面加载完后使用ajax获取已包装且发货的订单数
	function get_orders_by_ajax(){
		var pick_id = $("#pick_id").val();
		$.ajax( {     
			  url:"<?php echo admin_base_url('order/pick_manage/ajax_get_shipped_order')?>",// 跳转到 action     
			  data:{"pick_id":pick_id},     
			  type:'post',     
			  async:false,
			  cache:false,     
			  dataType:'json',     
			  success:function(data) {
				 $("#tbody_content").html(data);
				 return false;
			  }        
		});
	}

	//点击订单号获取订单详情
	$(document).on('click', '.orderInfo', function(){
		 var orderID = $(this).attr('data-id');
		  var pick_id = $("#pick_id").val();
		  
		  $.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['订单详情',true],
			    shadeClose : true,
			    iframe : {src : '/admin/order/pick_manage/getOrderInfo?orderID='+orderID+'&pick_id='+pick_id},
			    area   : ['800px' , '500px'],
			    success : function(){
	            layer.shift('top', 200)  
	        },
	        yes    : function(index){

	            layer.close(index);
	            move_order();
	        }
		  });
	});
	
	
	//点击打印按钮，打印该订单
	$(document).on('click', '.print', function(){
		var orderID = $(this).attr('data-id');
		var pick_id = $("#pick_id").val();
		var tof = PrintOneURL("<?php echo admin_base_url('print/order_print/orderPrint?id=')?>"+orderID);
		if(!tof){
    		alert("订单："+orderID+"打印失败");
    	}else{
    		location.reload();
    	}
		
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
	
	//点击删除按钮，删除一行
	$(document).on('click', '.sku_delete', function(){
		var sku=$(this).attr('id-data');
		$("#"+sku).remove();
		$("#skus_"+sku).remove();
		//获取最后一行的sku，赋值给#last_sku
		var sku_length = $(".scan_sku").length;
		var last_sku = $(".scan_sku").eq(sku_length-1).attr('name');
		$("#last_sku").val(last_sku);
	});
	
	//监听键盘事件，按回车以后查找扫描的sku和那个篮子能匹配上
	   $(document).keypress(function (event) {
            if (event.keyCode == 46 ) {
            	var pick_id = $("#pick_id").val();
    			var type = $("#type").val();
    			var sku = $("#sku").val();
    			var warehouse = $("#warehouse").val();
    			var basket_num = $("#basket_num").val();
    			
    			//获取隐藏域扫描sku的数量
    			var len = $(".scan_sku").length;
				var post_sku = '';
    		    $(".scan_sku").each(function(){
    		        post_sku += ','+$(this).val();
    		    });
    		    post_sku = post_sku.substring(1);
				layer.load('正在匹配订单中，请勿操作',3);
    		    $.ajax( {     
  				  url:"<?php echo admin_base_url('order/pick_manage/do_scan_mutil')?>",// 跳转到 action     
  				  data:{"data":post_sku,"pick_id":pick_id,"basket":basket_num},     
  				  type:'post',     
  				  async:false,
  				  cache:false,     
  				  dataType:'json',     
  				  success:function(data) {
  					if(data.status==0){
    					layer.alert(data.msg,8);
  	  	  			}else{
  	  	  	  			$("#scan_num_title").text('应扫描数');
  	  	  	  			$("#tbody_content").remove();
  	    	  	  	  	$("#tbody_content").append(data.orderInfo);

  	    	  		
  	    	  	  	  	//标记发货
  	  	    	  	  	$.ajax( {     
  	  	  				  url:"<?php echo admin_base_url('order/pick_manage/ajax_shipping_order')?>",// 跳转到 action     
  	  	  				  data:{"pick_id":pick_id,"warehouse":warehouse,"orders_id":data.orderid,"sku":'MPJ204',"type":'3'},     
  	  	  				  type:'post',     
  	  	  				  async:false,
  	  	  				  cache:false,     
  	  	  				  dataType:'json',     
  	  	  				  success:function(e) {
  	  	  					if(e.status==2){
  	  	  	  					var url = "<?php echo admin_base_url('print/order_print/orderPrint?id=')?>"+data.orderid;
  			                	var tof = PrintOneURL(url);
  			                	if(!tof){
  			                		layer.alert("订单："+data.orderid+"打印失败",8);
  			                	}else{
  			                		location.reload();
  			                	}
  	  	  	  				}else{
  	    	  	  	  			layer.alert(e.info,8);
  	  	  	  	  			}
  	  	  				  }        
  	  	  			   })
  	  	  	  		}
  				  }        
  			   });
    		    layer.close();
            } 
        });
</script>