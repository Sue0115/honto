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
        width: 280px;
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
<form action="<?php echo admin_base_url('publish/smt/doAction'); ?>" class="form-horizontal validate_form" method="post"  starurl="<?php echo admin_base_url('publish/smt/doAction'); ?>" id="proform">

    <!-- 类目选择 -->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;一般信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <!-- 分类选择 -->
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="red">*</span>已选择分类：</label>

                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="category_name" id="category_name"
                               value="<?php echo $category_info['name']; ?>" readonly="readonly" datatype="*"
                               nullmsg="类目不能为空" errormsg="类目错误">
                        <input type="hidden" id="categoryId" name="categoryId"
                               value="<?php echo $category_info['id']; ?>"/>
                        <input type="hidden" name="token_id" value="<?php echo $token_id; ?>"/>
                    </div>
                    <a href="<?php echo admin_base_url('publish/smt/add?token_id='.$token_id); ?>" class="btn btn-primary btn-sm">重新选择</a>
             <input id="skuinfo" type="text"   /> <a id="getskuinfo" class="btn btn-primary btn-sm" >获取SKU信息</a>
                </div>
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
                    <label for="subject" class="control-label col-sm-2"><span class="red">*</span>标题：</label>

                    <div class="col-sm-10">
                        <textarea name="subject" id="subject" class="form-control" placeholder="标题" datatype="*1-128"
                                  nullmsg="标题不能为空" errormsg="请输入长度在1-128之间的英文字符"
                                  maxlength="128"><?php echo filterData('subject', $draft_info); ?></textarea>

                        <div class="help-block">提示信息</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keyword" class="col-sm-2 control-label"><span class="red">*</span>关键字：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="keyword" id="keyword"
                               placeholder="关键字,3个关键字加起来不能超过255个字符"  nullmsg="关键字不能为空"
                               value="<?php echo filterData('keyword', $draft_detail); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="keyword2" class="col-sm-2 control-label">更多关键字：</label>

                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="productMoreKeywords1" id="productMoreKeywords1"
                               placeholder="关键字二"
                               value="<?php echo filterData('productMoreKeywords1', $draft_detail); ?>">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="productMoreKeywords2" id="productMoreKeywords2"
                               placeholder="关键字三"
                               value="<?php echo filterData('productMoreKeywords2', $draft_detail); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="shiptime" class="col-sm-2 control-label"><span class="red">*</span>发货期：</label>

                    <div class="col-sm-3 input-group">
                        <input type="text" class="form-control" name="deliveryTime" id="deliveryTime"
                               value="<?php echo filterData('deliveryTime', $draft_info) ? filterData('deliveryTime', $draft_info) : 7; ?>"
                               datatype="n,numrange" min="1" max="60" nullmsg="请输入1-60之间的整数" errormsg="请输入整数">

                        <div class="input-group-addon">天</div>
                    </div>
                    <label for="freightTemplateId" class="col-sm-2 control-label"><span
                            class="red">*</span>运费模板：</label>

                    <div class="col-sm-4">
                        <select name="freightTemplateId" id="freightTemplateId" class="form-control" datatype="*,n"
                                nullmsg="运费模板不能为空" errormsg="值必须是整数">
                            <option value="">--请选择运费模板--</option>
                            <?php
                            foreach ($freight as $f):
                                echo '<option value="' . $f['templateId'] . '" ' . (($action == 'edit' && filterData('freightTemplateId', $draft_detail) == $f['templateId']) || ($action == 'add' && $f['default'] == 1) ? 'selected="selected"' : '') . '>' . $f['templateName'] . '</option>';
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <a title="同步" class="btn btn-primary btn-sm" id="freightTemplateId_refresh"
                                onclick="refresh_template(this, <?php echo $token_id; ?>, '<?php echo filterData('freightTemplateId', $draft_detail); ?>')">
                            <i class="icon-refresh"></i>
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label for="availability_time" class="col-sm-2 control-label"><span class="red">*</span>有效期：</label>

                    <div class="col-sm-3 input-group">
                        <input type="text" class="form-control" name="wsValidNum" id="wsValidNum"
                               value="<?php echo filterData('wsValidNum', $draft_info) ? filterData('wsValidNum', $draft_info) : 30; ?>"
                               datatype="n,numrange" min="1" max="30" nullmsg="有效期为1-30天的整数" errormsg="有效期错误">

                        <div class="input-group-addon">天</div>
                    </div>
                    <label for="promiseTemplateId" class="col-sm-2 control-label">服务模板：</label>

                    <div class="col-sm-4">
                        <select name="promiseTemplateId" id="promiseTemplateId" class="form-control">
                            <option value="">--请选择服务模板--</option>
                            <?php
                            foreach ($service as $s):
                                echo '<option value="' . $s['serviceID'] . '" ' . (filterData('promiseTemplateId', $draft_detail) == $s['serviceID'] ? 'selected="selected"' : '') . '>' . $s['serviceName'] . '</option>';
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <a title="同步" class="btn btn-primary btn-sm" id="promiseTemplateId_refresh"
                                onclick="refresh_template(this, <?php echo $token_id; ?>, '<?php echo filterData('promiseTemplateId', $draft_detail); ?>')">
                            <i class="icon-refresh"></i>
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label for="unit" class="col-sm-2 control-label"><span class="red">*</span>计算单位：</label>

                    <div class="col-sm-3">
                        <select name="productUnit" id="productUnit" class="form-control" datatype="n" nullmsg="单位不能为空" errormsg="单位的值类型错误">
                            <?php
                            if (filterData('productId', $draft_info)){ //产品ID
                                $unitId = filterData('productUnit', $draft_detail);
                            }else {
                                $unitId = '100000015';
                            }
                            foreach ($unit as $id => $u):
                            ?>
                                <option value="<?php echo $id; ?>" <?php echo $unitId == $id ? 'selected="selected"' : ''; ?>><?php echo $u['name'] . '(' . $u['name_en'] . ')'; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <label class="col-sm-2 control-label">销售方式：</label>

                    <div class="col-sm-2">
                        <div class="checkbox">
                            <label for="packageType">
                                <input type="checkbox" id="packageType" name="packageType"
                                       value="1" <?php echo filterData('packageType', $draft_detail) ? 'checked' : ''; ?>>打包销售
                            </label>
                        </div>
                    </div>
                    <div
                        class="col-sm-3 sale_method_set <?php echo filterData('packageType', $draft_detail) ? '' : 'hide'; ?>">
                        <label class="col-sm-4 control-label no-padding-right"><span class="red">*</span>每包</label>

                        <div class="input-group col-sm-8">
                            <input type="text" name="lotNum"
                                   value="<?php echo filterData('lotNum', $draft_detail) ? filterData('lotNum', $draft_detail) : 2; ?>"
                                   class="form-control" datatype="numrange" nullmsg="打包销售情况下，数量不能为空" min="1"
                                   errormsg="打包销售数量错误"/>

                            <div class="input-group-addon unit_cn"
                                 id="package_unit"><?php echo $unitId ? $unit[$unitId]['name'] : '袋'; ?></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="grossWeight" class="control-label col-sm-2"><span class="red">*</span>包装后重量：</label>

                    <div class="col-sm-3 input-group">
                        <input type="text" class="form-control" name="grossWeight" id="grossWeight" datatype="numrange"
                               nullmsg="包装重量在0.001-70.000之间" min="0.001" max="70" errormsg="包装重量要在0.001-70.000之间"
                               value="<?php echo filterData('grossWeight', $draft_info); ?>">

                        <div class="input-group-addon">
                            KG
                        </div>
                    </div>
                    
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-2">
                        <div class="checkbox">
                            <label for="isPackSell">
                                <input type="checkbox" name="isPackSell" id="isPackSell"
                                       value="1" <?php echo filterData('isPackSell', $draft_detail) ? 'checked' : ''; ?>>自定义重量
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2"><span class="red">*</span>包装后体积：</label>

                    <div class="col-sm-3 input-group">
                        <input type="text" class="form-control" id="packageLength"  name="packageLength" placeholder="长"
                               datatype="n,numrange" nullmsg="长度为1-270之间的整数" errormsg="长度为1-270之间的整数" min="1" max="270"
                               value="<?php echo (int)filterData('packageLength', $draft_info); ?>"/>

                        <div class="input-group-addon">&times;</div>
                        <input type="text" class="form-control" id="packageWidth"  name="packageWidth" placeholder="宽"
                               datatype="n,numrange" nullmsg="宽度为1-270之间的整数" errormsg="宽度为1-270之间的整数" min="1" max="270"
                               value="<?php echo (int)filterData('packageWidth', $draft_info); ?>"/>

                        <div class="input-group-addon">&times;</div>
                        <input type="text" class="form-control" id="packageHeight"  name="packageHeight" placeholder="高"
                               datatype="n,numrange" nullmsg="高度为1-270之间的整数" errormsg="高度为1-270之间的整数" min="1" max="270"
                               value="<?php echo (int)filterData('packageHeight', $draft_info); ?>"/>
                    </div>
                    <div class="col-sm-4">
                        <label class="control-label">(单位：厘米，每<b
                                class="unit_cn"><?php echo $unitId ? $unit[$unitId]['name'] : '袋'; ?></b>&nbsp;<b
                                id="volume">0</b>&nbsp;cm3)</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="groupId" class="col-sm-2 control-label">产品分组：</label>

                    <div class="col-sm-3">
                        <select name="groupId" id="groupId" class="form-control">
                            <option value="">--请选择产品分组--</option>
                            <?php
                            foreach ($group as $g):
                                if (array_key_exists('child', $g)) { //有子选项
                                    echo '<optgroup label="'.$g['group_name'].'">';
                                    foreach ($g['child'] as $r):
                                        echo '<option value="' . $r['group_id'] . '" ' . (filterData('groupId', $draft_info) == $r['group_id'] ? 'selected="selected"' : '') . '>&nbsp;&nbsp;&nbsp;&nbsp;--' . $r['group_name'] . '</option>';
                                    endforeach;
                                    echo '</optgroup>';
                                }else {
                                    echo '<option value="' . $g['group_id'] . '" '.(filterData('groupId', $draft_info) == $g['group_id'] ? 'selected="selected"' : '').'>' . $g['group_name'] . '</option>';
                                }
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <a title="同步" class="btn btn-primary btn-sm" id="groupId_refresh"
                                onclick="refresh_template(this, <?php echo $token_id; ?>, '<?php echo filterData('groupId', $draft_info); ?>');">
                            <i class="icon-refresh"></i>
                        </a>
                    </div>
                </div>
                <!-- 图片显示窗口 -->
                <div class="form-group water-add">
                    <label class="col-sm-2 control-label"><span class="red">*</span>产品图片：</label>
                    <div class="col-sm-10">
                        <div>
                            <a href="javascript:void(0);" class="btn btn-default btn-sm from_local" lang="main">从我的电脑选取</a>
                            
                            <!-- <a href="javascript:void(0);" class="btn btn-default btn-sm" id="from_remote">从图片银行选取</a> -->
                            &nbsp;&nbsp;
                            <a class="btn btn-xs btn-primary pic-del-all" title="全部删除"><i class="icon-trash"></i></a>
                            <!--&nbsp;&nbsp;-->
                            <!--<div class="btn-group">-->
                            <!--    <a class="btn btn-xs pic-move btn-primary" title="左移" lang="left"><i class="icon-arrow-left"></i></a>-->
                            <!--    <a class="btn btn-xs pic-move btn-primary" title="右移" lang="right"><i class="icon-arrow-right"></i></a>-->
                            <!--</div>-->

                        </div>

                        <ul class="list-inline pic-main" id="se-water-add">
                            <?php
                            if (filterData('imageURLs', $draft_detail)):
                                $imageList = explode(';', $draft_detail['imageURLs']);
                                foreach ($imageList as $image):?>
                                    <li>
                                        <div>
                                            <img src="<?php echo $image;?>" width="100" height="100" style="border: 0px;">
                                            <input type="hidden" name="imgLists[]" value="<?php echo $image;?>"/>
                                            <a href="javascript: void(0);" class="pic-del">删除</a>&nbsp;<a href="javascript: void(0);" class="pic-add-water">水印</a>
                                        </div>
                                    </li>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>

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
                <?php
                //产品属性
                $aeopAeProductPropertys = filterData('aeopAeProductPropertys', $draft_detail) ? unserialize($draft_detail['aeopAeProductPropertys']) : array();

                //这个产品属性组装下
                $propertyArray  = array();
                $propertyArray2 = array();
                $propertyCustom = array(); //自定义的属性
                if ($aeopAeProductPropertys){
	                foreach ($aeopAeProductPropertys as $property) {
	                    if (array_key_exists('attrNameId', $property) && array_key_exists('attrValueId', $property)) { //这个属性ID和值ID的形式存在
	                        $propertyArray[] = $property; //同个属性ID可能会有多个值ID
	                    } elseif (array_key_exists('attrNameId', $property) && array_key_exists('attrValue', $property)) { //属性ID和值内容的形式存在
	                        $propertyArray2[$property['attrNameId']] = $property;
	                    } else {
	                        $propertyCustom[$property['attrName']] = $property;
	                    }
	                }
                }
                foreach ($attributes as $att):
                    if (!$att['sku']):
                        echo parseAttribute($att, 0, false, $propertyArray, $propertyArray2);
                    endif;
                endforeach;

                //输出自定义的基本属性
                foreach ($propertyCustom as $property) {
                    ?>
                    <div class="form-group">
                        <label class="col-sm-2">&nbsp;</label>

                        <div class="col-sm-6">
                            <div class="col-sm-5">
                                <input class="form-control" type="text" value="<?php echo $property['attrName'];?>"
                                       name="custom[attrName][]"/>
                            </div>
                            <div class="col-sm-5">
                                <input class="form-control" type="text" value="<?php echo $property['attrValue'];?>"
                                       name="custom[attrValue][]"/>
                            </div>
                            <div class="col-sm-2">
                                <a class="btn btn-success btn-sm del_row">删除</a>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="form-group">
                    <div class="col-sm-offset-2">
                        <a class="btn btn-primary btn-sm" href="javascript: void(0);" id="add_row">添加自定义属性</a>
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
                <?php
                /****************商品属性详情处理开始******************/
                $sku_property = array(); //SKU属性-值数据
                $isSkuMulti = false;  //SKU是否是多属性
                if ($draft_skus) {
                    foreach ($draft_skus as $sku_item) {
                        $aeopSKUProperty = unserialize($sku_item['aeopSKUProperty']);
                        if ($aeopSKUProperty) {
                            foreach ($aeopSKUProperty as $property) {
                                $sku_property[$property['skuPropertyId']][$property['propertyValueId']] = array(
                                    'propertyValueId'             => $property['propertyValueId'],
                                    'propertyValueDefinitionName' => array_key_exists('propertyValueDefinitionName', $property) ? $property['propertyValueDefinitionName'] : '', //自定义名称
                                    'skuImage'                    => array_key_exists('skuImage', $property) ? $property['skuImage'] : '' //自定义图片
                                );
                                $isSkuMulti = true;
                            }
                        }
                    }
                }
                /****************商品属性详情处理结束******************/

                $sku_flag   = false; //是否含有SKU属性
                $sku_attr   = ''; //SKU属性字段
                $sku_config = ''; //SKU配置信息,SKU的属性对象
                $sku_tmp    = array(); //SKU配置临时信息
                $sku_sort   = array(); //SKU属性的顺序，用这个来判断下属性组合的顺序--编辑用
                $att_val_sort = array(); //SKU属性的值的顺序，用这个来排序下吧
                $sku_values = array(); //属性和值的名称
                foreach ($attributes as $att):
                    if ($att['sku']):
                        $sku_flag = true;
                        $sku_attr .= '<td class="' . (array_key_exists($att['id'], $sku_property) ? '' : 'hide') . ' sku-a-' . $att['id'] . '">' . $att['names']['zh'] . '</td>';
                        //echo parseAttribute($att, 0, true);
                        echo parseSkuAttribute($att, $sku_property, $token_id);
                        foreach ($att['values'] as $at) {
                            $sku_tmp[$att['id']][$at['id']]['en'] = $at['names']['en'];
                            $sku_tmp[$att['id']][$at['id']]['zh'] = $at['names']['zh'];
                        }
                        $sku_sort[] = $att['id'];
                        foreach ($att['values'] as $values) {
                            $sku_values[$att['id']][$values['id']] = $values['names'];
                            $att_val_sort[$att['id']][] = $values['id'];
                        }

                    endif;
                endforeach;

                $sku_config = json_encode($sku_tmp);
                if ($sku_flag) { //含有SKU属性，输出SKU-TABLE
                    ?>
                    <div class="form-group <?php echo $sku_property ? '' : 'hide';?>">
                        <div class="col-sm-10 col-sm-offset-2">
                            <span>下表的零售价是最终展示给买家的产品价格。</span>
                            <table class="table table-bordered sku-table table-vcenter">
                                <caption>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">批量设置零售价：</label>

                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="batch-price"/>
                                        </div>
                                        <div class="col-sm-1">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary"
                                               id="batch-price-btn">确定</a>
                                        </div>

                                        <label class="col-sm-2 col-sm-offset-1 control-label">批量设置库存：</label>

                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="batch-stock"/>
                                        </div>
                                        <div class="col-sm-1">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary"
                                               id="batch-stock-btn">确定</a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">批量设置编码：</label>

                                        <div class="col-sm-2">
                                            <input type="text" class="form-control" id="batch-sku"/>
                                        </div>
                                        <div class="col-sm-1">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary"
                                               id="batch-sku-btn">确定</a>
                                        </div>
                                    </div>
                                </caption>
                                <thead>
                                <tr>
                                    <?php echo $sku_attr;?>
                                    <td width="22%"><span class="red">*</span>零售价</td>
                                    <td width="13%"><span class="red">*</span>库存</td>
                                    <td width="25%">商品编码(无需前后缀)</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                /*******************属性对应SKU开始*********************/
                                if ($isSkuMulti) { //只有是多属性的才进行这一步

                                    $newSkus = sortSkuAttr($draft_skus, $sku_sort, $att_val_sort);
                                    unset($att_val_sort);

                                    foreach ($newSkus as $sku_item) {
                                        $aeopSKUProperty = unserialize($sku_item['aeopSKUProperty']);
                                        if ($aeopSKUProperty) {
                                            $temp       = array();//临时变量
                                            $tr_temp    = array(); //临时变量
                                            $td_str     = ''; //单元格字段
                                            $class_temp = array(); //临时变量

                                            //按属性顺序来处理产品属性
                                            $newAeopSKUProperty = array();//新的产品属性
                                            foreach ($sku_sort as $sort){ //按SKU属性排序处理
                                                //$att_val_sort

                                                foreach ($aeopSKUProperty as $Property){
                                                    if ($sort == $Property['skuPropertyId']){ //分类信息
                                                        $newAeopSKUProperty[] = $Property;
                                                        break;
                                                    }
                                                }
                                            }

                                            //开始输出新的属性
                                            foreach ($newAeopSKUProperty as $Property) {
                                                $temp[$Property['skuPropertyId']] = $Property; //属性ID
                                                $td_str .= '<td class="td-' . $Property['skuPropertyId'] . '-' . $Property['propertyValueId'] . '">' . $sku_values[$Property['skuPropertyId']][$Property['propertyValueId']]['zh'] . '</td>';
                                                $class_temp[] = $Property['skuPropertyId'] . '_' . $Property['propertyValueId'];
                                            }
                                            foreach ($sku_sort as $sort) { //按属性排序来显示
                                                $tr_temp[] = array_key_exists($sort, $temp) ? $temp[$sort]['propertyValueId'] : '';
                                            }

                                            echo '<tr class="coord-' . implode('_', $tr_temp) . '">';
                                            echo $td_str;
                                            echo '<td>US $<input type="text" class="sku-price" size="8" maxlength="8" datatype="numrange" min="0" max="100000" nullmsg="零售价在0-100000范围内" errormsg="零售价格式错误" ignore="ignore" name="skuPrice[' . implode('-', $class_temp) . ']" value="' . $sku_item['skuPrice'] . '" />/<span class="unit_cn">' . ($draft_detail['productUnit'] ? $unit[$draft_detail['productUnit']]['name'] : '袋') . '</span></td>';
                                            echo '<td><input type="text" class="sku-quantity" size="6" maxlength="6" datatype="numrange" max="999999" min="0" nullmsg="库存范围为0-999999" errormsg="库存错误" ignore="ignore" name="skuStock[' . implode('-', $class_temp) . ']" value="' . $sku_item['ipmSkuStock'] . '" /></td>';
                                            echo '<td><input type="text" class="sku-code" maxlength="20" name="skuCode[' . implode('-', $class_temp) . ']" value="' . rebuildSmtSku($sku_item['smtSkuCode'], true) . '"/></td>';
                                            echo '</tr>';
                                        }
                                    }
                                    unset($newSkus);
                                }
                                /*******************属性对应SKU结束*********************/
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php
                }
                ?>

                <div class="form-group <?php echo $sku_property ? 'hide' : ''; ?>">
                    <label for="productPrice" class="control-label col-sm-2"><span class="red">*</span>零售价：</label>

                    <div class="col-sm-3 input-group">
                        <input type="text" class="form-control" name="productPrice" id="productPrice"
                               datatype="*,numrange" nullmsg="零售价范围为0-100000" min="0" max="100000"
                               value="<?php echo filterData('productPrice', $draft_info); ?>">

                        <div class="input-group-addon">USD</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">批发价：</label>

                    <div class="col-sm-2">
                        <div class="checkbox">
                            <label for="wholesale"><input type="checkbox" name="wholesale" id="wholesale" value="1" <?php echo filterData('bulkOrder', $draft_detail) && filterData('bulkDiscount', $draft_detail) ? 'checked' : ''; ?>>批发</label>
                        </div>
                    </div>
                </div>
                <div class="form-group wholesale_set <?php echo filterData('bulkOrder', $draft_detail) && filterData('bulkDiscount', $draft_detail) ? '' : 'hide'; ?>">
                    <label for="bulkOrder" class="control-label col-sm-2"><span class="red">*</span>起批数：</label>

                    <div class="input-group col-sm-3">
                        <input type="text" class="form-control" name="bulkOrder" id="bulkOrder"
                               value="<?php echo filterData('bulkOrder', $draft_detail); ?>" datatype="n,numrange"
                               min="2" max="100000" nullmsg="起批数为2-100000">

                        <div
                            class="input-group-addon unit_cn"><?php echo $unitId ? $unit[$unitId]['name'] : '袋'; ?></div>
                    </div>

                    <label for="bulkDiscount" class="control-label col-sm-2"><span class="red">*</span>打折优惠：</label>

                    <div class="input-group col-sm-3">
                        <input type="text" class="form-control" id="bulkDiscount" name="bulkDiscount"
                               datatype="n,numrange" min="1" max="99" nullmsg="打折优惠的范围为1-99" errormsg="打折优惠格式错误"
                               value="<?php echo filterData('bulkDiscount', $draft_detail); ?>">

                        <div class="input-group-addon">%</div>
                    </div>
                    <div class="col-sm-2">
                        <label class="control-label no-padding-left no-padding-right">(批发价：<b id="wholesale_price">0</b>
                            USD)</label>
                    </div>
                </div>
                <div class="form-group <?php echo $sku_property ? 'hide' : ''; ?>">
                    <label class="col-sm-2 control-label"><span class="red">*</span>库存：</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="productStock" name="productStock"
                               datatype="numrange" min="1" max="999999" nullmsg="库存值为1-999999之间" errormsg="库存错误"
                               value="<?php echo $draft_skus && filterData('ipmSkuStock', $draft_skus[0]) ? $draft_skus[0]['ipmSkuStock'] : 0; ?>"/>
                    </div>
                </div>

                <div class="form-group <?php echo $sku_property ? 'hide' : ''; ?>">
                    <label for="sku" class="col-sm-2 control-label">商品编码：</label>

                    <div class="col-sm-10">
                        <?php
                        $skuCode = ''; //SKU代码信息
                        if (!empty($draft_skus) && !empty($draft_skus[0]['smtSkuCode'])){
                            $skuCode = rebuildSmtSku($draft_skus[0]['smtSkuCode']);
                        }
                        ?>
                        <input type="text" class="form-control" id="productCode" name="productCode" placeholder="商品编码" value="<?php echo $skuCode;?>">
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
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="">模板：</label>
                    <div class="col-sm-3">
                        <select class="form-control" name="templateId" id="templateId">
                            <option value="">--请选择--</option>
                            <?php
                            if ($template_list):
                                foreach ($template_list as $template):
                                    echo '<option value="'.$template['id'].'" '.(filterData('templateId', $draft_detail) == $template['id'] ? 'selected="selected"' : '').'>'.$template['name'].'</option>';
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <a id="templateId_refresh" title="刷新" class="btn btn-primary btn-sm" onclick="refresh_template(this, <?php echo $token_id; ?>, '')">
                        <i class="icon-refresh"></i>
                    </a>
                    <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/smt_template/info');?>" target="_blank">新增</a>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="detail_title">描述标题：</label>
                    <div class="col-sm-10">
                        <input placeholder="请输入描述标题" class="form-control" id="detail_title" name="detail_title" type="text" value="<?php echo filterData('detail_title', $draft_detail);?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2" for="shouhouId">售后模板：</label>
                    <div class="col-sm-3">
                        <select class="form-control" name="shouhouId" id="shouhouId">
                            <option value="">--请选择--</option>
                            <?php
                            if ($shouhou_list):
                                foreach ($shouhou_list as $mkey=>$template):
                                    echo '<option value="'.$template['id'].'" '.(filterData('shouhouId', $draft_detail) == $template['id'] ? 'selected="selected"' : ($mkey==0?'selected="selected"':'')).'>'.$template['name'].'</option>';
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <a class="btn btn-primary btn-sm" id="shouhouId_refresh" title="刷新" onclick="refresh_template(this, <?php echo $token_id;?>, '');">
                        <i class="icon-refresh bigger-110"></i>
                    </a>
                    <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/after_sales_service/info');?>" title="新增售后模板" target="_blank">新增</a>
                </div>
                
                <div class="form-group clearfix">
                    <label class="col-sm-2 control-label">描述图片：</label>

                    <div class="col-sm-10">
                        <div>
                            <a href="javascript:void(0);" class="btn btn-default btn-sm from_local" lang="detail">从我的电脑选取</a>
                            <a href="javascript:void(0);" class="btn btn-default btn-sm copy_main_pic">复制主图图片</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/smt/ajaxUploadDirImage");?>', '<?php echo $token_id;?>', '');">图片目录上传</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/smt/ajaxUploadDirImage");?>', '<?php echo $token_id;?>', 'SP');">实拍目录上传</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/smt/ajaxUploadDirImageByNewSys?type=1");?>', '<?php echo $token_id;?>', '');">新图片(实拍)上传</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="addDir(this, '<?php echo admin_base_url("publish/smt/ajaxUploadDirImageByNewSys?type=2");?>', '<?php echo $token_id;?>', '');">新图片(链接)上传</a>
                            <a class="btn btn-default btn-sm copyToMain" href="javascript: void(0);" onclick="copyPicTo(this, 'pic-main', 6);">复制到主图</a>
                            &nbsp;&nbsp;
                            <a class="btn btn-xs btn-primary pic-del-all" title="全部删除"><i class="icon-trash"></i></a>
                            <b class="ajax-loading hide">图片上传中...</b>
                        </div>
                        <ul class="list-inline pic-detail">
                            <?php
                            if (filterData('detailPicList', $draft_detail)):
                                $imageList = explode(';', $draft_detail['detailPicList']);
                                foreach ($imageList as $image):?>
                                    <li>
                                        <div>
                                            <img src="<?php echo $image;?>" width="100" height="100" style="border: 2px solid white;">
                                            <input type="hidden" name="detailPicList[]" value="<?php echo $image;?>"/>
                                            <a href="javascript: void(0);" class="pic-del">删除</a>
                                        </div>
                                    </li>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                </div>

                <!--自定义关联产品-->
                <div class="form-group clearfix">
                    <label class="col-sm-2 control-label">自定义关联产品：</label>
                    <div class="col-sm-10">
                        <div>
                            <a href="javascript:void(0);" class="btn btn-default btn-sm relate-product">选择产品</a>
                            &nbsp;&nbsp;
                            <a class="btn btn-xs btn-primary pic-del-all" title="全部删除"><i class="icon-trash"></i></a>
                            &nbsp;&nbsp;
                            插入到位置：
                            <label>
                                <input type="radio" name="relation_loction" value="header" <?php echo filterData('relationLocation', $draft_detail) == 'header' ? 'checked' : '';?>/>在前
                            </label>
                            <label>
                                <input type="radio" name="relation_loction" value="footer" <?php echo (filterData('relationLocation', $draft_detail) == 'footer' || !filterData('relationLocation', $draft_detail)) ? 'checked' : '';?>/>在后
                            </label>
                        </div>
                        <ul class="list-inline relate-list">
                            <?php

                            if ($productIds = filterData('relationProductIds', $draft_detail)){
                                $relationArr = explode(';', $productIds);
                                foreach ($relationArr as $pid){
                                    $imageUrls = $relationDetailInfo[$pid]['imageURLs'];
                                    $mainPic   = array_shift(explode(';', $imageUrls));
                                    echo '<li><div><img src="'.$mainPic.'" width="100" height="100" style="border: 0px;"><input type="hidden" name="relationProduct[]" value="'.$pid.'" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                                }
                                unset($relationArr);
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label for="detail" class="col-sm-2 control-label">详情描述:</label>

                    <div class="col-sm-10">
					<textarea name="detail" id="detail" class="form-control" placeholder="产品详情描述">
						<?php
                        $detail = htmlspecialchars_decode(filterData('detail', $draft_detail));
                        $detailLocal = htmlspecialchars_decode(filterData('detailLocal', $draft_detail));
                        if ($detailLocal){
                        	echo replaceSmtModuleToImg($detailLocal);
                        }else if ($detail) {
                            echo replaceSmtModuleToImg($detail);
                        }
                        ?>
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
        	
            <!--action用来判断是提交到哪个操作-->
            <input type="hidden" name="action" id="action" value=""/>
            <input type="hidden" name="id" value="<?php echo filterData('productId', $draft_info); ?>" id="id"/>
            <input type="hidden" name="old_token_id" value="<?php echo filterData('old_token_id', $draft_info);?>"/>

            <?php if ($api == 'post'):?>
            <!--保存-->
            <button class="btn btn-success submit_btn" type="submit" name="save">
                <i class="icon-ok bigger-110"></i>
                保存
            </button>

            <!--发布-->
            <button class="btn btn-primary submit_btn" type="submit" name="post">
                <i class="icon-ok bigger-110"></i>
                发布
            </button>

            <!--保存为待发布-->
            <button class="btn btn-inverse submit_btn" type="submit" name="saveToPost">
                <i class="icon-ok bigger-110"></i>
                保存为待发布
            </button>
            <?php elseif ($api == 'edit'):?>
            <!--修改并发布-->
            <button class="btn btn-inverse submit_btn" type="submit" name="editAndPost">
                <i class="icon-ok bigger-110"></i>
                保存并发布
            </button>
            <?php endif;?>
            <button class="btn btn-reset" type="reset">
                <i class="icon-undo bigger-110"></i>重置
            </button>
        </div>
    </div>
</form>
<div class="hide" id="showDiv" style="overflow:scroll; width: 1000px; height: 500px;"></div>
<script type="text/javascript" src="<?php echo static_url('theme/common/jquery.dragsort-0.5.1.min.js');?>"></script>
<script type="text/javascript" src="<?php echo static_url('theme/common/createFullWindowTag.js');?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo static_url('theme/common/css/style2.css');?>">
<script type="text/javascript">
    var skuConfig = eval(<?php echo $sku_config;?>); //SKU属性的信息
    var token_id; //账号ID
    var skuObj;   //SKU对象,全部模糊查询查出的
    var productProperty; //用来保存属性的json信息

    /**自定义kindeditor插件开始,样式类ke-icon-module**/
    KindEditor.plugin('module', function (K) {
        var self = this, name = 'module', lang = self.lang(name + '.');
        self.clickToolbar(name, function () {

            var html = ['<div style="padding:10px 20px;">',
                '<div class="ke-header">',
                // left start
                '<div class="ke-left">',
                '选择信息模板',
                '</div>',
                '<div class="ke-clearfix"></div>',
                '</div>',
                '<iframe class="ke-textarea" frameborder="0" style="width:758px;height:360px;background-color:#FFF;" data="nameca"></iframe>',
                '</div>'].join('');
            var dialog = self.createDialog({
                name: name,
                width: 800,
                height: 400,
                title: self.lang(name),
                body: html,
                yesBtn: {
                    name: self.lang('yes'),
                    click: function (e) {
                        var doc = K.iframeDoc(iframe);

                        var inputs = doc.body.getElementsByTagName('input'), html = '';
                        K.each(inputs, function (index, el) {
                            if (inputs[index].className == 'checkbox') {
                                if (inputs[index].checked) {
                                    //产品关联模块分类
                                    var field = inputs[index].lang == 'relation' ? 'relatedProduct' : 'customText';
                                    var str = '<kse:widget data-widget-type="' + field + '" id="' + inputs[index].value + '" title="' + inputs[index].title + '" type="' + inputs[index].lang + '"></kse:widget>';
                                    html += '<img class="kse-widget" data="' + encodeURIComponent(str) + '" src="http://style.aliexpress.com/js/5v/lib/kseditor/plugins/widget/images/widget1.png" />';
                                }
                            }
                        });
                        self['insertHtml'](html).hideDialog().focus();
                    }
                }
            });
            var iframe = K('.ke-textarea', dialog.div);
            iframe.attr('src', '<?php echo admin_base_url("smt/smt_product/moduleSelect?token_id=$token_id");?>');
        });
    });
    /**自定义kindeditor插件结束**/

    KindEditor.ready(function (K) {
        var editor = K.create("[name=detail]", {
            'uploadJson': '<?php echo admin_base_url("kindeditor/upload?token_id=$token_id");?>',
            "width": "100%",
            "height": "400px",
            "filterModel": false,//是否过滤html代码,true过滤
            "resizeType": "2",//是否可以改变editor大小，0：不可以   1：可改高   2：无限
            "items": ['source', '|', 'fullscreen', 'undo', 'redo',
                'cut', 'copy', 'paste', 'plainpaste',
                'wordpaste', '|', 'justifyleft', 'justifycenter',
                'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist',
                'indent', 'outdent', 'subscript', 'superscript', '|', 'selectall', '-', 'title',
                'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                'strikethrough', 'removeformat', '|', 'image', 'multiimage', 'advtable', 'hr',
                'emoticons', 'link', 'unlink', 'module'],
            "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
            "afterBlur": function(){this.sync();} //必须，不然提交不到
        });
        //设置编辑器中的内容
        // editor.html("");

        var editor2 = K.editor({
            allowFileManager: true,
            uploadJson: '<?php echo admin_base_url("kindeditor/upload?target=temp&token_id=$token_id");?>'
        });
        var editor3 = K.editor({
            allowFileManager: true,
            uploadJson: '<?php echo admin_base_url("kindeditor/upload?token_id=$token_id");?>'
        });
        //从本地上传图片到图片银行
        K('.from_local').click(function () {
            var lang = $(this).attr('lang');
            //if (lang == 'main'){//才发现主图也可以使用图片银行的，那直接上传到图片银行吧
            //    var editors = editor2;
            //}else if (lang == 'detail'){
            	var editors = editor3;
            //}

            editors.loadPlugin('multiimage', function () {
            	editors.plugin.multiImageDialog({
                    clickFn: function (urlList) {
                        var div, picName;
                        if (lang == 'main'){
                            div = K('.pic-main');
                            picName = 'imgLists';
                        }else if(lang == 'detail'){
                            div = K('.pic-detail');
                            picName = 'detailPicList';
                        }
                        // div.html('');
                        K.each(urlList, function (i, data) {
                            var myli = '<li><div><img src="' + data.url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="'+picName+'[]" value="' + data.url + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                            div.append(myli);
                        });
                        editors.hideDialog();
                    }
                });
            });
        });

        //属性自定义图片上传
        K('.add-custom-image').click(function () {
            var lang = K(this).attr('lang');
            var input_next = K('.customizedPic-'+lang);
            var input_prev = K(this).prev('span');
            editor2.loadPlugin('image', function () {
                editor2.plugin.imageDialog({
                    //imageUrl : K('#url1').val(),
                    showRemote: false,
                    clickFn: function (url, title, width, height, border, align) {
                        input_next.val(url);
                        input_prev.empty();
                        var input_img = '<img src="' + url + '" width="30" height="30">';
                        var input_del = '<a href="javascript: void(0);" class="del-custom-image">删除</a>';
                        input_prev.append(input_img);
                        input_prev.append(input_del);
                        editor2.hideDialog();
                    }
                });
            });
        });


        //点击上传图片
        $('.createw').click(function(){
            $url = '<?php echo admin_base_url("publish/smt/imgWater");?>?imgurl=http://g04.a.alicdn.com/kf/HTB1QFENLVXXXXcpXFXXq6xXFXXXn.jpg';
            createFullWindowTag($url,400,400);
        });
        $("#getskuinfo").click(function(){
            var sku = $("#skuinfo").val();
            if(sku=='')
            {
                return false;
            }

            $.ajax({
                url:'<?php echo admin_base_url("publish/smt/getskuinfo"); ?>',
                data:'sku='+sku,
                type:'POST',
                dataType: 'JSON',
                success: function(data){

                    $("#grossWeight").val(data.data['weight']);
                    $("#packageLength").val(data.data['length']);
                    $("#packageWidth").val(data.data['width']);
                    $("#packageHeight").val(data.data['height']);
                    editor.html('');
                    editor.appendHtml(data.data['products_html_mod']);
                    $("#templateId ").get(0).selectedIndex=1;
                    $("#promiseTemplateId ").get(0).selectedIndex=1;

                   // $(".templateId").find("option[text='one']").attr("selected",true);
                   // $(".promiseTemplateId").find("option[text='Service Template for New Sellers']").attr("selected",true);
                    //  $("#detail").append(data.data['products_html_mod'])
                    /// alert(data.data['weight'])
                }
            })
            //  alert(sku);
        })
    });
    $(function () {
        layer.use('extend/layer.ext.js');
        ///选择关联产品
        $(document).on('click', '.relate-product', function(){
            //使用layer.js弹出层选择产品
            $.layer({
                type   : 2,
                shade  : [0.8 , '' , true],
                title  : ['选择产品',true],
                iframe : {src : '<?php echo admin_base_url("smt/smt_product/selectRelationProducts?token_id=".$token_id);?>'},
                area   : ['900px' , '400px'],
                success : function(){
                    layer.shift('top', 340)
                },
                btns : 2,
                btn : ['确定', '取消'],
                yes : function(index){ //确定按钮的操作
                    var li_num = $('.relate-list').find('li').length;
                    var product_str = '';
                    layer.getChildFrame('.product-list:checked', index).each(function(){

                        var product = $(this).val();
                        if (product != '' && product != 'undefined' && li_num < 8) {
                            var tmp = product.split(',');
                            product_str += '<li><div><img src="'+tmp[1]+'" width="100" height="100" style="border: 0px"><input type="hidden" name="relationProduct[]" value="'+tmp[0]+'" /><a class="pic-del" href="javascript: void(0);">删除</a></div></li>';
                            li_num++;
                        }
                    });
                    $('.relate-list').append(product_str);

                    layer.close(index);
                },
                no: function(index){
                    layer.close(index);
                }
            });
        });

        //图片拖拽排序
        $(".pic-main, .pic-detail, .relate-list").dragsort({ dragSelector: "div",  placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });

        //移开焦点后去空格处理
        $(document).on('blur', ':text, #subject', function () {
            $(this).val($(this).val().trim());
        });

        statisticsLength('subject');
        //标题根据输入字数判断
        $(document).on('keyup', '#subject', function () {
            var num = 0, now_length;
            num = $(this).attr('maxlength');
            now_length = $(this).val();
            $(this).closest('div').find('.help-block').html('还能够输入<i class="red">' + (num - now_length.length) + '</i>个字符');
        });

        //图片增加水印
        $(document).on('click', '.pic-add-water', function () {
            var input_pic = ''; 
            var src = $(this).parent().find('img').attr('src');
            index = $(this).parent().parent().index();
            $url = '<?php echo admin_base_url("publish/waterMark/imgwater");?>?imgsrc='+src+'&token_id='+"<?php echo $token_id;?>";
            createFullWindowTag($url,400,400);
        });

        //删除主图片
        $(document).on('click', '.pic-del', function () {
            //event.preventDefault();
            $(this).closest('li').remove();
        });
        //删除所有主图
        $(document).on('click', '.pic-del-all', function () {
            if (confirm('确认删除全部主图吗？')) {
                $(this).closest('.form-group').find('ul li .pic-del').trigger('click');
            }
        });

        //复制主图信息
        $(document).on('click', '.copy_main_pic', function(){
            var input_pic = '';
            $('.pic-main').find(':input').each(function () {
                var pic = $(this).val();
                if (pic) {
                    input_pic += '<li><div>' +
                    '<img src="' + pic + '" width="100" height="100" style="border: 2px solid white;">' +
                    '<input type="hidden" name="detailPicList[]" value="' + pic + '">' +
                    '<a href="javascript: void(0);" class="pic-del">删除</a>' +
                    '</div></li>';
                }

            });
            $(this).closest('.form-group').find('ul').append(input_pic);
        });


        //详情预览功能
        $(document).on('click', '.detail_view', function(){

            //模板ID
            var templateId = $('#templateId').val();
            //售后ID
            var shouhouId  = $('#shouhouId').val();
            //详情描述标题
            var detailTitle = $('#detail_title').val();
            //详情描述图片
            var detailPicList = '';
            if ($('input[name="detailPicList[]"]').length > 0){
                detailPicList = $('input[name="detailPicList[]"]').map(function(){
                    return $(this).val();
                }).get().join(';');
            }
            //详情描述的内容
            var detail = $('#detail').val();

            //关联的产品id
            var relation_id = '';
            if ($('input[name="relationProduct[]"]').length > 0){
                relation_id = $('input[name="relationProduct[]"]').map(function(){
                    return $(this).val();
                }).get().join(';');
            }
            //关联产品的位置信息
            var relationLocation = $('input[name=relation_loction]:checked').val();

            $.ajax({
                url: '<?php echo admin_base_url("publish/smt/detailView")?>',
                data: {
                    templateId:templateId,
                    shouhouId:shouhouId,
                    detailTitle:detailTitle,
                    detailPicList:detailPicList,
                    detail:detail,
                    relation_id:relation_id,
                    relationLocation: relationLocation
                },
                type: 'POST',
                dataType: 'JSON',
                success: function(data){
                    if (data.status){
                        var html = data.data;
                        $('#showDiv').empty().append(html);
                        $.layer({
                            type: 1,   //0-4的选择,
                            title: ['详情预览', true],
                            border: [0],
                            closeBtn: [0, true],
                            shadeClose: true,
                            offset: ['50px', ''],
                            area: ['1000px', '550px'],
                            page: {
                                //html: html
                                dom: '#showDiv'
                            },
                            success: function(){
                                $('#showDiv').removeClass('hide');
                            }
                        });
                    }

                }
            });
        });
        //单位变更,同时变更其他单位
        $('#productUnit').change(function () {
            var unit_id = $(this).val();
            var name = $(this).find("option:selected").text();
            var new_name = name.split('(');
            $('.unit_cn').html(new_name[0]);
        });

        //折叠显示功能
        $('.hideaccordion h1 i').click(function () {
            if (this.className == 'icon-plus') {
                this.className = 'icon-minus';
                $(this).parents('.row-border').children('.probody').children('.procnt').css('display', 'none');
                $(this).parents('.row-border').children('.probody').children('.promsg').css('display', '');
            } else {
                this.className = 'icon-plus';
                $(this).parents('.row-border').children('.probody').children('.procnt').css('display', '');
                $(this).parents('.row-border').children('.probody').children('.promsg').css('display', 'none');
            }
        });

        //起批数量显示功能
        $('#wholesale').click(function () {
            if (this.checked) {
                $('.wholesale_set').removeClass('hide');
            } else {
                $('.wholesale_set').addClass('hide');
            }
        });

        //打包销售显示功能
        $('#packageType').click(function () {
            if (this.checked) {
                $('.sale_method_set').removeClass('hide');
            } else {
                $('.sale_method_set').addClass('hide');
            }
        });

        //设置操作类型看是保存、发布或者是保存为待发布
        $('.submit_btn').click(function(e){
            if ( e && e.preventDefault )
                e.preventDefault();
            //IE中阻止函数器默认动作的方式
            else
                window.event.returnValue = false;

            $('#action').val($(this).attr('name'));
        })

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


        //属性选择时显示子属性或者显示其他属性 --主要针对多下拉框
        $(document).on('change', '#product_attributes select', function () {
            var value_id_str = $(this).val(); //选择的属性ID,注意值带了名称
            $(this).find('option:not(:selected)').each(function () {
                var isLeaf = $(this).attr('lang');
                var vid_str = $(this).val();
                var vid = vid_str.split('-');
                if (isLeaf == '0') { //说明还是有子属性的, =='1' 是没子属性的，也不管
                    if (!$('.p-' + vid[0]).hasClass('hide')) { //已经隐藏的就不管，不然添加样式隐藏
                        $('.p-' + vid[0]).addClass('hide');
                    }
                }
            });
            var isLeaf_m = $(this).find('option:selected').attr('lang'); //是否还有子属性
            var attr_id = $(this).attr('attr_id'); //属性ID

            if (!value_id_str){ //没有值，说明是选的空的，那所有其他的属性改删的删，该隐藏的已经隐藏了
                if ($('input[name="otherAttributeTxt[' + attr_id + ']"]')) {
                    $('input[name="otherAttributeTxt[' + attr_id + ']"]').closest('.form-group').remove();
                }
                return false;
            }
            var value_id = value_id_str.split('-');
            if (isLeaf_m == '0') {
                if ($('.p-' + value_id[0]).hasClass('hide')) { //已经隐藏的就去除这个属性
                    $('.p-' + value_id[0]).removeClass('hide');
                }
            }

            //console.log(attr_id);
            if (value_id && value_id[1].toLowerCase() == 'other') { //添加选其他时的输入项
                //在后边添加个输入框
                var input_html = '<div class="form-group">';
                input_html += '<div class="col-sm-10 col-sm-offset-2">';
                input_html += '<input type="text" class="form-control" name="otherAttributeTxt[' + attr_id + ']" />';
                input_html += '</div>';
                input_html += '</div>';
                $(this).closest('.form-group').after(input_html);
            } else { //删除
                if ($('input[name="otherAttributeTxt[' + attr_id + ']"]')) {
                    $('input[name="otherAttributeTxt[' + attr_id + ']"]').closest('.form-group').remove();
                }
            }
        });

        //多属性的零售价\库存批量操作
        $(document).on('click', '#batch-price-btn, #batch-stock-btn', function () {
            var btn_id = $(this).attr('id'); //按钮ID
            var input_id, target_class, input_val;
            //批量设置的值
            var input_num = $('#' + input_id).val();
            if (btn_id == 'batch-price-btn') { //批量设置价格
                input_id = 'batch-price';
                target_class = 'sku-price';
            } else if (btn_id == 'batch-stock-btn') {
                input_id = 'batch-stock';
                target_class = 'sku-quantity';
            }
            input_val = $('#' + input_id).val();
            if (parseFloat(input_val.trim()) > 0) {
                $('.' + target_class).val(input_val);
            } else {
                showxbtips('请先输入值', 'alert alert-warning');
                $('#' + input_id).select();
            }
        });

        //批量设置SKU
        $(document).on('click', '#batch-sku-btn', function () {
            var this_id = $(this).attr('id');
            var source_id = 'batch-sku';//资源ID
            var source_val = $('#' + source_id).val().trim();
            var ajax_flag = null;
            if (ajax_flag) {
                $.abort(ajax_flag);
                $(this).text('确定');
            } else {
                if (source_val != '') { //已经设置SKU了
                    $.ajax({
                        url: '<?php echo admin_base_url("smt/smt_product/recommendProductList");?>',
                        data: 'sku=' + source_val,
                        type: 'POST',
                        dataType: 'JSON',
                        success: function (data) {
                            if (data.status) {
                                skuObj = data.data;
                            }
                            if (data.data) { //有查找到数据
                                $('.sku-table>tbody>tr').each(function () {
                                    var target_td = $(this).find('td:last');
                                    var target_input = target_td.find('input');
                                    var selected = target_input.val();
                                    var input_txt = parseSkuList(data.data, selected);

                                    target_td.find('.sku_select, .sku_reinput').remove();
                                    input_txt += '<a class="btn btn-primary btn-xs sku_reinput" title="手动输入"><i class="icon-mail-reply"></i></a>';
                                    target_input.after(input_txt);

                                    target_input.attr('type', 'hidden');
                                });
                            }
                        },
                        beforeSend: function () {
                            $('#' + this_id).text('查询中');
                        },
                        complete: function () {
                            $('#' + this_id).text('确定');
                        }
                    });
                } else {
                    showtips('请先录入要筛选的SKU', 'alert-warning');
                }
            }
        });

        //SKU选择触发事件，变更隐藏域的值
        $(document).on('change', '.sku_select', function () {
            var new_val = $(this).val();
            var input = $(this).closest('td').find('.sku-code');
            input.attr('value', new_val);
        });

        //单个下拉框变成输入框
        $(document).on('click', '.sku_reinput', function () {
            $(this).closest('td').find('.sku-code').attr('type', 'text');
            $(this).closest('td').find(':not(".sku-code")').remove();
        });

        //添加自定义属性
        $('#add_row').on('click', function () {
            var input_row = '<div class="form-group">'
                + '<label class="col-sm-2">&nbsp;</label>'
                + '<div class="col-sm-6">'
                + '<div class="col-sm-5">'
                + '<input class="form-control" type="text" name="custom[attrName][]" />'
                + '</div>'
                + '<div class="col-sm-5">'
                + '<input class="form-control" type="text" name="custom[attrValue][]" />'
                + '</div>'
                + '<div class="col-sm-2">'
                + '<a class="btn btn-success btn-sm del_row">删除</a>'
                + '</div>'
                + '</div>'
                + '</div>';
            $(this).closest('.form-group').before(input_row);
        });

        //删除自定义属性
        $(document).on('click', '.del_row', function () {
            $(this).closest('.form-group').remove();
        });

        //SKU属性图片自定义删除
        $(document).on('click', '.del-custom-image', function () {
            $(this).closest('td').find('.customized-pic-input').val('');
            $(this).closest('.customize-pic').empty();
        });

        //多属性对应
        $(document).on('click', '.s_attr :checkbox', function () {
            //选择的计量单位
            var unit_name = $('#productUnit').find("option:selected").text();
            var unit_tmp_arr = unit_name.split('(');
            var unit = unit_tmp_arr[0];
            //首先看是否显示对应的自定义信息
            var parent_this = $(this).closest('.s_attr');

            var sibling_id_this = parent_this.find(':checked').map(function () {
                return $(this).val();
            }).get().join(',');
            var value_id_this = $(this).val();
            //本条属性的ID
            var attr_id_this = parent_this.attr('attr_id');
            //是否自定义属性
            var custome_flag = parent_this.attr('custome');

            var parent_index = parent_this.index('.s_attr');

            //所有的SKU属性的值
            var all_value_id = $('.s_attr').find(':checked').map(function () {
                return $(this).val();
            }).get().join(',');
            if (all_value_id != '') { //说明有选择多属性
                $('#productPrice, #productStock, #productCode').addClass('hide').closest('.form-group').addClass('hide');
            } else {
                $('#productPrice, #productStock, #productCode').removeClass('hide').closest('.form-group').removeClass('hide');
            }

            //选择的SKU属性数组
            var attr_value_all = new Array();

            if (sibling_id_this != '') {
                custome_flag == '1' ? $('#custome-' + attr_id_this).closest('.form-group').removeClass('hide') : '';
            } else {
                custome_flag == '1' ? $('#custome-' + attr_id_this).closest('.form-group').addClass('hide') : '';
            }

            //读取已经存在的行tr的className
            var now_class = new Array(); //已有的样式
            if ($('.sku-table tbody').find('tr')) {
                $('.sku-table tbody').find('tr').each(function () {
                    now_class.push($(this).attr('class'));
                });
            }

            var attr_arr = new Array(); //选择的属性ID数组
            var attr_array = new Array(); //所有的属性ID数组
            //var allAttrValueArr = new Array(); //所有的属性的值的数组，以属性为下标
            //其他SKU属性的ID
            $('.s_attr').each(function (index, el) {
                attr_value_all[index] = new Array();

                if ($(this).find(':checked').length == 0) {
                    attr_value_all[index].push("");
                    attr_arr[index] = '';
                } else {
                    $(this).find(':checked').each(function () {
                        attr_value_all[index].push($(this).val());
                    });
                    attr_arr[index] = $(this).attr('attr_id');
                }
                attr_array[index] = $(this).attr('attr_id'); //所有的属性ID数组
            });

            var result_cal = new Array(); //样式结果数组
            toResult(attr_value_all, 0, '', result_cal);

            //显示具体的属性
            if (this.checked) {
                $('.tr-p-' + attr_id_this + '-' + value_id_this).removeClass('hide');
                //显示多属性，添加多属性对应的行，并隐藏单属性的设置字段
                $('.sku-table').closest('.form-group').removeClass('hide');
                //显示头信息中的列
                $('.sku-a-' + attr_id_this).removeClass('hide');

                //应该含有我的样式
                var my_class = result_cal;
                //var my_class = getMineClass(result_cal, parent_index, value_id_this, 'add');

                //添加的时候的位置判断
                //点击的位置的所有属性值数组
                var this_val_all = new Array(); //当前属性的所有的值
                parent_this.find(':checkbox').each(function(){
                    this_val_all.push($(this).val());
                });


                //var selectedAllAttrValueArr = new Array(); //之前已选择的所有属性的数组

                //判断是添加行，还是替换样式
                for (var i = 0; i < my_class.length; i++) {
                    var flag = false;
                    var isExist = false;

                    for (var j = 0; j < now_class.length; j++) {
                        if (now_class[j] == 'coord-' +[my_class[i]]) {
                            isExist = true;
                            break;
                        }
                    }
                    if (isExist){
                        continue;
                    }

                    var tmp = my_class[i].split('_');
                    tmp[parent_index] = '';

                    for (var j = 0; j < now_class.length; j++) {
                        if (now_class[j] == 'coord-' + tmp.join('_')) {
                            flag = true;
                        }
                    }

                    if (flag) { //存在了，要替换下样式

                        //当前的索引位置
                        var divIndex = parent_this.index('.s_attr');

                        var showed=0; //已经显示的页面
                        for (var kk=0; kk<divIndex; kk++){
                            var one = $('.s_attr:eq('+kk+')').find(':checked').map(function(){
                                return $(this).val();
                            }).get().join(',');
                            if (one != ''){
                                showed++;
                            }
                        }

                        //添加单元格
                        $('tr.coord-' + tmp.join('_')).find('td:eq(' + (showed) + ')').before('<td class="td-' + attr_arr[parent_index] + '-' + value_id_this + '">' + skuConfig[attr_arr[parent_index]][value_id_this]['zh'] + '</td>');

                        var old_class = 'coord-' + tmp.join('_');
                        tmp[parent_index] = value_id_this;
                        var new_class = 'coord-' + tmp.join('_');
                        $('.' + old_class).addClass(new_class).removeClass(old_class);

                        //name名称
                        var input_name_arr = new Array(); //名输入框名称的属性值数组
                        for (var k = 0; k < tmp.length; k++) {
                            input_name_arr.push(attr_array[k] + '_' + tmp[k]);
                        }
                        $('.' + new_class).find('.sku-price').attr('name', 'skuPrice[' + input_name_arr.join('-') + ']');
                        $('.' + new_class).find('.sku-quantity').attr('name', 'skuStock[' + input_name_arr.join('-') + ']');
                        $('.' + new_class).find('.sku-code').attr('name', 'skuCode[' + input_name_arr.join('-') + ']');
                    } else {//不存在，直接添加吧

                        tmp[parent_index] = value_id_this;

                        //组装行字段
                        var td = '';
                        for (var z = 0; z < attr_arr.length; z++) {
                            if (tmp[z] != '') {
                                td += '<td class="td-' + attr_arr[z] + '-' + tmp[z] + '">' + skuConfig[attr_arr[z]][tmp[z]]['zh'] + '</td>';
                            }
                        }

                        var input_name_arr = new Array(); //名输入框名称的属性值数组
                        for (var k = 0; k < tmp.length; k++) {
                            input_name_arr.push(attr_array[k] + '_' + tmp[k]);
                        }

                        var input_fixed = '<td>US $<input type="text" class="sku-price" size="8" maxlength="8" datatype="numrange" min="0" max="100000" nullmsg="零售价在0-100000范围内" errormsg="零售价格式错误" ignore="ignore" name="skuPrice[' + input_name_arr.join('-') + ']" />/<span class="unit_cn">' + unit + '</span></td>'
                            + '<td><input type="text" class="sku-quantity" size="6" maxlength="6" datatype="numrange" max="999999" min="0" nullmsg="库存范围为0-999999" errormsg="库存错误" ignore="ignore" name="skuStock[' + input_name_arr.join('-') + ']" /></td>';
                        if (skuObj) {
                            input_fixed += '<td><input type="hidden" class="sku-code" maxlength="20" name="skuCode[' + input_name_arr.join('-') + ']" />' + parseSkuList(skuObj, "") + '<a class="btn btn-primary btn-xs sku_reinput" title="手动输入"><i class="icon-mail-reply"></i></a></td>';
                        } else {
                            input_fixed += '<td><input type="text" class="sku-code" maxlength="20" name="skuCode[' + input_name_arr.join('-') + ']" /></td>';
                        }

                        var target = '<tr class="coord-' + tmp.join('_') + '">' + td + input_fixed + '</tr>';

                        //$('.sku-table tbody').append(target);
                        if ($('.sku-table tbody tr').length < i+1){
                            $('.sku-table tbody').append(target);
                        }else {
                            $('.sku-table tbody tr:eq('+(i)+')').before(target);
                        }
                    }
                }
            } else {
                //这个只对自定义的有用
                $('.tr-p-' + attr_id_this + '-' + value_id_this).addClass('hide');

                if (all_value_id == '') {
                    $('.sku-table').closest('.form-group').addClass('hide');
                    $('.sku-table tbody').empty();
                } else {
                    if (sibling_id_this != '') { //还是有这个属性的，直接删除有这个属性的行
                        $('.sku-table tbody tr').find('td:eq(' + parent_index + ')').each(function () {
                            var tr_class = $(this).closest('tr').attr('class');
                            var tmp = tr_class.split('-');
                            var tmp2 = tmp[1].split('_');

                            if (tmp2[parent_index] == value_id_this) { //含有这个属性，直接删除吧
                                $('.' + tr_class).remove();
                            }
                        });
                    } else {
                        //该属性所有的信息不存在就直接隐藏
                        $('.sku-a-' + attr_id_this).addClass('hide');
                        //删除本列吧
                        $('.td-' + attr_id_this + '-' + value_id_this).remove();

                        //剩下的所有行应该是含有这个属性的，还是都删除吧
                        $('.sku-table tbody tr').each(function () {
                            //本行的原样式
                            var old_class = $(this).attr('class');

                            //本行的样式也要改变下
                            var tmp = old_class.split('-');
                            var tmp2 = tmp[1].split('_');
                            tmp2[parent_index] = '';

                            tmp[1] = tmp2.join('_');
                            var new_class = tmp.join('-');
                            $(this).addClass(new_class).removeClass(old_class);

                            //name名称
                            var input_name_arr = new Array(); //名输入框名称的属性值数组
                            for (var k = 0; k < tmp2.length; k++) {
                                input_name_arr.push(attr_array[k] + '_' + tmp2[k]);
                            }
                            $(this).find('.sku-price').attr('name', 'skuPrice[' + input_name_arr.join('-') + ']');
                            $(this).find('.sku-quantity').attr('name', 'skuStock[' + input_name_arr.join('-') + ']');
                            $(this).find('.sku-code').attr('name', 'skuCode[' + input_name_arr.join('-') + ']');
                        });
                    }
                }
            }
        });
    });



    /**
     * 计算一个二维数组的笛卡尔积，并返回一维数组信息
     * zz:二维数组信息
     * arrIndex:第一个索引，写0就好
     * aresult:写""空就好
     * result:返回的结果会存里边
     */
    function toResult(zz, arrIndex, aresult, result) {
        if (arrIndex >= zz.length) {
            result.push(aresult.substr(1));
            return;
        }
        ;
        var aArr = zz[arrIndex];
        if (!aresult) aresult = '';
        for (var i = 0; i < aArr.length; i++) {
            var theResult = aresult.substr(0, aresult.length);
            theResult += '_' + aArr[i];
            toResult(zz, arrIndex + 1, theResult, result);
        }
    }
    /**
     * 获取含有选中元素的所有样式
     * zz:大的一维数组
     * myIndex:选中元素父元素的索引
     * val:选中元素的值
     * act:增还是消--add,del
     */
    function getMineClass(zz, myIndex, val, act) {
        var res = new Array();
        if (zz.length > 0) {
            for (var i = 0; i < zz.length; i++) {
                var tmp = zz[i].split('_');
                if (act == 'add') {
                    if (tmp[myIndex] == val) {
                        res.push(tmp.join('_'));
                    }
                } else {
                    if (tmp[myIndex] != val) {
                        res.push(tmp.join('_'));
                    }
                }
            }
        }
        return res;
    }
    /**
     * 同步并刷新分组(包括运费模板、产品分组、服务模板)
     * @param obj
     * @param token_id
     * @param selected
     */
    function refresh_template(obj, token_id, selected) {
        //event.preventDefault();
        var this_id = obj.id; //改对象的ID
        var temp = this_id.split('_');
        var target_id = temp[0];

        if (token_id && target_id) {
            var url = '';
            var txt = '';
            switch (target_id) {
                case 'groupId':
                    url = '<?php echo admin_base_url("smt/smt_product/getProductGroup");?>';
                    txt = '产品分组';
                    break;
                case 'promiseTemplateId':
                    url = '<?php echo admin_base_url("smt/smt_product/getServiceTemplateList");?>';
                    txt = '服务模板';
                    break;
                case 'freightTemplateId':
                    url = '<?php echo admin_base_url("smt/smt_product/getFreightTemplateList");?>';
                    txt = '运费模板';
                    break;
                case 'module':
                    url = '<?php echo admin_base_url("smt/smt_product/getProductModuleList");?>';
                    txt = '产品信息模板';
                    break;
                case 'shouhouId': //售后模板ID
                    url = '<?php echo admin_base_url("publish/after_sales_service/ajaxSmtAfterServiceList");?>';
                    txt = '售后服务模板';
                    break;
                case 'templateId': //模板ID
                    url = '<?php echo admin_base_url("publish/smt_template/ajaxGetPlatTemplateList?plat=6");?>';
                    txt = '模板';
                    break;
            }

            $.ajax({
                url: url,
                data: 'token_id=' + token_id + '&return=data&selected=' + selected,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if (data.status) {
                        var options = '<option>--请选择' + txt + '--</option>';
                        options += data.data;
                        $('#' + target_id).empty().append(options);
                    } else {
                        showtips(data.info, 'alert-warning');
                    }
                },
                beforeSend: function () {
                    $('#' + this_id).addClass('disabled').attr('title', '同步中');
                },
                complete: function () {
                    $('#' + this_id).removeClass('disabled').attr('title', '同步');
                }
            });
        } else {
            showtips('请先选择账号', 'alert-warning');
        }
    }
    /**
     * SKU数据对象，选择的列表
     * @param dataObj
     * @param selected
     */
    function parseSkuList(dataObj, selected) {
        var input = '<select class="sku_select">';
        input += '<option>-请选择-</option>';
        $.each(dataObj, function (index, el) {
            input += '<option value="' + el + '"' + (selected == el ? ' selected="selected"' : '') + '>' + el + '</option>';
        });
        input += '</select>';
        return input;
    }
    /**
     * 根据输入的值统计标题的字数
     * @param id
     */
    function statisticsLength(id) {
        var num = 0, now_length;
        num = $('#' + id).attr('maxlength');
        now_length = $('#' + id).val();
        $('#' + id).closest('div').find('.help-block').html('还能够输入<i class="red">' + (num - now_length.length) + '</i>个字符');
    }

    /**
     * 按SKU目录添加图片
     * @param obj
     */
    function addDir(obj, url, token_id, opt){
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
                data: 'token_id='+token_id+'&dirName='+pass+'&opt='+opt,
                type: 'POST',
                dataType: 'JSON',
                success: function(data){
                    $('.ajax-loading').addClass('hide');
                    if (data.status){
                        if (data.data){ //说明有成功的，成功的添加到里边去
                            var liStr = '';
                            $.each(data.data, function(index, el){
                                liStr += '<li><div><img src="' + el + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="' + el + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                            });
                            $(obj).closest('div.form-group').find('ul').append(liStr);
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
     * 复制图片到...
     */
    function copyPicTo(obj, targetClass, count){
        var li = $(obj).closest('div.form-group').find('li');
        if (li.length == 0){
            layer.msg('<font color="red">还没有图片信息，请先上传</font>', 2, -1);
            return false;
        }

        //查看下目标已经有的数量
        var hasCount = 0;
        var picName = ''; //主图的图片列表的名称
        if (targetClass == 'pic-main'){ //是主图--主图有限制就是6张
            hasCount = $('.'+targetClass).find('li').length;
            picName = 'imgLists';
        }

        //总数少于要上传的数量，就用总的
        count = (count - hasCount) > li.length ? li.length : (count - hasCount);
        if (count == 0){ //不需要复制过去了
            layer.msg('<font color="red">图片已达到限制，无需再复制</font>', 2, -1);
            return false;
        }

        var newLiStr = '';
        for (var i=0; i<count; i++){
            var imgUrl = $(li[i]).find('img').attr('src');
            newLiStr += '<li><div><img src="' + imgUrl + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="'+picName+'[]" value="' + imgUrl + '" /><a href="javascript: void(0);" class="pic-del">删除</a>&nbsp;<a href="javascript: void(0);" class="pic-add-water">水印</a></div></li>';
        }
        $('.'+targetClass).append(newLiStr);
    }

    //从别处复制图片到这
    function copyToHere(obj, fromClass, url){
        var imgList = $('.'+fromClass).find('li img');

        if (imgList.length == 0){
            layer.msg('<font color="red">请先添加描述图片</font>', 1, -1);
            return false;
        }

        var imgStr = '<div style="width: 580px; height: 230px; overflow-y: auto; padding: 5px;">' +
            '<ul class="list-inline my-list-cust">';
        for (var i=0; i<imgList.length; i++){
            imgStr += '<li><img src="'+imgList[i].src+'" width="100" height="100" /><input type="checkbox" class="my-check-cust" value="'+imgList[i].src+'" /></li>';
        }
        imgStr += '</ul></div>';
        $.layer({
            type   : 1,
            shade  : [0.8 , '' , true],
            offset: ['50px', ''],
            title  : ['选择自定义图片',true],
            area   : ['580px' , '310px'],
            btns : 2,
            btn : ['确定', '取消'],
            yes : function(index){ //确定按钮的操作
                var checkImg = $('.my-list-cust .my-check-cust:checked');
                if (checkImg.length == 1){
                    //获取到图片，并上传吧
                    var img = checkImg.val();

                    $.ajax({
                        url: url,
                        data: 'img='+img+'&'+Math.random(),
                        type: 'POST',
                        dataType: 'JSON',
                        success: function(data){
                            var input_prev = $(obj).closest('td').find('span'); //图片信息等
                            input_prev.empty();
                            $(obj).closest('td').find('input').val(data.data);

                            if (data.status){ //成功了
                                var input_img = '<img src="' + data.data + '" width="30" height="30">';
                                var input_del = '<a href="javascript: void(0);" class="del-custom-image">删除</a>';
                                input_prev.append(input_img);
                                input_prev.append(input_del);
                            }else { //失败了
                                layer.msg('<font color="red">图片上传自定义属性图片失败'+data.info+'</font>', 1, !1);
                            }
                        }
                    });
                }
                layer.close(index);
            },
            no: function(index){
                layer.close(index);
            },
            page: {
                html: imgStr
            },
            success: function(){
                //点击复选框
                $('.my-list-cust .my-check-cust').on('click', function(){
                    if (this.checked){
                        $(this).closest('li').siblings().find(':checkbox').prop('checked', false);
                    }
                });
                //点击图片
                $('.my-list-cust li img').on('click', function(){
                    $(this).closest('li').find(':checkbox').trigger('click');
                });
            }
        });
    }



</script>