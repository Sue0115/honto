<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">用户管理-用户列表</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
						    <label>
							    <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('user/index/info')?>">
	                                <i class="icon-plus"></i>添加
	                            </a>
						    </label>
                            <label>
                             昵称：<input type="text"  name="nickname" value="" />
                            </label>
                             <label>
                             账号：<input type="text"  name="user_name" value="" />
                            </label>

                            <label>
                            <select name="status_id" id="status_id" >
                               <option value="">所有状态</option>
							   <?php foreach($user_status as $k => $v):?>
							   <option value="<?php echo $k;?>"><?php echo $v;?></option>
							   <?php endforeach;?>
                            </select>
                            </label>

                            <label>
                            <select name="group_id" id="group_id" >
                               <option value="">所有分组</option>
							   <?php foreach($group_list as $k => $v):?>
							   <option value="<?php echo $k;?>"><?php echo $v;?></option>
							   <?php endforeach;?>
                            </select>
                            </label>

                            <label>
                            <select name="warehouse_id" id="warehouse_id" >
                               <option value="">所属仓库</option>
							   <?php foreach($warehouse as $k => $v):?>
							   <option value="<?php echo $k;?>"><?php echo $v;?></option>
							   <?php endforeach;?>
                            </select>
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
				       <col width="15%">
				       <col width="15%">
				       <col width="15%">
				       <col width="8%">
				       <col>
                       <col width="15%">
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
                            <th>账号</th>
                            <th>昵称</th>
                            <th>用户组</th>
                            <th class="hidden-480">状态</th>
                            <th>注册时间</th>
                            <th>操作</th>
	                    </tr>
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                    <?php if($lc_list):?>
                      <?php foreach($lc_list as $item):?>
                      <tr>
                        <td class="center">
	                            <label>
	                                <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item->id?>" />
	                                <span class="lbl"></span>
	                            </label>
	                    </td>
                        <td><?php echo $item->id?></td>
                        <td><a href="<?php echo admin_base_url('user/index/info?uid=')?><?php echo $item->id?>" ><?php echo $item->user_name?></a></td>
                        <td><?php echo $item->nickname?></td>
                        <td><?php if(!$item->gid):?>超级管理员<?php else:?><?php if($item->group_name):?><?php echo $item->group_name?><?php else:?>无<?php endif;?><?php endif;?></td>
                        
                        <td class="hidden-480">
                                <label>
                                    <input type="checkbox" class="ace ace-switch ace-switch-6" name="status[]" item_id="<?php echo $item->id?>" value="<?php echo $item->status?>" <?php if($item->status):?>checked="checked"<?php endif;?> >
                                    <span class="lbl"></span>
                                </label>
	                    </td>

                        <td><?php echo datetime($item->regtime)?></td>
                        
                        <td>
	                            <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
	                                <?php if($item->status > 0):?> 
	                                <a class="green <?php if($item->status < 0):?>disabled<?php endif;?>" href="<?php echo admin_base_url('user/index/info?uid=')?><?php echo $item->id?>">
	                                    <i class="icon-pencil bigger-130"></i>
	                                </a>
	                                <!--
	                                <a class="red" href="javascript:" onclick="msgdelete(<?php echo $item->id?>)" >
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
	                                            <a href="<?php echo admin_base_url('user/index/info?uid=')?><?php echo $item->id?>" class="tooltip-success" data-rel="tooltip" title="修改">
	                                                <span class="green">
	                                                    <i class="icon-edit bigger-120"></i>
	                                                </span>
	                                            </a>
	                                        </li>
											<!--
	                                        <li>
	                                            <a href="javascript:" onclick="msgdelete(<?php echo $item->id?>)" class="tooltip-error" data-rel="tooltip" title="删除">
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
<script>
	$(function(){
	   $("#status_id").val("<?php echo $search['status_id'];?>");
	   $("#group_id").val("<?php echo $search['group_id'];?>");
	   $("#warehouse_id").val("<?php echo $search['warehouse_id'];?>");
	 });
</script>