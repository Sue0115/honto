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
                        <label></label>
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
                            设置人员:
                            <select name="search[user_id]" id="user_id">
                                <option value="">---全选---</option>
                                <?php
                                if ($all_user) {

                                    foreach ($all_user as $user)
                                    {
                                        if(!empty($user['user_id']))
                                        echo '<option value="'.$user['user_id'].'" '.(  (isset($search['user_id'])&&$search['user_id'] == $user['user_id']) ? 'selected="selected"': '').'>'.$user_last[$user['user_id']].'</option>';
                                    }
                                }
                                ?>
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
                        <label>
                            <a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('ebay/ebay_timing/addEbayTiming');?>">添加</a>

                        </label>

                    </form>

                </div>
            </div>

            <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
                <colgroup>
                    <col width="5%">
                    <col width="8%">
                    <col width="8%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                    <col width="10%">
                    <col width="5%">
                    <col width="7%">
                    <col width="5%">
                    <col width="5%">
                    <col width="17%">
                    <col width="10%">
                    <col width="5%">

                </colgroup>
                <thead>
                <tr>
                    <th class="center">
                        <label>
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>本地时间</th>
                    <th>站点时间</th>
                    <th>状态</th>
                    <th>名称</th>
                    <th>图片</th>
                    <th>ebay账号</th>
                    <th>站点</th>
                    <th>SKU</th>
                    <th>设置人员</th>
                    <th>刊登类型</th>
                    <th>标题</th>
                    <th>刊登结果</th>
                    <th>操作</th>
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
                            <td><?php echo $item->local_time;?></td>
                            <td><?php echo $item->publish_time;?></td>
                            <td><?php  if($item->status == 1){ echo '未执行';} if($item->status == 2){ echo '已执行';} ?></td>
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
                            <td><?php echo $item->sku; ?></td>
                            <td><?php   if(isset($user_last[$item->user_id])) echo  $user_last[$item->user_id]; ?></td>
                            <td><?php
                                if($item->ad_type=='paimai')
                                    echo '拍卖';
                                if($item->ad_type=='guding')
                                    echo '固定';
                                if($item->ad_type=='duoshuxing')
                                    echo '多属性';
                                ?></td>
                            <td><?php echo $item->title;?></td>
                            <td>
                                <?php echo $item->note;?>
                            </td>

                            <td>
                                <a title="删除" href="javascript:void(0);" onclick="msgdelete(<?php echo $item->id;?>, '<?php echo admin_base_url("ebay/ebay_timing/delete"); ?>')" class="red">
                                    <i class="icon-trash bigger-130"></i>
                                </a>
                            </td>
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
