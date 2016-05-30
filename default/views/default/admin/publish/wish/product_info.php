<style>
    .row-border {
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 3px 4px 3px rgba(238, 238, 238, 1);
        margin-bottom: 10px;
    }

    .proh {
        width: 100%;
        height: 30px;
    }

    .hideaccordion, .showaccordion {
        float: left;
        height: 18px;
        line-height: 18px;
        position: relative;
        padding: 6px;
    }

    .hideaccordion h1, .showaccordion h1 {
        font-size: 14px;
        font-weight: bold;
        color: #444;
    }

    .hideaccordion h1 i {
        cursor: pointer;
    }

    .probody {
        width: 100%;
        height: 100%;
        padding: 0 10px;
    }

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

    .table-vcenter td {
        vertical-align: middle !important;
    }

    /***Validform的样式--su20141125***/
    .Validform_checktip {
        margin-left: 8px;
        line-height: 20px;
        height: 20px;
        overflow: hidden;
        color: #999;
        font-size: 12px;
    }

    /*.Validform_right{color:#71b83d;padding-left:20px;background:url(images/right.png) no-repeat left center;}
    .Validform_wrong{color:red;padding-left:20px;white-space:nowrap;background:url(images/error.png) no-repeat left center;}
    .Validform_loading{padding-left:20px;background:url(images/onLoad.gif) no-repeat left center;}*/
    .Validform_error {
        background-color: #ffe7e7;
    }

    #Validform_msg {
        color: #7d8289;
        font: 12px/1.5 tahoma, arial, \5b8b\4f53, sans-serif;
        width: 350px;
        background: #fff;
        position: absolute;
        top: 0px;
        right: 50px;
        z-index: 99999;
        display: none;
        filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#999999');
        -webkit-box-shadow: 2px 2px 3px #aaa;
        -moz-box-shadow: 2px 2px 3px #aaa;
    }

    #Validform_msg .iframe {
        position: absolute;
        left: 0px;
        top: -1px;
        z-index: -1;
    }

    #Validform_msg .Validform_title {
        line-height: 25px;
        height: 25px;
        text-align: left;
        font-weight: bold;
        padding: 0 8px;
        color: #fff;
        position: relative;
        background-color: #000;
    }

    #Validform_msg a.Validform_close:link, #Validform_msg a.Validform_close:visited {
        line-height: 22px;
        position: absolute;
        right: 8px;
        top: 0px;
        color: #fff;
        text-decoration: none;
    }

    #Validform_msg a.Validform_close:hover {
        color: #cc0;
    }

    #Validform_msg .Validform_info {
        padding: 8px;
        border: 1px solid #000;
        border-top: none;
        text-align: left;
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
    .my-list-cust li{ padding: 5px; float: left; position: relative;}
    .my-list-cust li img{ cursor: pointer;}
    .my-list-cust .my-check-cust{ position: absolute; z-index: 999; left: 5px; top: 1px;}
</style>

