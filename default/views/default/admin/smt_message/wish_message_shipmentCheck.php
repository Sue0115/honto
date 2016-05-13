<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
	
		<div class="table-responsive">
			<div class="dataTables_wrapper">

				<div class="row">
				    <div class="col-sm-12">
					    <table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
							<colgroup>
								<col width="18%">
								<col width="15%"/>
								<col width="60%"/>
							</colgroup>
							<thead>
							    <tr>
								  <td colspan="3" class="text-center" style="font-weight:bold;font-color:#000;font-size:16px;">目的国家</td>
								</tr>
								<tr>
									
									<th class="center">时间</th>
									<th class="center">承运商</th>
									<th class="center">描述</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								   if(isset($result['DestTracking']['Events'])):
								   foreach($result['DestTracking']['Events'] as $dest):
								?>
								  <tr>
								    <td><?php echo date('Y-m-d H:i:s',strtotime($dest['Time']))?></td>
								    <td><?php echo $result['DestTracking']['Carrier']?></td>
								    <td><?php echo $dest['Description']?> </td>
								  </tr>
								<?php 
								  endforeach;
								  endif;
								?>
							</tbody>
						</table>
						<table class="table table-bordered table-striped table-hover dataTable">
							<colgroup>
								<col width="18%">
								<col width="15%"/>
								<col width="60%"/>
							</colgroup>
							<thead>
							    <tr>
								  <td colspan="3" class="text-center" style="font-weight:bold;font-color:#000;font-size:16px;">发件国家</td>
								</tr>
								<tr>
									
									<th class="center">时间</th>
									<th class="center">承运商</th>
									<th class="center">描述</th>
								</tr>
							</thead>
							<tbody>
								<?php 
 									if(isset($result['OriginTracking']['Events'])):
								    foreach($result['OriginTracking']['Events'] as $origan):
								?>
								  <tr>
								    <td><?php echo date('Y-m-d H:i:s',strtotime($origan['Time']))?></td>
								    <td><?php echo $result['OriginTracking']['Carrier']?></td>
								    <td><?php echo $origan['Description']?> </td>
								  </tr>
								<?php endforeach;endif;?>
							</tbody>
							
						</table>
				    </div>
				</div>
	
			</div>
		</div>
	</div>
</div>
