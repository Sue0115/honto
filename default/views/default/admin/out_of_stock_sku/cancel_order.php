<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-04
 * Time: 09:49
 */
?>

<style>
    .tishi {
        cursor: pointer;
    }

    .yeanse {
        background-color: #00ffff;
        cursor: pointer;
    }
</style>

<div class="row" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">撤单中心</h3>

        <div class="table-header">&nbsp;</div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-xs-12">
                        <form action="" method="post" id="sub" >

                            <label>
                                是否混合订单:
                                <select name="search[is_mix]" id="is_mix">
                                    <option value="1"  <?php  if($is_mix == 1 )  echo 'selected="selected"';    ?>>否</option>
                                    <option value="2"  <?php  if($is_mix == 2 )  echo 'selected="selected"';    ?>>是</option>
                                </select>
                            </label>



                            <label>
                                是否拆分过订单:
                                <select name="search[is_split]" id="is_split">
                                    <option value="1"  <?php  if($is_split == 1 )  echo 'selected="selected"';    ?>>否</option>
                                    <option value="2"  <?php  if($is_split == 2 )  echo 'selected="selected"';    ?>>是</option>
                                </select>
                            </label>



                            <label class="hidden">
                                <input name="sku"  value="<?php echo $sku;  ?>" />
                                <input name="reason"  value="<?php echo $reason;   ?>" />
                                <input name="getorder"  value="" />


                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <label>
                                平台:
                                <select name="search[platform]" id="platform">
                                    <option value="">==全部平台==</option>
                                    <?php
                                        foreach($order_type as $key=>$v)
                                        {
                                            if($platform == $key)
                                            {
                                                echo ' <option value="'.$key.'" selected="selected" >'.$v.'</option>';

                                            }else{
                                                echo ' <option value="'.$key.'" >'.$v.'</option>';

                                            }
                                        }

                                    ?>
                                </select>
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <label>
                                欠货天数 >
                                <select name="search[back_day]" id="back_day">
                                    <?php
                                     for($i=1;$i<50;$i++){
                                         if($back_day == $i)
                                         {
                                             echo ' <option value="'.$i.'" selected="selected" >'.$i.'</option>';

                                         }else{
                                             echo ' <option value="'.$i.'" >'.$i.'</option>';

                                         }

                                     }
                                    ?>
                                </select>
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;

                            <label>
                                SKU状态:
                                <select name="search[products_status_2]" id="products_status_2">
                                    <option value=""  <?php  if($products_status_2=='')  echo 'selected="selected"';    ?> >==全部状态==</option>
                                    <option value="selling"  <?php  if($products_status_2 == 'selling' )  echo 'selected="selected"';    ?> >在售</option>
                                    <option value="sellWaiting"  <?php  if($products_status_2 == 'sellWaiting' )  echo 'selected="selected"';    ?> >待售</option>
                                    <option value="stopping"  <?php  if($products_status_2  == 'stopping' )  echo 'selected="selected"';    ?> >停产</option>
                                    <option value="saleOutStopping"  <?php  if($products_status_2  == 'saleOutStopping' )  echo 'selected="selected"';    ?> >卖完下架</option>
                                    <option value="unSellTemp"  <?php  if($products_status_2  == 'unSellTemp' )  echo 'selected="selected"';    ?> >货源待定</option>
                                    <option value="trySale"  <?php  if($products_status_2 == 'trySale' )  echo 'selected="selected"';    ?> >试销(卖多少采多少)</option>
                                </select>
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;

                            <label class="">
                                <button class="btn btn-primary btn-sm " name="check" type="submit">筛选</button>
                            </label>
                            &nbsp;&nbsp; &nbsp;&nbsp;
                            <label class="">
                                <button class="btn btn-primary btn-sm " name="exportout" type="submit">导出</button>
                            </label>


                            <label class="">

                                &nbsp;&nbsp; &nbsp;&nbsp;

                                <a class="btn btn-primary btn-sm"
                                   href="<?php echo admin_base_url("out_of_stock_sku/out_of_stock_manage/index"); ?>">返回缺货中心</a>
                            </label>
                            <table class="table    dataTable " id="tbody_content">
                                <colgroup>
                                    <col width="25%">
                                    <col width="25%"/>
                                    <col width="25%"/>
                                    <col width="25%">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th class="center">订单号</th>
                                    <th class="center">平台</th>
                                    <th class="center">欠货SKU,数量</th>
                                    <th class="center">欠货天数</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($result):
                                    foreach ($result['last_result'] as $re):
                                        ?>
                                        <tr class="center">

                                            <td>
                                               <?php  echo  $re['erp_orders_id'];?>
                                            </td>

                                            <td>
                                                <?php  echo  $order_type[$re['orders_type']];?>
                                            </td>


                                            <td>
                                              <?php
                                              $num_arr = explode(',',$re['mix_num']);
                                              $sku_arr = explode(',',$re['mix_sku']);

                                              foreach($sku_arr as $key=> $s)
                                              {

                                                  if(in_array($s,$result['sku']))
                                                  {
                                                      echo $s .' * '. $num_arr[$key];
                                                      echo '<br/>';
                                                  }
                                              }
                                              ?>
                                            </td>


                                            <td>
                                                <?php
                                                          $day =  (time()-strtotime($re['orders_export_time']))/(24*60*60);
                                                echo    intval($day);
                                                ?>
                                            </td>



                                        </tr>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                                </tbody>
                            </table>

                            <label class="">
                                <button class="btn btn-primary btn-sm " name="cancel"  type="submit" onclick="if(confirm('确定撤单？')){ return true;}else{return false;};"">全部撤单</button>
                            </label>

                        </form>


                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showInfo(){
        alert(123);
        //如果不显示插入信息
        if(confirm("确定要上传数据吗？")){
            $("sub").submit();
        }
    }
    }
</script>