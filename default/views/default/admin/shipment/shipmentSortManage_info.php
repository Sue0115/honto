<?php 
echo ace_header('物流分类',$shipmentSortManageInfo->shipmentCatID);

echo ace_form_open('','',array('shipmentCatID'=>$shipmentSortManageInfo->shipmentCatID));

	echo ace_input('分类名称','shipmentCatName',$shipmentSortManageInfo->shipmentCatName);
	
  	echo ace_srbtn('shipment/shipmentSortManage/info');
  echo ace_form_close()
?>
