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
        <h3 class="header smaller lighter blue">ebay刊登-产品列表</h3>
        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">


                            <label>
                                ebay账号:
                                <select name="search[account_id]" id="account_id">
                                    <option value="">==请选择==</option>
                                    <?php foreach($account as $key=> $ac): ?>
                                        <option value="<?php echo $key ?>" <?php if(isset($search['account_id'])&&($search['account_id']==$key)){ echo  'selected="selected"';}  ?> > <?php  echo $ac;  ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label>
                                站点:
                                <select name="search[site]" id="site">
                                    <option value="999">==请选择==</option>
                                    <?php foreach($site_array as $key=> $ac): ?>
                                        <option value="<?php echo $key ?>"  <?php if(isset($search['site'])&&($search['site']==$key)){ echo  'selected="selected"';}  ?> > <?php  echo $ac;  ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </label>


                            <label>
                                上架类型:
                                <select name="search[ad_type]" id="ad_type">
                                    <option value="">==请选择==</option>
                                    <option value="1"  <?php if(isset($search['ad_type'])&&($search['ad_type']==1)){ echo  'selected="selected"';}  ?> >拍卖</option>
                                    <option value="2"  <?php if(isset($search['ad_type'])&&($search['ad_type']==2)){ echo  'selected="selected"';}  ?> >固定</option>
                                    <option value="3" <?php if(isset($search['ad_type'])&&($search['ad_type']==3)){ echo  'selected="selected"';}  ?> >多属性</option>
                                </select>
                            </label>


                            <label>
                                上架人员:
                                <select name="search[add_user]" id="add_user">
                                    <option value="">==请选择==</option>
                                    <?php foreach($user as $key=> $ac): ?>
                                        <option value="<?php echo $ac ?>"  <?php if(isset($search['add_user'])&&($search['add_user']==$ac)){ echo  'selected="selected"';}  ?> > <?php  echo $user_array[$ac];  ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </label>


                            <label>
                                SKU:
                                <input type="text" name="search[erp_sku]" placeholder="不要输入前后缀" value="<?php  echo (isset($search['erp_sku'])? $search['erp_sku']: '');  ?>" >
                            </label>



                            <label>
                                itemid:
                                <input type="text" name="search[itemid]"  value="<?php  echo (isset($search['itemid'])  ? $search['itemid']: '');  ?>" >
                            </label>
                            <br/>
                            上架时间
                            <input type="text"  value="<?php  echo (isset($search['start_date'])  ? $search['start_date']: '');  ?>" datefmt="yyyy-MM-dd" class="Wdate"  name="search[start_date]" />
                            ~
                            <input type="text" value="<?php  echo (isset($search['end_date'])  ? $search['end_date']: '');  ?>" datefmt="yyyy-MM-dd" class="Wdate"  name="search[end_date]"/>


                            <label>
                                <button class="btn btn-primary btn-sm" type="submit">筛选</button>
                            </label>

                            &nbsp; &nbsp; &nbsp;
                            <label>
                                <a class="btn btn-primary btn-sm" id="copy_draft" >另存为草稿</a>
                            </label>


                            &nbsp; &nbsp; &nbsp;
                            <label>
                                <a class="btn btn-primary btn-sm" id="getItem" >同步到刊登列表</a>
                            </label>

                            <!--   <label>
                                   <a class="btn btn-primary btn-sm" id="update_store_category" >同步指定账号店铺分类 </a>
                               </label>-->
                        </form>

                    </div>
                </div>

                <table  class="table   table-hover dataTable  center " id="tbody_content">
                    <colgroup>
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="5%">
                        <col width="10%">
                        <col width="20%">
                        <col width="10%">
                        <col width="5%">
                        <col width="5%">
                        <col width="20%">
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
                        <th class="center">站点</th>
                        <th class="center" >账号</th>
                        <th class="center" >类型</th>
                        <th class="center" >图片</th>
                        <th class="center" >标题</th>
                        <th class="center" >SKU</th>
                        <th class="center" >价格</th>
                        <th class="center" >添加人员</th>
                        <th class="center" >提示信息</th>
                        <th class="center" >上架时间</th>
                        <!-- <th class="center" >负责人</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $type =array(
                        1=>'拍卖',
                        2=>'固定',
                        3=>'多属性',
                    );

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
                                <td class="center"><?php  echo $site_array[$list->site]; ?></td>
                                <td class="center"><?php  echo $account_array[$list->account_id]; ?> </td>
                                <td class="center" ><?php  echo $type[$list->ad_type]; ?></td>
                                <td class="center" ><?php  $detailPicList = json_decode($list->detailPicList,true);
                                    if(!empty($detailPicList)){
                                        echo '<img src="'.$detailPicList[0].'"  width="100" height="100" style="border: 0px;" >';
                                    }
                                    ?></td>
                                <td class="center" ><?php  echo $list->title; ?></td>
                                <td class="center" ><?php  echo $list->ebay_sku; ?></td>
                                <td class="center" ><?php  echo $list->ebay_price; ?><br/><?php  echo $list->payPalEmailAddress; ?></td>
                                <td class="center" ><?php  echo $user_array[$list->add_user]; ?></td>
                                <td class="center" >  <a  target="_blank" href="http://www.ebay.com/itm/<?php  echo $list->itemid; ?>"><?php  echo $list->itemid; ?></a></td>
                                <td class="center" >

                                    <?php  echo $list->creat_time; ?>
                                    <!--<a href="<?php /*echo admin_base_url('ebay/ebay_product/add?')*/?><?php /*echo 'site_id='.$list->site.'&category_id='.$list->category_id.'&category_id_second='.$list->category_id_second.'&id='.$list->id;           */?>" class="tooltip-success" data-rel="tooltip" title="修改"  target="_blank">
	                                                <span class="green">
	                                                    <i class="icon-edit bigger-120"></i>
	                                                </span>
                                    </a>-->
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


    $(document).on('click', '#copy_draft', function(e){

            var productIds = $('input[name="ids[]"]:checked').map(function() {
                return $(this).val();
            }).get().join(',');
            if (productIds == ''){
                showtips('请先选择', 'alert-warning');
                return false;
            }
            $.ajax({
                url: '<?php echo admin_base_url("ebay/ebay_product_list/copy_to_draft");?>',
                data: 'productIds='+productIds,
                type: 'POST',
                dataType: 'JSON',
                success: function(data){
                    if(data.status==1){
                        alert("复制成功");
                    }else{
                        alert("复制失败");
                    }

                }
            });

    });

    $(document).on('click', '#getItem', function(e){

        var productIds = $('input[name="ids[]"]:checked').map(function() {
            return $(this).val();
        }).get().join(',');
        if (productIds == ''){
            showtips('请先选择', 'alert-warning');
            return false;
        }
        $.ajax({
            url: '<?php echo admin_base_url("ebay/ebay_listting/ajaxGetItem");?>',
            data: 'ids='+productIds,
            type: 'POST',
            dataType: 'JSON',
            success: function(data){
                alert(data.info)
            }
        });

    });

</script>