<style type="text/css">
.font-18{
    font-size: 28px;
    font-weight: bolder;
}

.margin-0{
    margin: 0;
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
            <?php if($no_scan_num <= 50){?>
                <a class="sendgood" id-data="<?php echo $pick['id'];?>" warehouse-data="<?php echo $pick['warehouse']; ?>">
                   <button class="btn btn-success btn-xs">结束本次包装作业</button>
                </a>
            <?php }?>
        </div>
    </div>
</div>
<div class="row">
  <div class="col-sm-12">
    <h1><div id="scan-result" class="red"></div></h1>
  </div>  
</div>
<div class="row" id="sku-info">


</div>

<script src="<?php echo $_SERVER['HTTP_HOST'] == '120.24.100.157:72'?  static_url('theme/lodop6194/LodopFuncs.js') : static_url('theme/lodop6194/LodopFuncs_v2.js');?>"></script>

<script src="<?php echo static_url('theme/light/light.js')?>"></script>

<script>
  $(function(){
    
    show_sku_info();

    $.ajaxSetup({ 
        async : false //同步请求
    }); 

    $("#sku").focus();
    $("#sku").attr('disabled',false);
    var LODOP; //声明为全局变量

    $("#sku").change(function(){
      Light.close(last_basket_num);//关闭上次的灯
      var pick_id = $("#pick_id").val();
      var type = $("#type").val();
      var sku = $("#sku").val();
      var warehouse = $("#warehouse").val();
      
      var last_basket_num = $("#last-basket-num").val();

      if(pick_id && sku){

         $("#sku").attr('disabled',true);
         $.post("<?php echo admin_base_url('order/pick_manage/do_scan')?>",
                {   pick_id: pick_id,
                    type : type,
                    sku:   sku
                 }  ,
                function(data){
                 
                    result = eval(data);
                    if(result['status'] == 1){
                      var str_tip = '';
                      if(result['can_print']){
                        str_tip = result['basket_num']+'号篮子-请把面单放到篮子中';
                      }
                      $("#scan-result").html(result['basket_num']+"号篮子，"+result['product_sku']+"("+result['scan_num']+")"+"，订单："+result['orders_id'] + str_tip);
                      Light.flashRed(result['basket_num']);//闪红灯
                      $("#last-basket-num").val(result['basket_num']);
                      if(result['can_print']){
                        ajaxchange_printed(result['orders_id'],pick_id,sku,warehouse,result['basket_num']);
                        alert(str_tip);
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
      show_sku_info();
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
                      var url = "<?php echo admin_base_url('print/order_print/orderPrint?id=')?>"+orders_id;
                      var tof = PrintOneURL(url);
                      Light.flashGreen(basket_num);//闪绿灯
                      if(!tof){
                        alert("订单："+orders_id+"打印失败");
                      }
                      
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

  //显示产品信息
  function show_sku_info(){
     var pick_id = $("#pick_id").val();

     if(pick_id){
      
      $.ajax({

           type: "GET",

           url: "<?php echo admin_base_url('order/pick_manage/show_has_scan_one')?>" ,

          data: { pick_id: pick_id }  ,

          success: function(data){
                    $("#sku-info").html(data);
                   } ,

          dataType: "html"

      });
        
      }

  }
</script>
