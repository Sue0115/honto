<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">供应商管理：<?php echo $title; ?></h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">
					
					<table class="table table-bordered">
							<tr>
								<th width="100">操作人员</th>
								<th width="120">操作时间</th>
								<th width="400">动作</th>
							</tr>
							<?php 
							if (!empty($logList))
							{
							?>
							<?php foreach($logList as $log){?>
							<tr>
								<td align="center"><?php echo $manages_model->getmanagefields('name',$log['operateUser']);?></td>
								<td align="center"><?php echo $log['operateTime'];?></td>
								<td><?php echo $log['operateText'];?></td>
							</tr>
							<?php }}?>
					</table>
            </div>
        </div>
        
    </div>
</div>



