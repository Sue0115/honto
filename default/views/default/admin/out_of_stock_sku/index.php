<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-04
 * Time: 09:49
 */
?>

<style>
    .tishi {
        cursor: pointer;
    }

    .yeanse {
        background-color: #00ffff;
        cursor: pointer;
    }
</style>

<div class="row" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">SKU缺货中心</h3>

        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="post" enctype="multipart/form-data" >


                            <label>
                                SKU:
                                <input type="text" id="sku" name="search[sku]" placeholder="请输入SKU"
                                       value="<?php if (isset($search['sku']) && !empty($search['sku'])) echo $search['sku']; ?>">
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;

                            <label>
                                缺货原因:
                                <select name="search[reason]" id="reason">
                                    <option value="">==全部原因==</option>
                                    <option value="1"  <?php  if(isset($search['reason']) && $search['reason'] == 1 )  echo 'selected="selected"';    ?>>采不到</option>
                                    <option value="2"  <?php  if(isset($search['reason']) && $search['reason'] == 2 )  echo 'selected="selected"';    ?>>有起订量</option>
                                    <option value="3"  <?php  if(isset($search['reason']) && $search['reason'] == 3 )  echo 'selected="selected"';    ?>>暂时涨价</option>
                                    <option value="4"  <?php  if(isset($search['reason']) && $search['reason'] == 4 )  echo 'selected="selected"';    ?>>其他</option>
                                </select>
                            </label>

                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <label>
                                状态:
                                <select name="search[status]" id="status">
                                    <option value="1"  <?php  if(isset($search['status']) && $search['status'] == 1 )  echo 'selected="selected"';    ?> >新申请</option>
                                    <option value="2"  <?php  if(isset($search['status']) && $search['status'] == 2 )  echo 'selected="selected"';    ?> >已确认</option>
                                    <option value="3"  <?php  if(isset($search['status']) && $search['status'] == 3 )  echo 'selected="selected"';    ?> >驳回</option>
                                    <option value="4"  <?php  if(isset($search['status']) && $search['status'] == 4 )  echo 'selected="selected"';    ?> >重新申请</option>
                                </select>
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;

                            <label>
                                SKU状态:
                                <select name="search[products_status_2]" id="products_status_2">
                                    <option value=""  <?php  if(!isset($search['products_status_2']))  echo 'selected="selected"';    ?> >==全部状态==</option>
                                    <option value="selling"  <?php  if(isset($search['products_status_2']) && $search['products_status_2'] == 'selling' )  echo 'selected="selected"';    ?> >在售</option>
                                    <option value="sellWaiting"  <?php  if(isset($search['products_status_2']) && $search['products_status_2'] == 'sellWaiting' )  echo 'selected="selected"';    ?> >待售</option>
                                    <option value="stopping"  <?php  if(isset($search['products_status_2']) && $search['products_status_2'] == 'stopping' )  echo 'selected="selected"';    ?> >停产</option>
                                    <option value="saleOutStopping"  <?php  if(isset($search['products_status_2']) && $search['products_status_2'] == 'saleOutStopping' )  echo 'selected="selected"';    ?> >卖完下架</option>
                                    <option value="unSellTemp"  <?php  if(isset($search['products_status_2']) && $search['products_status_2'] == 'unSellTemp' )  echo 'selected="selected"';    ?> >货源待定</option>
                                    <option value="trySale"  <?php  if(isset($search['products_status_2']) && $search['products_status_2'] == 'trySale' )  echo 'selected="selected"';    ?> >试销(卖多少采多少)</option>
                                </select>
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;


                            <label class="">
                                <button class="btn btn-primary btn-sm " name="check" type="submit">筛选</button>

                                &nbsp;&nbsp; &nbsp;&nbsp;

                                <a class="btn btn-primary btn-sm"
                                   href="<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/index"); ?>">重置</a>
                                &nbsp;&nbsp; &nbsp;&nbsp;




                            </label>
                            <label>
                            <button class="btn btn-primary btn-sm " name="exportfirst" type="submit">导出</button>
                            </label>
                            <div class="row">

                                <div class="col-sm-2">

                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle center" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        批量操作 <span class="caret"></span>

                                    </button>
                                    <ul id="batchdo" class="dropdown-menu">
                                        <li value="2"><a style="cursor: pointer">批量确认</a></li>
                                        <li value="3"><a style="cursor: pointer">批量删除</a></li>
                                        <li value="6"><a style="cursor: pointer">全部确认</a></li>
                                        <li value="7"><a style="cursor: pointer">全部删除</a></li>

                                    </ul>
                                    </div>
                                    <div class="col-sm-8">
                                        <label>
                                            <input type="file" id="file" name="excelFile" class="btn btn-primary btn-sm">
                                        </label>
                                        <label>
                                            <input type="submit" value="导入数据" id="sub"  name="export" class="btn btn-sm btn-primary"/>
                                        </label>

                                        <label><a  target="_blank" href="<?php echo base_url('attachments/template/export_out_of_sku.xls');?>"><span class="w-40-h-20">导入模版格式下载</span></a></label>
                                        <label><?php
                                             if(isset($export))
                                             {
                                                 echo '导入完成'.$export;
                                             }
                                            ?>
                                        </label>
                                    </div>

                                </div>
                            <table class="table    dataTable " id="tbody_content">
                                <colgroup>
                                    <col width="5%">
                                    <col width="5%">
                                    <col width="7%">
                                    <col width="8%">
                                    <col width="10%"/>
                                    <col width="10%"/>
                                    <col width="10%">
                                    <col width="20%">
                                    <col width="25%">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th class="center">
                                        <label>
                                            <input type="checkbox" class="ace"/>
                                            <span class="lbl"></span>
                                        </label>
                                    </th>
                                    <th class="center">SKU</th>
                                    <th class="center">欠货数量</th>
                                    <th class="center">状态</th>
                                    <th class="center">缺货原因</th>
                                    <th class="center">录入时间</th>
                                    <th class="center">状态</th>
                                    <th class="center">备注</th>
                                    <th class="center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $product_stauts= array(
                                    'selling'=>'在售',
                                    'sellWaiting'=>'待售',
                                    'stopping'=>'停产',
                                    'saleOutStopping'=>'卖完下架',
                                    'unSellTemp'=>'货源待定',
                                    'trySale'=>'试销(卖多少采多少)',
                                );


                                if ($data):
                                foreach ($data as $list):
                                ?>
                                <tr class="center">
                                    <td class="center">
                                        <label>
                                            <input type="checkbox" class="ace" name="ids[]" value="<?php echo $list->sku;?>">
                                            <span class="lbl"></span>
                                        </label>
                                    </td>

                                    <td>
                                       <?php echo $list->sku; ?>
                                    </td>

                                    <td>
                                        <?php echo !empty($list->skuCount)?$list->skuCount:0; ?>
                                    </td>

                                    <td>
                                        <?php echo $product_stauts[$list->products_status_2]; ?>
                                    </td>
                                    <td>
                                        <?php echo  $reason[$list->reason]; ?>
                                    </td>


                                    <td>
                                        <?php  echo  $list->exort_time;  ?>
                                    </td>


                                    <td>
                                        <?php  echo $status[$list->status];  ?>
                                    </td>


                                    <td>
                                        <?php  echo $list->remark;  ?>
                                    </td>

                                    <td>
                                        <span class="hidden"><?php echo $list->status;?></span>
                                        <a class="changeStatus del btn btn-primary btn-sm " title="删除申请">删除</a>
                                        <?php
                                        if($fenzu != 51)
                                        {
                                        ?>
                                        <a class="changeStatus comfirm btn btn-primary btn-sm" title="确定申请">确认</a>
                                        <a class="changeStatus reject btn btn-primary btn-sm" title="驳回申请">驳回</a>
                                            <?php
                                            }else{
                                            ?>
                                        <a class="changeStatus recomfirm btn btn-primary btn-sm" title="重新申请">重申</a>
                                            <?php
                                        }
                                        ?>
                                        <a class="changeStatus log btn btn-primary btn-sm" title="查看日志" >日志</a>
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
                            <?php
                            if($fenzu !=51)
                            {
                            ?>
                            <label class="">
                                <button class="btn btn-primary btn-sm " name="getorder"  type="submit">查看关联订单</button>
                            </label>
                            <?php
                            }
                            ?>
                        </form>


                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<script src="<?php echo static_url('theme/common/layer/layer.js') ?>"></script>
