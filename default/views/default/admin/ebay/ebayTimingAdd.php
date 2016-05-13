<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-08-05
 * Time: 11:27
 */
?>
<div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs"  role="tablist">
        <li role="presentation" id="homefa"  class="active col-sm-4"><a href="#home" aria-controls="home" role="tab" data-toggle="tab" class="center">规则设置</a></li>
       <li>
           <span class="next"></span>
       </li>
        <li role="presentation" id="profilefa " class=" col-sm-4"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab" class="center" >选择范本</a></li>
        <li>
            <span class="next"></span>
        </li>
        <li role="presentation" id="messagesfa" class="col-sm-4"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab" class="center" >定时刊登</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active " id="home">

        <form class="form-horizontal">

            <!--
            <span>频率</span>
            <hr>

            <div class="form-group">
                <label class="col-sm-2 control-label ">执行频率</label>
                <div class="col-sm-6">
                    <select id="dofrequency" >
                        <option value="1">按天</option>
                    </select>
                </div>
            </div>

             <div class="form-group">
                 <label class="col-sm-2 control-label"></label>
                 <div class="col-sm-6">
                   <span>每</span> <input type="text" id="per_day" class="control-label" /> <span>天</span>
                 </div>
             </div>-->

            <span>每天频率</span>
            <hr>
            <div class="form-group">
                <label class="col-sm-2 control-label">执行频率</label>
                <div class="col-sm-6">
                    <select id="dayfrequency">
                        <option value="1">每天时间</option>
                        <option value="2">间隔时间</option>
                    </select>
                </div>
            </div>


            <div class="form-group type1" >
                <label class="col-sm-2"></label>
                <div class="col-sm-10">
                    <div>
                        <span>开始时间</span> <input type="text"  value="" datefmt="HH:mm:ss" class="Wdate" id="day_start_date1" name="day_start_date1" size="15"/>
                        <span> 持续</span>  <input type="text" id="max_day" size="5"><span>天</span>
                    </div>
                </div>
            </div>

            <div class="form-group type2 hidden">
                <label class="col-sm-2"></label>
                <div class="col-sm-10">
                    <div>
                    <span>开始时间</span> <input type="text"  value="" datefmt="HH:mm:ss" class="Wdate" id="day_start_date2" name="day_start_date2" size="15"/>
                <span>每</span><input id="interval_time" type="text"size="10"/><span>分钟上传一个刊登 每天最多上传</span>
                    <input type="text" id="max_num" size="5"><span>个刊登</span>
                    </div>

                    <div class="hidden">
                        <span>开始时间</span><input type="text"  value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate"  name="import_start" size="15"/>
                        <span>结束时间</span><input type="text"  value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate"  name="import_start" size="15"/>
                        <span>均时上传</span><input type="text" size="10"><span>个刊登</span>
                    </div>
                </div>
            </div>



            <span>持续时间</span>
            <hr>
            <div class="form-group">
                <label class="col-sm-2 control-label">开始日期</label>
                <div class="col-sm-10">

                    <input type="text"  value="" datefmt="yyyy-MM-dd" class="Wdate"  name="start_time" id="start_time" size="15"/>
                    <select id="keeptime">
                        <option value="1">结束时间</option>
                    </select>

                        <input type="text" size="5" class="hidden"><span class="hidden">个刊登后停止</span>



                        <input type="text"  value="" datefmt="yyyy-MM-dd" class="Wdate "  name="end_time" id="end_time" size="15"/>



                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-8"></label>
                <div class="col-sm-2">
                    <a href="#"  id="goprofile"  class="btn-primary btn-sm" >下一步</a>
                </div>

            </div>

        </form>








        </div>



        <div role="tabpanel" class="tab-pane" id="profile">

            <form class="form-horizontal">
                <div class="form-group">
                <div class="col-sm-2 right">
                    <a href="javascript: void(0);"  class="btn-primary btn-sm" id="selectlist"> 选择范本</a>
                </div>
                <div class="col-sm-2 right">
                    <a href="javascript: void(0);"  class="btn-primary btn-sm" id="qingchu"> 清除</a>
                </div>
                </div>

                <div class="form-group">
                    <table class="table table1 table-condensed table-hover">
                        <tr><td class="hidden"  >id</td><td>图片</td> <td >名称 / SKU / 标题</td>  <td  class="center" >ebay账号</td> <td  class="center " >站点</td> <td  class="center" >类型</td> <td  class="center"> 操作</td></tr>

                    </table>
                </div>


                <div class="form-group">
                    <label class="col-sm-8"></label>

                    <div class="col-sm-2">
                        <a href="#" id="golast" class="btn-primary btn-sm"> 下一步</a>
                    </div>
                </div>


            </form>


        </div>



        <div role="tabpanel" class="tab-pane" id="messages">

            <form class="form-horizontal" id="form1">
             <!--   <div class="form-group">
                    <div class="col-sm-2 right">
                        <a href="javascript: void(0);"  class="btn-primary btn-sm" id="selectlist"> 选择范本</a>
                    </div>
                    <div class="col-sm-2 right">
                        <a href="javascript: void(0);"  class="btn-primary btn-sm" id="qingchu"> 清除</a>
                    </div>
                </div>-->

                <div class="form-group">
                    <table  name="table" class="table table2 table-condensed table-hover">

                        <tr><td class="hidden"  >id</td><td>本地时间/站点时间</td><td>图片</td> <td >名称 / SKU / 标题</td>  <td  class="center" >ebay账号</td> <td  class="center " >站点</td> <td  class="center" >类型</td> <td  class="center"> 操作</td></tr>

                    </table>
                </div>


                <div class="form-group">
                    <label class="col-sm-8"></label>

                    <div class="col-sm-4">
                    <!--    <input type="submit"   class=" btn-sm btn-success" value="保存"/>-->
                  <!--      <button type="submit" class="btn btn-primary">保存</button>-->
                        <a href="#" class="btn-primary btn-sm" id="baocun"> 保存</a>
                        <a href="<?php echo admin_base_url('ebay/ebay_timing/ebayTaskList');?>" class="btn-primary btn-sm">查看预刊登列表</a>
                    </div>
                </div>

            </form>

        </div>
    </div>

