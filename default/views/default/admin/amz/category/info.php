<?php
/**
 * AMZ自定义分类编辑页面
 */
echo ace_header('AMZ分类');//$productImages->id

echo ace_form_open('', array('class' => 'form-horizontal registerform'), array('id' => $id));
?>
<div class="row">
    <?php
    echo ace_input_m(array('label_text' => '名称(US)', 'help' => '分类名称(美国站)', 'nullmsg' => '请输入分类名称(美国站)'), 'category_us', $categoryInfo->category_us);

    echo ace_input(array('label_text' => '名称(CA)', 'help' => '分类名称(加拿大站)'), 'category_ca', $categoryInfo->category_ca);

    echo ace_input(array('label_text' => '名称(UK)', 'help' => '分类名称(英国站)'), 'category_uk', $categoryInfo->category_uk);

    echo ace_input(array('label_text' => '名称(FR)', 'help' => '分类名称(法国站)'), 'category_fr', $categoryInfo->category_fr);

    echo ace_input(array('label_text' => '名称(DE)', 'help' => '分类名称(德国站)'), 'category_de', $categoryInfo->category_de);

    echo ace_input(array('label_text' => '名称(IT)', 'help' => '分类名称(意大利站)'), 'category_it', $categoryInfo->category_it);

    echo ace_input(array('label_text' => '名称(ES)', 'help' => '分类名称(西班牙站)'), 'category_es', $categoryInfo->category_es);

    echo ace_input(array('label_text' => '名称(JP)', 'help' => '分类名称(日本站)'), 'category_jp', $categoryInfo->category_jp);
    ?>

    <?php
    //属性读出来处理下
    $attribute = array();
    $firstAttr = '';
    if (!empty($categoryInfo->attribute)){
        $attribute = unserialize($categoryInfo->attribute);
        $firstAttr = array_shift($attribute);
    }

    ?>
    <!--添加属性列表-->
    <div class="form-group attr">
        <label class="col-sm-2 control-label no-padding-right">属性</label>
        <div class="col-sm-5">
            <span class="input-icon block input-icon-right">
                <input type="text" name="attr[]" value="<?php echo $firstAttr;?>" class="width-100">
                <i class="icon icon-info-sign"></i>
            </span>
        </div>
        <label class="col-sm-1 text-left">
            <a href="javascript:" title="添加" onclick="addAttrRow();">
                <i class="icon-plus green bigger-150"></i>
            </a>
        </label>
    </div>
    <?php
    //还有其他属性，连续输出吧
    if (!empty($attribute)){
        $str = '';
        foreach ($attribute as $at){
            $str .= '<div class="form-group attr">';
            $str .= '<div class="col-sm-5 col-sm-offset-2">';
            $str .= '<span class="input-icon block input-icon-right">';
            $str .= '<input type="text" name="attr[]" value="'.$at.'" class="width-100">';
            $str .= '<i class="icon icon-info-sign"></i>';
            $str .= '</span>';
            $str .= '</div>';
            $str .= '<label class="col-sm-1 text-left">';
            $str .= '<a href="javascript:" title="添加" onclick="delAttrRow(this);">';
            $str .= '<i class="icon-remove red bigger-150"></i>';
            $str .= '</a>';
            $str .= '</label>';
            $str .=  '</div>';
        }
        echo $str;
    }
    ?>
</div>

<?php
echo ace_srbtn('amz/category');

echo ace_form_close();
?>
<script type="text/javascript">
    //添加属性行
    function addAttrRow(){
        var str = '<div class="form-group attr">' +
            '<div class="col-sm-5 col-sm-offset-2">' +
            '<span class="input-icon block input-icon-right">' +
            '<input type="text" name="attr[]" value="" class="width-100">' +
            '<i class="icon icon-info-sign"></i>' +
            '</span>' +
            '</div>' +
            '<label class="col-sm-1 text-left">' +
            '<a href="javascript:" title="添加" onclick="delAttrRow(this);">' +
            '<i class="icon-remove red bigger-150"></i>' +
            '</a>' +
            '</label>' +
            '</div>';
        $('.attr:last').after(str);
    }

    //删除属性行
    function delAttrRow(obj){
        $(obj).closest('.form-group').remove();
    }
</script>