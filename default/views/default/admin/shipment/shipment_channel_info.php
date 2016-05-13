<?php 
    echo ace_header('物流渠道',$item->id);
?>
 <script src="<?php echo static_url('theme/ckeditor/ckeditor.js')?>"></script>
<?php
    echo ace_form_open('','',array('id'=>$item->id));
    
?>

	    <div class="row">
	       <div class="col-xs-12">
			
			      <input type="hidden" name="id" value="<?php echo $item->id?>" />
				  
                  <div class="form-group">
				    <label  class="col-xs-12 col-sm-2 control-label no-padding-right"> <span class="red">*</span>所属物流供应商</label>
				    <div class="col-xs-12 col-sm-5">
				        <select name="suppliers_id" id="suppliers_id">
                            <?php foreach($shipment_suppliers as $v){?>
                            <option value="<?php echo $v->suppliers_id?>"><?php echo $v->suppliers_company?></option>
                            <?php }?>
                        </select>
				    </div>
                     <div class="help-block col-xs-12 col-sm-reset inline">
	                           <span class="red"></span>                             
			        </div>
				  </div>
                  
				 <div class="form-group">
				    <label for="channel_name" class="col-xs-12 col-sm-2 control-label no-padding-right">
			            <span class="red">*</span>渠道名称
			        </label>
				    <div class="col-xs-12 col-sm-5">
				        <span class="input-icon block input-icon-right">
                        	<input type="text" value="<?php echo $item->channel_name?>" class="width-100" name="channel_name"  datatype="*" nullmsg="请输入渠道名称" errormsg="请输入渠道名称" />
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
			        <div class="help-block col-xs-12 col-sm-reset inline">
	                                                      
			        </div>
				  </div>
				  
				  
				  <div class="form-group">
				    <label for="channel_name" class="col-xs-12 col-sm-2 control-label no-padding-right">
			            <span class="red">*</span>渠道名称
			        </label>
				    <div class="col-xs-12 col-sm-5">
				        <span class="input-icon block input-icon-right">
                        	<textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
                            <i class="icon icon-info-sign"></i>
                        </span>
				    </div>
			        <div class="help-block col-xs-12 col-sm-reset inline">
	                                                      
			        </div>
				  </div>
				  

	       </div>
	    </div>
<?php 
        echo ace_srbtn('shipment/shipment_channel');      
      
        echo ace_form_close();
?>
    
	<script>
	    $(function(){
			var suppliers_id = '<?php echo $item->suppliers_id?>';
			if(suppliers_id){
				$("#suppliers_id").val(suppliers_id);
			}

			
             // Replace the <textarea id="editor1"> with a CKEditor
             // instance, using default configuration.
             CKEDITOR.replace( 'editor1' );
         
	    })
	
	</script>
  