</div>

<script type="text/javascript" src="/static/theme/common/jquery-form.js" ></script>
<script type="text/javascript" src="/static/theme/common/My97DatePicker/WdatePicker.js" ></script>

<script type="text/javascript">
    function delecttablerow(e)
    {
        $(e).parent().parent().remove();
    }
    $(document).ready(function(){

        $(document).on('click','.Wdate',function(){
            var o = $(this); // $("#myTable tr:gt(0)").remove();
            if(o.attr('dateFmt') != '')
                WdatePicker({dateFmt:o.attr('dateFmt')});
            else if(o.hasClass('month'))
                WdatePicker({dateFmt:'yyyy-MM'});
            else if(o.hasClass('year'))
                WdatePicker({dateFmt:'yyyy'});
            else
                WdatePicker({dateFmt:'yyyy-MM-dd'});
        });
    $('#qingchu').click(function(){
        $(".table1 tr:gt(0)").remove();
    })

      //  $('#form1').submit(function()//提交表单
        $("#baocun").click(function()
        {

            url = '<?php echo admin_base_url("ebay/ebaylist/creatEbaylistTask");?>';
            $("#form1").formSerialize();
            var options = {
               //后台将把传递过来的值赋给该元素
                url:url, //提交给哪个执行
                type:'POST',
                dataType:'json',
                success: function(){
                alert('保存成功');

                } //显示操作提示
            };
            $('#form1').ajaxSubmit(options);
            return false; //为了不刷新页面,返回false，反正都已经在后台执行完了，没事！
        });

        $('#goprofile').click(function(){
         //   alert(123);\
        $("#home").removeClass('active');
        $("#homefa").removeClass('active');

        $("#profile").addClass('active')
        $("#profilefa").addClass('active')

            //$('#myTabs li:eq(1) a').tab('show')
        })

        $('#golast').click(function(){
            var ids='';
            $('.table1').find('tr').each(function () {
                if($(this).children().eq(0).text() !='id' && $(this).children().eq(0).text() !='')
                {
                    ids = ids+','+$(this).children().eq(0).text();
                }
            })
            if(ids=='')
            {
                return false;
            }
          //  var per_day = $('#per_day').val(); //每多少天

            var dayfrequency = $('#dayfrequency').val();

            var start_time =$('#start_time').val(); //开始时间
            var end_time =$('#end_time').val();//结束时间
            var data ='';
            if(dayfrequency==1)
            {
                var day_start_date = $('#day_start_date1').val();
                var max_day=$('#max_day').val(); //最多上传个数
                data = 'ids='+ids+'&dayfrequency='+dayfrequency+'&day_start_date='+day_start_date+'&max_day='+max_day+'&start_time='+start_time+'&end_time='+end_time;



            }

            if(dayfrequency==2)
            {

                var day_start_date = $('#day_start_date2').val(); //开始时间
                var interval_time = $('#interval_time').val(); // 时间段
                var max_num =$('#max_num').val(); //最多上传个数
                 data = 'ids='+ids+'&dayfrequency='+dayfrequency+'&day_start_date='+day_start_date+'&interval_time='+interval_time+'&max_num='+max_num+'&start_time='+start_time+'&end_time='+end_time;
            }
            url = '<?php echo admin_base_url("ebay/ebaylist/creatNewPublishTime");?>';
            $.ajax({
                url: url,
                data: data,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {

                    for(var i=0;i<data.data.length;i++)
                    {

                        $(".table2").append('<tr><td class="hidden" name="id">'+data.data[i]['id']+'</td><input type="hidden"  name="task[]" value="'+data.data[i]['id']+'"/> ' +
                        '<td>'+data.data[i]['local_publish_time']+'</br>'+data.data[i]['site_publish_time']+'</td><input type="hidden" name="local_publish_time[]" value="'+data.data[i]['local_publish_time']+'" /><input type="hidden"  name="site_publish_time[]" value="'+data.data[i]['site_publish_time']+'"/>' +
                        '<td><img src="'+data.data[i]['ebay_picture']+'"width="50" height="50" /></td>' +
                        '<td>'+data.data[i]['name']+'</br>'+data.data[i]['sku']+'</br>'+data.data[i]['title']+'</td><td>'+data.data[i]['ebayaccount']+'</td><td>'+data.data[i]['site']+'</td>' +
                        '<td>'+data.data[i]['ad_type']+'</td>' +
                        '<td><a href="javascript: void(0);" class="delettable btn-primary btn-sm " onclick="delecttablerow(this)">移除</a></td></tr>')

                    }

                }
            })

            $("#profile").removeClass('active');
            $("#profilefa").removeClass('active');
            $("#messages").addClass('active');
            $("#messagesfa").addClass('active');

        })


        layer.use('extend/layer.ext.js');

        $('#selectlist').click(function(){
            $.layer({
                type : 2,
                title  : ['选择Listting',true],
                shade  : [0.8 , '' , true],
                iframe : {src :'<?php echo admin_base_url("ebay/ebaylist/ebaylistinfocheck"); ?>'},
                area: ['1300px', '500px'],
                success : function(){
                    layer.shift('top', 340)
                },

                btns : 2,
                btn : ['确定', '取消'],

                yes : function(index){ //确定按钮的操作
                    var ids ='';
                    layer.getChildFrame('.ace:checked', index).each(function() {
                        if($(this).val()!='on' && $(this).val()!='')
                        {
                            ids=ids+','+$(this).val();
                        }
                    });
                    url = '<?php echo admin_base_url("ebay/ebaylist/getListById");?>';
                    $.ajax({
                        url: url,
                        data: 'ids=' + ids,
                        type: 'POST',
                        dataType: 'JSON',
                        success: function (data) {

                            for(var i=0;i<data.data.length;i++)
                            {

                                $(".table1").append('<tr><td class="hidden" >'+data.data[i]['id']+'</td><td><img src="'+data.data[i]['ebay_picture']+'"width="50" height="50" /></td><td>'+data.data[i]['name']+'</br>'+data.data[i]['sku']+'</br>'+data.data[i]['title']+'</td><td>'+data.data[i]['ebayaccount']+'</td><td>'+data.data[i]['site']+'</td><td>'+data.data[i]['ad_type']+'</td><td><a href="javascript: void(0);" class="delettable btn-primary btn-sm " onclick="delecttablerow(this)">移除</a></td></tr>')

                            }
                        }
                    })

                    layer.close(index);
                },
                no: function(index){
                    layer.close(index);
                }
            })
        })

        $(".delettable").click(function(){
            alert(123);
        })

         $("#dayfrequency").change(function(){

             var type =  $("#dayfrequency").val();

             if(type==1)
             {
                     $(".type1").removeClass("hidden");
                     $(".type2").addClass("hidden");
             }
             else
             {
                     $(".type2").removeClass("hidden");
                     $(".type1").addClass("hidden");
             }

         })
    });
</script>