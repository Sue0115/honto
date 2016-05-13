<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  

class MY_Controller extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model(array('allow_ip_model'));
        //$this->checkIP();//验证访问IP
        
        $this->load->library('Session');
        $this->load->model(array('slme_user_model','nocheckip_user_model'));
        $this->load->switch_theme();

        if(!$this->input->is_ajax_request() && !$this->input->get('is_ajax')){
            $this->output->enable_profiler((boolean)config_item('site_debug'));
        }
        
    }
    public function checkIP(){
            $nocheck = $this->nocheckip_user_model->getAll2Array();
            $nocheckuser =array();//白名单
            foreach($nocheck as $v){
              $nocheckuser[] = $v['user'];
            }
            $user_name = htmlspecialchars($this->input->get_post('user_name'));
            if(in_array($user_name,$nocheckuser)){
                $msg = 'ok';
                $mess = array('sta'=>'1','info'=>$user_name);
                return $mess;
               
            }
            $options = array();
            $options['where'] = array('status'=>1);
            $allIp = $this->allow_ip_model->getAll2Array($options);
            $allowIp = array();
            foreach($allIp as $v){
                    $allowIp[] = $v['ip'];
            }
            $visitIp = $this->getIP();
            $EorMsg = '地区访问受限,您的IP为：'.$visitIp.',请将IP和授权原因以邮件(zhangjinxing@moonarstore.com)或电话(13728660599)形式联系网管张锦星';
            if(empty($visitIp)){
                if( PHP_SAPI == 'cli'){ 
                    $msg = 'ok';
                    $mess = array('sta'=>'1','info'=>$msg);
                    return $mess;
                }else{
                    $msg = $EorMsg;
                    $mess = array('sta'=>'2','info'=>$msg);
                    return $mess;
                }
            }
            if(!in_array($visitIp, $allowIp)){
                //记录IP不在库内,查看cookieip
                  $cookieip = isset($_COOKIE['cookieip'])?$_COOKIE['cookieip']:'';
                  if(!in_array($cookieip,$allowIp)){
                    //如果登录cookieip也不在记录，则禁止访问
                    // $this->iplog();
                    $msg = $EorMsg;
                    $mess = array('sta'=>'2','info'=>$msg);
                    return $mess;
                  }else{
                    //cookieip存在库里,允许登录
                    $visitIp=$cookieip;
                  }
                    
            }
            //设置cookieip保存时间,15天后过期
            $last_time = time()+15*24*60*60;
            setcookie ( 'cookieip', $visitIp,$last_time);
            //访问IP在库内吗，更新最新使用时间
            $where = array('ip'=>$visitIp);
            $data['last_time']=time();
            $data['where'] = $where;
            $this->allow_ip_model->update($data);

             $msg = 'ok';
             $mess = array('sta'=>'1','info'=>$msg);
             return $mess;
       
    }
    //收录IP地址
    public function recordIP(){

        $options = array();
        $options['where'] = array('status'=>1);
        $allIp = $this->allow_ip_model->getAll2Array($options);
        $allowIp = array();
            foreach($allIp as $v){
                    $allowIp[] = $v['ip'];
            }
        $visitIp = $this->getIP();
        $data['ip'] = $visitIp;
        if($visitIp){
            $data['remark'] = '新erp系统自动收录';
        }else{
            $data['remark'] = $_SERVER['REMOTE_ADDR'];
        }
        if(!in_array($visitIp, $allowIp)){
             $this->allow_ip_model->add($data);
        }  
    }
    //失败登录日记
    public function iplog(){

        $options = array();
        $options['where'] = array('status'=>0);
        $allIp = $this->allow_ip_model->getAll2Array($options);
        $allowIp = array();
            foreach($allIp as $v){
                    $allowIp[] = $v['ip'];
            }
        $visitIp = $this->getIP();
        $data['ip'] = $visitIp;
        $user_name = htmlspecialchars($this->input->get_post('user_name'));
        $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        $data['remark'] = $user_name.'地区新系统登录失败,url:'.$url;  
        $data['last_time']=time();
        $data['status']=0;
        if(!in_array($visitIp, $allowIp)){
             $this->allow_ip_model->add($data);
        }     
    }
	/**
     * 获取请求ip
     *
     * @return ip地址
     */
    public function getIP() {
      $proxy_headers = array(
          'CLIENT_IP', 
          'FORWARDED', 
          'FORWARDED_FOR', 
          'FORWARDED_FOR_IP', 
          'HTTP_CLIENT_IP', 
          'HTTP_FORWARDED', 
          'HTTP_FORWARDED_FOR', 
          'HTTP_FORWARDED_FOR_IP', 
          'HTTP_PC_REMOTE_ADDR', 
          'HTTP_PROXY_CONNECTION',
          'HTTP_VIA', 
          'HTTP_X_FORWARDED', 
          'HTTP_X_FORWARDED_FOR', 
          'HTTP_X_FORWARDED_FOR_IP', 
          'HTTP_X_IMFORWARDS', 
          'HTTP_XROXY_CONNECTION', 
          'VIA', 
          'X_FORWARDED', 
          'X_FORWARDED_FOR'
         );

      foreach($proxy_headers as $proxy_header)
      {
        if(isset($_SERVER[$proxy_header]) && preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $_SERVER[$proxy_header])) 
        {
            return $_SERVER[$proxy_header];
        }
        else if(@stristr(',', $_SERVER[$proxy_header]) !== FALSE)
        {
          $proxy_header_temp = trim(array_shift(explode(',', $_SERVER[$proxy_header]))); 
          if(($pos_temp = stripos($proxy_header_temp, ':')) !== FALSE) $proxy_header_temp = substr($proxy_header_temp, 0, $pos_temp); 

          if(preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $proxy_header_temp)) return $proxy_header_temp;
        }
      }
      return $_SERVER['REMOTE_ADDR'];
    }
}


