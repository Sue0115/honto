<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">用户管理-管理组</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
						    <label>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('user/group/info')?>">
	                                <i class="icon-plus"></i>添加
	                            </a>
						    </label>
                            <label>
                            组名：<input type="text" class="input-text" name="title" value="" />
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
				       <col width="3%">
				       <col width="3%">
				       <col width="25%">
				  
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
                            <th>用户组名</th>
                           
                            <th class="hidden-480">是否可用</th>
                            <th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                     <?php if($lc_list):?>
                          <?php foreach($lc_list as $item):?>
                          <tr>
                            <td class="center">
	                            <label>
	                                <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->gid?>" />
	                                <span class="lbl"></span>
	                            </label>
	                        </td>
                            <td><?php echo $item->gid?></td>
                            <td><a href="<?php echo admin_base_url('user/group/info?gid=')?><?php echo $item->gid?>" ><?php echo $item->title?></a></td>
                           
                            <td class="hidden-480">
                                <label>
                                    <input type="checkbox" class="ace ace-switch ace-switch-6" name="status[]" item_id="<?php echo $item->gid?>" value="<?php echo $item->status?>" <?php if($item->status):?>checked="checked"<?php endif;?> >
                                    <span class="lbl"></span>
                                </label>
	                        </td>
                            
                            <td>
	                            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
	                                <?php if($item->status > 0):?> 
	                                <a class="green <?php if($item->status < 0):?>disabled<?php endif;?>" href="<?php echo admin_base_url('user/group/info?gid=')?><?php echo $item->gid?>">
	                                    <i class="icon-pencil bigger-130"></i>
	                                </a>
	                                <!--
	                                <a class="red" href="javascript:" onclick="msgdelete(<?php echo $item->gid?>)" >
	                                    <i class="icon-trash bigger-130"></i>
	                                </a>
	                            	-->
	                                <?php else:?>
	                                <button class="btn btn-xs btn-danger disabled"><i class="icon-trash bigger-130"></i> 已删除</button>
	                                <?php endif;?>
	                            </div>
	
	                            <div class="visible-xs visible-sm hidden-md hidden-lg">
	                                <div class="inline position-relative">
	                                    <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown">
	                                        <i class="icon-caret-down icon-only bigger-120"></i>
	                                    </button>
	
	                                    <ul class="dropdown-menu dropdown-only-icon dropdown-yellow pull-right dropdown-caret dropdown-close">
	
	                                        <li>
	                                            <a href="<?php echo admin_base_url('user/group/info?gid=')?><?php echo $item->gid?>" class="tooltip-success" data-rel="tooltip" title="修改">
	                                                <span class="green">
	                                                    <i class="icon-edit bigger-120"></i>
	                                                </span>
	                                            </a>
	                                        </li>
											<!--
	                                        <li>
	                                            <a href="javascript:" onclick="msgdelete(<?php echo $item->gid?>)" class="tooltip-error" data-rel="tooltip" title="删除">
	                                                <span class="red">
	                                                    <i class="icon-trash bigger-120"></i>
	                                                </span>
	                                            </a>
	                                        </li>
	                                    	-->
	                                    </ul>
	                                </div>
	                            </div>
	                        </td>
                            
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