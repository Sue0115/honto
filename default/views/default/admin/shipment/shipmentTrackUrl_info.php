<?php 
echo ace_header('物流查询',$shipmentTrackUrlInfo->track_id);

echo ace_form_open('','',array('track_id'=>$shipmentTrackUrlInfo->track_id));

	echo ace_input('查询网址短名称','track_short_name',$shipmentTrackUrlInfo->track_short_name);
	
	echo ace_input('API查询网址','track_url',$shipmentTrackUrlInfo->track_url);
	$data = array('label_text'=>'API提交方法','help'=>'');
	$group_list=array('GET'=>'GET','POST'=>'POST');
	echo ace_dropdown($data,'track_method',$group_list,$shipmentTrackUrlInfo->track_method);
	
	echo ace_input('API提交内容','track_data',$shipmentTrackUrlInfo->track_data);
	
	echo ace_input('返回html的解析正则','track_return_value',$shipmentTrackUrlInfo->track_return_value);
	
	echo ace_input('官方查询网址','track_query_url',$shipmentTrackUrlInfo->track_query_url);
	
	
  	echo ace_srbtn('shipment/shipmentTrackUrl/info');
  echo ace_form_close()
?>
