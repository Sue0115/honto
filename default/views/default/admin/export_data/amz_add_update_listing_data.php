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

    
 <form action="<?php echo admin_base_url('amz/amzListingTemplate/listingDataShow'); ?>" class="form-horizontal validate_form" method="post">
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
                
              	   <label class="col-sm-1 control-label" >请输入产品sku:</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="sku_search" value="<?php echo (isset($data) && !empty($data[0]->same_product_id) && strrpos($data[0]->same_product_id,'*')&& strrpos($data[0]->same_product_id,'[')) ? substr(substr($data[0]->same_product_id,((strrpos($data[0]->same_product_id,'*')?strrpos($data[0]->same_product_id,'*'):-1)+1)),0,strrpos(substr($data[0]->same_product_id,((strrpos($data[0]->same_product_id,'*')?strrpos($data[0]->same_product_id,'*'):-1)+1)),'[')) : $data[0]->same_product_id;?>" />  
                    </div>
                  
                    <label class="col-sm-1 control-label">修改颜色:</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="batch_color" value="" />
                    </div>
                    
                    <label class="col-sm-1 control-label">修改尺寸:</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="batch_size" value="" />
                    </div>
                    
                    <label class="col-sm-1 control-label">添加sku前缀:</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="prefix" value="" />
                    </div>
                    
                    <label class="col-sm-1 control-label" >添加sku后缀:</label>
                    <div class="col-sm-1">
                       <input type="text" class="form-control"  id="buffer" value="" />
                    </div>
                    <div class="col-sm-1">
                       <input type="button" class="btn btn-primary btn-sm"  id="queding" value="确定" size="10"/>
                    </div>
                </div>
                
              
              
              
                  <div class="form-group">
	                    <label class="col-sm-2 control-label" style="margin-left:30px;"><span class="red">*</span>SKU：</label>
	                    
	                    <label class="col-sm-2 control-label" style="margin-left:50px;">颜色(color_name)：</label>
	                    
	                    <label class="col-sm-2 control-label">尺寸(size_name)：</label>
	                    
                        <label class="col-sm-2 control-label" style="margin-left:-20px;">Product ID：</label>
	                    
	                    <label class="col-sm-2 control-label">Parent Child：</label>
	                    
	            </div>
	          
	            <div id="sku_info">
	               <?php if(empty($data)):?>
		              <div class="form-group">
	                
	                 	<label class="col-sm-1 control-label"></label>
	         
	                     <div class="col-sm-2">
		                    <input type="text" class="form-control" name="sku[]" id="sku" placeholder="请输入产品sku" datatype="*" nullmsg="sku不能为空" value="" />
		                </div>
		                    
	                    <div class="col-sm-2">
	                        <input type="text" class="form-control" name="color[]" id="color" value="" />
	                    </div>
	                    
	                    <div class="col-sm-2">
	                        <input type="text" class="form-control" name="size[]" id="size" value="" />
	                    </div>
	                    
	                    
	                    
	                    <div class="col-sm-2">
	                        <input type="text" class="form-control" name="external_product_id" id="external_product_id" value="" />
	                    </div>
	                    
	                    <div class="col-sm-2">
	                    <select class="form-control" name ='parent_child[]' >
    	                    <option value="" >请选择</option>
    	                    <option value="parent" >parent</option>
    	                    <option value="child" >child</option>
    	                    <option value="parent_child" >parent_child</option>
	                    </select>
	                        
	                    </div>
	                    
	                    <div class="col-sm-1">
			                        <a class="btn btn-success btn-sm del_row">删除</a>
			                    </div>
	                  </div>
	               <?php else:?>
		               <?php foreach($data as $val):$pf=(array)$val;?>
			               <div class="form-group">
			                
			                 	<label class="col-sm-1 control-label"></label>
			         
			                     <div class="col-sm-2">
				                    <input type="text" class="form-control" name="sku[]" id="sku"  datatype="*" nullmsg="sku不能为空"
				                               value="<?php echo $pf['item_sku']?>" />
				                </div>
				                    
			                    <div class="col-sm-2">
			                        <input type="text" class="form-control" name="color[]" id="color" value="<?php echo $pf['color_name']?>" />
			                    </div>
			                    
			                    <div class="col-sm-2">
			                        <input type="text" class="form-control" name="size[]" id="size" value="<?php echo $pf['size_name']?>" />
			                    </div>
			                    
			                    
			                    
			                    <div class="col-sm-2">
			                        <input type="text" class="form-control" name="external_product_id[]" id="external_product_id" value="<?php echo $pf['external_product_id']?>" />
			                    </div>
			                    
			                   <div class="col-sm-2">
	                                <select class="form-control" name ='parent_child[]' >
    	                    <option value="" >请选择</option>
    	                    <option value="parent" <?php echo ($pf['parent_child']=='parent')?'selected':''?>>parent</option>
    	                    <option value="child" <?php echo ($pf['parent_child']=='child')?'selected':''?>>child</option>
    	                    <option value="parent_child" <?php echo ($pf['parent_child']=='parent_child')?'selected':''?>>parent_child</option>
	                    </select>
	                          </div>
			                    
			                    <div class="col-sm-1">
			                        <a class="btn btn-success btn-sm del_row">删除</a>
			                    </div>
			                  </div>
		               <?php endforeach;?>
	               <?php endif;?>
	            </div>
                <div class="form-group">
                    <div class="col-sm-offset-4">
                        <label class="col-sm-2 control-label"><a class="btn btn-primary btn-sm" href="javascript: void(0);" id="add_row">添加一个sku</a></label>
                        <label class="col-sm-2 control-label"> <a class="btn btn-primary btn-sm" href="javascript: void(0);" id="before_add_row">前面添加一个sku</a></label>
                    </div>
                   
                </div>
               
            </div>
            <div class="promsg" style="display: none;">
               	 产品属性
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
                    <label class="col-sm-2 control-label"><span class="red">*</span>价格(sale_price)：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="sale_price" id="sale_price"
                               placeholder="产品价格" datatype="*" nullmsg="价格不能为空"
                               value="<?php echo  (isset($data) && !empty($data)) ? trim($data[0]->sale_price) : '' ?>">
                    </div>
                    <label class="col-sm-2 control-label"><span class="red">*</span>价格(standard_price)：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="standard_price" id="standard_price"
                               placeholder="产品价格" datatype="*" nullmsg="价格不能为空"
                               value="<?php echo  (isset($data) && !empty($data)) ? trim($data[0]->standard_price) : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="red">*</span>价格(list_price)：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="list_price" id="list_price"
                               placeholder="产品价格" datatype="*" nullmsg="价格不能为空"
                               value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->list_price) : '' ?>">
                    </div>
                    <label class="col-sm-2 control-label"><span class="red">*</span>上架数量(quantity)：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="quantity" id="quantity"
                               placeholder="上架数量" datatype="*" nullmsg="上架数量不能为空"
                               value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->quantity) : '999' ?>">
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
                    <span>&nbsp;基本信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="form-group">
                <div class="form-group">
                    <label for="subject" class="control-label col-sm-2"><span class="red">*</span>名称(item_name)：</label>

                    <div class="col-sm-4">
                        <textarea name="subject" id="subject" class="form-control" placeholder="名称" datatype="*1-128"
                                  nullmsg="名称不能为空" errormsg="请输入长度在1-128之间的英文字符"
                                  maxlength="128"><?php echo (isset($data) && !empty($data)) ? trim($data[0]->item_name) : ''?>
                        </textarea>
                    </div>
               
                    <label class="col-sm-2 control-label">Product ID Type：</label>

                    <div class="col-sm-3">                       
                        <textarea name="external_product_id_type" id="external_product_id_type" class="form-control" placeholder="external_product_id_type" >
						 <?php echo (isset($data) && !empty($data)) ? trim($data[0]->external_product_id_type) : ''?>
					    </textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Brand Name：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="brand_name" id="brand_name" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->brand_name) : ''?>" />
                    </div>
               
                    <label class="col-sm-2 control-label">Item Type：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="item_type" id="item_type" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->item_type) : ''?>" />
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">currency：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="currency" id="currency" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->currency) : ''?>" />
                    </div>

                    <label class="col-sm-2 control-label">bullet_point1：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="bullet_point1" id="bullet_point1" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->bullet_point1) : ''?>" />
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">bullet_point2：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="bullet_point2" id="bullet_point2" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->bullet_point2) : ''?>" />
                    </div>

                    <label class="col-sm-2 control-label">bullet_point3：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="bullet_point3" id="bullet_point3" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->bullet_point3) : ''?>" />
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">bullet_point4：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="bullet_point4" id="bullet_point4" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->bullet_point4) : ''?>" />
                    </div>
                    <label class="col-sm-2 control-label">bullet_point5：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="bullet_point5" id="bullet_point5" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->bullet_point5) : ''?>" />
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">generic_keywords1：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="generic_keywords1" id="generic_keywords1" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->generic_keywords1) : ''?>" />
                    </div>
                    <label class="col-sm-2 control-label">generic_keywords2：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="generic_keywords2" id="generic_keywords2" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->generic_keywords2) : ''?>" />
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">generic_keywords3：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="generic_keywords3" id="generic_keywords3" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->generic_keywords3) : ''?>" />
                    </div>
                    <label class="col-sm-2 control-label">generic_keywords4：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="generic_keywords4" id="generic_keywords4" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->generic_keywords4) : ''?>" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">generic_keywords5：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="generic_keywords5" id="generic_keywords5" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->generic_keywords5) : ''?>" />
                    </div>
                    <label class="col-sm-2 control-label">Parent Sku：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="parent_sku" id="parent_sku" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->parent_sku) : ''?>" />
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Relationship Type：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="relationship_type" id="relationship_type" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->relationship_type) : ''?>" />
                    </div>
                    <label class="col-sm-2 control-label">Variation Theme：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="variation_theme" id="variation_theme" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->variation_theme) : ''?>" />
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Department：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="department_name" id="department_name" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->department_name) : ''?>" />
                    </div>
                    <label class="col-sm-2 control-label">Fit Type：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="fit_type" id="fit_type" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->fit_type) : ''?>" />
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    
                    <label class="col-sm-2 control-label">NeckStyle：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="neck_style" id="neck_style" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->neck_style) : ''?>" />
                    </div>                
                    <label class="col-sm-2 control-label">Sleeve Type：</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="sleeve_type" id="sleeve_type" value="<?php echo (isset($data) && !empty($data)) ? trim($data[0]->sleeve_type) : ''?>" />
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
<!--                             <a href="javascript:void(0);" class="btn btn-default btn-sm image_url">图片外链</a> -->
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("amz/amzListingTemplate/ajaxUploadDirImage");?>', '', '');">图片目录获取</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("amz/amzListingTemplate/ajaxUploadDirImage");?>', '', 'SP');">实拍图片获取</a>
                            &nbsp;&nbsp;
                            <a class="btn btn-xs btn-primary pic-del-all" title="全部删除"><i class="icon-trash"></i></a>
                            <b class="ajax-loading hide">图片上传中...</b>
                        </div>
                        <ul class="list-inline pic-detail">
                           <?php foreach($data as $key=>$val){if($val->parent_child=='parent'){$data[0] = $val;}}if(!empty($data) && !empty($data[0]->main_image_url)):
                           $color  = strrpos($data[0]->main_image_url,'*')?substr($data[0]->main_image_url,strrpos($data[0]->main_image_url,'*')+1):'';
                           $imgSrc = strrpos($data[0]->main_image_url,'*')?substr($data[0]->main_image_url,0,strrpos($data[0]->main_image_url,'*')):$data[0]->main_image_url;
                              echo '<li><div><input type="text" name="img_color[]" value="'.$color.'"><img src="'.$imgSrc.'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="'.$imgSrc.'" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                              $imageArr= array();
                              $dataArr = array();
                              $dataArr = (array)$data[0];
                              for($i=1;$i<=8;$i++){
                                  if($dataArr['other_image_url'.$i]){
                                      $imageArr[]=$dataArr['other_image_url'.$i];
                                  }
                              }
                             
                              foreach($imageArr as $im){
                                  $otherColor  = '';
                                  $otherImgSrc = '';
                                  $otherColor  = strrpos($im,'*')?substr($im,strrpos($im,'*')+1):'';
                                  $otherImgSrc = strrpos($im,'*')?substr($im,0,strrpos($im,'*')):$im;
                              	echo '<li><div><input type="text" name="img_color[]" value="'.$otherColor.'" style="width:100px;height:30px;" title="图片颜色" placeholder="图片颜色"><img src="'.$otherImgSrc.'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="'.$otherImgSrc.'" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                             
                              }
                            endif;?> 
                        </ul>
                    </div>
                </div>

                <!--自定义关联产品-->
                
                <div class="form-group">
                    <label for="detail" class="col-sm-2 control-label">详情描述:</label>

                    <div class="col-sm-10">
					
					<textarea name="detail" id="detail" class="form-control" >
						<?php echo (isset($data) && !empty($data)) ? trim($data[0]->product_description): ''?>
					</textarea>
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
        	<input type="hidden" name="oriange_sku" id="oriange_sku" value="<?php echo (isset($data) && !empty($data[0]->same_product_id) && strrpos($data[0]->same_product_id,'*')&& strrpos($data[0]->same_product_id,'[')) ? substr(substr($data[0]->same_product_id,((strrpos($data[0]->same_product_id,'*')?strrpos($data[0]->same_product_id,'*'):-1)+1)),0,strrpos(substr($data[0]->same_product_id,((strrpos($data[0]->same_product_id,'*')?strrpos($data[0]->same_product_id,'*'):-1)+1)),'[')) : $data[0]->same_product_id;?>"/>
            <!--action用来判断是提交到哪个操作-->
