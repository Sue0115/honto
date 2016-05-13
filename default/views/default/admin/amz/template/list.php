<?php
/**
 * 亚马逊模板数据列表
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">亚马模板数据列表</h3>

        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                SKU:
                                <input type="text" name="sku" placeholder="模糊查询" value="<?php echo isset($params['sku']) ? trim($params['sku']) : ''; ?>"/>
                            </label>

                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    筛选
                                </button>
                            </label>

                            <br/>
                            <a class="btn btn-primary btn-sm" href="javascript:" onclick="exportToExcel();">导出到</a>
                            &nbsp;
                            <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('amz/template/info');?>" title="新增">新增</a>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="6%">
                        <col width="6%"/>
                        <col width="15%"/>
                        <col>
                        <col width="15%"/>
                        <col width="15%"/>
                        <col width="10%">
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
                        <th>SKU</th>
                        <th>标题</th>
                        <th>关键词</th>
                        <th>卖点</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($dataList)){
                        foreach ($dataList as $row){
                    ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $row['id'];?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $row['id'];?></td>
                                <td>
                                    <?php
                                    if (!empty($row['sku'])){
                                        echo str_replace(',', ', ', $row['sku']); //输出SKU
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($row['title'])){
                                        echo str_replace('-||-', '<br/>', $row['title']);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($row['keyword'])){
                                        $keywordList = explode(';', $row['keyword']);
                                        foreach ($keywordList as $key => $keyword){
                                            if (empty($keyword)) unset($keywordList[$key]);
                                        }
                                        echo implode('<br/>', $keywordList);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($row['bullet'])){
                                        $bulletList = explode('-||-', $row['bullet']);
                                        foreach ($bulletList as $key => $bullet){
                                            if (empty($bullet)) unset($bulletList[$key]);
                                        }
                                        echo implode('<br/>', $bulletList);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo admin_base_url('amz/template/info?id='.$row['id']);?>" title="编辑">
                                        <i class="icon-edit bigger-130"></i>
                                    </a>
                                    <!--&nbsp;&nbsp;
                                    <a href="javascrpt:" title="删除">
                                        <i class="icon-trash bigger-130 red"></i>
                                    </a>-->
                                </td>
                            </tr>
                    <?php
                        }
                    }
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
    function exportToExcel(){
        layer.msg('导出中...', 2, !1);
    }
</script>