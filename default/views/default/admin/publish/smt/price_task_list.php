<?php
/**
 * wish调价任务列表
 */
?>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>" xmlns="http://www.w3.org/1999/html"></script>

<div class="modal fade" id="myPriceModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >生成调价任务</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal"  >

                    <div class="form-group">
                        <label class="col-sm-2">账号:</label>
                        <div class="col-sm-6">
                    <select  id="selectaccount">
                        <option value="">请选择账号</option>
                        <?php foreach ($userInfo as $u):?>
                            <option value="<?php echo $u['token_id']?>"><?php echo $u['accountSuffix']?></option>
                        <?php endforeach;?>
                    </select>
                            </div>
                        </div>

                    <div class="form-group">
                        <label class="col-sm-2">产品分组:</label>
                        <div class="col-sm-6">
                            <select  id="groupId2" >
                                <option value="">=所有分组=</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group changetype">
                        <label class="col-sm-2">物流方式:</label>

                        <div class="col-sm-6">
                            <select  id="selectshipment">
                                <option value="">请选择物流</option>
                                <?php foreach ($shipment as $u):?>
                                    <option value="<?php echo $u['shipmentID'] ?>"><?php echo $u['shipmentID'].'-'.$u['shipmentTitle'] ?></option>
                                <?php  endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group changetype">
                        <label class="col-sm-2">粉、液、 电:</label>

                        <div class="col-sm-6">
                            <select  id="shipmentoption">
                                <option value="">请选择物流</option>
                                <?php foreach ($shipment as $u):?>
                                    <option value="<?php echo $u['shipmentID'] ?>"><?php echo $u['shipmentID'].'-'.$u['shipmentTitle'] ?></option>
                                <?php  endforeach; ?>
                            </select>
                        </div>
                    </div>


                    <div class="form-group changetype">
                        <label class="col-sm-2">利润率:</label>
                        <div class="col-sm-6">
                            <input type="text" class="control-label" id="percentage" size="10"/ >%
                        </div>
                    </div>




                    <div class="form-group">
                        <label class="col-sm-2">限价金额:</label>
                        <div class="col-sm-6">
                            <input type="checkbox" class="control-label" id="is_re_pirce" / >
                            <input type="text" class="control-label hidden" placeholder="输入现价金额" id="re_pirce" size="10"/>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <a href="#"   class="btn btn-primary " id="pricecheck">确定</a>
                        <!--<a href="#" class="btn btn-default" data-dismiss="modal">关闭</a>-->
                    </div>
                    <!--<button type="submit" class="btn btn-primary">提交</button>-->
                </form>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">SMT-调价任务列表</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <form action="" method="get">
                        <label>
                            账号:
                            <select name="search[token_id]" id="token_id">
                                <option value="">请选择账号</option>
                                <?php foreach ($userInfo as $u):?>
                                    <option value="<?php echo $u['token_id']?>"><?php echo $u['accountSuffix']?></option>
                                <?php endforeach;?>
                            </select>
                        </label>
                        <label>
                            <select name="search[stauts]" id="status">
                                <option value="">请选择调价状态</option>
                                <option value="1">未调价</option>
                                <option value="2">已调价</option>
                            </select>

                        </label>

                        <label>
                            <button class="btn btn-primary btn-sm" type="submit">筛选</button>
                        </label>
                        <label>
                            <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/smt/price_task_list');?>">清空</a>
                        </label>
                        <label>
                            <a class="btn btn-sm btn-primary batch_operate">
                                批量删除
                            </a>
                        </label>

                        <label>
                            <a class="btn btn-sm btn-primary " id="new_task">
                                生成调价任务
                            </a>
                        </label>

                        <label>
                            <a class="btn btn-sm btn-primary " id="do_task">
                                执行调价任务
                            </a>
                        </label>


               <!--         <label>
                            <a class="btn btn-sm btn-primary " id="export_task">
                                导出数据
                            </a>
                        </label>-->


                    </form>
                    <form action="<?php echo admin_base_url('publish/smt/export_task')?>" method="post" id="form">
                        <input type="text" name="selectid" id="selectid" class="hidden"/>
                        <label>
                            <a class="btn btn-primary btn-sm" id="export">导出数据</a>
                        </label>
                    </form>
                </div>
                <form action="" method="post" >
                    <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                        <colgroup>
                            <col width="3%">
                            <col width="12%">
                            <col width="12%">
                            <col width="14%"/>
                            <col width="6%"/>
                            <col width="6%"/>
                            <col width="6%"/>
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="6%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="center">
                                <label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </th>
                            <th>账号</th>
                      
                            <th>物流渠道</th>
                            <th>粉、液、电、物流渠道</th>
                            <th>调价幅度</th>
                            <th>限价金额</th>
                            <th>调价状态</th>
                            <th>创建时间</th>

                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data_list as $da):?>
                            <tr>
                                <td class="center">
                                    <input type="checkbox" name="ids[]" value="<?php echo $da->id?>" />
                                </td>
                                <td class="center"><?php echo $userInfoarr[$da->token_id]?></td>
                                <td class="center"><?php echo isset($shipmentarr[$da->shipment_id])?$shipmentarr[$da->shipment_id]:'' ?></td>
                                <td class="center"><?php echo isset($shipmentarr[$da->shipment_id_op])?$shipmentarr[$da->shipment_id_op]:'' ?></td>
                                <td class="center"><?php echo $da->percentage?>%</td>
                                <td class="center"><?php echo $da->re_pirce?></td>
                                <td class="center"><?php echo $price_status[$da->stauts]?></td>
                                <td class="center"><?php echo $da->create_time ?></td>


                                <td class="center">

                                    <a title="删除" class="a_delete" data-id="<?php echo $da->id;?>" style="cursor:pointer;">
                                        <i class="icon-trash bigger-130 red"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    </table>
                </form>
                <?php
                $this->load->view('admin/common/page_number');
                ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(function(){

        //单个删除
        $(".a_delete").click(function(){
            if (!confirm('确定要删除吗？')){
                return false;
            }
            var Ids = $(this).attr('data-id');
            $.ajax({
                url: '<?php echo admin_base_url("publish/smt/batch_delete_price");?>',
                data: 'Ids='+Ids,
                type: 'POST',
                dataType: 'json',

                success: function(data){
                    var str='';
                    if (data.data){
                        $.each(data.data, function(index, el){
                            str += el+';';
                        });
                    }
                    if (data.status) { //成功
                        showxbtips(data.info+str);
                    }else {
                        showxbtips(data.info+str, 'alert-warning');
                    }
                    window.location.reload();
                }

            });
            return false;

        });

        //批量审核通过
        $(".batch_operate").click(function(){
            if (confirm('确定要批量删除数据吗？')){
                var Ids = $('input[name="ids[]"]:checked').map(function() {
                    return $(this).val();
                }).get().join(',');
                if (Ids == ''){
                    alert('请勾选需要的数据');
                    return false;
                }

                $.ajax({
                    url: '<?php echo admin_base_url("publish/smt/batch_delete_price");?>',
                    data: 'Ids='+Ids,
                    type: 'POST',
                    dataType: 'json',

                    success: function(data){
                        var str='';
                        if (data.data){
                            $.each(data.data, function(index, el){
                                str += el+';';
                            });
                        }
                        if (data.status) { //成功
                            showxbtips(data.info+str);
                        }else {
                            showxbtips(data.info+str, 'alert-warning');
                        }
                        window.location.reload();
                    }


                });
                return false;
            }
        });
        $('#new_task').click(function(){
            $('#is_re_pirce').prop('checked',false);
            $('#re_pirce').val(0);
            $('#re_pirce').addClass('hidden');
            $('#myPriceModalSelect').modal('toggle');
        })

        $('#do_task').click(function(){
            if (confirm('确定要执行该条数据吗？')) {
                var Ids = $('input[name="ids[]"]:checked').map(function () {
                    return $(this).val();
                }).get().join(',');
                if (Ids == '') {
                    alert('请勾选需要的数据');
                    return false;
                }

                var ii = layer.load('执行中')
                $.ajax({
                    url: '<?php echo admin_base_url("publish/smt/getSmtPriceTask");?>',
                    data: 'do_task=yes&Ids='+Ids,
                    type: 'POST',
                    dataType: 'json',
                    success: function (data) {
                        layer.close(ii);
                    }
                });
            }
        })


        $('#is_re_pirce').click(function(){

            if($('#is_re_pirce').is(':checked'))
            {
                $('#re_pirce').removeClass('hidden');
            }
            else
            {
                $('#re_pirce').addClass('hidden');
            }
        })

        $('#pricecheck').click(function(){
            var token_id =$('#selectaccount').val();
            var selectshipment_id =$('#selectshipment').val();
            var shipment_op = $('#shipmentoption').val();
            var percentage =$('#percentage').val();
            var is_re_pirce = 'nopirce';
            var  re_pirce = 0;
            var groupId2 = $("#groupId2").val();
            if($('#is_re_pirce').is(':checked'))
            {
                 is_re_pirce = $('#is_re_pirce').val();
                 re_pirce =$('#re_pirce').val();
            }

            var  data= 'token_id='+token_id+'&selectshipment_id='+selectshipment_id+'&shipment_op='+shipment_op+'&percentage='+percentage+'&is_re_pirce='+is_re_pirce+'&re_pirce='+re_pirce+'&groupId2='+groupId2;


            var ii = layer.load('生成任务中。。。');
    //    alert(token_id+':'+selectshipment_id+':'+percentage+':'+is_re_pirce+':'+re_pirce)
            $.ajax({
                url: '<?php echo admin_base_url("publish/smt/creat_pirce_task");?>',
                data: data,
                type: 'POST',
                dataType: 'json',
                success: function(data){
                    layer.close(ii);
                }
            });
            $('#myPriceModalSelect').modal('toggle');

        })

        $("#export").click(function(){
                var Ids = $('input[name="ids[]"]:checked').map(function () {
                    return $(this).val();
                }).get().join(',');
                if (Ids == '') {
                    alert('请勾选需要的数据');
                    return false;
                }
                $('#selectid').val(Ids);

                $("#form").submit();
        });


        $(document).on('change', '#selectaccount', function(){
            var token_id = $(this).val();
            $('#groupId2').empty();
            if (token_id == ''){ //账号为空，隐藏分组信息
                return false;
            }else {
                //异步获取账号信息
                $.ajax({
                    url: '<?php echo admin_base_url("smt/smt_product/showAccountProductGroup");?>',
                    data: 'token_id='+token_id,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(data){
                        if (data.status){

                            $('#groupId2').append(data.data);
                        }
                    }
                });
            }
        });
    })

</script>