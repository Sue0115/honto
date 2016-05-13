<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 错误页面
 * @author xuebing.wang
 *
*/
class Error extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->output->enable_profiler((boolean)config_item('site_debug'));
    }
    
    function page404(){
        $this->load->view('error/404');
    }
}    