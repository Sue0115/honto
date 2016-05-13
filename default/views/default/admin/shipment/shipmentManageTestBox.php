
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


</style>

<div class="page-header">
                    <h1>
                     	 物流测试匹配
                        <small>
                            <i class="icon-double-angle-right"></i>
		                   	测试匹配
                        </small>
                    </h1>
                 </div>

 <div id="ship">
   <?php echo ace_form_open('','');?>
     <table width="1000" >
       <tr>
         <td colspan="2">
         	产品分类
             <select name="category" id="category">
               <option value="">==不限==</option>
               <?php foreach($categoryList as $v):?>
               <option value="<?php echo $v['category_id']?>"><?php echo $v['category_name']?></option>
               <?php endforeach;?>
             </select>          
         </td>
       </tr>
       <tr>
         <td>
                      订单类型
            <select name="category" id="category">
             <option value="">==不限==</option>
             <?php foreach($ordertype as $va):?>
              <option value="<?php echo $va['typeID']?>"><?php echo $va['typeName']?></option>
             <?php endforeach;?>
           </select>
         </td>
         <td>
           	包裹总重&nbsp;&nbsp;&nbsp;&nbsp;<input name="weight" type="text" id="weight"  />KG 
         </td>
       </tr>
       <tr>
         <td>
          	销售账号
          	<select name="sellerAccount" id="sellerAccount">
             <option value="">==不限==</option>
             <?php foreach($sellerCount as $seller):?>
              <option value="<?php echo $seller['seller_account']?>"><?php echo $seller['seller_account']?></option>
             <?php endforeach;?>
           </select>
         </td>
         <td>
         	订单总成本<input name="cost" type="text" id="cost" />RMB
         </td>
       </tr>
       <tr>
         <td>
          	发货国家
          	<select name="country" id="country" style="width:150px;">
             <option value="">==不限==</option>
              <?php foreach($countryList as $ca):?>
                <option value="<?php echo strtoupper($ca['country_en'])?>"><?php echo $ca['country_en']?>-<?php echo $ca['country_cn']?></option>
              <?php endforeach;?>
            </select>
         </td>
         <td>
         	买家付运费<input name="shippingFee" type="text" id="shippingFee" />USD
         </td>
       </tr>
       <tr>
         <td>
          	运输方式
          	<select name="shipmentSelected" id="shipmentSelected">
             <option value="">==不限==</option>
              <?php foreach($shippingMethod as $sa):?>
                <option value="<?php echo strtoupper($sa['methodTitle'])?>"><?php echo $sa['methodTitle']?></option>
              <?php endforeach;?>
            </select>
         </td>
         <td>
         	订单总金额<input name="ordersTotal" type="text" id="ordersTotal" />USD
         </td>
       </tr>
     </table>
     <?php
	  echo ace_srbtn('shipment/shipmentManage/index');
	 ?>
</div>