class Admin_Controller extends MY_Controller {
   
    public $user_info;
    
    public $model;
    
    public $operate_model;
    
    /**
     * menu_tree
     * 菜单集合
     *
     * @var Tree
     * @access  private
     **/
    public $menu_tree;
    
    /**
     * _current_menu
     * 当前所在的菜单的下标
     *
     * @var int
     * @access  private
     **/
    public $current_menu = -1;
    
	/**
     * breadcrumb
     * 面包屑
     *
     * @var array
     * @access  public
     **/
	public $breadcrumb = '';
    
	/**
	 * 构造函数
	 */
    public function __construct() {
		
        parent::__construct();
        
        $this->operate_model = $this->slme_operate_log_model;//操作日志模型
        
        $this->load->library('Auth');
        
//         var_dump($this->session->all_userdata());die;
//         var_dump($this->auth->islogin());die;
        // 检测是否有权限登录管理后台
		if (!$this->auth->islogin()){
				
			redirect( site_url('login/logout') );
		}
		
 		$this->user_info = $this->auth->user();
// 		unset($this->user_info->password);
    
    	$this->load->model("slme_colum_model");
		//管理菜单列表
		if ($this->user_info->key != 'root') {
			$list        = explode(",",$this->user_info->items);
			
			$options     = array();
			if(is_array($list)){
				$options['where_in'] = array('cid'=>$list);
			}
		}
		
		$options['where'] = array('status'=> 1,'type'=>'admin');
		$options['order'] = 'order_id desc,cid desc';

		//获取当前真实地址
		$current_arr['directory'] = $this->router->directory;
		$current_arr['con_name']  = $this->router->class;
		$current_arr['con_name'] .= $this->router->method == 'index' ? '' : '/'.$this->router->method;
		$list = $this->slme_colum_model->getAll2Array($options);
		
		$this->load->library('Tree');
		//构造树型对象
		$this->tree->init($list,array('cid', 'parents','title'),0,$current_arr,'hookForTree');
		
		$this->breadcrumb = array(admin_base_url('index')=>'<a href="'.admin_base_url('index').'">首页</a>');
		//找出当前地址的所有祖先
		foreach ($this->tree->navi($this->tree->current_id) as $item){
			$this->breadcrumb[admin_base_url($item['con_name'],$item['directory'])] = $item['title'];
		}
		
		$this->menu_tree      = $this->tree->leaf();
		$this->current_menu   = $this->tree->current_id;
		
    }
    
