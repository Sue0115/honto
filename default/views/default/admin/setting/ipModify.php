

<div><h2>修改IP-<a href="<?php echo admin_base_url('setting/allow/')?>">返回</a></h2></div>
<div style="width:500px;margin:auto;">
<style>
    .ipad tr{margin-top:20px;display:block;}
    .ipad tr td:nth-child(1){width:80px;}
</style>
<table class="ipad">
        <tr>
        <td>IP地址:</td>
        <td><input type="text" id='ip' value="<?php echo $ipdata->ip;?>"/></td>
        </tr>
        <tr>
        <td>备注信息:</td>
        <td><input type="text" id='remark' value="<?php echo $ipdata->remark;?>"/></td>
        </tr>
        <tr>
        <td>是否可用:</td>
         <td><select name="status" id="status">
             <option value="0" <?php if($ipdata->status == 0){echo "selected='selected'";}?>>否</option>
             <option value="1" <?php if($ipdata->status == 1){echo "selected='selected'";}?>>是</option>
         </select></td>
        </tr>
        <tr>
        <td colspan=2><butto class="btn btn-success" id="modify">
                确定
</butto></td>
        </tr>
</table>
</div>
<script>
$(function(){
    $('#modify').click(function(){
        var ipaddress = $('#ip').val();
        var remark =$('#remark').val();
        var status = $('#status').val();
        var id = "<?php echo $ipdata->id;?>";
        if(!isIP(ipaddress)){alert('请输入合法的IP地址');return false;};
        var url ="<?php echo admin_base_url('setting/allow/ipModify')?>";
        var data = 'ip='+ipaddress+'&remark='+remark+'&status='+status+'&id='+id;
        $.ajax({
            url:url,
            data:data,
            type:'post',
            dataType:'text',
            success:function(msg){
                var sta = eval("("+msg+")");
                    if(sta.status==1){
                        alert("修改成功");
                        location.reload();
                    }else{
                        alert(sta.info);
                    }
            },
            error:function(){
                alert('修改失败');
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