<div class="modal fade" id="myModal"  tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">生成SKU</h4>
            </div>

            <form  class="form-horizontal validate_form" >


                <div class="form-group">
                    <label class="control-label col-sm-2"></label>

                    <div class="col-sm-8">
                        <span class="red">颜色和尺寸用英文逗号分隔 <br/>例如 颜色:red,yellow,black</span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label col-sm-2">母SKU：</label>

                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="generate_sku_parent"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2">颜色：</label>

                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="generate_sku_color"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2">尺寸：</label>

                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="generate_sku_size"/>
                    </div>
                </div>

                </form>






            <div class="modal-footer">
                <button type="button" class="btn btn-primary fenlei" id="generate_sku_check"  >确定</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<form action="<?php echo admin_base_url('publish/wish/doAction'); ?>" class="form-horizontal validate_form" method="post">
	<div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;刊登账号选择</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
              <div class="col-sm-1"></div>
              <div class="col-sm-10">
                <!-- 分类选择 -->
                <div class="form-group">
                  <?php foreach ($account as $key => $a):?>
                   <div class="col-sm-2">
                      <input type="checkbox" value="<?php echo $key;?>" name="choose_account[]" class="choose_account"/> <?php echo $a['choose_code'];?>
                   </div>
                   <?php endforeach;?>
                   
                </div>
              </div>
              <div class="col-sm-1"></div>
            </div>
            <div class="promsg" style="display: none;">
                类目信息
            </div>
        </div>
    </div>
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;基本信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label for="subject" class="control-label col-sm-2"><span class="red">*</span>名称(name)：</label>

                    <div class="col-sm-10">
                        <textarea name="subject" id="subject" class="form-control" placeholder="名称" datatype="*1-128"
                                  nullmsg="名称不能为空" errormsg="请输入长度在1-128之间的英文字符"
                                  maxlength="128"><?php echo !empty($productInfo) ? $productInfo[0]['product_name'] : ''?>
                        </textarea>
                    </div>
                </div>

				<div class="form-group">
                    <label class="col-sm-2 control-label"><span class="red">*</span>Tags：</label>

                    <div class="col-sm-10">
                        
                        <textarea name="Tags" id="Tags" class="form-control" placeholder="Tags不超过10个" datatype="*" nullmsg="Tags不能为空">
						 <?php echo !empty($productInfo) ? trim($productInfo[0]['Tags']) : ''?>
					    </textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">parent_sku：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="parent_sku" id="parent_sku" value="<?php echo !empty($productInfo) ? $productInfo[0]['parent_sku'] : ''?>" />
                    </div>
                </div>
 
               <div class="form-group">
                    <label class="col-sm-2 control-label">发货时间(shipping_time)：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="shipping_time" id="shipping_time" value="<?php echo  !empty($productInfo) ? $productInfo[0]['shipping_time'] : '10-35' ?>"  />
                    </div>
                    
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">制造商(brand)：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="brand" id="brand" value="" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">UPC：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="upc" id="upc" value="" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">产品查询(landing_page_url)：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="landing_page_url" id="landing_page_url" value="" />
                    </div>
                </div>

               
            </div>

            <div class="promsg" style="display: none;">
                基本属性
            </div>
        </div>
    </div>
 
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;产品属性</span>
                </h1>
            </div>
        </div>
        <div class="probody">
            <div class="procnt" id="product_attributes">
            
                <div class="form-group">
                
              	   <label class="col-sm-2 control-label">请输入产品sku</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="sku_search" value="<?php echo !empty($productInfo) ?  $productInfo[0]['oriange_sku'] : ''?>" />
                    </div>
                    
                    <label class="col-sm-1 control-label">销售代码</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="newpirex" value="" />
                    </div>
                    
                    <label class="col-sm-1 control-label">价格</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="batch_price" value="" />
                    </div>
                    
                    <label class="col-sm-1 control-label">添加sku前缀</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="prefix" value="" />
                    </div>
                    
                    <label class="col-sm-1 control-label">添加sku后缀</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="buffer" value="" />
                    </div>
                    <div class="col-sm-1">
                       <input type="button" class="btn btn-primary btn-sm"  id="queding" value="确定" size="10"/>
                    </div>
                </div>
                
              
                
	            <div class="form-group title">
	                    <label class="col-sm-2 control-label"><span class="red">*</span>SKU：</label>
	                    
	                    <label class="col-sm-2 control-label">颜色(color)：</label>
	                    
	                    <label class="col-sm-2 control-label">尺寸：</label>
	                    
	                    <label class="col-sm-2 control-label">价格：</label>
	            </div>
	            <div id="sku_info">
	               <?php if(empty($productInfo)):?>
		              <div class="form-group">
	                
	                 	<label class="col-sm-1 control-label"></label>
	         
	                     <div class="col-sm-2">
		                    <input type="text" class="form-control" name="sku[]" id="sku" placeholder="请输入产品sku" datatype="*" nullmsg="sku不能为空"
		                               value="" />
		                </div>
		                    
	                    <div class="col-sm-2">
	                        <input type="text" class="form-control" name="color[]" id="color" value="" />
	                    </div>
	                    
	                    <div class="col-sm-2">
	                        <input type="text" class="form-control" name="size[]" id="size" value="" />
	                    </div>
	                    
	                    <div class="col-sm-2">
	                        <input type="text" class="form-control" name="prices[]" id="prices" value="" />
	                    </div>
	                    
	                  </div>
	               <?php else:?>
		               <?php foreach($productInfo as $pf):?>
			               <div class="form-group">
			                
			                 	<label class="col-sm-1 control-label"></label>
			         
			                     <div class="col-sm-2">
				                    <input type="text" class="form-control" name="sku[]" id="sku"  datatype="*" nullmsg="sku不能为空"
				                               value="<?php echo $pf['original_sku']?>" />
				                </div>
				                    
			                    <div class="col-sm-2">
			                        <input type="text" class="form-control" name="color[]" id="color" value="<?php echo $pf['color']?>" />
			                    </div>
			                    
			                    <div class="col-sm-2">
			                        <input type="text" class="form-control" name="size[]" id="size" value="<?php echo $pf['size']?>" />
			                    </div>
			                    
			                    <div class="col-sm-2">
			                        <input type="text" class="form-control" name="prices[]" id="prices" value="<?php echo $pf['product_price']?>" />
			                    </div>
			                    
			                    <div class="col-sm-2">
			                        <a class="btn btn-success btn-sm del_row">删除</a>
			                    </div>
			                  </div>
		               <?php endforeach;?>
	               <?php endif;?>
	            </div>
                
                <div class="form-group">
                    <div class="col-sm-offset-4">
                        <a class="btn btn-primary btn-sm" href="javascript: void(0);" id="add_row">添加一个sku</a>
                        <select id="choose_type"><option value="color">按颜色</option><option value="size">按尺寸</option></select>
                        <a class="btn btn-primary btn-sm" href="javascript: void(0);" id="add_mul_pic">设置多属性图片</a>
                        <a class="btn btn-primary btn-sm" href="javascript: void(0);"  data-toggle="modal" data-target="#myModal" id="generate_sku">生成SKU</a>
                    </div>
                </div>

                <div id="mul_pic_info">

                </div>




            </div>
            <div class="promsg" >


            </div>
        </div>
    </div>
    
  
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;价格属性</span>
                </h1>
            </div>
        </div>
        <div class="probody">
            <div class="procnt" id="price_attributes">
            
 				<div class="form-group">
                    <label class="col-sm-2 control-label"><span class="red">*</span>价格(price)：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="price" id="price"
                               placeholder="产品价格" datatype="*" nullmsg="价格不能为空"
                               value="<?php echo  !empty($productInfo) ? $productInfo[0]['product_price'] : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="red">*</span>库存(inventory)：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="inventory" id="inventory"
                               placeholder="产品库存" datatype="*" nullmsg="库存不能为空"
                               value="<?php echo  !empty($productInfo) ? $productInfo[0]['product_count'] : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="red">*</span>运费(shipping)：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="shipping" id="shipping"
                               placeholder="运费价格" datatype="*" nullmsg="运费不能为空"
                               value="<?php echo  !empty($productInfo) ? $productInfo[0]['shipping'] : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">零售价(msrp)：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="msrp" id="msrp"
                               placeholder="建议零售价"
                               value="<?php echo  !empty($productInfo) ? $productInfo[0]['msrp'] : '' ?>">
                    </div>
                </div>
                
            </div>
            <div class="promsg" style="display: none;">
                价格属性
            </div>
        </div>
    </div>

    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;详情描述</span>
                </h1>
            </div>
        </div>
        <div class="probody">
            <div class="procnt">
                <!--模板-->
             
                <div class="form-group clearfix">
                    <label class="col-sm-2 control-label">描述图片：</label>

                    <div class="col-sm-10">
                        <div>
                            <!-- <a href="javascript:void(0);" class="btn btn-default btn-sm from_local" lang="detail">从我的电脑选取</a> -->
                            <a href="javascript:void(0);" class="btn btn-default btn-sm image_url">图片外链</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/wish/ajaxUploadDirImage");?>', '', '');">图片目录上传</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/wish/ajaxUploadDirImage");?>', '', 'SP');">实拍目录上传</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/wish/ajaxUploadDirImage");?>', '', 'wish');">WISH目录上传</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/wish/ajaxUploadDirImage");?>', 'shui', '');">无水印目录上传</a>
                            &nbsp;&nbsp;
                            <a class="btn btn-xs btn-primary pic-del-all" title="全部删除"><i class="icon-trash"></i></a>
                            <b class="ajax-loading hide">图片上传中...</b>
                        </div>
                        <ul class="list-inline pic-detail">
                            <?php if(!empty($productInfo) && !empty($productInfo[0]['main_image'])):
                            
                              echo '<li><div><img src="'.$productInfo[0]['main_image'].'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="'.$productInfo[0]['main_image'].'" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                              
                              $imageArr = explode('|',$productInfo[0]['extra_image']);
                              
                              foreach($imageArr as $im){
                              	echo '<li><div><img src="'.$im.'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="'.$im.'" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                             
                              }
                            endif;?>
                        </ul>
                    </div>
                </div>
                
               <!-- 
                <div id="color_picture" style="display:none;">
                    <div class="form-group">
                        <label for="detail" class="col-sm-2 control-label">设置产品属性主图:</label>
    
                        <div class="col-sm-1">
    					  <a id="set_picture" href="javascript: void(0);" class="btn btn-primary btn-sm">设置图片</a>
                        </div>
                        <div class="col-sm-9">
    					  <a id="clear_picture" href="javascript: void(0);" class="btn btn-primary btn-sm"><i class="icon-trash"></i>清空图片</a>
                        </div>
                    </div>
                    <div id="colors_picturesArr">
                    
                    </div>
                </div>
                 -->
                 
                <!--自定义关联产品-->
                
                <div class="form-group">
                    <label for="detail" class="col-sm-2 control-label">详情描述:</label>

                    <div class="col-sm-10">
					
					<textarea name="detail" id="detail" class="form-control" >
						<?php echo !empty($productInfo) ? $productInfo[0]['product_description']: ''?>
					</textarea>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <div class="col-sm-2 col-sm-offset-2">
                        <a title="详情预览" class="detail_view blue" href="javascript: void(0);">
                            <i class="icon-eye-open bigger-130"></i>预览
                        </a>
                    </div>
                </div>
            </div>
            <div class="promsg" style="display: none;">
                详情描述
            </div>
        </div>
    </div>
    <div class="clearfix form-actions">
        <div class="col-md-offset-3 col-md-9">
        	<input type="hidden" name="oriange_sku" id="oriange_sku" value="<?php echo !empty($productInfo[0]['oriange_sku'])? $productInfo[0]['oriange_sku'] :'' ;?>"/>
            <!--action用来判断是提交到哪个操作-->
            <input type="hidden" name="action" id="action" value=""/>
            <input type="hidden" name="id" value="" id="id"/>
            <input type="hidden" name="old_token_id" value=""/>
            
            <?php if(empty($productInfo)):?>
            <button class="btn btn-success submit_btn" type="submit" name="saveToDraft">
                <i class="icon-ok bigger-110"></i>
                  	另存为草稿
            </button>
            <?php endif;?>
            
			<?php if(!empty($productInfo)):?>
			 <input type="hidden" name="old_product_id" value="<?php echo $productInfo[0]['productID']?>"/>
            <button class="btn btn-success submit_btn" type="submit" name="save">
                <i class="icon-ok bigger-110"></i>
               		 保存
            </button>
		   <?php endif;?>
