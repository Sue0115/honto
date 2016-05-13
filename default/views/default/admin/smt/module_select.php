<?php
/**
 * 产品信息模板-选择
 */
?>
<div class="row">
    <div class="col-xs-12">

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                <input type="text" name="module_name" value="<?php echo $module_name;?>"/>
                                <input type="hidden" name="token_id" value="<?php echo $token_id;?>"/>
                                <input type="hidden" name="single" value="<?php echo $single;?>"/>
                            </label>
                            <label>
                                <button class="btn btn-primary btn-sm" title="搜索">
                                    <i class="icon-search"></i>
                                </button>
                            </label>
                            <label>
                                <a class="btn btn-primary btn-sm" id="module_synchronization" title="同步">
                                    <i class="icon-refresh"></i>
                                </a>
                            </label>
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-hover dataTable">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>id</th>
                        <th>信息模板名称</th>
                        <th>模板类型</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($module as $m):?>
                        <tr>
                            <?php if ($single == 1):?>
                                <td><input type="radio" name="module_id" class="checkbox" value="<?php echo $m['module_id'];?>" title="<?php echo $m['module_name'];?>" lang="<?php echo $m['module_type'];?>"/></td>
                            <?php else:?>
                                <td><input type="checkbox" class="checkbox" value="<?php echo $m['module_id'];?>" title="<?php echo $m['module_name'];?>" lang="<?php echo $m['module_type'];?>"/></td>
                            <?php endif;?>
                            <td><?php echo $m['id'];?></td>
                            <td><?php echo $m['module_name'];?></td>
                            <td><?php echo $m['module_type'];?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
                <?php $this->load->view('admin/common/page_number'); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $(document).on('click', '#module_synchronization', function(event) {
            /* Act on the event */
            if (confirm('确定要同步吗?')) {
                $.ajax({
                    url:'<?php echo admin_base_url("smt/smt_product/getProductModuleList");?>',
                    type:'POST',
                    dataType:'json',
                    data:'token_id=<?php echo $token_id;?>',
                    beforeSend:function(){
                        $('#module_synchronization').attr('title', '同步中...').addClass('disabled');
                    },
                    success:function(data){
                        if (data.status) {
                            showxbtips(data.info);
                            window.location.href = '<?php echo admin_base_url("smt/smt_product/moduleSelect?token_id=".$token_id);?>';
                        }else {
                            showxbtips(data.info, 'alert-warning');
                        }
                    },
                    complete:function(){
                        $('#module_synchronization').attr('title', '同步').removeClass('disabled');
                    }
                });
            }else
                return false;
        });
    })
</script>