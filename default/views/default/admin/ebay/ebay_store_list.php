<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-15
 * Time: 14:17
 */
?>

<div class="row" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">ebay刊登-erp分类对应店铺分类列表</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">


                            <label>
                                ebay账号:
                                <select name="search[token_id]" id="token_id">
                                    <option value="">==请选择==</option>
                                    <?php foreach($account as $key=> $ac): ?>
                                        <option value="<?php echo $key ?>" <?php if(isset($search['token_id'])&&($search['token_id']==$key)){ echo  'selected="selected"';}  ?> > <?php  echo $ac;  ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </label>



                               <label>
                                erp分类:
                               <select name="search[erp_category]">
                                   <option value="">==请选择==</option>
                                   <?php foreach($erp_category as $category): ?>
                                    <option value="<?php echo $category['category_id'] ?>" <?php if(isset($search['erp_category'])&&($search['erp_category']==$category['category_id'])){ echo  'selected="selected"';}  ?> > <?php  echo $category['category_id'].'--'.$category['category_name'];  ?></option>

                                   <?php endforeach; ?>
                               </select>
                              </label>



                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">筛选</button>
                            </label>
                            &nbsp;&nbsp;    &nbsp;&nbsp;


                            <label>
                                <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('ebay/ebay_store_category/binding_category');?>">添加</a>

                            </label>


                            <label>
                                <a class="btn btn-primary btn-sm" id="update_store_category" >同步指定账号店铺分类 </a>
                            </label>
                        </form>



                    </div>
                </div>

                <table  class="table   table-hover dataTable  center " id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="30%">
                        <col width="10%">
                        <col width="10%">

                        <!--     <col width="8%"/>-->
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th class="center">erp分类id</th>
                        <th class="center">erp分类名字</th>
                        <th class="center">ebay店铺分类</th>
                        <th class="center">最后设置人员</th>
                        <th class="center" >操作</th>
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
                                <td class="center"><?php  echo $list->erp_category; ?></td>
                                <td class="center"><?php  echo $erp_category[$list->erp_category]['category_name']; ?> </td>
                                <td class="center" ><?php
                                    if(!empty($list->category_with_store)){
                                        $category_with_store = json_decode($list->category_with_store,true);
                                        foreach($category_with_store as $token_id=> $ac){

                                            if(isset($search['token_id'])&&!empty($search['token_id'])&&($search['token_id'] !=$token_id)){
                                                continue;
                                            }else{

                                                $name=isset($all_store_category[$token_id][$ac])?$all_store_category[$token_id][$ac]:'该分类可能别移除';
                                                echo $account[$token_id].' :'.$ac.'----'.$name.'<br/>';

                                            }
                                        }
                                    }




                                    ?></td>

                                <td class="center" ><?php  echo $user_array[$list->user]; ?></td>

                                <td class="center" >
                                    <a href="<?php echo admin_base_url('ebay/ebay_store_category/binding_category?')?><?php echo 'id='.$list->id;           ?>" class="tooltip-success" data-rel="tooltip" title="修改" >
	                                                <span class="green">
	                                                    <i class="icon-edit bigger-120"></i>
	                                                </span>
                                    </a>

                                    <a href="javascript: void(0);" title="删除" onclick="msgdelete('<?php echo $list->id;?>', '<?php echo admin_base_url('ebay/ebay_store_category/delete');?>');">
                                        <i class="icon-trash bigger-130 red"></i>
                                    </a>
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

<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>
<script type="text/javascript">
    $(function(){
        $(document).on('click','.Wdate',function(){
            var o = $(this);
            if(o.attr('dateFmt') != '')
                WdatePicker({dateFmt:o.attr('dateFmt')});
            else if(o.hasClass('month'))
                WdatePicker({dateFmt:'yyyy-MM'});
            else if(o.hasClass('year'))
                WdatePicker({dateFmt:'yyyy'});
            else
                WdatePicker({dateFmt:'yyyy-MM-dd'});
        });
    });

    $("#update_store_category").click(function(){
        var token_id = $("#token_id").val();
        if(token_id==''){
            alert("请先选择账号");
            return false;
        }
        url = '<?php echo admin_base_url("ebay/category/getStoreCategory");?>';
        var site = $('#siteid').val();
        $.ajax({
            url: url,
            data: 'token_id=' + token_id,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {

                alert(data.info);
            }
        });
    })


</script>