<!--
            <button class="btn btn-primary submit_btn" type="submit" name="post">
                <i class="icon-ok bigger-110"></i>
                发布
            </button>
-->

            <!--保存为待发布-->
            <?php if(!empty($productInfo) && $productInfo[0]['product_type_status']!=2):?>
            <button class="btn btn-inverse submit_btn" type="submit" name="saveToPost">
                <i class="icon-ok bigger-110"></i>
                	保存为待发布
            </button>
            <?php endif;?>
            <!--修改并发布--> 
            <button class="btn btn-inverse submit_btn" type="submit" name="editAndPost">
                <i class="icon-ok bigger-110"></i>
                保存并发布
            </button>
            
           
          <!--  <button class="btn btn-reset" type="reset">
                <i class="icon-undo bigger-110"></i>重置
            </button>-->
        </div>
    </div>
</form>
<div class="hide" id="showDiv" style="overflow:scroll; width: 1000px; height: 500px;"></div>
<script type="text/javascript" src="<?php echo static_url('theme/common/jquery.dragsort-0.5.1.min.js');?>"></script>
<script type="text/javascript">

//编辑器调用
KindEditor.ready(function(K) {
    var editor = K.create("#detail",{
        "allowFileManager" : true,
        "allowImageManager" : true,
        "width":"100%",
        "height":"400px",
        "filterModel":false,//是否过滤html代码,true过滤
        "resizeType":"2",//是否可以改变editor大小，0：不可以   1：可改高   2：无限
        "items" :  ['source', '|', 'fullscreen', 'undo', 'redo',
            'cut', 'copy', 'paste', 'plainpaste',
            'wordpaste', '|', 'justifyleft', 'justifycenter',
            'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist',
            'indent', 'outdent', 'subscript', 'superscript', '|', 'selectall', '-', 'title',
            'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
            'strikethrough', 'removeformat', '|','image', 'multiimage', 'advtable', 'hr',
            'emoticons', 'link', 'unlink', 'table'],
        "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
        "afterBlur": function(){this.sync();} //必须，不然第一次提交不到
});
    
    var editor2 = K.editor({
        allowFileManager : false,
        uploadJson: '<?php echo admin_base_url('kindeditor/uploadWishToProject');?>'
    });


    //图片上传，路径应该要处理下
    K('.from_local').click(function(){
        editor2.loadPlugin('image', function() {
            editor2.plugin.imageDialog({
                showRemote: false,
                clickFn : function(url, title, width, height, border, align) {
            		var myli = '<li><div><img src="<?php echo site_url().'attachments/upload';?>'+url+'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="<?php echo site_url().'attachments/upload';?>'+url+'" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
            		K('.pic-detail').append(myli);
                    editor2.hideDialog();
                }
            });
        });
    });
    
    
});
//设置产品图片
$("#set_picture").click(function(){
	var color_arr = new Array();//颜色的数组
	$("input[name='color[]']").each(function(i){
        color_arr[i] = $(this).val();
     });
	$.unique(color_arr);//去除重复元素

	if(color_arr.length==1 && color_arr[0]==''){
	    alert('请补全sku的颜色尺寸信息');
	    return false;
	}
	var divimg = '';
	
	$.each(color_arr,function(ke,va){
		divimg += '<div class="form-group">';
		divimg += '<label class="col-sm-2"></label>';
		divimg += '<div class="col-sm-1">';
		divimg += va;
		divimg += '</div>';
		divimg += '<div class="col-sm-3">';
		divimg += '<input type="text" name="'+va+'" id="color_'+va+'" size="35" placeholder="输入图片链接,不填默认第一张图片为主图"/>';
		divimg += '<a href="javascript:void(0);" class="btn btn-default btn-sm images_url" data-id="'+va+'">确定</a>';
		divimg += '</div>';
		divimg += '<div class="col-sm-3" id="show_'+va+'">';
		
		divimg += '</div>';
		divimg += '</div>';
		
	});
	$("#colors_picturesArr").append(divimg);
});

