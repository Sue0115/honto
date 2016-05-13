<?php if($type==1):?>
<style>
    .pic-main, .pic-detail, .relate-list {
        padding: 5px;
        border: 1px solid #ccc;
    }

    .pic-main li, .pic-detail li, .relate-list li {
        margin: 5px;
        padding: 0px;
        border: 0px;
        width: 102px;
        text-align: right;
    }
    /***拖拽样式***/
    .pic-main li div, .pic-detail li div, .relate-list li div{
        width: 102px;
        height: 125px;
        border: 1px solid #fff;
    }

    .pic-main .placeHolder div, .pic-detail .placeHolder div, .relate-list .placeHolder div{
        width: 102px;
        height: 125px;
        background-color: white !important;
        border: dashed 1px gray !important;
    }
</style>
<script type="text/javascript" src="<?php echo static_url('theme/common/jquery.dragsort-0.5.1.min.js');?>"></script>
<form action="<?php echo admin_base_url('wish/wish_product/saveToProduct'); ?>"  method="post">
    <div class="row">
      <div class="col-xs-12" style="font-size:15px;font-weight:bold;">基本属性</div>
    </div>
    <div class="row" style="height:35px;line-height:35px;">
      <div class="col-xs-2 text-right" style="font-size:13px;font-weight:bold;">刊登账号:</div>
      <div class="col-xs-10" ><?php echo $productInfoArr[0]['account']?></div>
    </div>
    <div class="row" style="height:35px;line-height:35px;">
      <div class="col-xs-2 text-right" style="font-size:13px;font-weight:bold;">标题:</div>
      <div class="col-xs-10" >
         <textarea rows="2" cols="80" name="product_name" style="resize:none"><?php echo $productInfoArr[0]['product_name']?></textarea>
      </div>
    </div>
    <div class="row" style="height:35px;line-height:35px;">
      <div class="col-xs-2 text-right" style="font-size:13px;font-weight:bold;">Tags:</div>
      <div class="col-xs-10" >
         <textarea rows="2" cols="80" name="Tags" style="resize:none"><?php echo $productInfoArr[0]['Tags']?></textarea>
      </div>
    </div>
    
    
    <div class="row">&nbsp;</div>
    <div class="row" style="height:35px;line-height:35px;font-weight:bold;">
       <div class="col-xs-2 text-right" >
                        广告图片：
       </div>
       <div class="col-xs-10" >
          <input type='text' name="product_sku" id="product_sku" placeholder="不输入不更改广告图片"/>
            <a href="javascript:void(0);" class="btn btn-default btn-sm image_url" data-id='1'>图片目录上传</a>
            <a href="javascript:void(0);" class="btn btn-default btn-sm image_url" data-id='2'>实拍目录上传</a>
            <a href="javascript:void(0);" class="btn btn-default btn-sm image_url" data-id='3'>WISH目录上传</a>
            <a href="javascript:void(0);" class="btn btn-default btn-sm image_url" data-id='4'>无水印目录上传</a>
       </div>
    </div>
    <div class="row" style="height:35px;line-height:35px;display:none;" id="imageArr">
      <div class="col-xs-2 text-right" style="font-size:13px;font-weight:bold;">图片:</div>
      <div class="col-xs-10" >
      
         <ul class="list-inline pic-detail">
           
         </ul>
         
      </div>
    </div>
    <div id="colorAndSku" style="display:none;">
    <?php if(!empty($colorArr)):?>
        <div class="row">&nbsp;</div>
        <div class="row">
          <div class="col-xs-12" style="font-size:15px;font-weight:bold;">设置不同颜色的产品主图</div>
        </div>
        <div class="row">&nbsp;</div>
        <?php foreach($colorArr as $c):?>
        <div class="row">
           <div class="col-xs-2 text-right" >
              <?php echo $c;?>
           </div>
           <div class="col-xs-5">
              <input type='text' name="<?php echo $c?>" id="color_<?php echo str_replace(' ','',$c)?>" size="30" placeholder="输入图片链接,不填默认第一张图片为主图"/>
                <a href="javascript:void(0);" class="btn btn-default btn-sm images_url" data-id='<?php echo $c;?>'>确定</a>
           </div>
           <div class="col-xs-4" id="<?php echo str_replace(' ','',$c);?>">
              
           </div>
        </div>
        <?php endforeach;?>
    <?php endif;?>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row" style="height:35px;line-height:35px;">
      <div class="col-xs-2 text-right" style="font-size:13px;font-weight:bold;">产品描述</div>
      <div class="col-xs-10" >
         <textarea rows="20" cols="80" autofocus="autofocus" style="resize:none" name="product_description"><?php echo $productInfoArr[0]['product_description']?></textarea>
      </div>
    </div>
    <input type="hidden" name="productID" value="<?php echo $productInfoArr[0]['productID']?>" />
    <input type="hidden" name="account" value="<?php echo $productInfoArr[0]['account']?>" />
    <?php 
     foreach($productInfoArr as $pd):
    ?>
     <input type="hidden" name="products_sku[]" value="<?php echo $pd['original_sku'].'-'.$pd['color']?>" />
    <?php endforeach;?>
    <div class="row text-center">
      <div class="col-xs-12">
        <button class="btn btn-inverse submit_btn" type="submit">
                <i class="icon-ok bigger-110"></i>
                             更新在线广告
        </button>
      </div>
    </div>
