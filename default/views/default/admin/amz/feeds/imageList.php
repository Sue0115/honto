<?php
/**
 * 亚马逊图片列表(本地的，需上传)
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">亚马逊产品图片上传列表</h3>

        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                账号:
                                <select name="token_id">
                                    <option value="">---全选---</option>
                                    <?php
                                    foreach ($tokenList as $token_id => $account){
                                        echo '<option value="' . $token_id . '" ' . ($params['token_id'] == $token_id ? 'selected="selected"' : '') . '>' . $account . '</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                            <label>
                                SKU:
                                <input type="text" name="sku" placeholder="模糊查询" value="<?php echo isset($params['sku']) ? trim($params['sku']) : ''; ?>"/>
                            </label>
                            <label>
                                上传结果:
                                <select name="callresult">
                                    <?php
                                    foreach ($callResult as $key => $text){
                                        echo '<option value="'.$key.'" '.($params['callresult'] == $key ? 'selected="selected"' : '').'>'.$text.'</option>';
                                    }
                                    ?>
                                </select>    
                            </label>
                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    筛选
                                </button>
                            </label>

                            <br/>
                            <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('amz/feeds/info');?>">添加产品图片</a>
                            <a class="btn btn-primary btn-sm" onclick="exportInData()">导入数据</a>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="10%">
                        <col width="8%"/>
                        <col width="20%">
                        <col/>
                        <col width="20%">
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
                        <th>账号</th>
                        <th>AMZ-SKU</th>
                        <th>上传结果</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if ($imageList):
                        foreach ($imageList as $item):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->id;?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $item->id;?></td>
                                <td><?php echo $tokenList[$item->token_id];?></td>
                                <td>
                                    <?php echo $item->sku;?>
                                </td>
                                <td>
                                    <?php echo $callResult[$item->callresult];?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a title="修改" href="<?php echo admin_base_url('amz/feeds/info?id=' . $item->id);?>" class="green">
                                            <i class="icon-pencil bigger-130"></i>
                                        </a>
                                        &nbsp;
                                        <?php if ($item->callresult == '0'):?>
                                        <a title="作废" href="javascript: void(0);" onclick="trash_product(<?php echo $item->id;?>)" class="red">
                                            <i class="icon-trash bigger-110"></i>
                                        </a>
                                        <?php endif;?>
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
<script type="text/javascript">
    /**
     * 删除产品
     * @param id
     */
    function trash_product(id){
        if (!id){
            showtips('数据不存在，请刷新');
            return false;
        }

        if (!confirm('确定要将这条数据作废吗？')){
            return false;
        }

        $.ajax({
            url: '<?php echo admin_base_url('amz/feeds/trash')?>',
            data: 'id='+id,
            type: 'POST',
            dataType: 'JSON',
            success: function(data){
                if (data.status){
                    showtips(data.info+data.data, 'alert-success');
                    window.location.reload();
                }else {
                    showtips(data.info+data.data, 'alert-warning');
                }
            }
        });
    }

    /**
     * 导入产品数据
     */
    function exportInData(){
        $.layer({
            type: 2,
            shadeClose: true,
            title: '导入产品图片数据',
            closeBtn: [0, true],
            shade: [0.8, '#000'],
            border: [0],
            offset: ['',''],
            area: ['450px', '300px'],
            iframe: {src: '<?php echo admin_base_url('amz/feeds/exportIn')?>'}
        });
    }
</script>