//清空图片
$("#clear_picture").click(function(){
	$("#colors_picturesArr").empty();
})

$(document).on('click', '.images_url', function () {
   var color = $(this).attr('data-id');
   var img_url = $("#color_"+color).val();
   var imgshow = '<img src="'+img_url+'" style="width:100px;height:100px;"/>';
   $("#show_"+color).empty();
   $("#show_"+color).append(imgshow);
});

//点击账号复选框的时候，删除图片
$(".choose_account").click(function(){
   $(".pic-detail").empty();
});
//移开焦点后去空格处理
//$(document).on('blur', ':text, #subject', function () {
//    $(this).val($(this).val().trim());
//});
//$(document).on('blur', ':text, #Tags', function () {
//    $(this).val($(this).val().trim());
//});

$(function(){
	layer.use('extend/layer.ext.js');

	//去空格处理
	$("#subject").val($("#subject").val().trim());
	$("#Tags").val($("#Tags").val().trim());

	 //图片拖拽排序
    $(".pic-main, .pic-detail, .relate-list").dragsort({ dragSelector: "div",  placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });
    
    //删除所有主图
    $(document).on('click', '.pic-del-all', function () {
        if (confirm('确认删除全部图片吗？')) {
            $(this).closest('.form-group').find('ul').empty();
        }
    });
 
    //删除主图片
    $(document).on('click', '.pic-del', function () {
        //event.preventDefault();
        $(this).closest('li').remove();
    });
    
    //设置操作类型看是保存、发布或者是保存为待发布
    $('.submit_btn').click(function(e){

        $('#action').val($(this).attr('name'));

    })
    
    //账号改变的时候产品图片为空
   $("#choose_account").change(function(){
	  $(".pic-detail").empty();
   });

    //表单验证
    $('.validate_form').Validform({
        btnSubmit: '.submit_btn',
        btnReset: '.btn-reset',
        ignoreHidden: true,
        ajaxPost: true,
        callback: function (data) { //返回数据
           if (data.status) {
               if (data.data){
            	   var productId = data.data.id || ''; //产品ID，回传过来保存下，不然一直都是新增
                   if (productId){
                	   $('#id').val(productId);
                   }
               }
               showxbtips(data.info);
           } else {
               showxbtips(data.info, 'alert-warning');
           }
        }
    });

    $("#generate_sku_check").click(function(){
        var generate_sku_parent = $("#generate_sku_parent").val();
        var generate_sku_color = $("#generate_sku_color").val();
        var generate_sku_size = $("#generate_sku_size").val();
        var sku_pre = $("#prefix").val();//sku前缀
        if(sku_pre !=''){
            sku_pre =sku_pre+'*';
        }
        var sku_buf = $("#buffer").val();//sku后缀
        var batch_price = $("#batch_price").val();//批量加价格
        if((generate_sku_parent=='')||(generate_sku_color=='')||(generate_sku_size=='')){
            alert('数据不能为空');
        }
        var input_row = '';
        $("#sku_info").empty();
        $.ajax({
            url: '<?php echo admin_base_url('publish/wish/generate_sku')?>',
            data: {'generate_sku_parent': generate_sku_parent, 'generate_sku_color': generate_sku_color,'generate_sku_size':generate_sku_size},
            type: 'POST',
            async: false,
            cache: false,
            dataType: 'JSON',
            success: function (data) {
               /* $("#generate_sku_parent").val('');
                $("#generate_sku_color").val('');
                $("#generate_sku_size").val('');*/
                for(var i=0;i<data.data.length;i++){
                    input_row = '<div class="form-group">'
                    + '<label class="col-sm-1 control-label"></label>'
                    + '<div class="col-sm-2">'
                    + '<input type="text" class="form-control" name="sku[]" id="sku"  datatype="*" nullmsg="sku不能为空" value="'+sku_pre+data.data[i].sku+sku_buf+'" />'
                    + '</div>'
                    + '<div class="col-sm-2">'
                    + '<input type="text" class="form-control" name="color[]" id="color" value="'+data.data[i].color+'" />'
                    + '</div>'
                    + '<div class="col-sm-2">'
                    + '<input type="text" class="form-control" name="size[]" id="size" value="'+data.data[i].size+'" />'
                    + '</div>'
                    + '<div class="col-sm-2">'
                    + '<input type="text" class="form-control" name="prices[]" id="prices" value="'+batch_price+'"  />'
                    + '</div>'
                    + '<div class="col-sm-2">'
                    + '<a class="btn btn-success btn-sm del_row">删除</a>'
                    + '</div>'
                    + '</div>';
                    $("#sku_info").append(input_row);
                }

            }
        });

        $('#myModal').modal('toggle');
    });

    //模糊匹配sku并添加
    $("#queding").click(function(){
        var sku_search = $("#sku_search").val();
        var sku_pre = $("#prefix").val();//sku前缀
        var sku_buf = $("#buffer").val();//sku后缀
        var sku_new_pre = $("#newpirex").val();//sku新前缀
        var batch_price = $("#batch_price").val();//批量加价格
        var sku_ori = $("#oriange_sku").val();//原sku
        var color_arr = new Array();//颜色的数组
        var size_arr = new Array();//尺寸的数组
        var price_arr = new Array();//价格的数组
        var pricess = 0;
        
        var is_draft = <?php echo !empty($productInfo)? '1' : '0' ?>;
        
        if( sku_search.length>0 && sku_ori.length>0 ){//查询的sku不为空并且原sku隐藏值不为空，说明更换sku

           $("input[name='color[]']").each(function(i){
              
              color_arr[i] = $(this).val();
           });
           $("input[name='size[]']").each(function(i){
               size_arr[i] = $(this).val();
            });
           $("input[name='prices[]']").each(function(i){
        	   pricess = 0;
               if(batch_price !== $(this).val()){
            	   pricess = batch_price;
               }else{
            	   pricess = $(this).val();
               }
        	   price_arr[i] = pricess;
            });
           
           $.ajax({
               url: '<?php echo admin_base_url('publish/wish/search_sku')?>',
               data:{'sku_search':sku_search,'sku_new_pre':sku_new_pre}, 
               type: 'POST',
               async:false,
   			   cache:false,
               dataType: 'JSON',
               success: function(data){
                   if(data.status=='1'){
   					$("#sku_info").empty();
   					$.each(data.skus,function(i,val){
   						var input_row = '';
   						var color = '';
   						var size = '';
   						var price = '';
   						if(color_arr.hasOwnProperty(i)){
   							color = color_arr[i];
   	   				    }else{
							color = '';
   	   	   				}
   						if(size_arr.hasOwnProperty(i)){
   							size = size_arr[i];
   	   				    }else{
   	   	   				   size = '';
   	   	   				}
   						if(price_arr.hasOwnProperty(i)){
   							price = price_arr[i];
   	   				    }else{
   	   	   				    price = '';
   	   	   				}
   						
   						input_row = '<div class="form-group">'
   				            + '<label class="col-sm-1 control-label"></label>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="sku[]" id="sku"  datatype="*" nullmsg="sku不能为空" value="'+sku_pre+val+sku_buf+'" />'
   				            + '</div>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="color[]" id="color" value="'+color+'" />'
   				            + '</div>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="size[]" id="size" value="'+size+'" />'
   				            + '</div>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="prices[]" id="prices" value="'+price+'" />'
   				            + '</div>'
   				            + '<div class="col-sm-2">'
   				            + '<a class="btn btn-success btn-sm del_row">删除</a>'
   				            + '</div>'
   				            + '</div>';
   			            $("#sku_info").append(input_row);
   				    });
   					$("#oriange_sku").val(sku_search);
                   }else{
   					layer.alert('没有搜索到该sku',8);
                   }
               }
           });
           return false;
        }
        if(sku_search.length==0 && sku_ori.length>0 ){//如果查询的sku为空等于隐藏的sku并且原sku隐藏值不为空，说明只是想更换sku前后缀

       	  $("input[name='color[]']").each(function(i){
             color_arr[i] = $(this).val();
          });
          $("input[name='size[]']").each(function(i){
              size_arr[i] = $(this).val();
           });
          $("input[name='prices[]']").each(function(i){
       	   price_arr[i] = $(this).val();
          });

          $.ajax({
              url: '<?php echo admin_base_url('publish/wish/search_sku')?>',
              data:{'sku_search':sku_ori,'sku_new_pre':sku_new_pre}, 
              type: 'POST',
              async:false,
  			  cache:false,
              dataType: 'JSON',
              success: function(data){
                  if(data.status=='1'){
  					$("#sku_info").empty();
  					$.each(data.skus,function(i,val){
  						var input_row = '';
  						
   						var color = '';
   						var size = '';
   						var price = '';
   						if(color_arr.hasOwnProperty(i)){
   							color = color_arr[i];
   	   				    }else{
							color = '';
   	   	   				}
   						if(size_arr.hasOwnProperty(i)){
   							size = size_arr[i];
   	   				    }else{
   	   	   				   size = '';
   	   	   				}
   						if(price_arr.hasOwnProperty(i)){
   							price = price_arr[i];
   	   				    }else{
   	   	   				    price = '';
   	   	   				}
  						input_row = '<div class="form-group">'
  				            + '<label class="col-sm-1 control-label"></label>'
  				            + '<div class="col-sm-2">'
  				            + '<input type="text" class="form-control" name="sku[]" id="sku"  datatype="*" nullmsg="sku不能为空" value="'+sku_pre+val+sku_buf+'" />'
  				            + '</div>'
  				            + '<div class="col-sm-2">'
  				            + '<input type="text" class="form-control" name="color[]" id="color" value="'+color[i]+'" />'
  				            + '</div>'
  				            + '<div class="col-sm-2">'
  				            + '<input type="text" class="form-control" name="size[]" id="size" value="'+size[i]+'" />'
  				            + '</div>'
  				            + '<div class="col-sm-2">'
  				            + '<input type="text" class="form-control" name="prices[]" id="prices" value="'+price[i]+'" />'
  				            + '</div>'
  				            + '<div class="col-sm-2">'
  				            + '<a class="btn btn-success btn-sm del_row">删除</a>'
  				            + '</div>'
  				            + '</div>';
  			            $("#sku_info").append(input_row);
  				    });
                  }else{
  					layer.alert('没有搜索到该sku',8);
                  }
              }
          });
        	return false;
        }
       
        if(sku_search==''){
 		 alert('请输入要搜索的sku');
  		 $("#sku_search").focus();
  		 return false;
        }

    	$.ajax({
            url: '<?php echo admin_base_url('publish/wish/search_sku')?>',
            data:{'sku_search':sku_search,'sku_new_pre':sku_new_pre},
            type: 'POST',
            async:false,
			cache:false,
            dataType: 'JSON',
            success: function(data){
                if(data.status=='1'){
					$("#sku_info").empty();
					$.each(data.skus,function(i,val){
						var input_row = '';
						input_row = '<div class="form-group">'
				            + '<label class="col-sm-1 control-label"></label>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="sku[]" id="sku"  datatype="*" nullmsg="sku不能为空" value="'+sku_pre+val+sku_buf+'" />'
				            + '</div>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="color[]" id="color" value="" />'
				            + '</div>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="size[]" id="size" value="" />'
				            + '</div>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="prices[]" id="prices" value="'+batch_price+'" />'
				            + '</div>'
				            + '<div class="col-sm-2">'
				            + '<a class="btn btn-success btn-sm del_row">删除</a>'
				            + '</div>'
				            + '</div>';
			            $("#sku_info").append(input_row);
				    });
					change_sku_description(sku_search);  
					$("#oriange_sku").val(sku_search);
                }else{
					layer.alert('没有搜索到该sku',8);
                }
            }
        });
    	
    });
 	
    //添加一个sku
    $('#add_row').on('click', function () {
        var input_row = '<div class="form-group">'
            + '<label class="col-sm-1 control-label"></label>'
            + '<div class="col-sm-2">'
            + '<input type="text" class="form-control" name="sku[]" id="sku" placeholder="请输入产品sku" datatype="*" nullmsg="sku不能为空" value="" />'
            + '</div>'
            + '<div class="col-sm-2">'
            + '<input type="text" class="form-control" name="color[]" id="color" value="" />'
            + '</div>'
            + '<div class="col-sm-2">'
            + '<input type="text" class="form-control" name="size[]" id="size" value="" />'
            + '</div>'
            + '<div class="col-sm-2">'
            + '<input type="text" class="form-control" name="prices[]" id="prices" value="" />'
            + '</div>'
            + '<div class="col-sm-2">'
            + '<a class="btn btn-success btn-sm del_row">删除</a>'
            + '</div>'
            + '</div>';
        $(this).closest('.form-group').before(input_row);
    });

    //删除自定义属性
    $(document).on('click', '.del_row', function () {
        $(this).closest('.form-group').remove();
    });
    
})

