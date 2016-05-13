<?php 
    echo ace_header('密码',true);

    echo ace_form_open();

    $options = array('label_text'=>'旧密码','datatype'=>'*6-16','help'=>'旧密码','errormsg'=>'旧密码格式不正确');
    echo ace_password($options,'old_password');
    
    $options = array('label_text'=>'新密码','datatype'=>'*6-16','help'=>'只允许字母、数字、下划线以及6到16个字符','errormsg'=>'新密码格式不正确');
    echo ace_password($options,'password');
    
    echo ace_srbtn('',false);
    
    echo ace_form_close();
?>