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

        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/ace.min.css')?>" />
        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/ace-rtl.min.css')?>" />
        <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/ace-skins.min.css')?>" />

        <!--[if lte IE 8]>
          <link rel="stylesheet" href="<?php echo static_url('theme/ace/css/ace-ie.min.css')?>" />
        <![endif]-->

        <!-- inline styles related to this page -->

        <!-- ace settings handler -->

        <script src="<?php echo static_url('theme/ace/js/ace-extra.min.js')?>"></script>

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

        <!--[if lt IE 9]>
        <script src="<?php echo static_url('theme/ace/js/html5shiv.js')?>"></script>
        <script src="<?php echo static_url('theme/ace/js/respond.min.js')?>"></script>
        <![endif]-->
		
		<!--[if !IE]>-->
		
			<script type="text/javascript">
				window.jQuery || document.write("<script src='<?php echo static_url('theme/common/jquery/jquery-2.0.3.min.js')?>'>"+"<"+"/script>");
			</script>
			
		<!--<![endif]-->

		<!--[if IE]>
			<script type="text/javascript">
			 window.jQuery || document.write("<script src='<?php echo static_url('theme/common/jquery/jquery-1.10.2.min.js')?>'>"+"<"+"/script>");
			</script>
		<![endif]-->
		
        <script src="<?php echo static_url('theme/admin/js/admin.js')?>"></script>
        <script src="<?php echo static_url('theme/common/common.js')?>"></script>
        <!--<script src="<?php echo static_url('theme/admin/js/calendar.js')?>"></script>-->
        <script src="<?php echo static_url('theme/common/kindeditor/kindeditor.js')?>"></script>
    </head>
    