/**
 *输入图片外链 
 */
 $(".image_url").click(function(){
	 layer.prompt({title: '请输入图片外链,并确认',type: 0}, function(pass, index, el){
	        if (pass.trim() == ''){
	            layer.close(index);
	            return false;
	        }
	        layer.close(index);
	        $('.ajax-loading').removeClass('hide');

	        var url = pass;
	        var liStr = '';
	        liStr += '<li><div><img src="' + url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="' + url + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
	        $('.pic-detail').append(liStr);
	        layer.msg('图片上传成功', 2, -1);
	        $('.ajax-loading').addClass('hide');
	       
	    });
 });

/**
 * 勾选账号的时候把sku后缀显示出来
 */
$('.choose_account').click(function(){
	
    var check = $(this).is(':checked');
    var account = $(this).val();
    
    if(check==false){
    	$("#buffer").val('');
		$("#prefix").attr("disabled",false); 
    	$("#buffer").attr("disabled",false); 
    	$("#newpirex").attr("disabled",false);
    }else{
    	$.ajax({
    		url: '<?php echo admin_base_url('publish/wish/getPublishCode')?>',
            data: 'account='+account,
            type: 'POST',
            async:false,
    		cache:false,
            dataType: 'JSON',
            success: function(data){
    		$("#buffer").val('');
    		$("#prefix").attr("disabled",false); 
        	$("#buffer").attr("disabled",false); 
        	$("#newpirex").attr("disabled",false); 
        	
                if(data.status==1){//老帐号
    				$("#buffer").val(data.code);
    				$("#newpirex").attr("disabled",true); 
                }else{//新账号
                	$("#prefix").attr("disabled",true); 
                	$("#buffer").attr("disabled",true); 
                	$("#newpirex").attr("disabled",false); 
                }
            }
        });
    }
    
});

