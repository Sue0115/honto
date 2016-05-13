<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-05
 * Time: 16:06
 */


?>

<div class="row" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">信息管理-SMT信息中心</h3>
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
                                    foreach($token as $t):
                                        echo '<option value="'.$t['token_id'].'" '.($search['token_id'] == $t['token_id'] ? 'selected="selected"': '').'>'.$t['token_id'].'-'.$t['accountSuffix'].'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>

                            <label>
                                类型 :
                                <select name="search[messageType]" id="messageType">
                                    <option value="order_msg"  <?php     if(isset($search['messageType'])&&($search['messageType']=='order_msg'))  echo 'selected';  ?> >订单留言</option>
                                    <option value="message_center" <?php     if(isset($search['messageType'])&&($search['messageType']=='message_center'))  echo 'selected';  ?>>站内信</option>
                                </select>
                            </label>


                            <label>
                                发件人 :
                                <input type="text" name="search[otherName]" placeholder="请输入发件人"  value="<?php     if(isset($search['otherName'])&&!empty($search['otherName']))  echo $search['otherName'];  ?>" >
                            </label>
                            <label>
                                SMT订单号:
                                <input type="text" name="search[channelId]" placeholder="请输入发件人SMT订单号"  value="<?php     if(isset($search['channelId'])&&!empty($search['channelId']))  echo $search['channelId'];  ?>" >
                            </label>


                          <!--  <label>
                                <select name="search[kefu]" id="kefu">
                                    <option value="order_msg">客服，点击选择</option>
                                    <option value="message_center">客服A</option>
                                    <option value="message_center">客服B</option>
                                    <option value="message_center">客服C</option>
                                    <option value="message_center">客服D</option>
                                    <option value="message_center">客服E</option>
                                </select>
                            </label>-->

                            <label>
                                <input type="text"  placeholder="发件开始时间"  datefmt="yyyy-MM-dd" class="Wdate" id="start_date"  value="<?php     if(isset($search['import_start'])&&!empty($search['import_start']))  echo $search['import_start'];  ?>"  name="search[import_start]" size="10"/>
                                ~
                                <input type="text" placeholder="发件结束时间" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="search[import_end]"    value="<?php     if(isset($search['import_end'])&&!empty($search['import_end']))  echo $search['import_end'];  ?>"   size="10"/>

                            </label>



                            <label>
                                <input type="text"  placeholder="导入开始时间"  datefmt="yyyy-MM-dd" class="Wdate" id="start_export"  value="<?php     if(isset($search['start_export'])&&!empty($search['start_export']))  echo $search['start_export'];  ?>"  name="search[start_export]" size="10"/>
                                ~
                                <input type="text" placeholder="导入结束时间" datefmt="yyyy-MM-dd" class="Wdate" id="end_export" name="search[end_export]"    value="<?php     if(isset($search['end_export'])&&!empty($search['end_export']))  echo $search['end_export'];  ?>"   size="10"/>

                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="search[chuli]" id="inlineRadio1"   <?php if(isset($search['chuli'])&&$search['chuli']==1)  echo 'checked' ?> value="1">  已处理
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="search[chuli]" id="inlineRadio2"  <?php if(isset($search['chuli'])&&($search['chuli']==0))  echo 'checked' ?>  value="0"> 未处理
                            </label>

                            <label class="radio-inline">
                                <input type="radio" name="search[isread]" id="inlineRadio1" <?php if(isset($search['isread'])&&$search['isread']==1)  echo 'checked' ?> value="1"> 已读
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="search[isread]" id="inlineRadio2"    <?php if(isset($search['isread'])&&($search['isread']==0))  echo 'checked' ?>  value="0"> 未读
                            </label>

                            &nbsp;&nbsp;

                            <label class="checkbox-inline">
                                <input type="checkbox" name="search[reply_no]"   <?php if(isset($search['reply_no'])&&($search['reply_no']==1))  echo 'checked' ?>   value="1"> 不必回
                            </label>

                            &nbsp;&nbsp;    &nbsp;&nbsp;    &nbsp;&nbsp;    &nbsp;&nbsp;    &nbsp;&nbsp;
                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">筛选</button>
                            </label>
                            &nbsp;&nbsp;    &nbsp;&nbsp;
                            <label>
                                <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url("smt_message/smt_message_center/message_center");  ?>" >重置</a>
                            </label>










                        </form>
                    <!--    <label>
                            <a class="btn btn-primary btn-sm"  >批量打开</a>
                        </label>-->

                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                批量操作 <span class="caret"></span>

                            </button>

                            <ul  id="batchdo"  class="dropdown-menu">
                                <li value="2" ><a style="cursor: pointer">批量展开</a></li>
                                <li value="3" ><a style="cursor: pointer">批量标记为已读</a></li>
                                <li value="4"><a style="cursor: pointer">批量标记为已处理</a></li>
                                <li value="5" ><a style="cursor: pointer">批量标记为不必回</a></li>
                            </ul>
                        &nbsp;&nbsp;    &nbsp;&nbsp;&nbsp;&nbsp;    &nbsp;&nbsp;

                    </div>
                </div>

                <table  class="table   table-hover dataTable " id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="10%"/>
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="7%">
                        <col width="25%">
                        <col width="18%"/>
                   <!--     <col width="8%"/>-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th class="center">状态</th>
                        <th class="center" >发件人</th>
                        <th class="center" >收件人</th>
                        <th class="center" >类型</th>
                        <th class="center" >订单号</th>
                        <th class="center" >主题</th>
                        <th class="center" >发件时间</th>
                       <!-- <th class="center" >负责人</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($data):
                        foreach($data as $list):
                            ?>
                            <tr  <?php  if($list->isRead == 0){ echo 'class="success "'; } ?> >
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $list->id;?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td class="center">
                                    <?php
                                    if($list->isRead == 0) //未读
                                    {
                                       // echo '   <i class="glyphicon glyphicon-eye-close red"></i> &nbsp;&nbsp;';
                                        echo '  <span  class="glyphicon glyphicon-eye-close red large"></span>  &nbsp;&nbsp;';
                                    }
                                    else
                                    {
                                        echo  ' <span    class="glyphicon glyphicon-eye-open"></span>  &nbsp;&nbsp;';
                                    }
                                    if($list->isReturn ==1)
                                    {
                                        echo  '<span  class=" glyphicon glyphicon-ok-sign"></span>  &nbsp;&nbsp;';
                                    }

                                    if($list->reply_no ==1)
                                    {
                                        echo  '<span  class=" glyphicon glyphicon-remove-sign"></span>  &nbsp;&nbsp;';
                                    }

                                    ?>
                                </td>
                                <td class="center red" > <?php echo $list->otherName; ?><span ><?php echo '('.$list->unreadCount.')' ; ?></span> </td>
                                <td class="center" ><?php echo $token_account[$list->token_id]['accountSuffix']; ?></td>

                                <td class="center" ><?php

                                    if($list->messageType=='message_center')
                                    {
                                        echo '站内信';
                                    }
                                    else
                                    {
                                        echo '订单留言';
                                        }

                                    ?></td>
                                <td class="center" >
                                    <?php
                                    if($list->messageType=='order_msg')
                                    {
                                        echo ' <a target="_blank" href="http://trade.alibaba.com/order_detail.htm?orderId=' . $list->channelId . '">' . $list->channelId . '</a>';
                                    }
                                    ?>

                                </td>
                                <td lang="<?php echo  admin_base_url('smt_message/smt_message_center/show_detail?id='.$list->id); ?> " ><?php


                                    echo '<a href="'.admin_base_url('smt_message/smt_message_center/show_detail?id='.$list->id).'"  target="_blank" >'.  mb_substr(str_replace("&nbsp;", '', $list->lastMessageContent), 0, 40) .'...'. '</a>';

                                    ?></td>
                                <td class="center" ><?php echo $list->messageTime; ?></td>
                              <!--  <td class="center" ><?php /*echo $list->erp_user_id; */?></td>-->
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

