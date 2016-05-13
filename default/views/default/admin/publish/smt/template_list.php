<?php
/**
 * 刊登模板列表页
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">速卖通刊登-模板列表</h3>
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
                                    foreach($plat_list as $key => $plat):
                                        echo '<option value="'.$key.'" '.($plat_selected == $key ? 'selected="selected"': '').'>'.$plat.'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <label>
                                模板名称:
                                <input type="text" name="name" placeholder="模糊查询" value="<?php echo $name;?>"/>
                            </label>
                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    筛选
                                </button>
                            </label>
                            <br/>

                            <label>
                                <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/smt_template/info');?>">添加</a>
                            </label>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="8%"/>
                        <col width="15%">
                        <col width="10%">
                        <col>
                        <col width="10%"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>ID</th>
                        <th>效果图</th>
                        <th>平台</th>
                        <th>模板名称</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($template_list):
                        foreach($template_list as $item):
                        ?>
                        <tr>
                            <td class="center">
                                <label>
                                    <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->id;?>">
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td><?php echo $item->id;?></td>
                            <td>
                                <a href="<?php echo site_url().'attachments/upload'.$item->pic_path;?>" target="_blank">
                                    <img width="50" height="50" src="<?php echo site_url().'attachments/upload'.$item->pic_path;?>" alt="<?php echo $item->name;?>" title="<?php echo $item->name;?>"/>
                                </a>
                            </td>
                            <td><?php echo $plat_list[$item->plat];?></td>
                            <td><?php echo $item->name;?></td>
                            <td>
                                <div class="btn-group">
                                    <a title="修改" href="<?php echo admin_base_url('publish/smt_template/info?id='.$item->id);?>" class="green">
                                        <i class="icon-pencil bigger-130"></i>
                                    </a>
                                    &nbsp;
                                    <a title="复制" href="javascript: void(0);" onclick="copyRow(<?php echo $item->id;?>, '<?php echo admin_base_url("publish/smt_template/copy");?>')" class="blue">
                                        <i class="icon-share-alt bigger-110"></i>
                                    </a>
                                    &nbsp;
                                    <a title="删除" href="javascript:void(0);" onclick="msgdelete(<?php echo $item->id;?>, '<?php echo admin_base_url("publish/smt_template/delete"); ?>')" class="red">
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