<style>
#detail{
	width:100%;
    height:auto;
}
a:hover{
	cursor:pointer;
}
.left{
	width:69%;
	float:left;
}
.address{
	border-bottom:1px dotted #ccc;
	padding-bottom:5px;
}
.buyerOperate{
	width:100%;
}
p{
	text-align:right;
}
p span{
	font-weight:bold;
}
.right{
	width:30%;
	height:auto;
	float:right;
}
table{
	border:1px solid #ccc;
}

</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">订单管理-全部订单</h3>
        <div class="table-header">
            &nbsp;
        </div>
        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
					        <label>
                         		    内单号:<input type="text"  name="search[erp_orders_id]" value="<?php echo $search['erp_orders_id']?>" size="20"/>
                            </label>
					  	    <label>
                         		    sku:<input type="text"  name="search[orders_sku]" value="" size="20"/>
                            </label>
                            <label>
                         		       买家ID/E-mail:<input type="text"  name="search[buyer_id]" value="<?php echo $search['buyer_id']?>" size="20"/>
                            </label>
                            <label>
                         		       销售账号:<input type="text"  name="search[seller_account]" value="<?php echo $search['seller_account']?>" size="20"/>
                            </label>
                            <label>
                         		   收货人:<input type="text"  name="search[buyer_name]" value="<?php echo $search['buyer_name']?>" size="20"/>
                            </label>
					  	    <label>
                         		    地址:<input type="text"  name="search[buyer_address]" value="<?php echo $search['buyer_address']?>" size="20"/>
                            </label>
                            <label>
                         		      交易号:<input type="text"  name="search[pay_id]" value="<?php echo $search['pay_id']?>" size="20"/>
                            </label>
                            <label>
                            <select name="search[orders_status]" id="orders_status" >
                               <option value="">全部状态</option>
                               <?php foreach($order_status as $key => $value):?>
                                 <option value="<?php echo $key;?>"><?php echo $value;?></option>
                               <?php endforeach;?>
                            </select>
                            </label>
                            <label>
                            <select name="search[orders_type]" id="orders_type" >
                               <option value="">订单类型</option>
                               <?php foreach($order_type as $ke => $v):?>
                                 <option value="<?php echo $ke?>"><?php echo $v;?></option>
                               <?php endforeach;?>
                            </select>
                            </label>
                            <label>
                            <select name="search[warehouse]" id="warehouse" >
                               <option value="">所属仓库</option>
                               <?php foreach($warehouse as $k=>$v):?>
                                <option value="<?php echo $k;?>"><?php echo $v;?></option>
                               <?php endforeach;?>
                            </select>
                            </label>
                            <label>
                            <select name="search[isBackOrder]" id="isBackOrder">
					            <option value="">全部</option>
					            <option value="0" selected="">不欠货</option>
					            <option value="1">欠货</option>
					        </select>
                            </label>
                            <label>
                            <select name="search[showType]" id="showType">
					            <option value=""  selected="">显示方式</option>
					            <option value="some">简洁</option>
					            <option value="all">全部</option>
					        </select>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                        </form>  
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover dataTable">
				   <colgroup>
				       <col width="1%">
				       <col width="6%">
				       <col width="5%">
				       <col width="8%">
				       <col width="12%">                
				       <col width="5%">                        
				       <col width="8%">
                       <col width="8%">
                       <col width="5%">
                       <col width="10%">
				       <col width="4%">                
				       <col width="5%">                        
				       <col width="9%">
                       <col width="8%">
                       <col width="6%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>选择</th>	                   
                            <th>订单号-内</th>
                            <th>类型</th>
                            <th>买家ID/E-mail</th>
                            <th>物流</th>                            
                            <th>所属仓库</th>             
                            <th>收货人</th>
                            <th>国家</th>
                            <th>总金额(运费)</th>
                            <th>销售帐号</th>
                            <th>状态</th>
                            <th>客服</th>
                            <th>时间</th>
                            <th>挂号码</th>
                            <th>详情</th>
	                    </tr>
	                </thead>
				<?php foreach($data_list as $kee => $v):?>
	               <tbody id="tbody_content">
                     <tr>
                       <td><input type="checkbox" name="sID[]"/></td>
                       <td><?php echo $v['erp_orders_id']?></td>
                       <td><?php echo $order_type[$v['orders_type']]?></td>
                       <td><?php echo $v['buyer_id']?></td>
                       <td><?php echo isset($shipemntInfo[$v['shipmentAutoMatched']])? $shipemntInfo[$v['shipmentAutoMatched']] : '' ;?></td>
                       <td><?php echo isset($warehouse[$v['orders_warehouse_id']])? $warehouse[$v['orders_warehouse_id']] : '' ;?></td>
                       <td><?php echo $v['buyer_name']?></td>
                       <td><?php echo $v['buyer_country']?></td>
                       <td><?php echo $v['currency_type'].' '.$v['orderTotalFee']?></td>
                       <td><?php echo $v['seller_name']?></td>
                       <td><?php echo $order_status[$v['orders_status']]?></td>
                       <td><?php echo isset($nameArr[$v['erp_user_id']])? $nameArr[$v['erp_user_id']] : '' ?></td>
                       <td><?php echo $v['orders_export_time']?></td>
                       <td><?php echo $v['orders_shipping_code']?></td>
                       <td>
                       		 <a class="shouqi" name="<?php echo $kee+1;?>">收起</a>
                       		 <a class="order_log" data_id="<?php echo $v['erp_orders_id']?>">
	                           <img src="<?php echo site_url('attachments/images/shipment/log.gif')?>" />
	                         </a>
                       </td>
                     </tr>
					 <tr class="no<?php echo $kee+1;?>" name="orderInfo">
					   <td colspan="15">
					     <div id="detail">
			                 <div class="left">
			                   <div class="address">
			                      <?php echo $v['buyer_name']?><br/>
			                      <?php echo $v['buyer_address_1'].' '.$v['buyer_address_2'].' '.$v['buyer_city'].' '.$v['buyer_state'].' '.$v['buyer_zip']?><br/>
			                      <?php echo $v['addressCountry']?><br/>
			                   </div>
			                   
			                   <div class="buyerOperate">
			                     <p class="first">
			                     	<span>买家Email:</span><?php echo $v['buyer_email']?>
			                     	<span>收款方式:</span><?php echo $v['pay_method']?>
			                     	<span>交易号:</span><?php echo $v['pay_id']?>
			                     	<span>运输方式:</span><?php echo isset($shipemntInfo[$v['shipmentAutoMatched']])? $shipemntInfo[$v['shipmentAutoMatched']] : '';?>
			                     	<font color="#999999">(<?php echo $v['ShippingServiceSelected'];?>)</font>
			                     	<font color="#CC0000">(运费:<?php echo $v['currency_type'].' '.$v['orders_ship_fee'];?>)</font>
			                     </p>
			                     <p>
			                      <span>打印时间:</span><?php echo $v['orders_print_time']?>
			                     </p>
			                   </div>
			                 </div>
			                 <div class="right">
			                   <table border="0" class="table  table-bordered dataTable">
				                   <colgroup>
								       <col width="10%">
								       <col width="20%">
								       <col width="30%">
								       <col width="20%">
								       <col width="10%">                
								       <col width="10%">
							   	   </colgroup>
							   	   <?php foreach($v['productInfo'] as $valu):?>
				                     <tr>
				                       <td><?php echo $valu['skuFrist']?></td>
				                       <td>
				                          <?php echo $valu['orders_sku']?>
				                           <?php if($valu['products_id']!=''):?>
				                             <img src="<?php echo site_url('attachments/images/question.gif');?>"/><br/>
				                           <?php endif;?>
				                          <?php echo isset($valu['status']) ? $valu['status'] : '' ;?>
				                       </td>
				                       <td><?php echo $valu['skuThird']?></td>
				                       <td><?php echo $valu['skuFour']?></td>
				                       <td>X<?php echo $valu['item_count']?></td>
				                       <td><?php echo $valu['skuFive']?></td>
				                     </tr>
				                     <?php endforeach;?>
				                     <?php echo $v['customer_select']?>
				                     <tr>
				                       <td colspan="6">平台费: $<?php echo isset($v['platFeeTotal'])? $v['platFeeTotal'] : 0 ?> 总运费:<?php echo $v['shippingCost'];?> RMB , 包裹重: <?php echo $v['totalWeight'];?>KG , 物品数量 <?php echo $v['skuNum'];?></td>
				                     </tr>
				                     <?php if(!empty($v['cargo_weight'])):?>
				                     <tr>
				                       <td colspan="6">货代重量: <?php echo $v['cargo_weight'];?> KG</td>
				                     </tr>
				                     <?php endif;?>
			                   </table>
			                 </div>
			              </div>
					   </td>
					 </tr>
					 
	               </tbody>
	               
	            <?php endforeach;?>
	            </table>
	      <?php 
				 if($key == 'root' || $key == 'manager'){
					 $this->load->view('admin/common/page_number');
						
				 }else{
					$this->load->view('admin/common/page'); 
				}
		 ?>
                
            </div>
        </div>
    </div>
