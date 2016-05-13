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

<form action="<?php echo admin_base_url('ebay/ebay_store_category/add'); ?>" class="form-horizontal validate_form"
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
                    <label class="control-label col-sm-2">erp分类：</label>

                    <div class="col-sm-4">

                        <select name="erp_category">
                            <option value="">==请选择==</option>
                            <?php foreach ($erp_category as $ac): ?>
                                <option value="<?php echo $ac['category_id']; ?>" <?php   if(isset($one_info['erp_category'])&&($one_info['erp_category']==$ac['category_id'])){ echo 'selected="selected"' ; }          ?>   ><?php echo $ac['category_id'].'--'.$ac['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>


                <?php if(isset($one_info['category_with_store'])&&!empty($one_info['category_with_store'])){
                    $category_with_store = json_decode($one_info['category_with_store'],true);
                }  ?>
                <?php foreach($ebay_category as  $key=>$ebay):  ?>
                <div class="form-group">
                    <label class="control-label col-sm-2"><?php  echo $account[$key] ; ?>：</label>
                    <div class="col-sm-4">
                        <select name="ebay_category[<?php echo $key; ?>]" >
                            <option value="">==请选择==</option>
                          <?php  foreach($ebay['root'] as $v){      ?>
                              <option  <?php   if(isset($ebay['child'][$v['category_id']])) { echo 'disabled';} ?>  value="<?php echo $v['category_id']; ?>"  <?php  if(isset($category_with_store[$key])&&($category_with_store[$key]==$v['category_id']))    {echo 'selected="selected"';}               ?> ><?php  echo $v['category_name']; ?></option>
                                <?php   if(isset($ebay['child'][$v['category_id']])) {
                                  foreach($ebay['child'][$v['category_id']] as $child1):
                                      ?>
                                      <option      <?php   if(isset($ebay['child'][$child1['category_id']])) { echo 'disabled';} ?>       value="<?php echo $child1['category_id']; ?>" <?php  if(isset($category_with_store[$key])&&($category_with_store[$key]==$child1['category_id']))    {echo 'selected="selected"';}               ?> ><?php  echo '&nbsp; &nbsp;|-'.$child1['category_name']; ?></option>
                                      <?php   if(isset($ebay['child'][$child1['category_id']])) {
                                      foreach($ebay['child'][$child1['category_id']] as $child2):
                                          ?>

                                          <option value="<?php echo $child2['category_id']; ?>"  <?php  if(isset($category_with_store[$key])&&($category_with_store[$key]==$child2['category_id']))    {echo 'selected="selected"';}               ?> ><?php  echo' &nbsp; &nbsp;&nbsp; &nbsp; |-'.$child2['category_name']; ?></option>
                                      <?php  endforeach;
                                  }    ?>
                                      <?php  endforeach;
                              }    ?>



                          <?php }      ?>
                        </select>
                    </div>

                </div>
                <?php  endforeach; ?>




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



            <a class="btn btn "  href="<?php echo admin_base_url("ebay/ebay_store_category/store_category_list"); ?>" >
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