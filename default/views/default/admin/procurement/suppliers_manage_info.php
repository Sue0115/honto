<?php 
    echo ace_header('产品', '1');

    if (empty($info['suppliers_id']))
    {
    	$info['suppliers_id'] = NULL;
    }
    echo ace_form_open('','',array('id'=>$info['suppliers_id']));
    
?>
	    <div class="row">
	       <div class="col-xs-12">
				  
                  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>供应商名称:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
                        	<input type="text" value="<?php echo (empty($info['suppliers_company'])) ? NULL : $info['suppliers_company']; ?>" 
	                        	class="width-100 form-control" name="suppliers_company"  
	                        	datatype="*" nullmsg="供应商名称为必填项" errormsg="供应商名称为必填项"
	                        	 />
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="blue"></span>                             
			        </div>
				  </div>
				  
				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>供应商地址:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input class="width-100 form-control" name="suppliers_address" type="text"
				    		value="<?php echo (empty($info['suppliers_address'])) ? NULL : $info['suppliers_address']; ?>"
				    		datatype="*" nullmsg="供应商地址为必填项" errormsg="供应商地址为必填项" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>
				  
				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>联系人:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_name" class="width-100"
				    		value="<?php echo (empty($info['suppliers_name'])) ? NULL : $info['suppliers_name']; ?>"
				    		datatype="*" nullmsg="联系人为必填项" errormsg="联系人为必填项" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>
				  
				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>联系电话:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_phone" class="width-100"
				    		value="<?php echo (empty($info['suppliers_phone'])) ? NULL : $info['suppliers_phone']; ?>"
				    		datatype="num" nullmsg="联系电话为必填项" errormsg="联系电话格式不对" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				 </div>
				  
				 <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>手机号:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_mobile" class="width-100"
				    		value="<?php echo (empty($info['suppliers_mobile'])) ? NULL : $info['suppliers_mobile']; ?>"
				    		datatype="num" nullmsg="手机号为必填项" errormsg="手机号格式不对" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				 </div>
				 
				 <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	传真号:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_fax" class="width-100"
				    		value="<?php echo (empty($info['suppliers_fax'])) ? NULL : $info['suppliers_fax']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>
				  
				  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	网址:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_website" class="width-100"
				    		value="<?php echo (empty($info['suppliers_website'])) ? NULL : $info['suppliers_website']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				 </div>
				 
				 <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	QQ:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_qq" class="width-100"
				    		value="<?php echo (empty($info['suppliers_qq'])) ? NULL : $info['suppliers_qq']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				 </div>
				 
				 
				 <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	旺旺号:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_wangwang" class="width-100"
				    		value="<?php echo (empty($info['suppliers_wangwang'])) ? NULL : $info['suppliers_wangwang']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				 
				 <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>退货地址:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input class="width-100 form-control" name="suppliers_return_address" type="text"
				    		value="<?php echo (empty($info['suppliers_return_address'])) ? NULL : $info['suppliers_return_address']; ?>"
				    		datatype="*" nullmsg="退货地址为必填项" errormsg="退货地址为必填项且最少10个字符" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				 
				 <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	开户行:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_bank" class="width-100"
				    		value="<?php echo (empty($info['suppliers_bank'])) ? NULL : $info['suppliers_bank']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	银行帐号:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_card_number" class="width-100"
				    		value="<?php echo (empty($info['suppliers_card_number'])) ? NULL : $info['suppliers_card_number']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	收款人:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_card_name" class="width-100"
				    		value="<?php echo (empty($info['suppliers_card_name'])) ? NULL : $info['suppliers_card_name']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	企业法人:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_company_manager" class="width-100"
				    		value="<?php echo (empty($info['suppliers_company_manager'])) ? NULL : $info['suppliers_company_manager']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	法人联系电话:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<input type="text" name="suppliers_company_manager_phone" class="width-100"
				    		value="<?php echo (empty($info['suppliers_company_manager_phone'])) ? NULL : $info['suppliers_company_manager_phone']; ?>" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
				</div>
				
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	付款方式:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<select name="pay_method">
					  		<option value="">=选择=</option>
							<?php 
							$pay_methods = payment_method();
							foreach ($pay_methods as $pay){
								echo '<option value="'.$pay['key'].'" '.($info['pay_method'] == $pay['key'] ? 'selected="selected"' : '').'>'.$pay['text'].'</option>';
							}
							?>
			            </select>
				    </div>
				</div>
				  
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>物流方式:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		
				    		<select name="ship_method" id="ship_method" lang="require" title="物流方式为必选项" datatype="*" nullmsg="物流方式为必填项" errormsg="物流方式为必填项">
							  	<option value="">=选择=</option>
					            <!-- 物流包邮；快递包邮；物流到付；快递到付；自提 -->
								<option value="物流包邮|0">物流包邮</option>
								<option value="快递包邮|0">快递包邮</option>
								<option value="物流到付|0">物流到付</option>
								<option value="快递到付|0">快递到付</option>
								<option value="自提|1">自提</option>
							  </select>
				    		
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>供货周期:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		
				    		<?php $arrivalMinDaysArray = defineSuppliersArrivalMinDays();?>
							  <select name="supplierArrivalMinDays" lang="require" title="该供应商的供货周期为必选项" datatype="*" nullmsg="供应商的供货周期为必选项" errormsg="供应商的供货周期为必选项">
							  	<option value="">==选择==</option>
								<?php foreach($arrivalMinDaysArray as $minDaysKey => $arrivalMinDays){?>
								<option value="<?php echo $minDaysKey;?>" <?php echo ($info['supplierArrivalMinDays'] == $minDaysKey) ? 'selected' : '';?>><?php echo $arrivalMinDays['text'];?> 系数:<?php echo $arrivalMinDays['modulus'];?></option>
								<?php }?>
							  </select>
				    		
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				
				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	<span class="red">*</span>收款信息:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<textarea rows="3" cols="50" name="gathering_information" datatype="*" nullmsg="收款信息为必选项" errormsg="收款信息为必选项"><?php echo $info['gathering_information'];?></textarea>
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				</div>
				
