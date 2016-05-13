<?php
/**
 * 图片银行列表
 * User: admin
 * Date: 2014/12/22
 * Time: 9:57
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue">速卖通-图片银行列表</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                账号:
                                <select name="token_id" id="token_id">
                                    <option value="">---全选---</option>
                                    <?php
                                    foreach($token as $t):
                                        echo '<option value="'.$t['token_id'].'" '.($token_id == $t['token_id'] ? 'selected="selected"': '').'>'.$t['token_id'].'-'.$t['seller_account'].'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <?php if ($group_list):?>
                            <label>
                                <select name="groupId" id="groupId">
                                    <option value="allGroup" <?php echo $groupId == 'allGroup' ? 'selected="selected"' : '';?>>所有分组</option>
                                    <option value="unGroup" <?php echo $groupId == 'unGroup' ? 'selected="selected"' : '';?>>未分组</option>
                                    <?php echo parsePhotoGroupArray($group_list, $groupId);?>
                                </select>
                            </label>
                            <?php endif;?>
                            <label>
                                <button class="btn btn-primary btn-sm">筛选</button>
                            </label>
                            <label>
                                <button class="btn btn-primary btn-sm" id="photo_synchronization">同步</button>
                            </label>
                        </form>
                    </div>
                </div>

                <ul class="list-inline">
                    <?php foreach ($photo_list as $photo):?>
                        <li style="padding: 10px;" class="text-center">
                            <img src="<?php echo $photo['url'];?>" alt="<?php echo $photo['displayName'];?>" width="100" height="100"/>
                            <br/>
                            <?php echo $photo['displayName'];?>
                        </li>
                    <?php endforeach;?>
                </ul>
                <?php $this->load->view('admin/common/page_number'); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
    //点击同步按钮
    $(document).on('click', '#photo_synchronization', function(){
        event.preventDefault(); //阻止默认事件
        var token_id = $('#token_id').val();
        var groupId = $('#groupId').val();
        if (token_id == ''){
            showxbtips('请先选择要同步的账号', 'alert-warning');
        }else {
            //异步加载同步信息
            $.ajax({
                url: '<?php echo admin_base_url("smt/photo_bank/getPhotoBankImageList");?>',
                data: 'token_id='+token_id+'&groupId='+groupId,
                type: 'POST',
                dataType: 'json',
                success: function(data){
                    showxbtips(data.info, data.status ? '' : 'alert-warning');
                },
                beforeSend: function(){
                    $(this).addClass('disabled').html('同步中...');
                },
                complete: function(){
                    $(this).removeClass('disabled').html('同步');
                }
            });
        }
    });

    //根据账号获取图片分组信息
    $('#token_id').on('change', function(){
        var token_id = $(this).val();
        if (token_id){
            //异步加载分组信息
            $.ajax({
                url: '<?php echo admin_base_url("smt/photo_bank/ajaxGetPhotoGroup");?>',
                data: 'token_id='+token_id,
                type: 'POST',
                dataType: 'json',
                success: function(data){
                    var options = '';
                    $.each(data.data, function(index, el){
                        var option='';
                        options += parseGroupData(option, el, '');
                    });

                    var select_str;
                    //判断下节点是否存在
                    if ($('#groupId').length > 0){
                        select_str += '<option value="allGroup">所有分组</option>'
                                +'<option value="unGroup">未分组</option>'
                                +options;
                        $('#groupId').empty();
                        $('#groupId').append(select_str);
                    }else {
                        //节点不存在，后边追加吧
                        select_str = '<label>'
                            +'<select name="groupId" id="groupId">'
                                +'<option value="allGroup">所有分组</option>'
                                +'<option value="unGroup">未分组</option>'
                                +options
                            +'</select>'
                            +'</label>';
                        $('#token_id').closest('label').after(select_str);
                    }
                }
            });
        }else {
            //图片银行分组信息清空处理
            $('#groupId').closest('label').remove();
        }
    });
})
//解析分组信息 --递归
function parseGroupData(options, obj, indent){
    options += '<option value="'+obj.groupId+'">'+indent+obj.groupName+'</option>';
    indent += '&nbsp;&nbsp;&nbsp;&nbsp;';
    if (obj.child){
        $.each(obj.child, function (index, el){
            var option = '';
            options += parseGroupData(option, el, indent);
        });
    }
    return options;
}
</script>