<style>
.red{
	color:red;
}
.green{
	color:green;
}
</style>
<?php if($type==1):?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="table-header">手动出入库</h3>
    </div>
</div>

<div class="row">&nbsp;</div>

<form action="<?php echo admin_base_url('caiwu/stock_deal/uploading')?>" enctype="multipart/form-data" method="post" id="submitForm">

<div class="row">
	<div class="col-xs-2">
	  所属仓库：
	  <select name="warehouse">
	    <?php foreach($warehouse as $k => $w):?>
	    <option value="<?php echo $k?>"><?php echo $w;?></option>
	    <?php endforeach;?>
	  </select>
	</div>
	<div class="col-xs-2">
	  出库类型：
	  <select name="type">
	    <option value="1">库存调整</option>
	    <option value="2">采购返修不良品</option>
	    <option value="3">产品领取</option>
	    <option value="4">其他</option>
	  </select>
	</div>
	<div class="col-xs-2">
	 出库人：<?php echo $userInfo->nickname?>
	 <input type="hidden" name="username" value="<?php echo $userInfo->nickname?>" />
	</div>
	<div class="col-xs-2">
	 出库人日期：<?php echo date('Y-m-d')?>
	</div>
</div>

<div class="row">&nbsp;</div>

<div class="row">
  <div class="col-xs-11 text-center">   	
            	 <label>
            	 	<input type="file" id="file" name="excelFile" class="btn btn-primary btn-sm">
            	 </label>
            	 <label>
            	 	<input type="submit" value="导入数据" id="sub"  class="btn btn-sm btn-primary"/>
            	 </label>
            	 <label>
            	 	<a class="btn btn-sm btn-primary" href="<?php echo site_url('attachments/template/sku_stock_模板.xls')?>">
            	 		模板文件下载
            	 	</a>
            	 </label>		
    </div>
</div>

</form>
<?php else:?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="table-header">请认真校对要出入库的信息</h3>
    </div>
</div>
<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="3%">
						<col width="8%"/>
						<col width="8%"/>
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="7%">
						<col width="10%">
						<col width="10%">
						<col width="7%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th>序号</th>
							<th>*出库日期</th>
							<th>质检日期</th>
							<th>*供应商/申请部门</th>
							<th>采购员</th>
							<th>原采购单号</th>
							<th>*SKU</th>
							<th>*出库原因</th>
							<th>*申请数量</th>
							<th>*出库数量</th>
							<th>操作结果</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($data as $item):	
						?>
						<tr>
							<td class="center">
								<?php echo $item[0];?>
							</td>
							<td class="center c_date">
								<?php echo $item[1];?>
							</td>
							<td class="center z_date">
								<?php echo $item[2];?>
							</td>
							<td class="center shenqing">
								<?php echo $item[3];?>
							</td>
							<td class="center caigouname">
								<?php echo $item[4];?>
							</td>
							<td class="center caigouid">
								<?php echo $item[5];?>
							</td>
							<td class="center deal_sku">
								<?php echo $item[6];?>
							</td>
							<td class="center c_reason">
								<?php echo $item[7];?>
							</td>
							<td class="center shenqing_num">
								<?php echo $item[8];?>
							</td>
							<td class="center c_num">
								<?php echo $item[9];?>
							</td>
							<td class="center">
							   <span class="<?php echo $item[6]?>"></span>
							</td>
						</tr>
						<?php
						endforeach;
						?>	
					</tbody>
</table>

<div class="row">&nbsp;</div>

<div class="row">
  <input type="hidden" id="warehouse" value="<?php echo $post['warehouse']?>"/>
  <input type="hidden" id="cr_type" value="<?php echo $post['type']?>"/>
  <div class="col-xs-12 center">
 	<label>
		<a class="btn btn-primary btn-sm" id="queding" style="width:100px;">确定</a>
	</label>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<label>
	    <a href="<?php echo admin_base_url('caiwu/stock_deal/pandian')?>" class="btn btn-primary btn-sm" style="width:100px;">返回</a>
	</label>
  </div>
</div>
<?php endif;?>
<script type="text/javascript">
$("#queding").click(function(){
	var c_date 	   = new Array();//出库日期的数组
    var z_date 	   = new Array();//质检日期的数组
    var shenqing   = new Array();//供应商或者申请部门的数组
    var caigouname = new Array();//采购员的数组
    var caigouid   = new Array();//原采购单号的数组
    var deal_sku   = new Array();//sku的数组
    var c_reason   = new Array();//出库原因的数组
    var shenqing_num = new Array();//申请数量的数组
    var c_num 	   = new Array();//出库数量的数组
    var warehouse  = $("#warehouse").val();//所属仓库
    var type	   = $("#cr_type").val();//出入库类型
   
    var flag 	   = true;//是否能提交的标志
    var message    = '';//存放提示错误的信息

    $(".c_date").each(function(i){
    	c_date[i] = $(this).text();
     });
    $(".z_date").each(function(i){
    	z_date[i] = $(this).text();
     });
    $(".shenqing").each(function(i){
    	shenqing[i] = $(this).text();
     });
    $(".caigouname").each(function(i){
    	caigouname[i] = $(this).text();
     });
    $(".caigouid").each(function(i){
    	caigouid[i] = $(this).text();
     });
    $(".deal_sku").each(function(i){
    	deal_sku[i] = $(this).text();
    	if($.trim($(this).text())==''){
			 flag = false;
			 message = '存在为空的sku';
	  	 }
     });
    $(".c_reason").each(function(i){
    	c_reason[i] = $(this).text();
     });
    $(".shenqing_num").each(function(i){
    	shenqing_num[i] = $(this).text();
     });
    $(".c_num").each(function(i){
    	c_num[i] = $(this).text();
    	if(type==2 || type==3){
  		  if($.trim($(this).text())<0){
			 flag = false;
			 message = '该出库类型的出库数量不能为负数';
  	  	  }
    	}
     });
    if(!flag){
        layer.alert(message,8);
    	return false;
    }
    
	layer.confirm('确定数据校对无误？', function(index){
		layer.load('正在处理数据，请稍候。。。', 3);
		$.ajax( {     
	  		  url:'<?php echo admin_base_url('caiwu/stock_deal/do_action')?>',  
	  		  dataType:'json',
		      data:{"c_date":c_date,"z_date":z_date,"shenqing":shenqing,"caigouname":caigouname,"caigouid":caigouid,"deal_sku":deal_sku,"c_reason":c_reason,"shenqing_num":shenqing_num,"c_num":c_num,"warehouse":warehouse,"type":type},
		  	  type: 'POST',
		      async:false,
			  cache:false,       
	  		  success:function(data) {
	 		   	 $.each(data,function(n,value) {
		 		   	 if(value.status==false){
			 		   		$("."+n).addClass("red");
			 		 }else{
				 			$("."+n).addClass("green");
				     }
		 		   	 $("."+n).text(value.msg);
		 	     })
			 	    $("#queding").attr('disabled',true);//禁用确定按钮
		 			$(".table-header").text(" ");
		 			$(".table-header").text("手动出入库结果显示");
		 			layer.alert('数据已经更新完毕',9);
	 		  }

		});
		
		
		
	})
	
});
			
</script>
