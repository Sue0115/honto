<?php
/**
 * 模板编辑页面
 */
?>
<div class="col-xs-12">
    <h3 class="header small lighter blue">Ebay刊登-模板详情</h3>
    <div class="table-header">&nbsp;
    </div>

    <form id="form1"  class="form-horizontal validate_form " action="<?php echo admin_base_url('ebay/ebay_template/htmladd'); ?>" method="post" >


        <div class="form-group hidden">
            <label class="col-sm-2 control-label">id</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" name="id" value="<?php
                if(isset($info))
                {
                    echo ($info[0]['id']);
                }
                ?>"
                    >
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">模板名字</label>
            <div class="col-sm-10">
                <input type="text"  class="form-control" name="mobanname" value="<?php
                if(isset($info))
                {
                    echo $info[0]['template_name'];
                }
                ?>"
                    >
            </div>
        </div>
        <br/>
        <div class="form-group">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-10">

            <span class="red">注意事项： 先将下面编辑框设置为HTMl代码，{{tittle}} 表示模板标题放置的地方 {{description}}  表示产品描述放置的地方
                              {{paymentterms}} 、{{shippingterms}}、{{termsofsales}}、{{contactus}}、 {{aboutus}} 分别对应卖家描述的对应字段</span>
            </div>

        </div>
        <br/>
        <div class="form-group">
            <label class="col-sm-2 control-label">html</label>
            <div class="col-sm-10">
                    <textarea name="content" id="content" class="form-control" rows="5">
                        <?php
                        if(isset($info))
                        {
                            echo htmlspecialchars_decode($info[0]['template_html']);
                        }
                        ?>

                    </textarea>
            </div>
        </div>

        <div class="clearfix form-actions align-right">
            <input type="hidden" name="action" id="action" value=""/>

            <button class="btn btn-success submit_btn " type="submit" name="save" >
                <i class="icon-ok bigger-110"></i>
                保存
            </button>
            <label>
                <a class="btn btn-inverse " href="<?php echo admin_base_url('ebay/ebay_template/html');?>">
                    <i class="icon-ok bigger-110"></i>返回列表</a>
            </label>

        </div>
    </form>
    <script type="text/javascript">
        KindEditor.ready(function(K) {
            var heihht = '600px'
            var editor = K.create("#content", {
                "width": "100%",
                "height": heihht,
                "filterModel": false,//是否过滤html代码,true过滤
                "resizeType": "2",//是否可以改变editor大小，0：不可以   1：可改高   2：无限
                "items": ['source', '|', 'fullscreen', 'undo', 'redo',
                    'cut', 'copy', 'paste', 'plainpaste',
                    'wordpaste', '|', 'justifyleft', 'justifycenter',
                    'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist',
                    'indent', 'outdent', 'subscript', 'superscript', '|', 'selectall', '-', 'title',
                    'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                    'strikethrough', 'removeformat', '|', 'advtable', 'hr',
                    'emoticons', 'link', 'unlink', 'table'],
                "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
                "afterBlur": function () {
                    this.sync();
                } //必须，不然第一次提交不到
            });


            $('.submit_btn').click(function (e) {
                if (e && e.preventDefault) {
                    e.preventDefault();
                    //IE中阻止函数器默认动作的方式
                }
                else {
                    window.event.returnValue = false;
                }
                $('#action').val($(this).attr('name'));
            })

            $('.validate_form').Validform({
                btnSubmit: '.submit_btn',
                btnReset: '.btn-reset',
                ignoreHidden: true,
                ajaxPost: true,
                callback: function (data) { //返回数据

                    showxbtips(data.info, 'alert-success');
                }
            });
        })
 </script>




