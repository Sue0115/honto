<div class="row">
	<div class="col-xs-12">
	    &nbsp;子&nbsp;sku&nbsp;&nbsp;：&nbsp;
	    <input type="text" name="original_sku" value="<?php echo $result['original_sku']?>" disabled/>
	</div>
	<div class="col-xs-12">
	    颜 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;色：&nbsp;
	  <input type="text" name="color" value="<?php echo !empty($result['color']) ? $result['color'] : '';?>" disabled/>
	</div>
	<div class="col-xs-12">
	   尺&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;寸：&nbsp;
	  <input type="text" name="size" value="<?php echo !empty($result['size']) ? $result['size'] : '';?>" disabled/>
	</div>
	<div class="col-xs-12">
	   库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存：&nbsp;
	  <input type="text" name="quantity" value="<?php echo !empty($result['quantity']) ? $result['quantity'] : '';?>" disabled/>
	</div>
	<div class="col-xs-12">
	   运输时长：<input type="text" name="ship_time" value="<?php echo !empty($result['shipping_time']) ? $result['shipping_time'] : '';?>" disabled/>
	</div>
	<div class="col-xs-12">
	   msrp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;：<input type="text" name="msrp" value="<?php echo !empty($result['msrp']) ? $result['msrp'] : '';?>" disabled/>
	</div>
	<div class="col-xs-12">
	   导入时间：<input type="text" name="createTime" value="<?php echo !empty($result['createTime']) ? $result['createTime'] : '';?>" disabled/>
	</div>
	<?php if(!empty($result['erp_main_image'])):?>
	<div class="col-xs-1" style="line-height:120px;">
	   主图：
	</div>
	<div class="col-xs-11">
	  <dl style="width:200px;height:120px;">
	  	<dd style="height:100px;text-align:center;">
	  	  <img src="<?php echo $result['erp_main_image'];?>" style="width:100px;height:100px;"/>
	  	</dd>
	    <dt style="height:20px;text-align:center;">
	       <?php echo $result['yuming'];?>
	    </dt>
	  </dl>
	</div>
	<?php endif;?>
	<?php 
		if(!empty($result['erp_extra_image'])):
		$imgFuArr = explode('|',$result['erp_extra_image']);
	?>
		<div class="col-xs-1" style="line-height:120px;">
		   附图：
		</div>
		<div class="col-xs-11">
		  <?php foreach($imgFuArr as $fu):?>
		  <dl style="width:200px;height:120px;display:inline-block;">
		  	<dd style="height:100px;text-align:center;">
		  	  <img src="<?php echo $fu;?>" style="width:100px;height:100px;"/>
		  	</dd>
		    <dt style="height:20px;text-align:center;">
		       <?php echo $result['yuming'];?>
		    </dt>
		  </dl>
		  <?php endforeach;?>
		</div>
	<?php endif;?>
	<div class="col-xs-12">
	   产品描述：<textarea rows="10" cols="80" disabled>
	    <?php echo !empty($result['product_description']) ? $result['product_description'] : '';?>
	   		</textarea>
	</div>
	
</div>