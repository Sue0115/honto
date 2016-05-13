<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class basic extends Admin_Controller {
	  
	function __construct() {
		   
		parent::__construct();
	} 
		 
	function index(){
	    redirect(admin_base_url('setting/basic/info'));
	}
	
	function info(){

	    parent::info();
	    
		$setting = (object) $this->config->config;
		
		$this->_template('admin/setting/basic',array('setting'=>$setting));
	} 
	 
    
	
	public function save(){
		
		$file='./default/config/setting.php';
		$array = array();
		foreach ($this->input->post(null,true) as $k=>$v){
			if(in_array($k, array('sms_pwd'))){
				$array[$k] = htmlspecialchars($v);
			}elseif(intval($v)){
				$array[$k] = intval($v);
			}else{
				$array[$k] = htmlspecialchars($v);
			}
		}
		unset($array['id']);		
		//缓存
		$text='<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");';
		
		$text.=' $config='.var_export($array,true).';';
		
		if(false!==fopen($file,'w+')){
			
			file_put_contents($file,$text);
			
			echo '{"info":"保存成功","status":"y","id":"1"}';exit();
		}else{
			
			echo '{"info":"保存失败","status":"n"}';exit();
		}
		die;
	}
	
}
 
?>