<!-- 				<div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	三证：营业执照，税务登记证，机构代码证；给个人付款5000元以上须提供收款人身份证
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    	
				    		<?php
						  		if ($attachmentData){
									echo '<table>';
						  			foreach ($attachmentData as $attachment){
						  				echo '<tr id="tr_'.$attachment['attachment_id'].'"><td width="120"><a href="'.$attachment['attachment_url'].'" target="_blank">'.$attachment['attachment_name'].'</a></td><td><a href="javascript:deleteAttachment('.$attachment['attachment_id'].')" onclick="return confirm(\'确定要删除？\');">删除</a></td></tr>';
						  			}
									echo '</table>';
						  		}
						  	?>
						  	<div id="file">
						  		<div class="newFile">
						  			<input type="file" name="suppliers_files[]" /><a href="javascript:addFile();">添加</a>
						  		</div>
						  	</div>
				    		
                            <i class="icon icon-info-sign"></i>
                        </span>
				        
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
                           <span class="red">
                           	备注:审单时请注意，营业执照必须填
                           </span>
			        </div>
				</div>
				 -->
				 
				 <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right">
				    	备注:
				    </label>
				    <div class="col-xs-12 col-sm-5">
				    	<span class="input-icon block input-icon-right">
				    		<textarea rows="3" cols="50" name="suppliers_remark"><?php echo $info['suppliers_remark'];?></textarea>
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
				</div>
				 
	       </div>
	    </div>
<?php 
        echo ace_srbtn('product/product_manage/index');      
      
        echo ace_form_close();
?>

<script>
$('#ship_method').val('<?php echo $info['ship_method']; ?>');
</script>