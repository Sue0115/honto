<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-13
 * Time: 17:35
 */
?>

<div class="row" >
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">信息管理-SMT统计中心</h3>

        <div class="table-header">&nbsp;</div>
        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">

                            <label>
                                <input type="text" placeholder="导入开始时间" datefmt="yyyy-MM-dd" class="Wdate"
                                       id="start_date"
                                       value="<?php if (isset($search['import_start']) && !empty($search['import_start'])) echo $search['import_start']; ?>"
                                       name="search[import_start]" size="15"/>
                                ~
                                <input type="text" placeholder="导入结束时间" datefmt="yyyy-MM-dd" class="Wdate" id="end_date"
                                       name="search[import_end]"
                                       value="<?php if (isset($search['import_end']) && !empty($search['import_end'])) echo $search['import_end']; ?>"
                                       size="15"/>
                            </label>

                            <label>
                                <a class="btn btn-primary btn-sm" href="#" id="searchday">信件情况</a>
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;


                            <label>
                                <a class="btn btn-primary btn-sm" href="#" id="searchpeople">回复统计</a>
                            </label>


                            &nbsp;&nbsp; &nbsp;&nbsp;

                            <label>
                                <button class="btn btn-primary btn-sm" type="reset">重置</button>
                            </label>
                        </form>
                        <div id="part1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js') ?>"></script>
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
    $("#searchday").click(function () {
        var date1 = $("#start_date").val();
        var date2 = $("#end_date").val();
        if (date1 == '' || date2 == '') {
            alert("选择起始时间");
            return false;
        }

        $.ajax({
            url: '<?php echo admin_base_url("smt_message/smt_message_count/get_message_count");?>',
            data: 'date1=' + date1 + '&date2=' + date2,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                var info = '';
                $("#part1").empty().append("<table id='table1' class='table   table-hover dataTable'><tr> <td class='center'>账号</td> <td class='center' >站内信</td> <td class='center'>订单留言</td></tr> </table>")

                for (var i = 0; i < data.data.length; i++) {
                    if(data.data[i].message_center.num==0 || data.data[i].order_msg.num==0)
                    {
                        info = '<tr class="red">';
                    }
                    else
                    {
                        info = '<tr>';
                    }


                    if (data.data[i].message_center) {
                        info = info + '<td>' + data.data[i].message_center.accountSuffix + '</td><td> 总量:' + data.data[i].message_center.num + '&nbsp;&nbsp; 已读：' + data.data[i].message_center.isRead + '&nbsp;&nbsp; 未读：' + data.data[i].message_center.un_Read + '&nbsp;&nbsp; 已处理：' + data.data[i].message_center.isReturn + '&nbsp;&nbsp; 未处理:' + data.data[i].message_center.un_Return + '&nbsp;&nbsp;不必回 :' + data.data[i].message_center.reply_no + '</td>';
                    }
                    else {
                        info = info + '<td>' + data.data[i].order_msg.accountSuffix + '</td><td></td>';
                    }

                    if (data.data[i].order_msg) {
                        info = info + '<td>总量:' + data.data[i].order_msg.num + ' &nbsp;&nbsp;已读：' + data.data[i].order_msg.isRead + ' &nbsp;&nbsp;未读：' + data.data[i].order_msg.un_Read + ' &nbsp;&nbsp;已处理：' + data.data[i].order_msg.isReturn + ' &nbsp;&nbsp;未处理:' + data.data[i].order_msg.un_Return + ' &nbsp;&nbsp;不必回 :' + data.data[i].order_msg.reply_no + ' </td>';
                    }
                    else {
                        info = info + '<td></td>';
                    }

                    info = info + '</tr>';
                    $("#table1").append(info);
                }


                //$("#table1").empty().append(info);
                //    alert(data.data[55].message_center.num);

            }
        });

    })


    $("#searchpeople").click(function () {
        var date1 = $("#start_date").val();
        var date2 = $("#end_date").val();
        if (date1 == '' || date2 == '') {
            alert("选择起始时间");
            return false;
        }

        $.ajax({
            url: '<?php echo admin_base_url("smt_message/smt_message_count/get_message_user");?>',
            data: 'date1=' + date1 + '&date2=' + date2,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                var info = '';
                $("#part1").empty();
                for(var i=0;i<data.data.length;i++)
                {
                    $("#part1").append("<div><label class='col-xs-1'>"+data.data[i].name+" </label><label class='col-xs-11'> 回复量：  "+data.data[i].num+"  </label></div>");
                }


            }
        });

    })


</script>
