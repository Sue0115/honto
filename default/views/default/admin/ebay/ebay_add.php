<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-08
 * Time: 10:30
 */

?>

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
    .pic-main li div, .pic-detail li div, .relate-list li div {
        width: 102px;
        height: 125px;
        border: 1px solid #fff;
    }

    .pic-main .placeHolder div, .pic-detail .placeHolder div, .relate-list .placeHolder div {
        width: 102px;
        height: 125px;
        background-color: white !important;
        border: dashed 1px gray !important;
    }

    .my-list-cust li {
        padding: 5px;
        float: left;
        position: relative;
    }

    .my-list-cust li img {
        cursor: pointer;
    }

    .my-list-cust .my-check-cust {
        position: absolute;
        z-index: 999;
        left: 5px;
        top: 1px;
    }
</style>







<form action="<?php echo admin_base_url('ebay/ebay_product/doAction'); ?>" class="form-horizontal validate_form"
      method="post">



    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;已选信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label class="control-label col-sm-2">所选站点：</label>

                    <div class="col-sm-1">
                        <input type="text" class="form-control text-center"  readonly="readonly" size="4"
                               value="<?php echo $site_name ?>">
                    </div>
                    <div class="col-sm-4 hidden">
                        <input type="text" class="form-control " id="site" name="site" value="<?php echo $site ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2">第一分类：</label>

                    <div class="col-sm-1">
                        <input type="text" class="form-control text-center" readonly="readonly" size="4"
                               name="category_id"
                               value="<?php echo $category_id ?>">
                    </div>
                    <div class="col-sm-4 ">
                        <input type="text" class="form-control  text-left " readonly="readonly"
                               value="<?php echo $category_name ?>">
                    </div>
                </div>

                <?php if (isset($category_id_second) && !empty($category_id_second)): ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2">第二分类：</label>

                        <div class="col-sm-1">
                            <input type="text" class="form-control text-center" readonly="readonly" size="4"
                                   name="category_id_second"
                                   value="<?php echo $category_id_second ?>">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control text-left" readonly="readonly"
                                   value="<?php echo $category_name_second ?>">
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="promsg" style="display: none;">
                已经选择的基本信息
            </div>
        </div>
    </div>


    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;ebay账号</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">


                <div class="form-group">
                    <label class="control-label col-sm-2"></label>
                    <div class="col-sm-4">
                    <input id="all_check" type="checkbox" />   <span >全选和取消</span>
                    </div>

                </div>
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    <?php $product_info = isset($product_info) ? $product_info : ''; ?>

                    <!-- 分类选择 -->
                    <div class="form-group">

                        <?php foreach ($token_arr as $key => $token): ?>
                            <div class="col-sm-2">
                                <input type="checkbox" value="<?php echo $token['token_id']; ?>"
                                       name="choose_account[]"  <?php if (filterDataEbay('account_id', $product_info) == $token['token_id']) echo 'checked="checked"' ?>
                                       class="choose_account"/> <?php echo $token['seller_account']; ?>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>


            </div>

            <div class="promsg" style="display: none;">
                设置ebay账号
            </div>
        </div>
    </div>


    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;刊登类型</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    <div class="form-group">


                        <div class="col-sm-2">
                            <input type="radio" value="1" id="choose_type"
                                   name="choose_type" <?php if (filterDataEbay('ad_type', $product_info) == 1) echo 'checked' ?>
                                   class="choose_type"/> <?php echo '拍卖'; ?>
                        </div>

                        <div class="col-sm-2">
                            <input type="radio" value="2" id="choose_type"
                                   name="choose_type" <?php if (filterDataEbay('ad_type', $product_info) == 2) echo 'checked' ?>
                                   class="choose_type"/> <?php echo '固定'; ?>
                        </div>

                        <div class="col-sm-2">
                            <input type="radio" value="3"
                                   id="choose_type" <?php if (filterDataEbay('ad_type', $product_info) == 3) echo 'checked' ?>
                                   name="choose_type"  <?php if (!$support_multi) {
                                echo 'disabled';
                            } ?>
                                   class="choose_type"/> <?php echo '多属性'; ?>

                            <?php if (!$support_multi): ?>
                                <span class="red">该分类不支持发布多属性</span>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>

            <div class="promsg" style="display: none;">
                设置刊登类型
            </div>
        </div>
    </div>

    <!--设置标题-->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;广告标题</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div id="mul_account_tittle">



                    <div class="account_tittle">
                        <?php if (filterDataEbay('ad_type', $product_info)): ?>
                        <div id="account_tittle_<?php filterDataEbay('account_id', $product_info) ?>">
                            <div class="form-group">
                                <label class="control-label col-sm-2"> <?php $account_id = filterDataEbay('account_id', $product_info); if(isset($account_array[$account_id])){ echo $account_array[$account_id]; } ?> 标题：</label>

                                <div class="col-sm-9">
                                    <input type="text"  maxlength="80"  onkeyDown="gettip(this)" class="col-sm-9"  name="account_tittle[<?php  echo filterDataEbay('account_id', $product_info) ?>][tittle]"  value="<?php echo filterDataEbay('title', $product_info) ?>" />
                                    <a class="black" title="填写子标题" href="javascript:" onclick="addSubTittle(this)"><i
                                            class=" glyphicon glyphicon-pencil bigger-200"></i></a>
                                </div>
                            </div>
                            <div class="form-group hidden">
                                <label class="control-label col-sm-2">子标题：</label>

                                <div class="col-sm-4">
                                    <input  maxlength="80" type="text" class="form-control"
                                           name="account_tittle[<?php echo filterDataEbay('account_id', $product_info) ?>][tittle_sub]"  value="<?php  echo filterDataEbay('subTitle', $product_info) ?>" />
                                </div>
                            </div>
                            <hr/>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
                <div class="promsg" style="display: none;">
                    设置广告标题
                </div>
            </div>
        </div>
    </div>

    <!--设置物品所在地-->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;刊登天数</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">

                <div class="form-group">
                    <label class="control-label col-sm-2">刊登天数：</label>

                    <div class="col-sm-4">
                        <?php $publish_array = array('GTC','Days_30','Days_15','Days_10','Days_7','Days_3', 'Days_1'   ); ?>
                        <select name="publish_day">
                            <?php foreach ($publish_array as $publish): ?>
                                <option value="<?php echo $publish; ?>"      <?php if (filterDataEbay('timeMax', $product_info) == $publish) echo 'selected="selected"' ?>      ><?php echo $publish; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="promsg" style="display: none;">
                设置物品的所在地
            </div>
        </div>
    </div>

    <!--设置物品属性-->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;物品属性</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">

                <div class="form-group">
                    <label class="control-label col-sm-2">物品属性 :</label>

                    <div class="col-sm-4">
                        <a class="btn btn-primary btn-sm dir_add" href="javascript: void(0);" onclick="addSpecifics()">添加属性</a>
                    </div>
                </div>

                <div id="all_specifics">

                    <?php  $product_spec =  filterDataEbay('itemSpecifics', $product_info);
                            if(!empty($product_spec)){
                                $product_spec = json_decode($product_spec,true);
                            }else{
                                $product_spec=array();
                            }
                    ?>
                    <?php foreach ($ebay_specifics as $specifics): ?>

                        <div class="form-group  col-sm-6">
                            <label class="control-label col-sm-4">
                                <?php if ($specifics['minvalues'] == 1): ?>
                                    <span class="red">*</span>
                                <?php endif; ?>



                                <?php echo $specifics['name'] ?> :
                            </label>

                            <div class="col-sm-6">
                                <?php $specifics_arr = unserialize($specifics['specificvalue']); ?>
                              <!--  --><?php /*if ($specifics['selectionmode'] == 'SelectionOnly'): */?>




                              <!--  --><?php /*else: */?>
                                    <input type="text" class="col-sm-8"
                                           name="skuSpe[<?php echo $specifics['name'] ?>]"     value="<?php  if(isset($product_spec[$specifics['name']])){echo $product_spec[$specifics['name']] ;  }  ?>"      />
                                    <?php if (!empty($specifics_arr)): ?>
                                        <select class="specifics_choose col-sm-4">
                                            <option value="">==请选择==</option>
                                            <?php foreach ($specifics_arr as $spe): ?>
                                                <option value="<?php echo $spe; ?>"><?php echo $spe ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                   <?php endif; ?>


                            </div>
                        </div>
                    <?php endforeach; ?>





                </div>
                <hr/>
                <br/>

                <div class="form-group"></div>
                <?php if (isset($ebay_condition[0])&&$ebay_condition[0]['upcenabled'] != ''): ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2">UPC ：</label>

                        <div class="col-sm-4">
                            <input type="text" name="needupc"  value="<?php echo filterDataEbay('upc', $product_info); ?>"   />
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($ebay_condition[0])&&$ebay_condition[0]['eanenabled'] != ''): ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2"    >EAN ：</label>

                        <div class="col-sm-4">
                            <input type="text" name="needean"  value="<?php echo filterDataEbay('ean', $product_info); ?>"  />
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($ebay_condition[0])&&$ebay_condition[0]['isbnenabled'] != ''): ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2" name="needisbn"  value="<?php echo filterDataEbay('isbn', $product_info); ?>" >ISBN ：</label>

                        <div class="col-sm-4">
                            <input type="text"/>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="control-label col-sm-2">物品状况 ：</label>

                    <div class="col-sm-4">
                        <?php if(isset($ebay_condition)&&!empty($ebay_condition)){  ?>
                        <select id="ebay_condition" name="ebay_condition">
                            <?php foreach ($ebay_condition as $condition): ?>
                                <option value="<?php echo $condition['condition_id'] ?>"   <?php   if(filterDataEbay('condition', $product_info)==$condition['condition_id']){ echo  'selected="selected"';}         ?>          > <?php echo $condition['displayname'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php  } ?>
                    </div>
                </div>

                <div class="form-group  <?php   if(!filterDataEbay('condition_detail', $product_info)){ echo 'hidden'; } ?> " id="ebay_condition_detail">
                    <label class="control-label col-sm-2">状况描述 ：</label>

                    <div class="col-sm-6">
                        <textarea name="ebay_condition_detail" class="form-control" maxlength="128" ><?php echo filterDataEbay('condition_detail', $product_info); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="promsg" style="display: none;">
                物品属性(设置物品的基本属性)
            </div>
        </div>
    </div>

    <!--SKU设置-->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;SKU信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label class="control-label col-sm-2">sku：</label>

                    <div class="col-sm-10">
                        <input type="text" id="search_sku" class="col-sm-2" name="local_sku" />

                    </div>

                </div>

                <hr/>
                <div class="form-group">
                    <label class="control-label col-sm-2">前缀：</label>

                    <div class="col-sm-10">
                        <div class="col-sm-2">
                            <input type="text" id="prefix_sku">
                        </div>

                        价格： <input type="text" id="price_sku">


                        数量： <input type="text" id="quantity_sku">


                        <a class="btn btn-primary btn-sm " href="javascript: void(0);"
                           onclick="getSkuInfo()">获取SKU信息</a>
                        <a class="btn btn-primary btn-sm " href="javascript: void(0);"
                           onclick ="getSkuPrice()" >查看各账号价格</a>

                    </div>
                </div>


                <hr/>
                <div id="auction">

                    <?php  if(filterDataEbay('ad_type', $product_info)==1): ?>
                    <div class="form-group"><label class="control-label col-sm-2">私人拍卖: </label>
                        <div class="col-sm-4">
                            <input type="checkbox" name="privateListing">不向公众显示买家的名称</div> </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2 red">sku(ebay后台): </label>
                        <div class="col-sm-2">
                            <input type="text" name="ebay_sku" value="<?php  echo filterDataEbay('ebay_sku', $product_info);  ?>" />
                            </div></div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">起拍价格: </label>
                        <div class="col-sm-2">
                            <input type="text" id="ebay_price" name="ebay_price" value="<?php  echo filterDataEbay('ebay_price', $product_info);  ?>"   />
                            </div>
                        <label class="control-label col-sm-2">数量: </label>
                        <div class=" col-sm-2">
                            <input type="text" name="ebay_quantity"  value="<?php  echo filterDataEbay('ebay_quantity', $product_info);  ?>" />
                            </div>
                        </div>
                    <hr>

                    <?php  endif; ?>


                </div>

                <div id="fixed_price">

                    <?php  if(filterDataEbay('ad_type', $product_info)==2): ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2 red">sku(ebay后台): </label>
                        <div class="col-sm-2">
                            <input type="text"  name="ebay_sku"  value="<?php  echo filterDataEbay('ebay_sku', $product_info);  ?>"/>
                            </div></div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">价格: </label>
                        <div class=" col-sm-2">
                            <input type="text"  id="ebay_price" name="ebay_price" value="<?php  echo filterDataEbay('ebay_price', $product_info);  ?>"  />
                            </div>
                        <label class="control-label col-sm-2">数量: </label>
                        <div class=" col-sm-2">
                            <input type="text" name="ebay_quantity" value="<?php  echo filterDataEbay('ebay_quantity', $product_info);  ?>" />
                            </div>
                        </div>

                    <?php  endif; ?>

                </div>


                <div id="mul_price">
                    <?php  if(filterDataEbay('ad_type', $product_info)==3): ?>

                        <div class="form-group">
                            <label class="control-label col-sm-2 red">sku(ebay后台): </label>
                            <div class="col-sm-2"><input type="text"  name="ebay_sku"  value="<?php  echo filterDataEbay('ebay_sku', $product_info);  ?>"/></div></div>
                                             <div class="form-group">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                <table class="table text-center table-bordered" id="table1">
                                    <tr><td>sku</td> <td>price</td> <td>quantity</td> <td></td>

                                    <?php
                                        if(filterDataEbay('zidingyi', $product_info)){
                                            $zidingyi = filterDataEbay('zidingyi', $product_info);
                                            $zidingyi = json_decode($zidingyi,true);
                                            foreach($zidingyi as $zi){
                                                echo '<td onclick="deleteinfoNew(this)">'.$zi.'</td><input class="hidden " type="text" value="'.$zi.'" name="zidingyi[]" size="15">';
                                            }
                                        }
                                    ?>
                                    </tr>
                                    <?php
                                    if(filterDataEbay('skuinfo', $product_info)){
                                        $skuinfo = filterDataEbay('skuinfo', $product_info);
                                        $skuinfo = json_decode($skuinfo,true);
                                        for($j=0;$j<100;$j++){
                                            if(isset($skuinfo['sku'][$j])){
                                                $add_htm  ='';
                                                $add_htm='<tr> <td><input type="text" name="skuinfo[sku][]" value="'.$skuinfo['sku'][$j].'" /></td>
                                                <td><input type="text"  name="skuinfo[price][]" value="'.$skuinfo['price'][$j].'" /></td>
                                                    <td><input type="text"  name="skuinfo[quantity][]" value="'.$skuinfo['quantity'][$j].'" /></td>
                                                    <td><a class="remove btn btn-primary btn-sm" href="javascript: void(0);" onclick="removeRow(this)">移除</a></td>';
                                                $zidingyi = filterDataEbay('zidingyi', $product_info);
                                                $zidingyi = json_decode($zidingyi,true);
                                                if(filterDataEbay('zidingyi', $product_info)){
                                                    $zidingyi = filterDataEbay('zidingyi', $product_info);
                                                    $zidingyi = json_decode($zidingyi,true);
                                                    foreach($zidingyi as $zi){
                                                        $add_htm = $add_htm.'<td><input type="text"  name="skuinfo['.$zi.'][]" value="'.$skuinfo[$zi][$j].'" /></td>';
                                                    }
                                                }
                                                $add_htm = $add_htm.'</tr>';
                                                echo $add_htm;
                                            }
                                        }
                                    }
                                    ?>
                                    </table></div></div><div class="form-group"> <div class="col-sm-2">
                                </div>
                            <div class="col-sm-3">
                                <a class="btn btn-primary btn-sm" href="javascript: void(0);" onclick="addRow()">增加一行</a>
                                </div><div class="col-sm-3">
                                <a class="btn btn-primary btn-sm" href="javascript: void(0);" onclick="addSpe()">增加属性</a></div>
                            <div class="col-sm-3"><a class="btn btn-primary btn-sm" href="javascript: void(0);" onclick="setMul()">设置多属性图片</a></div>
                            </div><hr/>
                    <?php  endif; ?>
                </div>


                <div id="mul_price_pic">

                    <?php  if(filterDataEbay('detailPicListMul', $product_info)): ?>

                        <?php   $detailPicListMul =    filterDataEbay('detailPicListMul', $product_info);
                        $detailPicListMul = json_decode($detailPicListMul,true);
                        foreach($detailPicListMul as $key=>$mul){
                            echo ' <div class="form-group" id="'.$key.'"><label class="col-sm-2 control-label">'.$key.'：</label><div class="col-sm-3"><a class="btn btn-primary btn-sm image_url_mul" onclick="image_url_mul(this)"  href="javascript: void(0);">添加图片</a><div><img src="'.$mul.'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicListMul['.$key.']" value="'.$mul.'" /><a href="javascript: void(0);" class="pic_del_mul">删除</a></div></div></div>';

                        }
                        ?>

                    <?php  endif; ?>

                </div>
            </div>

            <div class="promsg" style="display: none;">
                SKU信息(卖家设置SKU基本信息)
            </div>
        </div>
    </div>

    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;多账号价格设置</span>
                </h1>
            </div>
        </div>


        <div id="all_price">

        </div>

        <div class="probody">
            <div class="procnt">
            </div>

            <div class="promsg" style="display: none;">
                多账号价格设置(设置多个账号的价格)
            </div>
        </div>
    </div>


    <!--图片信息-->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;图片信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">

                <div class="form-group clearfix">
                    <label class="col-sm-2 control-label">图片信息：<br/>(勾选设置橱窗图)</label>

                    <div class="col-sm-10">
                        <div>
                            <a href="javascript:void(0);" class="btn btn-default btn-sm image_url">图片外链</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="getSkuPic()">获取SKU图片</a>
                            <a class="btn btn-default btn-sm dir_add" href="javascript: void(0);" onclick="getSkuPic('sp')">获取实拍图片</a>
                            <a class="btn btn-default btn-sm " href="javascript: void(0);"
                               onclick="copyToDescription()">复制到描述图片</a>
                            &nbsp;&nbsp;
                            <a class="btn btn-xs btn-primary pic-del-all" title="全部删除"><i class="icon-trash"></i></a>
                            <b class="ajax-loading hide">图片上传中...</b>
                        </div>
                        <ul class="list-inline pic-detail" id="pic-detail">
                            <?php if(filterDataEbay('detailPicList', $product_info)):   ?>

                                <?php

                                $detailPicList = filterDataEbay('detailPicList', $product_info);
                                $detailPicList = json_decode($detailPicList,true);
                                foreach($detailPicList as $pic){
                                    echo '<li><div><img src="'.$pic.'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="'.$pic. '" /> <input type="checkbox" title="可做为橱窗图" name="specify_image[]" value="'.$pic.'" ><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                                }

                                ?>

                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="promsg" style="display: none;">
                图片信息(卖家设置橱窗图片)
            </div>
        </div>
    </div>

    <!--详情描述-->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;广告描述</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">


                <div class="form-group">
                    <label for="detail" class="col-sm-2 control-label">模板标题:</label>

                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="template_titlle"  value="<?php   echo filterDataEbay('template_titlle', $product_info) ?>"  />
                    </div>
                </div>


                <div class="form-group">
                    <label for="detail" class="col-sm-2 control-label">描述模板:</label>

                    <div class="col-sm-4">
                        <select name="templatehtml">
                            <option value="">==请选择==</option>
                            <?php foreach ($templatehtml as $html): ?>
                                <option
                                    value="<?php echo $html['id'] ?>"  <?php   if(filterDataEbay('templatehtml', $product_info)==$html['id']){ echo  'selected="selected"';} ?> > <?php echo $html['template_name'] ?> </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>


                <div class="form-group">
                    <label for="detail" class="col-sm-2 control-label">卖家描述:</label>

                    <div class="col-sm-4">

                        <select name="template">
                            <option value="">==请选择==</option>
                            <?php foreach ($template as $html): ?>
                                <option value="<?php echo $html['id'] ?>"  <?php   if(filterDataEbay('template', $product_info)==$html['id']){ echo  'selected="selected"';} ?>  > <?php echo $html['name'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <div class="form-group clearfix">
                    <label class="col-sm-2 control-label">描述图片：</label>

                    <div class="col-sm-10">
                        <div>
                            <a href="javascript:void(0);" class="btn btn-default btn-sm image_url_description">图片外链</a>&nbsp;&nbsp;
                            <a class="btn btn-xs btn-primary pic-del-all" title="全部删除"><i class="icon-trash"></i></a>
                            <b class="ajax-loading hide">图片上传中...</b>
                        </div>
                        <ul class="list-inline pic-detail" id="pic-detail_description">


                            <?php if(filterDataEbay('detailPicListDescription', $product_info)):   ?>

                                <?php

                                $detailPicListDescription = filterDataEbay('detailPicListDescription', $product_info);
                                $detailPicListDescription = json_decode($detailPicListDescription,true);
                                foreach($detailPicListDescription as $pic){
                                    echo '<li><div><img src="'.$pic.'" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicListDescription[]" value="'.$pic. '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                                }
                                ?>

                            <?php endif; ?>
                        </ul>
                    </div>
                </div>


                <div class="form-group">
                    <label for="detail" class="col-sm-2 control-label">详情描述:</label>

                    <div class="col-sm-10">

					<textarea name="detail" id="detail" class="form-control">
                      <?php       echo htmlspecialchars_decode(filterDataEbay('detail', $product_info));        ?>
					</textarea>
                    </div>
                </div>
            </div>

            <div class="promsg" style="display: none;">
                广告描述(设置广告的描述信息)
            </div>
        </div>
    </div>


    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;国内物流</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label class="control-label col-sm-2">物流模板：</label>

                    <div class="col-sm-4">
                        <select name="transtemplate" id="transtemplate">
                            <option value="">==请选择==</option>
                            <?php foreach ($transtemplate as $trans): ?>
                                <option
                                    value="<?php echo $trans['id']; ?>"  <?php   if(filterDataEbay('transtemplate', $product_info)==$trans['id']){ echo  'selected="selected"';} ?>  > <?php echo $trans['transtemplatename']; ?></option>
                            <?php endforeach; ?>

                        </select>
                    </div>
                </div>
                <hr/>


                <?php
                if(filterDataEbay('transtemplate', $product_info)){
                    //shippingServiceOptions
                    $shippingServiceOptions =  unserialize(filterDataEbay('shippingServiceOptions', $product_info));

                }
                ?>

                <div class="form-group">
                    <label class="col-sm-2 control-label" ></label>
                    <div class="col-sm-10">
                        <span style="font-weight:bold;">第一运输</span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" >运输方式</label>

                    <input type="text" id="inter_trans_type" class="hidden"/>

                    <div class="col-sm-10">
                        <select id="guoneiyunshu1" name="guoneiyunshu1">
                            <option value="">--请选择--</option>
                            <?php  foreach($guoneitrans as $guonei ): ?>
                                <option value="<?php  echo $guonei['shippingservice']; ?>"    <?php   if(isset($shippingServiceOptions[1]['ShippingService'])&&($shippingServiceOptions[1]['ShippingService']==$guonei['shippingservice'])){echo 'selected="selected"';}              ?> > <?php echo $guonei['description']; ?></option>
                            <?php  endforeach; ?>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" >运费</label>
                    <div class="col-sm-10">
                        <input type="text"  id="guoneiyunfei1" name="guoneiyunfei1" value="<?php   if(isset($shippingServiceOptions[1]['ShippingServiceCost'])){echo $shippingServiceOptions[1]['ShippingServiceCost'];}              ?>" placeholder="0.00"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" >额外每件加收</label>
                    <div class="col-sm-10">
                        <input type="text" id="guoneiewaijiashou1" name="guoneiewaijiashou1" value="<?php   if(isset($shippingServiceOptions[1]['ShippingServiceAdditionalCost'])){echo $shippingServiceOptions[1]['ShippingServiceAdditionalCost'];}              ?>" placeholder="0.00"/>
                    </div>
                </div>

            </div>

            <div class="promsg" style="display: none;">
                物流模板设置( 国内物流 )
            </div>
        </div>
    </div>


    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;国际物流</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <?php
                if(filterDataEbay('transtemplate', $product_info)){
                    //shippingServiceOptions
                    $internationalShippingServiceOption =  unserialize(filterDataEbay('internationalShippingServiceOption', $product_info));
                //    var_dump($internationalShippingServiceOption);
                //    echo $internationalShippingServiceOption[2]['ShippingServiceCost'];
                }
                ?>
                <?php $array = array(1,2);$array_name = array(1=>'第一运输',2=>'第二运输',3=>'第三运输',4=>'第四运输',5=>'第五运输');  foreach($array as $i):  ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" ></label>
                        <div class="col-sm-10">
                            <span style="font-weight:bold;"><?php  echo $array_name[$i];  ?></span>
                        </div>
                    </div>



                    <div class="form-group">
                        <label class="col-sm-2 control-label" >运输方式</label>

                        <div class="col-sm-10">
                            <select id="<?php echo "yunshufangshi".$i; ?>" name="<?php echo "yunshufangshi".$i; ?>">
                                <option value="">--请选择--</option>

                                <?php  foreach($guowaitrans as $guowai ): ?>
                                    <option   value="<?php  echo $guowai['shippingservice']  ?>"  <?php   if(isset($internationalShippingServiceOption[$i]['ShippingService'])&&($internationalShippingServiceOption[$i]['ShippingService']==$guowai['shippingservice'])){echo 'selected="selected"';}         ?> ><?php  echo $guowai['description'];  ?></option>
                                <?php  endforeach; ?>

                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" >运费</label>
                        <div class="col-sm-10">
                            <input type="text" id="<?php echo "yunfei".$i; ?>"   name="<?php echo "yunfei".$i; ?>"  value="<?php   if(isset($internationalShippingServiceOption[$i]['ShippingServiceCost'])){ echo $internationalShippingServiceOption[$i]['ShippingServiceCost']; }              ?>"  placeholder="0.00"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" >额外每件加收</label>
                        <div class="col-sm-10">
                            <input type="text" id="<?php echo "ewai".$i; ?>"  name="<?php echo "ewai".$i; ?>"   value="<?php   if(isset($internationalShippingServiceOption[$i]['ShippingServiceAdditionalCost'])){echo $internationalShippingServiceOption[$i]['ShippingServiceAdditionalCost'];}              ?>"   placeholder="0.00"/>
                        </div>
                    </div>


                    <div class="form-group"><label class="col-sm-2 control-label"   >运到</label>
                        <div class="col-sm-6">
                            <input type="checkbox"  id="<?php  echo "Worldwide".$i; ?>" name="<?php  echo "Worldwide".$i; ?>"  <?php if(isset($internationalShippingServiceOption[$i]['ShipToLocation'])&&($internationalShippingServiceOption[$i]['ShipToLocation']=='Worldwide')){ echo 'checked="checked"';} ?> /><span>全球   </span>
                        </div>
                    </div>

                    <div class="form-group"><label class="col-sm-2 control-label"   >运输国家</label>
                        <div class="col-sm-4">


                            <input  class="form-control" id="<?php echo "guanjia".$i; ?>" name="<?php echo "guanjia".$i."[]" ?>" value="<?php

                            if(isset($internationalShippingServiceOption[$i]['ShipToLocation'])&&($internationalShippingServiceOption[$i]['ShipToLocation'] !='Worldwide')){
                                $international_is_country= json_decode($internationalShippingServiceOption[$i]['ShipToLocation'],true);
                                $international_is_country = implode(',',$international_is_country);
                                echo $international_is_country;
                            }
                            ?>">
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="promsg" style="display: none;">
                物流模板设置(设置买家要求 国内物流 国际物流 物品所在地 退货政策)
            </div>
        </div>
    </div>



    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;国际物流</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label class="control-label col-sm-2">不运送国家：</label>

                    <div class="col-sm-4">
                        <input class="form-control" type="text" id="un_ship" name="un_ship" value="<?php  echo filterDataEbay('un_ship', $product_info)  ?>  "/>
                        <span class="red">填写的国家会与物流模板设置的不运送国家相叠加</span>

                    </div>
                </div>
            </div>

            <div class="promsg" style="display: none;">
                物流模板设置(设置买家要求 国内物流 国际物流 物品所在地 退货政策)
            </div>
        </div>
    </div>





    <div class="clearfix form-actions">
        <div class="col-md-offset-3 col-md-9">

            <!--action用来判断是提交到哪个操作-->
            <input type="hidden" name="action" id="action" value=""/>
            <input type="hidden" name="id" value="<?php  echo filterDataEbay('id', $product_info)  ?>" id="id"/>
            <!--保存-->
            <button class="btn btn-success submit_btn" type="submit" name="save">
                <i class="icon-ok bigger-110"></i>
                保存
            </button>



            <!--修改并发布-->
            <button class="btn btn-inverse submit_btn" type="submit" name="editAndPost">
                <i class="icon-ok bigger-110"></i>
                保存并发布
            </button>

            <button class="btn btn-reset" type="reset">
                <i class="icon-undo bigger-110"></i>重置
            </button>
        </div>
    </div>
</form>


<div class="hide" id="showDiv" style="overflow:scroll; width: 1000px; height: 500px;"></div>
<script type="text/javascript" src="<?php echo static_url('theme/common/jquery.dragsort-0.5.1.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo static_url('theme/common/css/style2.css'); ?>">

<script type="text/javascript">
    //编辑器调用
    KindEditor.ready(function (K) {
        var editor = K.create("#detail", {
            "allowFileManager": true,
            "allowImageManager": true,
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
                'emoticons', 'link', 'unlink', 'table'],
            "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
            "afterBlur": function () {
                this.sync();
            } //必须，不然第一次提交不到
        });

        var editor2 = K.editor({
            allowFileManager: false,
            uploadJson: '<?php echo admin_base_url('kindeditor/uploadWishToProject');?>'
        });


        //图片上传，路径应该要处理下
        K('.from_local').click(function () {
            editor2.loadPlugin('image', function () {
                editor2.plugin.imageDialog({
                    showRemote: false,
                    clickFn: function (url, title, width, height, border, align) {
                        var myli = '<li><div><img src="<?php echo site_url().'attachments/upload';?>' + url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="<?php echo site_url().'attachments/upload';?>' + url + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                        K('.pic-detail').append(myli);
                        editor2.hideDialog();
                    }
                });
            });
        });


    });


    $(function () {
        layer.use('extend/layer.ext.js');
        $(".pic-main, .pic-detail, .relate-list").dragsort({
            dragSelector: "div",
            placeHolderTemplate: "<li class='placeHolder'><div></div></li>"
        });
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


        $(document).on('click', '.pic_del_mul', function () {
            $(this).parent().remove();
        });

        $('.submit_btn').click(function (e) {

            $('#action').val($(this).attr('name'));

        });

        //账号改变的时候产品图片为空
        $("#choose_account").change(function () {
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
                    if (data.data) {
                        $('#id').val(data.data);
                    }
                    showxbtips(data.info);
                } else {
                    showxbtips('2222', 'alert-warning');
                }
            }
        });


        $(".choose_account").click(function () {               //checkBox点击事件
            var value = $(this).val();
            var text_name = $(this).parent().text();
            var id_name = 'account_tittle_' + value;
            if ($(this).is(':checked')) { // 选中
                var add = '<div id="' + id_name + '"> ' +
                    '<div class="form-group">' +
                    '<label class="control-label col-sm-2"> ' + text_name + ' 标题：</label>' +
                    '<div class="col-sm-9">' +
                    '<input maxlength="80" type="text" onkeyDown="gettip(this)" class="tittle_input col-sm-9" name="account_tittle[' + value + '][tittle]"  /> <a class="black" title="填写子标题" href="javascript:" onclick="addSubTittle(this)"><i class=" glyphicon glyphicon-pencil bigger-200" ></i></a>' +
                    '<div class="help-block">提示信息</div></div>' +
                    '</div>' +
                    '<div class="form-group hidden">' +
                    '<label class="control-label col-sm-2">子标题：</label> ' +
                    '<div class="col-sm-4">' +
                    '<input maxlength="80"  type="text" class="form-control"  name="account_tittle[' + value + '][tittle_sub]" />' +
                    '</div>' +
                    '</div>' +
                    '<hr/>' +
                    '</div>';
                $('#mul_account_tittle').append(add);
            } else { //取消
                $("#account_tittle_" + value).remove();
            }
        });




        $("#ebay_condition").change(function () {
            //ebay_condition_detail

            if ($("#ebay_condition").val() == 1000) {
                if (!$("#ebay_condition_detail").hasClass("hidden")) {
                    $("#ebay_condition_detail").addClass("hidden");
                }
            } else {
                if ($("#ebay_condition_detail").hasClass("hidden")) {
                    $("#ebay_condition_detail").removeClass("hidden");
                }
            }
        });


        $(".specifics_choose").change(function () {
            var new_value = $(this).val();
            $(this).prev().val(new_value);
        })


    });


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
    //图片外链
    $(".image_url").click(function () {
        layer.prompt({title: '请输入图片外链,并确认', type: 0}, function (pass, index, el) {
            if (pass.trim() == '') {
                layer.close(index);
                return false;
            }
            layer.close(index);

            $('.ajax-loading').removeClass('hide');

            var url = pass;
            var liStr = '';
            liStr += '<li><div><img src="' + url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="' + url + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
            $("#pic-detail").append(liStr);
            layer.msg('图片上传成功', 2, -1);
            $('.ajax-loading').addClass('hide');

        });
    });

    $(".image_url_description").click(function () {
        layer.prompt({title: '请输入图片外链,并确认', type: 0}, function (pass, index, el) {
            if (pass.trim() == '') {
                layer.close(index);
                return false;
            }
            layer.close(index);

            $('.ajax-loading').removeClass('hide');

            var url = pass;
            var liStr = '';
            liStr += '<li><div><img src="' + url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicListDescription[]" value="' + url + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
            $("#pic-detail_description").append(liStr);
            layer.msg('图片上传成功', 2, -1);
            $('.ajax-loading').addClass('hide');

        });
    });

    $("#all_check").click(function(){
        if($("#all_check").is(':checked')){

            $("input[name='choose_account[]']:checkbox").each(function(){
                var value = $(this).val();
                var text_name = $(this).parent().text();
                var id_name = 'account_tittle_' + value;

                if (!$(this).is(':checked')) {

                    $(this).prop('checked',true);
                    var add = '<div id="' + id_name + '"> ' +
                        '<div class="form-group">' +
                        '<label class="control-label col-sm-2"> ' + text_name + ' 标题：</label>' +
                        '<div class="col-sm-9">' +
                        '<input maxlength="80" type="text" onkeyDown="gettip(this)" class="col-sm-9" name="account_tittle[' + value + '][tittle]"  /> <a class="black" title="填写子标题" href="javascript:" onclick="addSubTittle(this)"><i class=" glyphicon glyphicon-pencil bigger-200" ></i></a>' +
                        '<div class="help-block">提示信息</div></div>' +
                        '</div>' +
                        '<div class="form-group hidden">' +
                        '<label class="control-label col-sm-2">子标题：</label> ' +
                        '<div class="col-sm-4">' +
                        '<input  maxlength="80" type="text" class="form-control"  name="account_tittle[' + value + '][tittle_sub]" />' +
                        '</div>' +
                        '</div>' +
                        '<hr/>' +
                        '</div>';
                    $('#mul_account_tittle').append(add);
                }
            });

        }else{

            $("input[name='choose_account[]']:checkbox").each(function(){
                var value = $(this).val();
                var text_name = $(this).parent().text();
                var id_name = 'account_tittle_' + value;

                if ($(this).is(':checked')) {
                    $(this).removeProp('checked');
                    $(this).prop('checked',false);
                    $("#account_tittle_" + value).remove();

                }
            });

        }

      //  alert(123312);
    });



    $("#transtemplate").change(function(){
        var id = $("#transtemplate").val();
        if(id==''){
            return false;
        }
        $.ajax({
            url: '<?php echo admin_base_url("ebay/ebay_product/set_shipment");?>',
            data: 'id=' +id,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
               // alert(data.data.inter_trans_type);

                $("#guoneiyunshu1").val(data.data.inter_trans_type);
                $("#guoneiyunfei1").val(data.data.inter_trans_cost);
                $("#guoneiewaijiashou1").val(data.data.inter_trans_extracost);

                $("#yunshufangshi1").val(data.data.international_type1);
                $("#yunfei1").val(data.data.international_cost1);
                $("#ewai1").val(data.data.international_extracost1);
                if(data.data.international_is_worldwide1=='on'){
                        $("#Worldwide1").prop("checked",true);
                }
                $("#guanjia1").val(data.data.international_is_country1);

                $("#yunshufangshi2").val(data.data.international_type2);
                $("#yunfei2").val(data.data.international_cost2);
                $("#ewai2").val(data.data.international_extracost2);
                if(data.data.international_is_worldwide2=='on'){
                    $("#Worldwide2").prop("checked",true);
                }
                $("#guanjia2").val(data.data.international_is_country2);

                $("#un_ship").val(data.data.excludeship)

            }
        });
    });

    /*$(".image_url_mul").click(function () {
     var e = $(this);
     layer.prompt({title: '请输入图片外链,并确认', type: 0}, function (pass, index, el) {
     if (pass.trim() == '') {
     layer.close(index);
     return false;
     }
     layer.close(index);

     //   $('.ajax-loading').removeClass('hide');

     var url = pass;
     var liStr = '';
     liStr += '<div><img src="' + url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicListMul[]" value="' + url + '" /><a href="javascript: void(0);" class="pic_del_mul">删除</a></div>';
     //  $("#pic-detail_description").append(liStr);
     e.parent().append(liStr);
     //  layer.msg('图片上传成功', 2, -1);
     // $('.ajax-loading').addClass('hide');

     });
     });*/
    function gettip(e){
        var num = 80;
        var  now_length = $(e).val();
        $(e).next().next().html('还能够输入<i class="red">' + (num - now_length.length) + '</i>个字符');
    }

    //加属性
    function addSpecifics() {
        layer.prompt({title: '请输入属性名称，并确认', type: 0}, function (pass, index, el) {
                if (pass.trim() == '') {
                    layer.close(index);
                    return false;
                }
                layer.close(index);

                var add_info = '<div class="form-group  col-sm-6">' +
                    '<label class="control-label col-sm-4">' + pass + ':</label>' +
                    '<div class="col-sm-6"> <input type="text" class="col-sm-8" name="user_spe[' + pass + ']"/><a class="red" href="javascript:" onclick="deleteSpecifics(this)" >' +
                    '<i class=" icon-trash bigger-200" ></i>' +
                    '</a></div></div>';

                $('#all_specifics').append(add_info);
            }
        );
    }
    //获取图片
    function getSkuPic(type='no_sp') {
        layer.prompt({title: '请输入属性名称，并确认', type: 0}, function (pass, index, el) {
                if (pass.trim() == '') {
                    layer.close(index);
                    return false;
                }
                layer.close(index);

                $.ajax({
                    url: '<?php echo admin_base_url("ebay/ebay_product/getSkuPic");?>',
                    data: 'sku=' + pass + '&type=' + type,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        if (data.status == 1) {
                            var liStr = '';
                            $.each(data.data, function (index, el) {
                                liStr += '<li><div><img src="' + el + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicList[]" value="' + el.replace('getSkuImageInfo-resize','getSkuImageInfo') + '" /><input type="checkbox" title="可做为橱窗图" name="specify_image[]" value="' + el.replace('getSkuImageInfo-resize','getSkuImageInfo') + '" ><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
                            });
                            $("#pic-detail").append(liStr);
                        } else {
                            alert(data.info);
                        }

                    }
                });
            }
        );
    }
    //复制到描述
    function copyToDescription() {
        var liStr = '';
        $("input[name='detailPicList[]']").each(function () {
            var el = $(this).val();
            if (el != '') {
                liStr += '<li><div><img src="' + el + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicListDescription[]" value="' + el + '" /><a href="javascript: void(0);" class="pic-del">删除</a></div></li>';
            }
        });

        $("#pic-detail_description").append(liStr)
    }
    //加多属性的属性
    function addSpe(auto='') {

        if (auto == '') {
            var name = prompt("输入你要添加的属性", "")
            if (name != null && name != "") {

                if ((name == 'UPC') || (name == 'EAN')) {
                    nv = name;
                } else {
                    var str = name;
                    var nv = str.toLowerCase().replace(/\b(\w)/g, function ($0, $1) {
                        return $1.toUpperCase();
                    });
                }

            }
            else {
                return false;
            }
        } else {
            var nv = auto;
        }
        var i = 0;
        var j = 0;
        $('#table1').find('tr').each(function (i) {
            var tr = $(this);
            if (i == 0) {
                tr.append('<td  onclick="deleteinfoNew(this)" >' + nv + '<input size="15"  class="hidden " type="text" name="zidingyi[]" value="' + nv + '"/></td>');
            }
            else {
                if (auto == '') {
                    tr.append('<td ><input type="text" size="15"    name="skuinfo[' + nv + '][]"  /></td>');
                } else {
                    tr.append('<td ><input type="text" size="15"  value="Does not apply"   name="skuinfo[' + nv + '][]"  /></td>');
                }

                j++
            }
            i++;

        })
    }


    //删除加的多属性
    function deleteinfoNew(e) {
        if (confirm("是否要删除该属性")) {
            var text = $(e).text();
            var rr = $(e).prevAll().length + 1;
            var tt = $(e).parent().prevAll().length + 1;
            $('#table1 tr th:eq(' + tt + ')').remove();
            $('#table1 tr td:nth-child(' + rr + ')').remove();
            $("#tupianshezhiselect option[value='" + text + "']").remove();
        }
    }
    //移除某一行
    function removeRow(e) {
        $(e).parent().parent().remove();
    }
    //增加一行
    function addRow() {
        var newRow = '<tr><td><input type="text" name="skuinfo[sku][]"/></td> <td><input type="text" name="skuinfo[price][]"/></td><td><input type="text" name="skuinfo[quantity][]"/></td><td><a class="remove btn btn-primary btn-sm" href="javascript: void(0);" onclick="removeRow(this)">移除</a></td>';
        var tab = document.getElementById("table1");
        var rows = tab.rows.length;
        var cells = tab.rows.item(0).cells.length;

        $('#table1').find('tr').each(function (i) {
            var tr = $(this);
            if (i == 0) {
                if (cells > 4) {
                    var addSpeHtml = '';
                    for (var j = 4; j < cells; j++) {
                        var addSpe = tr.children().eq(j).text();
                        addSpeHtml = addSpeHtml + '<td><input type="text" name="skuinfo[' + addSpe + '][]" size="15"></td>'
                    }
                    newRow = newRow + addSpeHtml + '</tr>';
                    $("#table1 tr:last").after(newRow);
                } else {
                    newRow = newRow + '</tr>';
                    $("#table1 tr:last").after(newRow);
                }
            }
        });

    }
    //删除加的属性
    function deleteSpecifics(e) {
        $(e).parent().parent().remove();
    }

    function getSkuPrice(){
        $("#all_price").empty();
        var type = $("input[name='choose_type']:checked").val()
        var price ='';

        if(type==''){
            return  false;
        }
        if(type==1||type==2){

            price = $("#ebay_price").val();
        }

        if(type==3){
                var j=0;
            $("input[name='skuinfo[price][]']").each(function(){
                if(j==0){
                    price = $(this).val();
                }
                j++;
            });
        }

        if(price==''||price=='undefined'){
            alert("先设置价格");
            return false;
        }

       // var arr =[0.01,0.02,0.03,-0.01,-0.02,-0.03];

         var price_arr = new Array();
         var k=0;
        $("input[name='choose_account[]']:checkbox").each(function(){
          //  var index = Math.floor((Math.random()*arr.length));
           // var newprice = parseFloat(price)+parseFloat(arr[index]);
          //  newprice= newprice.toFixed(2);
            var value = $(this).val();
            var text_name = $(this).parent().text();
            var id_name = 'account_tittle_' + value;
            var add_info = '';
            if ($(this).is(':checked')) {

                if(k==0){
                    price = parseFloat(price).toFixed(2);
                    price_arr[k]=price;
                }else{
                    price_arr = price_arr.toString();
                    price=   testPrice(price_arr);
                    price_arr = price_arr.split(",");
                    price_arr[k]=price;
                }
              //  price = price.toFixed(2);
                add_info='<div class="form-group"><label class="control-label col-sm-4">'+text_name+' :</label>' +
                '<div class="col-sm-4">' +
                '<input type="text" class="form-control" value="'+price+'" name="all_price['+value+']"/>' +
                '</div>' +
                '</div>';

                $("#all_price").append(add_info);
                k++;
            }

        });

    }

    function testPrice(array){
        var getArr = array.split(",");
        getArr.sort();
        if(parseFloat(getArr[0])<1.01){
            var newprice = parseFloat(getArr[getArr.length-1]) + 0.02;
             return  newprice.toFixed(2);

        }else{
            var newprice = parseFloat(getArr[0])-0.02;
            return  newprice.toFixed(2);
        }
    }





    //获取SKU 信息
    function getSkuInfo() {

        var type = $('input[name="choose_type"]:checked').val();

        var prefix_sku = $("#prefix_sku").val();

        if (prefix_sku != '') {
            prefix_sku = prefix_sku + '*';
        }



        var price_sku = $("#price_sku").val();
        var quantity_sku = $("#quantity_sku").val();

        var sku = $("#search_sku").val();
        var add_sku_mul = prefix_sku + sku;
        KindEditor.instances[0].html('');
        $("#detail").html('');
        if (type == 'undefined') {
            alert('请选择刊登类型');
            return false;
        } else if (sku == '') {
            alert('请输入SKU');
            return false;
        }
        $.ajax({
            url: '<?php echo admin_base_url("ebay/ebay_product/get_sku_info");?>',
            data: 'sku=' + sku + '&type=' + type,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {

                if (data.status == 1) {

                    $("#fixed_price").empty();
                    $("#auction").empty();
                    $("#mul_price").empty();
                    $("#mul_price_pic").empty();
                    if (type == 1) {
                        var add_sku = prefix_sku + data.data.sku[0];
                        var add_info = ' <div class="form-group"><label class="control-label col-sm-2">私人拍卖: </label>' +
                            '<div class="col-sm-4">' +
                            '<input type="checkbox" name="privateListing">不向公众显示买家的名称</div> </div> ' +
                            '<div class="form-group">' +
                            '<label class="control-label col-sm-2 red">sku(ebay后台): </label>' +
                            '<div class="col-sm-2">' +
                            '<input type="text" name="ebay_sku" value="' + add_sku + '"/>' +
                            '</div></div>' +
                            '<div class="form-group">' +
                            '<label class="control-label col-sm-2">起拍价格: </label>' +
                            '<div class="col-sm-2">' +
                            '<input type="text" id="ebay_price" name="ebay_price" value="' + price_sku + '"  />' +
                            '</div>' +
                            '<label class="control-label col-sm-2">数量: </label>' +
                            '<div class=" col-sm-2">' +
                            '<input type="text" name="ebay_quantity" value="' + quantity_sku + '"/>' +
                            '</div>' +
                            '</div>' +
                            '<hr>';
                        $("#auction").empty().append(add_info);
                    }

                    if (type == 2) {
                        var add_sku = prefix_sku + data.data.sku[0];

                        var add_info = '<div class="form-group">' +
                            '<label class="control-label col-sm-2 red">sku(ebay后台): </label>' +
                            '<div class="col-sm-2">' +
                            '<input type="text"  name="ebay_sku"  value="' + add_sku + '"/>' +
                            '</div></div>' +
                            '<div class="form-group">' +
                            '<label class="control-label col-sm-2">价格: </label>' +
                            '<div class=" col-sm-2">' +
                            '<input type="text" id="ebay_price" name="ebay_price" value="' + price_sku + '"  />' +
                            '</div>' +
                            '<label class="control-label col-sm-2">数量: </label> ' +
                            '<div class=" col-sm-2">' +
                            '<input type="text" name="ebay_quantity" value="' + quantity_sku + '"/>' +
                            '</div>' +
                            '</div>';
                        $("#fixed_price").empty().append(add_info);

                    }

                    if (type == 3) {
                        var add_sku_info = '';
                        for (var i = 0; i < data.data.sku.length; i++) {
                            var add_sku = prefix_sku + data.data.sku[i];
                            add_sku_info = add_sku_info + '<tr> <td><input type="text" name="skuinfo[sku][]" value="' + add_sku + '" /></td>' +
                            '<td><input type="text"  name="skuinfo[price][]" value="' + price_sku + '" /></td>' +
                            '<td><input type="text"  name="skuinfo[quantity][]" value="' + quantity_sku + '" /></td>' +
                            '<td><a class="remove btn btn-primary btn-sm" href="javascript: void(0);" onclick="removeRow(this)">移除</a></td></tr>';
                        }


                        var add_info = '<div class="form-group">' +
                            '<label class="control-label col-sm-2 red">sku(ebay后台): </label>' +
                            '<div class="col-sm-2">' +
                            '<input type="text"  name="ebay_sku"  value="' + add_sku_mul + '"/>' +
                            '</div></div>' +
                            '<div class="form-group">' +
                            '<div class="col-sm-2"></div>' +
                            '<div class="col-sm-8">' +
                            '<table class="table text-center table-bordered" id="table1">' +
                            '<tr><td>sku</td> <td>price</td> <td>quantity</td> <td></td> </tr>' + add_sku_info +
                            '</table></div></div><div class="form-group"> <div class="col-sm-2"> ' +
                            '</div> ' +
                            '<div class="col-sm-3">' +
                            '<a class="btn btn-primary btn-sm" href="javascript: void(0);" onclick="addRow()">增加一行</a>' +
                            '</div><div class="col-sm-3">' +
                            '<a class="btn btn-primary btn-sm" href="javascript: void(0);" onclick="addSpe()">增加属性</a> </div> ' +
                            '<div class="col-sm-3"><a class="btn btn-primary btn-sm" href="javascript: void(0);" onclick="setMul()">设置多属性图片</a></div>' +
                            '</div><hr/>';
                        $("#mul_price").empty().append(add_info);
                        var siteid =$("#site").val();
                        if((siteid==0)||(siteid==2)||(siteid==15)){
                            addSpe('UPC');
                        }

                        if((siteid==3)||(siteid==71)||(siteid==77)||(siteid==101)||(siteid==186)){
                            addSpe('EAN');
                        }

                    }
                    KindEditor.instances[0].html(data.data.products_html_mod);
                    $("#detail").html(data.data.products_html_mod);
                } else {
                    alert(data.info);
                    return false;
                }


            }
        });
    }
    //显示子标题
    function addSubTittle(e) {
        if ($(e).parent().parent().next().hasClass("hidden")) {
            $(e).parent().parent().next().removeClass("hidden");
        } else {
            $(e).parent().parent().next().addClass("hidden");
            $(e).parent().parent().next().children().eq(1).children().eq(0).val("");
        }

    }

    function setMul() {

        layer.prompt({title: '请输入属性名称，并确认', type: 0}, function (pass, index, el) {
                if (pass.trim() == '') {
                    layer.close(index);
                    return false;
                }
                layer.close(index);

                var pass = pass.toLowerCase().replace(/\b(\w)/g, function ($0, $1) {
                    return $1.toUpperCase();
                });
                var tab = document.getElementById("table1");
                var rows = tab.rows.length;
                var cells = tab.rows.item(0).cells.length;
                var i = 0;
                var is_true = true;
                $('#table1').find('tr').each(function (i) {
                    var tr = $(this);
                    if (i == 0) {
                        for (var j = 0; j < cells; j++) {
                            var addSpe = tr.children().eq(j).text();
                            if (addSpe == pass) {
                                $("#mul_price_pic").empty();
                                is_true = false;
                                var add_name = 'skuinfo[' + addSpe + '][]';
                                $("input[name='" + add_name + "']").each(function () {
                                    var el = $(this).val();
                                    var add_html = '';
                                    if (el != '') {
                                        if (!document.getElementById(el)) {
                                            add_html = '<div class="form-group" id="' + el + '">' +
                                            '<label class="col-sm-2 control-label">' + el + '：</label>' +
                                            '<div class="col-sm-3">' +
                                            '<a class="btn btn-primary btn-sm image_url_mul" onclick="image_url_mul(this)"  href="javascript: void(0);">添加图片</a>' +
                                            '</div>' +
                                            '</div>';

                                            $("#mul_price_pic").append(add_html)
                                        }
                                    }
                                });
                            }
                        }

                        if (is_true) {
                            alert("未找到该属性")
                        }
                    }
                    i++;
                });

            }
        );


    }

    function image_url_mul(e) {
        layer.prompt({title: '请输入图片外链,并确认', type: 0}, function (pass, index, el) {
            if (pass.trim() == '') {
                layer.close(index);
                return false;
            }
            layer.close(index);

            //   $('.ajax-loading').removeClass('hide');

            var url = pass;
            var liStr = '';
            var add_value = $(e).parent().parent().attr("id");
            liStr += '<div><img src="' + url + '" width="100" height="100" style="border: 0px;"><input type="hidden" name="detailPicListMul[' + add_value + ']" value="' + url + '" /><a href="javascript: void(0);" class="pic_del_mul">删除</a></div>';
            //  $("#pic-detail_description").append(liStr);
            $(e).parent().append(liStr);
            //  layer.msg('图片上传成功', 2, -1);
            // $('.ajax-loading').addClass('hide');

        });

    }

</script>