<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>
<script type="text/javascript">
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
    });


    $(function(){
        $("#tbody_content td").click(function () {
            var tdSeq = $(this).parent().find("td").index($(this));

          if(tdSeq==6)
          {
              $(this).parent().removeClass("success");
              $(this).parent().children().eq(1).children().eq(0).removeClass("glyphicon glyphicon-eye-close red smaller-200");
              $(this).parent().children().eq(1).children().eq(0).addClass("glyphicon glyphicon-eye-open");
           // alert(tt);
          }
        })
    });

    $('#batchdo li').click(function(){

        var ids = $('input[name="ids[]"]:checked').map(function() {
            return $(this).val();
        }).get().join(',');
        if (ids == ''){
            alert('请先选择数据');
            return false;
        }
        var index = $(this).val();
        if(index==2)
        {
            if(confirm("是否确定批量打开"))
            {
                var host = window.location.host;
              //  var para = window.location.pathname;

                var para ='admin/smt_message/smt_message_center/show_detail';

                var idarr = ids.split(',');

                for(var i=0;i<idarr.length;i++)
                {
                    window.open('show_detail?id='+idarr[i]);
                }
                return false;
            }

        }

        if(index==3)
        {
            if(confirm("是否确定批量标记为已读"))
            {
                $.ajax({
                    url: '<?php echo admin_base_url("smt_message/smt_message_center/batchUpdateMsgRead");?>',
                    data: 'ids=' + ids+'&type=3',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        alert(data.info);
                    }
                });


            }
        }


        if(index==4)
        {
            if(confirm("是否确定批量标记为已处理(同时已处理)"))
            {
                $.ajax({
                    url: '<?php echo admin_base_url("smt_message/smt_message_center/batchUpdateMsgRead");?>',
                    data: 'ids=' + ids+'&type=4',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        alert(data.info);
                    }
                });
            }
        }

        if(index==5)
        {
            if(confirm("是否确定批量不必回(同时已读和已处理)"))
            {
                $.ajax({
                    url: '<?php echo admin_base_url("smt_message/smt_message_center/batchUpdateMsgRead");?>',
                    data: 'ids=' + ids+'&type=5',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        alert(data.info);
                    }
                });
            }
        }

    });
</script>
