<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流渠道：<?php echo $shipment_channel->channel_name?></h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
                            <label>
                             物流名称：<input type="text"  name="search[shipmentTitle]" value="<?php echo $search['shipmentTitle']?>" />
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                        </form>  
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="2%">
				      
				       <col width="8%">
                       <col width="8%">
                       <col width="15%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>ID</th>
                            <th>物流名称</th>
                            <th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                    <?php if($data_list):?>
                      <?php foreach($data_list as $item):?>
                      <tr>
                       
                        <td><?php echo $item->shipmentID?></td>
                        <td><?php echo $item->shipmentTitle?></td>
                        <td>
	                            <div class=" action-buttons">
	                                
                                    <span class="tip-<?php echo $item->id?>">
                                    	<a href="javascript::" onclick="del_to_channel('<?php echo $item->id?>','<?php echo $item->shipmentID?>','<?php echo $shipment_channel->id?>')">解除绑定</a>
                                    </span> 
                           
	                            </div>
	
	                           
	                        </td>

                      </tr>
                      <?php endforeach;?>
                    <?php endif;?>

	                </tbody>
	            </table>
	            
			    <?php  $this->load->view('admin/common/page_number');?>
                
            </div>
        </div>
    </div>
</div>
<script>
	function del_to_channel(id,shipment_id,shipment_channel_id){
		$.post("<?php echo admin_base_url('shipment/shipment_manage/ajax_shipment_delete_shipment_channel')?>",
                               { 
							   		id          : id,
									shipment_id : shipment_id,
									shipment_channel_id  : shipment_channel_id 
							   }	,
                               function(data){
                                   result = eval(data);
                                   if(result.status == 1){
                                       $(".tip-"+id).html(result.info);
                                   }else{
								   	   $(".tip-"+id).find('a').text(result.info);
								   }
                                },"json"	
        );			
	}
</script>