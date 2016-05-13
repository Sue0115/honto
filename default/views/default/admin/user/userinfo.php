<?php 
echo ace_header('用户',$item->id);

echo ace_form_open('','',array('id'=>$item->id));

	$options = array(
			'label_text'=>'账号',
			'datatype'=>'*',
			'nullmsg'=>"请输入账号！",
			'errormsg'=>"请输入账号",
			'help'=>'用户名,登录后台的账号'
	);
	echo ace_input_m($options ,'user_name',$item->user_name);
	
	if($key == 'root' || $key == 'manager'){
		$options = array('label_text'=>'密码','datatype'=>'*6-16','help'=>'用户登录密码','errormsg'=>'密码格式不正确');
		
		if($item->id){
			$options['datatype'] = '*';
			$data = array();
			$data['name']     = 'password';
			$data['type']     = 'password';
		    $data['class']    = 'width-100';
			echo ace_input($options,$data);
		}else{
			echo ace_password($options,'password');
		}		
	}

	$data = array('label_text'=>'所属用户组','help'=>'');
	echo ace_dropdown($data,'gid',$group_list,$item->gid);

	$data = array('label_text'=>'所属仓库','help'=>'');
	echo ace_dropdown($data,'warehouse_id',$warehouse,$item->warehouse_id);
	
	$options = array();
	$options = array('label_text'=>'昵称','datatype'=>'*','help'=>'姓名','errormsg'=>'请输入昵称','nullmsg'=>"请输入昵称");
	echo ace_input_m($options,'nickname',$item->nickname);
	
	$options = array();
	$options = array('label_text'=>'E-mail','datatype'=>'e','help'=>'邮箱','errormsg'=>'请输入邮箱','nullmsg'=>"请输入邮箱");
	echo ace_input_m($options,'email',$item->email);
	
	$options = array();
	$options = array('label_text'=>'老ERP账号ID','datatype'=>'n','help'=>'老ERP账号ID','errormsg'=>'请输入老ERP账号ID','nullmsg'=>"请输入老ERP账号ID,如果没有就输入0");
	echo ace_input_m($options, 'old_id', $item->old_id);
	
	if($item->id){
		$options = array('disabled'=>'disabled','name'=>'','class'=>'width-100');
		echo ace_input('注册时间',$options,datetime($item->regtime));
		echo ace_input('注册IP',$options,$item->regip);
		echo ace_input('最后登入时间',$options,datetime($item->lastlogin));
		echo ace_input('最后登入IP',$options,$item->lastip);
	}
  	echo ace_srbtn('user/index');
  echo ace_form_close()
?>
<script type="text/javascript">
    $(function(){

        $("#user_name").change(function(){
            
            user_name = $("#user_name").val();
            
            if(user_name !=""){
                $.post("<?php echo admin_base_url('user/index/ajaxUserName')?>",
                   { user_name: user_name }	,
                   function(data){
                       result = eval(data);
                       if(result.status == 1){
                           $("#user_name").val("");
                           $("#user_name").focus();
                            alert(result.info);
                       }
                    },"json"	
                );	
            } 	
        });
    });
</script>