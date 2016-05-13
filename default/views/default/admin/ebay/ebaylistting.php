<style>


    #myModalSelect .modal-content {
        width: 800px;
    }

    #myModal .modal-content {
        width: 800px;
    }


    </style>

<div class="modal fade" id="myModal"  tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">物品分类</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">

                    <div class="form-group">
                        <div class="row" >
                            <div class="col-sm-8">
                            <div class="alert alert-success hidden" role="alert" id="tishi" >您已经选择了一个物品分类，请点击【确定】继续！</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <span>分类号</span>
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="row">
                    <div class="col-sm-6">
                        <select id="fenleizi1" size="10" >
                            <option>11111111111</option>
                        </select>
                    </div>
                        <div class="col-sm-6">
                        <select id="fenleizi2" size="10" class="hidden" >
                            <option>11111111111</option>
                        </select>
                        </div>
                    </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <select id="fenleizi3" size="10"  class="hidden">
                                    <option>11111111111</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select id="fenleizi4" size="10" class="hidden" >
                                    <option>11111111111</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <select id="fenleizi5" size="10"  class="hidden">
                                    <option>11111111111</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select id="fenleizi6" size="10" class="hidden" >
                                    <option>11111111111</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary fenlei" id="fenleiqueding1"  >第一分类确定</button>
                <button type="button" class="btn btn-primary fenlei" id="fenleiqueding2"  >第二分类确定</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModalSelect"    tabindex="-1" role="dialog"   aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" >分类搜索</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" onsubmit="return false;">
                    <div class="form-group">
                        <label  class="col-sm-2 control-label">搜索：</label>
                        <div class="col-sm-8">
                            <input type="text"  class="form-control" name="categoryselectval" id="categoryselectval"/>
                            </div>
                        <div class="col-sm-2">
                          <!--  <button class="btn btn-primary" id="categoryselect"  autofocus="">查询</button>-->
                           <a href="#"   class="btn btn-primary " id="categoryselect">查询</a>
                        </div>
                        </div>

                <table id="categoryselectid" class="table table-bordered" >
                    <tr><td></td><td>分类号</td><td>名称</td></tr>
                </table>

                    <div class="modal-footer">
                       <a href="#"   class="btn btn-primary " id="categoryselectsub">确定</a>
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