</form>
<?php else:?>
<form action="<?php echo admin_base_url('wish/wish_product/batchSaveToProduct'); ?>"  method="post">
    <div class="row">&nbsp;</div>
    <div class="row" style="height:35px;line-height:35px;">
      <div class="col-xs-2 text-right" style="font-size:13px;font-weight:bold;">产品描述</div>
      <div class="col-xs-10" >
         <textarea rows="20" cols="80" autofocus="autofocus" style="resize:none" name="product_description"></textarea>
      </div>
    </div>
    <input type="hidden" name="productID" value="<?php echo $productID?>" />
    <div class="row text-center">
      <div class="col-xs-12">
        <button class="btn btn-inverse submit_btn" type="submit">
                <i class="icon-ok bigger-110"></i>
                            批量 更新在线广告
        </button>
      </div>
    </div>
</form>
<?php endif;?>
<script>
$(function(){
	 //图片拖拽排序
    $(".pic-main, .pic-detail, .relate-list").dragsort({ dragSelector: "div",  placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });
    //删除主图片
    $(document).on('click', '.pic-del', function () {
        $(this).closest('li').remove();
    });
});
//设置产品主图
$(".images_url").click(function(){
	var color = $(this).attr('data-id');

	color = color.replace(' ','');

	$("#"+color).empty();
	var img_url = $("#color_"+color).val();

	var divimg = '';
	if(img_url!=''){
      divimg +='<img src="'+img_url+'" style="width:100px;height:100px;"/>';
      $("#"+color).append(divimg);
	}
	
})

$(".image_url").click(function(){
	var sku = $('#product_sku').val();
	var url = "<?php echo admin_base_url("wish/wish_product/ajaxUploadDirImage");?>";
	var account = "<?php echo $productInfoArr[0]['account']?>";
	var type = $(this).attr('data-id');
	var opt = '';
	var shui = '';//是否有水印
	if(type==2){
		opt = 'SP';
    }
    if(type==3){
        opt = 'WISH';
    }
    if(type==4){
        shui = 'shui';
    }

	if(sku==''){
		alert('请输入图片的sku');
		return false;
	}

	//开始异步获取并上传文件
    $.ajax({
        url: url,
        data: 'dirName='+sku+'&account='+account+'&opt='+opt+'&shui='+shui,
        type: 'POST',
        async:false,
		cache:false,
        dataType: 'JSON',
        success: function(data){
       
            if (data){ //说明有成功的，成功的添加到里边去
                var liStr = '';
                var h_url = '';
                $("#imageArr").show();
                $.each(data, function(index, el){
      
                    h_url = el.replace(':81','');

					h_url = h_url.replace('image-resize/100x-x75','image');
                    
                    liStr += '<li><div><img src="' + el + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="' + h_url + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                });
                $('#imageArr').find('ul').append(liStr);
                $("#colorAndSku").show();
            }else{
                alert('没有获取到图片');
            }
               
        }
    });
});
</script>