<?php
/**
 * AMZ数据模板编辑页面
 */
echo ace_header('AMZ数据模板');//$productImages->id

echo ace_form_open('', array('class' => 'form-horizontal dataForm'), array('id' => $id));
?>
<style>
    .pic-list {
        padding: 5px;
        border: 1px solid #ccc;
        /*min-height: 100px;*/
    }
    .pic-list li {
        margin: 5px;
        padding: 0px;
        border: 0px;
        width: 102px;
        text-align: right;
    }
    .pic-list .placeHolder {
        width: 102px;
        height: 125px;
        background-color: white !important;
        border: dashed 1px gray !important;
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
</style>
<div>
    <!--分类-->
    <div class="form-group">
        <label class="control-label col-sm-2"><span class="red">*</span>分类：</label>
        <div class="col-sm-2">
            <select class="form-control" name="category" datatype="*" nullmsg="分类不能为空" errormsg="分类信息错误" onchange="selectAttrList(this);">
                <option value="">=请选择分类=</option>
                <?php
                $attributeList = array();
                $nowAttrOptions = array(); //当前分类已有的属性
                $nowAttrOptionsStr = '<option>=选择=</option>';
                if (!empty($myCateList)):
                foreach ($myCateList as $row){
                    echo '<option value="'.$row['id'].'"'.((!empty($templateInfo['category']) && $templateInfo['category'] == $row['id']) ? ' selected="selected"' : '').'>'.$row['category_us'].'</option>';
                    $attributeList[$row['id']] = !empty($row['attribute']) ? unserialize($row['attribute']) : '';
                    if (!empty($templateInfo['category']) && $templateInfo['category'] == $row['id']){
                        $nowAttrOptions = !empty($row['attribute']) ? unserialize($row['attribute']) : '';
                    }
                }
                endif;

                if (!empty($nowAttrOptions)){
                    foreach ($nowAttrOptions as $v){
                        $nowAttrOptionsStr .= '<option value="'.$v.'">'.$v.'</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <!--recommended_browse_nodes1-->
    <?php
    $node1 = $node2 = $node3 = '';
    if (!empty($templateInfo['nodes'])){
        list($node1, $node2, $node3) = explode(';', $templateInfo['nodes']);
    }
    ?>
    <div class="form-group p-nodes">
        <label class="control-label col-sm-2"><span class="red">*</span>recommended _browse_nodes：</label>
        <div class="col-sm-2">
            <input class="form-control" type="text" name="nodes[]" datatype="n" nullmsg="recommended _browse_nodes不能为空" errormsg="recommended _browse_nodes必须是数字" value="<?php echo $node1;?>"/>
        </div>
        <div class="col-sm-2">
            <input class="form-control" type="text" name="nodes[]" placeholder="(选填)" ignore="ignore" datatype="n" nullmsg="recommended _browse_nodes不能为空" errormsg="recommended _browse_nodes必须是数字" value="<?php echo $node2;?>"/>
        </div>
        <div class="col-sm-2">
            <input class="form-control" type="text" name="nodes[]" placeholder="(选填)" ignore="ignore" datatype="n" nullmsg="recommended _browse_nodes不能为空" errormsg="recommended _browse_nodes必须是数字" value="<?php echo $node3;?>"/>
        </div>
        <!--<label class="control-label">-->
        <!--    <a href="javascript:" title="添加recommended_browse_nodes" onclick="addRow(this, 'nodes');"><i class="icon-plus green bigger-150"></i></a>-->
        <!--</label>-->
    </div>

    <!--标题-->
    <?php
    $titleList = array();
    $firstTitle = '';
    if (!empty($templateInfo['title'])){
        $titleList = explode('-||-', $templateInfo['title']);
        $firstTitle = array_shift($titleList);
    }
    ?>
    <div class="form-group p-title">
        <label class="control-label col-sm-2"><span class="red">*</span>标题：</label>
        <div class="col-sm-8">
            <input class="form-control" type="text" name="item_name[]" datatype="*" nullmsg="标题不能为空" errormsg="标题不能为空" value="<?php echo $firstTitle;?>"/>
        </div>
        <label class="control-label">
            <a href="javascript:" title="添加标题" onclick="addRow(this, 'title');"><i class="icon-plus green bigger-150"></i></a>
        </label>
        &nbsp;&nbsp;
        <label class="control-label">
            <a href="javascript:" title="删除其他标题" onclick="deleteOtherRows(this, 'title');"><i class="icon-trash red bigger-150"></i></a>
        </label>
    </div>
    <?php
    if (!empty($titleList)):
        foreach ($titleList as $title):
            echo '<div class="form-group p-title"><div class="col-sm-8 col-sm-offset-2"><input class="form-control" type="text" value="'.$title.'" name="item_name[]"></div><label class="control-label"><a href="javascript:" title="删除此标题" onclick="delTitle(this);"><i class="icon-remove red bigger-150"></i></a></label></div>';
        endforeach;
    endif;
    ?>

    <!--SKU-->
    <?php
    $parentSku    = ''; //父SKU
    $parentPrice  = 0;  //父SKU价格
    $skuHtml      = ''; //SKU信息
    $attributeStr = ''; //属性列表
    $attrCount    = 0; //已有的属性的索引
    $attribute    = array();//解析出来的属性信息
    $parentPic    = ''; //主图
    $parentPicStr = ''; //主图字段
    $childPicHtml = ''; //子SKU详情图列表
    if (!empty($templateInfo['attribute'])){
        $attribute = unserialize($templateInfo['attribute']);
        foreach ($attribute as $key => $at){
            $attributeStr .= '<th>';
            $attributeStr .= '<select name="attr['.$key.']" style="width: 90px;" class="attr-options">';
            $attributeStr .= '<option>=选择=</option>';

            if (!empty($nowAttrOptions)){
                foreach ($nowAttrOptions as $item){
                    $attributeStr .= '<option value="'.$item.'" '.($at == $item ? 'selected="selected"' : '').'>'.$item.'</option>';
                }
            }
            $attributeStr .= '</select>';
            $attributeStr .= '&nbsp;<a href="javascript:" title="删除属性列" onclick="delAttCol(this);"><i class="icon-remove red"></i></a>';
            $attributeStr .= '</th>';
            $attrCount     = $key+1;
        }
    }

    if (!empty($skuList)){

        foreach ($skuList as $row){
            if ($row['isParent']){
                $parentSku   = $row['sku'];
                $parentPic   = $row['picLists'];
                $parentPrice = $row['price'];
                if (!empty($row['picLists'])) {
                    $picArr = explode(';', $row['picLists']);
                    foreach ($picArr as $val) {
                        $parentPicStr .= '<li><img src="'.$val.'" width="100" height="100" style="border: 0px;"><a href="javascript: void(0);" onclick="delOnePic(this);">删除</a></li>';
                    }
                }
                continue;
            }
            $skuHtml .= '<tr>';
            $skuHtml .= '<td><input type="text" name="item_sku[]" onblur="modPicField(this);" value="'.$row['sku'].'"></td>';
            $skuHtml .= '<td><input type="text" name="item_price[]" size="6" datatype="num,numrange" nullmsg="售价不能为空" errormsg="售价格式错误" min="0.01" ignore="ignore" value="'.$row['price'].'"></td>';
            if (!empty($templateInfo['discount'])){ //折扣价
                $skuHtml .= '<td><input type="text" name="discount_price[]" size="6" datatype="num,numrange" nullmsg="折扣价不能为空" errormsg="折扣价格式错误" min="0.01" ignore="ignore" value="' . ($row['discountPrice'] > 0 ? $row['discountPrice'] : '') . '"></td>';
            }

            if (!empty($attribute)) { //输出属性信息
                $perperty = !empty($row['property']) ? unserialize($row['property']) : array();
                foreach ($attribute as $key => $attr){
                    $skuHtml .= '<td><input type="text" size="12" name="attrVal['.$key.'][]" value="'.(!empty($perperty[$key]) ? $perperty[$key] : '').'" /></td>';
                }
            }
            $skuHtml .= '<td class="middle"><label><a href="javascript:" title="删除" onclick="delTbRow(this);"><i class="icon-remove  bigger-150 red"></i></a></label></td>';
            $skuHtml .= '</tr>';

            //子SKU的图片列表
            $childPicHtml .= '<div class="form-group pic-field">';
            $childPicHtml .= '<label class="control-label col-sm-2"><span class="red">*</span>子SKU(<span class="childSKu">'.$row['sku'].'</span>)图片列表：</label>';
            $childPicHtml .= '<div class="col-sm-8"><div><a href="javascript:void(0);" class="btn btn-default btn-sm from_local" lang="detail" onclick="copyMainPic(this);">复制主图片</a><a href="javascript:" class="btn btn-default btn-sm" onclick="addPicLink(this);">添加外链</a> <a class="btn btn-xs btn-primary pic-del-all" title="全部删除" href="javascript:" onclick="delSiblingsPics(this);"><i class="icon-trash"></i></a></div>';
            $childPicHtml .= '<ul class="list-inline pic-list pq-list">';
            $picArr = explode(';', $row['picLists']);
            foreach ($picArr as $val) {
                $childPicHtml .= '<li><img src="'.$val.'" width="100" height="100" style="border: 0px;"><a href="javascript: void(0);" onclick="delOnePic(this);">删除</a></li>';
            }
            $childPicHtml .= '</ul>';
            $childPicHtml .= '<input type="hidden" name="picLists[]" value="'.$row['picLists'].'"></div></div>';
        }
    }
    ?>
    <div class="form-group p-sku">
        <label class="control-label col-sm-2"><span class="red">*</span>SKU：</label>
        <div class="col-sm-2">
            <input class="form-control" type="text" name="item_sku[]" datatype="*1-128" nullmsg="SKU不能为空" errormsg="SKU格式错误" id="parentSku" value="<?php echo $parentSku;?>"/>
        </div>
        <label class="control-label">
            <a href="javascript:" title="添加子SKU" onclick="addChildSkuTbRow(this, false);"><i class="icon-plus green bigger-150"></i></a>
        </label>
        &nbsp;&nbsp;
        <label class="control-label">
            <a href="javascript:" title="删除其他SKU" onclick="delAllChildSku();"><i class="icon-trash red bigger-150"></i></a>
        </label>
        &nbsp;&nbsp;
        <label class="control-label">
            <a href="javascript:" title="获取子SKU" id="searchChildSku" onclick="addChildSkuTbRow(this, true, '<?php echo admin_base_url("amz/template/ajaxGetChildSkus");?>');"><i class="icon-search bigger-150"></i></a>
        </label>
        <label class="control-label p-sku-help hide">
            <b>获取中...</b>
        </label>
    </div>

    <div class="form-group childSku-field <?php echo !empty($skuHtml) ? '' : 'hide';?>">
        <label class="col-sm-2 control-label">
            子SKU列表:
        </label>

        <div class="col-sm-8">
            <table class="table table-bordered table-vcenter">
                <caption class="left">
                    <!--父子SKU划分依据:variation_theme这个字段; 要根据划分依据来显示子SKU-->
                    <?php
                    //划分依据
                    $theme = !empty($templateInfo['theme']) ? $templateInfo['theme'] : '';
                    ?>
                    <div class="form-group" style="margin-bottom: 2px;">
                        <label class="col-sm-2 control-label">划分依据:</label>
                        <div class="col-sm-3">
                            <input type="text" name="theme" value="<?php echo $theme;?>" id="theme" class="form-control"/>
                        </div>
                        <a href="javascript:" class="btn btn-sm col-sm-2" onclick="addAttribute(this);">添加属性</a>
                    </div>

                    <div class="form-group" style="margin-bottom: 2px;">
                        <label class="col-sm-2 control-label">售价:</label>
                        <div class="col-sm-2">
                            <input type="text" name="batchPrice" value="" class="form-control"/>
                        </div>
                        <a href="javascript:" class="btn btn-sm col-sm-1" onclick="batchSetPrice(this);">设置</a>

                        <label class="col-sm-2 control-label">折扣价:</label>
                        <div class="col-sm-2">
                            <input type="text" name="batchWholePrice" value="" class="form-control"/>
                        </div>
                        <a href="javascript:" class="btn btn-sm col-sm-1" onclick="batchSetPrice(this, 'discount');">设置</a>
                    </div>
                </caption>
                <thead>
                    <tr>
                        <th class="th-fixed">SKU</th>
                        <th class="th-fixed">售价</th>
                        <?php if (!empty($templateInfo['discount'])): //有选择折扣及子SKu才显示 ?>
                        <th class="th-fixed th-discount">折扣价</th>
                        <?php endif;?>
                        <?php echo $attributeStr;?>
                        <th class="th-fixed">操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php echo $skuHtml;?>
                </tbody>
            </table>
        </div>
    </div>

    <!--售价-->
    <div class="form-group standard_price <?php echo !empty($skuHtml) ? 'hide' : '';?>">
        <label class="control-label col-sm-2"><span class="red">*</span>售价：</label>
        <div class="col-sm-2 input-group">
            <input class="form-control" type="text" name="standard_price" datatype="num,numrange" nullmsg="售价不能为空" errormsg="售价格式错误" value="<?php echo $id ? $templateInfo['price'] : '';?>" min="0.01" ignore="ignore"/>
            <div class="input-group-addon">USD</div>
        </div>
    </div>

    <!--打包销售-->
    <div class="form-group">
        <label class="control-label col-sm-2"><span class="red">*</span>打包数量：</label>
        <div class="col-sm-2">
            <input class="form-control" type="text" name="item_package_quantity" value="1" datatype="n, numrange" nullmsg="打包数量不能为空" errormsg="打包数量格式错误" value="<?php echo empty($templateInfo['packageNum']) ? 1 : $templateInfo['packageNum'];?>"/>
        </div>
    </div>

    <!--打折-->
    <div class="form-group">
        <label class="control-label col-sm-2">打折：</label>
        <div class="col-sm-3">
            <div class="checkbox">
                <label for="discount"><input type="checkbox" name="discount" id="discount" value="1" class="discount" <?php echo !empty($templateInfo['discount']) && $templateInfo['discount'] == 1 ? 'checked' : '';?>>是</label><!--datatype="discount" errormsg="折扣详情信息错误"-->
            </div>
        </div>
    </div>

    <!--折扣详情-->
    <div class="form-group p-discount<?php echo !empty($templateInfo['discount']) && $templateInfo['discount'] == 1 ? '' : ' hide';?>">
        <label class="col-sm-2 control-label">
            <span class="red">*</span>折扣价：
        </label>
        <div class="col-sm-2 input-group <?php echo !empty($skuHtml) ? 'hide' : '';?>">
            <input type="text" class="form-control" name="sale_price" value="<?php echo $id ? $templateInfo['discountPrice'] : '';?>" min="0.01" datatype="numrange" />
            <div class="input-group-addon">
                USD
            </div>
        </div>

        <label class="col-sm-1 control-label">
            <span class="red">*</span>起始时间：
        </label>
        <div class="col-sm-2">
            <input class="form-control Wdate" type="text" name="sale_from_date" value="<?php echo $id ? $templateInfo['discountFromDate'] : '';?>" datatype="date"/>
        </div>

        <label class="col-sm-1 control-label">
            <span class="red">*</span>截止时间：
        </label>
        <div class="col-sm-2">
            <input class="form-control Wdate" type="text" name="sale_end_date" value="<?php echo $id ? $templateInfo['discountEndDate'] : '';?>" datatype="date"/>
        </div>
    </div>

    <!--图片-->
    <?php
    $imgList = array();
    if (!empty($templateInfo['imageUrl'])){
        $imgList = explode(';', $templateInfo['imageUrl']);
    }
    ?>
    <div class="form-group pic-field">
        <label class="control-label col-sm-2"><span class="red">*</span>图片列表：</label>
        <div class="col-sm-8">
            <div>
                <a href="javascript:void(0);" class="btn btn-default btn-sm from_local" lang="detail" onclick="importPic('<?php echo admin_base_url("amz/template/ajaxGetPicUrls");?>');">导入图片</a>
                <a class="btn btn-xs btn-primary pic-del-all" title="全部删除" href="javascript:" onclick="delSiblingsPics(this);"><i class="icon-trash"></i></a>
                <b class="ajax-loading hide">图片获取中...</b>
            </div>
            <ul class="list-inline pic-list">
                <?php
                echo $parentPicStr;
                ?>
            </ul>
            <!--在外边使用隐藏域存放图片链接,用;分割-->
            <input type="hidden" name="picLists[]" value="<?php echo $parentPic;?>"/>
        </div>
    </div>

    <!--输出子SKU图片信息-->
    <?php echo $childPicHtml;?>

    <!--品牌-->
    <div class="form-group">
        <label class="control-label col-sm-2">品牌：</label>
        <div class="col-sm-2">
            <input class="form-control" type="text" name="brand_name" value="<?php echo $id ? $templateInfo['brand'] : '';?>"/>
        </div>
    </div>

    <!--厂商-->
    <div class="form-group">
        <label class="control-label col-sm-2"><span class="red">*</span>厂商：</label>
        <div class="col-sm-2">
            <input class="form-control" type="text" name="manufacturer" datatype="*" nullmsg="厂商不能为空" errormsg="厂商不能为空" value="<?php echo $id ? $templateInfo['manufacturer'] : '';?>"/>
        </div>
    </div>

    <!--关键词-->
    <?php
    $key1 = $key2 = $key3 = $key4 = $key5 = '';
    if (!empty($templateInfo['keyword'])){
        list($key1, $key2, $key3, $key4, $key5) = explode(';', $templateInfo['keyword']);
    }
    ?>
    <div class="form-group p-keyword">
        <label class="control-label col-sm-2"><span class="red">*</span>关键词：</label>
        <div class="col-sm-6">
            <input class="form-control" type="text" name="keyword[]" placeholder="(不能超过50个字符)" datatype="*1-50" nullmsg="关键词不能为空" errormsg="关键词在50个字符内" value="<?php echo $key1;?>"/>
        </div>
        <!--<label class="control-label">
            <a href="javascript:" title="添加多关键词" onclick="addRow(this, 'keyword', 2);"><i class="icon-plus green bigger-150"></i></a>
        </label>-->
    </div>
    <div class="form-group p-keyword">
        <div class="col-sm-6 col-sm-offset-2">
            <input class="form-control" type="text" name="keyword[]" placeholder="(选填)" datatype="*1-50" nullmsg="关键词不能为空" errormsg="关键词在50个字符内" ignore="ignore" value="<?php echo $key2;?>"/>
        </div>
    </div>
    <div class="form-group p-keyword">
        <div class="col-sm-6 col-sm-offset-2">
            <input class="form-control" type="text" name="keyword[]" placeholder="(选填)" datatype="*1-50" nullmsg="关键词不能为空" errormsg="关键词在50个字符内" ignore="ignore" value="<?php echo $key3;?>"/>
        </div>
    </div>
    <div class="form-group p-keyword">
        <div class="col-sm-6 col-sm-offset-2">
            <input class="form-control" type="text" name="keyword[]" placeholder="(选填)" datatype="*1-50" nullmsg="关键词不能为空" errormsg="关键词在50个字符内" ignore="ignore" value="<?php echo $key4;?>"/>
        </div>
    </div>
    <div class="form-group p-keyword">
        <div class="col-sm-6 col-sm-offset-2">
            <input class="form-control" type="text" name="keyword[]" placeholder="(选填)" datatype="*1-50" nullmsg="关键词不能为空" errormsg="关键词在50个字符内" ignore="ignore" value="<?php echo $key5;?>"/>
        </div>
    </div>

    <!--卖点-->
    <?php
    $bullet1 = $bullet2 = $bullet3 = $bullet4 = $bullet5 = '';
    if (!empty($templateInfo['bullet'])){
        list($bullet1, $bullet2, $bullet3, $bullet4, $bullet5) = explode('-||-', $templateInfo['bullet']);
    }
    ?>
    <div class="form-group p-bullet">
        <label class="control-label col-sm-2"><span class="red">*</span>卖点：</label>
        <div class="col-sm-8">
            <textarea name="bullet[]" placeholder="(不能超过250个字符)" class="form-control" datatype="*1-250" nullmsg="卖点不能为空" errormsg="卖点在250个字符内"><?php echo $bullet1;?></textarea>
            <!--<input class="form-control" type="text" name="bullet[]" datatype="*1-250" nullmsg="卖点不能为空" errormsg="卖点不能为空"/>-->
        </div>

        <!--<label class="control-label">
            <a href="javascript:" title="添加更多卖点" onclick="addRow(this, 'maidian', 2);"><i class="icon-plus green bigger-150"></i></a>
        </label>-->
    </div>

    <div class="form-group p-bullet">
        <div class="col-sm-8 col-sm-offset-2">
            <textarea name="bullet[]" placeholder="(选填)" class="form-control" datatype="*1-250" nullmsg="卖点不能为空" errormsg="卖点在250个字符内" ignore="ignore"><?php echo $bullet2;?></textarea>
        </div>
    </div>
    <div class="form-group p-bullet">
        <div class="col-sm-8 col-sm-offset-2">
            <textarea name="bullet[]" placeholder="(选填)" class="form-control" datatype="*1-250" nullmsg="卖点不能为空" errormsg="卖点在250个字符内" ignore="ignore"><?php echo $bullet3;?></textarea>
        </div>
    </div>
    <div class="form-group p-bullet">
        <div class="col-sm-8 col-sm-offset-2">
            <textarea name="bullet[]" placeholder="(选填)" class="form-control" datatype="*1-250" nullmsg="卖点不能为空" errormsg="卖点在250个字符内" ignore="ignore"><?php echo $bullet4;?></textarea>
        </div>
    </div>
    <div class="form-group p-bullet">
        <div class="col-sm-8 col-sm-offset-2">
            <textarea name="bullet[]" placeholder="(选填)" class="form-control" datatype="*1-250" nullmsg="卖点不能为空" errormsg="卖点在250个字符内" ignore="ignore"><?php echo $bullet5;?></textarea>
        </div>
    </div>

    <!--描述-->
    <div class="form-group">
        <label class="control-label col-sm-2"><span class="red">*</span>描述：</label>
        <div class="col-sm-8">
            <textarea name="product_description" class="form-control" rows="3" datatype="*" nullmsg="描述不能为空" errormsg="描述错误"><?php echo $id ? $templateInfo['description'] : '';?></textarea>
        </div>
    </div>

    <!--发货周期-->
    <div class="form-group">
        <label class="col-sm-2 control-label">
            发货周期：
        </label>
        <div class="col-sm-2 input-group">
            <input type="text" class="form-control" name="deliveryTime" datatype="n,numrange" nullmsg="发货周期不能为空" errormsg="发货周期格式错误" value="<?php echo $id ? $templateInfo['deliveryTime'] : 3;?>"/>
            <div class="input-group-addon">
                天
            </div>
        </div>
    </div>

    <!--体积、重量及单位-->
    <div class="form-group">
        <label class="control-label col-sm-2"><span class="red">*</span>体积：</label>
        <div class="col-sm-3 input-group">
            <input class="form-control" type="text" name="item_display_length" placeholder="(长)" datatype="n,numrange" nullmsg="长度不能为空" errormsg="长度错误" id="length" value="<?php echo $id ? $templateInfo['length'] : '';?>"/>
            <div class="input-group-addon">*</div>
            <input class="form-control" type="text" name="item_display_width" placeholder="(宽)" datatype="n,numrange" nullmsg="宽度不能为空" errormsg="宽度错误" id="width" value="<?php echo $id ? $templateInfo['width'] : '';?>"/>
            <div class="input-group-addon">*</div>
            <input class="form-control" type="text" name="item_display_height" placeholder="(高)" datatype="n,numrange" nullmsg="高度不能为空" errormsg="高度错误" id="height" value="<?php echo $id ? $templateInfo['height'] : '';?>"/>
            <div class="input-group-addon">cm</div>
        </div>
        <label class="control-label col-sm-2 col-sm-offset-1"><span class="red">*</span>重量：</label>
        <div class="col-sm-2 input-group">
            <input class="form-control" type="text" name="item_weight" datatype="num,numrange" nullmsg="重量不能为空" errormsg="重量错误" id="weight" value="<?php echo $id ? $templateInfo['weight'] : '';?>"/>
            <div class="input-group-addon">KG</div>
        </div>
    </div>

    <!--材料-->
    <div class="form-group">
        <label class="control-label col-sm-2">材料：</label>
        <div class="col-sm-8">
            <select class="form-control" name="material_type" id="">
                <option value="" <?php echo !empty($templateInfo['material']) && $templateInfo['material'] == '' ? 'selected="selected"' : '';?>>材料列表</option>
            </select>
        </div>
    </div>

</div>
<?php
echo ace_srbtn('amz/template');

echo ace_form_close();
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>
<!--<script type="text/javascript" src="--><?php //echo static_url('theme/common/jquery.dragsort-0.5.1.min.js');?><!--"></script>-->
<script type="text/javascript" src="<?php echo static_url('theme/common/jquery.sortable.js');?>"></script>
<script type="text/javascript">
    var attrList = '<?php echo json_encode($attributeList);?>';
    attrList = eval('('+attrList+')');
    var attCount = parseInt('<?php echo $attrCount;?>'); //属性的个数,可以用它来统计索引
    //var attrOptionsStr = ''; //属性选项
    var nowAttrOptionsStr = '<?php echo $nowAttrOptionsStr;?>'; //当前的属性选项

    //同步替换属性
    function selectAttrList(obj){
        getAttrStr(obj);

        $('select.attr-options').empty().append(nowAttrOptionsStr);
    }

    //获取属性生成option选项列表
    function getAttrStr(obj){
        //当前选择的值
        var nowIndex = $(obj).val();

        nowAttrOptionsStr = '<option>=选择=</option>';
        $.each(attrList, function(index, el){
            if (nowIndex == index){
                $.each(el, function(i, e){
                    nowAttrOptionsStr += '<option value="'+e+'">'+e+'</option>';
                });
                return false;
            }
        });
    }

    /**
     * 添加多行
     * @param obj
     */
    function addRow(obj, opt, w, val){
        var pTarget = 'p-'+opt;
        var name = '';
        if (opt == 'sku'){
            name = 'item_sku';
        }else if(opt == 'title'){
            name = 'item_name';
        }else {
            name = opt;
        }
        val = val == undefined ? '' : val;
        w = w > 0 ? w : 8;
        var titleStr = '<div class="form-group '+pTarget+'"><div class="col-sm-'+w+' col-sm-offset-2"><input class="form-control" type="text" value="'+val+'" name="'+name+'[]"/></div><label class="control-label"><a href="javascript:" title="删除此标题" onclick="delTitle(this);"><i class="icon-remove red bigger-150"></i></a></label></div>';
        var parentobj = $(obj).closest('.form-group.'+pTarget);
        var plObj = parentobj.parent('div');
        plObj.find('.form-group.'+pTarget+':last').after(titleStr);

        if (opt == 'sku'){
            $('.p-'+opt+'-theme').removeClass('hide');
        }
    }

    /**
     * 删除第一个外的所有元素
     * @param obj
     * @param opt
     */
    function deleteOtherRows(obj, opt){
        layer.confirm('确定要删除其他项吗？', function(index){
            var parentobj = $(obj).closest('.form-group.p-'+opt);
            var plObj = parentobj.parent('div');
            plObj.find('.form-group.p-'+opt+':not(:first)').remove();

            layer.close(index);
        })
    }

    /**
     * 删除当前行
     * @param obj
     */
    function delTitle(obj){
        var parentDiv = $(obj).closest('div.form-group');

        if (parentDiv.hasClass('p-sku')){
            if (parentDiv.closest('div').find('.form-group.p-sku').length <= 2){
                $('.p-sku-theme').addClass('hide');
            }
        }
        parentDiv.remove();
    }

    /**
     * 删除同一个div.form-group下的所有li标签，应该已经包括图片在内了
     */
    function delSiblingsPics(obj){
        layer.confirm('确定要全部删除吗？', function(index){
            var ulObj = $(obj).closest('div.form-group').find('ul');
            ulObj.find('li').remove();
            refreshVal(ulObj);
            layer.close(index);
        });
    }

    //删除一张图片
    function delOnePic(obj){
        var ulObj = $(obj).closest('ul.pic-list');
        $(obj).closest('li').remove();
        refreshVal(ulObj);
    }

    /**
     * 导入图片链接信息
     */
    function importPic(url){
        //先获取sku信息
        var skus = [];
        $('.p-sku input[name="item_sku[]"]').each(function(){
            var oneVal = $(this).val().trim();
            if (oneVal != ''){
                skus.push(oneVal);
            }
        });

        if (skus.length == 0){
            layer.msg('请先输入SKU信息');
            return false;
        }

        var skuStr = skus.join(',');

        //加载前先清理下之前加载的图片
        //$('ul.pic-list').empty();


        //开始异步加载图片信息
        $.ajax({
            url: url,
            data: 'skuStr='+skuStr,
            type: 'POST',
            dataType: 'JSON',
            success: function(data){
                if (data.status){ //加载成功了
                    var htmlStr = '';
                    $.each(data.data, function (index, el) {
                        htmlStr += '<li><img src="' + el + '" width="100" height="100" style="border: 0px;"><a href="javascript: void(0);"  onclick="delOnePic(this);">删除</a></li>';
                    });
                    $('ul.pic-list').each(function(){
                        $(this).append(htmlStr);
                        $(this).closest('div').find('input[name="picLists[]"]').val(data.data.join(';'));
                    });
                    //refresh一下，这个必须啊
                    sortRefresh($('ul.pic-list'));
                }else {
                    layer.msg(data.info, 2, !1);
                }
            },
            beforeSend: function(){
                $('.ajax-loading').removeClass('hide');
            },
            complete: function(){
                $('.ajax-loading').addClass('hide');
            }
        });
    }

    /**
     * 异步联想获取到子SKU列表
     * @param obj
     * @param url
     */
    //function addChildSkuRow(obj, url){
    //    //父SKU
    //    var parentSku = $('#parentSku').val().trim();
    //
    //    if (parentSku == ''){
    //        layer.msg('请先输入父SKU', 2, !1);
    //        $('#thead').focus();
    //        return false;
    //    }
    //
    //    //先清空之前加载的子SKU列表
    //    var parentobj = $(obj).closest('.form-group.p-sku');
    //    var plObj = parentobj.parent('div');
    //    plObj.find('.form-group.p-sku:not(:first)').remove();
    //
    //    //清空体积重量等信息
    //    $('#length, #width, #height, #weight').val('');
    //
    //    //开始异步加载
    //    $.ajax({
    //        url: url,
    //        data: 'parentSku='+parentSku,
    //        type: 'GET',
    //        dataType: 'JSON',
    //        success: function(data){
    //            var length = '', width = '', height = '', weight='';
    //            if (data.status){
    //                //开始添加新的SKU列表
    //                $.each(data.data, function (index, el) {
    //                    if (el.products_sku.toUpperCase() != parentSku.toUpperCase()) {
    //                        //只要不是父SKU就添加下来
    //                        addRow(obj, 'sku', 2, el.products_sku);
    //                    }
    //                    length = (length != '' && parseFloat(length) > 0) ? length : el.length;
    //                    width = (width != '' && parseFloat(width) > 0) ? width : el.width;
    //                    height = (height != '' && parseFloat(height) > 0) ? height : el.height;
    //                    weight = (weight != '' && parseFloat(weight) > 0) ? weight : el.weight;
    //                });
    //
    //                $('#length').val(length);
    //                $('#width').val(width);
    //                $('#height').val(height);
    //                $('#weight').val(weight);
    //            }else {
    //                layer.msg('没有找到相应的子SKU', 1, !1);
    //            }
    //        }
    //    });
    //}

    //添加子SKU属性列
    function addAttribute(obj){
        var parentTb = $(obj).closest('table');
        var thStr = '<select name="attr['+attCount+']" style="width: 90px;" class="attr-options">' +
            nowAttrOptionsStr +
            '</select>' +
            '&nbsp;<a href="javascript:" title="删除属性列" onclick="delAttCol(this);"><i class="icon-remove red"></i></a>';
        var tdStr = '<input type="text" name="attrVal['+attCount+'][]" size="12" />';
        parentTb.find('thead tr th:last').before('<th>'+thStr+'</th>');
        var trs = parentTb.find('tbody tr');
        var tbCount = trs.length;
        for (var i=0; i<tbCount; i++){
            $(trs[i]).find('td:last').before('<td>'+tdStr+'</td>');
        }

        attCount++;
    }

    //删除属性列
    function delAttCol(obj){
        //点击的元素所在单元格的索引
        var parentTh = $(obj).closest('th');
        var index = parentTh.index();
        //删除表格的该列
        $(obj).closest('table').find("tbody tr").each(function(){
            $(this).find("td:eq("+index+")").remove();
        });
        //删除该单元格
        parentTh.remove();
    }

    //删除表格行
    function delTbRow(obj){
        //先获取表格
        var parentTb = $(obj).closest('table');
        //获取本行所在位置的索引
        var trIndex = $(obj).closest('tr').index('tbody tr');

        //删除本行
        $(obj).closest('tr').remove();
        //判断表格body是否还含有行
        var trCount = parentTb.find('tbody tr').length;

        //删除相应SKU的图片列
        delPicField(trIndex);

        if (trCount <= 0){
            //删除添加的属性
            parentTb.find('thead tr th:not(.th-fixed)').remove();
            parentTb.closest('div.form-group').addClass('hide');
            attCount = 0;

            $('.standard_price').removeClass('hide');
            $('input[name=sale_price]').closest('div').removeClass('hide');
        }
    }

    //添加表格行
    function addChildSkuTbRow(obj, ajax, url){
        var discountFlag  = $('#discount:checked').length == 1 ? true : false; //是否折扣标识
        var childSkuField = $('.childSku-field');
        var table = childSkuField.find('table'); //对应的表格

        //获取标题行，要是有属性，就需要把属性的列也添加进去
        var ths = table.find('thead tr th:not(.th-fixed)');
        var tdAttStr = ''; //SKU行的属性信息
        ths.each(function(){
            //当前下拉框的name值
            var name = $(this).find('select').attr('name');
            var temp = name.split('[');
            tdAttStr += '<td><input type="text" name="attrVal['+temp[1]+'[]" size="12" /></td>';
        })

        if (ajax){ //存在就异步获取，不然就添加个空行
            var parentSku = $('#parentSku').val().trim();

            if (parentSku == ''){
                layer.msg('请先输入父SKU', 2, !1);
                $('#parentSku').focus();
                return false;
            }

            //去除隐藏样式
            childSkuField.hasClass('hide') ? childSkuField.removeClass('hide') : '';
            $('.standard_price').addClass('hide');

            //异步获取的话，先清空已经存在的子SKU行,列的话暂时还是不管呢
            $.ajax({
                url: url,
                data: 'parentSku='+parentSku,
                type: 'POST',
                dataType: 'JSON',
                success: function(data){
                    var length = '', width = '', height = '', weight='';
                    if (data.status){
                        //先清空图片区域
                        $('.pic-field:not(:first)').remove();

                        //开始添加新的SKU列表
                        $.each(data.data, function (index, el) {
                            if (el.products_sku.toUpperCase() != parentSku.toUpperCase()) {
                                //只要不是父SKU就添加下来
                                var trStr = '<tr>' +
                                    '<td><input type="text" name="item_sku[]" onblur="modPicField(this);" value="'+el.products_sku+'"/></td>' +
                                    '<td><input type="text" name="item_price[]" size="6" datatype="num,numrange" nullmsg="售价不能为空" errormsg="售价格式错误" min="0.01" ignore="ignore"/> </td>' +
                                    (discountFlag ? '<td><input type="text" name="discount_price[]" size="6" datatype="num,numrange" nullmsg="折扣价不能为空" errormsg="折扣价格式错误" min="0.01" ignore="ignore"/> </td>' : '') +
                                    tdAttStr +
                                    '<td class="middle">' +
                                    '<label><a href="javascript:" title="删除" onclick="delTbRow(this);"><i class="icon-remove  bigger-150 red"></i></a></label>' +
                                    '</td>' +
                                    '</tr>';
                                table.find('tbody').append(trStr);

                                //显示子SKU图片区域
                                addPicField(el.products_sku);
                            }
                            length = (length != '' && parseFloat(length) > 0) ? length : el.length;
                            width = (width != '' && parseFloat(width) > 0) ? width : el.width;
                            height = (height != '' && parseFloat(height) > 0) ? height : el.height;
                            weight = (weight != '' && parseFloat(weight) > 0) ? weight : el.weight;
                        });

                        $('#length').val(length);
                        $('#width').val(width);
                        $('#height').val(height);
                        $('#weight').val(weight);

                        $('input[name=sale_price]').closest('div').addClass('hide');
                    }else {
                        layer.msg('没有找到相应的子SKU', 1, !1);
                    }
                },
                beforeSend: function(){
                    $('.p-sku-help').removeClass('hide');
                    table.find('tbody tr').remove(); //清空已经存在的行
                },
                complete: function(){
                    $('.p-sku-help').addClass('hide');
                }
            });
        }else {
            //去除隐藏样式
            childSkuField.hasClass('hide') ? childSkuField.removeClass('hide') : '';

            $('.standard_price').addClass('hide');

            var trStr = '<tr>' +
                '<td><input type="text" name="item_sku[]" onblur="modPicField(this);"/></td>' +
                '<td><input type="text" name="item_price[]" size="6" datatype="num,numrange" nullmsg="售价不能为空" errormsg="售价格式错误" min="0.01" ignore="ignore" /> </td>' +
                (discountFlag ? '<td><input type="text" name="discount_price[]" size="6" datatype="num,numrange" nullmsg="折扣价不能为空" errormsg="折扣价格式错误" min="0.01" ignore="ignore"/> </td>' : '') +
                tdAttStr +
                '<td class="middle">' +
                '<label><a href="javascript:" title="删除" onclick="delTbRow(this);"><i class="icon-remove  bigger-150 red"></i></a></label>' +
                '</td>' +
                '</tr>';
            table.find('tbody').append(trStr);
            addPicField('');
            $('input[name=sale_price]').closest('div').addClass('hide');
        }
    }

    //添加图片区域
    function addPicField(sku){
        var divStr = '<div class="form-group pic-field"><label class="control-label col-sm-2"><span class="red">*</span>子SKU(<span class="childSKu">'+sku+'</span>)图片列表：</label> ' +
            '<div class="col-sm-8">' +
            '<div>' +
            '<a href="javascript:void(0);" class="btn btn-default btn-sm from_local" lang="detail" onclick="copyMainPic(this);">复制主图片</a>' +
            '<a href="javascript:" class="btn btn-default btn-sm" onclick="addPicLink(this);">添加外链</a> ' +
            '<a class="btn btn-xs btn-primary pic-del-all" title="全部删除" href="javascript:" onclick="delSiblingsPics(this);"><i class="icon-trash"></i></a>' +
            '</div>' +
            '<ul class="list-inline pic-list pq-list" data-listidx="0">' +
            '</ul>' +
            '<input type="hidden" name="picLists[]" value="" />' +
            '</div>' +
            '</div>';
        $('.pic-field:last').after(divStr);
    }

    //修改图片区域的SKU的值
    function modPicField(obj){
        //当前行所在位置的索引
        var trIndex = $(obj).closest('tr').index('tbody tr');
        var nowSku = $(obj).val().trim();
        $('.pic-field:eq('+(trIndex+1)+')').find('.childSKu').text(nowSku);
    }

    /**
     * 删除SKU的图片信息
     * trIndex 要删除的位置的索引
     */
    function delPicField(trIndex){
        //直接找索引删除
        $('.pic-field:eq('+(trIndex + 1)+')').remove();
    }

    //删除所有子SKU
    function delAllChildSku(){
        layer.confirm('确定要删除其他项吗？', function(index){
            var childSkus = $('.childSku-field').find('input[name="item_sku[]"]');
            if (childSkus.length > 0){
                childSkus.each(function(){
                    delTbRow(this);
                });
            }

            layer.close(index);
        })
    }

    //复制主图图片
    function copyMainPic(obj){
        //先获取主图图片列表
        var imgs = $('.pic-list:eq(0)').closest('div').find('input[name="picLists[]"]').val().trim();
        if (imgs == ''){
            layer.msg('主图图片不存在', 1, !1);
            return false;
        }

        var imgList = imgs.split(';');
        var liStr = '';
        for (var i=0; i<imgList.length; i++){
            liStr += '<li><img src="'+imgList[i]+'" width="100" height="100" style="border: 0px;"><a href="javascript: void(0);" onclick="delOnePic(this);">删除</a></li>';
        }
        var ulObj = $(obj).closest('div.form-group').find('.pic-list');
        ulObj.append(liStr);
        sortRefresh(ulObj);
        refreshVal(ulObj);
    }

    //添加外链图片
    function addPicLink(obj){
        //目标图片列表框
        var ulObj = $(obj).closest('.form-group').find('ul.pic-list');

        //目标图片列表
        var picListObj = $(obj).closest('.form-group').find('input[name="picLists[]"]');

        //显示html输入框信息
        var htmlStr = '<div style="width: 580px; height: 230px; overflow-y: auto; padding: 5px;">' +
            '<table style="width: 95%;" cellpadding="2">' +
            '<tr><td width="84%"><input type="text" name="picUrl[]" value="" size="60" placeholder="请输入http://开头的图片链接"/></td><td><a href="javascript:" title="添加新行" onclick="addPicUrlRow(this);" class="btn btn-sm btn-primary" onclick="delPicUrlRow(this);">添加</a></td></tr>' +
            '</table></div>';
        $.layer({
            type   : 1,
            shade  : [0.8 , '' , true],
            offset: ['50px', ''],
            title  : ['请输入图片外链',true],
            area   : ['580px' , '310px'],
            btns : 2,
            btn : ['确定', '取消'],
            yes : function(index){ //确定按钮的操作
                //验证下图片
                var picReg = /^http:\/\/\w+(\.\w+)+.*\.((jpg)|(jpeg)|(gif)|(bmp)|(png))(\?.*)?$/i;//图片验证规则
                var flag = true; //通过验证标识
                $('input[name="picUrl[]"]').each(function(){
                    var picUrl = $(this).val().trim();
                    if (picUrl != '' && !picReg.test(picUrl)){ //图片链接没录入就算了
                        layer.msg('请输入正确的图片外链', 1, !1);
                        $(this).select();
                        flag = false;
                        return false;
                    }
                });
                if (!flag) return false;

                //把图片添加进列表中
                var newPicList = $('input[name="picUrl[]"]').map(function(){
                    var pic = $(this).val().trim();
                    if (pic != ''){
                        ulObj.append('<li><img src="'+pic+'" width="100" height="100" style="border: 0px;"><a href="javascript: void(0);" onclick="delOnePic(this);">删除</a></li>');
                        return pic;
                    }
                }).get().join(';');

                if (newPicList != ''){
                    var oldPicList = picListObj.val().trim();
                    if (oldPicList == ''){
                        picListObj.val(newPicList);
                    }else {
                        picListObj.val(oldPicList+';'+newPicList);
                    }

                    //重新排序
                    sortRefresh(ulObj);
                }

                layer.close(index);
            },
            no: function(index){
                layer.close(index);
            },
            page: {
                html: htmlStr
            }
        });
    }

    //添加图片列
    function addPicUrlRow(obj){
        var trStr = '<tr><td><input type="text" name="picUrl[]" size="60" /></td><td><a href="javascript:" title="删除" onclick="delPicUrlRow(this);"><i class="icon-remove red bigger-150"></i></a></td></tr>';
        $(obj).closest('table').append(trStr);
    }

    //排序刷新 --图片排序必须 obj为ul对象
    function sortRefresh(obj){
        obj.sortable('refresh').bind('sortupdate', function() {
            var imgStr = $(this).find('img').map(function(){
                return $(this).attr('src').trim();
            }).get().join(';');
            $(this).closest('div').find('input[name="picLists[]"]').val(imgStr);
        });
    }

    //增删后变更下这个图片列表的值 obj为ul对象
    function refreshVal(obj){
        var imgStr = obj.find('li img').map(function(){
            return $(this).attr('src').trim();
        }).get().join(';');
        obj.closest('div').find('input[name="picLists[]"]').val(imgStr);
    }

    //删除外链图片的行
    function delPicUrlRow(obj){
        $(obj).closest('tr').remove();
    }

    //批量设置售价
    function batchSetPrice(obj, whole){
        var pattern = /^(\d+[\s,]*)+\.?\d*$/;
        var name = whole == 'discount' ? 'discount_price' : 'item_price';
        var target = whole == 'discount' ? 'batchWholePrice' : 'batchPrice';
        var price = $('input[name=' + target + ']').val().trim();

        if (pattern.test(price) && parseFloat(price) >= 0.01){
            $(obj).closest('table').find('input[name="'+name+'[]"]').each(function(){
                $(this).val(price);
            });
        }
    }

    $(function(){
        $(document).on('click','.Wdate',function(){
            var o = $(this);
            if(o.attr('dateFmt') != '')
                WdatePicker({dateFmt:o.attr('dateFmt')});
            else if(o.hasClass('month'))
                WdatePicker({dateFmt:'yyyy-MM'});
            else if(o.hasClass('year'))
                WdatePicker({dateFmt:'yyyy'});
            else
                WdatePicker({dateFmt:'yyyy-MM-dd'});
        });

        //折扣方面的显示处理
        $('#discount').on('click', function(){
            var showFlag = $('.childSku-field table').find('thead tr th.th-discount').length == 0 ? false : true; //折扣列是否已显示

            var childSkuHideFlag = $('.childSku-field').hasClass('hide'); //子SKU是否显示,false才是显示

            if (this.checked) { //已经选中了
                $('.p-discount').removeClass('hide');

                //子SKU那把折扣列显示出来
                if (!showFlag) { //没显示，把各列的都显示出来吧
                    $('.childSku-field table').find('thead tr th:eq(1)').after('<th class="th-fixed th-discount">折扣价</th>');

                    $('.childSku-field table').find('tbody tr').each(function(){
                        $(this).find('td:eq(1)').after('<td><input type="text" name="discount_price[]" size="6" /></td>');
                    });
                }
            }else {
                $('.p-discount').addClass('hide');

                //子SKU那把折扣列都去掉
                if (showFlag){
                    $('.childSku-field table').find('thead tr th.th-discount').remove();
                    $('.childSku-field table').find('tbody tr').each(function(){
                        $(this).find('td:eq(2)').remove();
                    });
                }
            }

            if (!childSkuHideFlag){ //显示
                $('.p-discount').find('input[name="sale_price"]').closest('.input-group').addClass('hide');
            }else {
                $('.p-discount').find('input[name="sale_price"]').closest('.input-group').removeClass('hide');
            }
        });

        /**
         * 验证
         */
        $('.dataForm').Validform({
            datatype:{
                "date":function(gets,obj,curform,regxp){
                    /*参数gets是获取到的表单元素值，
                     obj为当前表单元素，
                     curform为当前验证的表单，
                     regxp为内置的一些正则表达式的引用。*/
                    var reg=/^[2][\d]{3}-[\d]{2}-[\d]{2}$/;
                    if(reg.test(gets)){return true;}
                    return false;
                }
            },
            ignoreHidden: true,
            ajaxPost: false,
            callback: function (form) { //返回数据
                $.ajax({
                    url: form.attr('action'),
                    data: form.serialize(),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(data){
                //
                //        if (data.status){ //修改成功了
                //            form.find('input[name=id]').val(data.data.id);
                //
                //            layer.alert(data.info, 1, '标题', function(index){
                //                layer.close(index);
                //                window.location.reload();
                //            });
                //        }else {
                //            layer.msg('修改失败', 2, !1);
                //        }
                    }
                });
                return false;
            }
        });

        //图片拖拽排序
        $('.pic-list').sortable();
    })
</script>