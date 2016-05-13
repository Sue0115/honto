<style type="text/css">
.font-18{
    font-size: 28px;
    font-weight: bolder;
}
.changebgcolor{
	background:green;
}
.margin-0{
    margin: 0;
}

#basketNum{
  margin-top:5px;
  height:200px;
  font-weight:bold;
  font-size:66px;
  line-height:180px;
  text-align:center;
  border:3px solid #000;
  display:none;
}
#info{
  font-weight:bold;
  font-size:30px;
  margin-top:5px;
  height:200px;
  background:#82AF6F;
  display:none;
}
#basketInfo{ 
 margin-top:10px;
}
.baskets{
	margin-left:5px;
	margin-bottom:5px;
	height:40px;
	line-height:40px;
	text-align:center;
    font-weight:bold;
	cursor:pointer;
}
.bask_detail{
	display:inline-block;
}
#images{
 overflow:hidden;
}
#images img{

 display:inline-block;
}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">当前包装作业拣货单：<?php echo $pick['id'].' '.$type_text[$pick['type']]; ?> <span class="btn-lg btn btn-success">今日已包装：<?php echo $total?></span></h3>
        <div class="table-header">
          
            <span>扫描/录入SKU：</span>
            <input type="hidden" id="pick_id"  name="pick_id" value="<?php echo $pick['id'];?>" />
            <input type="hidden" id="type"  name="type" value="<?php echo $pick['type'];?>" />
            <input type="hidden" id="warehouse"  name="warehouse" value="<?php echo $pick['warehouse'];?>" />
            <input type="text" id="sku"  name="sku" value="" />
            <input type="hidden" id="last-basket-num"  name="last-basket-num" value="" />
            <input type="hidden" id="last_sku" name="last_sku" value="" />
            <input type="hidden" id="last-color" name="last_color" value="1" />
            <?php if(false){?>
                <a class="sendgood" id-data="<?php echo $pick['id'];?>" warehouse-data="<?php echo $pick['warehouse']; ?>">
                   <button class="btn btn-success btn-xs">结束本次包装作业</button>
                </a>
            <?php }?>
            <a class="showErrOrder" id-data="<?php echo $pick['id'];?>" warehouse-data="<?php echo $pick['warehouse']; ?>">
                   <button class="btn btn-success btn-xs">未扫描</button>
            </a>
             <a href="/admin/order/print_no_pack/index?type=1&pick_id=<?php echo $pick['id'];?>&warehouse=<?php echo $pick['warehouse']; ?>" target="_blank">
		          <button class="btn btn-success btn-xs">打印异常</button>
			 </a>
        </div>
    </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <h1><div id="scan-result" class="red"></div></h1>
  </div>  
</div>
<div class="row">
	<div class="col-sm-7" style="background:#eee;height:500px;">
	
	
		<div class="row">
		
		   <div class="col-sm-3">
		   
		   </div>
		   
		   <div class="col-sm-6" >
		     <div id="basketNum"></div>
		   </div>
		   
		    <div class="col-sm-3">
		   
		   </div>
		   
		</div>
		
		<div id="info">
		    <div id="skuName"></div><br/>
		    <div id="skuCnName"></div>
		</div>
		
		
					
				     
	</div>
	<div class="col-sm-5">
		<div id="images">
			<img src="" id="imageUrl" width="400" height="400"/> 
		 </div>
	</div>
</div>

<div id="basketInfo">

</div>

<script src="<?php echo static_url('theme/lodop6194/LodopFuncs.js')?>"></script>

<script src="<?php echo static_url('theme/light/light.js')?>"></script>

