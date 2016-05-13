<div class="row">
	<div class="col-xs-12" >
		<span style="font-size:16px;font-weight:bold;">当前位置：message详情</span>
	</div>
</div>
<br/>
<div class="row">
  <div class="col-xs-9">
     
     <table class="table table-bordered  dataTable" style="background:#fff;">
        <colgroup>
			<col width="20%">
			<col width="80%"/>
		</colgroup>
        <tr style="font-weight:bold;">
          <td class="text-right">
          		主题：
          </td>
          <td>
           <?php echo $main['label']?>
          </td>
        </tr>
        <?php foreach($fu as $k => $data):?>
        <tr <?php echo $data['sender']=='user'? "style='background:#FF9'":" "?>>
          <td class="text-right">
          
           <span style="font-size:13px;font-weight:bold;">
              <?php
				if($data['sender']=='user'){
				  echo 'From:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$main['userInfo']['name'];
				}elseif($data['sender']=='merchant' && $data['userID']!=''){
				  echo 'Reply:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$userArr[$data['userID']];
				}else{
				  echo 'Reply:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$data['sender'];
				}
              ?>
            </span>
            <br/>
             <?php echo $data['message_date']?>
          </td>
          <td>
           <?php echo !empty($data['content_oriagnal']) ? $data['content_oriagnal'] : $data['content_en'];?><br/>
           <?php foreach($data['imgArr'] as $img):?>
             <span>
                 <a href="<?php echo $img?>" target="_blank"><img src="<?php echo $img?>" alt="" style="width:200px;height:200px;"/></a>
            </span>
           <?php endforeach;?>
           <br/>
           <span class="content_cn<?php echo $k;?>" style="display:none;"><?php echo $data['content_cn'];?></span>
           <?php if(!empty($data['content_cn'])):?>
              <span class="view" style="color:#0066FF;cursor:pointer;" data-id="<?php echo $k;?>">[view chinese]</span>
           <?php endif;?>
          </td>
        </tr>
        <?php endforeach;?>
     </table>
     
     <h3 class="blue">Order Detail</h3>
     <table class="table table-bordered dataTable" style="background:#fff;">
        <colgroup>
			<col width="2%">
			<col width="8%"/>
			<col width="15%"/>
			<col width="14%">
			<col width="10%">
			<col width="7%">
			<col width="7%">
			<col width="10%">
			<col width="10%">
			<col width="10%">
			<col width="10%">
		</colgroup>
		<thead>
			<tr>
				<th class="center">
					<label>
                       <input type="checkbox" class="ace" />
                        <span class="lbl"></span>
                     </label>
				</th>
				<th class="text-center">Img</th>
				<th class="text-center">Product</th>
				<th class="text-center">orderId</th>
				<th class="text-center">SKU</th>
				<th class="text-center">State</th>
				<th class="text-center">Total Cost</th>
				<th class="text-center">Tracking Info</th>
				<th class="text-center">Marked Shipped</th>
				<th class="text-center">Marked Refunded</th>
				<th class="text-center">Operation</th>
			</tr>
		</thead>
		<tbody>
		  <?php foreach($main['orderInfo'] as $data):?>
		    <tr style="word-break:break-all; word-wrap:break-word;">
		      <td>
		        <label>
					<input type="checkbox" class="ace" name="productIds[]" value="<?php echo $data['Order']['order_id'];?>" >
					<span class="lbl"></span>
				</label>
		      </td>
		      <td>
		        <a href="<?php echo $data['Order']['product_image_url'];?>" target="_blank">
		          <img src="<?php echo $data['Order']['product_image_url'];?>" style="width:70px;height:70px;"/>
		        </a>
		      </td>
		      <td><?php echo $data['Order']['product_id'];?></td>
		      <td><?php echo $data['Order']['order_id'];?></td>
		      <td><?php echo $data['Order']['sku'];?></td>
		      <td><?php echo $data['Order']['state'];?></td>
		      <td>$<?php echo $data['Order']['order_total'];?></td>
		      <td>
		       <?php 
		        echo isset($data['Order']['tracking_number']) ? $data['Order']['tracking_number'] : 'n/a';
		       ?>
		       
		      </td>
		      <td>
		       <?php 
		         if(isset($data['Order']['shipped_date'])){
		         	echo $data['Order']['shipped_date'];
		         	echo '<br/>';
		         	echo $mark_msg[$data['Order']['order_id']];
		         }else{
		         	echo 'n/a';
		         }
		       ?>
		      </td>
		      <td>
		        <?php 
		         if($data['Order']['state']=='REFUNDED'){
		         	echo 'REFUNDED<br/>'.$data['Order']['refunded_reason'];
		         }else{
		         	echo 'n/a';
		         }
		        ?>
		      </td>
		      <td>
		        
		        <input type="button" class="btn  btn-sm" value="退款" id="refurnd"/>
		       
		      </td>
		    </tr> 	
		  <?php endforeach;?>
		</tbody>
     </table>
     <div class="row">&nbsp;</div>
     
     <div style="width:100%;">
	  <div  style="height:330px;line-height:300px;font-size:16px;background:#fff;text-align:center;width:20%;float:left;">
	       回复:
	  </div>
	  <div style="width:80%;display:inline-block;float:right;">
	    <select id="pTemplate" name="pTemplate" style="width:10%;">
	       <option value="">==目录==</option>
	       <?php foreach($mainTemplatArr as $ma):?>
	          <option value="<?php echo $ma['modClassID']?>"><?php echo $ma['modClassName']?></option>
	       <?php endforeach;?>
	    </select>
	    <select id="sTemplate" name="sTemplate" style="width:71%;">
	       <option value="">==选择常用模板==</option>
	    </select>
	    &nbsp;&nbsp;&nbsp;&nbsp;语言类型：
	    <span style="font-weight:bold;font-size:14px;"><?php echo $lanauge[$main['userInfo']['locale']]?></span>
	    <textarea style="width:99%; resize: none;height:300px;" id="content" name="content">
	     
	    </textarea> 
	    <input type="hidden" name="mailID" value="<?php echo $main['mailID']?>" />
	    <input type="hidden" name="account" value="<?php echo $main['account']?>" />
	    <input type="hidden" name="countmail" value="<?php echo count($fu);?>" />
	  </div>
	  <?php 
	    if($main['isReturn']<3){
	  ?>
	  <div class="row">
	     <div class="col-xs-12 text-center">
		   <button class="btn btn-success su" data-id="1">
	                <i class="icon-ok bigger-110"></i>
	                  	回复
	       </button>
	       <button class="btn btn-success su" data-id="2">
	                <i class="icon-remove bigger-110"></i>
	                  	不必回(关闭wish后台和ERP邮件)
	       </button>
	       <button class="btn btn-warning su" data-id="4">
	                  	手动关闭ERP邮件
	       </button>
	       <button class="btn btn-inverse su" data-id="3">
	                <i class="bigger-110"></i>
	                  	Appeal To Wish Support
	       </button>
         </div>
      </div>  
      <?php }?>
	</div>
	
  </div>
  
  <div class="col-xs-3">
  <?php foreach($orderAndShipmentArr as $k => $orderAndshipment):?>
    
    <div class="orderDetail">
       <span class="glyphicon glyphicon-minus showandhide"  data-id="<?php echo $k?>" style="cursor:pointer;"></span><span style="font-size:14px;font-weight:bold;">订单信息</span>
    </div>
    <table width="100%" style="border:1px solid #ddd;background:#fff;word-break:break-all; word-wrap:break-word;" class="orderInfo<?php echo $k;?>">
        <colgroup>
		  <col width="20%">
		  <col width="80%"/>
		</colgroup>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           内单号
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <a href="http://120.24.100.157:70/all_orders_manage.php?erp_orders_id=<?php echo $orderAndshipment['orderInfo']['erp_orders_id']?>&Submit6=筛选" target="_blank">
	       <?php echo $orderAndshipment['orderInfo']['erp_orders_id']?>
	      </a>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           买家ID
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['buyer_id']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           销售账号
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['sales_account']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	      PP凭证
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['pay_id']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           收货人
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['buyer_name']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	         收件人国家
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo !empty($orderAndshipment['codeArr']) ? $orderAndshipment['codeArr']['country_cn'] : $orderAndshipment['orderInfo']['buyer_country_code']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           发货地址
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['buyer_name']?><br/>
	      <?php echo $orderAndshipment['orderInfo']['buyer_address_1'].' '.$orderAndshipment['orderInfo']['buyer_address_2']?><br/>
	      <?php echo $orderAndshipment['orderInfo']['buyer_city'].' '.$orderAndshipment['orderInfo']['buyer_state']?><br/>
	      zip:<?php echo $orderAndshipment['orderInfo']['buyer_zip']?><br/>
	      Phone:<?php echo $orderAndshipment['orderInfo']['buyer_phone']?><br/>
	      <?php echo $orderAndshipment['orderInfo']['buyer_country']?>
	    </td>
	  </tr>
	
	  <tr>
	    <td style="border:1px solid #ddd;">
	           总金额
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['orders_total'].' '.$orderAndshipment['orderInfo']['currency_type']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           运费
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['orders_ship_fee'].' '.$orderAndshipment['orderInfo']['currency_type']?>
	    </td>
	  </tr>
	
	  <tr>
	    <td style="border:1px solid #ddd;">
	           支付时间
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['orders_paid_time']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           广告物流
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['ShippingServiceSelected']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	           匹配物流
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['shipmentTitle']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	          状态
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orders_status[$orderAndshipment['orderInfo']['orders_status']]?>
	      <span style="color:red;"><?php echo $orderAndshipment['orderInfo']['orders_is_backorder']==1 ? '欠货' : ''?></span>
	      <span style="color:green;"><?php echo $orderAndshipment['orderInfo']['ebayStatusIsMarked']==1 ? '已标记发货' : ''?></span>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;" colspan="2">
	        <?php echo $orderAndshipment['orderStatusDescription'];?>
	    </td>
	  </tr>
	  <?php if(!empty($orderAndshipment['orderInfo']['orders_shipping_code'])):?>
	  <tr>
	    <td style="border:1px solid #ddd;" colspan="2">
	       <label>
				<button class="btn btn-success btn-sm" id="shipping_code" data-id="<?php echo $orderAndshipment['orderInfo']['orders_shipping_code']?>">物流追踪</button>
			</label>
	    </td>
	  </tr>
	  <?php endif;?>
	  <tr>
	    <td style="border:1px solid #ddd;">
	          订单备注
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['orders_remark']?>
	       <a target="_blank"  href="http://120.24.100.157:70/logList.php?operateMod=ordersManage&operateKey=<?php echo  $orderAndshipment['orderInfo']['erp_orders_id']; ?>" >日志</a>
       
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	         退款信息
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php 
	      if ($orderAndshipment['moneyBack']){//有退款
              foreach ($orderAndshipment['moneyBack'] as $money){
               echo $money['moneyback_currency'].'-'.$money['moneyback_amount'].'-'.$money['moneyback_status'].' '.$money['moneyback_submitTime'].'<br/>';
             }
          } 
	      ?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;">
	         买家留言
	    </td>
	    <td style="border-bottom:1px solid #ddd;">
	      <?php echo $orderAndshipment['orderInfo']['notes_to_yourself']?>
	    </td>
	  </tr>
	  <tr>
	    <td style="border:1px solid #ddd;" colspan="2">
	       <?php echo $orderAndshipment['skuInfo'];?>
	    </td>
	    
	  </tr>
    </table>
  
    <?php endforeach;?>
  </div>
  
