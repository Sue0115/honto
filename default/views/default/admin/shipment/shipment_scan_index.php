<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue"><span class="btn-lg btn btn-success">今日已分货：<span id="now-total"><?php echo $total?></span></span></h3>
        <div id="shipment-result">
        <?php 
          if($shipment_data){
           foreach ($shipment_data as $key => $v) {
        ?>
        	<span class="btn-lg btn btn-success"><?php echo $v['shipment_id'].'-'.$v['shipment_name'].'：'.$v['num'];?><span class="clean-shipment" id-data="<?php echo $v['id']?>">(装袋清零)</span></span>
        <?php 
         }
  			}
        ?>
        </div>
        <div class="table-header">
          
	        <span>扫描挂号码：</span>
	        <input type="text" id="shipping_code"  name="shipping_code" value=""  style="width:400px;"/>
        </div>
		<div>
			<div class="red" style="font-size:100px;" id="code-result"></div>	 
        </div>
        
    </div>
</div>

<!--<script src="<?php echo static_url('theme/audiojs/audio.min.js')?>"></script>-->

<div style="display:none;">
	 <audio src=""></audio>
</div>
<script>
	$(function(){

		 //var a = audiojs.createAll();

		 //var audio= a[0];

		$.ajaxSetup({ 
    		async : false //同步请求
		}); 

		$("#shipping_code").val('');
		$("#shipping_code").focus();
		$("#shipping_code").attr('disabled',false);
		
		$("#shipping_code").change(function(){

			$("#code-result").html();
			
			var shipping_code = $("#shipping_code").val();
			
			if(shipping_code){

				 $("#shipping_code").attr('disabled',true);
				 $.post("<?php echo admin_base_url('shipment/shipment_scan/ajax_scan_for_shipping')?>",
		            {   
		            	shipping_code: shipping_code
		             }	,
		            function(data){
		            	
		                result = eval(data);
		                if(result['status'] == 1){
		                   
		                   var video_url = "<?php echo static_url('theme/shipment_video');?>"+"/"+result['shipment_id']+".mp3";
		                   //audio.load(video_url);
                           //audio.play();
		                   $("#code-result").html(result['info']);
		                   get_shipment_scan_data();
		                   	
		                }else{
		                   $("#code-result").html(result['info']);
		                }

		                $("#shipping_code").val('');
						$("#shipping_code").focus();
						$("#shipping_code").attr('disabled',false);
		                    
		            },"json"
		            );
			}
			
		});

		 $(document).on('click', '.clean-shipment', function () {
            var my_id = $(this).attr('id-data');
             $.post("<?php echo admin_base_url('shipment/shipment_scan/ajax_clean_erp_shipment_scan_temporary')?>",
		            {   
		            	id: my_id
		             }	,
		            function(data){
		            	
		                result = eval(data);
		                if(result['status'] == 1){
			               window.location.reload();
		                }else{
		                  alert(result['info']);
		                }
		                    
		            },"json"
		            );
            });

	 });

	 function get_shipment_scan_data(){

	 	$.post("<?php echo admin_base_url('shipment/shipment_scan/ajax_erp_shipment_scan_temporary_info')?>",
		            {   
		             }	,
		            function(data){
		       			
		       			result = eval(data);
		       			
		       			var str = '';
		       			
		       			$.each(data['data'],function(index,content){
		       				str +='<span class="btn-lg btn btn-success">'+content.shipment_id+'-'+content.shipment_name+'：'+content.num+'<span class="clean-shipment" id-data="'+content.id+'">(装袋清零)</span></span>';
		       			});
		       			
		                $("#shipment-result").html(str);

		                var now_total = parseInt($("#now-total").html())+1;

		                $("#now-total").html(now_total);
		                
		            },"json"
		);

	 }

</script>