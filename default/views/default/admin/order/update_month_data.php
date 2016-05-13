<script src="<?php echo static_url('theme/common/layer/layer.min.js')?>"></script>
<style>
 #main{
    width:600px;
    margin:50px auto;
 }
</style>
<div class="">
    <div class="col-xs-12">
      <div id="main">
        <p style="font-weight:bold;">总工时：<?php echo isset($vdata['monthTotalTime']) ? $vdata['monthTotalTime'] : 0 ?></p><br/>
        <form action="<?php echo admin_base_url("order/MonthList")?>" id="form1">
          	工时:<input type="text" name="totalTime" value="" id="totalTime"/>
          	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          	总发错货数:<input type="text" name="totalNum" value="<?php echo isset($vdata['monthErrorNum']) ? $vdata['monthErrorNum'] : 0 ?>" id="totalNum"/><br/>
          	<input type="hidden" name="uid" value="<?php echo $uid;?>"/>
          	<input type="hidden" name="month" value="<?php echo $month;?>"/>
          	<input type="submit" value="修改" style = "margin-left:200px;margin-top:30px;width:80px;height:30px;"/>
          	<input type="reset"  value="重置" style = "width:80px;height:30px;"/> 
        </form>
      </div>
    </div>
</div>
<script type="text/javascript">
$("#form1").submit(function(){
	 var uid = <?php echo $uid;?>;
	 var month = <?php echo $month;?>;
	 var totalTime = $("#totalTime").val();
	 var totalNum = $("#totalNum").val();
	 var index = parent.layer.getFrameIndex(window.name);
        	$.ajax( {     
        		  url:'<?php echo admin_base_url("order/MonthList/updateing")?>',  
        		  type:'post',     
        		  async:false,
        		  cache:false, 
        		  data:{"uid":uid,"month":month,"totalTime":totalTime,"totalNum":totalNum},   
        		  dataType:'json',     
        		  success:function(data) {
        			  result = eval(data);
					  if(result==true){
						  layer.alert('数据更新成功',9,'信息提示');
						  
					  }else{
						  layer.alert('数据更新失败',8,'信息提示',true);
						
					  }
        		  }        
          });
    window.parent.location.href="/admin/order/MonthList/index";
	return false;    
});
</script>