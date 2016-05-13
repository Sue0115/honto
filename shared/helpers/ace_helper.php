<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('ace_html')){
	function ace_html($label_text='', $value = '', $extra = ''){
		$default = array('class'=>'autosize-transition span12 form-control','rows'=>'');
		return ace_group(ace_label($label_text), '<span class="form-control">'.$value.'</span>');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_view_pic')){
	function ace_view_pic($label_text='',$value=''){


		$label = ace_label($label_text);

		$element = '<a class="cboxElement" href="'.$value.'">
            		        <span>点击查看图片</span>
            	         </a>';
		return ace_group($label, $element);
	}
}
// ------------------------------------------------------------------------
if ( ! function_exists('ace_header')){
	function ace_header($title='', $action='', $url='',$flag=TRUE){
	    $action = $action ? '修改' : '增加';
	    
		$html = '<div class="page-header">
                    <h1>
                        '.$title.'管理
                        <small>
                            <i class="icon-double-angle-right"></i>
		                    '.$action.$title.'
                        </small>
                    </h1>
                 </div>';
	    return $html;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_form_open')){
	function ace_form_open($action = '', $attributes = array(), $hidden = array()){
		
		$default = array('class' => 'form-horizontal registerform', 'id' => 'form_submit','role'=>'form', 'ajaxpost'=>"ajaxpost");
		if(!is_array($attributes)){
		    $attributes = array();
		}
		$attributes = array_merge($default,$attributes);
		
	    return form_open($action,$attributes,$hidden);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_form_close')){
	function ace_form_close($extra=''){
	    return form_close($extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_radio'))
{
    function ace_radio($setting='', $data='', $checked = FALSE, $options=array(1=>'是',0=>'否'), $extra = 'class="ace"')
    {
        if ( ! is_array($setting))
        {
            $setting = array('label_text' => $setting);
        }

        $setting = array_merge(array('help'=>''),$setting);
        
        $element = '';
        foreach ($options as $v=>$text){
            $is_checked = $checked == $v ? true : false;
            $element .= '<label>'.form_radio($data,$v,$is_checked,$extra).'<span class="lbl">'.$text.'</span></label>';
        }
        
        return ace_group(ace_label($setting['label_text']),$element,$setting['help']);
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_password')){
	function ace_password($options='', $data=''){
		if ( ! is_array($data)){
			$data = array('name' => $data);
		}
		$data['type']     = 'password';
		$data['class']    = 'width-100';
	    return ace_input_m($options,$data,'',TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_label_m')){
	function ace_label_m($label_text='', $id=''){

	    return ace_label($label_text,$id,TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_label')){
	function ace_label($label_text='', $id='', $must=FALSE){

	    $label_attr         = array('class' => 'col-xs-12 col-sm-2 control-label no-padding-right');
	    if($must){
    	    $label_text         = '<span class="red">*</span>'.$label_text;
	    }
	    return form_label($label_text,$id,$label_attr);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_srbtn')){
	function ace_srbtn($url='',$flag=TRUE,$sb_content='确认保存',$reset_content='重置'){
	    $html = '<div class="clearfix form-actions">
                      <div class="col-md-offset-3 col-md-9">
                          <button class="btn btn-success" type="submit">
                              <i class="icon-ok bigger-110"></i> '.$sb_content.'
                          </button>
                          <button class="btn" type="reset">
                              <i class="icon-undo bigger-110"></i> '.$reset_content.'
                          </button>';
        if($flag){
        	$html .='	  <a href="javascript:;" class="btn btn-info" onClick="history.go(-1)">
	                         <i class="icon-reply"></i>
	                         <em>返回上一页</em>
	                      </a>
        				  <a href="'.admin_base_url($url).'" class="btn btn-info">
                             <i class="icon-list"></i>
                             <em>返回列表</em>
                          </a>
	                      <a href="" class="btn btn-info">
	                          <i class="icon-refresh"></i>
	                          <em>刷 新</em>
	                      </a>';
        }
        
		$html .= '	</div>
                  </div>';
	    return $html;
	}
}
// ------------------------------------------------------------------------

if ( ! function_exists('ace_upload_pic')){
	function ace_upload_pic($options='', $name='', $value='', $url=''){
	
        if(!is_array($options)){
            $options                = array('label_text'=>$options);
        }
	    $options = array_merge(array('help'=>''),$options);

        $label = ace_label($options['label_text'],$name);
        
        $data = array('class'=>'btn btn-sm btn-primary choose_pic','id'=>$name,'url'=>$url);
        $element = form_button($data,'<i class="icon-picture"></i> 选择图片');
        if($value){
            $element .= '<a class="cboxElement" href="'.$value.'">
            		        <span>点击查看图片</span>
            		        <input type="hidden" name="'.$name.'" value="'.$value.'" />
            	         </a>';
        }
	    return ace_group($label, $element, $options['help']);
	}
}
// ------------------------------------------------------------------------

if ( ! function_exists('ace_textarea')){
	function ace_textarea($label_text='', $data = '', $value = '', $extra = ''){
	    if ( ! is_array($data)){
	        $data           = array('name' => $data);
	    }
	    $default = array('class'=>'autosize-transition span12 form-control','rows'=>'');
        $data = array_merge($default,$data);
	    return ace_group(ace_label($label_text,$data['name']), form_textarea($data,$value,$extra));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_dropdown')){
	function ace_dropdown($data='', $name='', $options=array(), $selected=array(), $extra='class="width-100"'){

	    if(!is_array($data)){
	        $data                = array('label_text'=>$data);
	    }
	    $data = array_merge(array('help'=>''),$data);
	    
	    return ace_group(ace_label($data['label_text'],$name), form_dropdown($name,$options,$selected,$extra), $data['help']);
	}
}
// ------------------------------------------------------------------------

if ( ! function_exists('ace_input_m')){
	function ace_input_m($options='', $data='', $value='', $extra=''){
	    return ace_input($options,$data,$value,$extra,TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ace_input')){
	function ace_input($options='', $data='', $value='', $extra='', $must=FALSE){
	    if ( ! is_array($data)){
			$data           = array('name' => $data);
            $data['id']     = $data['name'];
            $data['class']  = 'width-100';
	    }
        if(!is_array($options)){
            $options                = array('label_text'=>$options);
        }

        $default                = array();
        $default['datatype']    = '*';
        $default['nullmsg']     = '请输入 '.$options['label_text'];
        $default['errormsg']    = '请输入 '.$options['label_text'];
        $default['help']        = '';
        
        $options = array_merge($default,$options);
        
	    if($must){
            $data['datatype']   = $options['datatype'];
            $data['nullmsg']    = $options['nullmsg'];
            $data['errormsg']   = $options['errormsg'];
            $label = ace_label_m($options['label_text'],$data['name']);
	    }else{
	        $label = ace_label($options['label_text'],$data['name']);
	    }
	    
	    $element = '<span class="input-icon block input-icon-right">'.form_input($data,$value,$extra).'<i class="icon icon-info-sign"></i></span>';
	    
	    return ace_group($label,$element,$options['help']);
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('ace_group')){
	function ace_group($label='', $element='',$help=''){
	    $html  = '<div class="form-group">';
	    $html .=     $label;
		$html .=     '<div class="col-xs-12 col-sm-5">';
        $html .=          $element;
        $html .=     '</div>
            	      <div class="help-block col-xs-12 col-sm-reset inline">'.
            	         $help
            	    .'</div>
            	 </div>';
	    return $html;
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('ace_linksel')){
	function ace_linksel($label='', $id='',$options=array()){
	    $html = ace_group(ace_label($label,$id),'<div id="'.$id.'"></div>');
	    $html .= '<script type="text/javascript">
                    $(function(){
                        $("#'.$id.'").linkagesel('.json_encode($options).');
                    });
                  </script>';
	    return $html;
	}
}
// ------------------------------------------------------------------------

/* End of file form_helper.php */
/* Location: ./system/helpers/form_helper.php */
