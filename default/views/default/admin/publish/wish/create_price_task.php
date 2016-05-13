<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">刊登管理—生产调价任务</h3>
			<div class="row">
			   <div class="col-xs-3">
			   </div>
			   <div class="col-xs-1 text-right">
			   	请选择账号：
			   </div>
			   <div class="col-xs-3">
			     <select name="account" id="account">
			       <option>--销售账号--</option>
			       <?php foreach($accountList as $k => $account):?>
			        <option value="<?php echo $k;?>"><?php echo $account;?></option>
			       <?php endforeach;?>
			     </select>
			   </div>
			   <div class="col-xs-3">
			   </div>
			</div>
			<br/>
		    <div class="row">
			   <div class="col-xs-3">
			   </div>
			   <div class="col-xs-1 text-right">
			   	请选择wish状态：
			   </div>
			   <div class="col-xs-3">
			     <select name="wish_status" id="wish_status">
			       <option>--wish状态--</option>
					<option value="1">在售(审核通过)</option>
					<option value="2">在售(待审核)</option>
					<option value="3">促销</option>
					<option value="4">下架</option>
			     </select>
			   </div>
			   <div class="col-xs-3">
			   </div>
			</div>
			<br/>
			<div class="row">
			   <div class="col-xs-3">
			   </div>
			   <div class="col-xs-1 text-right">
			   	请选择价格类型：
			   </div>
			   <div class="col-xs-3">
			     <select name="price_type" id="price_type">
			       <option>--价格类型--</option>
					<option value="1">指定价格</option>
					<option value="2">上调价格</option>
					<option value="3">下调价格</option>
			     </select>
			   </div>
			   <div class="col-xs-3">
			   </div>
			</div>
			<br/>
			<div class="row">
			   <div class="col-xs-3">
			   </div>
			   <div class="col-xs-1 text-right">
			   	输入价格：
			   </div>
			   <div class="col-xs-3">
			     <input type="text" name="price" value="" size="25" placeholder="请输入要调整的价格" id="price"/>
			   </div>
			   <div class="col-xs-3">
			   </div>
			</div>
			<br/>
			<div class="row">
			   <div class="col-xs-3">
			   </div>
			   <div class="col-xs-1 text-right">
			   	
			   </div>
			   <div class="col-xs-3">
			    <label>
					<button class="btn btn-primary btn-sm" id="create_submit">
					  生成任务
					</button>
				</label>
			   </div>
			   <div class="col-xs-3">
			   </div>
			</div>
    </div>
</div>
<script type="text/javascript">
$("#create_submit").click(function(){
	var account     =$("#account").val();
	var wish_status =$("#wish_status").val();
	var price_type  =$("#price_type").val();
	var price       =$("#price").val();
	if(confirm('确定生成wish调价任务？')){
		layer.load('正在执行。。。请稍候！！！', 3);
    	$.ajax( {     
    		  url:'<?php echo admin_base_url("publish/wish/creating_price_task")?>', 
    		  data:{"account":account,"wish_status":wish_status,"price_type":price_type,"price":price},  
    		  type:'post',     
    		  async:false,
    		  cache:false,     
    		  dataType:'json',     
    		  success:function(data) {
    			  result = eval(data);
				  if(result['status']===true){
					  layer.close();
					  layer.alert(result['msg'], 8);
					  layer.close();
					 
				  }else{
					  layer.alert(result['msg'], 9);
					  layer.close();
				  }
    		  }        
      });
	}
});
</script>