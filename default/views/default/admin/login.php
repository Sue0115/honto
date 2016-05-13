<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo config_item('site_name')?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <!-- basic styles -->

        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/bootstrap.min.css')?>"/>
        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/font-awesome.min.css')?>" />

        <!--[if IE 7]>
          <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/font-awesome-ie7.min.css')?>" />
        <![endif]-->

        <!-- page specific plugin styles -->

        <!-- fonts -->

        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/font-googleapis.css')?>" />

        <!-- ace styles -->

        <!-- ace styles -->
        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/ace.min.css')?>" />
        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/ace-rtl.min.css')?>" />

        <!--[if lte IE 8]>
          <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/ace-ie.min.css')?>" />
        <![endif]-->

        <!-- inline styles related to this page -->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

        <!--[if lt IE 9]>
        <script src="<?php echo static_url('theme/ace/js/html5shiv.js')?>"></script>
        <script src="<?php echo static_url('theme/ace/js/respond.min.js')?>"></script>
        <![endif]-->
        
        <style>
            body{overflow: hidden;}
			#login_bg {
			  position: absolute;
			  z-index: -1;
			}
			.login-panel{
	           margin-top: 5%;
			}
        </style>
    </head>
	<!-- 背景 -->
	<div id="login_bg"><img src="" alt="" style="display: none;" /></div>
    <body class="login-layout">
        <div class="main-container">
            <div class="main-content">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1 login-panel">
                        <div class="login-container">
                            <div class="center">
                                <h1>
                                    <i class="icon-leaf green"></i>
                                    <span class="red"><?php echo config_item('site_name')?></span>
                                    <span class="white">后台管理登录</span>
                                </h1>
                                <h4 class="blue">
                                    &copy; <?php echo config_item('site_name')?> 
                                    <a href="http://www.miibeian.gov.cn/" target="_blank">粤ICP备15099677号-1</a>
                                </h4>
                            </div>

                            <div class="space-6"></div>

                            <div class="position-relative">
                                <div id="login-box" class="login-box visible widget-box no-border">
                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <h4 class="header blue lighter bigger">
                                                <i class="icon-coffee green"></i>
                                                <span>请输入您的登录信息</span>
                                            </h4>

                                            <div class="space-6"></div>

                                            <form id="login-form" action="<?php echo $action?>" method="post">
                                                <input type="hidden" name="referer" value="<?php echo admin_base_url('index')?>" />
                                                <fieldset>
                                                    <label class="block clearfix">
                                                        <span class="block input-icon input-icon-right">
                                                            <input type="text" name="user_name" class="form-control" placeholder="用户名" />
                                                            <i class="icon-user"></i>
                                                        </span>
                                                    </label>

                                                    <label class="block clearfix">
                                                        <span class="block input-icon input-icon-right">
                                                            <input type="password" name="password" class="form-control" placeholder="密码" />
                                                            <i class="icon-lock"></i>
                                                        </span>
                                                    </label>

                                                    <div class="space"></div>

                                                    <div class="clearfix">