</div>
<script>
$(function(){
	$("#orders_status").val("<?php echo $search['orders_status'];?>");
	$("#warehouse").val("<?php echo $search['warehouse'];?>");
	$("#isBackOrder").val("<?php echo $search['isBackOrder'];?>");
	$("#orders_type").val("<?php echo $search['orders_type'];?>");
	$("#showType").val("<?php echo $search['showType'];?>");
	$(".order_log").click(function(){
		var id = $(this).attr('data_id');
		$.layer({
		    type   : 2,
		    shade  : [0.8 , '' , true],
		    title  : ['操作日志',true],
		    iframe : {src : '/admin/order/orderManage/operate_log?operateMod=ordersManage&id='+id+'?&is_ajax=1'},
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
	/**
	  显示类型
	*/
	var showType=$("#showType").val();
	if(showType=="some"){
		$("[name='orderInfo']").hide();
		$(".shouqi").text("展开");
	}else{
		$("[name='orderInfo']").show();
		$(".shouqi").text("收起");
	}
})
$(".shouqi").click(function(){
  var num=$(this).attr('name');
  var content=$(this).text();
  if(content=="收起"){
	  $(".no"+num).hide();
	  $(this).text("展开");
  }else{
	  $(".no"+num).show();
	  $(this).text("收起");
  }
  
});
</script>
