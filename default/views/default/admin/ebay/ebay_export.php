<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-25
 * Time: 13:46
 */

?>

<style type="text/css">
    td{text-align:center;border-color:#ccc;}
</style>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">导入ebay刊登数据</h3>
        <div class="table-header"> &nbsp;  </div>
        <div class="table-responsive">
            <div class="dataTables_wrapper">

                <div class="row">
                    <div class="col-sm-12" style="width:666px;">
                        <form id='submitForm' method='post' enctype="multipart/form-data" action="<?php echo admin_base_url('ebay/ebay_export/ebay_export');?>">
                            <label><input class="btn btn-primary btn-sm" name='excelFile' type='file' id='file' ></label>
                            <label><input type='hidden' name='add' value='add'></label>
                            <label><a class="btn btn-primary btn-sm" id="export" >导入ebay刊登数据</a></label>
                         <!--   <label><a  target="_blank" href="<?php /*echo base_url('attachments/template/orderMemoTemplate.xls');*/?>"><span class="w-40-h-20">导入订单通关时间模版格式</span></a></label>-->
                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //导入excel
    $("#export").click(function(){
        var file=$("#file").val();
        if(file==""){ showxbtips('请选择需要导入的excel', 'alert-warning');return false;}
        $("#submitForm").submit();



    });
</script>