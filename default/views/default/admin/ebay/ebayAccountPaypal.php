<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/11
 * Time: 10:19
 */
?>

<div class="modal fade" id="myModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >账号添加</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" onsubmit="return false;">

                    <div class="form-group">
                        <label  class="col-sm-2 control-label"></label>
                        <div class="col-sm-8">
                            <input type="text"  class="form-control hidden" id="id" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label  class="col-sm-2 control-label">账号：</label>
                        <div class="col-sm-8">
                            <input type="text"  class="form-control" id="account"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label"></label>
                        <div class="col-sm-8">
                         <span class="red">paypal之间用,隔开</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label">PAYPAL：</label>
                        <div class="col-sm-8">
                            <input type="text"  class="form-control"  id="paypal"/>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <a href="#"   class="btn btn-primary " id="accountcheck">确定</a>
                        <a href="#" class="btn btn-default" data-dismiss="modal">关闭</a>
                        <!--<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>-->
                        <!--<button  class="btn btn-primary " id="categoryselectsub"  >确定</button>
                         <button  class="btn btn-default" data-dismiss="modal">关闭</button>-->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">Ebay刊登-账号与PAYPAL</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <br/>
                            <label>
                                <a  class="btn btn-primary " href="<?php echo admin_base_url("ebay/ebay_template/add_paypal"); ?>"  >账号添加</a>
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
                        <col width="10%" >
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
                        <th>Ebay账号</th>
                        <th>PAYPAL账号</th>
                        <th>账号后缀</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($paypal_list):
                        foreach($paypal_list as $item):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item['id'];?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $item['id'];?></td>
                                <td><?php echo $item['ebay_account'];?></td>
                                <td><?php echo $item['paypal_account'];?></td>
                                <td><?php echo $item['account_suffix'];?></td>
                                <td>
                                    <div class="btn-group">
                                        <a title="修改"  href="<?php echo admin_base_url("ebay/ebay_template/add_paypal?id=".$item['id']); ?>"  class="green">
                                            <i class="icon-pencil bigger-130"></i>
                                        </a>
                                        &nbsp;

                                        &nbsp;
                                        <a title="删除" href="javascript:void(0);" onclick="msgdelete(<?php echo $item['id'];?>, '<?php echo admin_base_url("ebay/ebay_template/deleteAccountPaypal"); ?>')" class="red">
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
<script>

</script>