<script type="text/javascript">
    $(".changeStatus").click(function(){
        var mark  = $(this).parent().parent();
        var mark1 = $(this).parent().parent().children().eq(6);
        var mark2 = $(this).parent().children().eq(0);
        var sku =  $(this).parent().parent().children().eq(1).text();
        var status = $(this).parent().children().eq(0).text();
       // alert(status);
       // return false;

        var  type = 0;
        if($(this).hasClass('del')){
            if(confirm("确认删除"))
            {

            }else{
                return false;
            }
            type =1;
        }
        if($(this).hasClass('comfirm')){
            type =2;
            if(status==2){
                alert(" 已经为确认状态");
                return false;
            }
        }
        if($(this).hasClass('reject')){
            type =3;
            if(status==3){
                alert("已经为驳回状态")
                return false;
            }
        }
        if($(this).hasClass('recomfirm')){
            type =4;
            if(status==4){
                alert("已经重新申请回状态")
                return false;
            }
            if(status !=3){
                alert("不为驳回状态，无须重新申请");
                return false;
            }
        }
        if($(this).hasClass('log')){
            type =5;
        }
        if((type==1)||(type==2)||(type==3)||(type==4))
        {
            $.ajax({
                url: '<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/change_stauts");?>',
                data: 'sku=' + sku + '&type=' + type,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(data.status==1){
                        if(data.data.type==1){
                            alert(data.data.info);
                            $(mark).remove();
                        }
                        if(data.data.type==2){
                            $(mark1).text("已确认");
                            $(mark2).text(2);
                            alert(data.data.info);
                        }
                        if(data.data.type==3){
                            $(mark1).text("驳回");
                            $(mark2).text(3);
                            alert(data.data.info);
                        }
                        if(data.data.type==4){
                            $(mark1).text("重新申请");
                            $(mark2).text(4);
                            alert(data.data.info);
                        }
                    }else{
                        alert(data.data.info);
                    }

                }
            });
        }else{
            $.layer({
                type: 2,
                title: ['缺货SKU日志', true],
                shade: [0.8, '', true],
                iframe: {src: '<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/showlog?sku="); ?>'+sku},
                area: ['600px', '400px'],
                success: function () {
                    layer.shift('top', 340)
                }
              //  btns: 2
              //  btn: ['确定', '取消'],

              /*  yes: function (index) { //确定按钮的操作

                },
                no: function (index) {
                    layer.close(index);
                }*/
            });
        }



    });
    $('#batchdo li').click(function () {
        var index = $(this).val();
        var skus = $('input[name="ids[]"]:checked').map(function () {
            return $(this).val();
        }).get().join(',');
        if (skus == '') {
            if(index !=7){
                alert('请先选择数据');
                return false;
            }
        }
        var index = $(this).val();

        if (index == 2) {
            if (confirm("确认批量确认吗？")) {
                $.ajax({
                    url: '<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/batch_confirm");?>',
                    data: 'skus=' + skus,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        alert(data.info);
                        location.reload();
                    }
                });
            }
        }

        if (index == 3) {
            if (confirm('确认批量删除？')) {
                $.ajax({
                    url: '<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/batch_delete");?>',
                    data: 'skus=' + skus,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        alert(data.info);
                        location.reload();
                    }
                });
            }
        }

        if (index == 6) {
            if (confirm("确认全部确认吗？")) {
                $.ajax({
                    url: '<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/all_confirm");?>',
                    data: 'skus=' + skus,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        alert(data.info);
                        location.reload();
                    }
                });
            }
        }

        if (index == 7) {
            var sku = $("#sku").val();
            var reason = $("#reason").val();
            var status = $("#status").val();
            var products_status_2 = $("#products_status_2").val();
            var  string = 'SKU：'+sku+' 原因:'+$("#reason").find("option:selected").text()+' 状态:'+$("#status").find("option:selected").text()+' SKU状态:'+$("#products_status_2").find("option:selected").text();
            if (confirm("全部删除"+string)) {
               $.ajax({
                    url: '<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/all_detele");?>',
                    data: 'sku='+ sku+'&reason='+reason+'&status='+status+'&products_status_2='+products_status_2,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        alert(data.info);
                        location.reload();
                    }
                });
            }
        }


    });

</script>