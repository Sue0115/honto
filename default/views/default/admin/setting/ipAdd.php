

            
<div><h2>增加IP</h2></div>
<div style="width:500px;margin:auto;">
<style>
    .ipad tr{margin-top:20px;display:block;}
    .ipad tr td:nth-child(1){width:80px;}
</style>
<table class="ipad">
        <tr>
        <td>IP地址:</td>
        <td><input type="text" id='ip'/></td>
        </tr>
        <tr>
        <td>备注信息:</td>
        <td><input type="text" id='remark'/></td>
        </tr>
        <tr>
        <td>是否可用:</td>
         <td><select name="status" id="status">
             <option value="0">否</option>
             <option value="1" selected="selected">是</option>
         </select></td>
        </tr>
        <tr>
        <td colspan=2><butto class="btn btn-success" id="add">
                增加
</butto></td>
        </tr>
</table>

</div>
<script>
$(function(){
    $('#add').click(function(){
        var ipaddress = $('#ip').val();
        var remark =$('#remark').val();
        var status = $('#status').val();
        if(!isIP(ipaddress)){alert('请输入合法的IP地址');return false;};
        if(remark == ''){alert('请输入备注信息!');return false;}
        var url ="<?php echo admin_base_url('setting/allow/ipadd')?>";
        var data = 'ip='+ipaddress+'&remark='+remark+'&status='+status;
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