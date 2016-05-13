<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/18
 * Time: 14:38
 */

?>

<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">数据导出-订单下载</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                订单类型:
                                <select name="search[order_type]" id="order_type">
                                    <option value="">---全选---</option>
                                    <?php

                                    foreach($order_type as $key=> $v)
                                    {
                                        echo '<option  value='.$key.'  '.($search['order_type'] == $key ? 'selected="selected"': '').'>'.$v.'</option>';
                                    }

                                    ?>
                                </select>
                            </label>

                            <label>
                                仓库:
                                <select name="search[warehouse_id]" id="warehouse_id">
                                    <option value="">---全选---</option>
                                    <option value="1000" <?php  if(isset($search['warehouse_id'])&&($search['warehouse_id']==1000)) echo   'selected="selected"';     ?> >深圳仓</option>
                                    <option value="1025" <?php  if(isset($search['warehouse_id'])&&($search['warehouse_id']==1025)) echo   'selected="selected"';     ?> >义乌仓</option>
                                </select>
                            </label>



                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">筛选</button>
                            </label>

                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="5%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="25%">
                        <col width="10%">
                        <col width="5%"/>
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
                        <th>文件名</th>
                        <th>平台</th>
                        <th>仓库</th>
                        <th>订单时间范围</th>

                        <th>生成时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($data):
                        foreach($data as $list):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $list->id;?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $list->id;?></td>
                                <td><?php echo $list->file_name; ?></td>

                                <td><?php
                                    echo isset($order_type[$list->order_type])?$order_type[$list->order_type]:$list->order_type;
                                    ?></td>
                                <td><?php
                                    if($list->warehouse_id==1000)
                                    {
                                        echo '深圳仓';
                                    }

                                    if($list->warehouse_id==1025)
                                    {
                                        echo  '义乌仓';
                                    }

                                    ?></td>
                                <td><?php echo $list->order_from_date.'----'.$list->order_to_date; ?></td>
                                <td><?php echo $list->creat_time; ?></td>
                                <td class="center"><a href="<?php echo site_url($list->file_url);?>" class="files">下载</a></td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    </tbody>
                </table>
                <?php  $this->load->view('admin/common/page_number');?>

            </div>
        </div>
    </div>
</div>
<script>


</script>