<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/15
 * Time: 14:38
 */

?>


<div class="row">

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="get">
                            <label>
                                账号:
                                <select name="search[seller_account]" id="seller_account">
                                    <option value="">---全选---</option>
                                    <?php
                                    foreach($token as $t):
                                        echo '<option value="'.$t['seller_account'].'" '.($search['seller_account'] == $t['seller_account'] ? 'selected="selected"': '').'>'.$t['seller_account'].'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>

                            <label>
                                站点:
                                <select name="search[site]" id="site">
                                    <option value="999">---全选---</option>
                                    <?php
                                    foreach($sitearr as $key=>$t):
                                        echo '<option value="'.$key.'" '.(  (isset($search['site'])&&$search['site'] == $key) ? 'selected="selected"': '').'>'.$t.'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </label>
                            <label>
                                广告状态:

                                <select name="search[productStatusType]" id="productStatusType">
                                    <option value="">--所有状态--</option>
                                    <option value="1" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 1 ? 'selected="selected"': ''); } ?>>未刊登</option>
                                    <option value="2" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 2 ? 'selected="selected"': ''); } ?>  >已刊登</option>
                                    <option value="3" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 3 ? 'selected="selected"': ''); } ?> >刊登失败</option>
                                    <option value="4" <?php   if(isset($search['productStatusType'])){ echo ($search['productStatusType'] == 4 ? 'selected="selected"': ''); } ?> >已下架</option>
                                </select>
                            </label>

                            <label>
                                ItemID:
                                <input type="text" name="search[itemId]" placeholder="请输入itemID"  value="<?php  echo (isset($search['itemId'])  ? $search['itemId'] : '');  ?>">
                            </label>
                            <label>
                                SKU:
                                <input type="text" name="search[sku]" placeholder="不要输入前后缀" value="<?php  echo (isset($search['sku'])  ? $search['sku']: '');  ?>" >
                            </label>
                            <label>
                                标题:
                                <input type="text" name="search[subject]"  value="<?php  echo (isset($search['subject']) ? $search['subject']: ''); ?>"/>
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
                        <col width="5%">
                        <col width="5%">
                        <col width="10%">
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="5%">
                        <col width="20%">
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
                        <th>名称</th>
                        <th>图片</th>
                        <th>ebay账号</th>
                        <th>站点</th>
                        <th>SKU</th>
                        <th>刊登类型</th>
                        <th>刊登状态</th>
                        <th>标题</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($infolist):
                        foreach($infolist as $item):
                            ?>
                            <tr>
                                <td class="center">
                                    <label>
                                        <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->id;?>">
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                                <td><?php echo $item->id;?></td>
                                <td><?php echo $item->name; ?></td>

                                <td><?php
                                    if(!empty($item->ebay_picture))
                                    {
                                        $pictureinfo = json_decode($item->ebay_picture,true);


                                        echo '<img src='.$pictureinfo[0].'   width="50" height="50" />';
                                    }


                                    ?></td>
                                <td><?php echo $item->ebayaccount; ?></td>
                                <td><?php echo  $sitearr[$item->site]; ?></td>
                                <td><?php echo $item->sku;?></td>
                                <td><?php
                                    if($item->ad_type=='paimai')
                                        echo '拍卖';
                                    if($item->ad_type=='guding')
                                        echo '固定';
                                    if($item->ad_type=='duoshuxing')
                                        echo '多属性';
                                    ?></td>
                                <td><?php
                                    if($item->status  ==1)
                                    {
                                        echo '未刊登';
                                    }
                                    if($item->status  ==2)
                                    {
                                        echo '已刊登';
                                    }
                                    if($item->status  ==3)
                                    {
                                        echo '刊登失败';
                                    }
                                    if($item->status  ==4)
                                    {
                                        echo '已下架';
                                    }
                                    ?></td>
                                <td><?php echo $item->title;?></td>
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
