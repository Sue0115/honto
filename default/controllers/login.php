<?php if ( ! defined('BASEPATH') ) exit('No direct script access allowed');

class Login extends MY_Controller {
    
    /**
     * 拒绝登录等待时间
     * 
     * @access private
     * @var int
     */
    private $_access_login_time;
    
    /**
     * 限制登录总共次数
     * 
     * @access private
     * @var int
     */
    private $_access_login_count;
    
    /**
     * 登录错误剩余次数
     * 
     * @access private
     * @return int
     */
    private $_access_login_error;
    
    /**
     * 拒绝登录提示文字
     * 
     * @access private
     * @return int
     */
    private $_access_login_title;
    
    private $dbprefix = 'erp_';
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct() {
		
        parent::__construct(); 
        $this->load->library('Auth');
    }
    
    /**
     * 用户退出
     *
     * @access public
     * @return void
     */
    public function logout() {
        
        $this->load->helper('cookie');
        delete_cookie('P');
        
        $this->auth->process_logout();
        
        redirect( site_url() );
    }
    
    private function before_login($user_name=''){

        $this->_access_login_time   = (int) $this->config->item('site_accesslogin_time');
        
        $this->_access_login_count  = (int) $this->config->item('site_accesslogin_count');
        
        $ip = $this->input->ip_address();
        
        $in = time() - ($this->_access_login_time * 60);
        
        $max = (int) $this->db->where( array('logintime >'=>$in, 'loginip'=>$ip, 'status'=>1 ,'loginname'=>$user_name) )->select_max('lid')->get($this->dbprefix.'slme_userlog')->row('lid');
//         echo $this->db->last_query();exit;
        $err = (int) $this->db->where( array('lid >'=>$max, 'logintime >'=>$in, 'loginip'=>$ip, 'status'=>0 ,'loginname'=>$user_name) )->get($this->dbprefix.'slme_userlog')->num_rows();
        
        $this->_access_login_error  = (int) $this->_access_login_count - $err - 1;
        
        $this->_access_login_title  = '您错误次数太多，请 ' . $this->_access_login_time . ' 分钟后再登录';
        
    }
    
    /**
     * AJAX 用户登录
     * 
     * @access public
     * @return json
     */
    public function ajax_login_user() {

        if( ! $this->input->is_ajax_request() ) show_404();
        
        checkformhash();
     
        $user_name = htmlspecialchars($this->input->get_post('user_name'));
		
        $password = htmlspecialchars($this->input->get_post('password'));
        
        $this->before_login($user_name);

        /** 检测是否拒用户登录 */
        if( $this->_access_login_count ) {
			
            if( $this->_access_login_error == -1 ) {
				
                ajax_return('对不起，您的帐号已被禁止登录,因为密码错误次数过多');
            }
        }

        $logs = array('loginname'=>$user_name, 'logintime'=>time(), 'loginip'=>$this->input->ip_address(), 'status'=>0);
        
        /** 检测用户登录状态 */
        $user = $this->auth->checkuserlogin($user_name, $password);
        
        if( FALSE === $user ) {
			
            $this->db->insert($this->dbprefix.'slme_userlog', $logs);
			
            $tips = ($this->_access_login_error == 0) ? $this->_access_login_title : '用户名或密码错误，您还可以尝试 ' . $this->_access_login_error . ' 次';
			
            ajax_return($tips);
			
        }
        
        if( $user->status == 0 ) {
			
            $this->db->insert($this->dbprefix.'slme_userlog', $logs);
			
			ajax_return('对不起，您的帐号已被禁止登录,如有问题请联系管理员');
        }
        
        /** 写入用户登录日志 */
        $logs['status'] = 1;
		
        $this->db->insert($this->dbprefix.'slme_userlog', $logs);
        
		/*****判断对方地区是否有权限登录*****/
		$dqmess=$this->checkIP();//收录访问IP地址
		if($dqmess['sta'] == 2){
			//地区限制检测
			//$this->iplog();//记录新受限IP地址;
			ajax_return($dqmess['info']);
		}
		
		
        /** 下次是否自动登录 */
        $autologin = $this->input->get_post('autologin', TRUE);
        
        if(FALSE !== $autologin ) {
			
            $auto_login_time = (int) config_item('site_auto_login');
			
            $user_enocde_uid = authcode($user->id, 'ENCODE');
			
            set_cookie( array('name'=>'U', 'value'=>$user_enocde_uid, 'expire'=>$auto_login_time) );
			
            set_cookie( array('name'=>'P', 'value'=>md5($user->password), 'expire'=>$auto_login_time) );
			
        }
        
        /** 处理用户登录 */
        $this->auth->process_login($user);
        
        $referer = $this->input->get_post('referer');
        
        
        ajax_return($referer,0);
    }
    
    /**
     * 获取登录页面背景图片
     */
    function get_bg_img(){
        $this->load->helper('file');
        echo json_encode(get_filenames(FCPATH.config_item('site_attachments_dir').'login'));
    }
    
}

/* End of file accounts.php */
/* Location: ./application/controllers/accounts.php */