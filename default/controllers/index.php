<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  

class Index extends MY_Controller { 
 
    function __construct()  {  
        parent::__construct(); 
			
    }  
    function index()   {
    	
        $data = array('action'=>site_url('login/ajax_login_user'));
        $this->load->view('admin/login',$data);
    }  
} 