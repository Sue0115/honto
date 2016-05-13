<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/29
 * Time: 17:09
 */

?>

<form class="form-horizontal validate_form " action="<?php echo admin_base_url('ebay/ebay_product/transTemplateAdd'); ?>"method="post">


    <div class="row-fluid">
        <div class="span12">
            <div class="panel-body"><span style="font-weight:bold;">基本信息</span></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-sm-2 control-label" >运输模板名称：</label>
            <div class="col-sm-10">
                <input  class="form-control" name="transname"  value="<?php  if(isset($listinfo['transtemplatename'])){ echo $listinfo['transtemplatename']; } ?>"  />
                <input  class="form-control hidden" name="id"   value="<?php  if(isset($listinfo['id'])){ echo $listinfo['id']; } ?>"  />

            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >站点：</label>
            <div class="col-sm-10">
               <select id="siteid"  name="siteid">
                   <?php
                   foreach($side as $arr)
                   {
                       if(isset($listinfo))
                       {
                           if($listinfo['siteid']==$arr['siteid'])
                           {
                               echo '<option selected = "selected"  value="'.$arr['siteid'].'">'.$arr['site'].'</option>';
                           }
                           else
                           {
                               echo '<option value="'.$arr['siteid'].'">'.$arr['site'].'</option>';
                           }
                       }
                       else{
                           echo '<option value="'.$arr['siteid'].'">'.$arr['site'].'</option>';
                       }
                   }
                   ?>
               </select>
            </div>
        </div>

        </hr>

        <div class="span12">
            <div class="panel-body"><span style="font-weight:bold;">买家要求</span></div>
        </div>
        </hr>
        <div class="form-group ">
            <label class="col-sm-2 control-label" >买家要求</label>
            <?php if(isset($listinfo['BuyerRequirementDetails'])) $Buyer = unserialize($listinfo['BuyerRequirementDetails']);     /*  var_dump($Buyer);*/      ?>
            <div class="col-sm-10">
                <input type="radio"  name="yaoqiu" value="all"><span>允许所有买家购买我的物品</span>
                <br/>
                <input type="radio"  name="yaoqiu" value="notall"  <?php if(isset($Buyer['all_buyers'])) echo 'checked="checked"';  ?>  ><span>不允许以下买家购买我的物品</span>
            </div>

            <div class="form-group" id="maijiayaoqiudetail">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                    <input type="checkbox"  name="nopaypal"  <?php if(isset($Buyer['LinkedPayPalAccount'])&&($Buyer['LinkedPayPalAccount']=='on')) echo 'checked="checked"';  ?>  />没有 PayPal 账户 <br/>
                    <input type="checkbox" name="yunshufangweizhiwai"  <?php if(isset($Buyer['ShipToRegistrationCountry'])&&($Buyer['ShipToRegistrationCountry']=='on')) echo 'checked="checked"';  ?>   />主要运送地址在我的运送范围之外<br/>
                    <input type="checkbox" id="qibiao" name="qibiao" <?php if(isset($Buyer['MaximumUnpaidItemStrikesInfo']['main'])&&($Buyer['MaximumUnpaidItemStrikesInfo']['main']=='on')) echo 'checked="checked"';  ?>  ><span>曾收到</span>
                    <select id="qibiaonum" name="qibiaonum"  >
                        <?php $info_array = array(2,3,4,5);
                        foreach($info_array as $info):
                            ?>
                            <option value="<?php echo $info;  ?>"  <?php  if(isset($Buyer['MaximumUnpaidItemStrikesInfo']['Count'])&&($Buyer['MaximumUnpaidItemStrikesInfo']['Count']==$info)) echo ' selected = "selected" ';  ?> ><?php echo $info; ?> </option>
                        <?php endforeach; ?>

                    </select>
                    <span>个弃标个案，在过去</span>
                    <select id="qibiaotianshu" name="qibiaotianshu">
                        <?php $info_array = array('Days_30','Days_180','Days_360');
                        foreach($info_array as $info):
                            ?>
                            <option value="<?php echo $info;?>"    <?php  if(isset($Buyer['MaximumUnpaidItemStrikesInfo']['Period'])&&($Buyer['MaximumUnpaidItemStrikesInfo']['Period']==$info))  echo ' selected = "selected" '; ?> ><?php echo $info; ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <span>天</span>
                    <br/>
                    <input type="checkbox" id="jianjv" name="jianjv"  <?php if(isset($Buyer['MaximumBuyerPolicyViolations']['main'])&&($Buyer['MaximumBuyerPolicyViolations']['main']=='on')) echo 'checked="checked"';  ?>   ><span>曾收到</span>
                    <select id="jianjvnum" name="jianjvnum"  >
                        <?php $info_array = array(4,5,6,7);
                        foreach($info_array as $info):
                            ?>
                            <option value="<?php echo $info; ?>"  <?php  if(isset($Buyer['MaximumBuyerPolicyViolations']['Count'])&&($Buyer['MaximumBuyerPolicyViolations']['Count']==$info)) echo ' selected = "selected" ';  ?> ><?php echo $info; ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <span>个违反政策检举，在过去</span>
                    <select id="jianjvtianshu"  name="jianjvtianshu" >
                        <?php $info_array = array('Days_30','Days_180');
                        foreach($info_array as $info):
                            ?>
                            <option value="<?php echo $info; ?>"    <?php  if(isset($Buyer['MaximumBuyerPolicyViolations']['Period'])&&($Buyer['MaximumBuyerPolicyViolations']['Period']==$info))  echo ' selected = "selected" '; ?> ><?php echo $info; ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <span>天</span>
                    <br/>
                    <input type="checkbox" id="xinyong" name="xinyong"  <?php if(isset($Buyer['MinimumFeedbackScore']['main'])&&($Buyer['MinimumFeedbackScore']['main']=='on')) echo 'checked="checked"';  ?>    ><span>信用指标等于或低于：</span>
                    <select id="xinyongnum" name="xinyongnum">
                        <?php $info_array = array(-1,-2,-3);
                        foreach($info_array as $info):
                            ?>
                            <option value="<?php echo $info; ?>" <?php  if(isset($Buyer['MinimumFeedbackScore']['Count'])&&($Buyer['MinimumFeedbackScore']['Count']==$info)) echo ' selected = "selected" ';  ?>  ><?php echo $info; ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <br/>
                    <input type="checkbox" id="goumai" name="goumai"    <?php if(isset($Buyer['MaximumItemRequirements']['main'])&&($Buyer['MaximumItemRequirements']['main']=='on')) echo 'checked="checked"';  ?>  ><span>在过去10天内曾出价或购买我的物品，已达到我所设定的限制</span>
                    <select id="goumainum" name="goumainum"  >
                        <?php $info_array = array(1,2,3,4,5,6,7,8,9,10,25,50,70,100);
                                foreach($info_array as $info):
                        ?>
                                    <option value="<?php echo $info; ?>" <?php  if(isset($Buyer['MaximumItemRequirements']['MaximumItemCount'])&&($Buyer['MaximumItemRequirements']['MaximumItemCount']==$info)) echo ' selected = "selected" ';  ?>   ><?php echo $info; ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <br/>
                    <div <span>&nbsp; &nbsp;&nbsp;</span><input type="checkbox" name="maijiaxinyong" id="maijiaxinyong"  <?php if(isset($Buyer['MaximumItemRequirements']['main_score'])&&($Buyer['MaximumItemRequirements']['main_score']=='on')) echo 'checked="checked"';  ?>   ><span>这项限制只适用于买家信用指数等于或低于</span>
                    <select id="maijiaxinyongnum" name="maijiaxinyongnum"  >
                        <?php $info_array = array(5,4,3,2,1,0);
                        foreach($info_array as $info):
                            ?>
                            <option value="<?php echo $info; ?>"  <?php  if(isset($Buyer['MaximumItemRequirements']['MinimumFeedbackScore'])&&($Buyer['MaximumItemRequirements']['MinimumFeedbackScore']==$info)) echo ' selected = "selected" ';  ?>  ><?php echo $info; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        </div>


        <div class="span12">
            <div class="panel-body"><span style="font-weight:bold;">退货政策</span></div>
        </div>
        <hr/>
        <div class="form-group">
            <label class="col-sm-2 control-label" >退货政策</label>
            <?php
            if(isset($listinfo['returns_policy']))
            {
                ?>
                <input class="hidden" type="text" id="returns_policy" value="<?php echo $listinfo['returns_policy']; ?>">
            <?php
            }
            ?>
            <div class="col-sm-10">
                <select name="tuihuozhengce" id="tuihuozhengce">
                    <option value="Returns Accepted">Returns Accepted</option>
                    <option value="Returns Not Accepted">Returns Not Accepted</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >退货天数</label>
            <?php
            if(isset($listinfo['returns_days']))
            {
                ?>
                <input class="hidden" type="text" id="returns_days" value="<?php echo $listinfo['returns_days']; ?>">
            <?php
            }
            ?>
            <div class="col-sm-10">
                <select name="tuihuotianshu" id="tuihuotianshu">
                    <option value="14 days">14 days</option>
                    <option value="30 days">30 days</option>
                    <option value="60 days">60 days</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" ><input type="checkbox" name="returns_delay"
                    <?php
                    if(isset($listinfo['returns_delay']))
                    {
                        if($listinfo['returns_delay']=='on')
                        {
                            echo 'checked';
                        }
                    }
                    ?>
                    ></label>
            <div class="col-sm-10">
                <span>提供节假日延期退货至12月31日。</span>
            </div>
        </div>

        <div class="form-group" id="tuihuofangshiall">
            <label class="col-sm-2 control-label" >退款方式</label>
            <?php
            if(isset($listinfo['returns_type'])&&(!empty($listinfo['returns_type'])))
            {
            ?>
                <input class="hidden" type="text" id="returns_type" value="<?php echo $listinfo['returns_type']; ?>">
            <?php
            }
            ?>
            <div class="col-sm-10">
                <select name="tuihuofangshi" id="tuihuofangshi">
                    <?php
                    if(isset($listinfo['returns_type'])) {
                        if($listinfo['returns_type']=='')
                        {
                        ?>
                    <?php
                        }
                    else {

                        ?>

                        <option value="Money Back">Money Back</option>
                        <option value="Money Back or replacement(buyer's choice)">Money Back or replacement(buyer's choice)</option>
                        <option value="Money Back or exchange(buyer's choice)">Money Back or exchange(buyer's choice)</option>
                    <?php
                    }
                    }
                    ?>

                </select>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" >退货运费由谁负担</label>
            <?php
            if(isset($listinfo['returns_cost_by']))
            {
                ?>
                <input class="hidden" type="text" id="returns_cost_by" value="<?php echo $listinfo['returns_cost_by']; ?>">
            <?php
            }
            ?>
            <div class="col-sm-10">
                <select name="tuihuochengdang" id="tuihuochengdang">
                    <option value="Buyer">Buyer</option>
                    <option value="Seller">Seller</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >退货政策详情</label>
            <div class="col-sm-10">
                       <textarea name="return_details"    id="return_details" class="form-control">
                           <?php
                           if(isset($listinfo))
                           {
                               echo $listinfo['return_details'];
                           }
                           ?>
					</textarea>
                <div class="help-block">提示信息</div>
                <!--<span>0/( 最多 500 字符. 不支持 HTML )</span>-->
            </div>
        </div>



        <div class="span12">
            <div class="col-sm-2"><span style="font-weight:bold;">物品所在地</span></div>
            <!--   <div class="col-sm-10" align="right"> <button class="btn btn-default btn-xs" type="submit"  >选择</button>
                   <button class="btn btn-default btn-xs" type="submit">另存为</button>
               </div>-->
        </div>
        <br/>
        <hr/>

        <div class="form-group">
            <label class="col-sm-2 control-label" >物品所在地</label>
            <div class="col-sm-10">
                <input type="text" name="item_location"  value="<?php  if(isset($listinfo['item_location'])){ echo $listinfo['item_location']; } ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >国家或地区</label>
            <div class="col-sm-10">
                <select id="country" name="country">
                    <?php
                    if(isset($country))
                    {
                        echo'<option value="">--请选择--</option>';
                        foreach($country as $arr)
                        {
                            if(isset($listinfo))
                            {
                                if($arr['country'] == $listinfo['item_country'])
                                {

                                    echo '<option selected = "selected"  value="'.$arr['country'].'" >'.$arr['country_en'].'</option>';
                                }
                                else
                                {
                                    echo '<option value="'.$arr['country'].'">'.$arr['country_en'].'</option>';
                                }
                            }
                            else
                            {
                                if($arr['country']=='CN')
                                {
                                    echo '<option selected = "selected"  value="'.$arr['country'].'">'.$arr['country_en'].'</option>';
                                }
                                else
                                {
                                    echo '<option value="'.$arr['country'].'">'.$arr['country_en'].'</option>';
                                }

                            }

                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >邮编</label>
            <div class="col-sm-10">
                <input type="text" name="item_post" value="<?php  if(isset($listinfo['item_post'])){ echo $listinfo['item_post']; } ?>" />
            </div>
        </div>



    <div class="span12">
        <div class="panel-body"><span style="font-weight:bold;">国内运输</span></div>
    </div>
    <hr/>
    <div class="form-group">
        <label class="col-sm-2 control-label" ></label>
        <div class="col-sm-10">
            <p class="text-success">处理时间 0 代表同一工作日</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label" >处理时间</label>
        <div class="col-sm-10">
            <select name="guoneichulishijian">
                <option value="">--选择--</option>
                <option value="0" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==0) { echo 'selected="selected"'; } ?> >0</option>
                <option value="1" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==1) { echo 'selected="selected"'; } ?> >1</option>
                <option value="2" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==2) { echo 'selected="selected"'; } ?> >2</option>
                <option value="3" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==3) { echo 'selected="selected"'; } ?> >3</option>
                <option value="4" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==4) { echo 'selected="selected"'; } ?> >4</option>
                <option value="5" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==5) { echo 'selected="selected"'; } ?> >5</option>
                <option value="10" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==10) { echo 'selected="selected"'; } ?> >10</option>
                <option value="15" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==15) { echo 'selected="selected"'; } ?> >15</option>
                <option value="20" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==20) { echo 'selected="selected"'; } ?> >20</option>
                <option value="30" <?php if(isset($listinfo)&&$listinfo['inter_process_day']==30) { echo 'selected="selected"'; } ?> >30</option>
            </select>
            <span>工作日</span>
            <input type="checkbox" name="guoneikuaisu"   <?php if(isset($listinfo)&&$listinfo['inter_fast_send']=='true') { echo 'checked'; } ?>  value="true"/>
            <span>快速寄货</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" ></label>
        <div class="col-sm-10">
            <span style="font-weight:bold;">第一运输</span>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-2 control-label" >运输方式</label>

            <input type="text" id="inter_trans_type" class="hidden"/>

        <div class="col-sm-10">
            <select id="guoneiyunshu1" name="guoneiyunshu1">
                <?php
                if(isset($guoneitrans))
                {
                    echo '<option value="">--请选择--</option>';
                    foreach($guoneitrans as $guonei)
                    {
                        if(isset($listinfo))
                        {
                           if($listinfo['inter_trans_type']==$guonei['shippingservice'])
                           {
                               echo '<option   selected = "selected" value="'.$guonei['shippingservice'].'">'.$guonei['description'].'</option>';
                               continue;
                           }

                        }
                        echo '<option value="'.$guonei['shippingservice'].'">'.$guonei['description'].'</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" >运费</label>
        <div class="col-sm-10">
            <input type="text"  name="guoneiyunfei1" value="<?php  if(isset($listinfo['inter_trans_cost'])){ echo $listinfo['inter_trans_cost']; } ?>" placeholder="0.00"/><span class="wz">  USD </span><input type="checkbox" name="guoneimianfei1"  <?php if(isset($listinfo['inter_free'])&&($listinfo['inter_free']=='true')){ echo 'checked';} ?>  value="true"/><span class="red" >免费</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label" >额外每件加收</label>
        <div class="col-sm-10">
            <input type="text" name="guoneiewaijiashou1" value="<?php  if(isset($listinfo['inter_trans_extracost'])){ echo $listinfo['inter_trans_extracost']; } ?>" placeholder="0.00"/><span class="wz">  USD </span>
        </div>
    </div>
    </div>



<div class="span12">
    <div class="panel-body"><span style="font-weight:bold;">国际运输</span></div>
</div>
<hr/>

    <?php $array = array(1,2,3,4,5);$array_name = array(1=>'第一运输',2=>'第二运输',3=>'第三运输',4=>'第四运输',5=>'第五运输');  foreach($array as $i):  ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" ></label>
            <div class="col-sm-10">
                <span style="font-weight:bold;"><?php  echo $array_name[$i];  ?></span>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" >运输方式</label>

            <div class="col-sm-10">
                <select id="<?php echo "yunshufangshi".$i; ?>" name="<?php echo "yunshufangshi".$i; ?>">
                    <?php
                    if(isset($guowaitrans))
                    {
                        echo '<option value="">--请选择--</option>';
                        foreach($guowaitrans as $guowai)
                        {
                            if(isset($listinfo))
                            {
                                if($listinfo['international_type'.$i]==$guowai['shippingservice'])
                                {
                                    echo '<option  selected = "selected" value="'.$guowai['shippingservice'].'">'.$guowai['description'].'</option>';
                                    continue;
                                }
                            }
                            echo '<option value="'.$guowai['shippingservice'].'">'.$guowai['description'].'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >运费</label>
            <div class="col-sm-10">
                <input type="text"  name="<?php echo "yunfei".$i; ?>"  value="<?php  if(isset($listinfo['international_cost'.$i])){ echo $listinfo['international_cost'.$i]; } ?>"  placeholder="0.00"/><span class="wz">  USD </span><input type="checkbox" name="<?php  echo "mianfei".$i ?>"    <?php if(isset($listinfo['international_free'.$i])&&($listinfo['international_free'.$i]=='true')){ echo 'checked';} ?>   value="true"/><span class="red" >免费</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" >额外每件加收</label>
            <div class="col-sm-10">
                <input type="text" name="<?php echo "ewai".$i; ?>"   value="<?php  if(isset($listinfo['international_extracost'.$i])){ echo $listinfo['international_extracost'.$i]; } ?>"   placeholder="0.00"/><span class="wz">  USD </span>
            </div>
        </div>


        <div class="form-group"><label class="col-sm-2 control-label"   >运到</label>
            <div class="col-sm-6">
                <input type="checkbox"  name="<?php  echo "Worldwide".$i; ?>"  <?php if(isset($listinfo['international_is_worldwide'.$i])&&($listinfo['international_is_worldwide'.$i]=='on')){ echo 'checked';} ?> /><span>全球   </span>
            </div>
        </div>

        <div class="form-group"><label class="col-sm-2 control-label"   >运输国家</label>
            <div class="col-sm-4">

                <input  class="form-control" name="<?php echo "guanjia".$i."[]" ?>" value="<?php

                if(isset($listinfo['international_is_country'.$i])&&(!empty($listinfo['international_is_country'.$i]))){
                    $international_is_country =$listinfo['international_is_country'.$i];
                    $international_is_country = json_decode($international_is_country,true);
                    $international_is_country = implode(',',$international_is_country);
                    echo $international_is_country;
                }
                ?>">
            </div>
        </div>





    <?php endforeach; ?>



    <hr/>

    <div class="form-group">
        <label class="col-sm-2 control-label" >不运送国家</label>
        <div  class="col-sm-10">
            <input  class="col-sm-10" name="excludeship" type="text" value="<?php if(isset($listinfo['excludeship'])){ echo $listinfo['excludeship'];  }    ?>" > <span class="red">输入国家简称,不同国家用英文逗号隔开</span>
        </div>
    </div>

    <div class="clearfix form-actions align-center">
        <input type="hidden" name="action" id="action" value=""/>

        <button class="btn btn-success submit_btn " type="submit" name="save" >
            <i class="icon-ok bigger-110"></i>
            保存
        </button>
        <label>
            <a class="btn btn-inverse " href="<?php echo admin_base_url('ebay/ebay_product/transTemplateIndex');?>">
                <i class="icon-ok bigger-110"></i>返回列表</a>
        </label>


    </div>
</form>


<script>
    $(document).on('blur', ':text, #return_details', function () {
        $(this).val($(this).val().trim());
    });
    $(document).on('keyup', '#return_details', function () {
        var num = 5000;
        //num = $(this).attr('maxlength');
        var  now_length = $(this).val();
        $(this).height(this.scrollHeight);
        $(this).closest('div').find('.help-block').html('还能够输入<i class="red">' + (num - now_length.length) + '</i>个字符');
    });

    $(document).ready(function() {

        url = '<?php echo admin_base_url("ebay/ebay_product/getCurrencyinfo");?>';
        var site = $('#siteid').val();
        $.ajax({
            url: url,
            data: 'siteid=' + site,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                if (data.data['returnswithin'] != '') {
                    var nn = data.data['returnswithin'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuotianshu').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuotianshu').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_policy').length > 0) {
                        var acc = $('#returns_days').val();
                        $("#tuihuotianshu option[value=" + acc + "]").attr("selected", "true");

                    }


                }
                if (data.data['returnsaccepted'] != '') {
                    var nn = data.data['returnsaccepted'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuozhengce').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuozhengce').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_policy').length > 0) {
                        var acc = $('#returns_policy').val();
                        $("#tuihuozhengce option[value=" + acc + "]").attr("selected", "true");

                    }

                }
                if (data.data['shippingcostpaidby'] != '') {
                    var nn = data.data['shippingcostpaidby'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuochengdang').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuochengdang').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_cost_by').length > 0) {
                        var acc = $('#returns_cost_by').val();
                        $("#tuihuochengdang option[value=" + acc + "]").attr("selected", "true");

                    }
                }
                if (data.data['refund'] != '') {
                    var nn = data.data['refund'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuofangshi').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuofangshi').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_type').length > 0) {
                        var acc = $('#returns_type').val();
                        $("#tuihuofangshi option[value=" + acc + "]").attr("selected", "true");

                    }

                }
                else {
                    $('#tuihuofangshiall').addClass('hidden');
                    $('#tuihuofangshi').empty();
                }
                var currency = data.data['currency']
                $('.wz').each(function () {
                    $(this).html(currency);
                });
            }
        })



        $('.submit_btn').click(function (e) {
            if (e && e.preventDefault) {
                e.preventDefault();
                //IE中阻止函数器默认动作的方式
            }
            else {
                window.event.returnValue = false;
            }
            $('#action').val($(this).attr('name'));
        })

        $('.validate_form').Validform({
            btnSubmit: '.submit_btn',
            btnReset: '.btn-reset',
            ignoreHidden: true,
            ajaxPost: true,
            beforeSubmit:function(curform){
                 ii = layer.load('提交中');
            },
            callback: function (data) { //返回数据
                layer.close(ii);
                if(data.status==1)
                {
                    $('#id').val(data.data);
                    showxbtips(data.info, 'alert-success');
                }
                if(data.status==2)
                {
                    showxbtips(data.info, 'alert-success');
                }


            }
        });


    })

    $('#siteid').change(function () {
        url = '<?php echo admin_base_url("ebay/ebay_product/getCurrencyinfo");?>';
        var site = $('#siteid').val();
        $.ajax({
            url: url,
            data: 'siteid=' + site,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                if (data.data['returnswithin'] != '') {
                    var nn = data.data['returnswithin'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuotianshu').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuotianshu').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_policy').length > 0) {
                        var acc = $('#returns_days').val();
                        $("#tuihuotianshu option[value=" + acc + "]").attr("selected", "true");

                    }


                }
                if (data.data['returnsaccepted'] != '') {
                    var nn = data.data['returnsaccepted'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuozhengce').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuozhengce').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_policy').length > 0) {
                        var acc = $('#returns_policy').val();
                        $("#tuihuozhengce option[value=" + acc + "]").attr("selected", "true");

                    }

                }
                if (data.data['shippingcostpaidby'] != '') {
                    var nn = data.data['shippingcostpaidby'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuochengdang').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuochengdang').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_cost_by').length > 0) {
                        var acc = $('#returns_cost_by').val();
                        $("#tuihuochengdang option[value=" + acc + "]").attr("selected", "true");

                    }
                }
                if (data.data['refund'] != '') {
                    var nn = data.data['refund'];
                    $('#tuihuofangshiall').removeClass('hidden');
                    $('#tuihuofangshi').empty();
                    for (i = 0; i < nn.length - 1; i++) {
                        $('#tuihuofangshi').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                    }
                    if ($('#returns_type').length > 0) {
                        var acc = $('#returns_type').val();
                        $("#tuihuofangshi option[value=" + acc + "]").attr("selected", "true");

                    }

                }
                else {
                    $('#tuihuofangshiall').addClass('hidden');
                    $('#tuihuofangshi').empty();
                }
                var currency = data.data['currency']
                $('.wz').each(function () {
                    $(this).html(currency);
                });
            }
        })

        url = '<?php echo admin_base_url("ebay/ebay_product/getDetails");?>';
        var site = $('#siteid').val();
        $.ajax({
            url: url,
            data: 'siteid=' + site,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                var options = data.info;
                $("#guoneiyunshu1").empty().append(options);

            }
        })



        url = '<?php echo admin_base_url("ebay/ebay_product/getDetails");?>';
        var site = $('#siteid').val();
        $.ajax({
            url: url,
            data: 'internationalservice=1&siteid=' + site,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                var options = data.info;
                $("#yunshufangshi1").empty().append(options);
                $("#yunshufangshi2").empty().append(options);
                $("#yunshufangshi3").empty().append(options);
                $("#yunshufangshi4").empty().append(options);
                $("#yunshufangshi5").empty().append(options);

            }
        });
    })

    $(".fc").click(function(){
       var  chcekboxname =  $(this).parent().children().eq(4).children().eq(0).attr('name');
        $("input[name='"+chcekboxname+"']").each(function(){
             if($(this).is(':checked')) {
             $(this).removeProp('checked');
             $(this).prop('checked',false);
             }
             else
             {
             $(this).prop("checked",true);//全选
             }
        })
    })

</script>