<!--             <input type="hidden" name="action" id="action" value=""/> -->
<!--             <input type="hidden" name="id" value="" id="id"/> -->
<!--             <input type="hidden" name="old_token_id" value=""/> -->
            
                        
			 <input type="hidden" name="act" value="add_update"/>
            <button class="btn btn-success submit_btn" type="submit" name="save" onclick="return checkSubmit()">
                <i class="icon-ok bigger-110"></i>
               		 保存
            </button>
      
        </div>
    </div>
</form>
<div class="hide" id="showDiv" style="overflow:scroll; width: 1000px; height: 500px;"></div>
<script type="text/javascript" src="<?php echo static_url('theme/common/jquery.dragsort-0.5.1.min.js');?>"></script>
<script type="text/javascript">
function checkSubmit(){
	sale_price,standard_price,list_price,quantity,subject
	var sale_price     = $("#sale_price").val();
	var standard_price = $("#standard_price").val();
	var list_price     = $("#list_price").val();
	var quantity       = $("#quantity").val();
	var subject        = $("#subject").val();
	var flag           = 0;
	  $("input[name='sku[]']").each(function(i){
	          if( $(this).val()==""){
		          flag=1;	        	  
	          }
	  });
    if(flag==1){
    	layer.alert('sku不能为空',8);return false;
    }
	if(sale_price=="" || standard_price=="" || list_price=="" || quantity=="" || subject==""){
		layer.alert('sale_price,standard_price,list_price,上架数量,item_name不能为空',8);return false;
	}
	
}
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
        + '<input type="text" class="form-control" name="external_product_id[]" id="external_product_id" value="" />'
        + '</div>'
        + '<div class="col-sm-2">'
        + '<select class="form-control" name ="parent_child[]" ><option value="" >请选择</option><option value="parent" >parent</option><option value="child" selected="selected">child</option><option value="parent_child" >parent_child</option></select>'
        + '</div>'
        + '<div class="col-sm-1">'
        + '<a class="btn btn-success btn-sm del_row">删除</a>'
        + '</div>'
        + '</div>';
    $('#sku_info').append(input_row);
});

