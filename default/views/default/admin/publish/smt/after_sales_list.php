<?php
/**
 * 售后模板列表
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">售后模板列表</h3>

        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                平台:
                                <select name="plat" id="plat">
                                    <option value="">---全选---</option>
                                    <?php
                                    foreach ($plat_list as $key => $plat):
                                        echo '<option value="' . $key . '" ' . ($plat_selected == $key ? 'selected="selected"' : '') . '>' . $plat . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <?php
                            if ($plat_selected == 6) {
                                ?>
                                <label>
                                    账号:
                                    <select name="token_id" id="token_id">
                                        <option value="">--全选--</option>
                                        <?php
                                        foreach ($token_list as $key => $token):
                                            echo '<option value="' . $key . '" ' . ($token_id == $key ? 'selected="selected"' : '') . '>' . $key.'-'.$token['accountSuffix'] . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                </label>
                            <?php
                            }
                            ?>
                            <label>
                                模板名称:
                                <input type="text" name="name" placeholder="模糊查询" value="<?php echo $name; ?>"/>
                            </label>
                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    筛选
                                </button>
                            </label>
                            <br/>

                            <label>
                                <a class="btn btn-primary btn-sm"
                                   href="<?php echo admin_base_url('publish/after_sales_service/info'); ?>">添加</a>
                            </label>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="10%"/>
                        <col width="15%">
                        <col>
                        <col width="15%"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace"/>
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>ID</th>
                        <th>平台</th>
                        <th>账号</th>
                        <th>模板名称</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($template_list):
                        foreach ($template_list as $item):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->id;?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $item->id;?></td>
                                <td><?php echo $plat_list[$item->plat];?></td>
                                <td>
                                    <?php
                                    if ($item->plat == 6) {

                                         $array_list=  filterData($item->token_id, $token_list);
                                        echo $array_list['accountSuffix'];
                                    }
                                    ?>
                                </td>
                                <td><?php echo $item->name;?></td>
                                <td>
                                    <div class="btn-group">
                                        <a title="修改"
                                           href="<?php echo admin_base_url('publish/after_sales_service/info?id=' . $item->id);?>"
                                           class="green">
                                            <i class="icon-pencil bigger-130"></i>
                                        </a>
                                        &nbsp;
                                        <!--
                                        <a title="复制" href="javascript: void(0);" onclick="copyRow(<?php echo $item->id;?>, '<?php echo admin_base_url("publish/after_sales_service/copy");?>')" class="blue">
                                            <i class="icon-share-alt bigger-110"></i>
                                        </a>
                                        -->
                                        &nbsp;
                                        <a title="删除" href="javascript:void(0);"
                                           onclick="msgdelete(<?php echo $item->id;?>, '<?php echo admin_base_url("publish/after_sales_service/delete"); ?>')"
                                           class="red">
                                            <i class="icon-trash bigger-130"></i>
                                        </a>
                                    </div>
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
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        //联动获取账号
        $(document).on('change', '#plat', function () {
            var plat = $(this).val();

            if (!plat) {
                $('#token_id').closest('label').remove();
                return false;
            } else if (plat == 6) {
                $.ajax({
                    url: '<?php echo admin_base_url("publish/after_sales_service/ajaxGetTokenList");?>',
                    data: 'plat=' + plat,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        console.log(data.data);
                        if (data.status) {
                            var input = '<label>账号' +
                                '<select name="token_id" id="token_id">';
                            var options = '<option>--请选择--</option>'; //选项可以公用

                            $.each(data.data, function (index, el) {
                                options += '<option value="' + el.token_id + '">' + el.token_id + '-' + el.seller_account + '</option>';
                            });

                            if ($('#token_id').length > 0) { //说明输入框已经存在了
                                $('#token_id').empty().append(options);
                            } else {
                                input += options;
                                input += '</label>';
                                $('#plat').closest('label').after(input);
                            }
                        } else {
                            $('#token_id').closest('label').remove();
                            showxbtips(data.info, 'alert-warning');
                        }
                    }
                });
            } else {
                $('#token_id').closest('label').remove();
            }
        });
    })
</script>