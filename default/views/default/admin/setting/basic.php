<div class="page-header">
    <h1>
                                        站点设置
        <small>
            <i class="icon-double-angle-right"></i> 全局设置
        </small>
    </h1>
</div>
<div class="tabbable">
    <ul class="nav nav-tabs padding-18">
        <li class="active">
            <a data-toggle="tab" href="#basic_set">
                <i class="green icon-cog bigger-120"></i> 基本设置
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#upload_set">
                <i class="orange icon-cloud-upload bigger-120"></i> 上传设置
            </a>
        </li>

    </ul>
    
    <?php 
        echo ace_form_open();
    ?>
    <div class="tab-content no-border padding-24">
        <div id="basic_set" class="tab-pane active">
        <input name="id" type="hidden" value="" />
            <?php 
                echo ace_input(array('label_text'=>'网站标题','help'=>'站点名称，将显示在浏览器窗口标题等位置'),'site_name',$setting->site_name);

                echo ace_input(array('label_text'=>'管理员邮箱','help'=>'管理员 E-mail，将作为系统发邮件的时候的发件人地址'),'site_admin_email',$setting->site_admin_email);

                echo ace_input(array('label_text'=>'管理员QQ','help'=>'管理员QQ，作为网站登入和联系管理员QQ'),'site_admin_qq',$setting->site_admin_qq);

                echo ace_input(array('label_text'=>'系统错误信息','help'=>'当系统出错时的显示信息'),'site_error_message',$setting->site_error_message);

                echo ace_input(array('label_text'=>'字符串加密','help'=>'字符串加密,默认为123456,在启用程序时请修改,之后请劳记'),'site_auth_key',$setting->site_auth_key);

                echo ace_input(array('label_text'=>'缓存时间','help'=>'整站缓存时间，单位秒'),'site_cache_time',$setting->site_cache_time);

                echo ace_input(array('label_text'=>'登入超时时间','help'=>'允许最大登入时限'),'site_auto_login',$setting->site_auto_login);

                echo ace_input(array('label_text'=>'后台程序目录','help'=>'后台目录,如果程序目录没有改动,请不要修改此项'),'site_admin_dir',$setting->site_admin_dir);
                
                echo ace_input(array('label_text'=>'基本素材目录','help'=>'基本素材目录,如果目录没有改动,请不要修改此项'),'site_static_dir',$setting->site_static_dir);
                
                echo ace_input(array('label_text'=>'每页数量','help'=>'每页显示的数量'),'site_page_num',$setting->site_page_num);
                
                echo ace_input(array('label_text'=>'限制登录总共次数','help'=>'登入密码错误次数限制'),'site_accesslogin_count',$setting->site_accesslogin_count);
                
                echo ace_input(array('label_text'=>'拒绝登录等待时间','help'=>'登入密码错误次数达到, 将登录等待时间,单位分钟'),'site_accesslogin_time',$setting->site_accesslogin_time);
                
                echo ace_radio(array('label_text'=>'是否开启缓存','help'=>'开启缓存后,将提高页面执行效率'),'cache_open',$setting->cache_open);

                echo ace_radio(array('label_text'=>'是否开启调试','help'=>'开启调试后，会在页脚输出页面调试信息'),'site_debug',$setting->site_debug);

                echo ace_radio(array('label_text'=>'是否关闭站点','help'=>'关闭站点,不影响管理员登入'),'site_onoff',$setting->site_onoff);
            ?>
        </div>
        <div id="upload_set" class="tab-pane">
            <?php
                echo ace_input(array('label_text'=>'图片文件上传目录','help'=>'图片文件等素材上传保存目录,如果目录没有改动,请不要修改此项'),'site_attachments_dir',$setting->site_attachments_dir);
                
                echo ace_input(array('label_text'=>'上传图片限制大小','help'=>'上传图片限制大小,M为单位'),'site_image_maxsize',$setting->site_image_maxsize);
                
                echo ace_input(array('label_text'=>'上传文件限制大小','help'=>'上传文件限制大小,M为单位'),'site_file_maxsize',$setting->site_file_maxsize);
                
                echo ace_input(array('label_text'=>'允许的图片格式','help'=>'允许的图片格式,多个用|分隔'),'site_image_ext',$setting->site_image_ext);
                
                echo ace_input(array('label_text'=>'允许的附件格式','help'=>'允许的附件格式,多个用|分隔'),'site_file_ext',$setting->site_file_ext);
            ?>
        </div>
    </div>
    <?php 
           echo ace_srbtn();
       echo ace_form_close();
    ?>
</div>