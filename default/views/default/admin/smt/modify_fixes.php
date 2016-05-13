<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-07-29
 * Time: 16:43
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">速卖通刊登-设置SKU前缀和后缀</h3>

        <div class="table-header">&nbsp;</div>


                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2">账号:</label>

                        <div class="col-sm-6">
                            <select id="selectaccount">
                                <option value="">---请选择---</option>
                                <?php
                                foreach ($token as $t):
                                    echo '<option value="' . $t['token_id'] . '">' . $t['token_id'] . '-' . $t['seller_account'] . '</option>';
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2">前缀:</label>

                        <div class="col-sm-6">
                            <input type="text" id="qianzhui" class="control-label"/><span class="red">不需要加*</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 ">后缀:</label>

                        <div class="col-sm-6">
                            <input type="text" id="houzhui" class="control-label"/><span class="red">不需要加#</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <span class="red">更新完成后，更新失败的productID 将会在下方显示</span>
                        </div>
                    <div class="form-group">
                        <label  class="col-sm-4 "></label>
                        <div class="col-sm-4">
                        <a href="#" class="btn btn-primary " id="pricecheck">确定</a>
                        </div>
                    </div>
                </form>
            </div>
    <div id="errorinfo" class="red">

        </div>


</div>

<script type="text/javascript">
    $(function () {
        $('#pricecheck').click(function () {
            var token_id = $("#selectaccount").val();
            var qianzhui = $("#qianzhui").val();
            var houzhui = $("#houzhui").val();

            $.ajax({
                url: '<?php echo admin_base_url("publish/smt/modifyActiveListtingFixes");?>',
                data: 'token_id=' + token_id + '&qianzhui=' + qianzhui + '&houzhui=' + houzhui,
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function () {
                    ii = layer.load('更新中。。。');
                },
                success: function (data) {
                    layer.close(ii);
                    $('#errorinfo').empty();
                    for(var i=0;i<data.data.length;i++)
                    {
                        $('#errorinfo').append(data.data[i]+'</br>')

                        //alert(data.data[i]);
                    }
                //    $('#errorinfo').append(data.data[]);
                   // alert(data.data)
                }
            });
        })
    })

</script>