<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-06
 * Time: 14:48
 */
?>

<style>
    .hideaccordion, .showaccordion {

        height: 50px;
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
</style>

<div class="container-fluid " >
    <div><span>当前位置：SMT信息管理</span></div>
    <div class="row-fluid">


        <div class="col-sm-9">
            <table class="table table-bordered " id="detail_table">
                <?php
                foreach ($data as $v) {
                    ?>
                    <tr  bgcolor="#f6f6f6" <?php
                    $re_info = json_decode($v['summary'],true);
                    if($otherName == $re_info['receiverName'])
                    {
                        echo 'style="background-color:#eff999;border-bottom:1px solid #CCCCCC;"';
                    }
                    ?> >

                        <td  style="font-size:10pt; font-family:arial, sans-serif; color:#000" class="col-sm-3 text-right">
                            <input type="text" value="<?php echo $v['datail_id'] ?> " class="hidden"/>
                            <span> <?php echo $v['senderName']; ?></span>

                            <p><?php echo $v['gmtCreate']; ?></p>
                            <?php
                            if (!empty($v['reply_id'])&&(isset($user_info[$v['reply_id']]))) {
                                echo '<span class="header-color-red">' . $user_info[$v['reply_id']] . ' 回复了该信息，见下文 </span>';
                            }
                            ?>
                            </p>
                        </td>

                        <td  style="font-size:10pt; font-family:arial, sans-serif; color:#000" class="col-sm-6 " >

                            <p style="word-break: break-all; word-wrap:break-word;" ><?php
                                $content = $v['content'];
                                $content = str_replace("&nbsp;", ' ', $content);
                                $content = str_replace("&amp;nbsp;", ' ', $content);
                                $content = str_replace("&amp;iquest;", ' ', $content);
                                $content = str_replace("\n", "<br />", $content);
                                $content = preg_replace("'<br \/>[\t]*?<br \/>'", '', $content);

                                $content = str_replace("/:000", '<img src="http://i02.i.aliimg.com/wimg/feedback/emotions/0.gif" />', $content);

                                $content = preg_replace("'\/\:0+([1-9]+0*)'", "<img src='http://i02.i.aliimg.com/wimg/feedback/emotions/\\1.gif' />", $content);
                                $content = (stripslashes(stripslashes($content)));
                                echo $content;
                                ?></p>

                            <p>
                                <?php
                                if (!empty($v['filePath'])) {
                                    $pic = '';
                                    $pic = json_decode($v['filePath'], true);

                                    foreach ($pic as $p) {
                                        if (isset($p['mPath'])) {
                                            //<img id="replyImg86804" title="未回复" alt="未回复" src="images/reply_false.gif">
                                            echo '<a href="' . $p['lPath'] . '" target="_blank"><img   src="' . $p['sPath'] . '" /></a>';
                                        }
                                    }

                                }
                                ?>
                            </p>

                            <p>
                                <?php
                                if ($v['messageType'] == 'product') {
                                    $summary = '';
                                    $summary = json_decode($v['summary'], true);
                                    //<a href="http://www.aliexpress.com/item/2015-New-Sexy-Deep-V-Neck-Women-T-shirt-Tops-Zipper-Long-Sleeve-Slim-Hoody-Basic/32274596811.html" target="_blank">点击打开</a>
                                    echo '相关产品：' . '<a href="' . $summary['productDetailUrl'] . '" target="_blank">点击打开</a>';
                                }
                                ?>
                            </p>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
            <form>
                <table class="table ">
                    <tr bgcolor="#f6f6f6">
                        <td class="col-sm-3 text-right ">
                            <span> 回复：</span>
                        </td>
                        <td class="col-sm-7">
                            SMT订单链接:
                            <?php echo ' <a href="http://trade.alibaba.com/order_detail.htm?orderId=' . $channelId . '" target="_blank">' . $channelId . '</a>'; ?>

                            类型:<?php if ($messageType == 'message_center') {
                                echo " &nbsp&nbsp&nbsp; <span class='red'>站内信</span>";
                            } else {
                                echo "<span class='yellow'>订单留言</span>";
                            }
                            ?>
                            <input name="channelId" id="channelId" class="hidden" value="<?php echo $channelId ?>">
                            <input name="messageType" id="messageType" class="hidden"
                                   value="<?php echo $messageType ?>">
                            <input name="list_id" id="list_id" class="hidden" value="<?php echo $list_id ?>">
                            &nbsp&nbsp&nbsp;
                            买家账号:
                            <?php
                            echo $otherLoginId;
                            ?>
                            <input name="otherLoginId" id="otherLoginId" class="hidden"
                                   value="<?php echo $otherLoginId ?>">
                        </td>
                    </tr>
                    <tr bgcolor="#f6f6f6">
                        <td></td>
                        <td><select id="email_mod_class">
                                <option value="">==目录==</option>
                                <?php
                                if (!empty($result_mod)) {
                                    foreach ($result_mod as $mod) {
                                        echo '<option value="' . $mod['modClassID'] . '">' . $mod['modClassName'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <select id="email_mod" style="width:80%;">
                                <option value="">==选择常用模板==</option>
                            </select>
                        </td>
                    </tr>
                    <tr bgcolor="#f6f6f6">
                        <td class="col-sm-3 text-right ">
                            <?php
                            for ($i = 0; $i < 54; $i++) {
                                echo '<img class="aliimg" id="ali_' . str_pad($i, 3, "0", STR_PAD_LEFT) . '" onClick="setImg(this.id)" width="20" src="http://i02.i.aliimg.com/wimg/feedback/emotions/' . $i . '.gif" />&nbsp;';
                                if (($i + 1) % 9 == 0) echo '<br />';
                            }
                            ?>
                        </td>
                        <td class="col-sm-7">
                            <textarea id="reply_content" name="reply_content" style="width: 734px; height: 276px;"></textarea>
                        </td>
                    </tr>
                    <tr bgcolor="#f6f6f6">
                        <td class="col-sm-3"></td>
                        <td class="col-sm-5 text-right"><a id="tijiao" class="btn-primary btn-sm" style="cursor: pointer"> 提交</a></td>
                    </tr>
                </table>
            </form>
        </div>


        <div class="col-sm-3">

            <?php
            if(isset($last_order_info))
            {
                foreach($last_order_info as $o)
                {


                ?>


            <div class="row row-border">
                <div class="proh">
                    <div class="hideaccordion">
                        <h1>
                            <i class="icon-plus"></i>
                            <span>&nbsp;订单信息</span>
                        </h1>
                    </div>
                </div>


                <div class="probody">
                    <div class="procnt">

            <table class="table table-bordered table-condensed " style="font-size: 12px" >
                <tr >
                    <td width="80">内单号</td>
                    <td><a target="_blank" href="http://erp.moonarstore.com/all_orders_manage.php?selectFrom=erp_orders_view&Submit6=%E7%AD%9B%E9%80%89&erp_orders_id=<?php echo  $o['erp_orders_id']?>"><?php   echo $o['erp_orders_id']; ?></a> <a  target="_blank"  href="http://erp.moonarstore.com/moneyBackModify.php?oID=<?php echo  $o['erp_orders_id'] ?>" >申请退款</a></td>
                </tr>
                <tr >
                    <td>买家ID:</td>
                    <td > 	<a href="http://trade.alibaba.com/order_detail.htm?orderId=<?php  echo $o['buyer_id'] ?>" target="_blank"><?php  echo $o['buyer_id'] ?></a></td>
                </tr>
                <tr ><td> 支付时间:</td>
                    <td><?php  echo   $o['orders_paid_time']; ?></td>
                </tr>

                <tr >
                    <td>销售帐号:</td>
                    <td> <?php  echo $o['sales_account'] ?></td>
                </tr>
                <tr >
                    <td>PP凭证:</td>
                    <td> </td>
                </tr>

                <tr >
                    <td>收货人:</td>
                    <td><?php  echo $o['buyer_name'] ?></td>
                </tr>

                <tr >
                    <td>发货地址:</td>
                    <td><?php echo $o['buyer_address_1'].' '.$o['buyer_address_2'].' '.$o['buyer_city'].' '.$o['buyer_state'].' '.$o['buyer_country'].'<br>Zip:'.$o['buyer_zip'].'<br>Phone:'.$o['buyer_phone'] ; ?></td>
                </tr>

                <tr >
                    <td>总金额:</td>
                    <td> <?php echo $o['orders_total'].' '.$o['currency_type'];?></td>
                </tr>
                <tr>
                    <td>运费:</td>
                    <td> <?php echo $o['orders_ship_fee'].' '.$o['currency_type'];?></td>
                </tr>
                <tr>
                    <td>支付时间:</td>
                    <td><?php echo $o['orders_paid_time'];?></td>
                </tr>
                <tr>
                    <td>广告物流:</td>
                    <td><?php echo $o['ShippingServiceSelected']; ?></td>
                </tr>

                <tr>
                    <td>匹配物流:</td>
                    <td><?php echo  isset($shipment_info[$o['shipmentAutoMatched']]['shipmentTitle'])?$shipment_info[$o['shipmentAutoMatched']]['shipmentTitle']:'出现错误联系IT'; ?></td>
                </tr>

                <tr>
                    <td>状态:</td>
                    <td>  <font color="<?php echo $statusArray[$o['orders_status']]['color'];?>"><?php echo $statusArray[$o['orders_status']]['text'];?></font>
                        &nbsp;<?php if ( $o['orders_is_backorder'] == '1' ) { ?><span style="color:red">欠货</span><?php }?>
                        &nbsp;<?php if ( $o['ebayStatusIsMarked'] == '1' ) { ?><span style="color:darkgreen">已标记发货</span><?php }?>
                        &nbsp;<?php if ( $o['issue']){ ?><span style="color:red">纠纷中</span><?php }?>  </td>
                </tr>

                <tr>
                    <?php

                    $orderStatusDescription= '';
                    $_zhuizongma = '';

                    switch ($o['orders_status'])
                    {
                        case 3:
                            $orderStatusDescription = '下载时间:'.$o['orders_export_time'];
                            break;

                        case 4:
                            $orderStatusDescription = '打印时间:'.$o['orders_print_time'];
                            break;

                        case 5:
                          //  $tracking_result = track_info($o['orders_shipping_code']);
                            $orderStatusDescription = '打印时间:'.$o['orders_print_time'];
                            $orderStatusDescription .= '<br />发货时间:'.$o['orders_shipping_time'];
                            $orderStatusDescription .= '<br />追踪码:' .($o['tracking_result'] ? '&nbsp;<a href="http://erp.moonarstore.com/sellertool_api_info_detail.php?code='.$o['orders_shipping_code'].'&carrier1='.$o['tracking_result']['carrier1'].'&carrier2='.$o['tracking_result']['carrier2'].'\',700,400)" title="点击展开关闭详情">'.$o['orders_shipping_code'].'</a>' : '&nbsp;'.$o['orders_shipping_code']);
                            $orderStatusDescription .= '<br />物流查询网址:'.$shipment_info[$o['shipmentAutoMatched']]['shipmentDescription'];
                            $_zhuizongma .= '<div class="track_result">物流追踪结果: <br /><p>'.($o['tracking_result'] ? $o['tracking_result']['description'] : '暂无').'</p></div>';
                            break;

                        default:
                            break;
                    }


                    ?>

                    <td colspan="2"><?php echo $orderStatusDescription?$orderStatusDescription:'';?></td>
                </tr>


                <tr>
                    <td>订单备注:</td>
                    <td><a  target="_blank"  href="http://erp.moonarstore.com/logList.php?operateMod=ordersManage&operateKey=<?php echo  $o['erp_orders_id']; ?>" >日志</a></td>
                </tr>
                <tr>
                    <td>退款信息:</td>
                    <td>
                        <?php
                        if ($o['moneyBackInfo']){//有退款
                            foreach ($o['moneyBackInfo'] as $money){
                                echo '<br/>'.$money['moneyback_currency'].'-'.$money['moneyback_amount'].'-'.$money['moneyback_status'].' '.$money['moneyback_submitTime'];
                            }
                        }


                        ?>

                    </td>
                </tr>

                <tr>
                    <td>买家留言:</td>
                    <td><?php echo $o['notes_to_yourself'];?></td>
                </tr>

                        <?php
                        if(isset($o['product_result']))
                        {
                            foreach($o['product_result'] as $o_p)
                            {
                                echo ' <tr>
                    <td colspan="2"><a  target="_blank"  href="http://erp.moonarstore.com/productsShow.php?pID='.$o_p['products_id'].'" >['.$o_p['orders_sku'].']</a> * '.$o_p['item_count'].' ('.$o['currency_type'].'  '.$o_p['item_price'].') ('.$o_p['products_name_cn'].') </td>
                   </tr>';
                            }
                        }

                        ?>

                <tr>
                    <td colspan="2" bgcolor="#f6f6f6"><!-- --><?php /*echo  $_zhuizongma;*/?>

                    <div>
                        <div><a class="getShipMentInfo btn">物流信息查询</a><span ><?php echo empty($o['orders_shipping_code'])?'':$o['orders_shipping_code']; ?></span></div>
                        <div>
                        </div>

                    </div>

                    </td>
                </tr>

            </table>
                        </div>
                    </div>
        </div>

            <?php
                }
            }
            ?>
    </div>
</div>
    <script src="<?php echo static_url('theme/common/layer/layer.js') ?>"></script>
<script type="text/javascript">


    $(function(){
      var text =　$("#detail_table tr:last").children().eq(1).children().eq(0).text();
        text =changeSome(text,1);
     //   text= text.replace(/"?"/g, "@");
        $.ajax({
            url: '<?php echo admin_base_url("smt_message/smt_message_center/testbaidu");?>',
            data: 'info=' + text,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                    if(data.status==1){
                        var news = changeSome( data.info,2);
                        $("#detail_table tr:last").children().eq(1).children().eq(0).append('<br/>百度翻译<br/>'+news);

                    }
            }
        });

    });
    function changeSome(text,type){
        if(type==1){

            text=text.replace(/\?/g, "^");
        }
        if(type==2){
            text=text.replace(/\^/g, "?");
        }

        return text;
    }
    function setImg(id) {
        var value = jQuery('#reply_content').val();
        jQuery('#reply_content').val(value + " /:" + id.replace('ali_', ''));
    }


    $("#tijiao").click(function () {
        var detail_id = $('#detail_table tr:last').find('td').eq(0).children().eq(0).val();
        var channelId = $('#channelId').val();
        var buyerId = $('#otherLoginId').val();
        var msgSources = $('#messageType').val();
        var content = $('#reply_content').val();
        var list_id = $("#list_id").val();
        $.ajax({
            url: '<?php echo admin_base_url("smt_message/smt_message_center/addMsg");?>',
            data: 'list_id=' + list_id + '&content=' + content + '&detail_id=' + detail_id,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                if (data.status == 1) {
                        window.opener=null;
                        window.open('','_self');
                        window.close();
                }
                if (data.status == 2) {
                    alert(data.info)
                }
            }
        });


    });

    $("#email_mod_class").change(function () {
        var email_mod_class_id = $("#email_mod_class").val();
        if (email_mod_class_id != '') {
            $.ajax({
                url: '<?php echo admin_base_url("smt_message/smt_message_center/getModDetail");?>',
                data: 'email_mod_class_id=' + email_mod_class_id,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 1) {
                        $("#email_mod").empty().append(data.info);
                    }
                }
            });
        }
    });


    $("#email_mod").change(function () {
        var email_mod_class_id = $("#email_mod").val();
        if (email_mod_class_id != '') {
            $.ajax({
                url: '<?php echo admin_base_url("smt_message/smt_message_center/getModDetailInfo");?>',
                data: 'modID=' + email_mod_class_id,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 1) {
                        $("#reply_content").empty().append(data.info);
                    }
                }
            });
        }else{
            $("#reply_content").empty();
        }
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

    $('.getShipMentInfo').click(function(){

        var num  =  $(this).next().text();
        var mid = $(this).next();

        if(num ==''){
            $(this).next().parent().next().empty().append('<span>追踪号错误</span>')
        }else{
            $.layer({
                type: 2,
                title: ['物流信息', true],
                shade: [0.8, '', true],
                iframe: {src: '<?php echo admin_base_url("smt_message/smt_message_center/getShipmentInfo?num="); ?>'+num},
                area: ['800px', '500px'],
                success: function () {
                    layer.shift('top', 340)
                },
                  btns: 1,
                  btn: [ '取消'],
                 no: function (index) {
                 layer.close(index);
                 }
            });
        }
    })

</script>
