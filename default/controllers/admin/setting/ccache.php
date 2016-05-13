<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Ccache extends Admin_Controller {
	  
	function __construct() {
		   
		parent::__construct();
	} 
		 
	function index(){

	    if($this->input->is_post()){
	        $this->clean_cache();
	    }
	    
	    $this->_template('admin/setting/cache');
		 
	}
	
	public function clean_cache(){
		$this->load->helper('file');
	    $path = config_item('cache_path');
	    
	    $path = ($path == '') ? APPPATH.'cache/' : $path;
	    delete_files($path);
	     
		$this->cache->clean();
		echo '{"info":"清理成功","status":"y","id":"OK"}';exit();
	}
	
}
 
?>