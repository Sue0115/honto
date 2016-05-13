<?php
/**
 * 编辑自定义关联模板
 */
echo ace_header('模板', $relation_info['id']);

echo ace_form_open('', '', array('id' => $relation_info['id']));
?>
    <div class="row">
            <?php
            echo ace_dropdown('账号','token_id', $token_list, $relation_info['token_id']);

            echo ace_input_m('模板名称','name',$relation_info['name']);

            echo ace_dropdown('状态', 'status', $status_list, $relation_info['status']);

            ?>

            <div class="form-group">
                <label class="col-sm-2 control-label">详情</label>
                <div class="col-sm-10">
                    <textarea name="content" id="content" class="form-control" rows="5">
                        <?php echo htmlspecialchars_decode($relation_info['content']);?>
                    </textarea>
                </div>
            </div>

        </div>
<?php
echo ace_srbtn('publish/relation');

echo ace_form_close();
?>
<script type="text/javascript">
KindEditor.ready(function(K){
    var editor = K.create("#content",{
        "width":"100%",
        "height":"400px",
        "filterModel":false,//是否过滤html代码,true过滤
        "resizeType":"2",//是否可以改变editor大小，0：不可以   1：可改高   2：无限
        "items" :  ['source', '|', 'fullscreen', 'undo', 'redo',
            'cut', 'copy', 'paste', 'plainpaste',
            'wordpaste', '|', 'justifyleft', 'justifycenter',
            'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist',
            'indent', 'outdent', 'subscript', 'superscript', '|', 'selectall', '-', 'title',
            'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
            'strikethrough', 'removeformat', '|', 'advtable', 'hr',
            'emoticons', 'link', 'unlink', 'table'],
        "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
        "afterBlur": function(){this.sync();} //必须，不然第一次提交不到
    });
});
</script>