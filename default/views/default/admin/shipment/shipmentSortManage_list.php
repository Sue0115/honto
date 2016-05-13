<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流管理-物流分类</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
             <div class="row">
            	<div class="col-sm-12">
            	<form method="get" action="">
				 <label>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('shipment/shipmentSortManage/info')?>">
	                                <i class="icon-plus"></i>新建物流分类
	                            </a>
				 </label>
				 <label>
                          	   分类名称：<input type="text"  name="search[shipSortName]" value="<?php echo $search['shipSortName']?>" id="shipSortName"/>
                 </label>
                 <button class="btn btn-sm btn-primary" type="submit">
                        <i class="icon-search"></i>搜索
                 </button>
                 </form>
				</div>
			  </div>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup> 
				       <col width="8%">
                       <col width="10%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>编号</th>
                            <th>物流分类名称</th>
                          	<th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    <?php foreach($shipmentSortList as $va):?>
                      <tr>
                        <td><?php echo $va->shipmentCatID?></td>
                        <td><?php echo $va->shipmentCatName?></td>
                        <td>
                       	    <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
	                                <a class="green" href="<?php echo admin_base_url('shipment/shipmentSortManage/info?id=')?><?php echo $va->shipmentCatID?> ">
	                                    <i class="icon-pencil bigger-130"></i>
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