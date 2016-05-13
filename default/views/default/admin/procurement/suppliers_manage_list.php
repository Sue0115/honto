<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">当前位置-供应商管理</h3>
        <div class="table-header">
            &nbsp;
        </div>

        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
					    
                            <label>
                            	供应商编号
	                            <input name="suppliers_id" type="text" id="suppliers_id" value=""/>
							</label>
							
		
                           <label>
                            	供应商名
	                            <input name="suppliers_company" type="text" id="suppliers_company" value=""/>
							</label>
							
							<label>
                            	联系人
	                            <input name="suppliers_name" type="text" id="suppliers_name" value=""/>
							</label>
							
							<label>
                            	 电话(手机)
	                            <input name="suppliers_contact" type="text" id="suppliers_contact" value=""/>
							</label>
							
							<label>
	                            <select id="suppliers_status" name="suppliers_status">
									<option value="">==状态==</option>
									<option value="newData">待审核</option>
									<option value="confirmModify">待复审</option>
									<option value="unPassed">不通过</option>
									<option value="currentData">已通过</option>
								</select>
							</label>
							
							<label>
	                            <select name="user_id" id="user_id">
									<option value="">==采购==</option>
									<?php foreach($procurement_user as $us){?>
									<option value="<?php echo $us['id'];?>"> <?php echo $us['name'];?></option>
									<?php }?>
								</select>
							</label>
							
							<label>
                            	 SKU
	                            <input type="text" value="" id="sku" name="sku">
							</label>
                            
                            <label>
                                <button class="btn btn-sm btn-primary" type="submit">
                                   <i class="icon-search"></i>搜索
                                </button>
                            </label>
                            
                            <label>
                            	<input type="reset" value="清空" class="btn btn-danger btn-sm">
                            </label>
                        </form>  
					</div>
				</div>
				
				<?php 
				if (!empty($list))
				{
					$status = defineSuppliersDataStatus();
				?>
				<table class="table table-striped table-bordered table-hover dataTable">
				    <colgroup>
				       <col width="5%">
				       <col width="5%">
				       <col width="10%">
                       <col width="5%">
                       <col width="10%">
                       <col width="10%">
                       <col width="20%">
                       <col width="5%">
                       <col width="10%">
                       <col width="5%">
                       <col width="5%">
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
                            <th class="center">编号</th>
                            <th class="center">供应商名称</th>
                            <th class="center">联系人</th>
                            <th class="center">联系方式</th>
                            <th class="center">开户行</th>
                            <th class="center">地址</th>
                            <th class="center">采购</th>
                            <th class="center">本月采购额</th>
                            <th class="center">交期</th>
                            <th class="center">状态</th>
                            <th class="center">操作</th>
	                    </tr>
	                    
	                </thead>
	
	                <tbody id="tbody_content">
                    
                    
                      <?php foreach($list as $k=>$v):?>
                      <tr>
	                    
	                    <td class="center">
	                            <label>
	                                <input type="checkbox" class="ace" name="ids[]" value="<?php echo $k;?>" />
	                                <span class="lbl"></span>
	                            </label>
	                    </td>
	                    
                        <td><?php echo (empty($v['suppliers_id'])) ? NULL : $v['suppliers_id']; ?></td>
                        
                        <td><?php echo (empty($v['suppliers_company'])) ? NULL : $v['suppliers_company']; ?></td>
                        
                        <td><?php echo (empty($v['suppliers_name'])) ? NULL : $v['suppliers_name']; ?></td>
                        
                        <td>
                        <?php 
                        	if (!empty($v['suppliers_phone'])) {
                        		echo '电话:'.$v['suppliers_phone'];
                        		echo '<br>';
                        	}
                        	
                        	if (!empty($v['suppliers_qq'])) {
                        		echo 'QQ:'.$v['suppliers_qq'];
                        		echo '<br>';
                        	}
                        	
                        	if (!empty($v['suppliers_wangwang'])) {
                        		echo '旺旺:'.$v['suppliers_wangwang'];
                        	}
                        ?>
                        </td>
                        
                        <td><?php echo (empty($v['suppliers_bank'])) ? NULL : $v['suppliers_bank']; ?></td>
                        
                        <td><?php echo (empty($v['suppliers_address'])) ? NULL : $v['suppliers_address']; ?></td>
                        
                        <td>
                        <?php 
                        	if (!empty($v['user_id'])) {
                        		if (!empty($procurement_user[$v['user_id']]['name'])) {
                        			echo $procurement_user[$v['user_id']]['name'];
                        		}else {
                        			echo NULL;
                        		}
                        	}else {
                        		echo NULL;
                        	}
                        ?>
                        </td>
                        
                        <td>
                        <?php 
                        	if (!empty($v['suppliers_id']))
                        	{
                        		echo $procurement_model->getPurchaseTotalWithSuppliersID($v['suppliers_id']);
                        	}else {
                        		echo 0;
                        	}
                        ?>
                        </td>
                        
                        <td><?php echo (empty($v['supplierArrivalMinDays'])) ? NULL : $v['supplierArrivalMinDays']; ?></td>
                        
                        <td><?php echo (empty($v['suppliers_status'])) ? NULL : $status[$v['suppliers_status']]['text']; ?></td>
                        
                        
                        <td>
                        	<div class="dropdown">
                        		<a id="dLabel" data-target="#" href="http://example.com" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    	操作
							    	<span class="caret"></span>
							    </a>
							    
							    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel" style="min-width:80px;">
								    <li>
								    	<a href="<?php echo site_url('admin/procurement/suppliers_manage/info'); ?>?id=<?php echo $v['suppliers_id'];?>" title="修改" >
								    		<img src="<?php echo site_url('static/theme/ace/images/edit.gif'); ?>" />
								    		修改
								    	</a>
								    </li>
								    
								    <li>
								    	<a class="product_show" href="javascript:void(0)" title="展开详情" data_id="<?php echo $v['suppliers_id'];?>">
								    		<img src="<?php echo site_url('static/theme/ace/images/explode.gif')?>" />
								    		展开详情
								    	</a>
								    </li>
								    
								    <li>
								    	<a class="log_list" href="javascript:void(0)" name="productsManage" data_id="<?php echo $v['suppliers_id'];?>" title="日志">
								    		<img src="<?php echo site_url('static/theme/ace/images/log.gif')?>" />
								    		日志
								    	</a>
								    </li>
							    </ul>
							</div>
                        </td>
                        
                        <?php 
                        if (!empty($v['products_status_2']))
                        {
                        	echo '<td>';
	                        switch ($v['products_status_2'])
	                        {
	                        	case 'selling': echo '在售';break;
	                        	case 'sellWaiting': echo '待售';break;
	                        	case 'stopping': echo '停产';break;
	                        	case 'saleOutStopping': echo '卖完下架';break;
	                        	case 'trySale': echo '试销(卖多少采多少)';break;
	                        }
	                        echo '</td>';
                        }
                        ?>
                      
                        
                      </tr>
                      <?php endforeach;?>

	                </tbody>
	            </table>
	            <a title="新建供应商" class="btn btn-sm btn-primary" href="<?php echo site_url('admin/procurement/suppliers_manage/info'); ?>">新建供应商</a>
	            <?php 
		            if($key == 'root' || $key == 'manager'){
		            	$this->load->view('admin/common/page_number');
		            
		            }else{
		            	$this->load->view('admin/common/page');
		            }
				}
	            ?>
                
            </div>
        </div>
    </div>
</div>

<script>
$(function(){

	<?php 
	if (!empty($get))
	{
		foreach ($get as $k=>$v){
			if (!empty($v))
			{
				?>
				$("#<?php echo $k; ?>").val('<?php echo $v;?>');
				<?php 
			}
		}
	}
	?>

});

$(".product_show").click(function(){
	var id = $(this).attr('data_id');
	$.layer({
	    type   : 2,
	    shade  : [0.8 , '' , true],
	    title  : ['展开详情',true],
	    iframe : {src : '/admin/procurement/suppliers_manage/show?id='+id},
	    area   : ['800px' , '500px'],
	    success : function(){
            layer.shift('top', 400);
        },
        yes    : function(index){

            layer.close(index);
            move_order();
        }
	});
})

$(".log_list").click(function(){
		var id = $(this).attr('data_id');
		$.layer({
		    type   : 2,
		    shade  : [0.8 , '' , true],
		    title  : ['操作日志',true],
		    iframe : {src : '/admin/procurement/suppliers_manage/log_list?operateMod=suppliersManage&operateKey='+id},
		    area   : ['800px' , '500px'],
		    success : function(){
                layer.shift('top', 400);
            },
            yes    : function(index){

                layer.close(index);
                move_order();
            }
		});
	})
</script>