<form class="form-horizontal validate_form " action="<?php echo admin_base_url('ebay/ebayPublish/publish'); ?>" method="post">
<div class="panel panel-default">
    <div class="panel panel-default">
        <div class="row-fluid">
            <div class="span12">
                <div class="panel-body">
                    <span style="font-weight:bold;">一般信息</span></div>
                </div>
                <hr/>
                <div class="form-group">
                    <label  class="col-sm-2 control-label">名称：</label>
                    <div class="col-sm-8">
                        <input type="text"   name = 'mingcheng' class="form-control"  value="<?php
                        if(isset($listinfo))
                        {
                            echo $listinfo['name'];
                        }
                        ?>" >
                        </div>
                    </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">eBay账户：</label>
                <div class="col-sm-6">
                <select name="ebayaccount" id="ebayaccount">
                    <?php
                    foreach($userinfo as $arr)
                    {
                        $account[] = $arr['seller_account'];
                    }
                    sort($account);
                    foreach($account as $aco)
                    {
                     if(isset($listinfo))
                     {
                        if($aco==$listinfo['ebayaccount'])
                        {
                            echo '<option selected = "selected"  value="'.$aco.'">'.$aco.'</option>';
                        }
                         else
                         {
                             echo '<option value="'.$aco.'">'.$aco.'</option>';
                         }
                     }
                     else{
                         echo '<option value="'.$aco.'">'.$aco.'</option>';
                     }
                    }
                    ?>
                </select>
                    </div>
                </div>
            <div class="form-group">
                <label  class="col-sm-2 control-label" >站点：</label>
                <div class="col-sm-6">
                <select name="siteid" id="siteid">
                    <?php
                    foreach($side as $arr)
                    {
                        if(isset($listinfo))
                        {
                            if($listinfo['site']==$arr['siteid'])
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
            <div class="form-group">
                <label class="col-sm-2 control-label" >刊登类型:</label>
                <div class="col-sm-6">
                <input type="radio" name='leixing'  class="radioItem" id="paimai" checked="checked" value="paimai"<?php if(isset($listinfo)){if($listinfo['ad_type']=='paimai') echo 'checked';} ?> />拍卖
               <input type="radio" name='leixing' class="radioItem" id="guding" value="guding"<?php if(isset($listinfo)){if($listinfo['ad_type']=='guding') echo 'checked';  } ?>/>固定
               <input type="radio" name='leixing' class="radioItem" id="duoshuxing" value="duoshuxing" <?php if(isset($listinfo)){if($listinfo['ad_type']=='duoshuxing') echo 'checked';  } ?>/>多属性
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" >SKU：</label>
               <div class="col-sm-6">
                <input type="text"  id="inputsku" name="sku"  value="<?php if(isset($listinfo)){echo $listinfo['sku'];}  ?>">
                <a href="#" id="getskuinfo">获取SKU相关信息</a>
                </div>
            </div>
<!--            <div class="form-group">
                <label class="col-sm-2 control-label" >捆绑产品:</label>
                <div class="col-sm-6">
                <a href="">添加</a>
                </div>
            </div>-->
            <div class="form-group">
                <label class="col-sm-2 control-label" >物品标题:</label>
                <div class="col-sm-9">
                    <div class="panel panel-default">
                        <div class="row-fluid">
                            <br/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >标题:</label>
                                <div class="col-sm-9">
                                   <input type="text" maxlength="80" class="form-control" size="100" id="biaoti" name="biaoti" value="<?php if(isset($listinfo)){   $listinfo['title']=   str_replace('"', "&quot", $listinfo['title']);       echo $listinfo['title'];}  ?>"/>
                                    <div class="help-block">提示信息</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label " >子标题:</label>
                                <div class="col-sm-6">
                                    <input type="text" class=" input-sm" name="zibiaoti" value="<?php if(isset($listinfo)){echo $listinfo['title1'];}  ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
<!--
            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-6">
                    <a>添加新的标题 </a>
                </div>
            </div>-->


            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">分类</span></div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-sm-2 control-label" >第一分类：</label>
                <div class="col-sm-6">
                    <input type="text"  id="diyifenlei" name="diyifenlei" value="<?php if(isset($listinfo)){echo $listinfo['categoty1'];}  ?>" /> <a   href=""  id="fenlei1" data-toggle="modal" data-target="#myModal">选择分类</a>  <a   href="#"  data-toggle="modal" data-target="#myModalSelect">搜索</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                <input type="text"  name="diyifenleimiaoshu" id="diyifenleimiaoshu"  <?php
                if(isset($listinfo)&&($listinfo['categoty1_all'] !=''))
                {
                    echo  'value="'.$listinfo['categoty1_all'].'"';
                    echo 'class=" form-control"';

                }
                else
                {
                    echo 'class="hidden  form-control"';
                }  ?>
                        readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" >第二分类：</label>
                <div class="col-sm-6">
                    <input type="text"  id="dierfenlei" value="<?php if(isset($listinfo)){echo $listinfo['categoty2'];}  ?>"  name="dierfenlei"/> <a href="" id="fenlei2" data-toggle="modal" data-target="#myModal" >选择分类</a><span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                    <input type="text"  name="dierfenleimiaoshu" id="dierfenleimiaoshu"  <?php
                    if(isset($listinfo)&&($listinfo['categoty2_all'] !=''))
                    {
                        echo  'value="'.$listinfo['categoty2_all'].'"';
                        echo 'class=" form-control"';

                    }
                    else
                    {
                        echo 'class="hidden  form-control"';
                    }  ?>
                           readonly>
                </div>
            </div>
<!--            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">商店分类</span></div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-6">
                    <p class="text-success">请确认您的eBay账号开通了商店，否则无法使用商店分类。</p>
                    </div>
                </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" >第一分类：</label>
                <div class="col-sm-6">
                    <input type="text"  id="input" > <a href="">选择分类</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" >第二分类：</label>
                <div class="col-sm-6">
                    <input type="text"  id="input" > <a href="">选择分类</a>
                </div>
            </div>-->
            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">物品属性与状况</span></div>
            </div>
            <hr/>
            <div class="form-group ">
                <label class="col-sm-2 control-label">自定义物品属性</label>
                </div>
                <div class="form-group ">
                    <label class="col-sm-1 control-label"></label>
                <div class="col-sm-11">
                    <div class="hidden" id="not_mul"><span class="header-color-red">不支持多属性！</span></div>
                    <div class="<?php if(isset($listinfo)){ if($listinfo['ad_type']=='duoshuxing'){  }else{ echo 'hidden';} }else{echo 'hidden';} ?>" id="duoshuxingtable">
                  <!--  <input type="button"  class="btn btn-success"  value="Add" onclick="addNew();">-->
                    <input type="button"  class="btn btn-success" value="删除" onclick="del();">
                    <input type="button"  class="btn btn-success" value="新增属性" onclick="addSpe('');">
                     <input type="button"  class="btn btn-success" value="设置前缀" onclick="setper();">

                        <table id="table1" class="table table-bordered"  >
                            <tr >
                                <td><input type="checkbox" id="mul_check" /></td><td >SKU</td><td id="addnum" onclick="addnum(this.id)">数量</td><td id="addprice" onclick="addprice(this.id)">价格</td>
                                <?php if(isset($listinfo)){
                                    if(!empty($listinfo['add_mul']))
                                    {
                                        $mul = json_decode($listinfo['add_mul'],true);
                                        foreach($mul as $k=>$m)
                                        {
                                            echo '<td onclick="deleteinfoNew(this)">'.$m.'<input class="hidden" type="text" value='.'"'.$m.'"'.' name="zidingyi[]"></td>';
                                        }
                                        unset($mul);
                                    }
                                }?>
                            </tr>
                            <?php if(isset($listinfo)){
                                if(!empty($listinfo['mul_info']))
                                {
                                    $mul = json_decode($listinfo['mul_info'],true);
                                    $add_mul = json_decode($listinfo['add_mul'],true);
                                    foreach($mul as $k=>$m) {

                                        echo '<tr><td><input type="checkbox" name="count"></td><td><input type="text" value="' . $m['sku'] . '" name="skuinfo[sku][]" size="15"/></td>
                                        <td><input type="text" value="' . $m['qc'] . '"  name="skuinfo[qc][]" size="5"/></td><td><input type="text"  value="' . $m['price'] . '" name="skuinfo[price][]" size="5"/></td>';
                                     if(!empty($listinfo['add_mul'])) {

                                         foreach ($add_mul as $add) {
                                             echo '<td><input  type="text" size="15" value="' . $m[$add] . '" name="skuinfo[' . $add . '][]"/>';
                                         }
                                     }
                                        echo '</td></tr>';
                                    }
                                }
                            }?>
                            </table>
                    </div>
                    <div id="tupianshezhi" class="<?php  if(isset($listinfo['add_mul'])){ }else{echo "hidden";}?>" ><select  id="tupianshezhiselect" >
                            <?php  if(isset($listinfo['add_mul'])){ $mul = json_decode($listinfo['add_mul'],true);
                                foreach($mul as $m)
                                {
                                    echo '<option value="'.$m.'">'.$m.'</option>';
                                }
                            } ?></select> <input type="button"  class="btn btn-success" value="设置图片"  onclick="tupianshezhiNew();"/> </div>
                    <div class="form-group" id="multupianinfo">
                        <?php  if(isset($listinfo['add_mul']))
                        {

                            if(!empty($listinfo['mul_picture']))
                            {

                                $pic = json_decode($listinfo['mul_picture'],true);
                                foreach ( $pic as $k=> $p) {


                                    foreach($p as $pickey =>$picvalue)
                                    {
                                        echo '<div class="form-group center"><label class="col-sm-3 control-label center"><span>'.$k.'</span><br><span>'.$pickey.'</span><br><a class="btn btn-success" onclick="addpicNew(this)" data-id="'.$k.'-'.$pickey.'">使用外部图片</a></label>';
                                        echo'<div class="col-sm-4"><dl class="center"><dt><img width="100" height="100" src="'.$picvalue[0].'" style="border: 0px;"><input class="hidden" value="'.$picvalue[0].'" name="mulpic['.$k.']['.$pickey.'][]"></dt><dd><a onclick="deletedetail(this)" href="javascript: void(0);">删除</a></dd></dl></div></div>';

                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                        <table class="table table-bordered" id="wpsxtable" name="wpsxtable">
                        <tr><td width=150>名称</td><td width=600>值</td><td></td></tr>
                        <?php  if(isset($listinfo))
                                 {
                                     if(!empty($listinfo['item_specifics_user'])) {
                                         $sp = array();
                                         $sp = json_decode($listinfo['item_specifics_user']);
                                         $i = 0;
                                         foreach ($sp as $k => $s) {
                                             $i++;
                                             ?>
                                             <tr>
                                                 <td>自定义属性<?php echo $i;?></td>
                                                 <td>名称：<input type="text" name="wupinmiaoshuname[][<?php echo $i;?>]"
                                                               value="<?php echo $k ?>"/> 值:<input type="text" name="wupinmiaoshuzhi[][<?php echo $i; ?>]"  value="<?php echo $s ?>"/>
                                                 </td>
                                                 <td></td>
                                             </tr>
                                         <?php
                                         }
                                         unset($sp);
                                     }
                                     else{
                                         for($j=1;$j<4;$j++)
                                         {
                                             ?>
                                             <tr>
                                                 <td>自定义属性<?php echo $j;?></td>
                                                 <td>名称：<input type="text" name="wupinmiaoshuname[][<?php echo $j;?>]"
                                                               /> 值:<input type="text" name="wupinmiaoshuzhi[][<?php echo $j; ?>]" />
                                                 </td>
                                                 <td></td>
                                             </tr>
                            <?php

                                         }
                                     }
                                 }
                        ?>

                        <?php  if(isset($listinfo)&&!empty($listinfo['item_specifics']))
                                        {
                                            $sp = array();
                                            $sp = json_decode($listinfo['item_specifics'],true);
                                            $i =0;
                                            foreach($sp as $ke=>$ss)
                                            {

                                                foreach($ss as $k=>$s)
                                                {
                                                    ?>
                                                    <tr><td><?php echo $k  ?></td><td><input type="text" value="<?php  echo $s; ?>"  name="wupinmiaoshu[][<?php echo $k  ?>]" /> <select onchange="wpsxxz(this.id)"  class="download dropdown-select"  id="<?php  echo 'selee'.$i;?>">
                                                                <?php
                                                                $spe=0;
                                                                echo '<option>--请选择--</option>';
                                                                foreach($listinfo['speinfo'] as $s)
                                                                {
                                                                    if($k == $s['name'])
                                                                    {
                                                                        $spe = $s['specificvalue'];
                                                                    }
                                                                }
                                                                $specificvalue = explode('{@}',$spe);
                                                                if($specificvalue){
                                                                    foreach($specificvalue as $kkk=>$sp)
                                                                    {
                                                                        if($kkk==count($specificvalue)-1)
                                                                        {
                                                                            break;
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $k; ?>"><?php echo $sp  ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select></td><td></td></tr>
                                                <?php
                                                    $i++;
                                                }
                                            }
                                            unset($sp);
                                        }
                            ?>
                    </table>
                    </div>
            </div>

            <div class="form-group   <?php  if(isset($listinfo)){ if(empty($listinfo['upc'])){echo "hidden";} } else{   echo "hidden";       }  ?>" id="upcdiv">
                <label class="col-sm-2 control-label" >UPC：</label>

                <div class="col-sm-6">
                    <input type="text" name="upc" id="upc" value="<?php if(isset($listinfo)){    echo $listinfo['upc'];      } ?>" />
                    </div>
            </div>

            <div class="form-group <?php  if(isset($listinfo)){ if(empty($listinfo['isb'])){echo "hidden";} } else{   echo "hidden";       }  ?>" id="isbdiv">
                <label class="col-sm-2 control-label" >ISB：</label>

                <div class="col-sm-6">
                    <input type="text" name="isb" id="isb" value="<?php if(isset($listinfo)){    echo $listinfo['isb'];      } ?>"  />
                </div>
            </div>


            <div class="form-group <?php  if(isset($listinfo)){ if(empty($listinfo['ean'])){echo "hidden";} } else{   echo "hidden";       }  ?> " id="eandiv">
                <label class="col-sm-2 control-label" >EAN：</label>

                <div class="col-sm-6">
                    <input type="text" name="ean" id="ean"  value="<?php if(isset($listinfo)){    echo $listinfo['ean'];      } ?>" />
                </div>
            </div>

            <div class="form-group" id="wpzt1">
                <label class="col-sm-2 control-label" >物品状况：</label>
                <div class="col-sm-6">

                    <select id="wpms2" name="wpms2">
                        <?php
                        if(isset($listinfo['item_status_option']))
                        {
                           foreach($listinfo['item_status_option'] as $op)
                           {
                               if($op['condition_id'] == $listinfo['item_status'])
                               {

                                   echo '<option selected="selected" value="'.$op['condition_id'].'">'.$op['displayname'].'</option>';
                               }
                               else
                               {
                                   echo '<option  value="'.$op['condition_id'].'">'.$op['displayname'].'</option>';

                               }
                           }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group <?php if(isset($listinfo)){ if(empty($listinfo['item_status_description'])){ echo 'hidden';} } else{ echo 'hidden'; } ?> " id="wpms3">
                <label  class="col-sm-2 control-label">物品状况描述:</label>

                <div class="col-sm-10">
					<textarea name="wpms3" id="wpms4" class="form-control">
                        <?php  if(isset($listinfo['item_status_description'])) { echo $listinfo['item_status_description'];} ?>
					</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="row-fluid">
            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">eBay 物品描述</span></div>
            </div>
            <div class="span12">
                <div class="col-sm-2"><span style="font-weight:bold;">模板设置</span></div>
                </div>
        </div>
            <hr/>
        <div class="form-group">
            <label class="col-sm-2 control-label" >刊登模板：</label>
            <div class="col-sm-4">
                <select id="templatehtmlselect" name="templatehtmlselect">
                    <?php
                    if(isset($template))
                    {
                        echo '<option >--请选择--</option>';
                        foreach ( $template as $mo) {
                            if(isset($listinfo))
                            {
                                if($listinfo['publication_template_html'] == $mo['id'])
                                {
                                    echo '<option   selected="selected" value='.$mo['id'].'>'.$mo['template_name'].'</option>';
                                }
                                else
                                {
                                    echo '<option value='.$mo['id'].'>'.$mo['template_name'].'</option>';
                                }
                            }
                            else
                            {
                                echo '<option value='.$mo['id'].'>'.$mo['template_name'].'</option>';
                            }

                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" ></label>
            <div class="col-md-8">
                    <textarea  id="templatehtml" class="form-control" >
                        <?php
                        if(isset($listinfo))
                        {
                            if(isset($listinfo['resulttemplatehtml']))
                            {
                                echo htmlspecialchars_decode($listinfo['resulttemplatehtml']);

                            }

                        }
                        ?>
					</textarea>
            </div>
        </div>




                    <div class="form-group">
                        <label class="col-sm-2 control-label" >卖家描述：</label>
                        <div class="col-sm-4">
                           <select id="kandengmoban" name="templateid">
                               <?php
                               if(isset($mobanxinxi))
                               {
                                   echo '<option value="">--请选择--</option>';
                                   foreach ( $mobanxinxi as $mo) {
                                       if(isset($listinfo))
                                       {
                                           if($listinfo['publication_template'] == $mo['id'])
                                           {
                                               echo '<option   selected="selected" value='.$mo['id'].'>'.$mo['name'].'</option>';
                                           }
                                           else
                                           {
                                               echo '<option value='.$mo['id'].'>'.$mo['name'].'</option>';
                                           }
                                       }
                                       else
                                       {
                                           echo '<option value='.$mo['id'].'>'.$mo['name'].'</option>';
                                       }

                                   }
                               }
                               ?>
                           </select>
                        </div>
                    </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" ></label>
                <div class="col-md-8">
                    <textarea  id="detail1" class="form-control" >
                        <?php
                        if(isset($listinfo))
                        {
                            if(isset($listinfo['resulttemplate']))
                            {
                                echo htmlspecialchars_decode($listinfo['resulttemplate']);

                            }

                        }
                        ?>
					</textarea>
                </div>
            </div>

            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">模板标题</span></div>
            </div>
            <hr/>
            <div class="form-group">
                <label  class="col-sm-2 control-label">模板标题：</label>
                <div class="col-sm-6">
                    <input type="text"  name="template_title"  value="<?php if(isset($listinfo)){ echo $listinfo['template_title']; }  ?>" >
                </div>
            </div>
            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">eBay图片</span></div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                    <p class="text-success">图片不要多余12张</p>
                </div>
            </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="red">*</span>产品图片：</label>
                    <div class="col-sm-10">
                        <input type="button"  class="btn btn-success" value="新图片服务器图片" id="addnewpictrue"  onclick="addnewpicforproduct(this.id);"/>
                        <input type="button"  class="btn btn-success" value="复制到模板图片"   onclick="copytptempic();"/>
                        <input type="button"  class="btn btn-success" value="增加SP图片" id="addsppictrue"  onclick="addsppicforproduct(this.id);"/>
                        <input type="button"  class="btn btn-success" value="设置图片" id="addpictrue"  onclick="addpicforproduct(this.id);"/>
                        <ul class="list-inline pic-main" id="test123">
                                 <!--<li>-->

                                            <tr>
                                            <?php
                                            if(isset($listinfo))
                                            {
                                                if(!empty($listinfo['ebay_picture']))
                                                {
                                               $pc =json_decode($listinfo['ebay_picture'],true);
                                               foreach($pc as $k=>$c)
                                               {
                                                   $id = 'tupian'.$k;
                                                   $name ='tupian'.$k;
                                                   $src = stripslashes($c);
                                                   $value = stripslashes($c);
                                                   if(($k%6==0)&&($k!=0))
                                                   {
                                                      echo '<br/>';
                                                     }
                                            ?>
                               <li><span id="<?php echo $id; ?>"><img name="<?php echo $name; ?>"  src="<?php echo $src; ?>"  width="100" height="100" style="border: 0px;" /> <input type="hidden" name="imgLists[]" value="<?php echo $value; ?>"/> <a href="javascript: void(0);" name="<?php echo $name; ?>"  class="pic-del">删除</a></span></li>
                                    <?php
                                               }
                                                }
                                            }
                                            ?>
                                            </tr>

                                   <!-- </li>-->
                        </ul>

                    </div>
                </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" ></label>
            <div class="col-sm-10">
                <p class="text-success">以下图片用于模板中</p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label"></span>模板图片：</label>
            <div class="col-sm-10">
                <input type="button"  class="btn btn-success" value="设置图片" id="addpictruetemp"  onclick="addpicforproducttemp(this.id);"/>

                <ul class="list-inline pic-main" id="templatepic">
                    <!--<li>-->

                    <tr>
                        <?php
                        if(isset($listinfo))
                        {
                            if(!empty($listinfo['template_deteils']))
                            {
                                $pc =json_decode($listinfo['template_deteils'],true);
                                foreach($pc as $k=>$c)
                                {
                                    $id = 'temptupian'.$k;
                                    $name ='temptupian'.$k;
                                    $src = stripslashes($c);
                                    $value = stripslashes($c);
                                    if(($k%6==0)&&($k!=0))
                                    {
                                        echo '<br/>';
                                    }
                                    ?>
                                    <li><span id="<?php echo $id; ?>"><img name="<?php echo $name; ?>"  src="<?php echo $src; ?>"  width="100" height="100" style="border: 0px;" /> <input type="hidden" name="tempimgLists[]" value="<?php echo $value; ?>"/> <a href="javascript: void(0);" name="<?php echo $name; ?>"  class="pic-del">删除</a></span></li>
                                <?php
                                }
                            }
                        }
                        ?>
                    </tr>

                    <!-- </li>-->
                </ul>

            </div>
        </div>

            <div class="form-group">
                <label  class="col-sm-2 control-label">描述标题：</label>
                <div class="col-sm-10">
                    <input type="text"  name="description_title" value="<?php if(isset($listinfo)){ echo $listinfo['description_title']; }  ?>" >
                </div>
            </div>
<!--
            <div class="form-group">
                <label  class="col-sm-2 control-label">描述：</label>
                <div class="col-sm-6">
                    <select>
                        <option>--选择--</option>
                    </select>
                </div>
            </div>-->
            <div class="form-group">
                <label  class="col-sm-2 control-label">详情描述:</label>

                <div class="col-sm-10">
					<textarea name="detail" id="detail" class="form-control" >
                        <?php
                        if(isset($listinfo))
                        {
                            echo htmlspecialchars_decode($listinfo['description_details']);
                        }
                        ?>

					</textarea>
                </div>
            </div>
        </div>
    <div class="panel panel-default">
        <div class="row-fluid">

            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">拍卖</span></div>
            </div>
            <hr/>
         <!--   <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-6">
                    <p class="text-success">请确认您的eBay账号开通了商店，否则无法使用折扣。</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"   >折扣</label>
                <div class="col-sm-6">
                    <select>
                        <option>--选择--</option>
                    </select>
                    <a href="">添加</a>
                </div>
            </div>-->
            <div class="form-group">
                <label class="col-sm-2 control-label" >私人拍卖</label>
                <div class="col-sm-6">
                    <input name="sirenpaimai" type="checkbox" <?php if(isset($listinfo)){ if($listinfo['auction_is_private']=='true'){ echo 'checked'; }} ?>  value="true" /><span>不向公众显示买家的名称 </span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" >刊登天数</label>
                <div class="col-sm-6">
                    <?php
                    if(isset($listinfo))
                    {
                        ?>
                        <input type="text" id="published_day" value="<?php echo $listinfo['published_day']; ?>" class="hidden"/>
                    <?php
                    }
                    ?>
                    <select id="paimaitianshu" name="paimaitianshu">
                    </select>
                    <span>天</span>
                </div>
            </div>

            <div class="form-group" id="paimaijiage">
                <label class="col-sm-2 control-label" >价格</label>
                <div class="col-sm-6">
                    <input type="text" id="paimaiprice" name="paimaijiage" value="<?php if(isset($listinfo)){ echo $listinfo['price']; }?>" placeholder="0.00"/><span class="wz">  USD</span>
                </div>
            </div>

            <div class="form-group" id="baoliujia">
                <label class="col-sm-2 control-label" >保留价</label>
                <div class="col-sm-6">
                    <input type="text" name="paimaibaoliujia" value="<?php if(isset($listinfo)){ echo $listinfo['reserve_price']; }?>"  placeholder="0.00"/><span  class="wz">  USD</span>
                </div>
            </div>

            <div class="form-group" id="yikoujia">
                <label class="col-sm-2 control-label" >一口价</label>
                <div class="col-sm-6">
                    <input type="text" name="paimaiyikoujia"  value="<?php if(isset($listinfo)){ echo $listinfo['price_noce']; }?>" placeholder="0.00"/><span  class="wz">  USD</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                    <select>
                        <option>单独物品</option>
                     <!--   <option>批量物品</option>-->
                    </select>
                    <span>数量</span>
                    <input type="text" name="kandengshuliang" value="<?php if(isset($listinfo)){ echo $listinfo['quantity']; }?>" placeholder="1"/><!--<span>销售比基数</span><input type="text" name="" placeholder="1"/>-->
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="row-fluid">
            <div class="span12">
                <div class="col-sm-2"><span style="font-weight:bold;">付款</span></div>
                 <!--  <div class="col-sm-10" align="right"> <button class="btn btn-default btn-xs" type="submit"  >选择</button>
                    <button class="btn btn-default btn-xs" type="submit">另存为</button>
                   </div>-->
            </div>
            <br/>
            <hr/>

            <div class="form-group">
                <label class="col-sm-2 control-label" >PayPal</label>
                <div class="col-sm-10">
                    <select name="paypalaccount" id="paypalaccount">
                        <?php
                        if(isset($account_list))
                        {
                            foreach($account_list as $acc)
                            {
                                $new_paypal = $acc['paypal_email_address'];
                                $new_paypal =  substr_replace($new_paypal,'****','2','6');

                                if(isset($listinfo['paypal_account']))
                                {
                                    if($listinfo['paypal_account'] == $acc['paypal_email_address'])
                                    {
                                        echo '<option selected="selected" value="'.$acc['paypal_email_address'].'">'.$new_paypal.'</option>';

                                    }
                                    else
                                    {
                                        echo '<option value="'.$acc['paypal_email_address'].'">'.$new_paypal.'</option>';

                                    }

                                }
                                else
                                {
                                    echo '<option value="'.$acc['paypal_email_address'].'">'.$new_paypal.'</option>';

                                }
                            }
                        }
                        ?>
                    </select>

                    <input class="btn btn-success" type="button" onclick="autopaypal()"  value="点击匹配">
                </div>
            </div>
<!--            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>American express </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Cash on pickup accepted </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Discover card </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Integrated merchant credit card </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Money order / Cashier's check </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Other - See item description </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Personal check </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Visa or Master card </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Require immediate payment </span>
                    <br/>
                    <input name="love" type="checkbox" id="aa" value="音乐" /><span>Other online payments </span>
                    <br/>
                </div>
            </div>-->

                <div class="form-group">
                    <label class="col-sm-2 control-label" >付款说明</label>
                    <div class="col-sm-10">
                       <textarea name="shuoming" id="shuoming" class="form-control">
                           <?php
                           if(isset($listinfo))
                           {
                               echo $listinfo['payment_details'];
                           }
                           ?>
					</textarea>
                        <!--<span>0/( 最多 500 字符. 不支持 HTML )</span>-->
                    </div>
                </div>
            </div>
        </div>

    <div class="panel panel-default">
        <div class="row-fluid">
            <div class="span12">
                <div class="col-sm-2"><span style="font-weight:bold;">买家要求</span></div>
            <!--    <div class="col-sm-10" align="right"> <button class="btn btn-default btn-xs" type="submit"  >选择</button>
                    <button class="btn btn-default btn-xs" type="submit">另存为</button>
                </div>-->
            </div>
            <br/>
            <hr/>

            <div class="form-group ">
                <label class="col-sm-2 control-label" >买家要求</label>
                <div class="col-sm-10">
                    <input type="radio" name="yaoqiu" value="all"  <?php if(isset($listinfo)){ if($listinfo['all_buyers']=='all') { echo 'checked="checked"'; } }  ?> ><span>允许所有买家购买我的物品</span>
                    <br/>
                    <input type="radio" name="yaoqiu" value="notall" <?php if(isset($listinfo)){ if($listinfo['all_buyers']=='notall') { echo 'checked="checked"'; } }  else{ echo 'checked="checked"';}  ?> ><span>不允许以下买家购买我的物品</span>
                </div>
            </div>
            <div class="form-group  <?php if(isset($listinfo)) { if($listinfo['all_buyers']=='all') { echo 'hidden'; }}  ?> " id="maijiayaoqiudetail">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                        <input type="checkbox"  <?php if(isset($listinfo)&&($listinfo['nopaypal']=='on')){ echo 'checked';} ?> name="nopaypal"/>没有 PayPal 账户 <br/>
                        <input type="checkbox"  <?php if(isset($listinfo)&&($listinfo['noti_trans']=='on')){ echo 'checked';}  ?>  name="yunshufangweizhiwai"  checked="checked"  />主要运送地址在我的运送范围之外<br/>
                    <input type="checkbox"  <?php if(isset($listinfo)&&($listinfo['is_abandoned']=='on')){ echo 'checked';} ?>  id="qibiao" name="qibiao"><span>曾收到</span>
                    <select id="qibiaonum" name="qibiaonum"   <?php if(isset($listinfo)){ if(($listinfo['is_abandoned']!='on')){echo 'disabled="disabled"';}} else{ echo 'disabled="disabled"'; } ?>  >
                        <option value="2" <?php if(isset($listinfo)){if($listinfo['abandoned_num']==2){ echo 'selected="selected"';  } } ?> >2</option>
                        <option value="3" <?php if(isset($listinfo)){if($listinfo['abandoned_num']==3){ echo 'selected="selected"';  } } ?>>3</option>
                        <option value="4" <?php if(isset($listinfo)){if($listinfo['abandoned_num']==4){ echo 'selected="selected"';  } } ?>>4</option>
                        <option value="5" <?php if(isset($listinfo)){if($listinfo['abandoned_num']==5){ echo 'selected="selected"';  } } ?>>5</option>
                    </select>
                    <span>个弃标个案，在过去</span>
                    <select id="qibiaotianshu" name="qibiaotianshu" <?php if(isset($listinfo)){ if(($listinfo['is_abandoned']!='on')){echo 'disabled="disabled"';}} else{ echo 'disabled="disabled"'; } ?> >
                        <option value="Days_30" <?php if(isset($listinfo)){if($listinfo['abandoned_day']=='Days_30'){ echo 'selected="selected"';  } } ?>>30</option>
                        <option value="Days_180" <?php if(isset($listinfo)){if($listinfo['abandoned_day']=='Days_180'){ echo 'selected="selected"';  } } ?>>180</option>
                        <option value="Days_360" <?php if(isset($listinfo)){if($listinfo['abandoned_day']=='Days_360'){ echo 'selected="selected"';  } } ?> >360</option>
                    </select>
                    <span>天</span>
                    <br/>
                    <input type="checkbox" id="jianjv" name="jianjv" <?php if(isset($listinfo)&&($listinfo['is_report']=='on')){ echo 'checked';} ?> ><span>曾收到</span>
                    <select id="jianjvnum" name="jianjvnum" <?php if(isset($listinfo)){ if(($listinfo['is_report']!='on')){echo 'disabled="disabled"';}} else{ echo 'disabled="disabled"'; } ?> >
                        <option value="4" <?php if(isset($listinfo)){if($listinfo['report_num']==4){ echo 'selected="selected"';  } } ?> >4</option>
                        <option value="5" <?php if(isset($listinfo)){if($listinfo['report_num']==5){ echo 'selected="selected"';  } } ?> >5</option>
                        <option value="6" <?php if(isset($listinfo)){if($listinfo['report_num']==6){ echo 'selected="selected"';  } } ?> >6</option>
                        <option value="7" <?php if(isset($listinfo)){if($listinfo['report_num']==7){ echo 'selected="selected"';  } } ?> >7</option>
                    </select>
                    <span>个违反政策检举，在过去</span>
                    <select id="jianjvtianshu"  name="jianjvtianshu" <?php if(isset($listinfo)){ if(($listinfo['is_report']!='on')){echo 'disabled="disabled"';}} else{ echo 'disabled="disabled"'; } ?>">
                        <option value="Days_30"  <?php if(isset($listinfo)){if($listinfo['report_day']=='Days_30'){ echo 'selected="selected"';  } } ?> >30</option>
                        <option value="Days_180"  <?php if(isset($listinfo)){if($listinfo['report_day']=='Days_180'){ echo 'selected="selected"';  } } ?> >180</option>
                    </select>
                    <span>天</span>
                    <br/>
                    <input type="checkbox" id="xinyong" name="xinyong" <?php if(isset($listinfo)&&($listinfo['is_trust_low']=='on')){ echo 'checked';} ?> ><span>信用指标等于或低于：</span>
                    <select id="xinyongnum" name="xinyongnum" <?php if(isset($listinfo)){ if(($listinfo['is_trust_low']!='on')){echo 'disabled="disabled"';}} else{ echo 'disabled="disabled"'; } ?> >
                        <option value="-1" <?php if(isset($listinfo)){if($listinfo['trust_low_num']==-1){ echo 'selected="selected"';  } } ?> >-1</option>
                        <option value="-2" <?php if(isset($listinfo)){if($listinfo['trust_low_num']==-2){ echo 'selected="selected"';  } } ?> >-2</option>
                        <option value="-3" <?php if(isset($listinfo)){if($listinfo['trust_low_num']==-3){ echo 'selected="selected"';  } } ?> >-3</option>
                    </select>
                    <br/>
                    <input type="checkbox" id="goumai" name="goumai"   <?php if(isset($listinfo)&&($listinfo['already_buy']=='on')){ echo 'checked';} ?>  ><span>在过去10天内曾出价或购买我的物品，已达到我所设定的限制</span>
                    <select id="goumainum" name="goumainum" <?php if(isset($listinfo)){ if(($listinfo['already_buy']!='on')){echo 'disabled="disabled"';}}?> >
                        <option value="1" <?php if(isset($listinfo)){if($listinfo['buy_num']==1){ echo 'selected="selected"';  } } ?> >1</option>
                        <option value="2" <?php if(isset($listinfo)){if($listinfo['buy_num']==2){ echo 'selected="selected"';  } } ?> >2</option>
                        <option value="3" <?php if(isset($listinfo)){if($listinfo['buy_num']==3){ echo 'selected="selected"';  } } ?> >3</option>
                        <option value="4" <?php if(isset($listinfo)){if($listinfo['buy_num']==4){ echo 'selected="selected"';  } } ?> >4</option>
                        <option value="5" selected="selected"  <?php if(isset($listinfo)){if($listinfo['buy_num']==5){ echo 'selected="selected"';  } } ?> >5</option>
                        <option value="6" <?php if(isset($listinfo)){if($listinfo['buy_num']==6){ echo 'selected="selected"';  } } ?> >6</option>
                        <option value="7" <?php if(isset($listinfo)){if($listinfo['buy_num']==7){ echo 'selected="selected"';  } } ?> >7</option>
                        <option value="8" <?php if(isset($listinfo)){if($listinfo['buy_num']==8){ echo 'selected="selected"';  } } ?> >8</option>
                        <option value="9" <?php if(isset($listinfo)){if($listinfo['buy_num']==9){ echo 'selected="selected"';  } } ?> >9</option>
                        <option value="10" <?php if(isset($listinfo)){if($listinfo['buy_num']==10){ echo 'selected="selected"';  } } ?> >10</option>
                        <option value="25" <?php if(isset($listinfo)){if($listinfo['buy_num']==25){ echo 'selected="selected"';  } } ?> >25</option>
                        <option value="50" <?php if(isset($listinfo)){if($listinfo['buy_num']==50){ echo 'selected="selected"';  } } ?> >50</option>
                        <option value="75" <?php if(isset($listinfo)){if($listinfo['buy_num']==75){ echo 'selected="selected"';  } } ?> >75</option>
                        <option value="100" <?php if(isset($listinfo)){if($listinfo['buy_num']==100){ echo 'selected="selected"';  } } ?> >100</option>
                    </select>
                    <br/>
                   <div <span>&nbsp; &nbsp;&nbsp;</span><input type="checkbox" name="maijiaxinyong" id="maijiaxinyong" <?php if(isset($listinfo)&&($listinfo['buy_condition']=='on')){ echo 'checked';} ?>  <?php if(isset($listinfo)){ if(($listinfo['already_buy']!='on')){echo 'disabled="disabled"';}}  ?>><span>这项限制只适用于买家信用指数等于或低于</span>
                    <select id="maijiaxinyongnum" name="maijiaxinyongnum"  <?php if(isset($listinfo)){ if(($listinfo['buy_condition']!='on')){echo 'disabled="disabled"';}} ?> >
                        <option value="5" <?php if(isset($listinfo)){if($listinfo['buy_credit']==5){ echo 'selected="selected"';  } } ?> >5</option>
                        <option value="4" <?php if(isset($listinfo)){if($listinfo['buy_credit']==4){ echo 'selected="selected"';  } } ?> >4</option>
                        <option value="3" <?php if(isset($listinfo)){if($listinfo['buy_credit']==3){ echo 'selected="selected"';  } } ?> >3</option>
                        <option value="2" <?php if(isset($listinfo)){if($listinfo['buy_credit']==2){ echo 'selected="selected"';  } } ?> >2</option>
                        <option value="1" <?php if(isset($listinfo)){if($listinfo['buy_credit']==1){ echo 'selected="selected"';  } } ?> >1</option>
                        <option value="0" <?php if(isset($listinfo)){if($listinfo['buy_credit']==0){ echo 'selected="selected"';  } } ?> >0</option>
                    </select>
                   </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="row-fluid">
            <div class="span12">
                <div class="col-sm-2"><span style="font-weight:bold;">退货政策</span></div>
             <!--   <div class="col-sm-10" align="right"> <button class="btn btn-default btn-xs" type="submit"  >选择</button>
                    <button class="btn btn-default btn-xs" type="submit">另存为</button>
                </div>-->
            </div>
            <br/>
            <hr/>


            <div class="form-group">
                <label class="col-sm-2 control-label" >运输信息快速设置</label>
                <div class="col-sm-6">
                    <select id="transtemplate">
                        <?php

                        if(isset($trans_list)&&!empty($trans_list))
                        {
                            echo '<option value="">--请选择--</option>';
                            foreach($trans_list as $list)
                            {
                                echo '<option value="'.$list['id'].'">'.$list['transtemplatename'].'</option>';
                            }
                        }
                        ?>

                    </select>
                </div>

            </div>
            <hr/>
            <div class="form-group">
                <label class="col-sm-2 control-label" >退货政策</label>
                <?php
                if(isset($listinfo))
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
                if(isset($listinfo))
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
                <label class="col-sm-2 control-label" ><input type="checkbox"  id="returns_delay"  name="returns_delay"
                        <?php
                        if(isset($listinfo))
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
                if(isset($listinfo))
                {
                    ?>
                    <input class="hidden" type="text" id="returns_type" value="<?php echo $listinfo['returns_type']; ?>">
                <?php
                }
                ?>
                <div class="col-sm-10">
                    <select name="tuihuofangshi" id="tuihuofangshi">
                        <option value="Money Back">Money Back</option>
                        <option value="Money Back or replacement(buyer's choice)">Money Back or replacement(buyer's choice)</option>
                        <option value="Money Back or exchange(buyer's choice)">Money Back or exchange(buyer's choice)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" >退货运费由谁负担</label>
                <?php
                if(isset($listinfo))
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
                       <textarea name="return_details"   id="return_details" class="form-control">
                           <?php
                           if(isset($listinfo))
                           {
                               echo $listinfo['return_details'];
                           }
                           ?>
					</textarea>
                    <!--<span>0/( 最多 500 字符. 不支持 HTML )</span>-->
                </div>
            </div>
        </div>
    </div>


            <div class="panel panel-default">
                <div class="row-fluid">
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
                            <input type="text" id="item_location" name="item_location" value="<?php  if(isset($listinfo['item_location'])){ echo $listinfo['item_location']; } ?>" />
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
                            <input type="text"  id="item_post"name="item_post" value="<?php  if(isset($listinfo['item_post'])){ echo $listinfo['item_post']; } ?>" />
                        </div>
                    </div>
                </div>
            </div>
    <div class="panel panel-default">
        <div class="row-fluid">
            <div class="span12">
                <div class="col-sm-2"><span style="font-weight:bold;">运送选项</span></div>
               <!-- <div class="col-sm-10" align="right"> <button class="btn btn-default btn-xs" type="submit"  >选择</button>
                    <button class="btn btn-default btn-xs" type="submit">另存为</button>
                </div>-->
            </div>
            <br/>

<!--
            <div class="form-group">
                <label class="col-sm-2 control-label" >国内运输类型</label>
                <div class="col-sm-10">
                   <select>
                       <option>标准</option>
                       <option>计算</option>
                   </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" >国际运输类型</label>
                <div class="col-sm-10">
                    <select>
                        <option>标准</option>
                        <option>计算</option>
                    </select>
                </div>
            </div>-->
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
                    <select name="guoneichulishijian" id="guoneichulishijian">
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
                    <input type="checkbox" name="guoneikuaisu"  id="guoneikuaisu"  <?php if(isset($listinfo)&&$listinfo['inter_fast_send']=='true') { echo 'checked'; } ?>  value="true"/>
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
                <?php
                if(isset($listinfo))
                {
                    ?>
                    <input type="text" id="inter_trans_type" value="<?php echo $listinfo['inter_trans_type']; ?>" class="hidden"/>
                <?php
                }
                ?>
                <div class="col-sm-10">
                  <select id="guoneiyunshu1" name="guoneiyunshu1">
                 <!--     --><?php
/*                    if(!empty($listinfo['guonei_trans']))
                      {
                          foreach($listinfo['guonei_trans'] as $gw)
                          {
                              if($gw['shippingservice'] == $listinfo['inter_trans_type'])
                              {

                                  echo '<option selected="selected"  value="'.$gw['shippingservice'].'">'.$gw['description'].'</option>';

                              }
                              else
                              {
                                  echo '<option  value="'.$gw['shippingservice'].'">'.$gw['description'].'</option>';
                              }
                          }
                      }
                      */?>
                  </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" >运费</label>
                <div class="col-sm-10">
                 <input type="text"  id="guoneiyunfei1" name="guoneiyunfei1" value="<?php  if(isset($listinfo['inter_trans_cost'])){ echo $listinfo['inter_trans_cost']; } ?>" placeholder="0.00"/><span class="wz">  USD </span><input type="checkbox" id="guoneimianfei1" name="guoneimianfei1"  <?php if(isset($listinfo['inter_free'])&&($listinfo['inter_free']=='true')){ echo 'checked';} ?>  value="true"/><span class="red" >免费</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" >额外每件加收</label>
                <div class="col-sm-10">
                    <input type="text" id="guoneiewaijiashou1"  name="guoneiewaijiashou1" value="<?php  if(isset($listinfo['inter_trans_extracost'])){ echo $listinfo['inter_trans_extracost']; } ?>" placeholder="0.00"/><span class="wz">  USD </span>
                </div>
            </div>

            <div class="form-group hidden">
                <label class="col-sm-2 control-label" >AK,HI,PR 额外收费</label>
                <div class="col-sm-10">
                    <input type="text" name="guoneiAKewaijiashou1"  value="<?php  if(isset($listinfo['inter_trans_AK_extracost'])){ echo $listinfo['inter_trans_AK_extracost']; } ?>" placeholder="0.00"/><span class="wz">  USD </span>
                </div>
            </div>

<!--            <div class="form-group">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10">
                    <a href="">添加</a>
                </div>
            </div>-->

            <div class="span12">
                <div class="panel-body"><span style="font-weight:bold;">国际运输</span></div>
            </div>
            <hr/>

            <div class="form-group" id="InputsWrapper">
            <?php
            if(isset($listinfo)&&($listinfo['international_type1'] !=''))
            {
                if(!empty($listinfo['international_type1']))
                {
                    $trans['第一运输'] =1;
                }
                if(!empty($listinfo['international_type2']))
                {
                    $trans['第二运输'] =2;
                }
                if(!empty($listinfo['international_type3']))
                {
                    $trans['第三运输'] =3;
                }
                if(!empty($listinfo['international_type4']))
                {
                    $trans['第四运输'] =4;
                }
                if(!empty($listinfo['international_type5']))
                {
                    $trans['第五运输'] =5;
                }
               foreach($trans as $k=>$tr)
               {
                   if(isset($listinfo['international_is_country'.$tr]))
                   {
                       $co = json_decode($listinfo['international_is_country'.$tr],true);
                   }
                   ?>

                   <div  id="<?php echo 'yunshu'.$tr;?>"><div class="form-group"><label class="col-sm-2 control-label"   ></label>
                           <div class="col-sm-6"><span><?php echo $k;?></span></div></div>
                       <div class="form-group"><label class="col-sm-2 control-label"   >运输方式</label><div class="col-sm-6"><select name="<?php echo 'yunshufangshi'.$tr;?>" id="<?php echo 'yunshufangshi'.$tr;?>">
                                   <?php
                                   if(!empty($listinfo['guowai_trans']))
                                   {
                                       foreach($listinfo['guowai_trans'] as $gw)
                                       {
                                           if($gw['shippingservice'] == $listinfo['international_type'.$tr])
                                           {
                                               echo '<option selected="selected"  value="'.$gw['shippingservice'].'">'.$gw['description'].'('.$gw['shippingtimemin'].'-'.$gw['shippingtimemax'].')</option>';

                                           }
                                           else

                                           {
                                               echo '<option  value="'.$gw['shippingservice'].'">'.$gw['description'].'('.$gw['shippingtimemin'].'-'.$gw['shippingtimemax'].')</option>';
                                           }
                                       }
                                   }
                                   ?>


                               </select></div></div>
                       <div class="form-group"><label class="col-sm-2 control-label"   >运费</label><div class="col-sm-6"><input type="text" name="<?php echo 'yunfei'.$tr;?>" id="<?php echo 'yunfei'.$tr;?>" value="<?php  if(isset($listinfo['international_cost'.$tr])){ echo $listinfo['international_cost'.$tr]; } ?>" placeholder="0.00"/><input type="checkbox"  <?php  if(isset($listinfo['international_free'.$tr])&&($listinfo['international_free'.$tr]=='true')){ echo 'checked'; } ?> name="<?php echo 'mianfei'.$tr;?>" value="true"  id="<?php echo 'mianfei'.$tr;?>"/> <span>免费</span> </div></div>
                       <div class="form-group"><label class="col-sm-2 control-label"   >额外每件加收</label><div class="col-sm-6"><input type="text" value="<?php  if(isset($listinfo['international_extracost'.$tr])){ echo $listinfo['international_extracost'.$tr]; } ?>" name="<?php echo 'ewai'.$tr;?>" id="<?php echo 'ewai'.$tr;?>" placeholder="0.00"/></div></div>
                       <div class="form-group"><label class="col-sm-2 control-label"   >运到</label>
                           <div class="col-sm-6">
                               <input type="checkbox" <?php  if(isset($listinfo['international_is_worldwide'.$tr])&&($listinfo['international_is_worldwide'.$tr]=='on')){ echo 'checked'; } ?>   name="<?php echo 'Worldwide'.$tr; ?>" id="<?php echo 'quanqiu'.$tr;?>"/><span>全球   </span>
                               <a href="#" id="<?php echo 'guanjia'.$tr;?>"  class="fc">选择以下所有国家和地区</a>
                               <br/>
                               <input type="checkbox" value="CN" <?php if(isset($co)&&(in_array('CN',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>中国</input>
                               <input type="checkbox" value="RU" <?php if(isset($co)&&(in_array('RU',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>俄罗斯联邦</input>
                               <input type="checkbox" value="CA" <?php if(isset($co)&&(in_array('CA',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>加拿大</input>
                               <input type="checkbox" value="BR" <?php if(isset($co)&&(in_array('BR',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>巴西</input>
                               <input type="checkbox" value="DE" <?php if(isset($co)&&(in_array('DE',$co))){ echo 'checked';} ?>  name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>德国</input>
                               <input type="checkbox" value="FR" <?php if(isset($co)&&(in_array('FR',$co))){ echo 'checked';} ?>  name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>法国</input>
                               <input type="checkbox" value="Europe" <?php if(isset($co)&&(in_array('Europe',$co))){ echo 'checked';} ?>  name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>欧洲</input>
                               <input type="checkbox" value="GB"  <?php if(isset($co)&&(in_array('GB',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>联合王国</input>
                               <input type="checkbox" value="EuropeanUnion"  <?php if(isset($co)&&(in_array('EuropeanUnion',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>欧盟</input>
                               <input type="checkbox" value="Americas" <?php if(isset($co)&&(in_array('Americas',$co))){ echo 'checked';} ?>  name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>美洲</input>
                               <input type="checkbox" value="US" <?php if(isset($co)&&(in_array('US',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>美国</input>
                               <input type="checkbox" value="Asia" <?php if(isset($co)&&(in_array('Asia',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>亚洲</input>
                               <input type="checkbox" value="AU" <?php if(isset($co)&&(in_array('AU',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>澳大利亚</input>
                               <input type="checkbox" value="MX" <?php if(isset($co)&&(in_array('MX',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>墨西哥</input>
                               <input type="checkbox" value="JP" <?php if(isset($co)&&(in_array('JP',$co))){ echo 'checked';} ?> name="<?php echo 'guanjia'.$tr.'[]'; ?>"/>日本</input>
                           </div>
                       </div>
                   </div>
                <?php

                   unset($co);
               }
            }
            ?>
</div>
            <div class="form-group">
            <label class="col-sm-2 control-label" >不运送国家</label>
            <div  class="col-sm-10">
                <input  id="excludeship"  name="excludeship" type="text" value="<?php   if(isset($listinfo['excludeship'])){echo  $listinfo['excludeship'];  }   ?>" > <span class="red">输入国家简称,不同国家用,隔开</span>
                </div>
            </div>


            <div class="form-group" id="guojiyunshuinfo">
                <label class="col-sm-2 control-label" ></label>
                <div class="col-sm-10" id="tianjiaxianshi1">
                    <span><a href="#" id="AddMoreFileBox">添加</a> </span> <span class="hidden" id="yichu"><a href="#" class="removeclass">移除</a> </span>
                </div>

            </div>

           <!-- <div class="form-group">
                <label class="col-sm-2 control-label" >不运送地区</label>
                <div class="col-sm-10">
                   <select>
                       <option>运送至所有国家</option>
                       <option>使用ebay站点设置</option>
                       <option>选择不运送地区</option>
                   </select>
                </div>
            </div>-->
        </div>
    </div>

<!--    <div class="form-group" id="yidong1">
        <div class="col-sm-2"></div>
        <div class="col-sm-10" > <button class="btn btn-default btn-xs" name="tijiao1" value="kandeng" type="submit"  >提交</button>
        </div>
    </div>-->

    <div class="clearfix form-actions">
        <div class="col-md-offset-1 col-md-11">
            <input type="hidden" name="action" id="action" value=""/>
            <input type="hidden" name="id" value="<?php if(isset($listinfo['id']))
            {
                echo $listinfo['id'];
            }
            ?>" id="id"/>
                <button class="btn btn-success submit_btn" type="submit" name="save">
                    <i class="icon-ok bigger-110"></i>
                    保存
                </button>

            <button class="btn btn-success submit_btn" type="submit" name="verifyebay">
              检测刊登费用
            </button>

                <button class="btn btn-inverse submit_btn" type="submit" name="saveToPost" >

                    保存并发布
                </button>
              <button class="btn btn-inverse submit_btn" type="submit" name="moDescription">

                修改线上描述
            </button>

            <button class="btn btn-inverse submit_btn" type="submit" name="modifytrans">

                修改线上物流方式
            </button>

            <button class="btn btn-inverse submit_btn" type="submit" name="modifyskuinfo">

                修改线上SKU信息
            </button>

            <button class="btn btn-inverse submit_btn" type="submit" name="modifypictureurl">

                修改橱窗图片
            </button>

            <label >
                <a class="btn btn-inverse" href="<?php echo admin_base_url('ebay/ebaylist/ebaylistinfo');?>">
                  返回列表</a>
            </label>

            <button class="btn btn-reset" type="reset">
                <i class="icon-undo bigger-110"></i>重置
            </button>
        </div>
    </div>

    </form>




    <div class="hide" id="showDiv" style="overflow:scroll; width: 1000px; height: 500px;"></div>
    <script type="text/javascript" src="<?php echo static_url('theme/common/jquery.dragsort-0.5.1.min.js');?>"></script>
    <script type="text/javascript">

        KindEditor.ready(function(K) {
            var editor = K.create("#detail",{
                "allowFileManager" : true,
                "allowImageManager" : true,
                "width":"100%",
                "height":"400px",
                "filterModel":false,//是否过滤html代码,true过滤
                "resizeType":"2",//是否可以改变editor大小，0：不可以   1：可改高   2：无限
                "items" :  ['source', '|', 'fullscreen', 'undo', 'redo',
                    'cut', 'copy', 'paste', 'plainpaste',
                    'wordpaste', '|', 'justifyleft', 'justifycenter',
                    'justifyright', 'justifyfull', 'insertorderedlist', 'insertunorderedlist',
                    'indent', 'outdent', 'subscript', 'superscript', '|', 'selectall', '-', 'title',
                    'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                    'strikethrough', 'removeformat', '|','image', 'multiimage', 'advtable', 'hr',
                    'emoticons', 'link', 'unlink', 'table'],
                "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
                "afterBlur": function(){this.sync();} //必须，不然第一次提交不到
        });
            var editor2 = K.create("#detail1",{
                "width":"100%",
                "height":"200px",
                "filterModel":false,//是否过滤html代码,true过滤
                "resizeType":"2",//是否可以改变editor大小，0：不可以   1：可改高   2：无限
                "items" :  [],
                "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
                "afterBlur": function(){this.sync();} //必须，不然第一次提交不到
            });
            var editor3 = K.create("#templatehtml",{
                "width":"100%",
                "height":"400px",
                "filterModel":false,//是否过滤html代码,true过滤
                "resizeType":"2",//是否可以改变editor大小，0：不可以   1：可改高   2：无限
                "items" :  [],
                "htmlTags": false, //要过滤style中的样式的话，直接不用写这句
                "afterBlur": function(){this.sync();} //必须，不然第一次提交不到
            });
            $('#getskuinfo').click(function(){


                var list= $('input:radio[name="leixing"]:checked').val();
                editor.html('');

                var val = $('#inputsku').val();
                if(val=='')
                {
                    return false;
                }
                url = '<?php echo admin_base_url("ebay/ebay_product/getSkuhtmlmod");?>';
                $.ajax({
                    url: url,
                    data: 'sku='+val,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {

                        editor.appendHtml(data.data);

                    }
                });

                url = '<?php echo admin_base_url("ebay/ebay_product/ajaxUploadDirImage");?>';
                $.ajax({
                    url: url,
                    data: 'sku='+val,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        if(data.status=='1')
                        {
                            $("#test123").empty();
                            $('#templatepic').empty();
                            for(i=0;i<data.data.length;i++) {
                                /*if((i%6==0)&&(i!=0))
                                 {
                                 $("#test123").append('<br/>')
                                 }*/
                                $("#test123").append('<li><span id="tupian' + i + '"><img name="tupian' + i + '"  src=' + data.data[i] + '  width="100" height="100" style="border: 0px;"> <input type="hidden" name="imgLists[]" value=' + data.data[i] + ' ></input> <a href="javascript: void(0);" name="tupian' + i + '"  class="pic-del">删除</a></span></li>');

                                $("#templatepic").append('<li><span id="temptupian' + i + '"><img name="temptupian' + i + '"  src=' + data.data[i] + '  width="100" height="100" style="border: 0px;"> <input type="hidden" name="tempimgLists[]" value=' + data.data[i] + ' ></input> <a href="javascript: void(0);" name="temptupian' + i + '"  class="pic-del">删除</a></span></li>');
                            }

                        }
                        else
                        {
                            $("#test123").empty();
                            $("#test123").append('<span class="red">没有找到该sku的图片，请先将该SKU的图片传至图片服务器</span>');
                            $("#templatepic").empty();
                            $("#templatepic").append('<span class="red">没有找到该sku的图片，请先将该SKU的图片传至图片服务器</span>');

                        }
                    }
                })

            });

            $(document).on('click', '.pic-del', function () {

                var delname = this.name;
                $("#"+delname).empty();
            });

            $(document).on('click', '.picc-del', function () {

                var delname = this.name;
                $("#"+delname).remove();
            });


            //属性自定义图片上传



        $("#fenleizi1").change(function(){
            $("#tishi").addClass('hidden');
              $("#fenleizi2").empty('hidden');
             $("#fenleizi3").empty('hidden');
             $("#fenleizi4").empty('hidden');
             $("#fenleizi5").empty('hidden');
             $("#fenleizi6").empty('hidden');
            $("#fenleizi2").addClass('hidden');
            $("#fenleizi3").addClass('hidden');
            $("#fenleizi4").addClass('hidden');
            $("#fenleizi5").addClass('hidden');
            $("#fenleizi6").addClass('hidden');
            var check = $("#fenleizi1").val();
            var value = $("#fenleizi1").find("option:selected").text();
            var site =  $('#siteid').val();
            if(value.indexOf(">") <= 0 )
            {
                $("#tishi").removeClass('hidden');
                return;
            }
            url = '<?php echo admin_base_url("ebay/ebay_product/getCategory");?>';
            $.ajax({
                url: url,
                data: 'op2='+check+'&site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    var options = data.info;
                    $("#fenleizi2").empty().append(options);
                    $("#fenleizi2").removeClass('hidden');
                }
            });
        });

        $("#fenleizi2").change(function(){
            $("#tishi").addClass('hidden');
            $("#fenleizi3").addClass('hidden');
            $("#fenleizi4").addClass('hidden');
            $("#fenleizi5").addClass('hidden');
            $("#fenleizi6").addClass('hidden');
            var check = $("#fenleizi2").val();
            var value = $("#fenleizi2").find("option:selected").text();
            var site =  $('#siteid').val();
            if(value.indexOf(">") <= 0 )
            {
                $("#tishi").removeClass('hidden');
                return;
            }
            url = '<?php echo admin_base_url("ebay/ebay_product/getCategory");?>';
            $.ajax({
                url: url,
                data: 'op3='+check+'&site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    var options = data.info;

                    $("#fenleizi3").empty().append(options);
                    $("#fenleizi3").removeClass('hidden');
                }
            });
        });

        $("#fenleizi3").change(function(){
            $("#tishi").addClass('hidden');
            $("#fenleizi4").addClass('hidden');
            $("#fenleizi5").addClass('hidden');
            $("#fenleizi6").addClass('hidden');
            var check = $("#fenleizi3").val();
            var value = $("#fenleizi3").find("option:selected").text();
            var site =  $('#siteid').val();
            if(value.indexOf(">") <= 0 )
            {
                $("#tishi").removeClass('hidden');
                return;
            }
            url = '<?php echo admin_base_url("ebay/ebay_product/getCategory");?>';
            $.ajax({
                url: url,
                data: 'op4='+check+'&site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    var options = data.info;
                    $("#fenleizi4").empty().append(options);
                    $("#fenleizi4").removeClass('hidden');
                }
            });
        });

        $("#fenleizi4").change(function(){
            $("#tishi").addClass('hidden');
            $("#fenleizi5").addClass('hidden');
            $("#fenleizi6").addClass('hidden');
            var check = $("#fenleizi4").val();
            var value = $("#fenleizi4").find("option:selected").text();
            var site =  $('#siteid').val();
            if(value.indexOf(">") <= 0 )
            {
                $("#tishi").removeClass('hidden');
              return;
            }
            url = '<?php echo admin_base_url("ebay/ebay_product/getCategory");?>';
            $.ajax({
                url: url,
                data: 'op5='+check+'&site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    var options = data.info;
                    $("#fenleizi5").empty().append(options);
                    $("#fenleizi5").removeClass('hidden');
                }
            });
        });
        $("#fenleizi5").change(function(){
            $("#tishi").addClass('hidden');
            $("#fenleizi6").addClass('hidden');
            var check = $("#fenleizi5").val();
            var value = $("#fenleizi5").find("option:selected").text();
            var site =  $('#siteid').val();
            if(value.indexOf(">") <= 0 )
            {
                $("#tishi").removeClass('hidden');
                return;
            }

            url = '<?php echo admin_base_url("ebay/ebay_product/getCategory");?>';
            $.ajax({
                url: url,
                data: 'op6='+check+'&site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    var options = data.info;
                    $("#fenleizi6").empty().append(options);
                    $("#fenleizi6").removeClass('hidden');
                }
            });
        });
        $("#wpms2").change(function(){
            var diyi = $('#wpms2').prop('selectedIndex');
           if(diyi == 0)
           {
               $('#wpms3').addClass('hidden');
           }
            else
           {
               $('#wpms3').removeClass('hidden');
           }

        });


       // $('#fenleiqueding').click(function() {
            $('.fenlei').click(function(e) {
            var value = $("#fenleizi6").find("option:selected").text();
            var valueid = $("#fenleizi6").val();
            var valueall = $("#fenleizi1").find("option:selected").text()+$("#fenleizi2").find("option:selected").text()+$("#fenleizi3").find("option:selected").text()+$("#fenleizi4").find("option:selected").text()+$("#fenleizi5").find("option:selected").text()+$("#fenleizi6").find("option:selected").text();
            if(value =='')
            {
                var value = $("#fenleizi5").find("option:selected").text();
                var valueid = $("#fenleizi5").val();
                var valueall = $("#fenleizi1").find("option:selected").text()+$("#fenleizi2").find("option:selected").text()+$("#fenleizi3").find("option:selected").text()+$("#fenleizi4").find("option:selected").text()+$("#fenleizi5").find("option:selected").text();
            }
            if(value =='')
            {
                var value = $("#fenleizi4").find("option:selected").text();
                var valueid = $("#fenleizi4").val();
                var valueall = $("#fenleizi1").find("option:selected").text()+$("#fenleizi2").find("option:selected").text()+$("#fenleizi3").find("option:selected").text()+$("#fenleizi4").find("option:selected").text();
            }
            if(value =='')
            {
                var value = $("#fenleizi3").find("option:selected").text();
                var valueid = $("#fenleizi3").val();
                var valueall = $("#fenleizi1").find("option:selected").text()+$("#fenleizi2").find("option:selected").text()+$("#fenleizi3").find("option:selected").text();
            }
            if(value =='')
            {
                var value = $("#fenleizi2").find("option:selected").text();
                var valueid = $("#fenleizi2").val();
                var valueall = $("#fenleizi1").find("option:selected").text()+$("#fenleizi2").find("option:selected").text();
            }
            if(value =='')
            {
                var value = $("#fenleizi1").find("option:selected").text();
                var valueid = $("#fenleizi1").val();
                var valueall = $("#fenleizi1").find("option:selected").text();
            }
            if(value.indexOf(">") <= 0 )
            {

                var v_id = $(e.target).attr('id');
                if(v_id =='fenleiqueding1')
                {
                    $('#myModal').modal('toggle')
                    $("#diyifenlei").val(valueid);
                    $('#diyifenleimiaoshu').empty().val(valueall);
                    $('#diyifenleimiaoshu').removeClass('hidden');
                   var iii =  layer.load("同步分类信息中")
                    changesite();
                    addmul();
                     layer.close(iii);



                }
                if(v_id =='fenleiqueding2')
                {
                    $('#myModal').modal('toggle')
                    $("#dierfenlei").val(valueid);
                    $('#dierfenleimiaoshu').empty().val(valueall);
                    $('#dierfenleimiaoshu').removeClass('hidden');
                }

            }
            else
            {
                alert('请选择子类');
            }

        })

            $('#kandengmoban').change(function(){
                var mo = $("#kandengmoban").find("option:selected").val()
                if(mo=='')
                {
                    editor2.html('');
                }
                else
                {
                    url = '<?php echo admin_base_url("ebay/ebay_product/getTemplatedetails");?>';
                    $.ajax({
                        url: url,
                        data: 'id='+mo,
                        type: 'POST',
                        dataType: 'JSON',
                        success: function (data) {
                            if(data.status==1)
                            {
                                editor2.html(data.data);
                            }
                        }
                    });
                }
            //templatehtmlselect
            })

            $('#templatehtmlselect').change(function(){
                var mo = $("#templatehtmlselect").find("option:selected").val()
                if(mo=='')
                {
                    editor2.html('');
                }
                else
                {
                    url = '<?php echo admin_base_url("ebay/ebay_product/getTemplatedetailsHtml");?>';
                    $.ajax({
                        url: url,
                        data: 'id='+mo,
                        type: 'POST',
                        dataType: 'JSON',
                        success: function (data) {
                            if(data.status==1)
                            {
                                editor3.html(data.data);
                            }
                        }
                    });
                }
                //templatehtmlselect
            })


            $('#myModalSelect').on('shown.bs.modal', function (e) {

            })

        $('#myModal').on('shown.bs.modal', function (e) {
          var site =  $('#siteid').val();
            url = '<?php echo admin_base_url("ebay/ebay_product/getCategory");?>';
            $.ajax({
                url: url,
                data: 'site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                   var options = data.info;
                    $("#fenleizi1").empty().append(options);
                    $("#tishi").addClass('hidden');
                    $("#fenleizi2").empty('hidden');
                    $("#fenleizi3").empty('hidden');
                    $("#fenleizi4").empty('hidden');
                    $("#fenleizi5").empty('hidden');
                    $("#fenleizi6").empty('hidden');
                    $("#fenleizi2").addClass('hidden');
                    $("#fenleizi3").addClass('hidden');
                    $("#fenleizi4").addClass('hidden');
                    $("#fenleizi5").addClass('hidden');
                    $("#fenleizi6").addClass('hidden');

                }
            });
        })

            $("input:radio[name='yaoqiu']").change(function (){
                var dialCheckResult=$("input:radio[name='yaoqiu']:checked").val() ;
                if(dialCheckResult=='all')
                {
                    $('#maijiayaoqiudetail').addClass('hidden');

                }
                else
                {
                    $('#maijiayaoqiudetail').removeClass('hidden');
                }
            });

            $("#qibiao").change(function() {
                if($("#qibiao").is(":checked"))
                {
                    $("#qibiaonum").removeAttr("disabled");
                    $("#qibiaotianshu").removeAttr("disabled");
                }
                else {
                    $("#qibiaonum").attr("disabled", "disabled");
                    $("#qibiaotianshu").attr("disabled", "disabled");
                }
            });

            $("#jianjv").change(function() {
                if($("#jianjv").is(":checked"))
                {
                    $("#jianjvnum").removeAttr("disabled");
                    $("#jianjvtianshu").removeAttr("disabled");
                }
                else {
                    $("#jianjvnum").attr("disabled", "disabled");
                    $("#jianjvtianshu").attr("disabled", "disabled");
                }
            });

            $("#xinyong").change(function() {
                if($("#xinyong").is(":checked"))
                {
                    $("#xinyongnum").removeAttr("disabled");
                }
                else {
                    $("#xinyongnum").attr("disabled", "disabled");
                }
            });

            $("#goumai").change(function() {
                if($("#goumai").is(":checked"))
                {
                    $("#goumainum").removeAttr("disabled");
                    $("#maijiaxinyongnum").removeAttr("disabled");
                    $("#maijiaxinyong").removeAttr("disabled");
                }
                else {
                    $("#goumainum").attr("disabled", "disabled");
                    $("#maijiaxinyongnum").attr("disabled", "disabled");
                    $("#maijiaxinyong").attr("disabled", "disabled");
                }
            });
      /*      $('#ebayaccount').change(function(){
                autchangepaypal();
            })*/

            $("#diyifenlei").blur(function(){
               var  iii =  layer.load("同步分类信息中")
                    changesite();
                    addmul();
                layer.close(iii);
            })

            $(".pic-main, .pic-detail, .relate-list").dragsort({ dragSelector: "span",  placeHolderTemplate: "<li class='placeHolder'><div></div></li>" });

        });

        function changesite()
        {

            $('#not_mul').addClass('hidden');
            var valueid  = $("#diyifenlei").val();
            var list= $('input:radio[name="leixing"]:checked').val();
            $('#wpsxtable').empty();
            var site = $('#siteid').val();
            var is_mul ='';
            url = '<?php echo admin_base_url("ebay/ebay_product/getCondition");?>';
            $.ajax({
                url: url,
                data: 'categoryid='+valueid+'&site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if (data.status == 3) {
                  //  alert('这个分类属性获取失败');
                    }
                    else {
                        var options = data.data;

                        $("#wpms2").empty().append(options['options']);

                        if(options['upcenabled']==1)
                        {
                            $('#upcdiv').removeClass('hidden')
                        }
                        if(options['eanenabled']==1)
                        {
                            $('#eandiv').removeClass('hidden')
                        }

                        if(options['isbnenabled']==1)
                        {
                            $('#isbdiv').removeClass('hidden')
                        }
                        if (data.status == 2) {
                            if(list=='duoshuxing')
                            {
                                $('#not_mul').removeClass('hidden');
                                $('#duoshuxingtable').addClass('hidden');
                                $('#tupianshezhi').addClass('hidden');
                            }

                        }
                    }
                }

            });

            url = '<?php echo admin_base_url("ebay/ebay_product/getSpecifics");?>';
            $.ajax({
                url: url,
                data: 'categoryid='+valueid+'&site='+site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(data.status==2)
                    {

                    }
                    else
                    {
                        var options = data.info;
                        $('#wpsxtable').empty().append(options);
                    }
                }
            });

        }
        function addmul()
        {

            var list= $('input:radio[name="leixing"]:checked').val();
            if(list=='duoshuxing')
            {
                if(!$("#not_mul").is(":hidden"))
                {
                    return false;
                }
                $('#duoshuxingtable').removeClass('hidden');
                $('#tupianshezhi').removeClass('hidden');
                var val = $('#inputsku').val();
                url = '<?php echo admin_base_url("ebay/ebay_product/getSkuLike");?>';
                $.ajax({
                    url: url,
                    data: 'sku='+val,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        var table1 = $('#table1');
                        $('#multupianinfo').empty();
                        $('#tupianshezhiselect').empty();
                        $('#table1 tr:not(:first)').remove();
                        $('#table1 tr:eq(0) td:nth-child(7)').remove();
                        $('#table1 tr:eq(0) td:nth-child(6)').remove();
                        $('#table1 tr:eq(0) td:nth-child(5)').remove();


                        for(var i=0;i<data.info.length;i++)
                        {
                            var row = $("<tr></tr>");
                            row.append($('<td><input type="checkbox" name="count"/></td><td><input  size="15"   name="skuinfo[sku][]" value="'+data.info[i]["products_sku"]+'" type="text" /></td> <td><input  size="5" name="skuinfo[qc][]" type="text"/> </td><td><input  size="5" name="skuinfo[price][]" type="text"/></td>'));
                        //    row.append($('<td><input type="checkbox" name="count"/></td><td><input  size="15"   name="skuinfo['+i+'][sku]" value="'+data.info[i]['products_sku']+'" type="text" /></td> <td><input  size="5" name="skuinfo['+i+'][qc]" type="text"/> </td><td><input  size="5" name="skuinfo['+i+'][price]" type="text"/></td>'));
                            row.append(row);
                            table1.append(row);
                        }

                        var siteid =$("#siteid").val();
                        if((siteid==0)||(siteid==2)||(siteid==15)){
                            addSpe('UPC');
                        }

                        if((siteid==3)||(siteid==71)||(siteid==77)||(siteid==101)||(siteid==186)){
                            addSpe('EAN');
                        }


                    }
                });
            }
        }

        function getcategory()
        {
            var site = $('#siteid').val();
            var categoryid = $('#diyifenlei').val();


            url = '<?php echo admin_base_url("ebay/ebay_product/getCategoryIsSet");?>';
            $.ajax({
                url: url,
                data: 'categoryid=' + categoryid + '&site=' + site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(data.status==1)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }

                }
            })

        }

        function wpsxxz(x)
        {
            var node =document.getElementById(x);
            var index = node.selectedIndex;
            var text = node.options[index].text;
            var value = node.options[index].value;
            if(text=='--请选择--')
            {
                return false;
            }
            //var id = value+'1';
           // $("#"+id).val(text);
            $('#'+x).parent().children().eq(0).val(text);
        }

        function wpsxxz2(x)
        {
            var node =document.getElementById(x);
            var index = node.selectedIndex;
            var text = node.options[index].text;
            if(text=='--请选择--')
            {
                return false;
            }
            var value = node.options[index].value;
          $('#'+x).parent().children().eq(0).val(text);
         //   var cc =  $('#'+x).parent().children().eq(1).val();
         //   var id = 'se'+value;
          //  $("#"+id).val(text);
        }

        var row_count = 3;
        function addNew()
        {
            var table1 = $('#table1');
            //var firstTr = table1.find('tbody>tr:first');
            var row = $("<tr></tr>");
            row.append($("<td><input type='checkbox' name='count' value='New'/></td><td><input  style='width:40px;' type='text' /></td><td><input  type='text' /></td><td><input  style='width:60px;' type='text' /></td>"));
            row.append(row);
            table1.append(row);
            row_count++;
        }
        function addSpe(auto){



            if(auto==''){
                var name=prompt("输入你要添加的属性","")
                if (name!=null && name!="") {

                    if((name=='UPC')||(name=='EAN')){
                        nv=name;
                    }else{
                        var str = name;
                        var nv = str.toLowerCase().replace(/\b(\w)/g, function ($0, $1) {
                            return $1.toUpperCase();
                        });
                    }

                }
                else
                {
                    return false;
                }
            }else{
                var nv = auto;
            }
            var i=0
            var j=0;
            $('#table1').find('tr').each(function(i) {
                var tr = $(this);
                if(i==0)
                {
                    tr.append('<td  onclick="deleteinfoNew(this)" >'+nv+'<input size="15"  class="hidden " type="text" name="zidingyi[]" value="'+nv+'"/></td>');
                }
                else
                {
                    if(auto==''){
                        tr.append('<td ><input type="text" size="15"    name="skuinfo['+nv+'][]"  /></td>');
                    }else{
                        tr.append('<td ><input type="text" size="15"  value="Does not apply"   name="skuinfo['+nv+'][]"  /></td>');
                    }

                    j++
                }
                i++;

            })
            if(auto==''){
                $('#tupianshezhiselect').append('<option value='+'"'+nv+'"'+'>'+nv+'</option>');

            }

        }
        function addSpe1()
        {
            var name=prompt("输入你要添加的属性","")
            if (name!=null && name!="") {
                var str = name;
                var nv = str.toLowerCase().replace(/\b(\w)/g, function ($0, $1) {
                    return $1.toUpperCase();
                });
            }
            else
            {
                return false;
            }


                var id = 'addduoshuxing1';
                var inputid = 'mulspe';
                if ($('#addduoshuxing1').length > 0) {
                    var id = 'addduoshuxing2';
                    var inputid = 'mulspr';

                }
                if ($('#addduoshuxing2').length > 0) {
                    var id = 'addduoshuxing3';

                    var inputid = 'mulspt';
                }
                if ($('#addduoshuxing3').length > 0) {
                    return false;
                }
            var is_set =false;
            var tt='';
                $('#wpsxtable').find('tr').each(function (i) {
                    if (nv == $(this).children().eq(0).text()) {
                        is_set=true;
                        var j = 0;

                        var slecetid = $(this).children().eq(1).children().eq(1).attr('id');


                        $('#' + slecetid + ' option').each(function (i) {
                            if (j == 0) {
                                tt = tt + '<option>' + $(this).text() + '</option>'
                            }
                            else {
                                tt = tt + '<option value="' + $(this).text() + '">' + $(this).text() + '</option>'
                            }
                            j++;
                        })
                    }
                })
            if(is_set)
            {

                var i = 0;
                var j = 0;
                $('#table1').find('tr').each(function (i) {
                    var tr = $(this);
                    if (i == 0) {
                        tr.append('<td  id =' + id + ' onclick="deleteinfo(this.id)">' + nv + '<input type="text" name="zidingyi[]" value="' + nv + '" class="hidden"/></td>');
                    }
                    else {
                        //  tr.append('<td ><input id="'+nv+2+'" type="text" name="duoshuxing['+nv+']" /> <select id="'+nv+2+'"  onchange='+wpsxxz2(this.id)+' >'+tt+'</select></td>');
                        tr.append('<td ><input type="text" name="skuinfo[' + j + '][' + nv + ']"  size="10"   /><select   id="' + inputid + [j] + '" onchange="wpsxxz2(this.id)"  >' + tt + '</select></td>');
                        j++
                    }
                    i++;

                })
            }
            else
            {
                var i=0
                var j=0;
                $('#table1').find('tr').each(function(i) {
                    var tr = $(this);
                    if(i==0)
                    {
                        tr.append('<td  id ='+id+' onclick="deleteinfo(this.id)">'+nv+'<input type="text" name="zidingyi[]" value="'+nv+'" class="hidden"/></td>');
                    }
                    else
                    {
                        tr.append('<td ><input type="text"  name="skuinfo['+j+']['+nv+']" size="15"  id="' + inputid + [j] + '"  /></td>');
                        j++
                    }
                    i++;

                })
            }
                var t =$('#'+id).text();
                $('#tupianshezhiselect').append('<option value='+'"'+t+'"'+'>'+t+'</option>');
        }

        function setper()
        {
            var name=prompt("批量设置SKU前缀","")
            if (name!=null && name!="") {
                var str = name;
                var i=0;
                $('#table1').find('tr').each(function(i) {
                    if(i!=0)
                    {
                        var va = $(this).children().eq(1).children().eq(0).val();
                        $(this).children().eq(1).children().eq(0).val(str+va);
                    }
                    i++;
                });
            }
        }
        function deleteinfoNew(e){
            if(confirm("是否要删除该属性")) {
                var text = $(e).text();
                var rr = $(e).prevAll().length + 1;
                var tt = $(e).parent().prevAll().length + 1;
                $('#table1 tr th:eq(' + tt + ')').remove();
                $('#table1 tr td:nth-child(' + rr + ')').remove();
                $("#tupianshezhiselect option[value='"+text+"']").remove();
            }
        }
        function del()
        {

            var checked = $("input[type='checkbox'][name='count']");
            $(checked).each(function(){
                if($(this).is(":checked")) //注意：此处判断不能用$(this).attr("checked")==‘true'来判断。
                {
                    $(this).parent().parent().remove();
                }
            });
        }

        function tupianshezhiNew(){
            $('#multupianinfo').empty();
            var val = $('#tupianshezhiselect').val();
            if(val==''){
                return false;
            }
            var i=1
            var j=0;
            var notdistinct = new Array;
            $('#table1 tr:first').find('td').each(function(){
                if(val==$(this).text()){

                    $("#table1 tr td:nth-child("+i+")").each(function(){
                        var text =$(this).children().eq(0).val();
                        if((val !=text)&&(text !="")){
                            if(notdistinct.indexOf(text) > -1)
                            {
                                return true;
                            }
                            notdistinct[j] = text;
                            j++;
                        }
                    });
                }
                i++;
            })

            for(var i=0;i<notdistinct.length;i++){
               // $('#multupianinfo').append('<div class="vargallery"><p>'+val+'</p><p>'+value+'</p><p><a   id="'+info+'"   onclick="addpic(this.id)">使用外部图片</a></p></div>');
                $('#multupianinfo').append('<div  class="form-group center" ><label class="col-sm-3 control-label center"><span>'+val+'</span><br/><span>'+notdistinct[i]+'</span><br/><a class="btn btn-success" data-id="'+val+"-"+notdistinct[i]+'" onclick="addpicNew(this)">使用外部图片</a></label> <div class="col-sm-4"></div></div>');
            }
        }
        function tupianshezhi()
        {
            var val = $('#tupianshezhiselect').val();
            var num ='';
            if(val == $('#addduoshuxing1').text())
            {
                num = $('#addduoshuxing1').prevAll().length;
            }
            if(val == $('#addduoshuxing2').text())
            {
                num = $('#addduoshuxing2').prevAll().length;
            }
            if(val == $('#addduoshuxing3').text())
            {
                num = $('#addduoshuxing3').prevAll().length;
            }
            $('#multupianinfo').empty();

            var i=0;
            var j=0;
            var notdistinct = new Array;
            $('#table1').find('tr').each(function(i) {
                if(i!=0)
                {

                    var value= $(this).children().eq(num).children().eq(0).val();
                    if(!(value==''))
                    {
                        //alert(notdistinct.indexOf(value));
                        if(notdistinct.indexOf(value) > -1)
                        {

                            return true;
                        }
                        notdistinct[i] = value;
                       // var mul = $('#'+mulval+i ).val();
                        var pic = "divVarPic"+j;
                        var info = 'info'+j
                        $('#multupianinfo').append('<div id='+pic+'  class="vargallery"><p>'+val+'</p><p>'+value+'</p><p><a   id="'+info+'"   onclick="addpic(this.id)">使用外部图片</a></p></div>');
                        j++;
                    }
                }
                i++;
            })
        }
        function copytptempic()
        {
            //
            $('#templatepic').empty();
            var i=0;
            $("input[name='imgLists[]']").each(function(){
               var name = $(this).val();

                $('#templatepic').append('<li><span id="temptupian'+i+'"><img width="100" height="100" style="border: 0px;" src='+name+' name="temptupian'+i+'"> <input type="hidden" value='+name+' name="tempimgLists[]"> <a class="pic-del" name="temptupian'+i+'"  href="javascript: void(0);">删除</a></span></li>');
                i++;
            })
        }


        function  addnewpicforproduct(e)
        {
            var sku = $("#inputsku").val();
            url = '<?php echo admin_base_url("ebay/ebay_product/ajaxUploadDirImageNew");?>';
            $.ajax({
                url: url,
                data: 'sku='+sku,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(data.status=='1')
                    {
                            for( var i=0;i<data.data.length;i++) {
                                $("#test123").append('<li><span id="tupian' + i + '"><img name="tupian' + i + '"  src=' + data.data[i] + '  width="100" height="100" style="border: 0px;"> <input type="hidden" name="imgLists[]" value=' + data.data[i] + ' ></input> <a href="javascript: void(0);" name="tupian' + i + '"  class="pic-del">删除</a></span></li>');
                            }
                    }
                }
            })
        }
        function addsppicforproduct(e)
        {
            var sku = $("#inputsku").val();
            var test = $('#' + e).parent().children().eq(4).children().last().children().eq(0).attr("id");
          var skudata='sku='+sku+'&opt=1';
          url = '<?php echo admin_base_url("ebay/ebay_product/ajaxUploadDirImage");?>';
         $.ajax({
                url: url,
            data: skudata,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                if(data.status=='1')
                {


                        if($('#tupian0').length>0)
                        {
                            for(i=0;i<data.data.length;i++) {
                                var middom = test.split("n");
                                var last = parseInt(middom[1]) + i + 1;
                                var info = 'tupian' + last
                                $('#test123').append('<li><span id=' + info + '><img width="100" height="100" style="border: 0px;" src=' + data.data[i] + ' name=' + info + '> <input type="hidden" value=' + data.data[i] + ' name="imgLists[]"> <a class="pic-del" name=' + info + ' href="javascript: void(0);">删除</a></span></li>');
                            }
                        }
                        else
                        {
                            for(i=0;i<data.data.length;i++) {
                                $("#test123").append('<li><span id="tupian' + i + '"><img name="tupian' + i + '"  src=' + data.data[i] + '  width="100" height="100" style="border: 0px;"> <input type="hidden" name="imgLists[]" value=' + data.data[i] + ' ></input> <a href="javascript: void(0);" name="tupian' + i + '"  class="pic-del">删除</a></span></li>');
                            }
                        }
                    }
                }
            })
        }



        function addpicforproduct(e)
        {
            var name=prompt("输入图片的外链地址","")
            if (name!=null && name!="") {
                if($('#tupian0').length>0)
                {
                    var test = $('#' + e).parent().children().eq(4).children().last().children().eq(0).attr("id");
                    var middom = test.split("n");
                    var last = parseInt(middom[1]) + 1;
                    var info = 'tupian' + last
                    $('#test123').append('<li><span id='+info+'><img width="100" height="100" style="border: 0px;" src='+name+' name='+info+'> <input type="hidden" value='+name+' name="imgLists[]"> <a class="pic-del" name='+info+' href="javascript: void(0);">删除</a></span></li>');
                }
                else
                {
                    $('#test123').append('<li><span id="tupian0"><img width="100" height="100" style="border: 0px;" src='+name+' name="tupian0"> <input type="hidden" value='+name+' name="imgLists[]"> <a class="pic-del" name="tupian0" href="javascript: void(0);">删除</a></span></li>');
                }
            }
        }

        function addpicforproducttemp(e)
        {
            var name=prompt("输入图片的外链地址","")
            if (name!=null && name!="") {
                if($('#temptupian0').length>0)
                {
                    var test = $('#' + e).parent().children().eq(1).children().last().children().eq(0).attr("id");
                    var middom = test.split("n");
                    var last = parseInt(middom[1]) + 1;
                    var info = 'temptupian' + last
                    $('#templatepic').append('<li><span id='+info+'><img width="100" height="100" style="border: 0px;" src='+name+' name='+info+'> <input type="hidden" value='+name+' name="tempimgLists[]"> <a class="pic-del" name='+info+' href="javascript: void(0);">删除</a></span></li>');
                }
                else

                {
                    $('#templatepic').append('<li><span id="temptupian0"><img width="100" height="100" style="border: 0px;" src='+name+' name="temptupian0"> <input type="hidden" value='+name+' name="tempimgLists[]"> <a class="pic-del" name="temptupian0"  href="javascript: void(0);">删除</a></span></li>');
                }

            }
        }
        function addpicNew(test){
            var e = test;
            var mid_value = $(e).attr("data-id");
            var last_value = mid_value.split("-");
            var first0 =last_value[0];
            var first1 =last_value[1];
            var name=prompt("输入图片的外链地址","")
            if (name!=null && name!="") {
                $(e).parent().next().append('<dl class="center" ><dt><img  width="100" height="100" style="border: 0px;"  src="'+name+'"/><input class="hidden" name="mulpic['+first0+']['+first1+'][]"  value="'+name+'" /></dt><dd ><a href="javascript: void(0);" onclick="deletedetail(this)">删除</a></dd></dl>');
             //   $(e).parent().next().append('<span>132123213</span>');
            }
        }
        function deletedetail(e){
            if(confirm("确认删除？")){
                $(e).parent().parent().remove();
            }

        }
        function addpic(test)
        {
            var nameval = $('#'+test).parent().parent().children().eq(0).text();
            var val = $('#'+test).parent().parent().children().eq(1).text();
            var i=0;
            for(i=0;i<12;i++)
            {
                if($('#tupianinfo'+i).length>0)
                {

                }
                else
                {
                    var ee = 'tupianinfo'+i;
                    break;
                }


            }

            var name=prompt("输入图片的外链地址","")
            if (name!=null && name!="") {
                $('#'+test).parent().parent().append('<span id="'+ee+'"><img  name="mulpic['+nameval+']['+val+']"    src="'+name+'"  width="100" height="100" style="border: 0px;" /> <input type="hidden" name="mulpic['+nameval+']['+val+'][]"  value="'+name+'" /> <a href="javascript: void(0);"  name="'+ee+'"  class="picc-del">删除</a></span>');
                //<span id='mulspan'te><img name=""  src=""  width="100" height="100" style="border: 0px;" /> <input type="hidden" name="imgLists[]" value=""/> <a href="javascript: void(0);" name=""  class="pic-del">删除</a></span>")
            }
        }
        function addnum(id)
        {
            var name=prompt("批量设置数量","")
            if (name!=null && name!="") {
                var str = name;
                var i=0;
                $('#table1').find('tr').each(function(i) {
                    if(i!=0)
                    {
                        $(this).children().eq(2).children().eq(0).val(name);
                    }
                    i++;
                });
            }

        }
        function addprice(id)
        {
            var name=prompt("批量设置价格","")
            if (name!=null && name!="") {
                var str = name;
                var i=0;
                $('#table1').find('tr').each(function(i) {
                    if(i!=0)
                    {
                        $(this).children().eq(3).children().eq(0).val(name);
                    }
                    i++;
                });
            }

        }


        function autchangepaypal()
        {
            var account  = $('#ebayaccount').val();
            var checkpaypal = $('#paypalaccount').val();
            //alert(checkpaypal);return false;
            url = '<?php echo admin_base_url("ebay/ebay_product/getPaypalByAccount");?>';
            $.ajax({
                url: url,
                data: 'account=' + account,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(data.status==1)
                    {
                        $('#paypalaccount').empty().append(data.data);
                        $('#paypalaccount').val(checkpaypal);
                    }
                }
            })
        }

        function autopaypal(){

            var selectedvalue = $("input[name='leixing']:checked").val();
            var account = $("#ebayaccount").val();
            var site  = $("#siteid").val();
            var pirce = 0;
            if(selectedvalue=='duoshuxing'){
               pirce =  $("input[name='skuinfo[price][]']").val();
            }else{
                pirce = $("#paimaiprice").val();
            }
            if(pirce=='')
            {
                alert('请输入价格');
                return false;
            }
            url = '<?php echo admin_base_url("ebay/ebay_product/autopaypal");?>';
            $.ajax({
                url: url,
                data: 'account=' + account+'&site='+site+'&price='+pirce,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(data.status==1)
                    {
                        $('#paypalaccount').empty().append(data.data);
                    }
                }
            })
        }



    </script>



<script type="text/javascript" language="javascript">

    $(document).on('blur', ':text, #biaoti', function () {
        $(this).val($(this).val().trim());
    });
    $(document).on('keyup', '#biaoti', function () {
        var num = 80;
        //num = $(this).attr('maxlength');
      var  now_length = $(this).val();
        $(this).closest('div').find('.help-block').html('还能够输入<i class="red">' + (num - now_length.length) + '</i>个字符');
    });


    $(document).ready(function() {

        var selectedvalue = $("input[name='leixing']:checked").val();
        if (selectedvalue == 'paimai') {
            $("#baoliujia").removeClass('hidden');
            $("#yikoujia").removeClass('hidden');
            $("#paimaijiage").removeClass('hidden');
            $('#paimaitianshu').empty().append('<option value="Days_1">1</option><option value="Days_3">3</option><option value="Days_5">5</option><option value="Days_7">7</option><option value="Days_10">10</option>');
            if ($('#published_day').length > 0) {
                var acc = $('#published_day').val();
                $("#paimaitianshu option[value=" + acc + "]").attr("selected", "true");

            }
        }
        if (selectedvalue == 'guding') {
            $("#baoliujia").addClass('hidden');
            $("#yikoujia").addClass('hidden');
            $("#paimaijiage").removeClass('hidden');
            $('#paimaitianshu').empty().append('<option value="Days_3">3</option><option value="Days_5">5</option><option value="Days_7">7</option><option value="Days_10">10</option><option value="Days_30">30</option><option value="GTC">GTC</option>');
            if ($('#published_day').length > 0) {
                var acc = $('#published_day').val();
                $("#paimaitianshu option[value=" + acc + "]").attr("selected", "true");

            }
        }
        if (selectedvalue == 'duoshuxing') {
            $("#baoliujia").addClass('hidden');
            $("#yikoujia").addClass('hidden');
            $("#paimaijiage").addClass('hidden');
            $('#paimaitianshu').empty().append('<option value="Days_3">3</option><option value="Days_5">5</option><option value="Days_7">7</option><option value="Days_10">10</option><option value="Days_30">30</option><option value="GTC">GTC</option>');
            if ($('#published_day').length > 0) {
                var acc = $('#published_day').val();
                $("#paimaitianshu option[value=" + acc + "]").attr("selected", "true");

            }
        }


        //   $('#yunshu1').remove();
        //   $('#yunshu2').remove();
        //   $('#yunshu3').remove();
        //  $('#yunshu4').remove();
        //  $('#yunshu5').remove();
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
        });

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
                if ($('#inter_trans_type').length > 0) {
                    var acc = $('#inter_trans_type').val();
                    $("#guoneiyunshu1 option[value=" + acc + "]").attr("selected", "true");

                }
            }
        });

        var MaxInputs = 5; //maximum input boxes allowed
        var InputsWrapper = $("#InputsWrapper"); //Input boxes wrapper ID
        var AddButton = $("#AddMoreFileBox"); //Add button ID


        var x = InputsWrapper.length; //initlal text box count
        var FieldCount = 1; //to keep track of text box added

        $(AddButton).click(function (e)  //on add input button click
        {
            if ($("#yunshu1").length > 0) {
                x = 2;

            }
            else {
                x = 1;


            }

            if ($("#yunshu2").length > 0) {
                x = 3;


            }
            if ($("#yunshu3").length > 0) {
                x = 4;


            }
            if ($("#yunshu4").length > 0) {
                x = 5;


            }
            if ($("#yunshu5").length > 0) {
                x = 6;

            }


            if (x <= MaxInputs) //max input box allowed
            {
                if (x == 1) {
                    var tran = '第一运输';
                }
                if (x == 2) {
                    var tran = '第二运输';
                }
                if (x == 3) {
                    var tran = '第三运输';
                }
                if (x == 4) {
                    var tran = '第四运输';
                }
                if (x == 5) {
                    var tran = '第五运输';
                }


                FieldCount++; //text box added increment             <div class="form-group"><label class="col-sm-2 control-label"   >折扣</label><div class="col-sm-6"><select><option>--选择--</option></select><a href="">添加</a></div></div>
                //add input box
                $(InputsWrapper).append('<div  id="yunshu' + x + '"><div class="form-group"><label class="col-sm-2 control-label"   ></label><div class="col-sm-6"><span>' + tran + '</span></div></div>' +
                '<div class="form-group"><label class="col-sm-2 control-label"   >运输方式</label><div class="col-sm-6"><select name="yunshufangshi' + x + '" id="yunshufangshi' + x + '"></select></div></div>' +
                '<div class="form-group"><label class="col-sm-2 control-label"   >运费</label><div class="col-sm-6"><input type="text" name="yunfei' + x + '" id="yunfei' + x + '" placeholder="0.00"/><input type="checkbox" name="mianfei' + x + '" value="true" checked="checked" id="mianfei' + x + '"/> <span>免费</span> </div></div>' +
                '<div class="form-group"><label class="col-sm-2 control-label"   >额外每件加收</label><div class="col-sm-6"><input type="text" name="ewai' + x + '" id="ewai' + x + '" placeholder="0.00"/></div></div>' +
                '<div class="form-group"><label class="col-sm-2 control-label"   >运到</label><div class="col-sm-6"><input type="checkbox"  name="Worldwide' + x + '" id="quanqiu' + x + '"/><span>全球   </span><a href="#" id="guanjia' + x + '"  class="fc">选择以下所有国家和地区</a>' +
                '<br/><input type="checkbox" value="CN" name="guanjia' + x + '[]"/>中国</input>' +
                '<input type="checkbox" value="RU" name="guanjia' + x + '[]"/>俄罗斯联邦</input>' +
                '<input type="checkbox" value="CA" name="guanjia' + x + '[]"/>加拿大</input>' +
                '<input type="checkbox" value="BR" name="guanjia' + x + '[]"/>巴西</input>' +
                '<input type="checkbox" value="DE" name="guanjia' + x + '[]"/>德国</input>' +
                '<input type="checkbox" value="FR" name="guanjia' + x + '[]"/>法国</input>' +
                '<input type="checkbox" value="Europe" name="guanjia' + x + '[]"/>欧洲</input>' +
                '<input type="checkbox" value="GB" name="guanjia' + x + '[]"/>联合王国</input>' +
                '<input type="checkbox" value="EuropeanUnion" name="guanjia' + x + '[]"/>欧盟</input>' +
                '<input type="checkbox" value="Americas" name="guanjia' + x + '[]"/>美洲</input>' +
                '<input type="checkbox" value="US" name="guanjia' + x + '[]"/>美国</input>' +
                '<input type="checkbox" value="Asia" name="guanjia' + x + '[]"/>亚洲</input>' +
                '<input type="checkbox" value="AU" name="guanjia' + x + '[]"/>澳大利亚</input>' +
                '<input type="checkbox" value="MX" name="guanjia' + x + '[]"/>墨西哥</input>' +
                '<input type="checkbox" value="JP" name="guanjia' + x + '[]"/>日本</input></div></div></div>');


                url = '<?php echo admin_base_url("ebay/ebay_product/getDetails");?>';
                var site = $('#siteid').val();
                $.ajax({
                    url: url,
                    data: 'internationalservice=1&siteid=' + site,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (data) {
                        var options = data.info;
                        x--;
                        $("#yunshufangshi" + x).empty().append(options);
                        x++;
                    }
                });
                x++; //text box increment

            }
            $("#yichu").removeClass('hidden');



            return false;
        });



        $("body").on("click", ".fc", function (e) {

            var v_id = $(e.target).attr('id');
          //  $('input[name="' + v_id + '[]"]').attr("checked", true);
            $('input[name="' + v_id + '[]"]').each(function(){
                if($(this).is(':checked')) {
                    $(this).removeProp('checked');
                    $(this).prop('checked',false);
                }
                else
                {
                    $(this).prop("checked",true);//全选
                }
            })
            return false;
        })

        $("body").on("click", ".removeclass", function (e) {
            if (x > 1) {
                x--;
                var tt = 'yunshu' + x;
                $("#" + tt).remove();
                if (x == 1) {
                    $("#yichu").addClass('hidden');
                }
            }
            return false;
        })

        $(".radioItem").change(function () {
            var selectedvalue = $("input[name='leixing']:checked").val();
            if (selectedvalue == 'paimai') {
                $('#duoshuxingtable').addClass('hidden');
                $('#tupianshezhi').addClass('hidden');
                $('#not_mul').addClass('hidden');
                $('#multupianinfo').empty();
                $('#tupianshezhiselect').empty();
                $('#table1 tr:not(:first)').remove();
                $('#table1 tr:eq(0) td:nth-child(7)').remove();
                $('#table1 tr:eq(0) td:nth-child(6)').remove();
                $('#table1 tr:eq(0) td:nth-child(5)').remove();


                $("#baoliujia").removeClass('hidden');
                $("#yikoujia").removeClass('hidden');
                $("#paimaijiage").removeClass('hidden');
                $('#paimaitianshu').empty().append('<option value="Days_1">1</option><option value="Days_3">3</option><option value="Days_5">5</option><option value="Days_7">7</option><option value="Days_10">10</option>');
            }
            if (selectedvalue == 'guding') {

                $('#duoshuxingtable').addClass('hidden');
                $('#tupianshezhi').addClass('hidden');
                $('#not_mul').addClass('hidden');
                $('#multupianinfo').empty();
                $('#tupianshezhiselect').empty();
                $('#table1 tr:not(:first)').remove();
                $('#table1 tr:eq(0) td:nth-child(7)').remove();
                $('#table1 tr:eq(0) td:nth-child(6)').remove();
                $('#table1 tr:eq(0) td:nth-child(5)').remove();


                $("#baoliujia").addClass('hidden');
                $("#yikoujia").addClass('hidden');
                $("#paimaijiage").removeClass('hidden');
                $('#paimaitianshu').empty().append('<option value="Days_3">3</option><option value="Days_5">5</option><option value="Days_7">7</option><option value="Days_10">10</option><option value="Days_30">30</option><option value="GTC">GTC</option>');
            }
            if (selectedvalue == 'duoshuxing') {
                $("#baoliujia").addClass('hidden');
                $("#yikoujia").addClass('hidden');
                $("#paimaijiage").addClass('hidden');
                $('#paimaitianshu').empty().append('<option value="Days_3">3</option><option value="Days_5">5</option><option value="Days_7">7</option><option value="Days_10">10</option><option value="Days_30">30</option><option value="GTC">GTC</option>');
            }
        })

        $('#siteid').change(function () {
            x = 1;
            $('#yunshu1').remove();
            $('#yunshu2').remove();
            $('#yunshu3').remove();
            $('#yunshu4').remove();
            $('#yunshu5').remove();
            var site = $('#siteid').val();
            if ($('#diyifenlei').val() != '') {
                if ((site == 0) || (site == 2) || (site == 3) || (site == 15)) {
                }
                else {
                    /* */
                    var resu = getcategory();
                    if (resu) {
                        changesite();
                    }
                    else {

                        $('#wpsxtable').empty();
                        $('#wpms2').empty();
                        $('#duoshuxingtable').addClass('hidden');
                        $('#tupianshezhi').addClass('hidden');
                        $('#not_mul').addClass('hidden');
                        $('#multupianinfo').empty();
                        $('#tupianshezhiselect').empty();
                        $('#table1 tr:not(:first)').remove();
                        $('#table1 tr:eq(0) td:nth-child(7)').remove();
                        $('#table1 tr:eq(0) td:nth-child(6)').remove();
                        $('#table1 tr:eq(0) td:nth-child(5)').remove();
                        alert('该站点不存在该分类，请重新选择分类');
                    }


                }

            }


            url = '<?php echo admin_base_url("ebay/ebay_product/getCurrencyinfo");?>';
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

                    }
                    if (data.data['returnsaccepted'] != '') {
                        var nn = data.data['returnsaccepted'];
                        $('#tuihuofangshiall').removeClass('hidden');
                        $('#tuihuozhengce').empty();
                        for (i = 0; i < nn.length - 1; i++) {
                            $('#tuihuozhengce').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                        }
                    }
                    if (data.data['shippingcostpaidby'] != '') {
                        var nn = data.data['shippingcostpaidby'];
                        $('#tuihuofangshiall').removeClass('hidden');
                        $('#tuihuochengdang').empty();
                        for (i = 0; i < nn.length - 1; i++) {
                            $('#tuihuochengdang').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
                        }
                    }
                    if (data.data['refund'] != '') {
                        var nn = data.data['refund'];
                        $('#tuihuofangshiall').removeClass('hidden');
                        $('#tuihuofangshi').empty();
                        for (i = 0; i < nn.length - 1; i++) {
                            $('#tuihuofangshi').append('<option value="' + nn[nn.length - 2 - i] + '">' + nn[nn.length - 2 - i] + '</option>')
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
            });
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
            });
            if ($('#diyifenlei').val() != '') {
                var fenlei = $('#diyifenlei').val();
            }
        })
        $('#mul_check').click(function () {
            $('input[name="count"]').attr("checked", this.checked);
            var $subBox = $("input[name='count']");
            $subBox.click(function () {
                $("#mul_check").attr("checked", $subBox.length == $("input[name='count']:checked").length ? true : false);
            });
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
                        if(confirm('确定提交吗？'))
                        {
                            ii = layer.load('提交中');
                        }
                        else
                        {
                            return false;
                        }

                    },
                //    beforeCheck:confirm('进行这项操作码'),
                    callback: function (data) { //返回数据
                        layer.close(ii);
                    if (data.status) {
                        if (data.status == 1) {
                            if (data.data) {
                                var id = data.data
                                if (id) {
                                    $('#id').val(id);
                                }
                            }
                            showxbtips(data.info);
                        }
                        if (data.status == 2) {
                           alert(data.info);

                        }
                        if (data.status == 3) {
                            showxbtips(data.info);
                        }
                        if(data.status == 4)
                        {
                            var id = data.data
                            if (id) {
                                $('#id').val(id);
                            }
                            alert(data.info);
                        }

                    }
                    else {
                        showxbtips(data.info, 'alert-warning');
                    }
                }

            }

          );

        $('#categoryselect').click(function () {
            var caval = $('#categoryselectval').val();
            if(caval =='')
            {
                return false;
            }
            var site = $('#siteid').val();
            var i=0;
            ii = layer.load('查询中');
            url = '<?php echo admin_base_url("ebay/ebay_product/selectCategory");?>';
            $.ajax({
                url: url,
                data: 'caval=' + caval + '&site=' + site,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    layer.close(ii);
                    if(data.status==1)
                    {
                        $('#categoryselectid').empty().append('<tr><td></td><td>分类号</td><td>名称</td><td>匹配度</td></tr>');
                       for(i=0;i<data.data.length;i++)
                       {
                        $('#categoryselectid').append('<tr><td><input type="radio" name="categoryselect"/></td><td>'+data.data[i]['categoryid']+'</td><td>'+data.data[i]['stringname']+'</td><td>'+data.data[i]['match']+'%</td></tr>');
                       }

                    }
                    else
                    {
                        $('#categoryselectid').empty().append('<tr><td></td><td>分类号</td><td>名称</td><td>匹配度</td></tr>');
                    }
                }
            })
        })
        $('#categoryselectsub').click(function(){
            var categoryval = $('#categoryselectid input[name="categoryselect"]:checked ').parent().parent().children().eq(1).text();
            var categoryname = $('#categoryselectid input[name="categoryselect"]:checked ').parent().parent().children().eq(2).text();
            if(categoryval =='')
            {
                alert('请选择');
            }
            else
            {
                $('#myModalSelect').modal('toggle')
                $("#diyifenlei").val(categoryval);
                $('#diyifenleimiaoshu').empty().val(categoryname);
                $('#diyifenleimiaoshu').removeClass('hidden');

                changesite();
                addmul();
            }
        })


        $('#transtemplate').change(function(){
            var transvalue =  $('#transtemplate').val();

            if(transvalue=='')
            {
                return false;
            }
            var  siteid = $('#siteid').val();
            $.ajax({
                url: '<?php echo admin_base_url("ebay/ebay_product/autosettrans");?>',
                data: 'transvalue='+transvalue+'&site='+siteid,
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {

                    if(data.status==1)
                    {
                        if(data.data['transinfo']['inter_process_day'] !='')
                        {
                           // $('#guoneichulishijian').prop("checked",true);
                            $("#guoneichulishijian").val(data.data['transinfo']['inter_process_day']);
                        }
                        if(data.data['transinfo']['inter_fast_send'] =='true')
                        {
                            $('#guoneikuaisu').prop("checked",true);
                        }
                        //inter_trans_type
                        if(data.data['transinfo']['inter_trans_type'] !='')
                        {
                            $('#guoneiyunshu1').val(data.data['transinfo']['inter_trans_type']);
                        }
                        if(data.data['transinfo']['inter_free'] =='true')
                        {
                            $('#guoneimianfei1').prop("checked",true);
                        }
                        else
                        {
                            if(data.data['transinfo']['inter_trans_cost'] !='')
                            {
                                $('#guoneiyunfei1').val(data.data['transinfo']['inter_trans_cost']);
                            }

                            if(data.data['transinfo']['inter_trans_extracost'] !='')
                            {
                                $('#guoneiewaijiashou1').val(data.data['transinfo']['inter_trans_extracost']);
                            }
                        }
                        var InputsWrapper = $("#InputsWrapper"); //Input boxes wrapper ID
                        var i=1;
                        $(InputsWrapper).empty();
                        for(i=1;i<=5;i++)
                        {

                            var international_type= 'international_type'+i;
                            if(data.data['transinfo'][international_type] !="")
                            {
                            //    alert(data.data['transinfo']['international_cost'+i]);

                                if (i == 1) {
                                    var tran = '第一运输';
                                }
                                if (i == 2) {
                                    var tran = '第二运输';
                                }
                                if (i == 3) {
                                    var tran = '第三运输';
                                }
                                if (i == 4) {
                                    var tran = '第四运输';
                                }
                                if (i == 5) {
                                    var tran = '第五运输';
                                }

                                $(InputsWrapper).append('<div  id="yunshu' + i + '"><div class="form-group"><label class="col-sm-2 control-label"   ></label><div class="col-sm-6"><span>'+tran+'</span></div></div>' +
                                '<div class="form-group"><label class="col-sm-2 control-label"   >运输方式</label><div class="col-sm-6"><select name="yunshufangshi' + i + '" id="yunshufangshi' + i + '">'+data.data['guowaitrans']+'</select></div></div>' +
                                '<div class="form-group"><label class="col-sm-2 control-label"   >运费</label><div class="col-sm-6"><input type="text" name="yunfei' + i + '" id="yunfei' + i + '" placeholder="0.00"/><input type="checkbox" name="mianfei' + i + '" value="true" checked="checked" id="mianfei' + i + '"/> <span>免费</span> </div></div>' +
                                '<div class="form-group"><label class="col-sm-2 control-label"   >额外每件加收</label><div class="col-sm-6"><input type="text" name="ewai' + i + '" id="ewai' + i + '" placeholder="0.00"/></div></div>' +
                                '<div class="form-group"><label class="col-sm-2 control-label"   >运到</label><div class="col-sm-6"><input type="checkbox"  name="Worldwide' + i + '" id="quanqiu' + i + '"/><span>全球   </span><a href="#" id="guanjia' + i + '"  class="fc">选择以下所有国家和地区</a>' +
                                '<br/><input type="checkbox" value="CN" name="guanjia' + i + '[]"/>中国</input>' +
                                '<input type="checkbox" value="RU" name="guanjia' + i + '[]"/>俄罗斯联邦</input>' +
                                '<input type="checkbox" value="CA" name="guanjia' + i + '[]"/>加拿大</input>' +
                                '<input type="checkbox" value="BR" name="guanjia' + i + '[]"/>巴西</input>' +
                                '<input type="checkbox" value="DE" name="guanjia' + i + '[]"/>德国</input>' +
                                '<input type="checkbox" value="FR" name="guanjia' + i + '[]"/>法国</input>' +
                                '<input type="checkbox" value="Europe" name="guanjia' + i + '[]"/>欧洲</input>' +
                                '<input type="checkbox" value="GB" name="guanjia' + i + '[]"/>联合王国</input>' +
                                '<input type="checkbox" value="EuropeanUnion" name="guanjia' + i + '[]"/>欧盟</input>' +
                                '<input type="checkbox" value="Americas" name="guanjia' + i + '[]"/>美洲</input>' +
                                '<input type="checkbox" value="US" name="guanjia' + i + '[]"/>美国</input>' +
                                '<input type="checkbox" value="Asia" name="guanjia' + i + '[]"/>亚洲</input>' +
                                '<input type="checkbox" value="AU" name="guanjia' + i + '[]"/>澳大利亚</input>' +
                                '<input type="checkbox" value="MX" name="guanjia' + i + '[]"/>墨西哥</input>' +
                                '<input type="checkbox" value="JP" name="guanjia' + i + '[]"/>日本</input></div></div></div>');
                            }
                            if(data.data['transinfo']['international_type'+i]!='')
                            {

                                $('#yunshufangshi'+i).val(data.data['transinfo']['international_type'+i])
                                if(data.data['transinfo']['international_free'+i]=='true')
                                {
                                    $('#mianfei'+i).prop("checked",true);
                                }
                                else
                                {
                                    $('#yunfei'+i).val(data.data['transinfo']['international_cost'+i]);
                                    $('#ewai'+i).val(data.data['transinfo']['international_extracost'+i])
                                    $('#mianfei'+i).prop('checked',false);
                                }

                                if(data.data['transinfo']['international_is_worldwide'+i]=='on')
                                {
                                    $('#quanqiu'+i).prop("checked",true);
                                }
                                else
                                {
                                    for(var j=0;j<data.data['transinfo']['international_is_country'+i].length;j++)
                                    {
                                     //   $("input[name='"+chcekboxname+"']").each(function(){
                                        var name = 'guanjia'+i+'[]';
                                        $('input[name="'+name+'"]').each(function(){
                                            if($(this).val() == data.data['transinfo']['international_is_country'+i][j])
                                            {
                                                $(this).prop('checked',true);
                                            }
                                        })
                                    }
                                }
                            }
                        }

                        if(data.data['transinfo']['returns_policy'] !='')
                        {
                            $('#tuihuozhengce').val(data.data['transinfo']['returns_policy']);
                        }
                        if(data.data['transinfo']['returns_days'] !='')
                        {
                            $('#tuihuotianshu').val(data.data['transinfo']['returns_days']);
                        }
                        if(data.data['transinfo']['returns_delay'] !='')
                        {
                            $('#returns_delay').prop("checked",true);
                        }
                        if(data.data['transinfo']['returns_type'] !='')
                        {
                            $('#tuihuofangshi').val(data.data['transinfo']['returns_type']);
                        }
                        if(data.data['transinfo']['returns_cost_by'] !='')
                        {
                            $('#tuihuochengdang').val(data.data['transinfo']['returns_cost_by']);
                        }
                        if(data.data['transinfo']['return_details'] !='')
                        {
                            $('#return_details').val(data.data['transinfo']['return_details']);
                        }

                        if(data.data['transinfo']['item_location'] !='')
                        {
                            $('#item_location').val(data.data['transinfo']['item_location']);
                        }

                        if(data.data['transinfo']['item_country'] !='')
                        {
                            $('#country').val(data.data['transinfo']['item_country']);
                        }

                        if(data.data['transinfo']['item_post'] !='')
                        {
                            $('#item_post').val(data.data['transinfo']['item_post']);
                        }

                        $('#excludeship').val(data.data['transinfo']['excludeship']); //排除的运输国家

                      //  alert(data.data['transinfo']['inter_trans_type']);
                    }
                    if(data.status==2)
                    {
                        alert(data.info);
                    }
                }
            })
        })
        })



    </script>