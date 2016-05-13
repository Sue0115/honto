<?php
/**
 * 产品复制到模板账号列表
 * User: zrh
 * Date: 2015/05/07
 * Time: 13:44
 */
?>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header small lighter blue"><?php echo !empty($action) ? 'wish-选择导入数据的账号':'wish刊登-产品复制'?></h3>

        <div>
            <form action="">
                <div class="form-group clearfix">
                    <ul class="list-inline">
                        <li>
                            <label for="checkAll">
                                <input id="checkAll" type="checkbox"/>全选/全不选
                            </label>
                        </li>
                    </ul>
                </div>

                <div class="form-group clearfix">
                    <ul class="list-inline account_list">
                        <?php foreach($account_list as $account):?>
                            <li class="col-sm-4">
                                <label>
                                    <input type="checkbox" value="<?php echo $account['token_id'];?>" />
                                    <?php echo $account['choose_code'];?>
                                </label>
                            </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        //全选
        $('#checkAll').click(function(){
            this.checked ? $('.account_list').find(':checkbox').prop('checked', true) : $('.account_list').find(':checkbox').prop('checked', false);
        });
    })
</script>