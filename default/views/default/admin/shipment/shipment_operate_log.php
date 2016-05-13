<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">物流管理-物流日志</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
              <div class="row">
              
              </div>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup> 
				       <col width="8%">
                       <col width="10%">
                       <col width="10%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>操作人员</th>
                            <th>操作时间</th>
                          	<th>动作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    <?php foreach($logList as $va):?>
                      <tr>
                        <td><?php echo !empty($userArray[$va->operateUser]) ? $userArray[$va->operateUser] : '无' ?></td>
                        <td><?php echo $va->operateTime ?></td>
                        <td>
                       	   <?php echo $va->operateText  ?>
                        </td>
                      </tr>
                    <?php endforeach;?>

	                </tbody>
	            </table>
	            
			   
				 <?php  $this->load->view('admin/common/page_number');?>
				
            </div>
        </div>
    </div>
</div>