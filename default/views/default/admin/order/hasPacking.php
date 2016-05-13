<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">仓库操作-已包装的拣货单列表</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
						    
                            <label>
                             单号：<input type="text"  name="search[pick_id]" value="<?php echo $search['pick_id']?>" size="20"/>
                            </label>
                            <label>
                             类型：
                            <select name="search[pick_type]" id="pick_type" >
                               <option value="">选择类型</option>
							   <?php foreach($type_text as $k => $v):?>
							   <option value="<?php echo $k;?>"><?php echo $v;?></option>
							   <?php endforeach;?>
                            </select>
                            </label>
                            <label>
                            状态：
                            <select name="search[pick_status]" id="pick_status" >
                               <option value="">选择状态</option>
							   <?php foreach($status_text as $key => $va):?>
							   <option value="<?php echo $key;?>"><?php echo $va;?></option>
							   <?php endforeach;?>
                            </select>
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
				       <col width="10%">
				       <col width="15%">
				       <col width="15%">
				       <col width="5%">                
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
	                        <th>拣货单单号</th>	                   
                            <th>类型</th>
                            <th>订单数</th>
                            <th>SKU数</th>
                            <th>商品数</th>                            
                            <th>状态</th>             
                            <th>开始时间</th>
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
                        <td><?php echo $type_text[$item->type]?></td>
                        <td><?php echo $item->order_num?></td>
                        <td><?php echo $item->sku_num?></td>     
                        <td><?php echo $item->num?></td>
                        <td><?php echo $status_text[$item->status]?></td>
                        <td><?php echo datetime($item->pick_start_time)?></td>
                        <td>
	                            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
	                       
	                                <a href="<?php echo admin_base_url('order/pick_manage/pickView?pick_id=')?><?php echo $item->id?>">
                                    	<button class="btn btn-success btn-xs" data_id="<?php echo $item->id?>">查看拣货单</button>
	                                </a>

	                            </div>
	
	                            <div class="visible-xs visible-sm hidden-md hidden-lg">
	                                <div class="inline position-relative">
	                                    <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown">
	                                        <i class="icon-caret-down icon-only bigger-120"></i>
	                                    </button>
	
	                                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">
  
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
  $("#pick_type").val("<?php echo $search['pick_type'];?>");
  $("#pick_status").val("<?php echo $search['pick_status'];?>");
})
</script>