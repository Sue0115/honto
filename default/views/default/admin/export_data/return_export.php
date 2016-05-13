<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">数据导出—退款数据</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">  
             <div class="row"> 
            	<div class="col-sm-12">
            	   <form action="<?php echo admin_base_url('export_data/return_export/deal_data')?>" method="post" id="form">
					 		ERP导入时间
	                     	<input type="text"  value="" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="start_date"/>
	                         ~
	                        <input type="text" value="" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="end_date"/>
	                        
	                      <select name="orders_type" id="orders_type" >
						 	  <option value="">订单平台</option>
		                      <?php foreach($orders_type_arr as $k => $ot):?>
		                       <option value="<?php echo $k?>"><?php echo $ot['typeName'];?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                 	  
	                 	  <select name="warehouse" id="warehouse" >
						 	  <option value="">仓库</option>
		                      <?php foreach($warehouse as $key => $w):?>
		                       <option value="<?php echo $key?>"><?php echo $w;?></option>
		                      <?php endforeach;?>
	                 	  </select>
	                      
	                 &nbsp;&nbsp;&nbsp;&nbsp;
		             <label>
						<a class="btn btn-primary btn-sm" id="export">导出销售数据</a>
					</label>
				 </form>
				</div>
			 </div>
				
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
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
	
	$("#export").click(function(){
//		var start_date  = $('#start_date').val();//开始时间
//		var end_date    = $('#end_date').val();//结束时间
//		var orders_type = $("#orders_type").val();//订单平台
//		var warehouse   = $("#warehouse").val();//仓库
//        	layer.load('导表过程中请别刷新浏览器，请耐心等候。。', 3);
//        	$.ajax( {     
//        		  url:'<?php echo admin_base_url('export_data/return_export/deal_data')?>',  
//        		  type:'post',  
//        		  data:{"start_date":start_date,"end_date":end_date,"orders_type":orders_type,"warehouse":warehouse},   
//        		  async:false,
//        		  cache:false,     
//        		  dataType:'json',     
//        		  success:function(data) {
//        			  layer.alert('数据已经导出，请点击你要下载的数据',9);
//            		  show_file_list(data);
//        		  }
//        		      
//          });
      $("#form").submit();
    	
    });
    function show_file_list(data){
    	$.layer({
			type   : 2,
			shade  : [0.4 , '' , true],
			title  : ['下载的文件',true],
			iframe : {src : '<?php echo admin_base_url("export_data/sale_data_export/show_flie_list?data=");?>'+data},
			area   : ['700px' , '350px'],
			success : function(){
				layer.shift('top', 400)
			}
		});
    	
    }
</script>