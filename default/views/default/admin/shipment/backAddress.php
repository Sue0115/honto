<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流管理-回邮地址列表</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">  
             <div class="row"> 
            	<div class="col-sm-12">
            	<form>
				 <label>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('shipment/backAddress/info')?>">
	                                <i class="icon-plus"></i>新建回邮地址
	                            </a>
				 </label>
				</form>
				</div>
			 </div>
				<table class="table table-striped table-bordered table-hover dataTable">
				   
	                <thead>
	                    <tr>
	                        <th>编号</th>
                            <th>标题</th>
                          	<th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                     <?php foreach($eubList as $v):?>
                       <tr>
                         <td><?php echo $v['id'];?></td>
                         <td><?php echo $v['eub_setting_title']?></td>
                         <td>
                            <a class="green" href="<?php echo admin_base_url('shipment/backAddress/info?id=')?><?php echo $v['id']?> ">
	                           <i class="icon-pencil bigger-130"></i>
	                        </a>
                         </td>
                       </tr>
                     <?php endforeach;?>
	                </tbody>
	            </table>
	        
            </div>
        </div>
    </div>
</div>