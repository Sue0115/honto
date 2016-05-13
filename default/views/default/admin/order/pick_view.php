<style>
#first{
	border:1px solid #ccc;
	text-align:center;
	font-weight:bold;
	line-height:30px;
	background:#fff;
}
#second{
	border:1px solid #ccc;
	text-align:center;
	background:#fff;
	line-height:30px;
	height:30px;
}
a:hover{
	text-decoration:none;
	cursor:pointer;
}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">拣货单-查看-<a href="<?php echo admin_base_url('order/pick_manage');?>">返回列表</a></h3>
        <div class="table-header">
            &nbsp;拣货单单号&nbsp; <?php echo $pickInfo['id'];?>
            <button class="btn btn-success btn-xs"><?php echo $status_text[$pickInfo['status']];?></button>
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
             <div class="row">
            	<table width="100%" border="1" id="first">
            	  <tr>
            	    <td width="20%">
            	          拣货单类型<br/>物流商
            	    </td>
            	    <td width="35%" style="text-align:left;">
            	    	<?php echo $type_text[$pickInfo['type']];?><br/>
            	    	<?php echo $pickInfo['shipmentTitle'];?>
            	    </td>
            	    <td width="20%">创建时间<br/>创建人</td>
            	    <td width="25%" style="text-align:left;">
            	      <?php echo date('Y-m-d H:i',$pickInfo['create_time']);?><br/>
            	      <?php echo $username;?>
            	    </td>
            	  </tr>
            	</table>
            	<br/>
            	<table width="100%" border="1" id="second">
            	  <tr>
            	    <td width="20%" style="font-weight:bold;">创建包裹数</td>
            	    <td width="15%"><a class="packgetDetail" data_id="0"><?php echo $pickInfo['order_num']?></a></td>
            	    <td width="20%">异常包裹数</td>
            	    <td colspan="2">
            	       <a class="packgetDetail" data_id="9">
            	         <?php echo $status9;?>
            	       </a>
            	    </td>
            	  </tr>
            	  <tr>
            	    <td width="20%">等待包装数</td>
            	    <td width="15%">
            	     <a class="packgetDetail" data_id="1">
            	       <?php echo $status1;?>
            	     </a>
            	     <a target="_blank" href="<?php echo admin_base_url('order/pick_manage/printPickOrder?status=1&pick_id=')?><?php echo $pickInfo['id']?>">
            	      <button class="btn btn-success btn-xs">打印</button>
            	     </a>
            	   </td>
            	    <td width="20%">已扫描数</td>
            	    <td width="15%">
            	      <a class="packgetDetail" data_id="2">
            	       <?php echo $status2;?>
            	      </a>
            	    </td>
            	    <td width="30%"></td>
            	  </tr>
            	  <tr>
            	    <td width="20%">已包装数</td>
            	    <td width="15%">
            	     <a class="packgetDetail" data_id="3">
            	       <?php echo $status3;?>
            	     </a>
            	    </td>
            	    <td width="20%">已发货数</td>
            	    <td width="15%">
            	      <a class="packgetDetail" data_id="4">
            	       <?php echo $status4;?>
            	      </a>
            	    </td>
            	    <td width="30%"></td>
            	  </tr>
            	</table>
			  </div>
				
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$(function(){
		$(".packgetDetail").click(function(){
			var id = $(this).attr('data_id');
			var pick_id=<?php echo $pickInfo['id']?>;
			$.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['包裹详情',true],
			    iframe : {src : '/admin/order/pick_manage/packgetDetail?status='+id+'&pick_id='+pick_id},
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
	});
	
</script>