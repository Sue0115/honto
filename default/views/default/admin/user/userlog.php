<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">用户管理-登入详细</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="<?php echo admin_base_url('user/userlog')?>">
						    
                            <label>
                            用户名：<input type="text" class="input-text" name="loginname" value="" />
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
				       <col width="5%">
				       <col width="10%">
				       <col width="15%">
				       <col width="15%">
				       <col width="15%">
				    </colgroup>
	                <thead>
	                    <tr>
	                      	<th>用户账号</th>
                            <th>登入时间</th>
                            <th>退出时间</th>
                            <th>登入IP</th>
                            <th>状态</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    <?php if($lc_list):?>
                      <?php foreach($lc_list as $item):?>
                      <tr>
                        <td><?php echo $item->loginname?></td>
                        <td><?php echo datetime($item->logintime)?> </td>
                        <td><?php if($item->logouttime):?><?php echo datetime($item->logouttime)?><?php else:?><?php if($item->status == 1):?>强制退出<?php else:?>未登入<?php endif;?><?php endif;?></td>
                        <td><?php echo $item->loginip?></td>
                        <td><?php if($item->status == 1):?>成功<?php else:?>密码错误<?php endif;?></td>
                      </tr>
                      <?php endforeach;?>
                    <?php endif;?>

	                </tbody>
	            </table>
	            
			    <?php $this->load->view('admin/common/page')?>
            </div>
        </div>
    </div>
</div>