$('#add_mul_pic').click(function () {
    var value = $('#choose_type').val();
    var info_arr = new Array;
    $('input[name="'+value+'[]"]').each(function(){
        if($(this).val() !=''){
            info_arr.push($(this).val())
        }
    });

    var n = {},r=[]; //n为hash表，r为临时数组
    for(var i = 0; i < info_arr.length; i++) //遍历当前数组
    {
        if (!n[info_arr[i]]) //如果hash表中没有当前项
        {
            n[info_arr[i]] = true; //存入hash表
            r.push(info_arr[i]); //把当前数组的当前项push到临时数组里面
        }
    }
    var html_add ='';
    $('#mul_pic_info').empty();
    for(var i = 0; i < r.length; i++){

        html_add =' <div class="form-group"> ' +
        '<label class="control-label col-sm-5">'+r[i]+'：<input name="mul_color_one['+value+']['+r[i]+'][]"  type="text" /> <a class="set_color btn btn-primary btn-sm" onclick="set_color(this)" href="javascript: void(0);">设置图片</a></label> ' +
        '<div class="col-sm-2"> ' +
        '</div> ' +
        '</div>';
        $('#mul_pic_info').append(html_add);
    }

});


function set_color(e){
    //<img src="" title="暂无" style="width:100px;height:100px;"/>
    var url_info = $(e).prev().val();
    var tt = $(e).parent().next();
    if(url_info==''){
        tt.empty();
    }else{
        tt.empty();
        tt.append('<img src="'+url_info+'" title="暂无" style="width:100px;height:100px;"/>')
    }
}