</div>

<script type="text/javascript" src="<?php echo site_url('static/theme/common/layer/layer.min.js')?>"></script>
<script type="text/javascript">
$(function(){
  $("#pTemplate").val('');
  $("#sTemplate").val('');
});
$("#content").click(function(){
	var value = $(this).val();
    var len = $.trim(value).length;
	if(value==0){
	  $(this).val('');
	}
	
});
$("#pTemplate").change(function(){
  var pID = $(this).val();
  $.ajax({
      url: '<?php echo admin_base_url('smt_message/wish_message_center/getTemplateData')?>',
      data:{'pID':pID,'type':'1'},
      type: 'POST',
      async:false,
	  cache:false,
      dataType: 'JSON',
      success: function(data){
          if(data.status=='1'){
				$("#sTemplate").empty();
				$("#content").empty();
				var input_row = '<option value="">==选择常用模板==</option>';
				$.each(data.d,function(i,val){
					input_row += '<option value="'+val.modID+'">'+val.modTitle+'</option>';
			    });
				$("#sTemplate").append(input_row);
          }else{
				layer.alert('回信模板不存在',8);
          }
      }
  });
});
//显示中文翻译
$(".view").click(function(){
	var k =$(this).attr('data-id');
	$(this).remove();
	$(".content_cn"+k).show();
});
$("#sTemplate").change(function(){

  var sID = $(this).val();
  $.ajax({
      url: '<?php echo admin_base_url('smt_message/wish_message_center/getTemplateData')?>',
      data:{'pID':sID,'type':'2'},
      type: 'POST',
      async:false,
	  cache:false,
      dataType: 'JSON',
      success: function(data){
          if(data.status=='1'){
				$("#content").empty();
				var input_row = '';
				$.each(data.d,function(i,val){
					input_row += val.modContent;
			    });
				$("#content").text(input_row);
          }
      }
  });
  
});
//订单信息隐藏显示
$(".showandhide").click(function(){
	var ids = $(this).attr('data-id');
	$('.orderInfo'+ids).toggle();
	var icon = $(this).attr('class');
	var icons = icon.split('-');

	if(icons[1]=='minus showandhide'){
		$(this).attr('class','glyphicon glyphicon-plus showandhide');
    }else{
    	$(this).attr('class','glyphicon glyphicon-minus showandhide');
    }


});
//回复邮件操作
$(".su").click(function(){
	var type=$(this).attr('data-id');
	layer.load('正在执行操作，请稍候。。。', 3);
	var mailID = $("input[name='mailID']").val();
	var account = $("input[name='account']").val();
	var content = $("#content").val();
	var countmail = $("input[name='countmail']").val();
	$.ajax({
	      url: '<?php echo admin_base_url('smt_message/wish_message_center/replayEmail')?>',
	      data:{'mailID':mailID,'account':account,'countmail':countmail,'content':content,'type':type},
	      type: 'POST',
	      async:false,
		  cache:false,
	      dataType: 'JSON',
	      success: function(data){
	          if(data.status==true){
	        	  window.close();
	          }else{
				layer.alert(data.msg,8);
		      }
	      }
	      
	});
});
//退款操作
$("#refurnd").click(function(){
	var order_id = '<?php echo $data['Order']['order_id']?>';
	var account = '<?php echo $orderAndshipment['orderInfo']['sales_account']?>';
    var mailID = '<?php echo $main['mailID']?>';
	$.layer({
	    type   : 2,
	    shade  : [0.8 , '' , true],
	    title  : ['操作wish退款',true],
	    iframe : {src : '/admin/smt_message/wish_message_center/replayWishOrder?id='+order_id+'&account='+account+'&mailID='+mailID},
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
//查询物流信息操作
$("#shipping_code").click(function(){
	var shipping_code = $(this).attr('data-id');
	$.layer({
        type   : 2,
	    shade  : [0.8 , '' , true],
	    title  : ['物流查询结果',true],
	    iframe : {src : '/admin/smt_message/wish_message_center/get_shipping_info?shipping_code='+shipping_code},
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
</script>