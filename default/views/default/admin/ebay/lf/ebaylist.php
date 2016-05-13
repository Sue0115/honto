<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/15
 * Time: 14:38
 */

?>
<div class="modal fade" id="ModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >复制草稿</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal"  >

                    <div class="form-group">
                        <label class="col-sm-2">模板ID号</label>
                        <div class="col-sm-6">
                            <input type="text"  id="mubanid" readonly/>

                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-sm-2">账号:</label>
                        <div class="col-sm-6">
                        </div>
                    </div>
                            <?php

                            $tokenarr =array(6,20,12,25,21,22,9,46,45,15,17,7,8,11,48,14,24,33,42,43,47,51,5,18,16,19);
                            foreach($token as $t)
                            {
                                if(in_array($t['token_id'],$tokenarr))
                                {
                                    echo'<div class="col-sm-4">';
                                    echo $t['seller_account'].'<input type="checkbox"  name="ebayaccount[]" value="'.$t['token_id'].'">';
                                    echo'</div>';
                                }


                            }
                            ?>

                    <div class="modal-footer">
                        <a href="#"   class="btn btn-primary " id="pricecheck">确定</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">Ebay刊登-刊登列表</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                账号:
                                <select name="search[seller_account]" id="seller_account">
                                    <option value="">---全选---</option>
                                    <?php
                                    foreach($token as $t):
                                        echo '<option value="'.$t['seller_account'].'" '.($search['seller_account'] == $t['seller_account'] ? 'selected="selected"': '').'>'.$t['seller_account'].'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>

                            <label>
                                站点:
                                <select name="search[site]" id="site">
                                    <option value="999">---全选---</option>
                                    <?php
                                    foreach($sitearr as $key=>$t):
                                        echo '<option value="'.$key.'" '.(  (isset($search['site'])&&$search['site'] == $key) ? 'selected="selected"': '').'>'.$t.'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                        <label>
                            设置人员:
                            <select name="search[user_id]" id="user_id">
                                <option value="">---全选---</option>
                                <?php
                                if ($all_user) {

                                    foreach ($all_user as $user)
                                    {
                                        if(!empty($user['user_id']))
                                        echo '<option value="'.$user['user_id'].'" >'.$user_last[$user['user_id']].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </label>                            
                            <label>
                                广告状态:

                                <select name="search[productStatusType]" id="productStatusType">
                                    <option value="">--所有状态--</option>
                                    <option value="1" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 1 ? 'selected="selected"': ''); } ?>>未刊登</option>
                                    <option value="2" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 2 ? 'selected="selected"': ''); } ?>  >已刊登</option>
                                    <option value="3" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 3 ? 'selected="selected"': ''); } ?> >刊登失败</option>
                                    <option value="4" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 4 ? 'selected="selected"': ''); } ?> >已下架</option>
                                </select>
                            </label>
                            <label>
                                刊登类型:

                                <select name="search[ad_type]" id="ad_type">
                                    <option value="">--所有类型--</option>
                                    <option value="paimai" <?php   if(isset($search['ad_type'])){ echo ($search['ad_type'] == 'paimai' ? 'selected="selected"': ''); } ?>>拍卖</option>
                                    <option value="guding" <?php   if(isset($search['ad_type'])){ echo ($search['ad_type'] == 'guding' ? 'selected="selected"': ''); } ?>  >固定</option>
                                    <option value="duoshuxing" <?php   if(isset($search['ad_type'])){ echo ($search['ad_type'] == 'duoshuxing' ? 'selected="selected"': ''); } ?> >多属性</option>>
                                </select>
                            </label>
                            <label>
                                ItemID:
                                <input type="text" name="search[itemId]" placeholder="请输入itemID"  value="<?php  echo (isset($search['itemId'])  ? $search['itemId'] : '');  ?>">
                            </label>
                            <label>
                                SKU:
                                <input type="text" name="search[sku]" placeholder="不要输入前后缀" value="<?php  echo (isset($search['sku'])  ? $search['sku']: '');  ?>" >
                            </label>
                            <label>
                                标题:
                                <input type="text" name="search[subject]"  value="<?php  echo (isset($search['subject']) ? $search['subject']: ''); ?>"/>
                            </label>

                            <label>

                                刊登时间:
                                <input type="text"   name="search[creattime1]" value="<?php  echo (isset($search['creattime1']) ? $search['creattime1']: ''); ?>" datefmt="yyyy-MM-dd" class="Wdate" onfocus="WdatePicker({skin:'whyGreen',minDate: '2000-01-10', maxDate: '2050-01-01' })"/>~<input type="text"  name="search[creattime2]" value="<?php  echo (isset($search['creattime2']) ? $search['creattime2']: ''); ?>" datefmt="yyyy-MM-dd" class="Wdate" onfocus="WdatePicker({skin:'whyGreen',minDate: '2000-01-10', maxDate: '2050-01-01' })"/>

                            </label>

                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">筛选</button>
                            </label>



                            <label>
                                <a class="btn btn-primary btn-sm" href="javascript: void(0);" id="batch_delete">批量删除</a>
                            </label>

                            <label>
                                <a class="btn btn-primary btn-sm" href="javascript: void(0);" id="batch_mod">批量刊登</a>
                            </label>

                            <label>
                                <a class="btn btn-primary btn-sm" href="javascript: void(0);" id="batch_down">批量下架</a>
                            </label>


                            <label>
                                <a class="btn btn-primary btn-sm " href="<?php echo admin_base_url('ebay/ebay_export/index');?>" target="_blank" >ebay数据导入</a>
                            </label>

                            <br/>

                            <label>
                                <a class="btn btn-primary btn-sm" target="_blank"  href="<?php echo admin_base_url('ebay/ebay_product/ebaylistting');?>">添加</a>
                            </label>

                            <label>
                                <a class="btn btn-primary btn-sm " href="javascript: void(0);" id="copylistinfo">草稿复制</a>
                            </label>


                            <label>
                                <a class="btn btn-primary btn-sm "  target="_blank"  href="<?php echo admin_base_url('ebay/ebay_product_list/draft_center');?>" >草稿列表</a>
                            </label>


                            <label>
                                <a class="btn btn-primary btn-sm "  target="_blank"  href="<?php echo admin_base_url('ebay/ebay_product_list/product_center');?>" >产品列表</a>
                            </label>


                            <label>
                                <a class="btn btn-primary btn-sm "  target="_blank"  href="<?php echo admin_base_url('ebay/ebay_product/add');?>" >刊登新入口</a>
                            </label>



                            <label>
                                <a class="btn btn-primary btn-sm "  target="_blank"  href="<?php echo admin_base_url('ebay/ebay_store_category/store_category_list');?>" >ebay店铺分类绑定</a>
                            </label>




                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="10%">
                        <col width="5%">
                        <col width="10%">
                        <col width="15%">
                        <col width="10%">
                        <col width="5%">
                        <col width="5%">
                        <col width="10%">
                        <col width="10%"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>ID</th>
                        <th>名称</th>
                        <th>图片</th>
                        <th>ebay账号</th>
                        <th>站点</th>
                        <th>刊登类型</th>
                        <th>标题</th>
                        <th>sku</th>
                        <th>设置人员</th>
                        <th>刊登类型</th>
                        <th>价格</th>

                        <th>刊登状态</th>
                        <th>itemid/失败原因</th>
                        <th>上架时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($infolist):
                        foreach($infolist as $item):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item['id'];?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $item['id'];?></td>
                                <td><?php echo $item['name']; ?></td>

                                 <td><?php
                                     if(!empty($item['ebay_picture']))
                                     {
                                         $pictureinfo = json_decode($item['ebay_picture'],true);


                                         echo '<img src='.$pictureinfo[0].'   width="100" height="100" />';
                                     }


                                     ?></td>
                                <td><?php echo $item['ebayaccount']; ?></td>
                                <td><?php echo  $sitearr[$item['site']]; ?></td>
                                <td><?php
                                    if($item['ad_type']=='paimai')
                                        echo '拍卖';
                                    if($item['ad_type']=='guding')
                                        echo '固定';
                                    if($item['ad_type']=='duoshuxing')
                                        echo '多属性';
                                    ?></td>
                                <td><?php echo $item['title'];?></td>
                                <td><?php
                                           echo $item['sku'];

                                    ?></td>
                                <td>
                                    <?php   if(isset($user_last[$item['user_id']])) echo  $user_last[$item['user_id']]; ?>
                                </td>
                                <td><?php
                                    if($item['ad_type']=='paimai')
                                        echo '拍卖';
                                    if($item['ad_type']=='guding')
                                        echo '固定';
                                    if($item['ad_type']=='duoshuxing')
                                        echo '多属性';
                                    ?></td>                                
                                <td><?php
                                    if($item['ad_type']=='duoshuxing')
                                    {
                                        $mularray = json_decode($item['mul_info'],true);

                                        if(!empty($mularray))
                                        {
                                            $price_array = array();
                                            foreach($mularray as $mul)
                                            {
                                                $price_array[] = $mul['price'];
                                            }

                                            sort($price_array);
                                            echo $price_array[0];
                                        }
                                    }
                                    else
                                    {
                                        echo $item['price'];
                                    }

                                    ?></td>

                                <td><?php
                                    if($item['status']  ==1)
                                    {
                                        echo '未刊登';
                                    }
                                    if($item['status']  ==2)
                                    {
                                        echo '已刊登';
                                    }
                                    if($item['status']  ==3)
                                    {
                                        echo '刊登失败';
                                    }
                                    if($item['status']  ==4)
                                    {
                                        echo '已下架';
                                    }
                                    ?></td>
                                <td><?php
                                    if($item['status']  ==1)
                                    {
                                        echo '--';
                                    }
                                    if($item['status']  ==2)
                                    {
                                      echo ' <a  target="_blank" href=http://www.ebay.com/itm/'.$item['itemid'].'>'.$item['itemid'].'</a>';
                                    }
                                    if($item['status']  ==3)
                                    {
                                     //   echo $item['failurereasons'];
                                        echo $item['failurereasons'];
                                    }
                                    if($item['status']  ==4)
                                    {
                                        echo  '已下架';
                                    }
                                    ?></td>
                                <td>
                                    <?php
                                    echo $item['starttime'];
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a title="修改"  target="_blank"   href="<?php echo admin_base_url('ebay/ebay_product/ebaylistting?id='.$item['id']);?>" class="green">
                                            <i class="icon-pencil bigger-130"></i>
                                        </a>


                                        &nbsp;

                                        &nbsp;
                                        <a title="删除" href="javascript:void(0);" onclick="msgdelete(<?php echo $item['id'];?>, '<?php echo admin_base_url("ebay/ebaylist/delete"); ?>')" class="red">
                                            <i class="icon-trash bigger-130"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    </tbody>
                </table>
                <?php  $this->load->view('admin/common/page_number');?>

            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '#batch_mod', function(e){
        if(confirm("确定要刊登吗?")) {
            var productIds = $('input[name="ids[]"]:checked').map(function () {
                return $(this).val();
            }).get().join(',');
            if (productIds == '') {
                showtips('请先选择', 'alert-warning');
                return false;
            }
            $.ajax({
                url: '<?php echo admin_base_url("ebay/ebayPublish/batchPublish");?>',
                data: 'productIds=' + productIds,
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function () {
                    showtips('刊登中，请稍等', 'alert-success');
                },
                success: function (data) {
                    showtips(data.info, 'alert-success');
                    window.location.reload();
                }
            });
        }
    });

    $(document).on('click', '#batch_delete', function(e){
        if(confirm("确定要删除吗?"))
        {
        var productIds = $('input[name="ids[]"]:checked').map(function() {
            return $(this).val();
        }).get().join(',');
        if (productIds == ''){
            showtips('请先选择', 'alert-warning');
            return false;
        }
        $.ajax({
            url: '<?php echo admin_base_url("ebay/ebaylist/batchdelete");?>',
            data: 'productIds='+productIds,
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function(){
                showtips('批量删除中', 'alert-success');
            },
            success: function(data){
                showtips(data.info,'alert-success');
                window.location.reload();
            }
        });
        }
    });
   /* $(document).on('click','#copylistinfo',function(e))
    {
        $('#ModalSelect').modal('toggle');
    }*/
        $('#copylistinfo').click(function(){
            var productIds = $('input[name="ids[]"]:checked').map(function () {
                return $(this).val();
            }).get().join(',');
            if (productIds == '') {
                showtips('请先选择', 'alert-warning');
                return false;
            }
            $('#mubanid').val(productIds);
            $('#ModalSelect').modal('toggle');
        })
    $('#pricecheck').click(function(){

        var accountIds = $('input[name="ebayaccount[]"]:checked').map(function () {
            return $(this).val();
        }).get().join(',');
        if (accountIds == '') {
            showtips('请先选择', 'alert-warning');
            return false;
        }
        var ids =  $('#mubanid').val();
        ii = layer.load('生成中');
        $.ajax({
            url: '<?php echo admin_base_url("ebay/ebay_product/copyEbayInfo");?>',
            data: 'accountIds=' + accountIds+'&ids='+ids,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                layer.close(ii);

                $('#ModalSelect').modal('toggle');
                window.location.reload();
            }
        });




    })


    $(document).on('click', '#batch_down', function(e){
        if(confirm("确定要下架吗?")) {
            var productIds = $('input[name="ids[]"]:checked').map(function () {
                return $(this).val();
            }).get().join(',');
            if (productIds == '') {
                showtips('请先选择', 'alert-warning');
                return false;
            }
            $.ajax({
                url: '<?php echo admin_base_url("ebay/ebayPublish/batchDown");?>',
                data: 'productIds=' + productIds,
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function () {
                    showtips('批量下架中', 'alert-success');
                },
                success: function (data) {
                    showtips(data.info, 'alert-success');
                    window.location.reload();
                }
            });
        }
    });


    $("#tbody_content td").click(function () {

        var tdSeq = $(this).parent().find("td").index($(this)); //第列
        var trSeq = $(this).parent().parent().find("tr").index($(this).parent());//第几行
        if(tdSeq==7)
        {
            if($('#modifyKeyWord').length>0)
            {
                return false;
            }


            var textval = $(this).text();
            var item = $(this).parent().children().eq(1).text();


            $(this).text('');
            $(this).append('<textarea id="modifyKeyWord"  class="reporttextarea" />')


            var _width = $('.reporttextarea').parent().width();
            var _height = $('.reporttextarea').parent().height();

            $('.reporttextarea').css({'width':_width,"height":_height});
            $('#modifyKeyWord').val(textval);


            $('#modifyKeyWord').blur(function() {
                if (confirm('确认保存吗？')) {
                    var textnewval = $('#modifyKeyWord').val();
                    ii = layer.load('提交中');

                    $.ajax({
                        url: '<?php echo admin_base_url("ebay/ebay_product/modifyTitle");?>',
                        data: 'item='+item+'&textnewval='+textnewval,
                        type: 'POST',
                        dataType: 'JSON',
                        success: function(data){
                            layer.close(ii);
                            if(data.status==1)
                            {
                                $('#modifyKeyWord').parent().text(textnewval);
                                $('#modifyKeyWord').remove();

                            }

                            if(data.status==2)
                            {
                                alert('修改失败');
                            }
                        }
                    })
                }
                else
                {
                    $('#modifyKeyWord').parent().text(textval);
                    $('#modifyKeyWord').remove();
                }
            })

        }


     //   alert(tdSeq+'__'+trSeq);
    })
    
</script>