<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">仓库操作-拣货单列表<span class="btn-lg btn btn-success">今日打印拣货单：<?php echo $data['print_page']?>张</span><span class="btn-lg btn btn-success">等待包装：<?php echo $data['need_pack']?></span> <span class="btn-lg btn btn-success">今日生成需包装订单总数：<?php echo $data['total']?></span> <span class="btn-lg btn btn-success">今日已发货：<?php echo $data['has_shipped']?></span></h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
						    <label>
						    	<?php if($key == 'manager'){?>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url($c_url.'/info')?>">
	                                <i class="icon-plus"></i>生成拣货单
	                            </a>
	                            <?php }?>
						    </label>
                            
                            <label>
                             物流：
                            <select name="search[shipmentID]" id="shipmentID" >
                               <option value="">选择物流</option>
							   <?php foreach($shipmentList as $k => $v):?>
							   <option value="<?php echo $k;?>"><?php echo $v;?></option>
							   <?php endforeach;?>
                            </select>
                            </label>
                            
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
                            仓库：
                            <select name="search[pick_warehouse]" id="pick_warehouse" >
                               <option value="">类型</option>
							   <?php foreach($warehouse as $k=>$v):?>
							   <option value="<?php echo $k;?>"><?php echo $v;?></option>
							   <?php endforeach;?>
                            </select>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>&nbsp;&nbsp;&nbsp;
							<label><a class="btn btn-primary btn-sm" id="over" onclick="over()">已标记发货</a></label>
                        </form>  
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="2%">
				       <col width="10%">
				       <col width="8%">
				       <col width="6%">
				       <col width="5%">                
				       <col width="8%">                        
				       <col width="10%">
                       <col width="10%">
                       <col width="8%">
                       <col width="7%">
                       <col width="12%">
                       <col width="17%">
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
                            <th>创建时间</th>
                            <th>创建人</th>
							<th>成功发货订单数</th>
                            <th>成功发货件数</th>
                            <th>物流渠道</th>
                            <th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                    <?php if($data_list):?>
                      <?php foreach($data_list as $item):?>
                      <tr>
                        <td class="center">
	                            <label>
	                                <input type="checkbox" class="ace" name="ids" value="<?php echo $item->id?>" />
	                                <span class="lbl"></span>
	                            </label>
	                    </td>
                        <td><?php echo $item->id?></td>
                        <td><?php echo $type_text[$item->type]?></td>
                        <td><?php echo $item->order_num?></td>
                        <td><?php echo $item->sku_num?></td>     
                        <td><?php echo $item->num?></td>
                        <td><?php echo $status_text[$item->status]?></td>
                        <td><?php echo datetime($item->create_time)?></td>
                        <td><?php echo $item->nickname?></td>
						<td><?php echo $item->total?></td>
          				<td><?php echo $item->count?></td>
          				<td><?php echo (strpos($item->shipment_id,',')) ?'混合渠道' :$shipmentList[$item->shipment_id];?></td>
                        <td>
	                            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
	                                
	                                <a href="<?php echo admin_base_url('order/pick_manage/printPickOrder?pick_id=')?><?php echo $item->id?>" target="_blank">
                                    	<button class="btn btn-success btn-xs" data_id="<?php echo $item->id?>">打印拣货单</button>
	                                </a>
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
				 if($key == 'root'){
					 $this->load->view('admin/common/page');
						
				 }else{
					$this->load->view('admin/common/page_number'); 
				}
				?>
                
            </div>
        </div>
    </div>
</div>
<script>
	$(function(){
	   $("#shipmentID").val("<?php echo $search['shipmentID'];?>");
	   $("#pick_type").val("<?php echo $search['pick_type'];?>");
	   $("#pick_status").val("<?php echo $search['pick_status'];?>");
	   $("#pick_warehouse").val("<?php echo $search['pick_warehouse']?>");
	 })
	function over(){
		if(confirm("确认将勾选的拣货单状态改为已标记发货？")){
			var obj=document.getElementsByName('ids'); 
			var s='';
			for(var i=0; i<obj.length; i++){
				if(obj[i].checked) s+="'"+obj[i].value+"'"+',';   //pick_id拼接
			}
			if(s){   //当s有值的时候能执行操作
			  location.href="<?php echo admin_base_url('order/pick_manage/pickcheck?pick_id=');?>"+s+"";
		  }else{
			  alert("请选择要操作的数据！");
			  return false;
		  }
		}else{
			return false;
		}
	}
</script>