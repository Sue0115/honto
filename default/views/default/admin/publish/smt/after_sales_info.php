<?php
/**
 * 售后模板详情页
 */
echo ace_header('模板', $template_info['id']);

echo ace_form_open('', '', array('id' => $template_info['id']));
?>
<div class="row">
    <?php
    echo ace_dropdown('平台类型', 'plat', $plat_type, $template_info['plat'], 'id="plat" class="width-100"');

    if ($template_info['plat'] == 6) { //速卖通平台
        echo ace_dropdown('账号', 'token_id', $token_list, $template_info['token_id'], 'id="token_id" class="width-100"');
    }

    echo ace_input_m('模板名称', 'name', $template_info['name']);

    ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">模板详情</label>

        <div class="col-sm-10">
            <textarea name="content" id="content" class="form-control" rows="5">
                <?php echo htmlspecialchars_decode($template_info['content']); ?>
            </textarea>
        </div>
    </div>

</div>
<?php
echo ace_srbtn('publish/after_sales_service');

echo ace_form_close();
?>
<script type="text/javascript">
    KindEditor.ready(function (K) {
        var editor = K.create("#content", {
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
            "afterBlur": function(){this.sync();} //必须，不然提交不到
        });
    });

    $(function () {

        //联动获取账号
        $(document).on('change', '#plat', function () {
            var plat = $(this).val();

            if (!plat) {
                $('#token_id').closest('.form-group').remove();
                return false;
            } else {
                $.ajax({
                    url: '<?php echo admin_base_url("publish/after_sales_service/ajaxGetTokenList");?>',
                    data: 'plat=' + plat,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        if (data.status) {
                            var input = '<div class="form-group">' +
                                '<label for="token_id" class="col-xs-12 col-sm-2 control-label no-padding-right">账号</label>' +
                                '<div class="col-xs-12 col-sm-5">' +
                                '<select name="token_id" id="token_id" class="width-100">';
                            var options = '<option>--请选择--</option>'; //选项可以公用

                            $.each(data.data, function (index, el) {
                                options += '<option value="' + el.token_id + '">' + el.token_id + '-' + el.seller_account + '</option>';
                            });

                            if ($('#token_id').length > 0) { //说明输入框已经存在了
                                $('#token_id').empty().append(options);
                            } else {
                                input += options;
                                input += '</select>' +
                                '</div>' +
                                '<div>';
                                $('#plat').closest('.form-group').after(input);
                            }
                        } else {
                            $('#token_id').closest('.form-group').remove();
                            showxbtips(data.info, 'alert-warning');
                        }
                    }
                });
            }
        });
    })
</script>