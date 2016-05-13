<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Region extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model(array('region_model'));
	}
	
	function json(){
	    $cache_name = 'region_data_cache_name';
	    $this->cache->set_dir($this->region_model->_table);
	    $data = $this->cache->get($cache_name);
	    if($data === FALSE){
//         if(true){
            $data = array();
    	    foreach ($this->region_model->getAll2Array(array('select'=>'id,parent_id,name')) as $tmp){
    	        $data[$tmp['parent_id']][$tmp['id']] = array($tmp['id'],$tmp['name']);
    	    }
    	    $data = json_encode($data);
    	    $this->cache->save($cache_name,$data,864000);//缓存10天
	    }
	    exit($data);
	}
}