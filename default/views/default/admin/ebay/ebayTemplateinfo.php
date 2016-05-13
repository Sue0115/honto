<?php
/**
 * 模板编辑页面
 */
?>
<div class="col-xs-12">
    <h3 class="header small lighter blue">Ebay刊登-卖家描述</h3>
    <div class="table-header">&nbsp;
    </div>

<form id="form1"  class="form-horizontal validate_form " action="<?php echo admin_base_url('ebay/ebay_template/infoadd'); ?>" method="post" >


    <div class="form-group hidden">
        <label class="col-sm-2 control-label">id</label>
        <div class="col-sm-10">
            <input type="text"  class="form-control" name="id" value="<?php
            if(isset($info))
            {
                echo htmlspecialchars_decode($info[0]['id']);
            }
            ?>"
                >
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">卖家描述</label>
        <div class="col-sm-10">
            <input type="text"  class="form-control" name="maijiamiaoshu" value="<?php
            if(isset($info))
            {
                echo htmlspecialchars_decode($info[0]['name']);
            }
            ?>"
                >
        </div>
    </div>
<br/>
    <div class="form-group">
        <label class="col-sm-2 control-label">Payment</label>
        <div class="col-sm-10">
                    <textarea name="content" id="content" class="form-control" rows="5">
                        <?php
                            if(isset($info))
                            {
                                echo htmlspecialchars_decode($info[0]['payment']);
                            }
                        ?>

                    </textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Delivery details</label>
        <div class="col-sm-10">
                    <textarea name="content2" id="content2" class="form-control" rows="5">
                        <?php
                        if(isset($info))
                        {
                            echo htmlspecialchars_decode($info[0]['shipping']);
                        }
                        ?>

                    </textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Terms of sales</label>
        <div class="col-sm-10">
                    <textarea name="content3" id="content3" class="form-control" rows="5">
                            <?php
                            if(isset($info))
                            {
                                echo htmlspecialchars_decode($info[0]['sales_policy']);
                            }
                            ?>
                    </textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">About us</label>
        <div class="col-sm-10">
                    <textarea name="content4" id="content4" class="form-control" rows="5">
                        <?php
                        if(isset($info))
                        {
                            echo htmlspecialchars_decode($info[0]['about_us']);
                        }
                        ?>
                    </textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Contact us</label>
        <div class="col-sm-10">
                    <textarea name="content5" id="content5" class="form-control" rows="5">
                        <?php
                        if(isset($info))
                        {
                            echo htmlspecialchars_decode($info[0]['contact_us']);
                        }
                        ?>
                    </textarea>
        </div>
    </div>
    <div class="clearfix form-actions align-right">


            <button class="btn btn-success submit_btn " type="submit" name="save" >
                <i class="icon-ok bigger-110"></i>
                保存
            </button>
        <label>

            <a class="btn btn-inverse " href="<?php echo admin_base_url('ebay/ebay_template/ebaymodel');?>">
                <i class="icon-ok bigger-110"></i>返回列表</a>
        </label>

    </div>
</form>
<script type="text/javascript">
    KindEditor.ready(function(K){
        var heihht = '400px'

        var editor = K.create("#content",{
            "width":"100%",
            "height":heihht,
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

        var editor2 = K.create("#content2",{
            "width":"100%",
            "height":heihht,
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
        var editor3 = K.create("#content3",{
            "width":"100%",
            "height":heihht,
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
        var editor4 = K.create("#content4",{
            "width":"100%",
            "height":heihht,
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
        var editor5 = K.create("#content5",{
            "width":"100%",
            "height":heihht,
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


        $('.validate_form').Validform({
            btnSubmit: '.submit_btn',
            btnReset: '.btn-reset',
            ignoreHidden: true,
            ajaxPost: true,
            callback: function (data) { //返回数据

                    showxbtips(data.info, 'alert-success');
            }
        });

    });


</script>

