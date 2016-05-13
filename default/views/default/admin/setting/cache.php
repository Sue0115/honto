<div class="page-header">
    <h1>
                                        站点设置
        <small>
            <i class="icon-double-angle-right"></i> 清理缓存
        </small>
    </h1>
</div>
<div class="row">
    <div class="col-xs-12">
        <form method="post" action="" enctype="multipart/form-data" id="form_submit" class="registerform" ajaxpost="ajaxpost">
            <div class="clearfix form-actions">
                <div class="col-md-offset-3 col-md-9">
                    <button class="btn btn-info" type="submit" onClick="dbsubmit()">
                        <i class="icon-ok bigger-110"></i> 马上清理
                    </button>
                </div>
            </div>
        </form> 
    </div>
</div>
<script type="text/javascript">
function dbsubmit(){
	showtips("正在清理中请稍后......")	
}
</script>

