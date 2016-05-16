<?php

echo ace_header('账单',$item->id);
echo ace_form_open('','',array('id'=>$item->id));

echo ace_input_m('用途/备注' ,'remarks');
echo ace_input_m('金额' ,'money');
$timedata =array(
		'name'=>'active_time',
		'class'=>'width-100 Wdate',
		'datefmt'=>'yyyy-MM-dd HH:mm:ss',
	);
echo ace_input_m('使用时间' ,$timedata);
$group_list=array('1'=>'支出','2'=>'注入');
$data = array('label_text'=>'金额种类','help'=>'');
echo ace_dropdown($data,'type',$group_list);
echo ace_srbtn('honto/caiwu');
echo ace_form_close();

echo "<script src='".static_url('theme/common/My97DatePicker/WdatePicker.js')."'></script> ";
echo "
<script type='text/javascript'>
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
</script>
";
