<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-09
 * Time: 13:24
 */
?>
<style>

    #elevator_item{width:60px;height:100px;position:fixed;right:15px;bottom:10px;-webkit-transition:opacity .4s ease-in-out;-moz-transition:opacity .4s ease-in-out;-o-transition:opacity .4s ease-in-out;opacity:1;z-index:100020; }
 /*   #elevator_item.off{opacity:0;visibility:hidden}*/
    #elevator{display:block;width:60px;height:50px;background:url(<?php echo site_url('attachments').'/images/icon_top.png';?>) center center no-repeat;background-color:#444;background-color:rgba(0,0,0,.6);border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,.2);cursor:pointer;margin-bottom:10px}
    #elevator:hover{background-color:rgba(0,0,0,0.7)}
    #elevator:active{background-color:rgba(0,0,0,.75)}
    #elevator_item .qr{display:block;width:60px;height:40px;border-radius:2px;box-shadow:0 1px 3px rgba(0,0,0,.2);cursor:pointer;background:url(<?php echo site_url('attachments').'/images/icon_code.png';?>) center center no-repeat;background-color:#444;background-color:rgba(0,0,0,.6)}
    #elevator_item .qr:hover{background-color:rgba(0,0,0,.7)}
    #elevator_item .qr:active{background-color:rgba(0,0,0,.75)}
    #elevator_item .qr-popup{width:170px;height:200px;background:#fff;box-shadow:0 1px 8px rgba(0,0,0,.1);position:absolute;left:-130px;bottom:0px;border-radius:2px;display:none;text-align:center}
    #elevator_item .qr-popup .code-link{display:block;margin:10px;color:#777}
    #elevator_item .qr-popup .code{display:block;margin-bottom:10px}
    #elevator_item .qr-popup .arr{width:6px;height:11px;background:url(<?php echo site_url('attachments').'/images/code_arrow.png';?>) 0 0 no-repeat;position:absolute;right:-6px;bottom:14px}
</style>


<div class="row" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="col-xs-12">
      <!--  <h3 class="header smaller lighter blue">信息管理-SMT纠纷中心</h3>-->

        <div class="table-header">&nbsp;</div>

        <div class=".table-bordered">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                账号:
                                <select name="search[token_id]" id="token_id">
                                    <option value="">---全选---</option>
                                    <?php
                                    foreach ($token_array as $t):
                                        echo '<option value="' . $t['token_id'] . '" ' . ($search['token_id'] ==  $t['token_id'] ? 'selected="selected"' : '') . '>' . $t['accountSuffix'] . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <label class="" >
                                <button class="btn btn-primary btn-sm " type="submit">筛选</button>

                                &nbsp;&nbsp; &nbsp;&nbsp;

                            </label>
                            <label>
                            <button type="button" class="btn btn-sm btn-danger" data-toggle="popover" title="操作流程/注意事项" data-content="先token检测，提示信息为 Beyond the app call frequency limit  说明该账号当日API次数已经用完 需等明天回复后在使用.提示信息为 Request need user authorized  点击刷新token.  提示信息为wrong refreshToken  点击重新授权. 以上操作后,仍无效,请联系IT  ">注意事项</button>
                            </label>
                        </form>
                    </div>
                </div>

                <table class="table    dataTable " id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="20%" >
                        <col width="30%"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace"/>
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th class="center">账号:</th>
                        <th class="center">access_token</th>
                        <th class="center">操作</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($data):
                        foreach ($data as $list):
                           ?>
                           <tr class="center">
                               <td class="center">
                                   <label>
                                       <input type="checkbox" class="ace" name="ids[]" value="<?php echo $list->token_id;?>">
                                       <span class="lbl"></span>
                                   </label>
                               </td>
                               <td><?php echo $list->accountSuffix;  ?> </td>
                               <td><?php echo $list->access_token;  ?> </td>
                               <td>


                                   <button type="button" class="dosome check  btn btn-success btn-sm  ">
                                       <span class="glyphicon glyphicon-bell" aria-hidden="true"></span>token检测
                                   </button>

                                   <button type="button" class="dosome refresh  btn btn-primary btn-sm  ">
                                       <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>刷新token
                                   </button>

                                   <a type="button" class=" reget btn btn-danger btn-sm" href="<?php echo admin_base_url("smt/account_manage/auto_refresh_refresh_token/".$list->token_id) ?>"  target="_blank">
                                       <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>重新授权
                                   </a>

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
    <div id="elevator_item"> <a id="elevator" onclick="return false;" title="回到顶部"></a> <a class="qr"></a>
        <div class="qr-popup">
            <a class="code-link">
                <img class="code" width="130px" height="130px" src="<?php echo site_url('attachments').'/images/wx.png';?>" />
            </a> <span>二维码扫描加好友</span>
            <div class="arr"></div>
        </div>
    </div>
</div>

<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js') ?>"></script>
<script src="<?php echo static_url('theme/common/layer/layer.js') ?>"></script>
<script type="text/javascript">
    $(function() {
        layer.use('extend/layer.ext.js');
        $('[data-toggle="popover"]').popover()

        $(window).scroll(function(){
            $("#elevator_item").show();
            /*var scrolltop=$(this).scrollTop();
            if(scrolltop>=50){
                $("#elevator_item").show();
            }else{
                $("#elevator_item").hide();
            }*/
        });
        $("#elevator").click(function(){
            $("html,body").animate({scrollTop: 0}, 500);
        });
        $(".qr").hover(function(){
            $(".qr-popup").show();
        },function(){
            $(".qr-popup").hide();
        });
    });


    $(".dosome").click(function(){
        var token_id = $(this).parent().parent().children().eq(0).children().eq(0).children().eq(0).val();
        var type = '';
        var string ='';
        if($(this).hasClass("check"))
        {
            type='check';
            string="检测token？";
        }else if($(this).hasClass("refresh")){
            type='refresh';
            string="刷新token？";
        }else{
            alert("出错了！");
            return false;
        }

        if (confirm('确认'+string)) {
            $.ajax({
                url: '<?php echo admin_base_url("smt/account_manage/do_action");?>',
                data: 'token_id=' + token_id+'&type='+type,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    layer.alert(data.info,2);
                }
            });
        }




    });

    $(".reget").click(function(){
        var token_id = $(this).parent().parent().children().eq(0).children().eq(0).children().eq(0).val();
        layer.prompt({title: '请输入code，并确认',type: 0}, function(pass, index, el) {
            if (pass.trim() == '') {
                layer.close(index);
                return false;
            }
            layer.close(index);

            $.ajax({
                url: '<?php echo admin_base_url("smt/account_manage/code_update_refresh_token");?>',
                data: 'code='+pass+'&token_id='+token_id,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    layer.alert(data.info,2)
                }
            });


        });

    });


/*    $(".reget").click(function(){
        var token_id = $(this).parent().parent().children().eq(0).children().eq(0).val(123);

        alert(token_id);
    })*/
</script>
