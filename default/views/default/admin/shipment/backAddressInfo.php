<?php 
echo ace_header('回邮地址',$backData['id']);
?>
<style>
table{
	width:1600px;
	margin:0 auto;
}
.in{
	text-align:center;
}
table tr{
	height:40px;
}
</style>
<?php echo ace_form_open('','',array('id'=>$backData['id']));?>
<div id="address">
   <table>
   		 <colgroup> 
			<col width="10%">
			<col width="25%">
            <col width=70%">
         </colgroup>
	     <tbody id="tbody_content">
	      <tr>
	         <td style="text-align:right;">API信息</td>
	         <td class="in"><input type="text" value="<?php echo $backData['apiInfo']?>" name="apiInfo" size="40"/></td>
	         <td>授权码,客户编码,大客户编码(大客户编码可为空，线下eub API 信息)</td>
	       </tr>
	       <tr>
	         <td style="text-align:right;">回邮地址标题</td>
	         <td class="in"><input type="text" value="<?php echo $backData['eub_setting_title']?>" name="eub_setting_title" size="40"/></td>
	         <td>请填写回邮地址标题</td>
	       </tr>
	       <tr>
	         <td style="text-align:right;">是否使用E邮宝 API</td>
	         <td class="in"><input type="text" value="<?php echo $backData['is_use_eub_api']?>" name="is_use_eub_api" size="40"/></td>
	         <td>
	         	  设置E邮宝 API功能: 1 - 使用E邮宝 API,打开该功能后，请确保销售帐号对应的E邮宝 API签名等内容已完善, <br/>
	             0 - 关闭E邮宝 API,如果E邮宝 API的签名尚未获取，请设置为关闭。关闭后，系统将关闭E邮宝发货方式
	         </td>
	       </tr>
	       <tr>
	         <td style="text-align:right;">EUB打印方式</td>
	         <td class="in"><input type="text" value="<?php echo $backData['eub_print_method']?>" name="eub_print_method" size="40"/></td>
	         <td>标签格式，可用值：0 - 适用于打印 A4 格式标签 1 – 适用于打印 4寸 的热敏标签纸格式标签</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝交运方式</td>
	         <td class="in"><input type="text" value="<?php echo $backData['eub_shipping']?>" name="eub_shipping" size="40"/></td>
	         <td>设置E邮宝交运方式，0 - 上门揽收 ， 1 - 卖家自送</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">联系人</td>
	         <td class="in"><input type="text" value="<?php echo $backData['contacter']?>" name="contacter" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时的联系人</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">公司名称</td>
	         <td class="in"><input type="text" value="<?php echo $backData['company']?>" name="company" size="40"/></td>
	         <td>设置E邮宝上门揽收时包裹所在公司</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">街道</td>
	         <td class="in"><input type="text" value="<?php echo $backData['street']?>" name="street" size="40"/></td>
	         <td>设置E邮宝上门包裹时的详细街道地址及门牌号</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">地区代码</td>
	         <td class="in"><input type="text" value="<?php echo $backData['areaCode']?>" name="areaCode" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时所在地区的代码，地址代码请参考相关文档</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">城市代码</td>
	         <td class="in"><input type="text" value="<?php echo $backData['cityCode']?>" name="cityCode" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时的所在城市的代码，代码请参考相关文档</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">省份代码</td>
	         <td class="in"><input type="text" value="<?php echo $backData['provinceCode']?>" name="provinceCode" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时所在省份的代码，代码请参考相关文档</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">邮编</td>
	         <td class="in"><input type="text" value="<?php echo $backData['zipCode']?>" name="zipCode" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时的地址对应邮编</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">国家</td>
	         <td class="in"><input type="text" value="<?php echo $backData['country']?>" name="country" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹的国家</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">Email</td>
	         <td class="in"><input type="text" value="<?php echo $backData['email']?>" name="email" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时的联系email</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">移动电话</td>
	         <td class="in"><input type="text" value="<?php echo $backData['mobilePhone']?>" name="mobilePhone" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时的联系移动电话</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">固定电话</td>
	         <td class="in"><input type="text" value="<?php echo $backData['phone']?>" name="phone" size="40"/></td>
	         <td>设置E邮宝上门揽收包裹时的联系电话</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件人</td>
	         <td class="in"><input type="text" value="<?php echo $backData['sender']?>" name="sender" size="40"/></td>
	         <td>设置E邮宝寄件人(请用英文填写)</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件公司名称</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderCompany']?>" name="senderCompany" size="40"/></td>
	         <td>设置E邮宝寄件人公司名(请用英文填写)</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件街道地址</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderStreet']?>" name="senderStreet" size="40"/></td>
	         <td>设置E邮宝寄件人街道地址(请用英文填写)</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件地区</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderArea']?>" name="senderArea" size="40"/></td>
	         <td>设置E邮宝的寄件地区(请用英文填写)</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件城市</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderCity']?>" name="senderCity" size="40"/></td>
	         <td>设置E邮宝寄件城市(请用英文填写)</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件 州/省</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderState']?>" name="senderState" size="40"/></td>
	         <td>设置E邮宝寄件 州/省(请用英文填写)</td>
	       </tr>
	       <tr>
	         <td style="text-align:right;">E邮宝寄件 州/省编码</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderProvinceCode']?>" name="senderProvinceCode" size="40"/></td>
	         <td><span style="color:red;">设置E邮宝寄件 州/省编码(请用编码填写,仅限线下E邮宝使用)</span></td>
	       </tr>
	       <tr>
	         <td style="text-align:right;">E邮宝寄件城市编码</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderCityCode']?>" name="senderCityCode" size="40"/></td>
	         <td><span style="color:red;">设置E邮宝寄件 城市编码(请用编码填写,仅限线下E邮宝使用)</span></td>
	       </tr>
	       <tr>
	         <td style="text-align:right;">E邮宝寄件地区编码</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderAreaCode']?>" name="senderAreaCode" size="40"/></td>
	         <td><span style="color:red;">设置E邮宝寄件地区编码(请用编码填写,仅限线下E邮宝使用)</span></td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件国家</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderCountry']?>" name="senderCountry" size="40"/></td>
	         <td>设置E邮宝寄件国家(请用英文填写)</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件地邮编</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderZip']?>" name="senderZip" size="40"/></td>
	         <td>设置E邮宝寄件地址对应邮编</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件人Email</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderEmail']?>" name="senderEmail" size="40"/></td>
	         <td>设置E邮宝寄件人电子邮件地址。</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">E邮宝寄件人移动电话</td>
	         <td class="in"><input type="text" value="<?php echo $backData['senderMobile']?>" name="senderMobile" size="40"/></td>
	         <td>设置E邮宝寄件人移动电话</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">系统默认报关申报价值</td>
	         <td class="in"><input type="text" value="<?php echo $backData['declared_value']?>" name="declared_value" size="40"/></td>
	         <td>订单物品未设置报关信息时，物品申报价值将以此值为准。</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">系统默认报关申报重量</td>
	         <td class="in"><input type="text" value="<?php echo $backData['declared_weight']?>" name="declared_weight" size="40"/></td>
	         <td>订单物品未设置报关信息时，物品申报重量将以此值为准</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">系统默认中文报关物品名称</td>
	         <td class="in"><input type="text" value="<?php echo $backData['declared_cn']?>" name="declared_cn" size="40"/></td>
	         <td>订单物品未设置报关信息时，物品中文报关名称将以此值为准</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">系统默认英文报关物品名称</td>
	         <td class="in"><input type="text" value="<?php echo $backData['declared_en']?>" name="declared_en" size="40"/></td>
	         <td>订单物品未设置报关信息时，物品英文报关名称将以此值为准</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">系统默认物品原产地国家或地区代码</td>
	         <td class="in"><input type="text" value="<?php echo $backData['countryCode']?>" name="countryCode" size="40"/></td>
	         <td>订单物品未设置报关信息时，物品原产地国家或地区代码将以此值为准。可用值请参考相关文件(根目录/Config/CountryCode.ini)。</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--联系人</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_contacter']?>" name="back_contacter" size="40"/></td>
	         <td>退回包裹--联系人（中文填写）</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--公司名</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_company']?>" name="back_company" size="40"/></td>
	         <td>退回包裹--公司名（中文填写）</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--地址</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_address']?>" name="back_address" size="40"/></td>
	         <td>退回包裹--地址（中文填写）</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--区</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_area']?>" name="back_area" size="40"/></td>
	         <td>退回包裹--区（中文填写）</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--市</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_city']?>" name="back_city" size="40"/></td>
	         <td>退回包裹--市（中文填写）</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--省</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_province']?>" name="back_province" size="40"/></td>
	         <td>退回包裹--省（中文填写）</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--国家</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_country']?>" name="back_country" size="40"/></td>
	         <td>退回包裹--国家（中文填写）</td>
	       </tr>
	        <tr>
	         <td style="text-align:right;">退回包裹--邮编</td>
	         <td class="in"><input type="text" value="<?php echo $backData['back_zip']?>" name="back_zip" size="40"/></td>
	         <td>退回包裹--邮编（中文填写）</td>
	       </tr>
	     </tbody>
   </table>
</div>

<?php
	  echo ace_srbtn('shipment/backAddress');
	  
	  echo ace_form_close();
?>
