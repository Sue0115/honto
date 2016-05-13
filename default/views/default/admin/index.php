    
	        <div class="page-header">
	            <h1>
	                                                                                 控制台
	                <small>
	                    <i class="icon-double-angle-right"></i>
	                                                                                              查看
	                </small>
	                
	            </h1>
	        </div><!-- /.page-header -->
	
	        <div class="row">
	            <div class="col-xs-12">
	                <!-- PAGE CONTENT BEGINS -->
	
	                <!--[if lte IE 7]>
	                <div class="alert alert-block alert-warning">
	                    <button type="button" class="close" data-dismiss="alert">
	                        <i class="icon-remove"></i>
	                    </button>
	
	                    <i class="icon-lightbulb "></i>
	                    <strong>您使用的浏览器版本过低,请使用IE8以上浏览器 浏览本后台</strong>
	                </div>
	                <![endif]--> 
	                <!--msg -->
	                
	                <div class="alert alert-block alert-success">
	                    <button type="button" class="close" data-dismiss="alert">
	                        <i class="icon-remove"></i>
	                    </button>
	
	                    <i class="icon-ok green"></i>
	
	                                                                                            欢迎使用
	                    <strong class="green">
							<?php echo config_item('site_name')?>后台管理系统
	                        <small>(v1.0)</small>
	                    </strong>
	                    ,XXXX.    
	                </div>
	                <?php if($data['show']==1):?>
	                <div class="table-header">
	                  	今日操作
	                </div>
	                <div class="row">
	                   <table class="table  table-bordered">
										    <colgroup>
										       <col width="20%">
										       <col width="20%">
										       <col width="20%">
										       <col width="20%">
										       <col width="20%">
										    </colgroup>
							                <thead>
							                    <tr>
							                     	<th>仓库</th>
							                        <th>今日打印拣货单数</th>
						                            <th>等待包装数</th>
						                            <th>今日生成需包装订单总数</th>
						                            <th>今日已发货数</th>
							                    </tr>
							                </thead>
							                <tbody id="tbody_content">
							                 <?php foreach($warehouse as $ke => $v):?>
							                  <tr>
							                    <td><?php echo $v?></td>
							                    <td><?php echo isset($data[$ke]['print_page']) ? $data[$ke]['print_page'] : 0 ?></td>
							                    <td><?php echo isset($data[$ke]['need_pack']) ? $data[$ke]['need_pack'] : 0 ?></td>
							                    <td><?php echo isset($data[$ke]['total']) ? $data[$ke]['total'] : 0 ?></td>
							                    <td><?php echo isset($data[$ke]['has_shipped']) ? $data[$ke]['has_shipped'] : 0?></td>
							                  </tr>
							                  <?php endforeach;?>
							                </tbody>
						</table>
	                </div>
	                <?php endif;?>
	                <div class="row">
	                    <div class="space-6"></div>
	
	                    <div class="col-sm-7 infobox-container">
	
	                    </div>
	
	                    <div class="vspace-sm"></div>
	
	                    <div class="col-sm-5">
	                       
	                    </div><!-- /span -->
	                </div><!-- /row -->
	
	                <div class="hr hr32 hr-dotted"></div>
	
	                <div class="row">
	                    <div class="col-sm-5">
	                    
	                    </div>
	
	                    <div class="col-sm-7">
	                    </div>
	                </div>
	
	                <!-- PAGE CONTENT ENDS -->
	            </div><!-- /.col -->
	        </div><!-- /.row -->
	        <!--内容-->
<!--[if lte IE 8]>
<script src="<?php echo static_url('theme/ace/js/excanvas.min.js')?>"></script>
<![endif]-->
  
<script type="text/javascript">
    jQuery(function($) {
        
    })
</script>