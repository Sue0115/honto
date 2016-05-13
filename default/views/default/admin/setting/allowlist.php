<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">IP管理-允许访问IP列表</h3>
        <div class="table-header">
            &nbsp;
        </div>

		<!--列表 -->
		 <div class="table-responsive">
            <div class="dataTables_wrapper">   
                <div class="row">
                    <div class="col-sm-12">
                        <div style="width:80px;float:left;">
                        <?php if($qx==1):?>
                            <label>
                                <a class="btn btn-sm btn-primary" href="<?php echo admin_base_url('setting/allow/ipAdd')?>">
                                    <i class="icon-plus"></i>添加
                                </a>
                            </label>
                         <?php endif;?>   
                        </div>
                        <div style="width:500px;float:right;">
                             <label>IP地址: 
                                <input type="text" id="searip" value="<?php echo $searip;?>" >
                            </label>
                            <label>是否可用: 
                                <select id="searstatus" id="type">
                                <option value="2" <?php if($searstatus =='2'){ echo "selected='selected'";}?>>全部</option>
                                  <option value="1" <?php if($searstatus =='1'){ echo "selected='selected'";}?>>是</option>
                                  <option value="0" <?php if($searstatus ==='0'){ echo "selected='selected'";}?>>否</option>
                                </select>
                            </label>
                            <label>
                                <button class="btn btn-sm btn-primary" id="search">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                        </div>
                    </div>
                </div>
			    <table id="tbody_content" class="table table-striped table-bordered table-hover dataTable" >
			        <colgroup>
                       <col width="40">
                       <col width="100">
                       <col >
                       <col width="170">
                       <col width="100">
                       <col width="100">
                    </colgroup>
				    <thead>
					    <tr>
     
							<th style="text-align: center;">ID</th>
							<th style="text-align: left;">IP地址</th>
                            <th style="text-align: left;">备注信息</th>
                            <th style="text-align: left;">最后使用时间</th>
							
                            <th >是否可用</th>
                            
							<th>操作</th>
                       </tr>
				   </thead>
				   <tbody id="tbody_content">
						<?php if($list):?> 
						<?php foreach($list as $key=>$item):?>
						<tr>
                            <td><?php echo $key+1 ;?>&nbsp;</td>
							<td><?php echo $item['ip']?>&nbsp;</td>
                            <td><?php echo $item['remark']?>&nbsp;</td>
                            <td><?php echo date('Y-m-d H:i:s',$item['last_time']);?></td>
        
							<td>
							    <label>
                                    <input type="checkbox" class="ace ace-switch ace-switch-6 staldb"  item_id="<?php echo $item['id']?>" value="<?php echo $item['status']?>" <?php if($item['status']):?>checked="checked"<?php endif;?> >
                                    <span class="lbl"></span>
                                </label>
							</td>
                  
							<td >
							     <div class="" style="display: block">
									
                                     <a class="green <?php if(!$item['status']):?>disabled<?php endif;?>" href="<?php echo admin_base_url('setting/allow/ipModify?id=')?><?php echo $item['id']?>">
                                         <i class="icon-pencil bigger-130"></i>
                                     </a>
                                     <a class="red del" href="javascript:"  id="<?php echo $item['id']?>">
                                         <i class="icon-trash bigger-130"></i>
                                     </a>
                                    
                                 </div>
                                 
                                
							</td>
						</tr>
					    <?php endforeach;?> 
					    <?php endif;?>
					</tbody>
				</table>

                <script type="text/javascript">
					$(function(){
                        $('#search').click(function(){
                            var searip = $('#searip').val();
                            var searstatus = $('#searstatus').val();
                            if(searip !=''){
                                if(!isIP(searip)){
                                alert('请输入合法的IP地址');
                                return false;
                                 };
                            }
                            
                            var url = "<?php echo admin_base_url('setting/allow/')?>"+'?searip='+searip+'&searstatus='+searstatus;
                           
                            window.location.href=url;
                        });
                        $('.del').click(function(){
                            if(!confirm('确定删除吗?')){
                                return false;
                            }
                            var id = $(this).attr('id');
                            var url ="<?php echo admin_base_url('setting/allow/ipdel')?>";
                            var data = 'id='+id;
                            $.ajax({
                                url:url,
                                data:data,
                                type:'post',
                                dataType:'text',
                                success:function(msg){
                                    var sta = eval("("+msg+")");
                                        if(sta.status==1){
                                            alert("删除成功");
                                            location.reload();
                                        }else{
                                            alert(sta.info);
                                        }
                                },
                                error:function(){
                                    alert('添加失败');
                                }

                            });
                        });
						$('#add').click(function(){
                            var ipaddress = $('#ipaddress').val();
                            if(!isIP(ipaddress)){alert('请输入合法的IP地址');return false;};
                            var url ="<?php echo admin_base_url('setting/allow/ipadd')?>";
                            var data = 'ip='+ipaddress;
                            $.ajax({
                                url:url,
                                data:data,
                                type:'post',
                                dataType:'text',
                                success:function(msg){
                                    var sta = eval("("+msg+")");
                                        if(sta.status==1){
                                            alert("添加成功");
                                            location.reload();
                                        }else{
                                            alert(sta.info);
                                        }
                                },
                                error:function(){
                                    alert('添加失败');
                                }

                            });
                        });
					});
                    function isIP(strIP) {
                        var re=/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/g //匹配IP地址的正则表达式
                        if(re.test(strIP)){
                                if( RegExp.$1 <256 && RegExp.$2<256 && RegExp.$3<256 && RegExp.$4<256) return true;
                           }return false;
                        }     
        		</script>
            </div>
        </div>
    </div>
</div>