$('#before_add_row').on('click', function () {

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
        + '<input type="text" class="form-control" name="external_product_id[]" id="external_product_id" value="" />'
        + '</div>'
        + '<div class="col-sm-2">'
        + '<select class="form-control" name ="parent_child[]" ><option value="" >请选择</option><option value="parent" >parent</option><option value="child" selected="selected">child</option><option value="parent_child" >parent_child</option></select>'
        + '</div>'
        + '<div class="col-sm-1">'
        + '<a class="btn btn-success btn-sm del_row">删除</a>'
        + '</div>'
        + '</div>';
    $('#sku_info').prepend(input_row);
});

//删除自定义属性
$(document).on('click', '.del_row', function () {
    $(this).closest('.form-group').remove();
});


$(function(){
 	layer.use('extend/layer.ext.js');
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
 	
	//模糊匹配sku并添加
    $("#queding").click(function(){
        var sku_search = $("#sku_search").val();
        var batch_color = $("#batch_color").val();//批量加颜色
        var batch_size = $("#batch_size").val();//批量加尺寸        
        var sku_pre = $("#prefix").val();//sku前缀
        var sku_buf = $("#buffer").val();//sku后缀
        var sku_ori = $("#oriange_sku").val();//原sku
        var color_arr = new Array();//颜色的数组
        var size_arr = new Array();//尺寸的数组   
        var external_product_id_arr = new Array(); //Product ID
        var parent_child_arr = new Array(); //parent and child   
      
        if( sku_search.length>0 && sku_ori.length>0 &&  sku_search!=sku_ori){
           $("input[name='color[]']").each(function(i){
        	   colorss = 0;
               if(batch_color !== $(this).val()){
            	   colorss = batch_color;
               }else{
            	   colorss = $(this).val();
               }
              color_arr[i] = colorss;
           });
           $("input[name='size[]']").each(function(i){
        	   sizess = 0;
               if(batch_size !== $(this).val()){
            	   sizess = batch_size;
               }else{
            	   sizess = $(this).val();
               }
               size_arr[i] = sizess;
            });
           $("input[name='external_product_id[]']").each(function(i){
        	   external_product_id_arr[i] = $(this).val();
            });
           $("select[name='parent_child[]']").each(function(i){
        	   parent_child_arr[i] = $(this).val();
            });
           
           
           $.ajax({
               url: '<?php echo admin_base_url('amz/amzListingTemplate/search_sku');?>',
               data:'sku_search='+sku_search, 
               type: 'POST',
               async:false,
   			   cache:false,
               dataType: 'JSON',
               success: function(data){
                   if(data.status=='1'){
   					$("#sku_info").empty();
   					
   	   				// 添加product ID
   					var add_product_id_arr = new Array();
   		           var num =data.skus.length; 
   		           $.ajax({
   		               url: '<?php echo admin_base_url('amz/amzListingTemplate/search_product_id');?>',
   		               data:'num='+num, 
   		               type: 'POST',
   		               async:false,
   		   			   cache:false,
   		               dataType: 'JSON',
   		               success: function(data){
   		           	        if(data.status=='1'){
   		                   		$.each(data.info,function(i,val){
   		                   			add_product_id_arr[i] = val;
   		                   		});
   		               			        	       					
   			                }
   		                }
   		           });  		          
   					$.each(data.skus,function(i,val){
   						var input_row = '';
   						var color = '';
   						var size = '';
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
//    						if(add_product_id_arr.hasOwnProperty(i)){
//    							external_product_id = add_product_id_arr[i];
//    	   				    }else{
//    	   	   				    external_product_id = '';
//    	   	   				}
   						if(parent_child_arr.hasOwnProperty(i)){
   							parent_child = parent_child_arr[i];
   	   				    }else{
   	   	   				    parent_child = '';
   	   	   				}   						
   						input_row = '<div class="form-group">'
   				            + '<label class="col-sm-1 control-label"></label>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="sku[]"  placeholder="请输入产品sku" datatype="*" nullmsg="sku不能为空" value="'+sku_pre+val+sku_buf+'" />'
   				            + '</div>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="color[]" value="'+color+'" />'
   				            + '</div>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="size[]" value="'+size+'" />'
   				            + '</div>'
   				            + '<div class="col-sm-2">'
   				            + '<input type="text" class="form-control" name="external_product_id[]" value="" />'//'+external_product_id+'
   				            + '</div>'
      				         + '<div class="col-sm-2">'
 				            + '<select class="form-control" name ="parent_child[]" id="parent_child'+i+'"><option value="" >请选择</option><option value="parent" >parent</option><option value="child" selected="selected">child</option><option value="parent_child" >parent_child</option></select>'
 				            + '</div>'
   				            + '<div class="col-sm-1">'
   				            + '<a class="btn btn-success btn-sm del_row">删除</a>'
   				            + '</div>'
   				            + '</div>';
   			            $("#sku_info").append(input_row);      			         
      			         $("#parent_child"+i+"  option[value='child'] ").attr("selected",true)
   				    });
   					$("#oriange_sku").val(sku_search);
      				 change_sku_description(sku_search); 
       				$('.form-group').find('ul').empty();
       				
    	                					
                   }else{
   					layer.alert('没有搜索到该sku',8);
                   }
               }
           });
           return false;
        }
    
        if( (sku_pre.length>0 || sku_buf.length>0 || batch_color.length>0 || batch_size.length>0) && sku_ori.length>0){//修改
            var skuArr= new Array();
        	$("input[name='sku[]']").each(function(i){
            	   skuArr[i] = $(this).val();
                });
        	$("input[name='color[]']").each(function(i){
          	     colorss = 0;
                 if(batch_color !== $(this).val()){
              	   colorss = batch_color;
                 }else{
              	   colorss = $(this).val();
                 }
                color_arr[i] = colorss;
             });
             $("input[name='size[]']").each(function(i){
          	   sizess = 0;
                 if(batch_size !== $(this).val()){
              	   sizess = batch_size;
                 }else{
              	   sizess = $(this).val();
                 }
                 size_arr[i] = sizess;
              });
             $("input[name='external_product_id[]']").each(function(i){
          	   external_product_id_arr[i] = $(this).val();
              });
             $("select[name='parent_child[]']").each(function(i){
            	   parent_child_arr[i] = $(this).val();
                });
             $("#sku_info").empty();
             // 添加product ID
				var add_product_id_arr = new Array();
	           var num =skuArr.length; 
	           $.ajax({
	               url: '<?php echo admin_base_url('amz/amzListingTemplate/search_product_id');?>',
	               data:'num='+num, 
	               type: 'POST',
	               async:false,
	   			   cache:false,
	               dataType: 'JSON',
	               success: function(data){
	           	        if(data.status=='1'){
	                   		$.each(data.info,function(i,val){
	                   			add_product_id_arr[i] = val;
	                   		});
	               			        	       					
		                }
	                }
	           });  		          
//              $.each(skuArr,function(i,val){            	
//             	var input_row = '';
// 				var color = '';
// 				var size = '';
// 				if(color_arr.hasOwnProperty(i)){
// 					color = color_arr[i];
// 				    }else{
// 				color = '';
// 	   				}
// 				if(size_arr.hasOwnProperty(i)){
// 					size = size_arr[i];
// 				    }else{
// 	   				    size = '';
// 	   				}
// 				if(external_product_id_arr.hasOwnProperty(i)){
//     					if(external_product_id_arr[i]!=0){
//     					external_product_id = external_product_id_arr[i];
//     					}else{
//     						external_product_id =add_product_id_arr[i];
//     					}
// 				}else{
// 	   				    external_product_id = '';
// 	   			}
// 				if(parent_child_arr.hasOwnProperty(i)){
// 					parent_child = parent_child_arr[i];
// 				    }else{
// 	   				    parent_child = '';
// 	   				}                
//             	   input_row += '<div class="form-group">'
//     		            + '<label class="col-sm-1 control-label"></label>'
//     		            + '<div class="col-sm-2">'
//     		            + '<input type="text" class="form-control" name="sku[]"  placeholder="请输入产品sku" datatype="*" nullmsg="sku不能为空" value="'+sku_pre+val+sku_buf+'" />'
//     		            + '</div>'
//     		            + '<div class="col-sm-2">'
//     		            + '<input type="text" class="form-control" name="color[]" value="'+color+'" />'
//     		            + '</div>'
//     		            + '<div class="col-sm-2">'
//     		            + '<input type="text" class="form-control" name="size[]" value="'+size+'" />'
//     		            + '</div>'
//     		            + '<div class="col-sm-2">'
//     		            + '<input type="text" class="form-control" name="external_product_id[]" value="'+external_product_id+'" />'
//     		            + '</div>'
//     			         + '<div class="col-sm-2">'
//     	            + '<select class="form-control" name ="parent_child[]" id="parent_child'+i+'"><option value="" >请选择</option><option value="parent" >parent</option><option value="child">child</option><option value="parent_child" >parent_child</option></select>'
//     	            + '</div>'
//     		            + '<div class="col-sm-1">'
//     		            + '<a class="btn btn-success btn-sm del_row">删除</a>'
//     		            + '</div>'
//     		            + '</div>';
            	   
//     	             $("#sku_info").append(input_row);
//     		         $("#parent_child"+i+"  option[value="+parent_child+"] ").attr("selected",true) 
//                });
             $.ajax({
                url: '<?php echo admin_base_url('amz/amzListingTemplate/search_sku');?>',
                 data:'sku_search='+sku_ori, 
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
//      						if(external_product_id_arr.hasOwnProperty(i)){
//     	     					if(external_product_id_arr[i]!=0){
//     	     					external_product_id = external_product_id_arr[i];
//     	     					}else{
//     	     						external_product_id =add_product_id_arr[i];
//     	     					}
//         	 				}else{
//         	 	   				    external_product_id = '';
//         	 	   			}
     						if(parent_child_arr.hasOwnProperty(i)){
     							parent_child = parent_child_arr[i];
     	   				    }else{
     	   	   				    parent_child = '';
     	   	   				}   						
     						input_row = '<div class="form-group">'
     				            + '<label class="col-sm-1 control-label"></label>'
     				            + '<div class="col-sm-2">'
     				            + '<input type="text" class="form-control" name="sku[]"  placeholder="请输入产品sku" datatype="*" nullmsg="sku不能为空" value="'+sku_pre+val+sku_buf+'" />'
     				            + '</div>'
     				            + '<div class="col-sm-2">'
     				            + '<input type="text" class="form-control" name="color[]" value="'+color+'" />'
     				            + '</div>'
     				            + '<div class="col-sm-2">'
     				            + '<input type="text" class="form-control" name="size[]" value="'+size+'" />'
     				            + '</div>'
     				            + '<div class="col-sm-2">'
     				            + '<input type="text" class="form-control" name="external_product_id[]" value="" />'//'+external_product_id+'
     				            + '</div>'
        				        + '<div class="col-sm-2">'
   				                + '<select class="form-control" name ="parent_child[]" id="parent_child'+i+'"><option value="" >请选择</option><option value="parent" >parent</option><option value="child" selected="selected">child</option><option value="parent_child" >parent_child</option></select>'
   				                + '</div>'
     				            + '<div class="col-sm-1">'
     				            + '<a class="btn btn-success btn-sm del_row">删除</a>'
     				            + '</div>'
     				            + '</div>';
     			            $("#sku_info").append(input_row);
      			           $("#parent_child"+i+"  option[value="+parent_child+"] ").attr("selected",true) 					
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
//新增
        $("input[name='color[]']").each(function(i){
           color_arr[i] = $(this).val();       
       });
       $("input[name='size[]']").each(function(i){
           size_arr[i] = $(this).val();
        });

       $("select[name='parent_child[]']").each(function(i){
    	   parent_child_arr[i] = $(this).val();
        });     
        $.ajax({
            url: '<?php echo admin_base_url('amz/amzListingTemplate/search_sku');?>',
            data:'sku_search='+sku_search, 
            type: 'POST',
            async:false,
			   cache:false,
            dataType: 'JSON',
            success: function(data){
                if(data.status=='1'){
					$("#sku_info").empty();
					// 添加product ID
   					var add_product_id_arr = new Array();
   		           var num =data.skus.length; 
   		           $.ajax({
   		               url: '<?php echo admin_base_url('amz/amzListingTemplate/search_product_id');?>',
   		               data:'num='+num, 
   		               type: 'POST',
   		               async:false,
   		   			   cache:false,
   		               dataType: 'JSON',
   		               success: function(data){
   		           	        if(data.status=='1'){
   		                   		$.each(data.info,function(i,val){
   		                   			add_product_id_arr[i] = val;
   		                   		});
   		               			        	       					
   			                }
   		                }
   		           });  		          
					$.each(data.skus,function(i,val){
						var input_row = '';
						var color = '';
						var size = '';
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
// 						if(add_product_id_arr.hasOwnProperty(i)){
// 							external_product_id = add_product_id_arr[i];
// 	   				    }else{
// 	   	   				    external_product_id = '';
// 	   	   				}
						if(parent_child_arr.hasOwnProperty(i)){
							parent_child = parent_child_arr[i];
	   				    }else{
	   	   				    parent_child = '';
	   	   				}   						
						input_row = '<div class="form-group">'
				            + '<label class="col-sm-1 control-label"></label>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="sku[]"  placeholder="请输入产品sku" datatype="*" nullmsg="sku不能为空" value="'+sku_pre+val+sku_buf+'" />'
				            + '</div>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="color[]" value="'+color+'" />'
				            + '</div>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="size[]" value="'+size+'" />'
				            + '</div>'
				            + '<div class="col-sm-2">'
				            + '<input type="text" class="form-control" name="external_product_id[]" value="" />'//'+external_product_id+'
				            + '</div>'
   				            + '<div class="col-sm-2">'
				            + '<select class="form-control" name ="parent_child[]" id="parent_child'+i+'"><option value="" >请选择</option><option value="parent" >parent</option><option value="child" selected="selected">child</option><option value="parent_child" >parent_child</option></select>'
				            + '</div>'
				            + '<div class="col-sm-1">'
				            + '<a class="btn btn-success btn-sm del_row">删除</a>'
				            + '</div>'
				            + '</div>';
						
			            $("#sku_info").append(input_row);
    			        $("#parent_child"+i+"  option[value='child'] ").attr("selected",true)			            
			            $("#oriange_sku").val(sku_search);
			            $('.form-group').find('ul').empty(); 
			            change_sku_description(sku_search);
				    });
                }else{
					layer.alert('没有搜索到该sku',8);
                }
            }
        });

    });   
})


 

/**
 * sku变化以后，更改产品描述内容为sku的英文内容
 * 先清除文本域和编辑器里的内容
 */
function change_sku_description(sku){

	var return_content = '';
    var item_names = '';
	$("#detail").empty();
	$("#subject").empty();
	KindEditor.instances[0].html('');

	$.ajax({
        url: '<?php echo admin_base_url('amz/amzListingTemplate/getSkuInfoLike')?>',
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
				$.each(data.item_name,function(j,v){
					item_names+=v;
			    });
				$("#subject").html(item_names);
            }else{
				layer.alert('没有搜索到该sku的英文资料',8);
            }
        }
    });
}


 
/**
 * 按SKU目录添加图片
 * @param obj
 */
function addDir(obj, url, token_id, opt){

	$('.ajax-loading').removeClass('hide');
    var sku_search = $("#sku_search").val();
    if(sku_search==''){
    	layer.alert('请选择SKU',8);
    	$('.ajax-loading').addClass('hide');
    	return false;
    }
        //开始异步获取
        $.ajax({
            url: url,
            data: 'dirName='+sku_search+'&opt='+opt,
            type: 'POST',
            async:false,
			cache:false,
            dataType: 'JSON',
            success: function(data){
            	$('.ajax-loading').addClass('hide');
                if (data.status){
                    if (data.data){ //说明有成功的，成功的添加到里边去
                        var liStr = '';
                        $.each(data.data, function(index, el){
                            liStr += '<li><div><input type="text" name="img_color[]" style="width:100px;height:30px;" title="图片颜色" placeholder="图片颜色"><img src="' + el + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="' + el + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                        });
                        $(obj).closest('div.form-group').find('ul').empty();
                        $(obj).closest('div.form-group').find('ul').append(liStr);
                        layer.msg('图片获取成功', 2, -1);
                    }
                    if (data.info != ''){ //说明有失败的，失败的再显示出来
                        var msg = '';
                        $.each(data.info, function(index, el){
                            msg += el+"获取失败<br/>";
                        });
                        layer.alert(msg, 3, !1);
                    }
                }else {
                    //layer.msg('图片上传失败,'+data.info, 2, -1);
                    layer.alert('<font color="red">图片获取失败,</font>'+data.info, 3, !1);
                }
            }
        });
   
}
</script>