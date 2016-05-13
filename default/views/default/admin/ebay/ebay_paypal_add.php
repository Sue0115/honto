<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-18
 * Time: 11:04
 */

?>

<style>
    .row-border {
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 3px 4px 3px rgba(238, 238, 238, 1);
        margin-bottom: 10px;
    }

    .proh {
        width: 100%;
        height: 30px;
    }

    .hideaccordion, .showaccordion {
        float: left;
        height: 18px;
        line-height: 18px;
        position: relative;
        padding: 6px;
    }

    .hideaccordion h1, .showaccordion h1 {
        font-size: 14px;
        font-weight: bold;
        color: #444;
    }

    .hideaccordion h1 i {
        cursor: pointer;
    }

    .probody {
        width: 100%;
        height: 100%;
        padding: 0 10px;
    }

    .pic-main, .pic-detail, .relate-list {
        padding: 5px;
        border: 1px solid #ccc;
    }

    .pic-main li, .pic-detail li, .relate-list li {
        margin: 5px;
        padding: 0px;
        border: 0px;
        width: 102px;
        text-align: right;
    }

    .table-vcenter td {
        vertical-align: middle !important;
    }

    /***Validform的样式--su20141125***/
    .Validform_checktip {
        margin-left: 8px;
        line-height: 20px;
        height: 20px;
        overflow: hidden;
        color: #999;
        font-size: 12px;
    }

    /*.Validform_right{color:#71b83d;padding-left:20px;background:url(images/right.png) no-repeat left center;}
    .Validform_wrong{color:red;padding-left:20px;white-space:nowrap;background:url(images/error.png) no-repeat left center;}
    .Validform_loading{padding-left:20px;background:url(images/onLoad.gif) no-repeat left center;}*/
    .Validform_error {
        background-color: #ffe7e7;
    }

    #Validform_msg {
        color: #7d8289;
        font: 12px/1.5 tahoma, arial, \5b8b\4f53, sans-serif;
        width: 280px;
        background: #fff;
        position: absolute;
        top: 0px;
        right: 50px;
        z-index: 99999;
        display: none;
        filter: progid:DXImageTransform.Microsoft.Shadow(Strength=3, Direction=135, Color='#999999');
        -webkit-box-shadow: 2px 2px 3px #aaa;
        -moz-box-shadow: 2px 2px 3px #aaa;
    }

    #Validform_msg .iframe {
        position: absolute;
        left: 0px;
        top: -1px;
        z-index: -1;
    }

    #Validform_msg .Validform_title {
        line-height: 25px;
        height: 25px;
        text-align: left;
        font-weight: bold;
        padding: 0 8px;
        color: #fff;
        position: relative;
        background-color: #000;
    }

    #Validform_msg a.Validform_close:link, #Validform_msg a.Validform_close:visited {
        line-height: 22px;
        position: absolute;
        right: 8px;
        top: 0px;
        color: #fff;
        text-decoration: none;
    }

    #Validform_msg a.Validform_close:hover {
        color: #cc0;
    }

    #Validform_msg .Validform_info {
        padding: 8px;
        border: 1px solid #000;
        border-top: none;
        text-align: left;
    }

    </style>

<form action="<?php echo admin_base_url('ebay/ebay_template/save_paypal'); ?>" class="form-horizontal validate_form"
      method="post">


    <div class="row row-border">
        <div class="proh">
            <div class="hideaccordion">
                <h1>
                    <i class="icon-plus"></i>
                    <span>&nbsp;基本信息</span>
                </h1>
            </div>
        </div>

        <div class="probody">
            <div class="procnt">
                <div class="form-group">
                    <label class="control-label col-sm-2">账号：</label>

                    <div class="col-sm-4">

                        <select name="account">
                            <option value="">==请选择==</option>
                            <?php foreach ($account as $ac): ?>
                                <option value="<?php echo $ac; ?>" <?php   if(isset($one_info['ebay_account'])&&($one_info['ebay_account']==$ac)){ echo 'selected="selected"' ; }          ?>   ><?php echo $ac; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <?php  if(isset($one_info['currency'])&&(!empty($one_info['currency']))){

                    $currency_site  = json_decode($one_info['currency'],true);
                } ?>
                <?php foreach($currency as $cur):  ?>
                    <div class="form-group">
                        <label class="control-label col-sm-2"><?php  echo  $cur; ?> :</label>

                        <div class="col-sm-4">
                            <input type="text" name="currency[<?php  echo  $cur; ?>]"  class="col-sm-4"  value="<?php if(isset($currency_site[$cur])){echo $currency_site[$cur]; }  ?>">
                        </div>

                    </div>
                <?php endforeach;  ?>



                <?php if(isset($one_info['paypal_account'])&&(!empty($one_info['paypal_account']))){
                    $paypal_account = explode(',',$one_info['paypal_account']);
                }      ?>

                <div class="form-group">
                    <label class="control-label col-sm-2">大PP :</label>

                    <div class="col-sm-4">
                        <select name="big_paypal">
                            <option value="">==请选择==</option>
                            <?php foreach ($paypal as $p): ?>
                                <option value="<?php echo $p['paypal_email_address']; ?>" <?php   if(isset($paypal_account[0])&&($paypal_account[0]==$p['paypal_email_address'])){ echo 'selected="selected"' ; }          ?>  ><?php echo $p['paypal_email_address']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>



                <div class="form-group">
                    <label class="control-label col-sm-2">小PP :</label>

                    <div class="col-sm-4">
                        <select name="small_paypal">
                            <option value="">==请选择==</option>
                            <?php foreach ($paypal as $p): ?>
                                <option value="<?php echo $p['paypal_email_address']; ?>" <?php   if(isset($paypal_account[1])&&($paypal_account[1]==$p['paypal_email_address'])){ echo 'selected="selected"' ; }          ?>><?php echo $p['paypal_email_address']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>




                <div class="form-group">
                    <label class="control-label col-sm-2">账号后缀 :</label>

                    <div class="col-sm-4">
                        <input type="text" name="account_suffix"  value="<?php   if(isset($one_info['account_suffix'])){ echo $one_info['account_suffix'] ; }          ?>" class="col-sm-4" ><span class="red">不需要输入[]</span>
                    </div>

                </div>


            </div>

            <div class="promsg" style="display: none;">
            </div>
        </div>
    </div>


    <div class="clearfix form-actions">
        <div class="col-md-offset-2 col-md-9">


            <input type="hidden" name="id" value="<?php if(isset($one_info['id'])){ echo $one_info['id']; } ?>" id="id"/>

            <button class="btn btn-success submit_btn" type="submit" name="save">
                <i class="icon-ok bigger-110"></i>
                保存
            </button>



            <a class="btn btn "  href="<?php echo admin_base_url("ebay/ebay_template/accountpaypal"); ?>" >
                返回列表
            </a>

        </div>
    </div>

</form>

<script type="text/javascript">
    $(function () {
        $('.validate_form').Validform({
            btnSubmit: '.submit_btn',
            btnReset: '.btn-reset',
            ignoreHidden: true,
            ajaxPost: true,
            callback: function (data) { //返回数据
                if (data.status) {
                    if (data.data) {
                        $('#id').val(data.data);
                    }
                    showxbtips(data.info);
                } else {
                    showxbtips('出错了');
                }
            }
        });
    });

</script>