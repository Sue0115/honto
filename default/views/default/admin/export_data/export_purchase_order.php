<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">数据导出—采购订单数据</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
                <div class="row">
                    <div class="col-sm-12">
                        <form action="<?php echo admin_base_url('export_data/export_order_data/exportPurchaseOrder')?>" method="post" id="form">


                            采购类型
                            <select name="orders_type" id="orders_type" >
                                <option value="">--请选择--</option>
                                <option value="1">普通</option>
                                <option value="2">特采</option>
                            </select>


                          <!--  采购状态
                            <select name="po_status" id="po_status" >
                                <option value="">--请选择--</option>
                                <option value="2">已完成</option>
                                <option value="6">未完成</option>
                            </select>
-->


                            入库仓库
                            <select name="warehouse" id="warehouse" >
                                <option value="">--请选择--</option>
                                <?php foreach($warehouse as $key => $w):?>
                                    <option value="<?php echo $key?>"><?php echo $w;?></option>
                                <?php endforeach;?>
                            </select>




                            <br/>
                            入库日期
                            <input type="text"  value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="start_date" name="import_start" size="15"/>
                            ~
                            <input type="text" value="" datefmt="yyyy-MM-dd HH:mm:ss" class="Wdate" id="end_date" name="import_end" size="15"/>


                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                <a class="btn btn-primary btn-sm" id="export">导出数据</a>
                            </label>
                            <label>
                                <a class="btn btn-primary btn-sm" href="<?php admin_base_url('export_data/export_order_data/exprot_purchase_order')?>">清空</a>
                            </label>
                            <br/><span style="color:green;font-weight:bold;">时间筛选：例如（导出7月1日数据，时间段应选7月1日至7月2日）</span>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

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
    $("#export").click(function(){
        $("#form").submit();
    });
</script>