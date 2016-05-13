<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {
    
    /**
     * 用户信息 session user
     *
     * @access private
     * @var object
     */
    private $_SU;
    
    /**
     * CI 句柄 codeigniter
     *
     * @access private
     * @var object
     */
    private $_CI;

    private $dbprefix = 'erp_';
    
    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct() {
        $this->_CI = & get_instance();
        $this->_SU = unserialize( $this->_CI->session->userdata('sessuser') );
    }
    
    /**
     * 获取 session 数据
     *
     * @access public
     * @return string
     */
    public function user($key = '') {
        
        if(empty($this->_SU) || !$this->_SU->id) {
            return FALSE;
        }
        
        if( empty($key) ) {
            return $this->_SU;
        } else {
            return isset( $this->_SU->$key ) ? $this->_SU->$key : FALSE;
        }
    }
    
    /**
     * 判断是否登录
     *
     * @access public
     * @return boolean
     */
    public function islogin() {

        if( ! empty($this->_SU) && NULL !== $this->_SU->id) {
            $token = $this->_CI->db->where( array('id'=>$this->_SU->id) )->get($this->dbprefix.'slme_user')->row('token');
            return ( $token && $token == $this->_SU->token ) ? TRUE : FALSE;
        }
        
        return FALSE;
    }
    
    /**
     * 检测用户登录
     *
     * @access public
     * @params string $user_name 用户名
     * @params string $password 用户密码
     * @return void
     */
    public function checkuserlogin($user_name, $password) {

        $this->_CI->db->select(array($this->dbprefix.'slme_user.*',$this->dbprefix.'slme_group.key',$this->dbprefix.'slme_group.items',$this->dbprefix.'slme_group.title'));
        $this->_CI->db->join($this->dbprefix.'slme_group',$this->dbprefix.'slme_user.gid='.$this->dbprefix.'slme_group.gid');
        $this->_CI->db->where(array($this->dbprefix.'slme_group.status'=>1,$this->dbprefix.'slme_user.status'=>1));
        
        $user = $this->_CI->db->where( array('user_name'=>$user_name) )->get($this->dbprefix.'slme_user')->row_array();
        if( ! $user ) return FALSE;
        $user = (object)array_change_key_case($user);
        
        $this->_CI->load->library('passwordhash');
		
        $pass = $this->_CI->passwordhash->CheckPassword($password, $user->password);
    
        return $pass ? $user : FALSE;
    }
	
    /**
     * 检测管理员登录是否有权限操作此模块
     *
     * @access public
     * @params string $colum 模块名
     * @return void
	*/
	public function permitlcc($colum){
		
		$gid = (int)$this->user('gid');
		
		$items = $this->_CI->db->where( array('gid'=>$gid) )->get($this->dbprefix.'slme_group')->row('items');
		if(is_array($colum)){
			$where = array('con_name'=>$colum[0],'con_name'=>$colum[1]);
		}else{
			$where = array('con_name'=>$colum);
		}
		$cid = $this->_CI->db->where( $where )->get($this->dbprefix.'slme_colum')->row('cid');
		
		if((int)$cid==0){
			
			showmessage('你没有权限处理此模块或模块不存在');exit();
		}else{
		
			if(!in_array($cid,explode(",",$items))){
				
				showmessage('你没有权限处理此模块');exit();
			}
		}
	}
    
    /**
     * 处理用户登录
     *
     * @access public
     * @params object $user 用户信息
     * @return void
     */
    public function process_login($user) {

        $data = new stdClass();
		
        $data->lastlogin = time();
		
        $data->activity  = time();
		
        $data->lastip    = $this->_CI->input->ip_address();
		
        $data->token     = random(10);
        
        /** 去除密码 */
        unset($user->password);
        
        /** 更新token */
        $user->token    = $data->token;
        
        /** 写入session */
        $this->set_session($user);
        $this->_CI->db->update($this->dbprefix.'slme_user', $data, array('id'=>$user->id));
    }
    
    /**
     * 处理用户登出
     * 
     * @access public
     * @return void
     */
    public function process_logout() {
		
        $this->_CI->db->update($this->dbprefix.'slme_userlog', array('logouttime'=>time()), array('lid'=>$this->user('slme_log')));
		
        $this->_CI->session->sess_destroy();
		
    }
    
    /**
     * 设置 session 数据
     *
     * @access public
     * @return void
     */
    public function set_session($data) {
		
        $session_data = array( 'sessuser'=>serialize($data) );
		
        $this->_CI->session->set_userdata($session_data);
		
    }

}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */