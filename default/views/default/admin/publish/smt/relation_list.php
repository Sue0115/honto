<?php
/**
 * 自定义关联产品模块列表(目前不区分平台)
 * sw20150304
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">速卖通刊登-自定义关联产品模板列表</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <!--<label>
                                平台:
                                <select name="plat" id="plat">
                                    <option value="">---全选---</option>
                                    <?php
                                    //foreach($plat_list as $key => $plat):
                                    //    echo '<option value="'.$key.'" '.($plat_selected == $key ? 'selected="selected"': '').'>'.$plat.'</option>';
                                    //endforeach;
                                    ?>
                                </select>
                            </label>-->
                            <label>
                                账号:
                                <select name="token_id" style="width: 150px;">
                                    <option value="">=所有账号=</option>
                                    <?php
                                    if (!empty($token_list)){
                                        foreach($token_list as $tid => $account){
                                            echo '<option value="'.$tid.'" '.($tid == $token_id ? 'selected="selected"' : '').'>'.$tid.'-'.$account.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </label>
                            <label>
                                名称:
                                <input type="text" name="name" placeholder="模糊查询" value="<?php echo $name;?>"/>
                            </label>
                            <label>
                                状态:
                                <select name="status">
                                    <option value="">全部</option>
                                    <?php
                                    foreach ($status_list as $key => $text):
                                        echo '<option value="'.$key.'" '.($status != '' && $status == $key ? 'selected="selected"' : '').'>'.$text.'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">
                                    筛选
                                </button>
                            </label>
                            <br/>

                            <label>
                                <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('publish/relation/info');?>">添加</a>
                            </label>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="10%">
                        <col>
                        <col width="10%"/>
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
                        <th>账号</th>
                        <th>模板名称</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($relation_list):
                        foreach($relation_list as $item):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->id;?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $item->id;?></td>
                                <td><?php echo $token_list[$item->token_id];?></td>
                                <td><?php echo $item->name;?></td>
                                <td>
                                    <label>
                                        <input type="checkbox" class="ace ace-switch ace-switch-6" name="status[]" item_id="<?php echo $item->id?>" value="<?php echo $item->status?>" <?php if($item->status):?>checked="checked"<?php endif;?> >
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a title="修改" href="<?php echo admin_base_url('publish/relation/info?id='.$item->id);?>" class="green">
                                            <i class="icon-pencil bigger-130"></i>
                                        </a>
                                        &nbsp;
                                        <a title="复制" href="javascript: void(0);" onclick="copyRow(<?php echo $item->id;?>, '<?php echo admin_base_url("publish/relation/copy");?>')" class="blue">
                                            <i class="icon-share-alt bigger-110"></i>
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