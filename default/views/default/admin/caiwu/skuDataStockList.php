<style>
#detail{
	width:100%;
    height:auto;
}
a:hover{
	cursor:pointer;
}
.left{
	width:69%;
	float:left;
}
.address{
	border-bottom:1px dotted #ccc;
	padding-bottom:5px;
}
.buyerOperate{
	width:100%;
}
p{
	text-align:right;
}
p span{
	font-weight:bold;
}
.right{
	width:30%;
	height:auto;
	float:right;
}
table{
	border:1px solid #ccc;
}
.col-sm-4{
	display:none;
}
.col-sm-8{
	float:right;
}
</style>
<script src="<?php echo static_url('theme/common/My97DatePicker/WdatePicker.js')?>"></script>  
<div class="row">
    <div class="col-xs-12">
        <h3 class="header smaller lighter blue">财务-SKU每日实际库存</h3>
        <div class="table-header">
            &nbsp;
        </div>
        <div class="table-responsive">
            <div class="dataTables_wrapper">   
				<div class="row">
					<div class="col-sm-12">
					    <form method="get" action="">
					        
					  	    <label>
                         		    sku:<input type="text"  name="search[sku]" value="<?php echo isset($search['sku'])&&$search['sku']?$search['sku']:'';?>" size="20"/>
                            </label>
                          
                            <label>
                            
                            <label>
                            <select name="search[warehouse]" id="warehouse" >
                               <option value="">所属仓库</option>
                               <?php foreach($warehouse as $k=>$v):?>
                                <option value="<?php echo $k;?> " <?php echo (isset($search['warehouse']) && trim($search['warehouse'])==$k)? 'selected="selected"':''; ?>><?php echo $v;?></option>
                               <?php endforeach;?>
                            </select>
                            </label>
                            <label>
					   时间:<input type="text"  value="<?php echo array_key_exists('start_date', $search) ? $search['start_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="start_date" name="search[start_date]"/>
					</label>
					<label>
					  ~<input type="text" value="<?php echo array_key_exists('end_date', $search) ? $search['end_date'] : '';?>" datefmt="yyyy-MM-dd" class="Wdate" id="end_date" name="search[end_date]"/>
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
				       <col width="8%">
				       <col width="20%">
				       <col width="20%">
				       <col width="20%">
				       <col width="20%">
				    </colgroup>
	                <thead>
	                    <tr>
	                        <th>选择</th>	                   
                            <th>sku</th>
                            <th>实际库存</th>                                                      
                            <th>所属仓库</th>                                         
                            <th>创建时间</th>
	                    </tr>
	                </thead>
				<?php foreach($data_list as $k => $v):?>
	               <tbody id="tbody_content">
                     <tr>
                       <td><input type="checkbox" name="sID[]"/></td>
                       <td><?php echo $v['sku'];?></td>
                       <td><?php echo $v['stock_num'];?></td>
                       <td><?php echo $warehouse[$v['warehouse_id']];?></td>
                       <td><?php echo $v['create_time'];?></td>                       
                     </tr>
               </tbody>
	               
	            <?php endforeach;?>
	            </table>
	      <?php 
				 
					$this->load->view('admin/common/page'); 
				
		 ?>
                
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
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
</script>