/**
 * 按SKU目录添加图片
 * @param obj
 */
function addDir(obj, url, token_id, opt){
	 //存取账号和图片url的关联数组
    var tp_host = new Array();
    var host_url =[]; 
    <?php 
      foreach($account as $k => $v):
      	echo "tp_host['".$k."']='".$v['photo_url']."';";
      endforeach;
    ?>
	//获取账户所对应的账户名
   
     $('input[name="choose_account[]"]:checked').each(function(){
    	 host_url.push($(this).val());
    }); 
     if(host_url==''){
    	 alert('请勾选至少一个账号');
    	 return false;
     }

     var flag=false;
     $.each(host_url,function(index,account){ 
    	 if(tp_host[account]==''){
 	    	alert(account+'该账户下的图片服务器网址为空,请重新勾选');
 	    	flag = true;
 	     }
    });
	if(flag){//判断是否有账号的服务器网址为空
	  return false;
	}
    
    layer.prompt({title: '请输入SKU名称，并确认',type: 0}, function(pass, index, el){
        if (pass.trim() == ''){
            layer.close(index);
            return false;
        }
        layer.close(index);
        $('.ajax-loading').removeClass('hide');

        //开始异步获取并上传文件
        $.ajax({
            url: url,
            data: 'dirName='+pass+'&shui='+token_id+'&opt='+opt+'&host_url='+tp_host[host_url[0]],
            type: 'POST',
            async:false,
			cache:false,
            dataType: 'JSON',
            success: function(data){
                $('.ajax-loading').addClass('hide');
                if (data.status){
                    if (data.data){ //说明有成功的，成功的添加到里边去
                        var liStr = '';
                        var h_url = '';
                        
                        $.each(data.data, function(index, el){
                            
                            h_url = el.replace(':81','');

							h_url = h_url.replace('image-resize/100x-x75','image');
                            
                            liStr += '<li><div><img src="' + (el.replace('getSkuImageInfo','getSkuImageInfo-resize')) + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="' + h_url + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                        });
                        $(obj).closest('div.form-group').find('ul').append(liStr);
                        $("#color_picture").show();
                        layer.msg('图片上传成功', 2, -1);
                    }
                    if (data.info != ''){ //说明有失败的，失败的再显示出来
                        var msg = '';
                        $.each(data.info, function(index, el){
                            msg += el+"<br/>";
                        });
                        layer.alert(msg, 3, !1);
                    }
                }else {
                    //layer.msg('图片上传失败,'+data.info, 2, -1);
                    layer.alert('<font color="red">图片上传失败,</font>'+data.info, 3, !1);
                }
            }
        });
    });
}
/**
 * sku变化以后，更改产品描述内容为sku的英文内容
 * 先清除文本域和编辑器里的内容
 */
function change_sku_description(sku){

	var return_content = '';

	$("#detail").empty();

	KindEditor.instances[0].html('');

	$.ajax({
        url: '<?php echo admin_base_url('publish/wish/getSkuInfoLike')?>',
        data:'sku_search='+sku, 
        type: 'POST',
        async:false,
		cache:false,
        dataType: 'JSON',
        success: function(data){
            if(data.skuInfo!==''){
				$.each(data.skuInfo,function(i,val){
					return_content+=val;
			    });
				KindEditor.instances[0].html(return_content);
				$("#detail").html(return_content);
            }else{
				layer.alert('没有搜索到该sku的英文资料',8);
            }
        }
    });
	

}
</script>