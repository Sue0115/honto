<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流管理-物流方式</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
						    <label>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('shipment/shipmentManage/info')?>">
	                                <i class="icon-plus"></i>添加物流
	                            </a>
	                            <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('shipment/shipmentTestBox')?>">
	                                <i class="icon"></i>测试匹配
	                            </a>
						    </label>
                            <label>
                             物流编号：<input type="text"  name="search[shipmentID]" value="<?php echo $search['shipmentID']?>" id="shipmentID"/>
                            </label>
                            <label>
                             所在仓库：
                             <select name="search[warehouse]" id="warehouse" >
                               <option value="">选择仓库</option>
							   <?php foreach($warehouse as $key => $house):?>
							    <option value="<?php echo $key?>"><?php echo $house?></option>
							   <?php endforeach;?>
                            </select>
                            </label>
                             <label>
                             物流分类：
                             <select name="search[shipmentCategoryID]" id="shipmentCategoryID" >
                               <option value="">选择物流分类</option>
							   <?php foreach($shipmentCArr as $v):?>
							   <option value="<?php echo $v->shipmentCatID;?>"><?php echo $v->shipmentCatName;?></option>
							   <?php endforeach;?>
                            </select>
                            </label>
                            <label>
                             物流名称：<input type="text"  name="search[shipmentTitle]" value="<?php echo $search['shipmentTitle']?>" id="shipmentTitle"/>
                            </label>
                            <label>
                             <select name="search[shipmentEnable]" id="shipmentEnable" >
                               <option value="">状态</option>
							   <option value="1">启用</option>
							   <option value="0">停用</option>
                            </select>
                            </label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            
                        </form>  
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="3%">
				       <col width="3%">
				       <col width="15%">
				       <col width="20%">
				       <col width="5%">
				       <col width="8%">
				       <col width="8%">
                       <col width="8%">
                       <col width="10%">
                       <col width="10%">
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
                            <th>物流方式</th>
                            <th>查询网址</th>
                            <th>所属仓库</th>
                          	<th>计算依据</th>
                            <th>计算范围</th>
                            <th>状态</th>
                            <th>优先级</th>
                            <th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    <?php foreach($data_list as $va):?>
                      <tr>
                        <td class="center">
	                            <label>
	                                <input type="checkbox" class="ace" name="ids[]" value="<?php echo $va->shipmentID?>" />
	                                <span class="lbl"></span>
	                            </label>
	                    </td>
                        <td><?php echo $va->shipmentID?></td>
                        <td><?php echo $va->shipmentTitle?></td>
                        <td><?php echo $va->shipmentDescription?></td>
                        <td><?php echo $warehouse[$va->shipment_warehouse_id] ?></td>
                        <td><?php echo $va->shipmentCalculateMethod?></td>
                        <td><?php echo $va->shipmentElementMin?>~<?php echo $va->shipmentElementMax?></td>
                        <td><?php echo $shipment_status[$va->shipmentEnable]?></td>
                        <td><?php echo $va->shipmentRate?></td>
                        <td>
                       	    <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                       	    
                                    <a onclick="if(!confirm('确定要复制当前物流方式以创建新物流方式吗？')){return false;}" href="<?php echo admin_base_url('shipment/shipmentManage/copyShipment?id=')?><?php echo $va->shipmentID;?>">
                                     <img src="<?php echo site_url('attachments/images/shipment/copy.gif')?>" />
                                    </a>
                                    
	                                <a class="green" href="<?php echo admin_base_url('shipment/shipmentManage/info?id=')?><?php echo $va->shipmentID?>">
	                                    <i class="icon-pencil bigger-130"></i>
	                                </a>
	                                
	                              <a class="bing_shipment_log" data_id="<?php echo $va->shipmentID?>">
	                                <img src="<?php echo site_url('attachments/images/shipment/log.gif')?>" />
	                              </a>

	                            </div>
                        </td>
                      </tr>
                    <?php endforeach;?>

	                </tbody>
	            </table>
	            
			    <?php 
				 $this->load->view('admin/common/page'); 
				?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$(function(){
		$("#shipmentCategoryID").val("<?php echo $search['shipmentCategoryID']?>");
		$("#shipmentEnable").val("<?php echo $search['shipmentEnable']?>");
		$("#warehouse").val("<?php echo $search['warehouse']?>");
		
		$(".bing_shipment_log").click(function(){
			var id = $(this).attr('data_id');
			$.layer({
			    type   : 2,
			    shade  : [0.8 , '' , true],
			    title  : ['操作日志',true],
			    iframe : {src : '/admin/shipment/shipmentManage/operate_log?operateMod=shipmentManage&id='+id+'?&is_ajax=1'},
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
	});
	
</script>
