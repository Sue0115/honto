<?php
/**
 * 模板编辑页面
 */
echo ace_header('模板', $template_info['id']);

echo ace_form_open('', '', array('id' => $template_info['id']));
?>
    <div class="row">
            <?php
            echo ace_dropdown('平台类型','plat',$plat_type, $template_info['plat']);

            echo ace_input_m('模板名称','name',$template_info['name']);

            ?>
            <div class="form-group clearfix">
                <label class="col-sm-2 control-label">
                    效果图
                </label>

                <div class="col-sm-5">
                    <a class="btn btn-primary btn-sm" id="uploadPic">上传</a>
                    <div class="img">
                        <?php if ($template_info['pic_path']):?>
                            <!--图片显示,注意图片路径问题-->
                            <a href="javascript: viod(0);">
                                <img src="<?php echo site_url().'attachments/upload'.$template_info['pic_path'];?>" id="pic_show" alt="<?php echo $template_info['name'];?>" width="50" height="50"/>
                            </a>

                        <?php endif;?>
                        <input type="hidden" name="pic_path" id="pic_path" value="<?php echo $template_info['pic_path'];?>"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">模板详情</label>
                <div class="col-sm-10">
                    <textarea name="content" id="content" class="form-control" rows="5">
                        <?php echo htmlspecialchars_decode($template_info['content']);?>
                    </textarea>
                </div>
            </div>

            <!--/内容 -->
        </div>
<?php
echo ace_srbtn('publish/smt_template');

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

    var editor2 = K.editor({
        allowFileManager : false,
        uploadJson: '<?php echo admin_base_url('kindeditor/uploadToProject');?>'
    });


    //图片上传，路径应该要处理下
    K('#uploadPic').click(function(){
        editor2.loadPlugin('image', function() {
            editor2.plugin.imageDialog({
                showRemote: false,
                clickFn : function(url, title, width, height, border, align) {

                    if (K('#pic_show').length > 0){
                        K('#pic_show').attr('src', "<?php echo site_url().'attachments/upload';?>"+url);
                    }else {
                        K('.img').append('<a href="javascript: viod(0);"><img id="pic_show" src="<?php echo site_url().'attachments/upload';?>'+url+'" width="50" height="50" /></a>');
                    }
                    K('#pic_path').val(url);
                    editor2.hideDialog();
                }
            });
        });
    });
});
</script>