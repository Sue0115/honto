<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-08
 * Time: 13:08
 */
?>
<style>
    .row-border {
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 3px 4px 3px rgba(238, 238, 238, 1);
        margin-bottom: 10px;
    }

    .proh {
        width: 100%;
        height: 30px;
    }

    .hideaccordion,.showaccordion {
        float: left;
        height: 18px;
        line-height: 18px;
        position: relative;
        padding: 6px;
    }

    .hideaccordion h1,.showaccordion h1 {
        font-size: 14px;
        font-weight: bold;
        color: #444;
    }

    .hideaccordion h1 i {
        cursor: pointer;
    }

    .probody {
        width: 100%;
        height: 100%;
        padding: 0 10px;
    }

    .pic-main {
        padding: 5px;
        border: 1px solid #ccc;
    }

    .pic-main li {
        margin: 5px;
        padding: 0px;
        border: 0px;
        width: 102px;
        text-align: right;
    }
    /***Validform的样式--su20141125***/
    .Validform_checktip {
        margin-left: 8px;
        line-height: 20px;
        height: 20px;
        overflow: hidden;
        color: #999;
        font-size: 12px;
    }
    /*.Validform_right{color:#71b83d;padding-left:20px;background:url(images/right.png) no-repeat left center;}
    .Validform_wrong{color:red;padding-left:20px;white-space:nowrap;background:url(images/error.png) no-repeat left center;}
    .Validform_loading{padding-left:20px;background:url(images/onLoad.gif) no-repeat left center;}*/
    .Validform_error {
        background-color: #ffe7e7;
    }

    #Validform_msg {
        color: #7d8289;
        font: 12px/1.5 tahoma, arial, \5b8b\4f53, sans-serif;
        width: 280px;
        background: #fff;
        position: absolute;
        top: 0px;
        right: 50px;
        z-index: 99999;
        display: none;
        filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135,
        Color='#999999');
        -webkit-box-shadow: 2px 2px 3px #aaa;
        -moz-box-shadow: 2px 2px 3px #aaa;
    }

    #Validform_msg .iframe {
        position: absolute;
        left: 0px;
        top: -1px;
        z-index: -1;
    }

    #Validform_msg .Validform_title {
        line-height: 25px;
        height: 25px;
        text-align: left;
        font-weight: bold;
        padding: 0 8px;
        color: #fff;
        position: relative;
        background-color: #000;
    }

    #Validform_msg a.Validform_close:link,#Validform_msg a.Validform_close:visited
    {
        line-height: 22px;
        position: absolute;
        right: 8px;
        top: 0px;
        color: #fff;
        text-decoration: none;
    }

    #Validform_msg a.Validform_close:hover {
        color: #cc0;
    }

    #Validform_msg .Validform_info {
        padding: 8px;
        border: 1px solid #000;
        border-top: none;
        text-align: left;
    }
</style>


