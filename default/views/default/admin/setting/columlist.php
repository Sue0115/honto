<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">栏目管理-栏目列表</h3>
        <div class="table-header">
            &nbsp;
        </div>

		<!--列表 -->
		 <div class="table-responsive">
            <div class="dataTables_wrapper">   
                <div class="row">
                    <div class="col-sm-12">
                        <form method="get" action="">
                            <label>
                                <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('setting/colum/info')?>">
                                    <i class="icon-plus"></i>添加
                                </a>
                            </label>
                            <label>栏目名: 
                                <input type="text" name="title" value="<?php echo $fileter_options['title']?>" >
                            </label>
                            <label>类型: 
                                <select name="type" id="type">
                                  <option value="admin">admin</option>
                                  <option value="test">test</option>
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
			    <table id="tbody_content" class="table table-striped table-bordered table-hover dataTable">
			        <colgroup>
                       <col width="5%">
                       <col width="5%">
                       <col>
                       <col width="15%">
                       <col width="15%">
                       <col width="8%">
                       <col width="10%">
                       <col width="10%">
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
							<th style="text-align: center;">ID</th>
							<th style="text-align: left;">栏目名</th>
							<th>目录名</th>
							<th>控制器名</th>
                            <th>类型</th>
							<th>是否可用</th>
							<th>排序</th>
							<th>备注</th>
                       </tr>
				   </thead>
				   <tbody id="tbody_content">
						<?php if($lc_tree):?> 
						<?php foreach($lc_tree->getValueOptions() as $item):?>
						<tr>
							<td align="center">
                                <label>
                                    <input type="checkbox" class="ace" name="ids[]" value="<?php echo $item['cid']?>" />
                                    <span class="lbl"></span>
                                </label>
			                </td>
							<td align="center" <?php if(!$item['parents']):?>class="red"<?php endif;?>><?php echo $item['cid']?></td>
							<td style="text-align: left;">
							     <a href="<?php echo admin_base_url('setting/colum/info?cid=')?><?php echo $item['cid']?>" <?php if(!$item['parents']):?>class="red"<?php endif;?>><?php echo $item['title']?></a>
						     </td>
							<td><?php echo $item['directory']?>&nbsp;</td>
							<td><?php echo $item['con_name']?></td>
                            <td><?php echo $item['type']?></td>
							<td>
							    <label>
                                    <input type="checkbox" class="ace ace-switch ace-switch-6" name="status[]" item_id="<?php echo $item['cid']?>" value="<?php echo $item['status']?>" <?php if($item['status']):?>checked="checked"<?php endif;?> >
                                    <span class="lbl"></span>
                                </label>
							</td>
							<td>
							     <div class="order_class_id ace-spinner touch-spinner" item_id="<?php echo $item['cid']?>" field="order_id"><?php echo $item['order_id']?></div>
				            </td>
							<td>
							     <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
									 <?php if($item['status'] > 0):?> 
                                     <a class="green <?php if(!$item['status']):?>disabled<?php endif;?>" href="<?php echo admin_base_url('setting/colum/info?cid=')?><?php echo $item['cid']?>">
                                         <i class="icon-pencil bigger-130"></i>
                                     </a>
                                     <a class="red" href="javascript:" onclick="msgdelete(<?php echo $item['cid']?>)" >
                                         <i class="icon-trash bigger-130"></i>
                                     </a>
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
                                                <a href="<?php echo admin_base_url('setting/colum/info?cid=')?><?php echo $item['cid']?>" class="tooltip-success" data-rel="tooltip" title="修改">
                                                    <span class="green">
                                                        <i class="icon-edit bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>
    
                                            <li>
                                                <a href="javascript:" onclick="msgdelete(<?php echo $item['cid']?>)" class="tooltip-error" data-rel="tooltip" title="删除">
                                                    <span class="red">
                                                        <i class="icon-trash bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>
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
                <script type="text/javascript">
					$(function(){
						$("#type").val("<?php echo $fileter_options['type']?>");	
					});
				</script>
            </div>
        </div>
    </div>
</div>