<script>
  $(function(){
	Light.closeAll();
    show_sku_info_detail();

    $.ajaxSetup({ 
        async : false //同步请求
    }); 

    var colorArr = ['alert alert-danger','alert alert-info','alert alert-warning','alert alert-success'];

    $("#sku").focus();
    $("#sku").attr('disabled',false);
    var LODOP; //声明为全局变量

    $("#sku").change(function(){
      
      var pick_id = $("#pick_id").val();
      var type = $("#type").val();
      var sku = $("#sku").val();
      var last_sku = $("#last_sku").val();//上一个sku
      var warehouse = $("#warehouse").val();
      var img = $("#imageUrl").attr('src');//原图片路径
      var skuName = $("#skuName").text();//原图sku名称
      var skuCnName = $("#skuCnName").text();//原图sku中文名称

      var last_basket_num = $("#last-basket-num").val();

      Light.close(last_basket_num);//关闭上次的灯
	  
      if(pick_id && sku){

         $("#sku").attr('disabled',true);
         $.post("<?php echo admin_base_url('order/pick_manage/do_scan')?>",
                {   pick_id: pick_id,
                    type : type,
                    sku:   sku,
                    last_sku : last_sku
                 }  ,
                function(data){
                 
                    result = eval(data);
                    if(result['status'] == 1){
                      var str_tip = '';
                      if(result['can_print']){
                        str_tip = result['basket_num']+'号篮子-请把面单放到篮子中';
                      }
                      $("#basketNum").show();
                      $("#basketNum").text(result['basket_num']+'号');
                      $("#info").show();
                      
                      var color_num = $("#last-color").val();
                      if(color_num>=3){
                    	  color_num = 0;
                      }else{
                    	  color_num++;
                      }
                      $("#last-color").val(color_num);
                      $("#basketNum").removeClass();
                      $("#basketNum").addClass(colorArr[color_num]);
                      
                      if(result['is_exit']==0){//如果扫描的sku和以前的不一样，要载入图片路径
                    	  $("#imageUrl").attr('src',result['img_url']);
                    	  $("#last_sku").val(sku);
                    	  $("#skuName").text('sku：'+result['product_sku']);
                          $("#skuCnName").text('sku中文名称：'+result['name_cn']);
                          $("#basketNum").addClass(colorArr[color_num]);
                      }else{
                    	  $("#imageUrl").attr('src',img);	
                    	  $("#skuName").text(skuName);		
                    	  $("#skuCnName").text(skuCnName);	
                      }

                      
                      //$("#scan-result").html(result['basket_num']+"号篮子，"+result['product_sku']+"("+result['scan_num']+")"+"，订单："+result['orders_id'] + str_tip);
                      Light.flashRed(result['basket_num']);//闪红灯
                      $("#last-basket-num").val(result['basket_num']);
                      if(result['can_print']){
                        ajaxchange_printed(result['orders_id'],pick_id,sku,warehouse,result['basket_num']);
                        show_sku_info_detail();
                      }
                    }else{
                      alert(result.info);
                    }

                     $("#sku").attr('disabled',false);
                        
                },"json"
                );
      }
      $("#sku").val('');
      $("#sku").focus();

      //显示信息
      show_sku_info_detail();
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
//    LODOP.SET_SHOW_MODE("MESSAGE_GETING_URL",""); //该语句隐藏进度条或修改提示信息
//    LODOP.SET_SHOW_MODE("MESSAGE_PARSING_URL","");//该语句隐藏进度条或修改提示信息
    tof = LODOP.PRINT();
    return tof;

  };

  function ajaxchange_printed(orders_id,pick_id,sku,warehouse,basket_num){
    $.post("<?php echo admin_base_url('order/pick_manage/change_status_printed')?>",
                { orders_id: orders_id,pick_id: pick_id,sku:sku,warehouse:warehouse}  ,
                function(data){
                    result = eval(data);
                    if(result['status'] == 1){
                      //var url = "<?php echo admin_base_url('print/order_print/orderPrint?id=')?>"+orders_id;
                      //var tof = PrintOneURL(url);
                      Light.flashGreen(basket_num);//闪绿灯
                      //if(!tof){
                      //  alert("订单："+orders_id+"打印失败");
                      //}
                      
                    }else{
                      alert(result['info']);
                    }

                },"json"
                );
  } 
  
  $(".sendgood").click(function(){
    if(confirm('确定要结束本次包装作业吗 ？')){
      var pick_id=$(this).attr('id-data');
      var warehouse=$(this).attr('warehouse-data');
      $.layer({
          type   : 2,
          shade  : [0.8 , '' , true],
          title  : ['标记发货',true],
          iframe : {src : "/admin/order/pick_manage/endPicking?pick_id="+pick_id+"&warehouse="+warehouse+"&type=3"},
          area   : ['800px' , '700px'],
          success : function(){
                  layer.shift('top', 400)  
              },
              close: function(){
          window.location.href='/admin/order/pick_manage/multi_order_deal';
              },
              yes    : function(index){
                  layer.close(index);
                  move_order();
              }
      });
    }
  })
  
  //显示错误的订单
  $(".showErrOrder").click(function(){

      var pick_id=$(this).attr('id-data');
      var warehouse=$(this).attr('warehouse-data');
      $.layer({
          type   : 2,
          shade  : [0.8 , '' , true],
          title  : ['未扫描',true],
          iframe : {src : "/admin/order/pick_manage/showErrorPackget?pick_id="+pick_id+"&warehouse="+warehouse},
          area   : ['800px' , '800'],
          shadeClose : true,
          success : function(){
              layer.shift('top', 400)  
          },
          yes    : function(index){

              layer.close(index);
              move_order();
              }
      });

  })
  
  
  
  //显示篮子详情
  function showBasketInfo(bn,orderid){
   var pick_id = $("#pick_id").val();
   $.layer({
	    type   : 2,
	    shade  : [0.8 , '' , true],
	    title  : [bn+'号篮子详情&nbsp;&nbsp;&nbsp;&nbsp;订单号'+orderid,true],
	    iframe : {src : '/admin/order/pick_manage/getPickInfoByBaskBum?pick_id='+pick_id+'&bn='+bn},
	    area   : ['500px' , '300px'],
	    shadeClose : true,
	    success : function(){
          layer.shift('top', 400)  
      },
      yes    : function(index){

          layer.close(index);
          move_order();
      }
	});
  }

  //显示产品信息
  function show_sku_info_detail(){
     var pick_id = $("#pick_id").val();

     if(pick_id){
      
      $.ajax({

           type: "GET",

           url: "<?php echo admin_base_url('order/pick_manage/show_has_scan_ones')?>" ,

          data: { pick_id: pick_id }  ,

          success: function(data){
                    $("#basketInfo").html(data);
                   } ,

          dataType: "html"

      });
        
      }

  }
</script>
