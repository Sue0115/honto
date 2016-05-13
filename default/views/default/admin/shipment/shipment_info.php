<?php 
echo ace_header('物流',$shipmentInfo->shipmentID);
?>
<style>
 #ship{
	width:100%;
	height:auto;
	
 }
 table{
 	margin:0 auto;
 }
 input{
	height:33px;
 }
 td{
	width:500px;
 	height:40px;
 }
 td span{
	font-size:12px;
 	color:red;
 	font-weight:bold;
 	padding-left:30px;
 }
 td b{
	color:red;
 }
 font{
     cursor: pointer;
 	 color: #FF0000;
 	 display:inline-block;
 	 width:30px;  
 	 font-size: 20px;
 	 font-weight:bold;
 	 text-align:center;
 }
 #inn{
	cursor: pointer;
 	 color: #FF0000;
 	 display:inline-block;
 	 width:30px;  
 	 font-size: 20px;
 	 font-weight:bold;
 	 text-align:center;
 }
.box_content_style{
	width:1000px;
    margin:0 auto;
    margin-top:15px;
 	margin-bottom:5px;
}
</style>
 <div id="ship">
   <?php echo ace_form_open('','',array('shipmentID'=>$shipmentInfo->shipmentID));?>
     <table width="1000" >
       <tr>
         <td>
           	所在仓库
           <select class="require" title="所在仓库为必选项" name="shipment_warehouse_id" id="shipment_warehouse_id">
            <option value="">请选择所属仓库</option>
			<?php foreach($warehouse as $key => $v):?>
             <option value="<?php echo $key;?>"><?php echo $v?></option>
             <?php endforeach;?>
           </select>
         </td>
         <td>
         	  物流分类
           <select class="require" title="物流分类为必选项" name="shipmentCategoryID" id="shipmentCategoryID">
             <option value="">请选择物流分类</option>
             <?php foreach($allShipmentCategory as $val):?>
              <option value="<?php echo $val->shipmentCatID?>"><?php echo $val->shipmentCatName?></option>
             <?php endforeach;?>
           </select>
         </td>
       </tr>
       <tr>
         <td>
           	物流方式
           	<input name="shipmentTitle" type="text" id="shipmentTitle" title="物流名称为必填项" class="require" size="40" value="<?php echo $shipmentInfo->shipmentTitle?>" />
           	<input name="shipmentID" type="hidden" id="shipmentID" value="<?php echo $shipmentInfo->shipmentID?>" />
         </td>
         <td>
           	物流方式英文名称
           	<input name="shipmentEnTitle" type="text" id="shipmentEnTitle" value="<?php echo $shipmentInfo->shipmentEnTitle?>" />
         </td>
       </tr>
       <tr>
         <td colspan="2">
           	适用范围
           	<input name="shipmentElementMin" type="text" id="shipmentElementMin" title="适用范围最小值为必填项" lang="mustint_0" value="<?php echo $shipmentInfo->shipmentElementMin?>"/> 
           	～ 
           	<input name="shipmentElementMax" type="text" id="shipmentElementMax" title="适用范围最大值为必填项" lang="mustint_0.001" value="<?php echo $shipmentInfo->shipmentElementMax?>"/>
         </td>
       </tr>
       <tr>
         <td colspan="2">
           	计算方式一:<input type="radio" name="shipmentCalculateMethod" value="weight" />重量(Kg)
         </td>
       </tr>
       <tr>
         <td colspan="2">
           	参数设置<br/>
           	 首重:<input name="shipmentCalculateElementArray[first][unit]" type="text" value="<?php echo $shipmentInfo->shipmentCalculateElementArray['first']['unit']?>" size="18"/>Kg 
           	 费用:<input name="shipmentCalculateElementArray[first][feeTax]" type="text" value="<?php echo $shipmentInfo->shipmentCalculateElementArray['first']['feeTax']?>" size="18"/>RMB
           	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           	续重:<input name="shipmentCalculateElementArray[additional][unit]" type="text" value="<?php echo $shipmentInfo->shipmentCalculateElementArray['additional']['unit']?>" size="18"/>Kg 
           	费用:<input name="shipmentCalculateElementArray[additional][feeTax]" type="text" value="<?php echo $shipmentInfo->shipmentCalculateElementArray['additional']['feeTax']?>" size="18"/>RMB<br/>
           	<span>*(计算公式: 运费 = 首重费用 + {[总重 - 首重] ÷ 续重} * 续重费用 + 操作费 ) </span>
         </td>
       </tr>
       <tr>
         <td colspan="2">
           	计算方式二:<input type="radio" name="shipmentCalculateMethod" value="sangeweight" />重量
           	【<a href="javascript:getsangeWeightList()">添加</a>】<br/>参数设置
         </td>
       </tr>
       <tr id="sangeweight1">
         <td colspan="2">
                         范围：<input name="shipmentSangeCalculateElementArray[1][start]" type="text" value="<?php echo $shipmentInfo->shipmentSangeCalculateElementArray[1]['start']?> " />Kg
                          ～ <input name="shipmentSangeCalculateElementArray[1][end]" type="text" value="<?php echo $shipmentInfo->shipmentSangeCalculateElementArray[1]['end']?> " />Kg
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         费用:<input name="shipmentSangeCalculateElementArray[1][operational]" type="text" value="<?php echo $shipmentInfo->shipmentSangeCalculateElementArray[1]['operational']?>"/>RMB
         </td>
       </tr>
       <?php 
        if(!empty($shipmentInfo->shipmentSangeCalculateElementArray)):
        foreach($shipmentInfo->shipmentSangeCalculateElementArray as $key=>$v):
          if($key==1): continue; endif;
       ?>
       <tr id="sangeweight<?php echo $key;?>">
         <td colspan="2">
                         范围：<input name="shipmentSangeCalculateElementArray[<?php echo $key;?>][start]" type="text" value="<?php echo $shipmentInfo->shipmentSangeCalculateElementArray[$key]['start']?> " />Kg
                          ～ <input name="shipmentSangeCalculateElementArray[<?php echo $key;?>][end]" type="text" value="<?php echo $shipmentInfo->shipmentSangeCalculateElementArray[$key]['end']?> " />Kg
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         费用:<input name="shipmentSangeCalculateElementArray[<?php echo $key;?>][operational]" type="text" value="<?php echo $shipmentInfo->shipmentSangeCalculateElementArray[$key]['operational']?>"/>RMB
        	<?php if($key>1):?><font onclick="deproduct(this)">×</font><?php endif;?>
         </td>
       </tr>
       <?php 
        endforeach;
        endif;
       ?>
       <tr>
         <td>
         	优先级:<input name="shipmentRate" type="text" id="shipmentRate" title="物流匹配优先级为必填项" lang="mustint_0" value="<?php echo $shipmentInfo->shipmentRate?>"  maxlength="3"/>(0为最高优先级,999为最低优先级)
         </td>
         <td>
           	追踪码<input name="shipmentNeedTrackingCode" type="radio" value="0" />不需要  
           		 <input name="shipmentNeedTrackingCode" type="radio" value="1" />需要
         </td>
       </tr>
       <tr>
         <td>
         	预计送达时间&nbsp;
         	<input name="for_delivered" type="text" id="for_delivered" title="预计这个时间之后送达" class="require" value="<?php echo $shipmentInfo->for_delivered;?>" >
         	(单位：天) <b>(填写 0 表示不发送问候邮件）</b>
         </td>
         <td>
         	面单标签
         	<input type="radio" name="shipmentCustomLabel" value="registered" />挂号
         	<input type="radio" name="shipmentCustomLabel" value="express" />快递
         	<input type="radio" name="shipmentCustomLabel" value="special" />专线
         	<input type="radio" name="shipmentCustomLabel" value="none" />无
         </td>
       </tr>
       <tr>
         <td>
          ebay承运商
          <input name="shipmentCarrierInfo[name]" type="text" id="shipmentCarrierebayCode" value="<?php echo $shipmentInfo->shipmentCarrierInfo['name'];?>" />
         </td>
         <td>
                      扫描方式
          <input type="radio" name="shipmentScanMethod" value="ordersID" />内单号
          <input type="radio" name="shipmentScanMethod" value="trackingNumber" />追踪码
         </td>
       </tr>
       <tr>
         <td>
           ebay承运商
           <select id="nameen" onchange="changeEbayCode();" name="nameen">
            <option  value="">-请选择承运商-</option>  
            <?php foreach($amzList as $va):?>
             <option value="<?php echo $va->amz_logisticName;?>"><?php echo $va->amz_logisticName;?></option>
            <?php endforeach;?>                			   
           </select>
         </td>
          <td>
          	扫描位置
          	<input type="radio" name="shipmentScanLocal" value="1" />ERP扫描
          	<input type="radio" name="shipmentScanLocal" value="2" />海外仓扫描
         </td>
       </tr>
       <tr>
         <td>
           wish承运商
           <select name="shipmentWishCodeID" id="shipmentWishCodeID">
            <option  value="">-请选择承运商-</option> 
             <?php foreach($wishList as $va):?>
             <option value="<?php echo $va->logistics_id?>"><?php echo $va->logistics_name?></option>
            <?php endforeach;?>               			   
           </select>
         </td>
         <td>
          	上传挂号
          	<input name="updateTrackingNumber" type="radio" value="0"  />不需要
          	<input name="updateTrackingNumber" type="radio" value="1"/>需要
         </td>
       </tr>
       <tr>
         <td>
         	   速卖通承运商
           <select name="shipmentSmtCodeID" id="shipmentSmtCodeID">
            <option  value="">-请选择承运商-</option>      
            <?php foreach($smtList as $va):?>
             <option value="<?php echo $va->logistics_id?>"><?php echo $va->logistics_name?></option>
            <?php endforeach;?>          			   
           </select>
         </td>
         <td>
          	 挂号码等同内单号
          	 <input name="equal_order_id" type="radio" value="1" />是
          	 <input name="equal_order_id" type="radio" value="0" />否
         </td>
       </tr>
       <tr>
         <td>
         	   物流查询方式
           <select name="shipmentTrackingUrl" id="shipmentTrackingUrl">
            <option  value="">-请选择-</option>
            <?php foreach($allTrackUrl as $url):?>
             <option value="<?php echo $url->track_id;?>"><?php echo $url->track_short_name;?></option>
            <?php endforeach;?>                			   
           </select>
           

         </td>
         <td>
	              自助打印
          	<input name="buffet_print" type="radio" value="1" />是
          	<input name="buffet_print" type="radio" value="0" />否
	     </td>
       </tr>
       <tr>
         <td>
          AMZ承运商
          <input type="text"  id="shipmentAMZCode" name="shipmentAMZCode" value="<?php echo $shipmentInfo->shipmentAMZCode;?>" />
         </td>
         <td>
                      已打印拦截
          <input name="shipmentIsIntercept" type="radio" value="0" />是
          <input name="shipmentIsIntercept" type="radio" value="1" />否
         </td>
       </tr>
       <tr>
         <td>
           AMZ承运商
           <select  onchange="changeAMZCode()" name="shipmentForAMZ" id="shipmentForAMZ">
            <option  value="">-请选择承运商-</option>  
            <?php foreach($amzList as $va):?>
             <option value="<?php echo $va->amz_logisticName;?>"><?php echo $va->amz_logisticName;?></option>
            <?php endforeach;?>           			   
           </select>
         </td>
         <td>
      	      销售可见
          	<input name="sales_view" type="radio" value="1" />是
          	<input name="sales_view" type="radio" value="0" />否
         </td>
       </tr>
       <tr>
         <td colspan="2">
         	物流渠道API获取
	        <select name="yw_channel_1" id="yw_channel_1">
		        <option value="">=请选择=</option>
		        <option value="燕文">燕文</option>
		        <option value="捷特">捷特</option>
		        <option value="云途">云途</option>
		    </select>
		    <input type="text"  id="yw_channel_2" name="yw_channel_2" value="<?php echo $shipmentInfo->yw_channel2;?>" />
	    </td>
	   
       </tr>
       <tr>
         <td colspan="2">
          	 选择面单打印模板
          	<select name="shipment_template" id="shipment_template">
     		 <option value="">请选择面单打印模板</option>
     		 <?php foreach($allTemplate as $va):?>
     		  <option value="<?php echo $va->id?>"><?php echo $va->template_name;?></option>
     		 <?php endforeach;?>
            </select>
         </td>
        
       </tr>
       <tr>
        <td colspan="2">
          	中邮一体化面单设置
          	<input type="radio" name="showPostOrder" value="1" />是(申报名称)
          	<input type="radio" name="showPostOrder" value="2" />是(分类)
          	<input type="radio" name="showPostOrder" value="0" />否
        </td>
       </tr>
       <tr>
		 <td>
		    ebay发货备注
		    <input type="text" name="ebayRemark" value="<?php echo $shipmentInfo->ebayRemark?>" />
		 </td>
		 <td>
		   ebay发货备注网址
		   <input type="text" name="ebaySearchUrl" value="<?php echo $shipmentInfo->ebaySearchUrl?>" />
		 </td>
	  </tr>
	  <tr>
		 <td>
		    wish发货备注
		    <input type="text" name="wishRemark" value="<?php echo $shipmentInfo->wishRemark?>" />
		 </td>
		 <td>
		   wish发货备注网址
		   <input type="text" name="wishSearchUrl" value="<?php echo $shipmentInfo->wishSearchUrl?>" />
		 </td>
	  </tr>
	  <tr>
		 <td>
		    smt发货备注
		    <input type="text" name="smtRemark" value="<?php echo $shipmentInfo->smtRemark?>" />
		 </td>
		 <td>
		   smt发货备注网址
		   <input type="text" name="smtSearchUrl" value="<?php echo $shipmentInfo->smtSearchUrl?>" />
		 </td>
	  </tr>
	  <tr>
		 <td>
		    amz发货备注
		    <input type="text" name="amzRemark" value="<?php echo $shipmentInfo->amzRemark?>" />
		 </td>
		 <td>
		   amz发货备注网址
		   <input type="text" name="amzSearchUrl" value="<?php echo $shipmentInfo->amzSearchUrl?>" />
		 </td>
	  </tr>
     </table>
     <div class="box_content_style">
		<div class="box_title_style">
		   匹配规则【<a href="javascript:getMatchRuleList()">添加</a>】
		</div>
		<div id="matchRuleListBox">
			<div>
			  <strong>1.</strong>
			  <select style="margin-left:60px;" class="require" title="匹配规则为必选项" name="shipmentRuleMatchArray[1][rule]">
				<option value="">==选择匹配规则==</option>
				<?php foreach($ruleList as $va):?>
				 <option value="<?php echo $va->ruleID?>"><?php echo $va->ruleTitle?></option>
				<?php endforeach;?>
			  </select>
			</div>	
		</div>
		<?php
		  if(!empty($shipmentInfo->shipmentSangeCalculateElementArray)):
		  foreach($shipmentInfo->shipmentRuleMatchArray as $k => $v):
		    if($k==1): continue; endif;
		?>
		<div id="matchRuleListBox">
			<div>
			  <strong><?php echo $k;?>.</strong>
			  <select style="margin-left:60px;" class="require" title="匹配规则为必选项" name="shipmentRuleMatchArray[<?php echo $k?>][rule]">
				<option value="">==选择匹配规则==</option>
				<?php foreach($ruleList as $va):?>
				 <option value="<?php echo $va->ruleID?>" <?php echo $shipmentInfo->shipmentRuleMatchArray[$k]['rule']==$va->ruleID? 'selected': '' ?>><?php echo $va->ruleTitle?></option>
				<?php endforeach;?>
			  </select>
			  <?php if($k>1):?><span id="inn" onclick="deproducts(this)">×</span><?php endif;?>
			</div>	
		</div>
		<?php endforeach;endif;?>
	</div>
	<div class="box_content_style">
	  物流查询网址
	  <input type="text" name="shipmentDescription" id="shipmentDescription"  title="物流查询网址"  value="<?php echo $shipmentInfo->shipmentDescription;?>" size="50">
	</div>
     <?php
	  echo ace_srbtn('shipment/shipmentManage/info');
	  
	  echo ace_form_close();
	 ?>
   
 </div>
 <div id="innerHTMLForShipmentRuleMatchArray" style="display: none;">
		 <strong>{i}.</strong>
		 <label><input name="shipmentRuleMatchArray[{i}][connectMethod]" type="radio" value="&&" checked="checked" />且</label> 
		 <label><input name="shipmentRuleMatchArray[{i}][connectMethod]" type="radio" value="||" />或</label>
		 <select name="shipmentRuleMatchArray[{i}][rule]" title="匹配规则为必选项" lang="require">
	    	<option value="">==选择匹配规则==</option>
	    	<?php foreach($ruleList as $va):?>
				 <option value="<?php echo $va->ruleID?>"><?php echo $va->ruleTitle?></option>
			<?php endforeach;?>
	  	 </select>
		 <span id="inn" onclick="deproducts(this)">×</span>
</div>
 <script>
 //选择ebay承运商
function changeEbayCode(){
	var jj=$("#nameen").val();
    $("#shipmentCarrierebayCode").val(jj);
}
//选择amz承运商
function changeAMZCode(){
	var tmp = $('#shipmentForAMZ').val();
	$('#shipmentAMZCode').val(tmp);
}
//添加匹配规则 add by zengrihua 2014.11.05
function getMatchRuleList()
{
	var ruleCount=$('#matchRuleListBox').find('div').length;
	ruleCount=ruleCount+1;
	string = document.getElementById('innerHTMLForShipmentRuleMatchArray').innerHTML;
	newString = string.replace(/{i}/g,ruleCount);
	box = document.getElementById('matchRuleListBox');
	var newRule = document.createElement('div');
		newRule.innerHTML = newString;
		box.appendChild(newRule);
}

//添加计算方式二 add by zengrihua 2014.11.05
function getsangeWeightList()
{
	var len=$('font').length;
	var alen=len+1;
    var clen=len+2;
	var html='<tr id="sangeweight'+clen+'"><td colspan="2">范围：<input name="shipmentSangeCalculateElementArray['+clen+'][start]" type="text" value="" />Kg～ <input name="shipmentSangeCalculateElementArray['+clen+'][end]" type="text" value="" />Kg&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;费用:<input name="shipmentSangeCalculateElementArray['+clen+'][operational]" type="text" value=""/>RMB<font onclick="deproduct(this)">×</font></td></tr>';
	$('#sangeweight'+alen).after(html);
}
//删除新添加的节点
function deproduct(e){
	if(confirm('确定要删除该选项吗？')){
	 productLine = e.parentNode;
	 box = productLine.parentNode;
	 box.remove(productLine);
	}
}
//删除匹配规则的节点
function deproducts(e){
	if(confirm('确定要删除该选项吗？')){
	 productLine = e.parentNode;
	 productLine.remove(e);
	}
}
//返回检查表单
$().ready(function(){
	$('.btn-success').click(function(){
	    var ob=$('.require');
	    var flag=false;
	    ob.each(function(e){
	    	if(this.value==''){
		    	flag=true;
				alert(this.title);
				return false;
			}
		});
	    if(flag==true){
		return false;
		}
	});
});
$(function(){
	$("#shipment_warehouse_id").val("<?php echo $shipmentInfo->shipment_warehouse_id;?>");
	$("#nameen").val("<?php echo $shipmentInfo->shipmentCarrierInfo['name'];?>");	
	$("#shipmentCategoryID").val("<?php echo $shipmentInfo->shipmentCategoryID;?>");
	$("#shipmentWishCodeID").val("<?php echo $shipmentInfo->shipmentWishCodeID;?>");
	$("#shipmentSmtCodeID").val("<?php echo $shipmentInfo->shipmentSmtCodeID;?>");
	$("#shipmentTrackingUrl").val("<?php echo $shipmentInfo->shipmentTrackingUrl;?>");
	$("#shipmentForAMZ").val("<?php echo $shipmentInfo->shipmentAMZCode;?>");
	$("#shipment_template").val("<?php echo $shipmentInfo->shipment_template;?>");
	$("#yw_channel_1").val("<?php echo $shipmentInfo->yw_channel1;?>");
	$("[name='shipmentRuleMatchArray[1][rule]']").val("<?php echo $shipmentInfo->shipmentRuleMatchArray[1]['rule']?>");

	
	$("input[name='shipmentCalculateMethod']").val(["<?php echo $shipmentInfo->shipmentCalculateMethod ? $shipmentInfo->shipmentCalculateMethod : 'weight';?>"]); 
	$("input[name='shipmentNeedTrackingCode']").val(["<?php echo $shipmentInfo->shipmentNeedTrackingCode ? $shipmentInfo->shipmentNeedTrackingCode : '0';?>"]);
	$("input[name='shipmentCustomLabel']").val(["<?php echo $shipmentInfo->shipmentCustomLabel ? $shipmentInfo->shipmentCustomLabel : 'registered' ;?>"]);
	$("input[name='shipmentScanMethod']").val(["<?php echo $shipmentInfo->shipmentScanMethod ?  $shipmentInfo->shipmentScanMethod : 'trackingNumber';?>"]);
	$("input[name='shipmentScanLocal']").val(["<?php echo $shipmentInfo->shipmentScanLocal ? $shipmentInfo->shipmentScanLocal : '0' ;?>"]);
	$("input[name='buffet_print']").val(["<?php echo $shipmentInfo->buffet_print ? $shipmentInfo->buffet_print : '0' ;?>"]);
	$("input[name='shipmentIsIntercept']").val(["<?php echo $shipmentInfo->shipmentIsIntercept ? $shipmentInfo->shipmentIsIntercept : '0' ;?>"]);
	$("input[name='sales_view']").val(["<?php echo $shipmentInfo->sales_view ? $shipmentInfo->sales_view : '0';?>"]);
	$("input[name='showPostOrder']").val(["<?php echo $shipmentInfo->showPostOrder ? $shipmentInfo->showPostOrder : '0';?>"]);
	$("input[name='equal_order_id']").val(["<?php echo $shipmentInfo->equal_order_id ? $shipmentInfo->equal_order_id : '0' ;?>"]);
	$("input[name='updateTrackingNumber']").val(["<?php echo $shipmentInfo->updateTrackingNumber ? $shipmentInfo->updateTrackingNumber : '0' ;?>"]);
});
</script>
