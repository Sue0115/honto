<style>
a{
	text-decoration:none;
	color:#000;
}
</style>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
	<div class="col-xs-12">
		<h3 class="header small lighter blue">WISH-邮件列表</h3>
		<div class="table-header">&nbsp;</div>

		<div class="table-responsive">
			<div class="dataTables_wrapper">
				<div class="row">
				  <form action="" method="get">
				     <label>
				            账号:
				       <select name="search[account]" id="account">
				         <option value="">请选择账号</option>
				         <?php foreach($account as $a):?>
				           <option value="<?php echo $a['account_name']?>">
				            <?php echo $a['account_name']?>  
				           </option>
				         <?php endforeach;?>
				         
				       </select>
				     </label>
				 	 <label>
						交易号:
						<input type="text" name="search[transactionID]" placeholder="请输入交易号" value="<?php echo array_key_exists('transactionID', $search) ? $search['transactionID'] : '';?>">
					 </label>
					
					<label>
					   发件时间:<input type="text"  value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="search[start_date]"/>
					</label>
					<label>
					  ~<input type="text" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="search[end_date]"/>
					</label>
					
					<br/>
					message状态：
					<input type="radio" name="search[isRead]" value="1" <?php  echo array_key_exists('isRead', $search)&&$search['isRead']==1  ? 'checked' : '';?>/>未读
					&nbsp;&nbsp;
					<input type="radio" name="search[isRead]" value="2" <?php  echo array_key_exists('isRead', $search)&&$search['isRead']==2  ? 'checked' : '';?>/>已读
					&nbsp;&nbsp;
					<input type="radio" name="search[isReturn]" value="1" <?php  echo array_key_exists('isReturn', $search)&&$search['isReturn']==1  ? 'checked' : '';?>/>未回复
					&nbsp;&nbsp;
					<input type="radio" name="search[isReturn]" value="2" <?php  echo array_key_exists('isReturn', $search)&&$search['isReturn']==2  ? 'checked' : '';?>/>已回复
					&nbsp;&nbsp;
					<input type="radio" name="search[isReturn]" value="3" <?php  echo array_key_exists('isReturn', $search)&&$search['isReturn']==3  ? 'checked' : '';?>/>不必回
					&nbsp;&nbsp;
					<input type="radio" name="search[isReturn]" value="4" <?php  echo array_key_exists('isReturn', $search)&&$search['isReturn']==4  ? 'checked' : '';?>/>已关闭
					
					<label>
						<button class="btn btn-primary btn-sm" type="submit">筛选</button>
					</label>
					<label>
						<a class="btn btn-primary btn-sm" href="<?php echo admin_base_url('smt_message/wish_message_center/message_center');?>">清空</a>
					</label>
					<label>
						<button class="btn btn-primary btn-sm" id="BatchOpen">批量打开</button>
					</label>
				  </form>
				   <div style="color:green;font-weight:bold;">
					     状态说明：
					   <span class="glyphicon glyphicon-eye-close"></span>未读
					   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					   <span class="glyphicon glyphicon-eye-open"></span>已读
					   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					   <span class="glyphicon glyphicon-ok"></span>已回
					   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					   <span class="glyphicon glyphicon-remove"></span>不必回
					   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					   <span class="glyphicon glyphicon-remove-circle"></span>已关闭
					   
				   </div>
				</div>
				
			   <form action="" method="post" id="dataArea">
			   <input type="hidden" name="pageData" value="" />
				<table class="table table-bordered table-striped table-hover dataTable" id="tbody_content">
					<colgroup>
						<col width="3%">
						<col width="11%"/>
						<col width="11%"/>
						<col width="11%"/>
						<col width="13%">
						<col width="30%">
						<col width="9%">
						<col width="7%">
					</colgroup>
					<thead>
						<tr>
							<th class="center">
								<label>
                                    <input type="checkbox" class="ace" />
                                    <span class="lbl"></span>
                                </label>
							</th>
							<th class="center">发件人</th>
							<th class="center">收件人</th>
							<th class="center">邮件ID</th>
							<th class="center">交易号</th>
							<th class="center">主题</th>
							<th class="center">发件时间</th>
							<th class="center">状态</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($data as $item):
						  $senderInfo = unserialize($item->userInfo);//获取发件人信息
						  $style="color:#000;font-weight:normal;";
						  if($item->isRead==1 && $item->isReturn==1){
						  	 $style="font-weight:bold;color:#000;";
						  }
						  if($item->isReturn==2){
							$style="color:green;";
						  }
						 
						?>
						  <tr style="<?php echo $style;?>">
						    <td class="center">
						      <label>
									<input type="checkbox" class="ace" name="mailIds[]" value="<?php echo $item->id;?>" >
									<span class="lbl"></span>
							  </label>
						    </td>
						    <td><?php echo $senderInfo['name']?></td>
						    <td><?php echo $item->account?></td>
						    <td><?php echo $item->mailID?></td>
						    <td><?php echo $item->transactionID?></td>
						    <td>
							      <a <?php echo $item->isReturn==2 ? 'style="color:green"' : ''?> href="<?php echo admin_base_url('smt_message/wish_message_center/show_detail?id=')?><?php echo $item->id;?>" target="_blank">
							       <?php echo $item->label?>
							      </a>
						    </td>
						    <td><?php echo $item->last_update_date?></td>
						    <td>
						      <span title="<?php echo $item->isRead==1 ? '未读' : '已读';?>" class="glyphicon glyphicon-eye-<?php echo $item->isRead==1 ? 'close' : 'open';?>">
						      <?php 
						        if($item->isReturn==2){
						        	echo '<span title="已回" class="glyphicon glyphicon-ok"></span>';
						        }elseif($item->isReturn==3){
						        	echo '<span title="不必回" class="glyphicon glyphicon-remove"></span>';
						        }elseif($item->isReturn==4){
						        	echo '<span title="不必回" class="glyphicon glyphicon-remove-circle"></span>';
						        }
						      ?>
						    </td>
						   
						  </tr>
						<?php
						endforeach;
						?>
					</tbody>
				</table>
			   </form>
				<?php
				
					$this->load->view('admin/common/page');
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$("#account").val("<?php echo $search['account']?>");
$(function(){
	
	$(document).on('click','.Wdate',function(){ 
		var o = $(this); 
		if(o.attr('dateFmt') != '') 
		WdatePicker({dateFmt:o.attr('dateFmt')}); 
		else if(o.hasClass('month')) 
		WdatePicker({dateFmt:'yyyy-MM'}); 
		else if(o.hasClass('year')) 
		WdatePicker({dateFmt:'yyyy'}); 
		else 
		WdatePicker({dateFmt:'yyyy-MM-dd'}); 
	}); 
	//批量打开信件
	$("#BatchOpen").click(function(){

    	var mailIds = $('input[name="mailIds[]"]:checked').map(function() {
			return $(this).val();
		}).get().join(',');
		if (mailIds == ''){
			alert('请先勾选要打开的信件');
			return false;
		}
		var mails= mailIds.split(',');
		$.each(mails, function(i, value) {
			window.open('show_detail?id='+value);
		});
	
        return false;
    });

})

</script>