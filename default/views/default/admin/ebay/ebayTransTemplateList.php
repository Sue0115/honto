<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/30
 * Time: 14:10
 */

?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">Ebay刊登-运输模板</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <br/>
                            <label>
                                <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('ebay/ebay_product/transTemplateIndexAdd');?>">添加</a>
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
                        <th>运输模板名字</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($translist)):
                        foreach($translist as $item):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item['id'];?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $item['id'];?></td>
                                <td><?php echo $item['transtemplatename'];?></td>
                                <td>
                                    <div class="btn-group">
                                        <a title="修改" href="<?php echo admin_base_url('ebay/ebay_product/transTemplateIndexAdd?id='.$item['id']);?>" class="green">
                                            <i class="icon-pencil bigger-130"></i>
                                        </a>
                                        &nbsp;

                                        &nbsp;
                                        <a title="删除" href="javascript:void(0);" onclick="msgdelete(<?php echo $item['id'];?>, '<?php echo admin_base_url("ebay/ebay_product/deleteTransTemplate"); ?>')" class="red">
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
            </div>
        </div>
    </div>
</div>