    /**
     * 列表页
     */
    public function index(){
        $this->load->model('sharepage');
    }
    
    /**
     * 单页，详情页
     * @param string $id
     */
    public function info($id=null){

        if($this->input->is_post()){
            $this->save();
        }
    }
    
    /**
     * 保存方法
     */
    protected function save(){
    	die;
    }
    
    /**
     * 状态修改
     */
    public function clicktik(){
    
    	$val = intval($this->input->get_post('val')); //字段值
    
    	$field = htmlspecialchars($this->input->get_post('field'));//所要操作的字段
    	$field = $field ? $field : 'status';
    	
    	$result = null;
    	if($this->model){
	    	$data[$field] = $val;
	    	$data[$this->model->getPK()] = intval($this->input->get_post('id')); //所在的字段ID,主键
	    	//修改信息
	    	$result = $this->model->update($data);
    	}
    
    	//信息返回操作
    	if($result){
    			
    		echo json_encode(array('status'=> '1','msg'=>'修改成功'));exit();
    	}else{
    			
    		echo json_encode(array('status'=> '2','msg'=>'修改失败'));exit();
    	}
    
    }
    
    /**
     * 删除,为假删
     */
    public function delete(){
    
    	//删除信息
    	$result = null;
    	if($this->model){
	    	$data['status'] = -1;
    		$data[$this->model->getPK()] = intval($this->input->get_post('id')); //所在的字段ID,主键
    		//修改信息
    		$result = $this->model->update($data);
    	}
    
    	//信息返回操作
    	if($result){
    			
    		echo json_encode(array('status'=> '1','msg'=>'删除成功'));exit();
    	}else{
    			
    		echo json_encode(array('status'=> '2','msg'=>'删除失败'));exit();
    	}
    
    }
    
    /**
     * 批量操作
     */
    public function batch(){
    
    	$idArr = $this->input->post("id");
    
    	$type	= intval($this->input->post("type"));
    
    	$field	= htmlspecialchars($this->input->post("field"));
    
    	//选择要处理的ID
    	if( count($idArr) < 0){
    		echo json_encode(array('status'=> '2'));exit();
    	}
    
    	//处理的字段
    	if(!$field){
    		echo json_encode(array('status'=> '2'));exit();
    	}
    	
    	if($this->model){
	    	//101为删除恢复
    		if($type==101){
    			$data[$field] = '1';
    		}else{
    			$data[$field] = $type;
    		}
    		
    		$where_in[$this->model->getPK()] = $idArr;
    		
    		$options['where_in'] = $where_in;
    		
    		if($type == 100){
    			$this->model->delete($options);
    		}else{
	    		$this->model->update($data,$options);
    		}
    	}
    
    	echo json_encode(array('status'=> '1'));exit();
    }
    

    /**
     * 加载视图
     *
     * @access  protected
     * @param   string
     * @param   array
     * @return  void
     */
    protected function _template($template, $data = array())
    {
        $data['tpl']        = $template;
        $this->load->view('admin/sys_entry', $data);
    }
    
	protected function template($template, $data = array())
    {	
    	$this->load->view('admin/common/header');
        $this->load->view($template, $data);
    }
  protected function only_template($template, $data = array())
    { 
        $this->load->view($template, $data);
    }
}
