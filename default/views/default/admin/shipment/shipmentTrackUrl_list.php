<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流管理-物流网址设置</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">  
             <div class="row"> 
            	<div class="col-sm-12">
            	<form>
				 <label>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('shipment/shipmentTrackUrl/info')?>">
	                                <i class="icon-plus"></i>新建物流查询地址
	                            </a>
				 </label>
				 <label>
                          	   短名称：<input type="text"  name="search[shortName]" value="<?php echo $search['shortName']?>" id="shortName"/>
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
				       <col width="8%">
                       <col width="8%">
                       <col width="10%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>编号</th>
                            <th>短名称</th>
                            <th>网址</th>
                            <th>方法</th>
                          	<th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    <?php foreach($urlList as $va):?>
                      <tr>
                        <td><?php echo $va->track_id?></td>
                        <td><?php echo $va->track_short_name?></td>
                        <td><?php echo $va->track_url?></td>
                        <td><?php echo $va->track_method ?></td>
                        <td>
                       	    <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
	                                <a class="green" href="<?php echo admin_base_url('shipment/shipmentTrackUrl/info?id=')?><?php echo $va->track_id?> ">
	                                    <i class="icon-pencil bigger-130"></i>
	                                </a>
	                                <a class="red" href="<?php echo admin_base_url('shipment/shipmentTrackUrl/delete?id=')?><?php echo $va->track_id?>" >
	                                    <i class="icon-trash bigger-130"></i>
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