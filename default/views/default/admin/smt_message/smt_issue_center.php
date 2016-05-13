<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-09
 * Time: 13:24
 */
?>
<style>
    .tishi {
        cursor: pointer;
    }

    .yeanse {
        background-color: #00ffff;
        cursor: pointer;
    }
</style>

<div class="row" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">信息管理-SMT纠纷中心</h3>

        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                账号:
                                <select name="search[token_id]" id="token_id">
                                    <option value="">---全选---</option>
                                    <?php
                                    foreach ($token as $t):
                                        echo '<option value="' . $t['token_id'] . '" ' . ($search['token_id'] == $t['token_id'] ? 'selected="selected"' : '') . '>' . $t['accountSuffix'] . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>

                            <label>
                                纠纷状态:
                                <select name="search[issue_status]" id="issue_status">
                                    <option value="">--全部--</option>
                                    <?php
                                    foreach ($issue_status as $key => $status) {
                                        echo '<option value="' . $key . '" ' . ($search['issue_status'] == $key ? 'selected="selected"' : '') . '>' . $status . '</option>';
                                    }


                                    ?>

                                </select>
                            </label>


                            <label>
                                纠纷原因:
                                <select name="search[issue_reason_cn]" id="issue_reason_cn">
                                    <option value="">--全部--</option>
                                    <?php
                                    foreach ($reason as $re) {
                                        echo '<option value="' . $re . '" ' . ($search['issue_reason_cn'] == $re ? 'selected="selected"' : '') . '>' . $re . '</option>';
                                    }


                                    ?>

                                </select>
                            </label>


                            <!--  <label>
                                发件人 :
                                <input type="text" name="search[otherName]" placeholder="请输入发件人"  value="<?php /*    if(isset($search['otherName'])&&!empty($search['otherName']))  echo $search['otherName'];  */ ?>" >
                            </label>-->
                            <label>
                                SMT订单号:
                                <input type="text" name="search[order_id]" placeholder="请输入发件人SMT订单号"
                                       value="<?php if (isset($search['order_id']) && !empty($search['order_id'])) echo $search['order_id']; ?>">
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="search[num]" checked
                                       id="inlineRadio1" <?php if (isset($search['num']) && $search['num'] == 0) echo 'checked' ?>
                                       value="0"> 单纠纷
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="search[num]"
                                       id="inlineRadio2"    <?php if (isset($search['num']) && ($search['num'] == 1)) echo 'checked' ?>
                                       value="1"> 多纠纷
                            </label>
                            <br/>

                            <label class="radio-inline">
                                <input type="radio" name="search[orderby]" checked
                                       id="inlineRadio3" <?php if (isset($search['orderby']) && $search['orderby'] == 'asc') echo 'checked' ?>
                                       value="asc"> 响应时间由少到多
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="search[orderby]"
                                       id="inlineRadio4"    <?php if (isset($search['orderby']) && ($search['orderby'] == 'desc')) echo 'checked' ?>
                                       value="desc"> 响应时间由多到少
                            </label>


                            <label class="radio-inline">
                                <input type="radio" name="search[orderby]"
                                       id="inlineRadio4"    <?php if (isset($search['orderby']) && ($search['orderby'] == 'descstart')) echo 'checked' ?>
                                       value="descstart"> 升级仲裁时间由少到多
                            </label>

                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <label>
                                单页显示
                                <input size="3" type="number" name="search[curpage]" min="1" max="50" placeholder="显示条数"
                                       value="<?php if (isset($search['curpage']) && !empty($search['curpage'])) echo $search['curpage']; ?>">
                                条数
                            </label>


                            <label class="" style="float:right;">
                                <button class="btn btn-primary btn-sm " type="submit">筛选</button>

                                &nbsp;&nbsp; &nbsp;&nbsp;

                                <a class="btn btn-primary btn-sm"
                                   href="<?php echo admin_base_url("smt_message/smt_issue_center/issue_center"); ?>">重置</a>
                            </label>

                        </form>
                        <!--    <label>
                                <a class="btn btn-primary btn-sm"  >批量打开</a>
                            </label>-->
                        <br/>
                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            批量操作 <span class="caret"></span>

                        </button>

                        <ul id="batchdo" class="dropdown-menu">
                            <li value="2"><a style="cursor: pointer">批量拒绝</a></li>
                            <li value="3"><a style="cursor: pointer">批量标记平台处理</a></li>
                            <li value="4"><a style="cursor: pointer">批量打开延迟发货界面</a></li>
                            <!--<li value="5"><a style="cursor: pointer">批量同意</a></li>-->
                            <li value="6"><a style="cursor: pointer">批量处理界面</a></li>
                        </ul>


                        <a id="export" class="btn btn-primary btn-sm" style="cursor: pointer">导入纠纷</a>

                    </div>
                </div>

                <table class="table    dataTable " id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="10%"/>
                        <col width="15%"/>
                        <col width="5%">
                        <col width="15%">
                        <col width="6%">
                        <col width="6%">
                        <col width="10%">
                        <col width="15%">
                        <col width="18%"/>
                        <!--     <col width="8%"/>-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace"/>
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th class="center">订单号:</th>
                        <th class="center">ERP信息</th>
                        <th class="center">账号</th>
                        <th class="center">纠纷开始时间</th>
                        <th class="center">金额总计</th>
                        <th class="center">退款金额</th>
                        <th class="center">订单状态</th>
                        <th class="center">纠纷原因</th>
                        <th class="center">操作</th>
                        <!-- <th class="center" >负责人</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($data):
                        foreach ($data as $list):
                            if ($list->issue_status == 'wait_seller_confirm_refund') {
                                $refuese = false;
                                $num = 0;
                                $detail = unserialize($list->issueProcessDTOs_detail);
                                foreach ($detail as $de) {
                                    if ($de['submitMemberType'] == 'buyer') {
                                        $num++;
                                    }

                                    if ($de['submitMemberType'] == 'seller') {
                                        $refuese = true;
                                    }
                                }
                                if ($num >= 2 && $refuese) {


                                    echo '  <tr class=" table" style="background-color: #00ffff">';
                                } else {

                                    echo '  <tr class="tishi table">';
                                }
                            } else {
                                echo '  <tr class="tishi table">';
                            }

                            ?>


                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $list->id;?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td class="center">
                                    <?php
                            echo '<a target="_blank" title="订单信息"  href="http://trade.aliexpress.com/issue/issueDetail.htm?issueId=' . $list->issue_id . ' ">' . $list->order_id . '</a>
                                        <br/> <a target="_blank" href="' . $list->snapshotUrl . '">产品信息</a><br/>';

                            if ($list->num > 0) {
                                echo '<span class="red"> ' . $list->num . '</span>';
                            }

                            ?>
                                </td>
                                <td class="center "> <?php
                            $orders_data = objectToArray($list->orders_data);
                            if (!empty($orders_data)) {
                                foreach ($orders_data as $order) {

                                    if (!empty($order['orders_shipping_time'])) {

                                        if (time() - strtotime($order['orders_shipping_time']) > 80 * 24 * 60 * 60) {
                                            echo '<span title="超过80天" style="font-size: 10px;color: #feffde;background-color: #0066FF " >' . $order['orders_shipping_time'] . '</span><br/>';

                                        } else {
                                            echo '<span title="ERP发货时间" style="font-size: 10px;color: #00CC00" >' . $order['orders_shipping_time'] . '</span><br/>';
                                        }


                                        if (strstr($shipment[$order['shipmentAutoMatched']], "平邮")) {
                                            echo '<span title="ERP物流渠道" style="font-size: 10px;" >' . $shipment[$order['shipmentAutoMatched']] . '</span><br/>';

                                        } else {
                                            echo '<span title="改物流渠道为专线" style="font-size: 10px;color: #d2322d;" >' . $shipment[$order['shipmentAutoMatched']] . '</span><br/>';

                                        }

                                    } else {
                                        echo '<span title="未在ERP找到发货时间" style="font-size: 10px;color: #d2322d;" >无发货时间</span><br/>';
                                    }


                                }
                                unset($order);
                            } else {
                                echo '<span title="特别提示"  style="font-size: 10px;color: #CC0000;cursor: pointer">订单信息不存在</span>';
                            }
                            ?> </td>
                                <td class="center" title="账号简称"><?php echo $token_account[$list->token_id]['accountSuffix']; ?></td>

                                <td class="center" ><?php echo '<span >' . $list->issue_creat_time . '</span>';?>
                                    <br/>
                                    <span class="red"
                                          title="剩余响应时间"><?php if ($list->issue_status == 'wait_seller_confirm_refund') {
                            echo count_date_to_date($list->issue_update_time, 5);
                        } elseif ($list->issue_status == 'seller_refuse_refund') {
                            $detail = unserialize($list->issueProcessDTOs_detail);
                            $is_many = false;
                            foreach ($detail as $key=> $de) {

                                if($de['actionType']=='cancel'){
                                    $is_many = true;
                                 //   var_dump($detail[$key-1]);
                                    $str = mb_substr($detail[$key-1]['gmtModified'], 0, 14);
                                  //  $true_time = $detail[$key-1][''];
                                    break;

                                }
                            }
                            if($is_many){
                              //  echo  date('Y-m-d H:i:s', strtotime($str));
                                echo count_date_to_date(date('Y-m-d H:i:s', strtotime($str)), 15);
                            }else{
                                echo count_date_to_date($list->issue_creat_time, 15);
                            }

                        } else {
                            echo '未计算剩余响应时间';
                        } ?></span>
                                </td>
                                <td class="center "   <?php  if($list->order_price>15){ echo 'style="color: red;font-weight: bold "';}   ?>  >
                                    <?php echo $list->order_price . '  ' . $list->order_currency ?>

                                </td>
                                <td class="center " >
                                <?php
                                $detail = unserialize($list->issueProcessDTOs_detail);
                                if ($detail):
                                foreach ($detail as $de):
                                    if($de['submitMemberType']=='buyer'){
                                        echo isset($de['issueRefundSuggestionList'][0]['issueMoneyPost']['currencyCode']) ? '<span>' . $de['issueRefundSuggestionList'][0]['issueMoneyPost']['amount'] . '</span><br/><span >' . $de['issueRefundSuggestionList'][0]['issueMoneyPost']['currencyCode'] . '</span>' : '';
                                         break;
                                    }
                                endforeach;
                                endif;
                            ?></td>
                                <td class="center" style="font-size: 12px;">
                                    <?php echo $issue_status[$list->issue_status]; ?>
                                </td>

                                <td class="center" style="font-size: 12px;">
                                    <?php echo $list->issue_reason_cn; ?>
                                </td>

                                <td class="center">
                                <?php

                            echo ' <button title="点击查看纠纷详情" class="btn zhankai btn-primary btn-sm">展开 </button>';

                            if ($list->issue_status == 'wait_seller_confirm_refund') {
                                echo ' <button class="btn agree btn-primary btn-sm">同意 </button>';
                            }
                            if ($list->issue_status == 'seller_refuse_refund') {
                                echo ' <button title="接受买家方案" class="btn agree btn-primary btn-sm">接受 </button>';
                            }


                            ?>

                               </td>
                            </tr>

                        <tr class="hidden" style="background-color: #f8c600">
                            <td colspan="10">
                            <table class="table    dataTable " id="small">

                             <colgroup>
                        <col width="10%">
                        <col width="10%"/>
                        <col width="10%"/>
                        <col width="10%">
                        <col width="15%">
                        <col width="5%">
                        <col width="20%">
                        <col width="20%">
                             </colgroup>
                            <thead>
                            <tr class="center">
                            <th class="center">发起方</th>
                            <th class="center">是否收到货</th>
                            <th class="center">是否退货</th>
                            <th class="center">退款金额</th>
                            <th class="center">日期</th>
                            <th class="center">操作</th>
                            <th class="center">原因</th>
                            <th class="center">附件</th>
                            </tr>
                            </thead>
                            <?php
                            $detail = unserialize($list->issueProcessDTOs_detail);
                            if ($detail):
                                foreach ($detail as $de):
                                    ?>
                                    <tr style="background-color: #f8c600">
                                        <td class="tishi center">
                                            <?php echo $de['submitMemberType']; ?>
                                        </td>
                                        <td class="tishi center">
                                            <!--  --><?php /* var_dump($de); */
                                            ?>
                                            <?php echo isset($de['isReceivedGoods']) ? $de['isReceivedGoods'] : ''; ?>

                                        </td>
                                        <td class="tishi center">
                                            <?php echo (isset($de['issueRefundSuggestionList'][0]['issueReturnGoods']) && $de['issueRefundSuggestionList'][0]['issueReturnGoods']) ? '是' : '否'; ?>
                                        </td>
                                        <td class="tishi center">

                                            <?php echo isset($de['issueRefundSuggestionList'][0]['issueMoneyPost']['currencyCode']) ? '<span>' . $de['issueRefundSuggestionList'][0]['issueMoneyPost']['currencyCode'] . '</span>  <span >' . $de['issueRefundSuggestionList'][0]['issueMoneyPost']['amount'] . '</span>' : ''; ?>


                                        </td>

                                        <td class="tishi center">

                                            <?php

                                            $str = mb_substr($de['gmtModified'], 0, 14);
                                            echo date('Y-m-d H:i:s', strtotime($str));
                                            ?>
                                        </td>

                                        <td class="tishi center">
                                            <?php
                                            echo $de['actionType'];
                                            ?>

                                        </td>

                                        <td class="tishi center">

                                            <?php
                                            echo isset($de['content']) ? $de['content'] : "";
                                            ?>

                                        </td>


                                        <td class="tishi center">
                                            <?php
                                            if (isset($de['attachments'])) {
                                                foreach ($de['attachments'] as $pictrue) {
                                                    echo '<img  onclick=window.open("'.$pictrue.'")  width=150 height=150 src="' . $pictrue . '"   />';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                endforeach;
                            endif;
                            ?>
                            </table>
                            </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    </tbody>
                </table>

                <?php
                $this->load->view('admin/common/page_number');
                ?>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js') ?>"></script>
<script src="<?php echo static_url('theme/common/layer/layer.js') ?>"></script>
<script type="text/javascript">
    $(function () {
        $(document).on('click', '.Wdate', function () {
            var o = $(this);
            if (o.attr('dateFmt') != '')
                WdatePicker({dateFmt: o.attr('dateFmt')});
            else if (o.hasClass('month'))
                WdatePicker({dateFmt: 'yyyy-MM'});
            else if (o.hasClass('year'))
                WdatePicker({dateFmt: 'yyyy'});
            else
                WdatePicker({dateFmt: 'yyyy-MM-dd'});
        });
    });

    $('#batchdo li').click(function () {
        var ids = $('input[name="ids[]"]:checked').map(function () {
            return $(this).val();
        }).get().join(',');
        if (ids == '') {
            alert('请先选择数据');
            return false;
        }
        var index = $(this).val();
     //   alert(index);
        if (index == 2) {

            $.layer({
                type: 2,
                title: ['修改纠纷内容', true],
                shade: [0.8, '', true],
                iframe: {src: '<?php echo admin_base_url("smt_message/smt_issue_center/issue_refuse"); ?>'},
                area: ['800px', '500px'],
                success: function () {
                    layer.shift('top', 340)
                },
                btns: 2,
                btn: ['确定', '取消'],

                yes: function (index) { //确定按钮的操作

                    var type = layer.getChildFrame('input[name="type"]:checked').val();

                    var content = layer.getChildFrame('#content').val();

                    //$("#id").is(":checked")
                    var automsg = '';
                    var onlymsg = '';

                    if (layer.getChildFrame('#onlymsg').is(":checked")) {
                        onlymsg = 2;
                    } else {
                        onlymsg = 1;
                    }

                    if (layer.getChildFrame('#automsg').is(":checked")) {
                        automsg = 2;
                    } else {
                        automsg = 1;
                    }
                    if (content == '') {
                        layer.alert('原因不能为空');
                        return false;
                    }
                    if ((content.length) > 510 || content.length < 4) {
                        alert('请输入4-512个英文字符');
                        return false;
                    }

                    if (type == 1 || type == 2) {
                        layer.alert('暂不支持该操作');
                        return false;
                    }
                    layer.close(index);

                    if (onlymsg == 1) {
                        var caozuo = layer.load('操作中');
                        $.ajax({
                            url: '<?php echo admin_base_url("smt_message/smt_issue_center/refuse_issue_smt");?>',
                            data: 'ids=' + ids + '&type=' + type + '&content=' + content + '&automsg=' + automsg,
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (data) {
                                layer.close(caozuo);
                                layer.alert(data.info, 2);
                                //   window.location.reload();
                            }
                        });
                    }

                    if (onlymsg == 2) {
                        var caozuo = layer.load('发送订单留言中');

                        $.ajax({
                            url: '<?php echo admin_base_url("smt_message/smt_issue_center/batch_leave_message");?>',
                            data: 'ids=' + ids + '&content=' + content,
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (data) {
                                layer.close(caozuo);
                                layer.alert(data.info, 2);
                                //   window.location.reload();
                            }
                        });
                    }
                },
                no: function (index) {
                    layer.close(index);
                }
            });


        }

        if (index == 3) {
            layer.confirm('确认标记为平台处理？', function () {
                $.ajax({
                    url: '<?php echo admin_base_url("smt_message/smt_issue_center/setIssueNotNew");?>',
                    data: 'ids=' + ids,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        layer.alert(data.info, 2);
                        $('input[name="ids[]"]:checked').each(function () {
                            $(this).parent().parent().parent().next().remove();
                            $(this).parent().parent().parent().remove();

                        })

                    }
                });


            });
        }


        if (index == 4) {

            if (confirm('确认批量打开延迟发货页面？')) {
                $('input[name="ids[]"]:checked').map(function () {
                    var orderId = $(this).parent().parent().next().children().eq(0).text();
                    //   alert(orderId);
                    var url = "http://trade.aliexpress.com/orderDetail.htm?orderId=" + orderId
                    window.open(url);
                });
            }
        }


        if(index ==5){
            if (confirm('确认批量同意？')) {
                var caozuo = layer.load('批量同意中');
                $.ajax({
                    url: '<?php echo admin_base_url("smt_message/smt_issue_center/agree_issue_smt");?>',
                    data: 'action=batch&ids=' + ids,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        var result ='';
                        for(var i=0;i<data.data.length;i++){
                            result =result+data.data[i];
                        }
                        layer.close(caozuo);
                        layer.alert(result, 2);
                    }
                });
            };
        }
        if (index == 6) {

            if (confirm('确认批量打开处理页面？')) {
                $('input[name="ids[]"]:checked').map(function () {
                    var url = $(this).parent().parent().next().children().eq(0).attr("href");

                   // var url = "http://trade.aliexpress.com/orderDetail.htm?orderId=" + orderId
                    window.open(url);
                });
            }
        }
    });

    $("#export").click(function () {
        var token_id = $('#token_id').val();
        var account = $('#token_id').find("option:selected").text();
        if (token_id == '') {
            layer.alert('请选择账号', 2);
        } else {
            layer.confirm('确认导入' + account + '账号的纠纷？', function () {
                var jiazai = layer.load('同步纠纷中');
                $.ajax({
                    url: '<?php echo admin_base_url("auto/auto_smt_exportIssue/autoExportIssue");?>',
                    data: 'token_id=' + token_id + '&type=export',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        layer.close(jiazai);
                        layer.alert(data.info, 2);
                    }
                });
            });

        }
    });
    $('.agree').click(function () {
        var mark = $(this).parent().parent();
        var id = $(this).parent().parent().children().eq(0).children().eq(0).children().eq(0).val();
        if (confirm('确认同意？')) {
            var jiazai = layer.load('同意中');
            $.ajax({
                url: '<?php echo admin_base_url("smt_message/smt_issue_center/agree_issue_smt");?>',
                data: 'ids=' + id,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    layer.alert(data.data['string']);

                    if (data.data['is_success'] == 1) {
                        $(mark).remove();
                        $(mark).next().remove();
                    }
                    layer.close(jiazai);
                }
            });
        }
    });
    $('.zhankai').click(function () {
        if ($(this).parent().parent().next().hasClass('hidden')) {
            $(this).parent().parent().next().removeClass('hidden');
        } else {
            $(this).parent().parent().next().addClass('hidden');
        }
    });

    /*$("#tbody_content tr").bind("click", function () {
     if ($(this).hasClass("table")) {
     if ($(this).next().hasClass('hidden')) {
     $(this).next().removeClass('hidden');
     } else {
     $(this).next().addClass('hidden');
     }
     }

     });*/

</script>
