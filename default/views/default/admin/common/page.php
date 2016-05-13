       <div class="row">
            <div class="col-sm-4">
                <select class="input-text" id="hazyid">
		            <option value="" >请选择</option>
		            <option value="1" >可用</option>
		            <option value="0" >屏蔽</option>
		            <option value="-1" >删除</option>
		            <?php if($this->user_info->key == 'root'):?>
		            <option value="100" >超级管理员删除</option>
		            <option value="101" >超级管理员恢复</option>
		            <?php endif;?>
	            </select>
	            <button type="button" class="btn btn-xs btn-danger" id="hazysubmit"><i class="icon-ok bigger-130"></i> 确定</button>
            </div>
            <div class="col-sm-8">
                <div class="dataTables_paginate paging_bootstrap">
                    <ul class="pagination">
                    <?php echo $page?>
                    </ul>
                </div>
            </div>
        </div>