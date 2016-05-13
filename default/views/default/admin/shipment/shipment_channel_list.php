<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流管理-渠道管理</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
						    <label>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('shipment/shipment_channel/info')?>">
	                                <i class="icon-plus"></i>添加
	                            </a>
						    </label>
                            
                            <label>
                             物流供应商：
                             <select name="search[suppliers_id]" id="suppliers_id" >
                               <option value="">选择物流供应商</option>
							   <?php foreach($shipment_suppliers as $v){?>
                            	<option value="<?php echo $v->suppliers_id?>"><?php echo $v->suppliers_company?></option>
                           	   <?php }?>
                            </select>
                            </label>
                            
                            <label>
                             渠道名称：<input type="text"  name="search[channel_name]" value="<?php echo $search['channel_name']?>" />
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
				       <col width="2%">
				       <col width="15%">
				       <col width="15%">
				       <col width="5%">
                       <?php if($key == 'root'){ ?> 
				       <col width="8%">
                       <?php }?>    
				       <col width="8%">
                       <col width="8%">
                       <col width="15%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th class="center" >
	                            <label>
	                                <input type="checkbox" class="ace" />
	                                <span class="lbl"></span>
	                            </label>
	                        </th>
	                        <th>ID</th>
                            <th>所属物流供应商</th>
                            <th>渠道名称</th>
                            <th>创建者</th>
                     <?php if($key == 'root'){ ?>       
                            <th>状态</th>
                      <?php }?>      
                            <th>创建时间</th>
                            <th>修改时间</th>
                            <th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                    <?php if($data_list):?>
                      <?php foreach($data_list as $item):?>
                      <tr>
                        <td class="center">
	                            <label>
	                                <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->id?>" />
	                                <span class="lbl"></span>
	                            </label>
	                    </td>
                        <td><?php echo $item->id?></td>
                        <td><?php echo $item->suppliers_company?></td>
                        <td><a href="javascript::" class="see-bind-shipment" data_id="<?php echo $item->id?>"><?php echo $item->channel_name?></a></td>
                        <td><?php echo $item->user_name?></td>
                        
                         <?php if($key == 'root'){ ?>   
                        <td class="hidden-480">
                                <label>
                                    <input type="checkbox" class="ace ace-switch ace-switch-6" name="status[]" item_id="<?php echo $item->id?>" value="<?php echo $item->status?>" <?php if($item->status):?>checked="checked"<?php endif;?> >
                                    <span class="lbl"></span>
                                </label>
	                    </td>
                        <?php }?>
                        
                        <td><?php echo datetime($item->create_time)?></td>
                        <td><?php echo datetime($item->update_time)?></td>
          
                        <td>
	                            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
	                                <?php if($item->status > 0):?> 
                                    <button class="btn btn-success btn-xs bing_shipment" data_id="<?php echo $item->id?>">绑定物流</button>
	                                <a class="green <?php if($item->status < 0):?>disabled<?php endif;?>" href="<?php echo admin_base_url('shipment/shipment_channel/info?id=')?><?php echo $item->id?>">
	                                    <i class="icon-pencil bigger-130"></i>
	                                </a>
	                                <a class="red" href="javascript:" onclick="msgdelete(<?php echo $item->id?>)" >
	                                    <i class="icon-trash bigger-130"></i>
	                                </a>
	                                <?php else:?>
	                                <button class="btn btn-xs btn-danger disabled"><i class="icon-trash bigger-130"></i> 已删除</button>
	                                <?php endif;?>
	                            </div>
	
	                            <div class="visible-xs visible-sm hidden-md hidden-lg">
	                                <div class="inline position-relative">
	                                    <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown">
	                                        <i class="icon-caret-down icon-only bigger-120"></i>
	                                    </button>
	
	                                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">
	
	                                        <li>
	                                            <a href="<?php echo admin_base_url('shipment/shipment_channel/info?id=')?><?php echo $item->id?>" class="tooltip-success" data-rel="tooltip" title="修改">
	                                                <span class="green">
	                                                    <i class="icon-edit bigger-120"></i>
	                                                </span>
	                                            </a>
	                                        </li>
	
	                                        <li>
	                                            <a href="javascript:" onclick="msgdelete(<?php echo $item->id?>)" class="tooltip-error" data-rel="tooltip" title="删除">
	                                                <span class="red">
	                                                    <i class="icon-trash bigger-120"></i>
	                                                </span>
	                                            </a>
	                                        </li>
	                                    </ul>
	                                </div>
	                            </div>
	                        </td>

                      </tr>
                      <?php endforeach;?>
                    <?php endif;?>

	                </tbody>
	            </table>
	            
			    <?php 
				 if($key == 'root' || $key == 'manager'){
					 $this->load->view('admin/common/page_number');
						
				 }else{
					$this->load->view('admin/common/page'); 
				}
				?>
                
            </div>
        </div>
    </div>
</div>
<script>
	$(function(){
		
		var suppliers_id = '<?php echo $search['suppliers_id']?>';
		if(suppliers_id){
			$("#suppliers_id").val(suppliers_id);
		}
		
		$(".bing_shipment").click(function(){
			
			var id = $(this).attr('data_id');
			$.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['绑定物流',true],
			    iframe : {src : '/admin/shipment/shipment_manage/show_all_shipment/'+id+'?&is_ajax=1'},
			    area   : ['800px' , '700px'],
			    success : function(){
                    layer.shift('top', 400)  
                },
                yes    : function(index){

                    layer.close(index);
                    move_order();
                }
			});
		})
		
		$(".see-bind-shipment").click(function(){
			
			var id = $(this).attr('data_id');
			$.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['已绑定物流列表',true],
			    iframe : {src : '/admin/shipment/shipment_manage/see_bind_shipment/'+id+'?&is_ajax=1'},
			    area   : ['800px' , '700px'],
			    success : function(){
                    layer.shift('top', 400)  
                },
                yes    : function(index){

                    layer.close(index);
                    move_order();
                }
			});
		})
		
	 })
	
</script>