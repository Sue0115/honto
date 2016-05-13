<!--date S-->

<script type="text/javascript" src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>?<?php echo random(3)?>"></script>
<script>
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
</script>
<!--date E-->