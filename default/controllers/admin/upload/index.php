<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends Admin_Controller{
	
	public function __construct(){
	    parent::__construct();
	     
	}
	
	/**
	 * 图片上传方法
	 * @param string $dir 文件保存路径
	 */
	public function image($dir='images'){

	    $data = array(
	            'dir'=>$dir,
	    );
	    
		$this->load->view("admin/upload/image",$data);	
	}
	
	public function upload_image($dir='images'){
        $options = array(
                'upload_dir'=>FCPATH.config_item('site_attachments_dir').$dir.'/',
                'upload_url'=>'/'.config_item('site_attachments_dir').$dir.'/',
                'script_url'=>'/admin/upload/index/upload_image/'.$dir,
                'image_versions' => array(
                    '' => array(
                            'auto_orient' => false
                        ),
                    'medium' => array(
                        'max_width' => 700,
                        'max_height' => 300
                        ),
                    'thumbnail_80_80' => array(
                        'max_width' => 80,
                        'max_height' => 80
                        )
                ),
        );
        $this->load->library('UploadHandler',$options);
        
        create_dir($this->uploadhandler->get_option('upload_dir'));
	     
        $this->uploadhandler->initialize();
	}
	
	
}

/* End of file index.php */
/* Location: ./defaute/controllers/index.php */


?>