<form action="" class="form-horizontal validate_form" method="post">
    <!-- 类目选择 -->
    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i> <span>&nbsp;站点信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label class="col-sm-2 control-label">请选择站点：</label>
                    <div class="col-sm-10">
                        <select name="site_id" id="site_id">
                            <option value="all">==请选择站点==</option>
                            <?php foreach($site_list as $site):?>
                                <option value="<?php echo $site['siteid'];?>"><?php echo $site['site'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="Clearfix"></div>
            </div>
            <div class="promsg" style="display: none;">站点信息</div>
        </div>
    </div>

    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i> <span>&nbsp;第一分类</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label for="category_keyword" class="control-label col-sm-2">关键词：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="category_keyword" placeholder="关键词" name="category_keyword">
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-default btn-sm command_btn">推荐类目</a>
                        <a class="btn btn-default btn-sm choose_btn" id="choose_btn1">本地选择</a>
                    </div>
                </div>
                <!-- 分类选择 -->
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2 clear-fix">
                        <div id="category_select">
                        </div>
                        <div class="category_ajax hidden" >
                            <select id="category_ajax_select_first" size="10">

                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">已选择分类：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" readonly="readonly" id="category_name"/>
                        <input type="hidden" id="category_id" name="category_id" datatype="*" nullmsg="必须选择分类" />
                    </div>
                </div>
                <div class="Clearfix"></div>
            </div>
            <div class="promsg" style="display: none;">第一分类信息</div>
        </div>
    </div>

    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-minus"></i> <span>&nbsp;第二分类</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt" style="display: none;">
                <div class="form-group">
                    <label for="category_keyword" class="control-label col-sm-2">关键词：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="category_keyword" placeholder="关键词" name="category_keyword">
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-default btn-sm command_btn">推荐类目</a>
                        <a class="btn btn-default btn-sm choose_btn" id="choose_btn2">本地选择</a>
                    </div>
                </div>
                <!-- 分类选择 -->
                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2 clear-fix">
                        <div id="category_select_second">
                        </div>
                        <div class="category_ajax hidden" >
                            <select id="category_ajax_select_second" size="10">

                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">已选择分类：</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" readonly="readonly" id="category_name_second"/>
                        <input type="hidden" id="category_id_second" name="category_id_second" datatype="*" nullmsg="必须选择分类" />
                    </div>
                </div>

                <div class="Clearfix"></div>
            </div>
            <div class="promsg" >第二分类信息</div>
        </div>
    </div>

    <div class="row row-border">
        <div class="form-gorup">
            <div class="col-sm-10 col-sm-offset-2" style="margin-bottom: 10px;">
                <button class="btn btn-primary btn-sm disabled" type="submit" id="category_btn">确定选择</button>
            </div>
        </div>
        </div>

</form>

<script type="text/javascript">
    $('.hideaccordion h1 i').click(function () {
        if (this.className == 'icon-plus') {
            this.className = 'icon-minus';
            $(this).parents('.row-border').children('.probody').children('.procnt').css('display', 'none');
            $(this).parents('.row-border').children('.probody').children('.promsg').css('display', '');
        } else {
            this.className = 'icon-plus';
            $(this).parents('.row-border').children('.probody').children('.procnt').css('display', '');
            $(this).parents('.row-border').children('.probody').children('.promsg').css('display', 'none');
        }
    });

    $(document).on('change', '.category_list', function(){

        var mark = $(this);
        var site = $("#site_id").val();
        var a_isleaf = null;
        var a_category_id = $(this).val();
        a_isleaf = $(this).find('option:selected').attr('lang');


        $(this).parents('.col-sm-2').nextAll().remove();
        //判断是否是末子叶,不是末子叶才异步
        if (a_isleaf ==''|| a_isleaf=='null' ){
            $.ajax({
                url: '<?php echo admin_base_url('ebay/ebay_product/select_category');?>',
                data: 'site='+site+'&level=nostart'+'&parentID='+a_category_id,
                type: 'GET',
                dataType: 'json',
                success:function(data){
                    var input = '<div class="col-sm-2 no-padding-left">';
                    input += '<select size="10" class="form-control category_list" multiple>';

                    $.each(data, function(index, el){
                        input += '<option value="'+el.CategoryID+'" lang="'+el.LeafCategory+'">'+el.CategoryName+'</option>';
                    });

                    input += '</select>';
                    input += '</div>';

                    mark.parent().parent().append(input);
                    //$('#category_select').append(input);
                }
            });
            if (!$('#category_btn').hasClass('disabled')){
                $('#category_btn').addClass('disabled');
            }
        }else{

            var choose = mark.parent().parent().attr('id');

            //末节点了，可以设置分类ID了
            var category_name = $("#"+choose).children().map(function(){
                return $(this).find('option:selected').text();
            }).get().join('>>');
            if(choose=='category_select'){
                $('#category_name').val(category_name);
                $('#category_id').val(a_category_id);
            }

            if(choose=='category_select_second'){
                $('#category_name_second').val(category_name);
                $('#category_id_second').val(a_category_id);
            }

            $('#category_btn').removeClass('disabled');
        }

    });


    $("#site_id").change(function(){
        var site = $("#site_id").val();
        if(site=='all'){
            if (!$('#category_btn').hasClass('disabled')){
                $('#category_btn').addClass('disabled');
            }
            return false;
        }

        if ($('#category_select').hasClass('hidden')){
            $('#category_select').removeClass('hidden');
        }//category_select_second

        if ($('#category_select_second').hasClass('hidden')){
            $('#category_select_second').removeClass('hidden');
        }//category_select_second

        if (!$('#category_ajax_select_first').parent().hasClass('hidden')){
            $('#category_ajax_select_first').parent().addClass('hidden');
        }
        if (!$('#category_ajax_select_second').parent().hasClass('hidden')){
            $('#category_ajax_select_second').parent().addClass('hidden');
        }
    //category_ajax
        $.ajax({
            url: '<?php echo admin_base_url('ebay/ebay_product/select_category');?>',
            data: 'site='+site+'&level=start',
            type: 'GET',
            dataType: 'json',
            success: function(data){
                if(data.status==2){
                    alert('出错了');
                }else{
                    $('#category_select').empty();
                    $('#category_select_second').empty();
                    $('#category_name').val('');
                    $('#category_id').val('');
                    $('#category_name_second').val('');
                    $('#category_id_second').val('');

                    var input = '<div class="col-sm-2 no-padding-left">';
                    input += '<select size="8" class="form-control category_list" multiple>';

                    $.each(data, function(index, el){
                        input += '<option value="'+el.CategoryID+'" lang="'+el.LeafCategory+'">'+el.CategoryName+'</option>';
                    });

                    input += '</select>';
                    input += '</div>';
                    $('#category_select').append(input);
                    $('#category_select_second').append(input);
                    if (!$('#category_btn').hasClass('disabled')){
                        $('#category_btn').addClass('disabled');
                    }
                }
            }
        });
    });


    $(".choose_btn").click(function(){
        var site = $("#site_id").val();
        if(site==''){
            return false;
        }
        var e =$(this);
       // alert(123);

        var id_name = $(this).attr("id");
        if(!e.parent().parent().next().children().eq(0).children().eq(1).hasClass("hidden")){
            e.parent().parent().next().children().eq(0).children().eq(1).addClass("hidden")
        }
     /*   if(e.parent().parent().next().children().eq(0).children().eq(0).hasClass("hidden")){
            e.parent().parent().next().children().eq(0).children().eq(0).remove("hidden")
        }*/


        $.ajax({
            url: '<?php echo admin_base_url('ebay/ebay_product/select_category');?>',
            data: 'site='+site+'&level=start',
            type: 'GET',
            dataType: 'json',
            success: function(data){
                if(data.status==2){
                    alert('出错了');
                }else{
                    var input = '<div class="col-sm-2 no-padding-left">';
                    input += '<select size="8" class="form-control category_list" multiple>';

                    $.each(data, function(index, el){
                        input += '<option value="'+el.CategoryID+'" lang="'+el.LeafCategory+'">'+el.CategoryName+'</option>';
                    });

                    input += '</select>';
                    input += '</div>';

                    if(id_name=='choose_btn1'){
                        $('#category_select').empty();
                        $('#category_name').val('');
                        $('#category_id').val('');
                        if($("#category_select").hasClass("hidden")){
                            $("#category_select").removeClass("hidden");
                        }

                        $('#category_select').append(input);

                        if (!$('#category_btn').hasClass('disabled')){
                            $('#category_btn').addClass('disabled');
                        }
                    }
                    if(id_name=='choose_btn2'){
                        $('#category_select_second').empty();
                        $('#category_name_second').val('');
                        $('#category_id_second').val('');
                        if($("#category_select_second").hasClass("hidden")){
                            $("#category_select_second").removeClass("hidden");
                        }

                        $('#category_select_second').append(input);
                    }
                }
            }
        });










    });


    $(".command_btn").click(function(){
        var keyword = $("#category_keyword").val();
        var site = $("#site_id").val();
        if(site==''){
            return false;
        }
        var e =$(this);
        $.ajax({
            url: '<?php echo admin_base_url('ebay/ebay_product/suggest_category');?>',
            data: 'site=' + site + '&keyword='+keyword,
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if(data.status==1){
                    e.parent().parent().next().children().eq(0).children().eq(1).children().eq(0).empty();
                    for(var i=0;i<data.data.length;i++){
                        if(data.data[i].category_name !=''){
                            var text_value =data.data[i].id+' :'+data.data[i].category_name+' ('+data.data[i].percentItemfound+'%)';

                            e.parent().parent().next().children().eq(0).children().eq(1).children().eq(0).append('<option value="'+data.data[i].id+'">'+text_value+'</option>');
                        }
                      }
                    e.parent().parent().next().children().eq(0).children().eq(0).addClass("hidden");
                    e.parent().parent().next().children().eq(0).children().eq(1).removeClass("hidden");
                    if (!$('#category_btn').hasClass('disabled')){
                        $('#category_btn').addClass('disabled');
                    }
                   // $("#category_select").addClass("hidden");
                    //$("#category_ajax_select").removeClass("hidden");
                }else{
                    alert('未找到建议分类');
                }

            }
        });

       // suggest_category


     //   alert(keyword);
    });


    $("#category_ajax_select_first").change(function(){
        var text_value = $("#category_ajax_select_first").find("option:selected").text();;
        var value = $("#category_ajax_select_first").val();
        $('#category_name').val(text_value);
        $('#category_id').val(value);
        if ($('#category_btn').hasClass('disabled')){
            $('#category_btn').removeClass('disabled');
        }
    });

    $("#category_ajax_select_second").change(function(){
        var text_value = $("#category_ajax_select_second").find("option:selected").text();;
        var value = $("#category_ajax_select_second").val();
        $('#category_name_second').val(text_value);
        $('#category_id_second').val(value);
        if ($('#category_btn').hasClass('disabled')){
            $('#category_btn').removeClass('disabled');
        }

    });

</script>
