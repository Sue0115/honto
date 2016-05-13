<?php
/**
 * 亚马逊自定义产品分类列表
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">亚马逊分类列表</h3>

        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                分类名称:
                                <input type="text" name="category" placeholder="模糊查询" value="<?php echo isset($params['category']) ? trim($params['category']) : ''; ?>"/>
                            </label>

                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    筛选
                                </button>
                            </label>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="5%"/>
                        <col/>
                        <col width="10%"/>
                        <col width="10%"/>
                        <col width="10%"/>
                        <col width="10%"/>
                        <col width="10%"/>
                        <col width="10%">
                        <col width="10%">
                        <col width="8%"/>
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
                        <th>category_us</th>
                        <th>category_ca</th>
                        <th>category_uk</th>
                        <th>category_fr</th>
                        <th>category_de</th>
                        <th>category_it</th>
                        <th>category_es</th>
                        <th>category_jp</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    if (!empty($categoryList)){
                        foreach ($categoryList as $row){
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
                                    <?php echo $row['category_us'];?>
                                </td>
                                <td>
                                    <?php echo $row['category_ca'];?>
                                </td>
                                <td>
                                    <?php echo $row['category_uk'];?>
                                </td>
                                <td>
                                    <?php echo $row['category_fr'];?>
                                </td>
                                <td>
                                    <?php echo $row['category_de'];?>
                                </td>
                                <td>
                                    <?php echo $row['category_it'];?>
                                </td>
                                <td>
                                    <?php echo $row['category_es'];?>
                                </td>
                                <td>
                                    <?php echo $row['category_jp'];?>
                                </td>
                                <td>
                                    <a title="编辑" href="<?php echo admin_base_url('amz/category/info?id='.$row['id']);?>"><i class="icon-edit bigger-150"></i></a>
                                    &nbsp;
                                    <a title="删除" href="javascript:" onclick="msgdelete(<?php echo $row['id'];?>, '<?php echo admin_base_url("amz/category/delete");?>');"><i class="icon-trash bigger-150 red"></i></a>
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