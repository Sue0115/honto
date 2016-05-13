<?php
/**
 * AMZ产品图片设置页面
 */
echo ace_header('AMZ图片', $productImages->id);

echo ace_form_open('', array('class' => 'form-horizontal editorform'), array('id' => $productImages->id));
?>
<div class="row">
    <?php
    echo ace_dropdown('站点', 'token_id', $tokenList, $productImages->token_id);

    echo ace_input_m(array('label_text' => 'AMZ-SKU', 'help' => '请输入亚马逊站点上的SKU', 'nullmsg' => '请输入亚马逊站点上的SKU'), 'sku', $productImages->sku);

    echo ace_input_m(array('label_text' => '主图', 'help' => '请输入以http://开头的图片链接', 'errormsg' => '请输入以http://开头的图片链接', 'datatype' => 'imageUrl'), array('class' => 'width-100 inputurl', 'name' => 'Main', 'id' => 'Main'), $productImages->Main);

    echo ace_input_m(array('label_text' => 'Swatch', 'help' => '请输入以http://开头的图片链接', 'errormsg' => '请输入以http://开头的图片链接', 'datatype' => 'imageUrl'), array('class' => 'width-100 inputurl', 'name' => 'Swatch', 'id' => 'Swatch'), $productImages->Swatch);

    echo ace_input('PT1', 'PT1', $productImages->PT1);

    echo ace_input('PT2', 'PT2', $productImages->PT2);

    echo ace_input('PT3', 'PT3', $productImages->PT3);

    echo ace_input('PT4', 'PT4', $productImages->PT4);

    echo ace_input('PT5', 'PT5', $productImages->PT5);

    echo ace_input('PT6', 'PT6', $productImages->PT6);

    echo ace_input('PT7', 'PT7', $productImages->PT7);

    echo ace_input('PT8', 'PT8', $productImages->PT8);

    if ($productImages->callresult == 2): //已上传
    ?>

    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2 red">
            备注：确认保存后将会重新上传并覆盖以前的图片
        </div>
    </div>
    <?php endif;?>

    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2 red">
            图片链接必须是以(jpg|jpeg|bmp|gif|png)结尾的url地址,可带参数
        </div>
    </div>
</div>
<?php
echo ace_srbtn('amz/feeds');

echo ace_form_close();
?>
<script type="text/javascript">
    $(function(){
        var index = $(".editorform").attr("ajaxpost");
        $(".editorform").Validform({
            datatype:{
                //添加个验证规则
                "imageUrl":function(gets,obj,curform,regxp){
                    /*参数gets是获取到的表单元素值，
                     obj为当前表单元素，
                     curform为当前验证的表单，
                     regxp为内置的一些正则表达式的引用。*/

                    var reg1=/^http:\/\/\w+(\.\w+)+.*\.((jpg)|(jpeg)|(gif)|(bmp)|(png))(\?.*)?$/i;

                    if(reg1.test(gets)){return true;}

                    return false;
                }
            },

            tiptype:function(msg,o,cssctl){
                //msg：提示信息;
                //o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
                //cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;

                if(!o.obj.is("form")){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;

                    var objtip=o.obj.parents('.form-group').find('.help-block');

                    cssctl(objtip,o.type);
                    objtip.text(msg);

                    if(o.type==2){
                        //					objtip.fadeOut(2000);
                    }else{
                        if(objtip.is(":visible")){return;}
                        //					objtip.show();
                    }

                }

            },
            ajaxPost:index,
            callback:function(data){
                if(data.status=="y"){
                    jQuery("input:eq(0)").val(data.id);
                    showxbtips(data.info,'alert-warning');
                }else{
                    showtips(data.info);
                }
            }

        });
    })
</script>