<!--                                                         <label class="inline"> -->
<!--                                                             <input type="checkbox" class="ace" /> -->
<!--                                                             <span class="lbl"> 记住我</span> -->
<!--                                                         </label> -->

                                                        <button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
                                                            <i class="icon-key"></i>
                                                            <span>登录</span>
                                                        </button>
                                                    </div>

                                                    <div class="space-4"></div>
                                                </fieldset>
                                            </form>
                                        </div><!-- /widget-main -->

                                        <div style="display: none;"  class="toolbar clearfix">
                                            <div>
                                                <a href="#" onclick="show_box('forgot-box'); return false;" class="forgot-password-link">
                                                    <i class="icon-arrow-left"></i>
                                                    <span>我忘记密码了</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div><!-- /widget-body -->
                                </div><!-- /login-box -->

                                <div id="forgot-box" class="forgot-box widget-box no-border">
                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <h4 class="header red lighter bigger">
                                                <i class="icon-key"></i>
                                                <span>忘记密码</span>
                                            </h4>

                                            <form>
                                                <fieldset>
                                                    <label class="block clearfix">
                                                        <span class="block input-icon input-icon-right">
                                                            <input type=text class="form-control" placeholder="手机号码" />
                                                            <i class="icon-mobile-phone"></i>
                                                        </span>
                                                    </label>
                                                    
                                                    <label class="input-group">
                                                        <input type="text" id="form-field-mask-1" class="form-control input-mask-date" placeholder="手机验正码">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-sm btn-danger">
                                                                <i class="icon-lightbulb"></i>
                                                                <span>获取验正码</span>
                                                            </button>
                                                        </span>
                                                    </label>

                                                    <label class="block clearfix">
                                                        <span class="block input-icon input-icon-right">
                                                            <input type="password" class="form-control" placeholder="新密码" />
                                                            <i class="icon-lock"></i>
                                                        </span>
                                                    </label>
                                                    
                                                    <div class="space"></div>

                                                    <div class="clearfix">

                                                        <button type="button" class="width-35 pull-right btn btn-sm btn-danger">
                                                            <i class="icon-key"></i>
                                                            <span>提交</span>
                                                        </button>
                                                    </div>

                                                </fieldset>
                                            </form>
                                        </div><!-- /widget-main -->

                                        <div class="toolbar center">
                                            <a href="#" onclick="show_box('login-box'); return false;" class="back-to-login-link">
                                                <span>返回登录</span>
                                                <i class="icon-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div><!-- /widget-body -->
                                </div><!-- /forgot-box -->
                            </div><!-- /position-relative -->
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
        </div><!-- /.main-container -->

        <!-- basic scripts -->

        <script type="text/javascript">
            if("ontouchend" in document) document.write("<script src=\"<?php echo static_url('theme/ace/js/jquery.mobile.custom.min.js')?>\">"+"<"+"/script>");
        </script>
		
		<!--[if !IE]>-->
		
			<script type="text/javascript">
				window.jQuery || document.write("<script src='<?php echo static_url('theme/common/jquery/jquery-2.0.3.min.js')?>'>"+"<"+"/script>");
			</script>
			
		<!--<![endif]-->

		<!--[if IE]>WWWWWW
			<script type="text/javascript">
			 window.jQuery || document.write("<script src='<?php echo static_url('theme/common/jquery/jquery-1.10.2.min.js')?>'>"+"<"+"/script>");
			</script>
		<![endif]-->
		
        <!-- inline scripts related to this page -->
        <script src="<?php echo static_url('theme/admin/js/admin.js')?>"></script>
        <script src="<?php echo static_url('theme/common/common.js')?>"></script>
        
        <script type="text/javascript">
            function show_box(id) {
             jQuery('.widget-box.visible').removeClass('visible');
             jQuery('#'+id).addClass('visible');
            }
            
            var img_list = [];
         // JavaScript Document
            jQuery(function() {
            	$.ajax({
           	        url: 'login/get_bg_img',
           	        dataType: 'json'
           	    }).done(function (list) {
                       //随机背景
                       //setInterval(function(){
                       //},3000)
                       var ranNum=parseInt((list.length)*Math.random());
                       var src = 'attachments/login/'+list[ranNum];
                       $.get(src,function(){
                    	   $("#login_bg img").attr('src',src).fadeIn(500);
                       });
           	    });
                
                //屏幕自适应
                $(window).resize(function(){
                    change();
                })
                    
                change();
                function change(){
                    var w=$(window).width();
                    var h=$(window).height();
                    $("#login_bg img,#lay").css({
                        'width':w,
                        'height':h
                        })
                    $("#login_bg img").animate({opacity:1})
                    $("#login_box").css({
                        'left':(w-364)/2,
                        'top':(h-264)/2
                    })
                }
                $("#login-form").submit(function(){
                	var sbtn = $(this).find('button[type="submit"]').attr('disabled',true)
                	$.ajax({
                        type: 'POST',
                        url: this.action,
                        data: $(this).serializeArray(),
                        success: complete,
                        dataType: 'json',
                        error:function(){
                            alert('出错误了，请重新再试！')
                        }
                      }).always(function () {
                    	  sbtn.attr('disabled',false)
                      });
                	return false;
                })
                function complete(data) {
                    (data.status > 0) ? showtips(data.info) : window.location = data.info;
                }
                
            });

        </script>
	</body>
</html>